@extends('layouts.app')

@push("crumb")
<li><a href="{{ url("/") }}">主页</a></li>
<li><a href="{{ url("/inquiry") }}">留言中心</a></li>
<li><a href="{{ url("/inquiry/".$department->number) }}">{{ $department->name }}</a></li>
<li class="active">查看问题 - {{ $inquiry->title }}</li>
@endpush

@push("css")
<style>
    a, button, .slow-down {
        -webkit-transition-duration: 0.45s;
        transition-duration: 0.45s;
    }

    .bh-inquiry-title {
        text-align: center;
        margin: 8px 0 13px 0;
        font-size: 30px;
        font-weight: bold;
    }

    .bh-inquiry-subtitle {
        text-align: center;
        margin: 4px 0 8px 0;
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
        padding: 0;
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
        padding-top: 0;
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

    @media (max-width: 596px) {
        .bh-inquiry-head-icon {
            width: 30px;
        }

        .bh-inquiry-head-icon img {
            width: 30px;
        }
    }

    .bh-inquiry-detail-recode {
        padding-top: 5px;
        padding-left: 10px;
    }

    .bh-inquiry-list-item-black .bh-inquiry-detail-recode-identity,
    .bh-inquiry-list-item-black .bh-inquiry-detail-recode-time,
    .bh-inquiry-list-item-black .bh-inquiry-detail-recode-content,
    .bh-inquiry-list-item-black .bh-inquiry-detail-recode-secret-full {
        color: black;
    }

    .bh-inquiry-list-item-black .bh-inquiry-detail-recode-content,
    .bh-inquiry-list-item-black .bh-inquiry-detail-recode-secret-full {
        background-color: #f8f8f8;
    }

    .bh-inquiry-list-item-gray .bh-inquiry-detail-recode-identity,
    .bh-inquiry-list-item-gray .bh-inquiry-detail-recode-time,
    .bh-inquiry-list-item-gray .bh-inquiry-detail-recode-content,
    .bh-inquiry-list-item-gray .bh-inquiry-detail-recode-secret-full {
        color: #858585;
    }

    .bh-inquiry-list-item-gray .bh-inquiry-detail-recode-content,
    .bh-inquiry-list-item-gray .bh-inquiry-detail-recode-secret-full {
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
    .bh-inquiry-detail-recode-secret-content {
        word-wrap: break-word;
        white-space: pre-wrap;
    }

    .bh-inquiry-detail-recode-content,
    .bh-inquiry-detail-recode-secret-full {
        border-radius: 5px;
        border: none;
        padding: 7px 12px;
        margin-top: 3px;
        margin-bottom: 0;

        font-family: inherit;
        font-size: 16px;
        line-height: 24px;
    }
</style>
@endpush

@push("js")
<script>
    $(function () {
        // 左侧信息栏自动滚动（窄屏时无效）
        var scroll_info_check = function () {
            var info_margin = 0; // 窄屏幕下默认margin-top: 0px;
            if (!($(".bh-inquiry-left").css("width") === $(".bh-inquiry-right").css("width"))) {
                // 识别为宽屏幕，根据滚动状况进行自动调整
                var main_top = $(".bh-inquiry-main").offset().top - $(window).scrollTop();
                var max_top = $(".bh-inquiry-main").outerHeight() - $(".bh-inquiry-info").outerHeight();
                info_margin = 10 - main_top;
                if (info_margin > max_top) info_margin = max_top;
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
    <h3 class="bh-inquiry-title">{{ $inquiry->title }}</h3>
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
                    <b>{{ $inquiry->user->name }}</b>
                </h5>
                <h5>
                    提问部门：
                    <b>{{ $inquiry->department->name }}</b>
                </h5>
                <h5>
                    提问时间：
                    <b>{{ $inquiry->created_at }}</b>
                </h5>
                <h5>状态：
                    @if($inquiry->replied)
                        <span class="label label-success" style="font-size: 14px">已回复</span>
                    @else
                        <span class="label label-warning" style="font-size: 14px">待回复</span>
                    @endif
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
                            <img src="{{ \App\Models\User::downcasting($inquiry->user)->avatarUrl }}"
                                 alt="{{ $inquiry->user->name }}" class="img-circle">
                        </div>
                        <div class="bh-inquiry-detail-recode">
                            <div class="bh-inquiry-detail-recode-title">
                                <div class="pull-left bh-inquiry-detail-recode-identity">
                                    问题描述
                                </div>
                                <div class="pull-right bh-inquiry-detail-recode-time">
                                    {{ $inquiry->created_at }}
                                </div>
                            </div>
                            <div class="bh-inquiry-detail-recode-content">{{ $inquiry->content }}</div>

                            @if($inquiry->hasSecret())
                                <div class="bh-inquiry-detail-recode-secret-full">
                                    <b>机密信息：</b>
                                    <div class="bh-inquiry-detail-recode-secret">{{ $inquiry->display_secret }}</div>
                                </div>
                            @endif
                        </div>
                    </li>

                    @foreach($inquiryReplies as $inquiryReply)
                        <li class="bh-inquiry-list-item
                            {{ $inquiryReply->回复? "bh-inquiry-list-item-black" : "bh-inquiry-list-item-gray" }}">
                            <div class="bh-inquiry-head-icon">
                                <img src="{{ \App\Models\User::downcasting($inquiryReply->user)->avatarUrl }}"
                                     alt="{{ $inquiryReply->user->name }}" class="img-circle">
                            </div>
                            <div class="bh-inquiry-detail-recode">
                                <div class="bh-inquiry-detail-recode-title">
                                    <div class="pull-left bh-inquiry-detail-recode-identity">
                                        {{ $inquiryReply->user->name }}{{ $inquiryReply->reply_type_name }}
                                    </div>
                                    <div class="pull-right bh-inquiry-detail-recode-time">
                                        {{ $inquiryReply->created_at }}
                                    </div>
                                </div>
                                <div class="bh-inquiry-detail-recode-content">{{ $inquiryReply->content }}</div>
                                @if($inquiryReply->hasSecret())
                                    <div class="bh-inquiry-detail-recode-secret-full">
                                        <b>机密信息：</b>
                                        <div class="bh-inquiry-detail-recode-secret">{{ $inquiryReply->display_secret }}</div>
                                    </div>
                                @endif
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>

    @if($inquiry->CanDiplaySecret())
        <form class="form-horizontal" role="form" method="POST"
              action="{{ route('inquiry')."/{$department->number}/{$inquiry->id}" }}">
            {{ csrf_field() }}
            <h3 class="text-center">我要{{ $inquiry->回答者? "回复" :"追问" }}</h3>

            <div class="form-group{{ $errors->has('content')?' has-error' :'' }}">
                <label for="content" class="col-md-2 control-label">内容</label>

                <div class="col-md-9">
                <textarea id="content" class="form-control" name="content" rows="10" required
                          autocomplete="off">{{ old('content') }}</textarea>
                    @if($errors->has('content'))
                        <span class="help-block">
                        <strong>{{ $errors->first('content') }}</strong>
                    </span>
                    @endif
                </div>
            </div>

            <div class="form-group{{ $errors->has('secret')?' has-error' :'' }}">
                <label for="secret" class="col-md-2 control-label">机密信息(选填)</label>

                <div class="col-md-9">
                <textarea id="secret" class="form-control" name="secret" rows="5" autocomplete="off"
                          placeholder="该信息将作加密处理，仅您自己和回复者可见，选填">{{ old('secret') }}</textarea>
                    @if($errors->has('secret'))
                        <span class="help-block">
                            <strong>{{ $errors->first('secret') }}</strong>
                        </span>
                    @endif
                </div>
            </div>

            <div class="form-group">
                <div class="col-md-3 pull-right">
                    <button type="submit" class="btn btn-primary">
                        提交{{ $inquiry->回答者? "回复" :"追问" }}
                    </button>
                </div>
            </div>
        </form>
    @endif
@endsection
