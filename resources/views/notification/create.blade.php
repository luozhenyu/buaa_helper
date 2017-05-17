@extends('layouts.app')
@php($auth_user = Auth::user())

@push('cssLink')
<link rel="stylesheet" href="//cdn.bootcss.com/bootstrap-select/1.12.1/css/bootstrap-select.min.css">
<link rel="stylesheet" href="//cdn.bootcss.com/flatpickr/2.6.1/flatpickr.min.css">
@endpush

@push('jsLink')
<script src="//cdn.bootcss.com/bootstrap-select/1.12.1/js/bootstrap-select.min.js"></script>
<script src="//cdn.bootcss.com/bootstrap-select/1.12.1/js/i18n/defaults-zh_CN.js"></script>

<script src="//cdn.bootcss.com/flatpickr/2.6.1/flatpickr.min.js"></script>
<script src="//cdn.bootcss.com/flatpickr/2.6.1/l10n/zh.js"></script>

<script src="{{url('/ueditor/ueditor.config.js')}}"></script>
<script src="{{url('/ueditor/ueditor.all.js')}}"></script>
@endpush

@push('js')
<script>
    $(function () {
        window.onbeforeunload = function () {
            return "您确认要退出此页面?";
        };

        $("#department").selectpicker("val", "{{ old('department') }}");

        Flatpickr.localize(Flatpickr.l10ns.zh);
        $("#time").flatpickr({
            enableTime: true,
            altInput: true,
            minDate: "today",
            mode: "range",
            weekNumbers: true
        });

        var ue = UE.getEditor("container");
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

@section('content')
                <div class="panel panel-default">
                    <div class="panel-heading">创建通知</div>

                    <div class="panel-body">
                        <form class="form-horizontal" role="form" method="POST"
                              action="{{ route('notification') }}"
                              onsubmit="stringifyFiles()">
                            {{ csrf_field() }}

                            <div class="form-group{{ $errors->has('title') ? ' has-error' : '' }}">
                                <label for="title" class="col-md-2 control-label">标题</label>
                                <div class="col-md-9">
                                    <input id="title" type="text" class="form-control" name="title"
                                           value="{{ old('title') }}" required autocomplete="off">
                                    @if ($errors->has('title'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('title') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group{{ $errors->has('department') ? ' has-error' : '' }}">
                                <label for="department" class="col-md-2 control-label">发布院系或部门</label>
                                <div class="col-md-9">
                                    <select class="selectpicker form-control{{ $errors->has('department') ? ' has-error' : '' }}"
                                            id="department" name="department">
                                        @if($auth_user->canDo(\App\Func\PrivilegeDef::VIEW_ALL_USER))
                                            @foreach(\App\Models\Department::get() as $department)
                                                <option value="{{ $department->id }}">{{ ($department->number<100?$department->number.'-':'').$department->name }}</option>
                                            @endforeach
                                        @else
                                            @php($department = $auth_user->department)
                                            <option value="{{ $department->id }}">{{ ($department->number<100?$department->number.'-':'').$department->name }}</option>
                                        @endif
                                    </select>

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
                                    <input id="time" type="text" class="form-control flatpickr flatpickr-input"
                                           name="time" placeholder="请选择起止日期" readonly
                                           value="{{ old('time') }}" autocomplete="off">

                                    @if ($errors->has('time'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('time') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group{{ $errors->has('content') ? ' has-error' : '' }}">
                                <div class="col-md-10 col-md-offset-1">
                                    <script id="container" name="content"
                                            type="text/plain">{!! old('content') !!}</script>
                                    @if ($errors->has('content'))
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

                            <div class="form-group{{ $errors->has('end_time') ? ' has-error' : '' }}">
                                <div class="col-md-offset-7 col-md-5">
                                    <div class="checkbox">
                                        <label>
                                            <input type="checkbox" name="important"
                                                   autocomplete="off" {{ old('important')==='on'? 'checked' :'' }}>
                                            这是重要通知（要求仔细阅读）
                                        </label>
                                    </div>
                                </div>

                                @if ($errors->has('important'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('important') }}</strong>
                                    </span>
                                @endif
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
                    </div>
                </div>
@endsection
