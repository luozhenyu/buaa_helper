@extends('layouts.app')

@push('js')
<script>
    $(function () {
        $('.destroy').click(function () {
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

        $('.statistic').click(function () {
            $.ajax({
                url: "{{ route('notification').'/'}}" + $(this).data("id") + "/statistic",
                type: "POST",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (json) {
                    var title = json["title"],
                        link = json["link"],
                        read = json["user_read_cnt"],
                        notRead = json["user_not_read_cnt"],
                        total = read + notRead,
                        users = json["users"];
                    $("#myModal").find("#modal_title").html(title);
                    $("#myModal").find("#modal_total").html("应读人数：" + total);
                    $("#myModal").find("#modal_read").html("已读人数：" + read + ' (' + (total ? (read / total).toFixed(2) : 0) + '%)');
                    $("#myModal").find("#modal_notread").html("未读人数：" + notRead + ' (' + (total ? (notRead / total).toFixed(2) : 0) + '%)');
                    $("#myModal").find("#modal_download").attr("href", link);
                    $("#myModal").find("#modal_users").html(users.join(", "));

                    /*$('#myModal').find(".modal-body").html(
                        '<h3>' + title + '</h3>' +
                        '<p>应读人数：' + total + '</p>' +
                        '<p>已读人数：' + read + '(' + (total ? (read / total).toFixed(2) : 0) + '%)</p>' +
                        '<p>已读人数：' + notRead + '(' + (total ? (notRead / total).toFixed(2) : 0) + '%)</p>' +
                        '<a class="btn btn-primary" href="' + link + '" target="_blank">统计表下载 [Excel]</a>' +
                        '<br><h5>部分未读名单(前50人):</h5>' +
                        '<p>' + users.join(", ") + '...</p>'
                    ).end()
                        .modal("show");*/
                    $("#myModal").modal("show");
                }
            });
        });
    });
</script>
@endpush

@push("crumb")
<li><a href="{{ url("/") }}">主页</a></li>
<li><a href="{{ url("/notification") }}">通知中心</a></li>
<li class="active">通知管理</li>
@endpush

@section('content')
    <table class="table table-condensed table-hover">
        <caption>
            <a type="button" class="btn btn-success" href="{{route('notification').'/create'}}"
               style="text-shadow: black 2px 2px 2px;">
                <span class="glyphicon glyphicon-plus"></span>
                添加新通知
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
                           href="{{route('notification').'/'.$notification->id.'/modify'}}"
                           target="_blank">修改</a>
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
                <div class="modal-body">
                    <h3 id = "modal_title" style = "text-align: center;"></h3>
                    <p id = "modal_total"></p>
                    <p id = "modal_read"></p>
                    <p id = "modal_notread"></p>
                    <a class="btn btn-primary" href="" target="_blank" id = "modal_download">统计表下载 [Excel]</a>
                    <br><h5>部分未读名单(前50人):</h5>
                    <p id = "modal_users"></p>
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
