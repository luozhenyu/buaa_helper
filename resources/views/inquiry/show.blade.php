@extends('layouts.app')

@push("crumb")
<li><a href="{{ url("/") }}">主页</a></li>
<li class="active">留言管理</li>
@endpush

@push("css")
<style>
    a, button, .slow-down {
        -webkit-transition-duration: 0.45s;
        transition-duration: 0.45s;
    }

    .bh-inquiry-title {
        text-align: center;
        margin: 8px 0px 13px 0px;
        font-size: 30px;
        font-weight: bold;
    }

    .bh-inquiry-subtitle {
        text-align: center;
        margin: 4px 0px 8px 0px;
        font-size: 18px;
        color: gray;
    }

    .bh-inquiry-left, .bh-inquiry-right {
        display: inline-block;
        vertical-align: top;
    }

    .bh-inquiry-info .panel-body > * {
        display: inline-block;
    }

    @media (min-width: 992px) {
        .bh-inquiry-left {
            width: 32.5%;
            margin-right: 1.5%;
        }

        .bh-inquiry-right {
            width: 65%;
        }

        .bh-inquiry-info .panel-body > * {
            width: 100%;
        }
    }

    @media (max-width: 991px) {
        .bh-inquiry-left {
            width: 100%;
        }

        .bh-inquiry-right {
            width: 100%;
        }

        .bh-inquiry-info .panel-body > * {
            width: 49%;
        }
    }

    @media (max-width: 581px) {
        .bh-inquiry-info .panel-body > * {
            width: 100%;
        }
    }

    .bh-inquiry-main {
        border-radius: 3px;
    }

    .bh-inquiry-main .panel-heading .panel-title,
    .bh-inquiry-info .panel-heading .panel-title {
        padding: 5px;
        font-size: 18px;
        font-weight: bold;
    }

    .bh-inquiry-info .panel-body {
        padding: 8px 13px;
    }

    .bh-inquiry-main .panel-body {
        padding: 20px 20px;
    }

    .bh-inquiry-list {
        padding: 0px;
    }

    .bh-inquiry-list .bh-inquiry-list-item {
        display: table;
        border-top: 1px dashed gray;
        min-height: 100px;
        width: 100%;
        padding-top: 20px;
        padding-bottom: 25px;
    }

    .bh-inquiry-list .bh-inquiry-list-item:first-child {
        border-top: none;
        padding-top: 0px;
    }

    .bh-inquiry-list .bh-inquiry-list-item:last-child {
        padding-bottom: 5px;
    }

    .bh-inquiry-head-icon {
        display: table-cell;
        vertical-align: top;
        width: 50px;
    }

    .bh-inquiry-head-icon img {
        width: 50px;
    }

    .bh-inquiry-detail-recode {
        padding-top: 5px;
        padding-left: 10px;
    }

    .bh-inquiry-list-item-black .bh-inquiry-detail-recode-identity,
    .bh-inquiry-list-item-black .bh-inquiry-detail-recode-time,
    .bh-inquiry-list-item-black .bh-inquiry-detail-recode-content,
    .bh-inquiry-list-item-black .bh-inquiry-detail-recode-secret {
        color: black;
    }

    .bh-inquiry-list-item-black .bh-inquiry-detail-recode-content,
    .bh-inquiry-list-item-black .bh-inquiry-detail-recode-secret {
        background-color: #f8f8f8;
    }

    .bh-inquiry-list-item-gray .bh-inquiry-detail-recode-identity,
    .bh-inquiry-list-item-gray .bh-inquiry-detail-recode-time,
    .bh-inquiry-list-item-gray .bh-inquiry-detail-recode-content,
    .bh-inquiry-list-item-gray .bh-inquiry-detail-recode-secret {
        color: #858585;
    }

    .bh-inquiry-list-item-gray .bh-inquiry-detail-recode-content,
    .bh-inquiry-list-item-gray .bh-inquiry-detail-recode-secret {
        background-color: #f6f6f6;
    }

    .bh-inquiry-detail-recode-title {
        clear: both;
        display: table;
        width: 100%;
    }

    .bh-inquiry-detail-recode-identity {
        font-weight: bold;
        font-size: 16px;
    }

    .bh-inquiry-detail-recode-time {
        font-size: 15px;
    }

    .bh-inquiry-detail-recode-content,
    .bh-inquiry-detail-recode-secret {
        word-wrap: break-word;
        white-space: pre-wrap;

        border-radius: 5px;
        border: none;
        padding: 7px 12px;
        margin-top: 3px;
        margin-bottom: 0px;

        font-family: inherit;
        font-size: 16px;
        line-height: 24px;
    }
