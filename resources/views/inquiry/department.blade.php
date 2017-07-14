@extends('layouts.app')

@php($authUser = Auth::user())
@php
    const 旁观者 = 0;
    const 提问者 = 1;
    const 回答者 = 2;
    function inquiryRole($user, $inquiry) {
        if ($user->id === $inquiry->user->id) {
            return 提问者;
        } else if ($user->hasPermission('view_all_inquiry')
            || ($user->hasPermission('view_owned_inquiry') && $user->department->id === $inquiry->department->id)
        ) {
            return 回答者;
        } else {
        return 旁观者;
        }
    }
@endphp

@push("crumb")
<li><a href="{{ url("/") }}">主页</a></li>
<li><a href="{{ url("/inquiry") }}">留言管理</a></li>
<li class="active">{{ $department->name }}</li>
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

    .bh-inquiry-list .panel-heading .panel-title {
        padding: 1px 0;
        font-size: 18px;
        font-weight: bold;
    }

    .bh-inquiry-list .panel-title img {
        width: 45px;
        border-radius: 8px;
    }

    .bh-inquiry-set {
        padding: 0;
        margin: 0;
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
        background-color: #F4F4F4;
    }

    .bh-inquiry-set .bh-inquiry-set-item.focus:hover {
        background-color: #FFF5D3;
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
                <img src="{{ $department->avatar->url }}" alt="{{ $department->name }}">
                留言列表 - {{ $department->name }}
            </h5>
        </div>
        <div class="panel-body">
            <ul class="bh-inquiry-set">
                @php($count = 0)
                @foreach($inquiries as $inquiry)
                    @php($count++)
                    <li class="bh-inquiry-set-item slow-down
                        {{ (((inquiryRole($authUser, $inquiry) === 回答者) && (!$inquiry->replied))
                            || (inquiryRole($authUser, $inquiry) === 提问者)) ? "focus" : "" }}">
                        <div class="bh-inquiry-set-item-title">
                            @if($inquiry->replied)
                                <span class="label label-success" style="font-size: 14px">已回答</span>
                            @else
                                <span class="label label-warning" style="font-size: 14px">待回答</span>
                            @endif
                            <a href="{{ url("/inquiry/{$department->number}/{$inquiry->id}") }}">{{ $inquiry->title }}</a>
                        </div>
                        <div class="bh-inquiry-set-item-content">
                            {{ $inquiry->content }}
                        </div>
                        <div class="bh-inquiry-set-item-info">
                            提问者: <a><b>{{ $inquiry->user->name }}</b></a>
                            提问时间: <b>{{ $inquiry->created_at }}</b>
                        </div>
                    </li>
                @endforeach
                @if($count === 0)
                    <h3 style = "color: gray;text-align: center;">(暂无留言)</h3>
                @endif
            </ul>

            {{ $inquiries->links() }}
        </div>
    </div>

    <form class="form-horizontal" role="form" method="POST"
          action="{{ route('inquiry')."/{$department->number}" }}">
        {{ csrf_field() }}
        <h3 class="text-center">我要提问</h3>

        <div class="form-group{{ $errors->has('title') ? ' has-error' : '' }}">
            <label for="title" class="col-md-2 control-label">标题</label>

            <div class="col-md-9">
                <input id="title" type="text" class="form-control" name="title"
                       value="{{ old('title') }}" required autocomplete="off" placeholder="一句话简要概括您遇到的问题">

                @if ($errors->has('title'))
                    <span class="help-block">
                        <strong>{{ $errors->first('title') }}</strong>
                    </span>
                @endif
            </div>
        </div>

        <div class="form-group{{ $errors->has('content')?' has-error' :'' }}">
            <label for="content" class="col-md-2 control-label">问题描述</label>

            <div class="col-md-9">
                <textarea id="content" class="form-control" name="content" rows="10" required
                          autocomplete="off" placeholder="请详细描述您遇到的问题">{{ old('content') }}</textarea>
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
                          placeholder="该信息将作加密处理，仅回复者可见，选填">{{ old('secret') }}</textarea>
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
                    发布提问
                </button>
            </div>
        </div>
    </form>
@endsection
