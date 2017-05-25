@extends('layouts.app')

@push("css")
<style>
    /* 背景图 */
    @media (min-width: 992px) {
        #back_div {
            background: url({{ url('/img/bk2.jpg') }}) -80px -280px;
            background-size: 1750px;
        }
    }

    @media (max-width: 991px) and (min-width: 768px) {
        #back_div {
            background: url({{ url('/img/bk2.jpg') }}) -600px -200px;
            background-size: 1600px;
        }
    }

    @media (max-width: 767px) {
        #back_div {
            background: url("{{ url('/img/bk2.jpg') }}") -800px -200px;
            background-size: 1600px;
        }
    }

    #back_div, #title_one, #title_two, #btn_area {
        filter: alpha(opacity=1);
        -moz-opacity: 0.01;
        opacity: 0.01;
    }

    #main_div {
        margin-bottom: 15px;
    }

    /* 背景区，内容区 */
    #back_div, #content_div {
        position: absolute;
        left: 0;
        top: 0;
        right: 0;
        bottom: 0;
        height: 100%
    }

    @media (min-width: 768px ) {
        #title_one {
            font-size: 70px;
        }

        #title_two {
            font-weight: 800;
            font-size: 50px;
        }

        #main_div, #back_div {
            min-height: 500px;
        }

        #btn_area {
            margin-top: 30px;
        }
    }

    @media (max-width: 767px ) {
        #title_one {
            font-size: 50px;
        }

        #title_two {
            font-weight: 600;
            font-size: 30px;
        }

        #main_div, #back_div {
            min-height: 400px;
        }

        #btn_area {
            margin-top: 20px;
        }
    }

    #title_one {
        -webkit-text-stroke: 1px #e8f5fd;
        color: #975860;
        font-weight: bold;
    }

    #title_two {
    }

    .btn {
        font-size: 25px;
    }

    #btn_area a {
        width: 100%;
        margin-bottom: 7px;
    }
</style>
@endpush

@push("js")
<script>
    $(function () {
        $("#back_div").fadeTo(800, 1).delay(950).fadeTo(640, 0.65);
        $("#title_one").delay(450).fadeTo(320, 1);
        $("#title_two").delay(700).fadeTo(320, 1);
        $("#btn_area").delay(1150).fadeTo(400, 1);
    });
</script>
@endpush

{{--@section('content')
    <div style = "text-align: center;">
        <h2>欢迎使用 {{ config('app.name', 'Laravel') }}</h2>
    </div>
@endsection--}}

@section("content2")
    <div id="main_div" style="position:relative">
        <div id="back_div"></div>
        <div id="content_div">
            <div class="container" style="margin-bottom: 20px; padding: 0;">
                <div class="row" style="margin: 20px;">
                    <div class="col-xs-12" style="text-align:center;">
                        <h1 id="title_one">欢迎使用</h1>
                        <h1 id="title_two">{{ config('app.name', 'Laravel') }}</h1>
                        <div id="btn_area">
                            <div class="col-md-4 col-md-offset-4 col-xs-9 col-xs-offset-1 col-sm-6 col-sm-offset-3">
                                @if (Auth::guest())
                                    <a href="{{ url('/login') }}" class="btn btn-info ">开始使用</a>
                                @else
                                    @permission(['view_all_user','view_owned_user'])
                                    <a href="{{ route('accountManager') }}" class="btn btn-warning">用户管理</a>
                                    @endpermission
                                    <a href="{{ url('/notification') }}" class="btn btn-info">
                                        通知
                                    {{-- 预留：未读消息 --}}
                                    <!--<span class="badge">50</span>-->
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection