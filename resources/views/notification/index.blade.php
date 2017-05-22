@extends('layouts.app')

@push("crumb")
<li><a href="{{ url("/") }}">主页</a></li>
<li class="active">通知中心</li>
@endpush

@section('content')
    <table class="table table-condensed table-hover">
        <caption>
            <a type="button" class="btn btn-info" href="{{route('notification').'/stared'}}">
                <span class="glyphicon glyphicon-star"></span>星标通知
            </a>

            @permission('create_notification')
            <a type="button" class="btn btn-warning" href="{{ route('notification').'/manage' }}">
                通知管理
            </a>
            @endpermission

            <form class="form-inline pull-right" role="form" method="get"
                  action="{{ route('notification') }}">
                <div class="input-group">
                    <span class="input-group-btn">
                        <button class="btn btn-default"
                                onclick="window.location.href='{{ url('/notification/1')}};'">
                            <span class="glyphicon glyphicon-remove"></span>
                        </button>
                    </span>

                    <input type="search" class="form-control" name="wd" value="{{ $wd }}" placeholder="题目">

                    <span class="input-group-btn">
                        <button type="submit" class="btn btn-primary">
                            <span class="glyphicon glyphicon-search"></span> 搜索
                        </button>
                    </span>
                </div>

            </form>
        </caption>
        <thead>
        <tr>
            <th>
                <a href="{{route('notification').'?wd='.$wd.'&sort=title&by='.($sort==='title'&&$by==='asc'?'desc':'asc')}}">标题</a>
            </th>
            <th>
                <a href="{{route('notification').'?wd='.$wd.'&sort=department_id&by='.($sort==='department_id'&&$by==='asc'?'desc':'asc')}}">发布部门</a>
            </th>
            <th>
                <a href="{{route('notification').'?wd='.$wd.'&sort=content&by='.($sort==='content'&&$by==='asc'?'desc':'asc')}}">正文</a>
            </th>
            <th>
                <a href="{{route('notification').'?wd='.$wd.'&sort=updated_at&by='.($sort==='updated_at'&&$by==='asc'?'desc':'asc')}}">更新时间</a>
            </th>
        </tr>
        </thead>
        <tbody>

        @foreach($notifications as $notification)
            <tr>
                <td>
                    @if($notification->important)
                        <span class="label label-danger">必读</span>
                    @endif
                    <a href="{{route('notification').'/'.$notification->id}}" target="_blank">
                        {{$notification->title}}
                    </a>
                </td>
                <td>{{$notification->department->name}}</td>
                <td>
                    <a href="{{route('notification').'/'.$notification->id}}" target="_blank">
                        {{ str_limit(strip_tags($notification->content),50) }}
                    </a>
                </td>
                <td>{{\App\Func\Time::format($notification->updated_at)}}</td>
            </tr>
        @endforeach
        </tbody>
    </table>

    @if($notifications->count() === 0)
        <h2 style="color:gray;text-align:center;">(没有通知)</h2>
    @endif

    <div class="text-center">{{ $notifications->links() }}</div>
@endsection
