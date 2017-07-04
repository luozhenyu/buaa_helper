@extends('layouts.mobile')

@section('title','修改信息')

@section('content')
    <form class="form-horizontal" role="form" method="POST" action="{{ url()->current() }}">
        {{ csrf_field() }}

        <input type="hidden" name="access_token" value="{{ $access_token }}">

        <div class="form-group">
            <label for="number" class="col-md-4 control-label">学号</label>

            <div class="col-md-6">
                <input id="number" type="text" class="form-control" name="number"
                       value="{{ $auth_user->number }}" disabled>
            </div>
        </div>

        <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
            <label for="name" class="col-md-4 control-label">姓名</label>

            <div class="col-md-6">
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
            <label for="email" class="col-md-4 control-label">邮箱</label>

            <div class="col-md-6">
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
            <label for="phone" class="col-md-4 control-label">手机号</label>

            <div class="col-md-6">
                <input id="phone" type="number" class="form-control" name="phone"
                       value="{{ $auth_user->phone }}">

                @if ($errors->has('phone'))
                    <span class="help-block">
                        <strong>{{ $errors->first('phone') }}</strong>
                    </span>
                @endif
            </div>
        </div>

        <div class="form-group">
            <button type="submit" class="btn btn-primary btn-block">
                保存
            </button>
        </div>
    </form>
@endsection