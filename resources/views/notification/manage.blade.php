@extends('layouts.app')

@push('css')
@endpush

@push('js')
<script>
    $(function () {
        $('.destroy').on("click", function () {
            if (confirm("确定删除?")) {
                $.ajax({
                    url: "{{ route('notification').'/'}}" + $(this).data("id"),
                    type: "DELETE",
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    error: function (event) {
                        alert("您没有权限访问！");
                    },
                    success: function (data) {
                        alert(data);
                        window.location.reload();
                    }
                });
            }
        });

        $('.statistic').on("click", function () {
            $.ajax({
                url: "{{ route('notification').'/'}}" + $(this).data("id") + "/statistic",
                type: "POST",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (data) {
                    $('#modalBody').html(data);
                    $('#myModal').modal('show');
                }
            });
        });
    });
</script>
@endpush

@push("crumb")
    <li><a href = "{{ url("/") }}">主页</a></li>
    <li><a href = "{{ url("/notification") }}">通知中心</a></li>
    <li class = "active">通知管理</li>
@endpush

@section('content')
                <div class="panel panel-default">
                    <div class="panel-heading">通知管理</div>

                    <div class="panel-body">
                        <table class="table table-condensed table-hover">
                            <caption>
                                <a type="button" class="btn btn-success" href="{{route('notification').'/create'}}"
                                   style="text-shadow: black 5px 3px 3px;">
                                    <span class="glyphicon glyphicon-plus"></span>
                                </a>

                                <form class="form-inline pull-right" role="form" method="get"
                                      action="{{ route('notification') . '/manage' }}">
                                    <input type="search" class="form-control" name="wd" value="{{$wd}}"
                                           placeholder="题目">

                                    <a class="glyphicon glyphicon-remove"
                                       style="color:red;text-decoration:none;display:inline-block"
                                       href="{{route('notification') . '/manage'}}"></a>

                                    <button type="submit" class="btn btn-primary">
                                        <span class="glyphicon glyphicon-search"></span> 搜索
                                    </button>
                                </form>
                            </caption>
                            <thead>
                            <tr>
                                <th>
                                    <a href="{{route('notification').'/manage?wd='.$wd.'&sort=title&by='.($sort==='title'&&$by==='asc'?'desc':'asc')}}">标题</a>
                                </th>
                                <th>
                                    <a href="{{route('notification').'/manage?wd='.$wd.'&sort=department_id&by='.($sort==='department_id'&&$by==='asc'?'desc':'asc')}}">发布部门</a>
                                </th>
                                <th>
                                    <a href="{{route('notification').'/manage?wd='.$wd.'&sort=content&by='.($sort==='content'&&$by==='asc'?'desc':'asc')}}">正文</a>
                                </th>
                                <th>
                                    <a href="{{route('notification').'/manage?wd='.$wd.'&sort=updated_at&by='.($sort==='updated_at'&&$by==='asc'?'desc':'asc')}}">更新时间</a>
                                </th>
                                <th></th>
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
                                            {{strlen($content = strip_tags($notification->content))>50?substr($content,0,50).'...':$content}}
                                        </a>
                                    </td>
                                    <td>{{\App\Func\Time::format($notification->updated_at)}}</td>
                                    <td>
                                        <div class="btn-group">
                                            <button type="button" class="btn btn-danger btn-xs destroy"
                                                    data-id="{{$notification->id}}">
                                                删除
                                            </button>
                                            <a type="button" class="btn btn-info btn-xs"
                                               href="{{route('notification').'/'.$notification->id.'/edit'}}"
                                               target="_blank">修改</a>
                                            <a type="button" class="btn btn-success btn-xs"
                                               href="{{route('notification').'/'.$notification->id.'/push'}}"
                                               target="_blank">推送</a>
                                            <button type="button" class="btn btn-default btn-xs statistic"
                                                    data-id="{{$notification->id}}">
                                                阅读统计
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                        <div class="text-center">{{ $notifications->links() }}</div>
                    </div>
                </div>

    <div class="modal fade" id="myModal" tabindex="-1" role="dialog"
         aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"
                            aria-hidden="true">&times;
                    </button>
                    <h4 class="modal-title" id="myModalLabel">阅读统计</h4>
                </div>
                <div class="modal-body" id="modalBody">

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default"
                            data-dismiss="modal">关闭
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection
