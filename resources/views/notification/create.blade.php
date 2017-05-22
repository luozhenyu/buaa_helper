@extends('layouts.app')

@push('cssLink')
<link rel="stylesheet" href="{{ url('/components/bootstrap-select/dist/css/bootstrap-select.min.css') }}">
<link rel="stylesheet" href="{{ url('/components/flatpickr/dist/flatpickr.min.css') }}">

<link href="" rel="stylesheet"/>
@endpush

@push('jsLink')
<script src="{{ url('/components/bootstrap-select/dist/js/bootstrap-select.min.js') }}"></script>
<script src="{{ url('/components/bootstrap-select/dist/js/i18n/defaults-zh_CN.js') }}"></script>

<script src="{{ url('/components/flatpickr/dist/flatpickr.min.js') }}"></script>
<script src="{{ url('/components/flatpickr/dist/l10n/zh.js') }}"></script>

<script src="{{ url('/ckeditor/ckeditor.js') }}"></script>
@endpush

@push('js')
<script>
    $(function () {
        window.onbeforeunload = function () {
            return "您确认要退出此页面?";
        };

        $("#department").selectpicker("val", "{{ old('department')?: Auth::user()->department_id }}");
        $("#important").selectpicker("val", "{{ old('department') }}");

        Flatpickr.localize(Flatpickr.l10ns.zh);
        $("#time").flatpickr({
            enableTime: true,
            altInput: true,
            minDate: "today",
            mode: "range",
            weekNumbers: true
        });

        var editor = CKEDITOR.replace("content");
        editor.on('fileUploadRequest', function (evt) {
            var xhr = evt.data.fileLoader.xhr;
            xhr.setRequestHeader('X-CSRF-TOKEN', $("meta[name='csrf-token']").attr("content"));
        });
    });

    function stringifyFiles() {
        var allFiles = [];
        $("#filesContainer").find("a").each(function (index, element) {
            allFiles.push({"title": element.title, "href": element.href});
        });
        $("#files").val(JSON.stringify(allFiles));
    }
</script>
@endpush

@push("crumb")
<li><a href = "{{ url("/") }}">主页</a></li>
<li><a href = "{{ url("/notification") }}">通知中心</a></li>
<li><a href = "{{ url("/notification/manage") }}">通知管理</a></li>
<li class = "active">创建新通知</li>
@endpush

@section('content')
            <form class="form-horizontal" role="form" method="POST" action="{{ route('notification') }}"
                  onsubmit="stringifyFiles()">
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
                                    id="department" name="department">
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

                <div class="form-group{{ $errors->has('title') ? ' has-error' : '' }}">
                    <label for="time" class="col-md-2 control-label">起止日期</label>
                    <div class="col-md-9">
                        <input id="time" type="text" class="form-control flatpickr flatpickr-input" name="time"
                               placeholder="请选择起止日期" readonly value="{{ old('time') }}" autocomplete="off">

                        @if($errors->has('time'))
                            <span class="help-block">
                                <strong>{{ $errors->first('time') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>

                <div class="form-group{{ $errors->has('important') ? ' has-error' : '' }}">
                    <label for="important" class="col-md-2 control-label">通知类型</label>
                    <div class="col-md-9">
                        <select class="selectpicker form-control{{ $errors->has('important') ? ' has-error' : '' }}"
                                id="important" name="important">
                            <option>普通通知</option>
                            <option value="true">重要通知（要求阅读后确认）</option>
                        </select>

                        @if ($errors->has('important'))
                            <span class="help-block">
                                <strong>{{ $errors->first('important') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>

                <div class="form-group{{ $errors->has('content')?' has-error' :'' }}">
                    <label for="content" class="col-md-1 control-label"></label>
                    <div class="col-md-10">
                        <textarea id="content" name="content" cols="80" rows="10"></textarea>

                        @if($errors->has('content'))
                            <span class="help-block">
                                <strong>{{ $errors->first('content') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>

                <input id="files" type="hidden" name="files">
                <div class="form-group">
                    <div class="col-md-10 col-md-offset-1">
                        <div class="panel panel-success">
                            <div class="panel-heading">
                                <h3 class="panel-title">附件列表</h3>
                            </div>
                            <div id="filesContainer"
                                 class="panel-body{{ $errors->has('files') ? ' has-error' : '' }}">
                                {!! \App\Http\Controllers\NotificationController::insertFile(old('files'),true) !!}
                                @if ($errors->has('files'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('files') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-md-3 pull-right">
                        <button type="submit" class="btn btn-primary"
                                onclick="window.onbeforeunload=null;">
                            保存通知
                        </button>
                    </div>
                </div>
            </form>
@endsection
