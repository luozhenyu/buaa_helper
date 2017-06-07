@extends('layouts.app_full')

@push("css")
<style>
    /* 背景图 */
    @media (min-width: 1200px) {
        #back_div {
            background: url({{ url('/img/bk2.jpg') }}) -80px -280px;
            background-size: 2450px;
        }

        #main_div, #content_div {
            min-height: 580px;
        }
    }

    @media (min-width: 992px) and (max-width: 1199px) {
        #back_div {
            background: url({{ url('/img/bk2.jpg') }}) -80px -280px;
            background-size: 1750px;
        }

        #main_div, #content_div {
            min-height: 550px;
        }
    }

    @media (max-width: 991px) and (min-width: 768px) {
        #back_div {
            background: url({{ url('/img/bk2.jpg') }}) -600px -200px;
            background-size: 1600px;
        }

        #main_div, #content_div {
            min-height: 470px;
        }
    }

    @media (max-width: 767px) {
        #back_div {
            background: url("{{ url('/img/bk2.jpg') }}") -800px -200px;
            background-size: 1600px;
        }

        #main_div, #content_div {
            min-height: 430px;
        }
    }

    @media (max-width: 767px) {
        #back_div {
            background: url("{{ url('/img/bk2.jpg') }}") -800px -200px;
            background-size: 1600px;
        }

        #main_div, #content_div {
            min-height: 380px;
        }
    }

    @media (max-width: 365px) {
        #main_div, #content_div {
            min-height: 440px;
        }
    }

    #back_div, #title_one, #title_two, #title_three, #tool_area {
        filter: alpha(opacity=1);
        -moz-opacity: 0.01;
        opacity: 0.01;
    }

    #main_div {
        margin-bottom: 15px;
        height: 80%;
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

    #content_div {
        width: 100%;
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

@push("js")
<script>
    var text = "学生事务服务中心APP为同学们打造全校信息发布平台，在这里，你可以十分方便的查看全校各机关部处、学院、辅导员等发布的通知，同学们以后再也不用为收不到通知而担心啦！";
    var present_text = "";
    var loc = 0, end = 0;
    var points = [",", " ", ".", "，", "。", "!", "！", "\n"];
    function nx(span, hook) {

        var str_append, need_stop = text[loc];
        if (text[loc] == '\n') str_append = "<br>"; else str_append = text[loc];
        present_text += str_append;

        loc += 1;
        if (end == 1) {
            $("#title_three").html(text.replace("\n", "<br>"));
            $("#title_three").removeClass("click");
            eval(hook);
        } else {
            if (loc < text.length) {
                setTimeout("nx(" + span + ",\"" + hook + "\");", span);
                $("#title_three").html(present_text + "_");
            } else {
                $("#title_three").html(present_text);
                $("#title_three").removeClass("click");
                eval(hook);
            }
        }
    }
    function end_title_three() {
        end = 1;
    }
    function start_title_three() {
        loc = 0;
        present_text = "";
        end = 0;
        $("#title_three").addClass("click");
        nx(100, "$('#tool_area').delay(150).fadeTo(400, 1);");
    }

    function no_px(st) {
        return parseInt(st.substr(0, st.length - 2));
    }

    $(function () {
        $("#main_div").css("height", ($(document).height() - 170) + "px");
        $("#back_div").fadeTo(1200, 0.8).delay(450).fadeTo(640, 0.7);
        @if (Auth::guest())
            $("#title_one").delay(450).fadeTo(320, 1);
        $("#title_two").delay(700).fadeTo(320, 1);
        $("#title_three").delay(1150).fadeTo(500, 1);

        setTimeout("start_title_three();", 2000);
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

        $(window).resize(function () {

            var main_bottom = $(window).height() - no_px($("footer").css("height")) - no_px($("#main_div").css("margin-bottom"));
            var main_top = $("#main_div").offset().top;
            var main_height = main_bottom - main_top;
            console.log(main_height);
            $("#main_div").css("height", (main_height) + "px");
        })
    });

</script>
@endpush

@section("content_full")
    <div id="main_div" style="position:relative">
        <div id="back_div"></div>
        <table id="content_div">
            <tr>
                <td style="margin-bottom: 20px; padding: 0;vertical-align: middle;width: 100%;">
                    <div class="row" style="margin: 20px;">
                        <div class="col-xs-12" style="text-align:center;">

                            <!--<div id="btn_area">-->

                            @if (Auth::guest())
                                <h1 id="title_one">欢迎使用</h1>
                                <h1 id="title_two">{{ config('app.name', 'Laravel') }}</h1>
                                <p class="col-sm-10 col-sm-offset-1 col-md-8 col-md-offset-2 col-lg-6 col-lg-offset-3"
                                   id="title_three" onclick="end_title_three();"><br></p>
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
            </tr>
        </table>

    </div>
@endsection