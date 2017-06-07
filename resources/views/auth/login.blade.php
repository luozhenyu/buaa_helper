@extends('layouts.index')

@push("css_2")
<style>
    .form-horizontal label.control-label {
        text-align: left;
        margin-bottom: 5px;
        padding-top: 7px;
    }

    span.input-group-addon {
        height: 19px;
    }

    #login_box {
        background-color: white;
        border-radius: 10px;
        margin-top: 100px;
        margin-bottom: 100px;
    }

    @media (max-height: 542px) {
        #login_box {
            margin-top: 6px;
            margin-bottom: 6px;
        }
    }

    @media (max-height: 572px) and (min-height: 543px) {
        #login_box {
            margin-top: 15px;
            margin-bottom: 15px;
        }
    }

    @media (max-height: 604px) and (min-height: 573px) {
        #login_box {
            margin-top: 30px;
            margin-bottom: 30px;
        }
    }

    @media (max-height: 650px) and (min-height: 605px) {
        #login_box {
            margin-top: 45px;
            margin-bottom: 45px;
        }
    }

    @media (max-height: 768px) and (min-height: 651px) {
        #login_box {
            margin-top: 65px;
            margin-bottom: 65px;
        }
    }

    #login_box {
        filter: alpha(opacity=1); /*IE滤镜，透明度50%*/
        -moz-opacity: 0.01; /*Firefox私有，透明度50%*/
        opacity: 0.01; /*其他，透明度50%*/
    }
</style>
@endpush

@push("js_2")
<script>
    $(function () {
        $("#back_div").fadeTo(1200, 0.8).delay(450).fadeTo(640, 0.7);
        $("#login_box").delay(400).fadeTo(600, 1);
    });
</script>
@endpush

@section('content_full_2')
    <td style="margin-bottom: 20px; padding: 0;vertical-align: middle;width: 100%;">
        <div class="row" style="margin: 20px;">
            <div class="col-xs-12" style="text-align:center;">
                <div id="login_box"
                     class="col-xs-12 col-sm-offset-6 col-sm-6 col-md-offset-8 col-md-4 col-lg-offset-7 col-lg-4">
                    <div class = "col-lg-10 col-lg-offset-1">
                        <h2 style="text-align: center;padding-bottom: 20px;margin-top: 30px;">用户登录</h2>
                        <form class="form-horizontal" role="form" method="POST" action="{{ url('/login') }}">
                            {{ csrf_field() }}

                            <div class="form-group{{ $errors->has('user') ? ' has-error' : '' }}">

                                <div class="col-xs-12">
                                    <div class="input-group">
                                    <span class="input-group-addon">
                                        <span class="glyphicon glyphicon-user"></span>
                                    </span>
                                        <input id="user" type="text" class="form-control" name="user"
                                               placeholder="学号 / 手机号 / 邮箱" value="{{ old('user') }}" required autofocus>
                                    </div>

                                    @if ($errors->has('user'))
                                        <span class="help-block">
                                        <strong>{{ $errors->first('user') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">

                                <div class="col-xs-12">
                                    <div class="input-group">
                                    <span class="input-group-addon">
                                        <span class="glyphicon glyphicon-lock"></span>
                                    </span>
                                        <input id="password" type="password" class="form-control" name="password"
                                               placeholder="密码" required>
                                    </div>

                                    @if ($errors->has('password'))
                                        <span class="help-block">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="col-md-12">
                                    <div class="checkbox">
                                        <label>
                                            <input type="checkbox"
                                                   name="remember" {{ old('remember') ? 'checked' : ''}}>下次自动登录
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="col-md-12">
                                    <button type="submit" class="btn btn-primary col-xs-5 col-sm-offset-1 col-sm-4 ">
                                        登录
                                    </button>

                                    <a href="{{ url('/register') }}"
                                       class="btn btn-info col-xs-5 col-xs-offset-2 col-sm-offset-2 col-sm-4">
                                        注册
                                    </a>
                                </div>
                            </div>

                            <div class="form-group">
                                <a class="btn btn-link" href="{{ url('/password/reset') }}">
                                    忘记密码?
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </td>
@endsection
