@extends('layouts.app')

@push("crumb")
<li><a href="{{ url("/") }}">主页</a></li>
<li><a href="{{ route('notification') }}">通知中心</a></li>
<li class="active">我的通知</li>
@endpush

@section('content')

    <table class="table table-condensed table-hover">
        <caption>
            <div class="btn-group">
                @permission('create_notification')
                <a type="button" class="btn btn-info" href="{{ route('notification') . '/create' }}">
                    <span class="glyphicon glyphicon-pencil"></span> 新通知
                </a>
                <a type="button" class="btn btn-primary" href="{{ route('notification') . '/draft' }}">
                    <span class="glyphicon glyphicon-edit"></span> 草稿箱
                </a>
                <a type="button" class="btn btn-primary" href="{{ route('notification') . '/published' }}">
                    <span class="glyphicon glyphicon-send"></span> 已发布
                </a>
                @endpermission
            </div>
            <div class="btn-group">
                <a type="button" class="btn btn-default" href="{{ route('notification') }}">
                    <span class="glyphicon glyphicon-envelope"></span> 我的通知
                </a>
                <a type="button" class="btn btn-default" href="{{ route('notification') . '/stared' }}">
                    <span class="glyphicon glyphicon-star"></span> 收藏
                </a>
            </div>

            <form class="form-inline pull-right" role="form" method="get" action="{{ route('notification') }}">
                <div class="input-group">
                    <input type="search" class="form-control" name="wd" value="{{ $wd }}" placeholder="请输入查询关键词">
                    <span class="input-group-btn">
                        <button type="submit" class="btn btn-primary">
                            <span class="glyphicon glyphicon-search"></span>&nbsp;搜索
                        </button>
                    </span>
                </div>
            </form>
        </caption>
        <thead>
        <tr>
            <th>部门</th>
            <th>类别</th>
            <th>阅读情况</th>
            <th>标题</th>
            <th>发布时间</th>
        </tr>
        </thead>
        <tbody>
        @foreach($notifications as $notification)
            <tr>
                <td>
                    <img width="40px" src="{{ $notification->department->avatar->url }}">
                    {{ $notification->department->name }}
                </td>
                <td>{{ $notification->important? '必读' :'普通' }}</td>
                <td>{{ $notification->pivot->read_at? '已读' :'未读' }} {{ $notification->pivot->stared_at? '已收藏' :'' }}</td>

                <td>
                    <a href="{{ route('notification').'/'.$notification->id }}" target="_blank">
                        {{ $notification->title }}
                    </a>
                </td>
                <td>{{ \App\Func\Time::format($notification->updated_at) }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>

    @if($notifications->count() === 0)
        <h2 style="color:gray;text-align:center;">(没有通知)</h2>
    @endif

    <div class="text-center">{{ $notifications->links() }}</div>
@endsection
