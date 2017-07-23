@extends('layouts.app')

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

    <script src="{{ url('/js/user_select.js') }}"></script>
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
                    @if(old('start_date'))
                    new Date("{{ old('start_date') }}"),
                    @endif
                    @if(old('finish_date'))
                    new Date("{{ old('finish_date') }}")
                    @endif
                ],
                onChange: function (selectedDates, dateStr, instance) {
                    if (selectedDates[0] && selectedDates[1]) {
                        if (selectedDates[1] < new Date(Date.now() + 2 * 86400 * 1000)
                            && !confirm("截止时间在48小时内，请确认此通知是否如此紧急！")) {
                            instance.clear();
                            return;
                        }
                        $("#start_date").val(selectedDates[0].toISOString());
                        $("#finish_date").val(selectedDates[1].toISOString());
                    }
                }
            });

            var editor = CKEDITOR.replace("content");
            editor.on("fileUploadRequest", function (evt) {
                var xhr = evt.data.fileLoader.xhr;
                xhr.setRequestHeader("X-CSRF-TOKEN", $("meta[name='csrf-token']").attr("content"));
            });

            $("#attachmentBtn").click(function () {
                $(this).upload({
                    url: "{{ route('upload') }}",
                    success: function (json) {
                        if (json.uploaded) {
                            $("#attachmentContainer").append(parseFile(json, true));
                        } else {
                            alert(json.message);
                        }
                    }
                });
            });

            $("#important").selectpicker("val", "{{ old('important') }}");
            var files = {!! collect(old('attachment'))->map(function ($item, $key) {
        if ($file = \App\Models\File::where('hash', $item)->first()) {return $file->file_info;}})->toJson() !!};
            for (var i = 0; i < files.length; i++) {
                $("#attachmentContainer").append(parseFile(files[i], true));
            }

            //TARGET SELECT
            function ajaxQuery(condition) {
                condition = condition || {type: "student"};

                $.ajax({
                    url: "/account_manager/ajax",
                    type: 'POST',
                    contentType: "application/json; charset=utf-8",
                    data: JSON.stringify(condition),
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr("content")
                    },
                    success: function (json) {
                        $("#target_count").html("共有 <b>" + json.total + "</b> 名学生满足条件");
                        $("#target_value").val(condition);

                        $("#target_confirm").unbind().click(function () {
                            $("#target_value").val(JSON.stringify(condition));
                            $("#target").val("已选" + json.total + "人");
                        });
                    }
                });
            }

            $("#target_select").user_select({
                data: {!! json_encode($selectData) !!},
                callback_filter: function (data) {  //单击筛选
                    ajaxQuery({
                        range: data.departments,
                        property: data.properties,
                        search: data.search
                    });
                }
            });

            $("#target_clear").click(function () {
                $("#target_value").val("");
                $("#target").val("");
            });
        });
    </script>
@endpush

@push("crumb")
    <li><a href="{{ url("/") }}">主页</a></li>
    <li><a href="{{ route('notification') }}">通知中心</a></li>
    <li><a href="{{ route('notification').'/draft' }}">草稿箱</a></li>
    <li class="active">创建新通知</li>
@endpush

@section('content')
    <form class="form-horizontal" id="form" method="POST" action="{{ route('notification') }}">
        {{ csrf_field() }}

        <div class="form-group{{ $errors->has('title') ? ' has-error' : '' }}">
            <label for="title" class="col-md-2 control-label">标题</label>
            <div class="col-md-9">
                <input id="title" class="form-control" name="title" value="{{ old('title') }}"
                       required autocomplete="off">
                @if ($errors->has('title'))
                    <span class="help-block">
                        <strong>{{ $errors->first('title') }}</strong>
                    </span>
                @endif
            </div>
        </div>

        <div class="form-group">
            <label for="department" class="col-md-2 control-label">发布院系/部门</label>
            <div class="col-md-9">
                <select class="selectpicker form-control" id="department" disabled>
                    @php
                        $department = Auth::user()->department
                    @endphp
                    <option value="{{ $department->number }}">{{ $department->display_name }}</option>
                </select>
            </div>
        </div>

        <div class="form-group{{ $errors->has('important') ? ' has-error' : '' }}">
            <label for="important" class="col-md-2 control-label">通知类型</label>
            <div class="col-md-9">
                <select class="selectpicker form-control{{ $errors->has('important') ? ' has-error' : '' }}"
                        id="important" name="important" required autocomplete="off">
                    <option value="0">普通通知</option>
                    <option value="1">必读通知（要求阅读后确认）</option>
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
                <input id="excerpt" class="form-control" name="excerpt" value="{{ old('excerpt') }}"
                       required autocomplete="off" maxlength="70" placeholder="摘要中应包含通知的主要内容或注意事项（70字以内）">
                @if ($errors->has('excerpt'))
                    <span class="help-block">
                        <strong>{{ $errors->first('excerpt') }}</strong>
                    </span>
                @endif
            </div>
        </div>

        <input id="start_date" name="start_date" type="hidden" value="{{ old('start_date') }}">
        <input id="finish_date" name="finish_date" type="hidden" value="{{ old('finish_date') }}">
        <div class="form-group{{ $errors->has('start_date') || $errors->has('finish_date') ? ' has-error' : '' }}">
            <label for="timeRange" class="col-md-2 control-label">起止日期</label>
            <div class="col-md-9">
                <input id="timeRange" class="form-control flatpickr flatpickr-input"
                       placeholder="请选择起止日期" readonly autocomplete="off">

                @if($errors->has('start_date') || $errors->has('finish_date'))
                    <span class="help-block">
                        <strong>{{ $errors->first('start_date') . $errors->first('finish_date') }}</strong>
                    </span>
                @endif
            </div>
        </div>

        <input id="target_value" name="target" type="hidden" value="{{ old('target') }}">
        <div class="form-group{{ $errors->has('target') ? ' has-error' : '' }}">
            <label for="target" class="col-md-2 control-label">通知对象</label>
            <div class="col-md-9">
                <input id="target" class="form-control" name="target_hint" placeholder="请选择通知对象" readonly
                       autocomplete="off" data-toggle="modal" data-target="#targetModal"
                       value="{{ old('target_hint') }}">
                @if ($errors->has('target'))
                    <span class="help-block">
                        <strong>{{ $errors->first('target') }}</strong>
                    </span>
                @endif
            </div>
        </div>
        <!-- BEGIN TARGET -->
        <div class="modal fade" id="targetModal" tabindex="-1" role="dialog"
             aria-labelledby="targetModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                            &times;
                        </button>
                        <h4 class="modal-title" id="targetModalLabel">通知对象选择</h4>
                    </div>

                    <div class="modal-body container">
                        <div class="col-xs-6">
                            <div id="target_select"></div>
                        </div>
                        <div class="col-xs-6">
                            <div id="target_count"></div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" id="target_clear" data-dismiss="modal">清除所有
                        </button>
                        <button type="button" class="btn btn-primary" id="target_confirm" data-dismiss="modal">确认选择
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <!-- END TARGET -->

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

        <div class="form-group">
            <div class="col-md-10 col-md-offset-1">
                <div class="panel panel-success">
                    <div class="panel-heading">
                        <h3 class="panel-title">
                            附件列表
                            <span class="btn btn-default btn-sm" id="attachmentBtn">
                                <span class="glyphicon glyphicon-file"></span>
                                添加附件 {{ \App\Http\Controllers\FileController::uploadLimitHit() }}
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
                <button class="btn btn-primary" onclick="window.onbeforeunload=null;">
                    存入草稿箱并预览
                </button>
            </div>
        </div>
    </form>
@endsection
