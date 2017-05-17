@extends('layouts.app')

@php($auth_user = Auth::user())

@push("css")
<style>
    .tab-content {
        padding-top: 20px;
        border: 1px solid #ddd;
        border-top: 0px solid #ddd;
        border-bottom-left-radius: 4px;
        border-bottom-right-radius: 4px;
    }

    label.control-label {
        padding-left: 30px;
    }
</style>
@endpush

@push("crumb")
<li><a href="{{ url("/") }}">主页</a></li>
<li class="active">个人中心</li>
@endpush

@section('content')
    <div class="panel panel-default">
        <div class="panel-heading">个人中心</div>
        <div class="panel-body">
            <ul id="myTab" class="nav nav-tabs">
                <li class="active">
                    <a href="#tab_profile" data-toggle="tab">个人资料</a>
                </li>
                <li>
                    <a href="#tab_password" data-toggle="tab">修改密码</a>
                </li>
            </ul>

            <div id="myTabContent" class="tab-content">
                <div class="tab-pane fade in active" id="tab_profile">
                    <form class="form-horizontal" role="form" method="POST"
                          action="{{ url('/account/profile') }}">
                        {{ csrf_field() }}

                        <div class="form-group">
                            <label for="number" class="col-md-3 col-xs-2 control-label">学号</label>

                            <div class="col-md-6 col-xs-9 col-sm-8">
                                <input id="number" type="text" class="form-control" name="number"
                                       value="{{ $auth_user->number }}" disabled>
                            </div>
                        </div>

                        <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                            <label for="name" class="col-md-3 col-xs-2 control-label">姓名</label>

                            <div class="col-md-6 col-xs-9 col-sm-8">
                                <input id="name" type="text" class="form-control" name="name"
                                       value="{{ $auth_user->name }}" required disabled>

                                @if ($errors->has('name'))
                                    <span class="help-block">
                                                    <strong>{{ $errors->first('name') }}</strong>
                                                </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                            <label for="email" class="col-md-3 col-xs-2 control-label">邮箱</label>

                            <div class="col-md-6 col-xs-9 col-sm-8">
                                <input id="email" type="email" class="form-control" name="email"
                                       value="{{ $auth_user->email }}">

                                @if ($errors->has('email'))
                                    <span class="help-block">
                                                    <strong>{{ $errors->first('email') }}</strong>
                                                </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group{{ $errors->has('phone') ? ' has-error' : '' }}">
                            <label for="phone" class="col-md-3 col-xs-2 control-label">手机号</label>

                            <div class="col-md-6 col-xs-9 col-sm-8">
                                <input id="phone" type="text" class="form-control" name="phone"
                                       value="{{ $auth_user->phone }}">

                                @if ($errors->has('phone'))
                                    <span class="help-block">
                                                    <strong>{{ $errors->first('phone') }}</strong>
                                                </span>
                                @endif
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

                <div class="tab-pane fade" id="tab_password">
                    <form class="form-horizontal" role="form" method="POST"
                          action="{{ url('/account/password') }}">
                        {{ csrf_field() }}
                        <div class="form-group{{ $errors->has('old_password') ? ' has-error' : '' }}">
                            <label for="old_password" class="col-md-3 col-xs-2 control-label">当前密码</label>

                            <div class="col-md-6 col-xs-9 col-sm-8">
                                <input id="old_password" type="password" class="form-control"
                                       name="old_password"
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
                                <input id="password" type="password" class="form-control" name="password"
                                       required>

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
                                <input id="password-confirm" type="password" class="form-control"
                                       name="password_confirmation" required>
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
        </div>
    </div>
@endsection
