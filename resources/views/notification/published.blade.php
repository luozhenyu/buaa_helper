@extends('layouts.app')

@push('jsLink')
<script src="{{ url('/components/highcharts/highcharts.js') }}"></script>
@endpush

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

        $('.statistic').click(function () {
            $.ajax({
                url: "{{ route('notification').'/'}}" + $(this).data("id") + "/statistic",
                type: "POST",
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content")
                },
                success: function (json) {
                    var title = json.title,
                        link = json.link,
                        read = json.user_read_cnt,
                        notRead = json.user_not_read_cnt,
                        total = read + notRead,
                        users = json.users;

                    var chart = $("<div>");

                    var html = $("#statisticModalBody").empty();
                    html.append(
                        $("<h3>").addClass("text-center").css("font-weight", "bold").text(title)
                    ).append(
                        $("<h4>").addClass("text-center").css("font-color", "gray").text("阅读情况")
                    ).append(chart).append(
                        $("<div>").addClass("panel panel-info").append(
                            $("<div>").addClass("panel panel-heading").append(
                                $("<h3>").addClass("panel-title text-center").text("部分未读名单(前50人):").append(
                                    $("<a>").addClass("btn btn-warning").css("color", "white").attr("href", link).text("统计表下载 [Excel]")
                                )
                            )
                        ).append(
                            $("<div>").addClass("panel-body").append(
                                $("<p>").css("color", "gray").text(users.length > 0 ? users.join(", ") : "(全部人员已阅)")
                            )
                        )
                    );

                    if (total > 0) {
                        var config = {
                            chart: {type: "bar"},
                            title: {text: null},
                            xAxis: {categories: ["阅读情况", "总人数"],},
                            yAxis: {min: 0, title: {text: "比例(%)", align: "high"}, labels: {overflow: "justify"}},
                            tooltip: {valueSuffix: " 人"},
                            plotOptions: {bar: {dataLabels: {enabled: true}}, series: {stacking: "percent"}},
                            legend: {
                                layout: "vertical", align: "center", verticalAlign: "middle",
                                x: 0, y: -20, floating: true, borderWidth: 1, shadow: true,
                                backgroundColor: (Highcharts.theme && Highcharts.theme.legendBackgroundColor) || "#FFFFFF"
                            },
                            credits: {enabled: false},
                            series: [
                                {name: "已阅读", data: [read], color: "#16FE62"},
                                {name: "未阅读", data: [notRead], color: "#FA2D62"},
                                {name: "总应读人数", data: [null, total], color: "#4AE6FA"}
                            ]
                        };
                        chart.highcharts(config);
                    } else {
                        chart.append($("<h3>").addClass("text-center").css("color", "gray").css("margin-bottom", "20px").text("(无人员应读)"));
                    }

                    $("#statisticModal").modal("show");
                }
            });
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
            <th>常用功能</th>
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
                    <button type="button" class="btn btn-danger btn-xs destroy" data-id="{{ $notification->id }}">
                        <span class="glyphicon glyphicon-remove">删除</span>
                    </button>
                    @endpermission
                    <button type="button" class="btn btn-default btn-xs statistic" data-id="{{$notification->id}}">
                        阅读统计
                    </button>
                    <!-- BEGIN MODAL -->
                    <div class="modal fade" id="statisticModal" tabindex="-1" role="dialog"
                         aria-labelledby="statisticModalLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;
                                    </button>
                                    <h4 class="modal-title" id="statisticModalLabel">阅读统计</h4>
                                </div>
                                <div id="statisticModalBody" class="modal-body">
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-info" data-dismiss="modal">关闭</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- END MODAL -->
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
