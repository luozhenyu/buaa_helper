@extends('layouts.app')

@php
    $files = collect();
    foreach (explode(',', old('attachment')) as $sha1) {
        if ($file = \App\Models\File::where('sha1', $sha1)->first()) {
            $files->push($file->downloadInfo());
        }
    }
@endphp

@push('cssLink')
<link rel="stylesheet" href="{{ url('/components/bootstrap-select/dist/css/bootstrap-select.min.css') }}">
<link rel="stylesheet" href="{{ url('/components/flatpickr/dist/flatpickr.min.css') }}">
@endpush

@push('jsLink')
<script src="{{ url('/components/bootstrap-select/dist/js/bootstrap-select.min.js') }}"></script>
<script src="{{ url('/components/bootstrap-select/dist/js/i18n/defaults-zh_CN.js') }}"></script>

<script src="{{ url('/components/flatpickr/dist/flatpickr.min.js') }}"></script>
<script src="{{ url('/components/flatpickr/dist/l10n/zh.js') }}"></script>

<script src="{{ url('/ckeditor/ckeditor.js') }}"></script>

<script src="{{ url('/js/file_upload.js') }}"></script>
@endpush

@push('js')
<script>
    $(function () {
        window.onbeforeunload = function () {
            return "您确认要退出此页面?";
        };
        flatpickr("#timeRange", {
            locale: "zh",
            enableTime: true,
            altInput: true,
            minDate: "today",
            mode: "range",
            weekNumbers: true,
            defaultDate: [
                @if(old('start_time'))
                new Date("{{ old('start_time') }}"),
                @endif
                @if(old('end_time'))
                new Date("{{ old('end_time') }}")
                @endif
            ],
            onChange: function (selectedDates, dateStr, instance) {
                if (selectedDates[0] && selectedDates[1]) {
                    if (selectedDates[1] < new Date(Date.now() + 2 * 86400 * 1000)
                        && !confirm("截止时间在48小时内，请确认此通知是否如此紧急！")) {
                        instance.clear();
                        return;
                    }
                    $("#start_time").val(selectedDates[0].toISOString());
                    $("#end_time").val(selectedDates[1].toISOString());
                }
            }
        });

        var editor = CKEDITOR.replace("content");
        editor.on('fileUploadRequest', function (evt) {
            var xhr = evt.data.fileLoader.xhr;
            xhr.setRequestHeader('X-CSRF-TOKEN', $("meta[name='csrf-token']").attr("content"));
        });

        $("#attachmentBtn").click(function () {
            $(this).upload({
                success: function (json) {
                    if (json.uploaded) {
                        $("#attachmentContainer").append(parseFile(json, true));
                    } else {
                        alert(json.message);
                    }
                }
            });
        });

        $("#form").submit(function () {
            var allFiles = [];
            $("#attachmentContainer").find("p").each(function () {
                allFiles.push($(this).data('sha1'));
            });
            $("#attachment").val(allFiles.join(','));
        });

        $("#department").selectpicker("val", "{{ old('department')?: Auth::user()->department_id }}");
        $("#important").selectpicker("val", "{{ old('important') }}");
        var files = JSON.parse("{!! addslashes($files->toJson()) !!}");
        for (var i = 0; i < files.length; i++) {
            $("#attachmentContainer").append(parseFile(files[i], true));
        }
    });
</script>
@endpush

@push("crumb")
<li><a href="{{ url("/") }}">主页</a></li>
<li><a href="{{ url("/notification") }}">通知中心</a></li>
<li><a href="{{ url("/notification/manage") }}">通知管理</a></li>
<li class="active">创建新通知</li>
@endpush

