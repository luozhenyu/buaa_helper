@extends('layouts.mobile')

@section('title','个人信息')

@section('content')
    <form class="form-horizontal" role="form" method="POST" action="{{ url()->current() }}">
        {{ csrf_field() }}

        <input type="hidden" name="access_token" value="{{ $access_token }}">

        <div class="form-group{{ $errors->has('old_password') ? ' has-error' : '' }}">
            <label for="old_password" class="col-md-4 control-label">当前密码</label>
            <div class="col-md-6">
                <input id="old_password" type="password" class="form-control" name="old_password" required>
                @if ($errors->has('old_password'))
                    <span class="help-block">
                        <strong>{{ $errors->first('old_password') }}</strong>
                    </span>
                @endif
            </div>
        </div>

        <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
            <label for="password" class="col-md-4 control-label">新密码</label>
            <div class="col-md-6">
                <input id="password" type="password" class="form-control" name="password" required>
                @if ($errors->has('password'))
                    <span class="help-block">
                        <strong>{{ $errors->first('password') }}</strong>
                    </span>
                @endif
            </div>
        </div>

        <div class="form-group">
            <label for="password-confirm" class="col-md-4 control-label">确认密码</label>
            <div class="col-md-6">
                <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required>
            </div>
        </div>

        <div class="form-group">
            <button type="submit" class="btn btn-primary btn-block">
                保存
            </button>
        </div>
    </form>

@endsection