@extends('layouts.app')

@push("crumb")
<li><a href="{{ url("/") }}">主页</a></li>
<li class="active">通知中心</li>
@endpush

@section('content')
    @if($notifications->count() > 0)
        <table class="table table-condensed table-hover">
            <caption>
                <a type="button" class="btn btn-info" href="{{route('notification').'/stared'}}">
                    发通知
                </a>

                @permission('create_notification')
                <a type="button" class="btn btn-warning" href="{{ route('notification').'/manage' }}">
                    <span class="glyphicon glyphicon-cog"></span>我的通知
                </a>
                @endpermission

                <a type="button" class="btn btn-info" href="{{route('notification').'/stared'}}">
                    草稿箱
                </a>

                <a type="button" class="btn btn-info" href="{{route('notification').'/stared'}}">
                    <span class="glyphicon glyphicon-star"></span>我的收藏
                </a>

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
                @foreach($orders as $key => $value)
                    <th>
                        <a href="{{ route('notification').'?wd='.$wd.'&sort='.$key.'&by='.$value['by'] }}">{{ $value['name'] }}</a>
                    </th>
                @endforeach
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
                    <td>{{ $notification->pivot->read_at? '已读' :'未读' }}{{ $notification->pivot->stared_at? '已收藏' :'' }}</td>

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
    @else
        <h2 style="color:gray;text-align:center;">(没有通知)</h2>
    @endif

    <div class="text-center">{{ $notifications->links() }}</div>
@endsection