</style>
@endpush

@push("js")
<script>
    $(function(){
        // 左侧信息栏自动滚动（窄屏时无效）
        var scroll_info_check = function(){
            var info_margin = 0; // 窄屏幕下默认margin-top: 0px;
            if (!($(".bh-inquiry-left").css("width") === $(".bh-inquiry-right").css("width"))) {
                // 识别为宽屏幕，根据滚动状况进行自动调整
                var main_top = $(".bh-inquiry-main").offset().top - $(window).scrollTop();
                info_margin = 10 - main_top;
                if (info_margin < 0) info_margin = 0;
            }
            $(".bh-inquiry-info").css("margin-top", info_margin + "px");
        };
        scroll_info_check();
        $(window).scroll(scroll_info_check).resize(scroll_info_check);
    });
</script>
@endpush

@section('content')
    <h3 class="bh-inquiry-title">（示例标题）Questionor上不去了</h3>
    <div class="bh-inquiry-left">
        <div class="panel panel-info bh-inquiry-info">
            <div class="panel-heading">
                <h3 class="panel-title">
                    问题信息
                </h3>
            </div>
            <div class="panel-body">
                <h5>
                    提问者：
                    <b>HansBug</b>
                </h5>
                <h5>
                    提问部门：
                    <b>Questionor</b>
                </h5>
                <h5>
                    提问时间：
                    <b>2017-06-19 10:30:01</b>
                </h5>
                <h5>
                    状态：
                    <span class="label label-danger" style="font-size: 14px">待回答</span>
                    <!--<span class = "label label-warning" style = "font-size: 14px">待确认解决</span>-->
                    <!--<span class = "label label-success" style = "font-size: 14px">已解决</span>-->
                </h5>

            </div>
        </div>
    </div>
    <div class="bh-inquiry-right">
        <div class="panel panel-info bh-inquiry-main">
            <div class="panel-heading">
                <h3 class="panel-title">
                    沟通记录
                </h3>
            </div>
            <div class="panel-body">
                <ul class="bh-inquiry-list">

                    <!-- 问题描述demo -->
                    <li class="bh-inquiry-list-item bh-inquiry-list-item-gray">
                        <div class="bh-inquiry-head-icon">
                            <img src="https://cdn.v2ex.com/gravatar/554cee7335fc3490a6c93fe128d1519d?s=60"
                                 alt="UserName" class="img-circle">
                        </div>
                        <div class="bh-inquiry-detail-recode">
                            <div class="bh-inquiry-detail-recode-title">
                                <div class="pull-left bh-inquiry-detail-recode-identity">
                                    问题描述
                                </div>
                                <div class="pull-right bh-inquiry-detail-recode-time">
                                    2017-06-19 00:30:19
                                </div>
                            </div>
                            <pre class="bh-inquiry-detail-recode-content">Questionor.cn上不去了</pre>
                            <pre class="bh-inquiry-detail-recode-secret"><b>机密信息：</b></br>******</pre>
                        </div>
                    </li>


                    <li class="bh-inquiry-list-item bh-inquiry-list-item-black">
                        <div class="bh-inquiry-head-icon">
                            <img src="departmentLogo/109.png" alt="UserName" class="img-circle">
                        </div>
                        <div class="bh-inquiry-detail-recode">
                            <div class="bh-inquiry-detail-recode-title">
                                <div class="pull-left bh-inquiry-detail-recode-identity">
                                    管理员回复
                                </div>
                                <div class="pull-right bh-inquiry-detail-recode-time">
                                    2017-06-19 00:30:19
                                </div>
                            </div>
                            <pre class="bh-inquiry-detail-recode-content">已经让站长HansBug同学去修复了，感谢您的反馈</pre>
                        </div>
                    </li>

                    <li class="bh-inquiry-list-item bh-inquiry-list-item-black">
                        <div class="bh-inquiry-head-icon">
                            <img src="departmentLogo/109.png" alt="UserName" class="img-circle">
                        </div>
                        <div class="bh-inquiry-detail-recode">
                            <div class="bh-inquiry-detail-recode-title">
                                <div class="pull-left bh-inquiry-detail-recode-identity">
                                    管理员回复
                                </div>
                                <div class="pull-right bh-inquiry-detail-recode-time">
                                    2017-06-19 00:30:19
                                </div>
                            </div>
                            <pre class="bh-inquiry-detail-recode-content">已经让站长HansBug同学去修复了，感谢您的反馈</pre>
                        </div>
                    </li>

                    <li class="bh-inquiry-list-item bh-inquiry-list-item-black">
                        <div class="bh-inquiry-head-icon">
                            <img src="departmentLogo/109.png" alt="UserName" class="img-circle">
                        </div>
                        <div class="bh-inquiry-detail-recode">
                            <div class="bh-inquiry-detail-recode-title">
                                <div class="pull-left bh-inquiry-detail-recode-identity">
                                    管理员回复
                                </div>
                                <div class="pull-right bh-inquiry-detail-recode-time">
                                    2017-06-19 00:30:19
                                </div>
                            </div>
                            <pre class="bh-inquiry-detail-recode-content">已经让站长HansBug同学去修复了，感谢您的反馈</pre>
                        </div>
                    </li>

                    <li class="bh-inquiry-list-item bh-inquiry-list-item-black">
                        <div class="bh-inquiry-head-icon">
                            <img src="departmentLogo/109.png" alt="UserName" class="img-circle">
                        </div>
                        <div class="bh-inquiry-detail-recode">
                            <div class="bh-inquiry-detail-recode-title">
                                <div class="pull-left bh-inquiry-detail-recode-identity">
                                    管理员回复
                                </div>
                                <div class="pull-right bh-inquiry-detail-recode-time">
                                    2017-06-19 00:30:19
                                </div>
                            </div>
                            <pre class="bh-inquiry-detail-recode-content">已经让站长HansBug同学去修复了，感谢您的反馈</pre>
                        </div>
                    </li>

                    <li class="bh-inquiry-list-item bh-inquiry-list-item-gray">
                        <div class="bh-inquiry-head-icon">
                            <img src="https://cdn.v2ex.com/gravatar/554cee7335fc3490a6c93fe128d1519d?s=60"
                                 alt="UserName" class="img-circle">
                        </div>
                        <div class="bh-inquiry-detail-recode">
                            <div class="bh-inquiry-detail-recode-title">
                                <div class="pull-left bh-inquiry-detail-recode-identity">
                                    追问
                                </div>
                                <div class="pull-right bh-inquiry-detail-recode-time">
                                    2017-06-19 00:30:19
                                </div>
                            </div>
                            <pre class="bh-inquiry-detail-recode-content">不行啊，Questionor.cn还是上不去，究竟怎么回事，求尽快解决，我们还要复习航概，谢谢</pre>
                        </div>
                    </li>


                    <li class="bh-inquiry-list-item bh-inquiry-list-item-black">
                        <div class="bh-inquiry-head-icon">
                            <img src="departmentLogo/109.png" alt="UserName" class="img-circle">
                        </div>
                        <div class="bh-inquiry-detail-recode">
                            <div class="bh-inquiry-detail-recode-title">
                                <div class="pull-left bh-inquiry-detail-recode-identity">
                                    追答
                                </div>
                                <div class="pull-right bh-inquiry-detail-recode-time">
                                    2017-06-19 00:30:19
                                </div>
                            </div>
                            <pre class="bh-inquiry-detail-recode-content">已经让站长HansBug同学去修复了，感谢您的反馈</pre>
                        </div>
                    </li>


                </ul>
            </div>
        </div>
    </div>
@endsection
