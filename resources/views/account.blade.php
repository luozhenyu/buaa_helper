@extends('layouts.app')

@push('cssLink')
<link rel="stylesheet" href="{{ url('/components/bootstrap-select/dist/css/bootstrap-select.min.css') }}">
@endpush

@push("css")
<style>
    .tab-content {
        padding-top: 20px;
        border: 1px solid #ddd;
        border-top: 0 solid #ddd;
        border-bottom-left-radius: 4px;
        border-bottom-right-radius: 4px;
    }

    label.control-label {
        padding-left: 30px;
    }

    #avatarImg {
        width: 150px;
        height: 150px;
    }

</style>
@endpush

@push('jsLink')
<script src="{{ url('/components/bootstrap-select/dist/js/bootstrap-select.min.js') }}"></script>
<script src="{{ url('/components/bootstrap-select/dist/js/i18n/defaults-zh_CN.js') }}"></script>

<script src="{{ url('/js/file_upload.js') }}"></script>
@endpush

@push('js')
<script>
    $(function () {
        $("#department").selectpicker("val", "{{ $user->department->number }}");

        @if($user instanceof \App\Models\Student)
        $("#grade").selectpicker("val", "{{ $user->grade }}");
        $("#class").selectpicker("val", "{{ $user->class }}");
        $("#political_status").selectpicker("val", "{{ $user->political_status }}");
        $("#native_place").selectpicker("val", "{{ $user->native_place }}");
        $("#financial_difficulty").selectpicker("val", "{{ $user->financial_difficulty }}");
        @endif
    });
</script>
@endpush

@push("crumb")
<li><a href="{{ url("/") }}">主页</a></li>
<li class="active">个人中心</li>
@endpush

