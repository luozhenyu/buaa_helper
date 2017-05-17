@extends('layouts.app')
@php($auth_user = Auth::user())

@push('cssLink')
<link rel="stylesheet" href="//cdn.bootcss.com/bootstrap-select/1.12.1/css/bootstrap-select.min.css">
@endpush

@push('jsLink')
<script src="//cdn.bootcss.com/bootstrap-select/1.12.1/js/bootstrap-select.min.js"></script>
<script src="//cdn.bootcss.com/bootstrap-select/1.12.1/js/i18n/defaults-zh_CN.js"></script>
@endpush

@push('js')
<script>
    $(function () {
        $("#department").selectpicker("val", "{{old('department')}}");
        $("#role").selectpicker("val", "{{ old('role')?:\App\Func\RoleDef::NORMAL['id'] }}");
    });
</script>
@endpush

@push("crumb")
<li><a href="{{ url("/") }}">主页</a></li>
<li><a href="{{ url("/account_manager") }}">用户管理</a></li>
<li class="active">创建新用户</li>
@endpush


@section('content')
    <div class="panel panel-default">
        <div class="panel-heading">新用户</div>

        <div class="panel-body">
            <form class="form-horizontal" role="form" method="POST"
                  action="{{ route('accountManager') }}">
                {{ csrf_field() }}

                <div class="form-group{{ $errors->has('number') ? ' has-error' : '' }}">
                    <label for="number" class="col-md-4 control-label">学号／工号</label>
                    <div class="col-md-6">
                        <input id="number" type="text" class="form-control" name="number"
                               value="{{ old('number') }}"
                               required autocomplete="off">
                        @if ($errors->has('number'))
                            <span class="help-block">
                                        <strong>{{ $errors->first('number') }}</strong>
                                    </span>
                        @endif
                    </div>
                </div>

                <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                    <label for="name" class="col-md-4 control-label">姓名</label>
                    <div class="col-md-6">
                        <input id="name" type="text" class="form-control" name="name"
                               value="{{ old('name') }}" required autocomplete="off">
                        @if ($errors->has('name'))
                            <span class="help-block">
                                            <strong>{{ $errors->first('name') }}</strong>
                                        </span>
                        @endif
                    </div>
                </div>

                <div class="form-group{{ $errors->has('department') ? ' has-error' : '' }}">
                    <label for="department" class="col-md-4 control-label">院系</label>
                    <div class="col-md-6">
                        <select class="selectpicker form-control{{ $errors->has('department') ? ' has-error' : '' }}"
                                id="department" name="department" autocomplete="off" required
                                title="请选取用户所属的院系">
                            @foreach(\App\Models\Department::get() as $department)
                                <option value="{{ $department->id }}">{{ ($department->number<100?$department->number.'-':'').$department->name }}</option>
                            @endforeach
                        </select>

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
                        <input id="email" type="email" class="form-control" name="email"
                               value="{{ old('email') }}" autocomplete="off">

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
                        <input id="phone" type="text" class="form-control" name="phone"
                               value="{{ old('phone') }}" autocomplete="off">
                        @if ($errors->has('phone'))
                            <span class="help-block">
                                            <strong>{{ $errors->first('phone') }}</strong>
                                        </span>
                        @endif
                    </div>
                </div>

                <div class="form-group{{ $errors->has('role') ? ' has-error' : '' }}">
                    <label for="role" class="col-md-4 control-label">账号类型</label>
                    <div class="col-md-6">
                        <select class="selectpicker form-control{{ $errors->has('department') ? ' has-error' : '' }}"
                                id="role" name="role">
                            @foreach(\App\Func\RoleDef::dict as $role)
                                @if(\App\Func\RoleDef::isChild($auth_user->role_id, $role['id']))
                                    <option value="{{ $role['id'] }}">{{ $role['name'] }}</option>
                                @endif
                            @endforeach
                        </select>
                        @if ($errors->has('role'))
                            <span class="help-block">
                                            <strong>{{ $errors->first('role') }}</strong>
                                        </span>
                        @endif
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-md-8 col-md-offset-4">
                        <button type="submit" class="btn btn-primary">
                            <span class="glyphicon glyphicon-ok"></span> 保存信息
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

@endsection
