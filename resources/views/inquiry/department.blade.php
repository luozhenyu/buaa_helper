@extends('layouts.app')

@push("crumb")
<li><a href="{{ url("/") }}">主页</a></li>
<li><a href="{{ url("/inquiry") }}">留言管理</a></li>
<li class="active">XXX学院机关</li>
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

    .bh-inquiry-list .panel-heading .panel-title {
        padding: 1px 0px;
        font-size: 18px;
        font-weight: bold;
    }

    .bh-inquiry-list .panel-title img {
        width: 45px;
        border-radius: 8px;
    }

    .bh-inquiry-set {
        padding: 0px;
        margin: 0px;
    }

    .bh-inquiry-set .bh-inquiry-set-item {
        display: table;
        padding: 15px 10px;
        border-top: 1px solid #d8d8d8;
        min-height: 100px;
        width: 100%;
    }

    .bh-inquiry-set .bh-inquiry-set-item.focus {
        background-color: #fffbec;
    }

    .bh-inquiry-set .bh-inquiry-set-item:hover {
        background-color: #f7f7f7;
    }

    .bh-inquiry-set .bh-inquiry-set-item.focus:hover {
        background-color: #fbf1cb;
    }

    .bh-inquiry-set .bh-inquiry-set-item:first-child {
        border-top: none;
    }

    .bh-inquiry-set-item .bh-inquiry-set-item-title {
        font-size: 20px;
        font-weight: bold;
    }

    .bh-inquiry-set-item a {
        text-decoration: none;
        cursor: pointer;
    }

    .bh-inquiry-set-item a:hover {
        color: #63aae7;
    }

    .bh-inquiry-set-item .bh-inquiry-set-item-content {
        color: #626262
    }

    .bh-inquiry-set-item .bh-inquiry-set-item-info {
        text-align: right;
        font-size: 13px;
        color: #303030
    }
</style>
@endpush

@section('content')
    <div class="panel panel-info bh-inquiry-list">
        <div class="panel-heading">
            <h5 class="panel-title">
                <img src="departmentLogo/6.png" alt="picture">
                留言列表 - XXX机关/学院
            </h5>
        </div>
        <div class="panel-body">
            <ul class="bh-inquiry-set">
                <li class="bh-inquiry-set-item slow-down">
                    <div class="bh-inquiry-set-item-title">
                        <span class="label label-success">
							<span class="glyphicon glyphicon-ok"></span>已解决
						</span>
                        <!--
                        <span class = "label label-danger">
                            <span class = "glyphicon glyphicon-remove"></span>已关闭
                        </span>
                        <span class = "label label-warning">
                            <span class = "glyphicon glyphicon-time"></span>待回答
                        </span>
                        <span class = "label label-info">
                            <span class = "glyphicon glyphicon-comment"></span>待确认解决
                        </span>
                        -->
                        <a href = "/inquiry/1/1">Questionor上不去了</a>
                    </div>
                    <div class="bh-inquiry-set-item-content">
                        Questionor上不去了上不去了上不去了上不去了上不去了上不去了上不去了上不去了上不去了上不去了上不去了上不去了上不去了上不去了上不去了上不去了上不去了上不去了上不去了上不去了上不去了上不去了上不去了上不去了上不去了上不去了上不去了上不去了上不去了上不去了上不去了上不去了上不去了上不去了上不去了上不去了上不去了上不去了上不去了上不去了上不去了上不去了上不去了上不去了上不去了上不去了上不去了上不去了
                    </div>
                    <div class="bh-inquiry-set-item-info">
                        提问者: <a><b>HansBug</b></a>
                        提问时间: <b>1970-01-01</b>
                    </div>
                </li>
                <li class="bh-inquiry-set-item slow-down focus">
                    <div class="bh-inquiry-set-item-title">
                        <span class="label label-warning">
							<span class="glyphicon glyphicon-time"></span>待回答
						</span>
                        <a>Questionor上不去了</a>
                    </div>
                    <div class="bh-inquiry-set-item-content">
                        Questionor上不去了上不去了上不去了上不去了上不去了上不去了上不去了上不去了上不去了上不去了上不去了上不去了上不去了上不去了上不去了上不去了上不去了上不去了上不去了上不去了上不去了上不去了上不去了上不去了上不去了上不去了上不去了上不去了上不去了上不去了上不去了上不去了上不去了上不去了上不去了上不去了上不去了上不去了上不去了上不去了上不去了上不去了上不去了上不去了上不去了上不去了上不去了上不去了
                    </div>
                    <div class="bh-inquiry-set-item-info">
                        提问者: <a><b>HansBug</b></a>
                        提问时间: <b>1970-01-01</b>
                    </div>
                </li>
            </ul>
            <h3 style="text-align: center;">分页预留位置</h3>
        </div>
    </div>
@endsection
