@extends('layouts.app_full')

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

    #back_div, #title_one, #title_two, #tool_area {
        filter: alpha(opacity=1);
        -moz-opacity: 0.01;
        opacity: 0.01;
    }

    #main_div {
        margin-bottom: 15px;
    }

    /* 背景区，内容区 */
    #back_div, #content_div, .background_div, .content_div {
        position: absolute;
        left: 0;
        top: 0;
        right: 0;
        bottom: 0;
        height: 100%
    }

    @media (min-width: 768px ) {
        #title_one {
            font-size: 55px;
        }

        #title_two {
            font-weight: 800;
            font-size: 38px;
        }

        #main_div, #back_div {
            min-height: 500px;
        }

        #tool_area {
            margin-top: 30px;
        }

        .functions .function_block {
            margin-left: 1%;
            width: 47.5%;
            margin-right: 1%;
        }

        .function_btn {
            margin-bottom: 12px;
            padding: 18px 24px;
        }

        .function_btn .icon {
            font-size: 40px;
        }

        .function_btn .content p {
            font-size: 18px;
        }

        .function_btn .content .content_title {
            font-size: 25px;
        }

        .function_btn .content_box {
            margin-right: 40px;
        }
    }

    @media (max-width: 767px ) {
        #title_one {
            font-size: 40px;
            margin-top: 7px;
        }

        #title_two {
            font-weight: 600;
            font-size: 28px;
        }

        #main_div, #back_div {
            min-height: 400px;
        }

        #tool_area {
            /*margin-top: 18px;*/
        }

        .functions .function_block {
            margin-left: 0%;
            margin-right: 0%;
            width: 100%;
        }

        .function_btn {
            margin-bottom: 8px;
            padding: 8px 10px;
        }

        .function_btn .icon {
            font-size: 18px;
        }

        .function_btn .content p {
            font-size: 12px;
        }

        .function_btn .content .content_title {
            font-size: 18px;
        }

        .function_btn .content_box {
            margin-right: 18px;
        }
    }

    .functions {

    }

    .functions .function_block {
        display: inline-block;
        /*margin-bottom: 15px;*/
        vertical-align: middle;
    }

    .function_btn {
        text-align: left;
    }

    #title_one {
        -webkit-text-stroke: 0.8px #e8f5fd;
        color: #975860;
        font-weight: bold;
    }

    #title_two {
    }

    .btn {
        font-size: 25px;
    }

    #tool_area > .btn, #tool_area > .btn-group {
        width: 100%;
        margin-bottom: 12px;
    }

    {{-- 功能区 --}}
    .function_btn .icon,
    .function_btn .content {
        display: inline-block;
        vertical-align: middle;
    }

    .function_btn .content {
        padding-left: 3px;
    }

    .function_btn .content p {
        white-space: normal;
        margin-bottom: 0px;
        color: gray;
    }

    .function_btn .content .content_title {
        font-weight: bold;
        margin-top: 0px;
        margin-bottom: 5px;
    }

    .function_btn {
        white-space: nowrap;
        width: 100%;
        border-radius: 5px;
        min-height: 20px;

        background-color: rgba(255, 255, 255, 0.6);
    }

    .function_btn:hover {
        background-color: rgba(255, 255, 255, 0.75);
    }

</style>
@endpush

@push("js")
<script>
    $(function () {
        $("#back_div").fadeTo(1200, 0.8).delay(450).fadeTo(640, 0.7);
        @if (Auth::guest())
        $("#title_one").delay(450).fadeTo(320, 1);
        $("#title_two").delay(700).fadeTo(320, 1);
        $("#tool_area").delay(1150).fadeTo(400, 1);
        @else
            $("#tool_area").delay(1150).fadeTo(400, 1);
        @endif
    });
</script>
@endpush

@section("content_full")
    <div id="main_div" style="position:relative">
        <div id="back_div"></div>
        <div id="content_div">
            <div style="margin-bottom: 20px; padding: 0;">
                <div class="row" style="margin: 20px;">
                    <div class="col-xs-12" style="text-align:center;">

                        <!--<div id="btn_area">-->

                        @if (Auth::guest())
                            <h1 id="title_one">欢迎使用</h1>
                            <h1 id="title_two">{{ config('app.name', 'Laravel') }}</h1>
                            <div id="tool_area">
                                <div class="col-md-4 col-md-offset-4 col-xs-10 col-xs-offset-1 col-sm-6 col-sm-offset-3">
                                    <a href="{{ url('/login') }}" class="btn btn-info"
                                       style="width: 60%;margin-left: 20%;margin-right: 20%;">开始使用</a>
                                </div>
                            </div>
                        @else
                            <div id="tool_area">
                                <div class="functions col-md-10 col-md-offset-1 col-xs-12 text-center">
                                    @permission(['view_all_user','view_owned_user','modify_all_user', 'view_all_user'])
                                    <div class="function_block">
                                        @permission(['view_all_user','view_owned_user'])
                                        <div class="function_btn clickable slow_down"
                                             href="{{ url("/account_manager") }}">
                                            <div class="content_box">
                                                <div class="icon">
                                                    <span class="glyphicon glyphicon-user"></span>
                                                </div>
                                                <div class="content">
                                                    <h4 class="content_title">用户列表</h4>
                                                    <p>查看用户列表，查看常用分类中的用户等</p>
                                                </div>
                                            </div>
                                        </div>
                                        @endpermission

                                        @permission(['create_user'])
                                        <div class="function_btn clickable slow_down"
                                             href="{{ url("/account_manager/create") }}">
                                            <div class="content_box">
                                                <div class="icon">
                                                    <span class="glyphicon glyphicon-plus"></span>
                                                </div>
                                                <div class="content">
                                                    <h4 class="content_title">添加用户</h4>
                                                    <p>可输入相关信息或导入Excel工作表添加用户</p>
                                                </div>
                                            </div>
                                        </div>
                                        @endpermission
                                    </div>
                                    @endpermission

                                    <div class="function_block">
                                        <div class="function_btn clickable slow_down" href="{{ url("/notification") }}">
                                            <div class="content_box">
                                                <div class="icon">
                                                    <span class="glyphicon glyphicon-list-alt"></span>
                                                </div>
                                                <div class="content">
                                                    <h4 class="content_title">通知列表</h4>
                                                    <p>查看通知列表、必读通知、已收藏通知等</p>
                                                </div>
                                            </div>
                                        </div>

                                        @if(Entrust::can(["create_notification", "delete_notification", "modify_all_notification", "modify_owned_notification"]))
                                            <div class="function_btn clickable slow_down"
                                                 href="{{ url("/notification/manage") }}">
                                                <div class="content_box">
                                                    <div class="icon">
                                                        <span class="glyphicon glyphicon-cog"></span>
                                                    </div>
                                                    <div class="content">
                                                        <h4 class="content_title">通知管理</h4>
                                                        <p>对通知进行管理，可查询通知的阅读人数</p>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif

                                        @permission(['create_notification'])
                                        <div class="function_btn clickable slow_down"
                                             href="{{ url("/notification/create") }}">
                                            <div class="content_box">
                                                <div class="icon">
                                                    <span class="glyphicon glyphicon-pencil"></span>
                                                </div>
                                                <div class="content">
                                                    <h4 class="content_title">通知发布</h4>
                                                    <p>在权限内发布通知，可发布必读通知</p>
                                                </div>
                                            </div>
                                        </div>
                                        @endpermission
                                    </div>

                                </div>
                            </div>
                        @endif

                    </div>


                </div>
            </div>
        </div>

    </div>
@endsection