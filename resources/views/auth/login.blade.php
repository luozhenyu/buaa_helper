@extends('layouts.app')

@section('content')
            <h2 style = "text-align: center;padding-bottom: 20px">用户登录</h2>
            <form class="form-horizontal" role="form" method="POST" action="{{ url('/login') }}">
                {{ csrf_field() }}

                <div class="form-group{{ $errors->has('user') ? ' has-error' : '' }}">
                    <label for="user" class="col-md-4 control-label">学号／手机号／邮箱</label>

                    <div class="col-md-6">
                        <input id="user" type="text" class="form-control" name="user"
                               value="{{ old('user') }}"
                               required autofocus>

                        @if ($errors->has('user'))
                            <span class="help-block">
                                        <strong>{{ $errors->first('user') }}</strong>
                                    </span>
                        @endif
                    </div>
                </div>

                <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                    <label for="password" class="col-md-4 control-label">密码</label>

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
                    <div class="col-md-6 col-md-offset-4">
                        <div class="checkbox">
                            <label>
                                <input type="checkbox"
                                       name="remember" {{ old('remember') ? 'checked' : ''}}>下次自动登录
                            </label>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-md-8 col-md-offset-4">
                        <button type="submit" class="btn btn-primary">
                            登录
                        </button>

                        <a class="btn btn-link" href="{{ url('/password/reset') }}">
                            忘记密码?
                        </a>
                    </div>
                </div>
            </form>
@endsection
