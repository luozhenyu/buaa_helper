@extends('layouts.app')

@section('content')
    <div class="panel panel-default">
        <div class="panel-heading">注册号码</div>
        <div class="panel-body">
            @if(isset($user))
                <form class="form-horizontal" role="form" method="post"
                      action="{{ url('/register/'.$user->number) }}">
                    {{ csrf_field() }}

                    <div class="form-group">
                        <label for="user_id" class="col-md-4 control-label">学号</label>

                        <div class="col-md-6">
                            <input id="user_id" type="text" class="form-control"
                                   value="{{ $user->number }}" disabled>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="name" class="col-md-4 control-label">姓名</label>

                        <div class="col-md-6">
                            <input id="name" type="text" class="form-control"
                                   value="{{ $user->name }}" disabled>
                        </div>
                    </div>

                    <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                        <label for="password" class="col-md-4 control-label">密码</label>

                        <div class="col-md-6">
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
                        <label for="password-confirm" class="col-md-4 control-label">确认密码</label>

                        <div class="col-md-6">
                            <input id="password-confirm" type="password" class="form-control"
                                   name="password_confirmation" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-md-6 col-md-offset-4">
                            <button type="submit" class="btn btn-primary">
                                立即注册
                            </button>
                        </div>
                    </div>
                </form>
            @else
                <form class="form-horizontal" role="form" method="post" action="{{ url('/register') }}">
                    {{ csrf_field() }}

                    <div class="form-group{{ $errors->has('number') ? ' has-error' : '' }}">
                        <label for="number" class="col-md-4 control-label">学号</label>

                        <div class="col-md-6">
                            <input id="number" type="text" class="form-control" name="number"
                                   value="{{ old('number') }}" required>

                            @if ($errors->has('number'))
                                <span class="help-block">
                                                <strong>{{ $errors->first('number') }}</strong>
                                            </span>
                            @endif
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-md-6 col-md-offset-4">
                            <button type="submit" class="btn btn-primary">
                                下一步
                            </button>
                        </div>
                    </div>
                </form>
            @endif
        </div>
    </div>
@endsection