@section('content')
    <ul id="profileTab" class="nav nav-tabs">
        <li class="active">
            <a href="#tab_profile" data-toggle="tab">个人资料</a>
        </li>
        <li>
            <a href="#tab_password" data-toggle="tab">修改密码</a>
        </li>
    </ul>

    <div id="profileTabContent" class="tab-content">
        <div class="tab-pane fade in active" id="tab_profile">
            <form class="form-horizontal" role="form" method="POST"
                  action="{{ url('/account/profile') }}">
                {{ csrf_field() }}

                <div class="form-group">
                    <label for="avatar" class="col-md-4 control-label">用户头像</label>
                    <div class="col-md-6">
                        <img id="avatarImg" src="{{ $user->avatarUrl }}" class="img-thumbnail">
                    </div>
                </div>

                <div class="form-group">
                    <label for="number" class="col-md-4 control-label">学号／工号</label>
                    <div class="col-md-6">
                        <input id="number" type="text" class="form-control" value="{{ $user->number }}" disabled>
                    </div>
                </div>

                <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                    <label for="name" class="col-md-4 control-label">姓名</label>
                    <div class="col-md-6">
                        <input id="name" type="text" class="form-control" name="name" value="{{ $user->name }}"
                               required autocomplete="off">
                        @if ($errors->has('name'))
                            <span class="help-block">
                        <strong>{{ $errors->first('name') }}</strong>
                    </span>
                        @endif
                    </div>
                </div>

                <div class="form-group{{ $errors->has('department') ? ' has-error' : '' }}">
                    <label for="department" class="col-md-4 control-label">院系或部门</label>
                    <div class="col-md-6">
                        @permission(['modify_all_student', 'modify_admin'])
                        <select class="selectpicker form-control{{ $errors->has('department') ? ' has-error' : '' }}"
                                id="department" name="department">
                            @foreach(\App\Models\Department::orderBy('number')->get() as $department)
                                <option value="{{ $department->number }}">{{ $department->display_name }}</option>
                            @endforeach
                        </select>
                        @else
                            <select class="selectpicker form-control{{ $errors->has('department') ? ' has-error' : '' }}"
                                    id="department" name="department">
                                @php($department = $user->department)
                                <option value="{{ $department->number }}">{{ $department->display_name }}</option>
                            </select>
                            @endpermission

                            @if ($errors->has('department'))
                                <span class="help-block">
                        <strong>{{ $errors->first('department') }}</strong>
                    </span>
                            @endif
                    </div>
                </div>

                <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                    <label for="email" class="col-md-4 control-label">邮箱</label>

                    <div class="col-md-6">
                        <input id="email" type="email" class="form-control" name="email" value="{{ $user->email }}"
                               required autocomplete="off">

                        @if ($errors->has('email'))
                            <span class="help-block">
                                <strong>{{ $errors->first('email') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>

                <div class="form-group{{ $errors->has('phone') ? ' has-error' : '' }}">
                    <label for="phone" class="col-md-4 control-label">手机号</label>

                    <div class="col-md-6">
                        <input id="phone" type="text" class="form-control" name="phone" value="{{ $user->phone }}"
                               required autocomplete="off">

                        @if ($errors->has('phone'))
                            <span class="help-block">
                                <strong>{{ $errors->first('phone') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>

                @if($user instanceof \App\Models\Student)
                    <div class="form-group{{ $errors->has('grade') ? ' has-error' : '' }}">
                        <label for="grade" class="col-md-4 control-label">年级</label>
                        <div class="col-md-6">
                            <select class="selectpicker form-control{{ $errors->has('grade') ? ' has-error' : '' }}"
                                    id="grade" name="grade" title="请选择年级">
                                @if($grade = \App\Models\Property::where('name','grade')->first())
                                    @foreach($grade->propertyValues as $value)
                                        <option value="{{ $value->name }}">{{ $value->display_name }}</option>
                                    @endforeach
                                @endif
                            </select>
                            @if ($errors->has('grade'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('grade') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>


                    <div class="form-group{{ $errors->has('class') ? ' has-error' : '' }}">
                        <label for="class" class="col-md-4 control-label">班级</label>
                        <div class="col-md-6">
                            <select class="selectpicker form-control{{ $errors->has('class') ? ' has-error' : '' }}"
                                    id="class" name="class" title="请选择班级">
                                @if($class = \App\Models\Property::where('name','class')->first())
                                    @foreach($class->propertyValues as $value)
                                        <option value="{{ $value->name }}">{{ $value->display_name }}</option>
                                    @endforeach
                                @endif
                            </select>
                            @if ($errors->has('class'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('class') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="form-group{{ $errors->has('political_status') ? ' has-error' : '' }}">
                        <label for="political_status" class="col-md-4 control-label">政治面貌</label>
                        <div class="col-md-6">
                            <select class="selectpicker form-control{{ $errors->has('political_status') ? ' has-error' : '' }}"
                                    id="political_status" name="political_status" title="请选择政治面貌">
                                @if($political_status = \App\Models\Property::where('name','political_status')->first())
                                    @foreach($political_status->propertyValues as $value)
                                        <option value="{{ $value->name }}">{{ $value->display_name }}</option>
                                    @endforeach
                                @endif
                            </select>
                            @if ($errors->has('political_status'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('political_status') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="form-group{{ $errors->has('native_place') ? ' has-error' : '' }}">
                        <label for="native_place" class="col-md-4 control-label">籍贯</label>
                        <div class="col-md-6">
                            <select class="selectpicker form-control{{ $errors->has('native_place') ? ' has-error' : '' }}"
                                    id="native_place" name="native_place" title="请选择籍贯">
                                @if($native_place = \App\Models\Property::where('name','native_place')->first())
                                    @foreach($native_place->propertyValues as $value)
                                        <option value="{{ $value->name }}">{{ $value->display_name }}</option>
                                    @endforeach
                                @endif
                            </select>
                            @if ($errors->has('native_place'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('native_place') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="form-group{{ $errors->has('financial_difficulty') ? ' has-error' : '' }}">
                        <label for="financial_difficulty" class="col-md-4 control-label">经济情况</label>
                        <div class="col-md-6">
                            <select class="selectpicker form-control{{ $errors->has('financial_difficulty') ? ' has-error' : '' }}"
                                    id="financial_difficulty" name="financial_difficulty" title="请选择经济情况">
                                @if($financial_difficulty = \App\Models\Property::where('name','financial_difficulty')->first())
                                    @foreach($financial_difficulty->propertyValues as $value)
                                        <option value="{{ $value->name }}">{{ $value->display_name }}</option>
                                    @endforeach
                                @endif
                            </select>
                            @if ($errors->has('financial_difficulty'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('financial_difficulty') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>
                @endif

                <div class="form-group">
                    <div class="col-md-8 col-md-offset-4">
                        <button type="submit" class="btn btn-primary">
                            <span class="glyphicon glyphicon-ok"></span>&nbsp;保存头像及信息
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <div class="tab-pane fade" id="tab_password">
            <form class="form-horizontal" role="form" method="POST" action="{{ url('/account/password') }}">
                {{ csrf_field() }}
                <div class="form-group{{ $errors->has('old_password') ? ' has-error' : '' }}">
                    <label for="old_password" class="col-md-3 col-xs-2 control-label">当前密码</label>

                    <div class="col-md-6 col-xs-9 col-sm-8">
                        <input id="old_password" type="password" class="form-control" name="old_password"
                               required>

                        @if ($errors->has('old_password'))
                            <span class="help-block">
                                <strong>{{ $errors->first('old_password') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>

                <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                    <label for="password" class="col-md-3 col-xs-2 control-label">新密码</label>

                    <div class="col-md-6 col-xs-9 col-sm-8">
                        <input id="password" type="password" class="form-control" name="password" required
                               autocomplete="off">

                        @if ($errors->has('password'))
                            <span class="help-block">
                                <strong>{{ $errors->first('password') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>

                <div class="form-group">
                    <label for="password-confirm" class="col-md-3 col-xs-2 control-label">确认密码</label>

                    <div class="col-md-6 col-xs-9 col-sm-8">
                        <input id="password-confirm" type="password" class="form-control" name="password_confirmation"
                               required autocomplete="off">
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-md-8 col-md-offset-4">
                        <button type="submit" class="btn btn-primary">
                            提交
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
