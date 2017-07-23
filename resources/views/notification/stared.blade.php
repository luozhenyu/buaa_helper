@extends('layouts.app')

@push('js')
    <script>
        $(function () {
            $('.destroy').click(function () {
                $.ajax({
                    url: "{{ route('notification').'/'}}" + $(this).data("id") + "/unstar",
                    type: "POST",
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (data) {
                        location.reload();
                    }
                });
            });
        });
    </script>
@endpush

@push("crumb")
    <li><a href="{{ url("/") }}">主页</a></li>
    <li><a href="{{ route("notification") }}">通知中心</a></li>
    <li class="active">收藏</li>
@endpush

@section('content')
    <table class="table table-condensed table-hover">
        <caption>
            <a type="button" class="btn btn-default" href="{{ route('notification') }}">返回</a>
        </caption>
        <thead>
        <tr>
            <th>部门</th>
            <th>类别</th>
            <th>标题</th>
            <th>发布时间</th>
            <th>收藏时间</th>
            <th></th>
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
                <td>
                    <a href="{{route('notification').'/'.$notification->id}}" target="_blank">
                        {{ $notification->title }}
                    </a>
                </td>
                <td>{{ \App\Func\Time::format($notification->updated_at) }}</td>
                <td>{{ (new Carbon\Carbon($notification->pivot->stared_at))->diffForHumans() }}</td>
                <td>
                    <div class="btn-group">
                        <button type="button" class="btn btn-danger btn-xs destroy"
                                data-id="{{ $notification->id }}">
                            <span class="glyphicon glyphicon-star-empty">取消收藏</span>
                        </button>
                    </div>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
    @if($notifications->count() === 0)
        <h2 style="color:gray;text-align:center;">(没有通知)</h2>
    @endif
    <div class="text-center">{{ $notifications->links() }}</div>
@endsection
