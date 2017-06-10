@extends('layouts.index')

@push("css_2")
<style>

    #title_one, #title_two, #title_three, #tool_area {
        filter: alpha(opacity=1);
        -moz-opacity: 0.01;
        opacity: 0.01;
    }

    @media (min-width: 1200px) {
        #title_one {
            font-size: 80px;
        }

        #title_two {
            font-weight: 800;
            font-size: 60px;
        }

        #title_three {
            font-size: 30px;
            padding: 10px;
            border-radius: 10px;
        }

        #start {
            font-size: 50px;
        }

        #tool_area {
            margin-top: 30px;
        }

        .function_btn {
            margin-bottom: 15px;
            padding: 15px 20px;
            margin-left: 2%;
            margin-right: 2%;
            width: 45%;
        }

        .function_btn .icon {
            font-size: 60px;
        }

        .function_btn .content {
            padding-left: 20px;
        }

        .function_btn .content p {
            font-size: 26px;
        }

        .function_btn .content .content_title {
            font-size: 38px;
        }

        .function_btn .content_box {
            margin-right: 60px;
        }
    }

    @media (min-width: 768px ) and (max-width: 1199px) {
        #title_one {
            font-size: 55px;
        }

        #title_two {
            font-weight: 800;
            font-size: 38px;
        }

        #title_three {
            font-size: 23px;
            padding: 8px;
            border-radius: 8px;
        }

        #start {
            font-size: 35px;
        }

        #tool_area {
            margin-top: 18px;
        }

        .function_btn {
            margin-bottom: 15px;
            padding: 18px 24px;
            margin-left: 2%;
            margin-right: 2%;
            width: 45%;
        }

        .function_btn .icon {
            font-size: 40px;
        }

        .function_btn .content {
            padding-left: 12px;
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
        }

        #title_two {
            font-weight: 600;
            font-size: 28px;
        }

        #title_three {
            font-size: 13px;
            padding: 6px;
            border-radius: 6px;
        }

        #start {
            font-size: 25px;
        }

        #tool_area {
            /*margin-top: 18px;*/
        }

        .function_btn {
            margin-bottom: 5px;
            padding: 8px 8px;
            margin-left: 0%;
            margin-right: 0%;
            width: 100%;
        }

        .function_btn .icon {
            font-size: 18px;
        }

        .function_btn .content {
            padding-left: 4px;
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

    .functions .function_block {
        display: inline-block;
        vertical-align: middle;
        width: 100%;
        text-align: left;
    }

    .function_btn {
        text-align: left;
    }

    #title_one {
        -webkit-text-stroke: 0.8px #e8f5fd;
        color: #975860;
        font-weight: bold;
        margin-top: 0px;
    }

    #title_two {
    }

    #title_three {
        word-wrap: break-word;
        background-color: rgba(255, 255, 255, 0.45);
        color: #828282;
        font-weight: bold;
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
        opacity: 0;
        white-space: nowrap;
        /*width: 100%;*/
        border-radius: 5px;
        min-height: 20px;
        display: inline-block;
        background-color: rgba(255, 255, 255, 0.6);
    }

    .function_btn:hover {
        background-color: rgba(255, 255, 255, 0.82);
    }

</style>
@endpush

@push("js_2")
<script>

    $(function() {
        $("#back_div").fadeTo(1200, 0.8).delay(450).fadeTo(640, 0.7);
        @if (Auth::guest())
            $("#title_one").delay(450).fadeTo(320, 1);
        $("#title_two").delay(700).fadeTo(320, 1);
        $("#title_three").delay(1150).fadeTo(500, 1);
        $("#tool_area").delay(1600).fadeTo(400, 1);

        @else
            $("#tool_area").delay(500).fadeTo(400, 1);
        var main_delay = 150;
        $(".function_block").each(function () {
            var sub_delay = main_delay;
            $(this).find(".function_btn").each(function () {
                $(this).delay(sub_delay).fadeTo(300, 1);
                sub_delay += 150;
            });
            main_delay += 400;
        })
        @endif
    });

</script>
@endpush

@section("content_full_2")

    <td style="margin-bottom: 20px; padding: 0;vertical-align: middle;width: 100%;">
        <div class="row" style="margin: 20px;">
            <div class="col-xs-12" style="text-align:center;">

                <!--<div id="btn_area">-->

                @if (Auth::guest())
                    <h1 id="title_one">欢迎使用</h1>
                    <h1 id="title_two">{{ config('app.name', 'Laravel') }}</h1>
                    <p class="col-sm-10 col-sm-offset-1 col-md-8 col-md-offset-2 col-lg-6 col-lg-offset-3"
                       id="title_three" onclick="end_title_three();">
                        学生事务服务中心APP为同学们打造信息发布平台，在这里，你可以十分方便的查看全校各机关部处、学院、辅导员等发布的通知，同学们以后再也不用为收不到通知而担心啦！</p>
                    <div id="tool_area">
                        <div class="col-md-4 col-md-offset-4 col-xs-10 col-xs-offset-1 col-sm-6 col-sm-offset-3">
                            <a id="start" href="{{ url('/login') }}" class="btn btn-info"
                               style="width: 60%;margin-left: 20%;margin-right: 20%;">开始使用</a>
                        </div>
                    </div>
                @else
                    <div id="tool_area">
                        <div class="functions col-md-10 col-md-offset-1 col-xs-12 text-center">
                            {{-- @permission(['view_all_user','view_owned_user','modify_all_user', 'view_all_user']) --}}
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
                                {{-- </div> --}}
                                {{-- @endpermission --}}

                                {{-- <div class="function_block"> --}}
                                <div class="function_btn clickable slow_down"
                                     href="{{ url("/notification") }}">
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
    </td>

@endsection