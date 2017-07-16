@extends('layouts.app')

@push('js')
<script>
    $(function () {
        $(".destroy").click(function () {
            if (confirm("该通知已发布，确认删除？")) {
                $.ajax({
                    url: "{{ route('notification') }}/" + $(this).data("id"),
                    type: "DELETE",
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (data) {
                        location.reload();
                    }
                });
            }
        });
    });
</script>
@endpush

@push("crumb")
<li><a href="{{ url("/") }}">主页</a></li>
<li><a href="{{ route('notification') }}">通知中心</a></li>
<li class="active">已发布</li>
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
                <td>
                    @permission('delete_notification')
                    <button type="button" class="btn btn-danger btn-xs destroy"
                            data-id="{{ $notification->id }}">
                        <span class="glyphicon glyphicon-remove">删除</span>
                    </button>
                    @endpermission
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