@section('content')
    <form class="form-horizontal" id="form" role="form" method="POST" action="{{ route('notification') }}">
        {{ csrf_field() }}

        <div class="form-group{{ $errors->has('title') ? ' has-error' : '' }}">
            <label for="title" class="col-md-2 control-label">标题</label>
            <div class="col-md-9">
                <input id="title" type="text" class="form-control" name="title" value="{{ old('title') }}"
                       required autocomplete="off">
                @if ($errors->has('title'))
                    <span class="help-block">
                        <strong>{{ $errors->first('title') }}</strong>
                    </span>
                @endif
            </div>
        </div>

        <div class="form-group{{ $errors->has('department') ? ' has-error' : '' }}">
            <label for="department" class="col-md-2 control-label">发布院系/部门</label>
            <div class="col-md-9">
                @if(Entrust::hasRole('admin'))
                    <select class="selectpicker form-control{{ $errors->has('department') ? ' has-error' : '' }}"
                            id="department" name="department" required>
                        @foreach(\App\Models\Department::get() as $department)
                            <option value="{{ $department->id }}">{{ ($department->number<100?$department->number.'-':'').$department->name }}</option>
                        @endforeach
                    </select>
                @else
                    <select class="selectpicker form-control{{ $errors->has('department') ? ' has-error' : '' }}"
                            id="department" name="department" disabled>
                        @php($department = Auth::user()->department)
                        <option value="{{ $department->id }}">{{ ($department->number<100?$department->number.'-':'').$department->name }}</option>
                    </select>
                @endif

                @if ($errors->has('department'))
                    <span class="help-block">
                        <strong>{{ $errors->first('department') }}</strong>
                    </span>
                @endif
            </div>
        </div>

        <input id="start_time" name="start_time" type="hidden" value="{{ old('start_time') }}">
        <input id="end_time" name="end_time" type="hidden" value="{{ old('end_time') }}">
        <div class="form-group{{ $errors->has('start_time') || $errors->has('end_time') ? ' has-error' : '' }}">
            <label for="timeRange" class="col-md-2 control-label">起止日期</label>
            <div class="col-md-9">
                <input id="timeRange" type="text" class="form-control flatpickr flatpickr-input"
                       placeholder="请选择起止日期" readonly autocomplete="off" required>

                @if($errors->has('start_time') || $errors->has('end_time'))
                    <span class="help-block">
                        <strong>{{ $errors->first('start_time') . $errors->first('end_time') }}</strong>
                    </span>
                @endif
            </div>
        </div>

        <div class="form-group{{ $errors->has('important') ? ' has-error' : '' }}">
            <label for="important" class="col-md-2 control-label">通知类型</label>
            <div class="col-md-9">
                <select class="selectpicker form-control{{ $errors->has('important') ? ' has-error' : '' }}"
                        id="important" name="important" required autocomplete="off">
                    <option value="0">普通通知</option>
                    <option value="1">重要通知（要求阅读后确认）</option>
                </select>

                @if ($errors->has('important'))
                    <span class="help-block">
                        <strong>{{ $errors->first('important') }}</strong>
                    </span>
                @endif
            </div>
        </div>

        <div class="form-group{{ $errors->has('excerpt') ? ' has-error' : '' }}">
            <label for="excerpt" class="col-md-2 control-label">摘要</label>
            <div class="col-md-9">
                <input id="excerpt" type="text" class="form-control" name="excerpt" value="{{ old('excerpt') }}"
                       required autocomplete="off" placeholder="摘要中应包含通知的主要内容或注意事项（70字以内）">
                @if ($errors->has('excerpt'))
                    <span class="help-block">
                        <strong>{{ $errors->first('excerpt') }}</strong>
                    </span>
                @endif
            </div>
        </div>

        <div class="form-group{{ $errors->has('content')?' has-error' :'' }}">
            <label for="content" class="col-md-1 control-label"></label>
            <div class="col-md-10">
                <textarea id="content" name="content" cols="80" rows="10" required
                          autocomplete="off">{!! old('content') !!}</textarea>
                @if($errors->has('content'))
                    <span class="help-block">
                        <strong>{{ $errors->first('content') }}</strong>
                    </span>
                @endif
            </div>
        </div>

        <input id="attachment" name="attachment" type="hidden">
        <div class="form-group">
            <div class="col-md-10 col-md-offset-1">
                <div class="panel panel-success">
                    <div class="panel-heading">
                        <h3 class="panel-title">
                            附件列表
                            <span class="btn btn-default btn-sm" id="attachmentBtn">
                                <span class="glyphicon glyphicon-file"></span>
                                添加附件 {{ \App\Http\Controllers\FileController::getLimit() }}
                            </span>
                        </h3>
                    </div>
                    <div class="panel-body{{ $errors->has('attachment') ? ' has-error' : '' }}">
                        <div id="attachmentContainer"></div>
                        @if ($errors->has('attachment'))
                            <span class="help-block">
                                <strong>{{ $errors->first('attachment') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="form-group">
            <div class="col-md-3 pull-right">
                <button type="submit" class="btn btn-primary" onclick="window.onbeforeunload=null;">
                    保存通知
                </button>
            </div>
        </div>
    </form>
@endsection
