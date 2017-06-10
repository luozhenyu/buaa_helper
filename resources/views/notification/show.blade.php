@extends('layouts.app')

@php
    $files = collect();
    foreach ($notification->files as $file) {
        $files->push($file->downloadInfo);
    }
@endphp

@push('css')
<style>
    .label-block {
        display: inline-block;
        margin: 10px 5px 20px 5px;
    }

    .information-line-1 .label-block > label {
        text-shadow: black 2px 2px 1px;
        font-size: 14px;
    }

    .information-line-2 .label-block > span {
        font-weight: 900;
    }

    #star:hover {
        background-color: #00a0e9;
        border-color: #00a0e9;
    }

    #progress_div {
        width: 80%;
        margin-left: 10%;
        height: 15px;
        background-color: #eeeeee;
        margin-bottom: 15px;
        box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.1);
    }

    #inner_progress_div {
        height: 100%;
        /*animation: progress-bar-stripes 2s linear infinite;*/
        animation: reverse progress-bar-stripes 0.7s linear infinite, animate-positive 2s,
        myfirst 1.2s;
        background-size: 40px 40px;
    }

    #progress_div, #inner_progress_div {
        border-radius: 4px;
    }

    @keyframes myfirst {
        from {
            width: 0;
        }
        to {
            width: 100%;
        }
    }

    #excerpt {
        font-size: 16px;
    }
</style>
@endpush

@push('jsLink')
<script src="{{ url('/js/file_upload.js') }}"></script>
@endpush

@push('js')
<script>
    $(function () {
        function setStarState(self, stared) {
            if (stared) {
                self.innerHTML = '<span class="glyphicon glyphicon-star">已收藏</span>';
            } else {
                self.innerHTML = '<span class="glyphicon glyphicon-star-empty">收藏</span>';
            }
            self.dataset.stared = stared;
        }

        var star = document.getElementById("star");
        star.onclick = function () {
            var stared = star.dataset.stared !== "true";
            var url = "{{ route('notification') . '/' . $notification->id }}";
            url += stared ? "/star" : "/unstar";

            $.ajax({
                url: url,
                type: "POST",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (data) {
                    star.dataset.stared = stared;
                    setStarState(star, stared);
                }
            });
        };
        setStarState(star,{{ $stared_at? 'true': 'false' }});

        var files = JSON.parse("{!! addslashes($files->toJson()) !!}");
        if (files.length > 0) {
            for (var i = 0; i < files.length; i++) {
                $("#attachmentContainer").append(parseFile(files[i], false));
            }
        } else {
            $("#attachmentContainer").html("<h3 style='text-align:center;color:gray;margin:0;'>(无附件)</h3>");
        }

        $(window).scroll(function(){
            var bottom = $(".panel-success").offset().top + no_px($(".panel-success").css("height"));
            console.log($(document).scrollTop() + $(window).height()
                , $(document).height(), bottom);
        })
    });
    function no_px(st) {
        return parseInt(st.substr(0, st.length - 2));
    }
</script>
@endpush

@push("crumb")
<li><a href="{{ url("/") }}">主页</a></li>
<li><a href="{{ url("/notification") }}">通知中心</a></li>
<li class="active">通知 - {{ $notification->title }}</li>
@endpush

@section('content')
    <div class="col-md-12">
        <h3 class="text-center">
            {{ ($notification->important? '[必读] ' : '') . $notification->title }}
        </h3>
        <div class="text-center information-line-1">
            <div class="label-block">
                <label class="label label-info">发布部门</label> {{ $notification->department->name }}
            </div>

            <div class="label-block">
                <label class="label label-warning">作者</label> {{ $notification->user->name}}
            </div>

            <div class="label-block">
                <label class="label label-success">更新时间</label> {{ $notification->updated_at->format('Y年m月d日 H:i:s') }}
            </div>

            <div class="label-block">
                <span id="star" class="btn btn-primary btn-xs" style="font-size: 14px;">
                    <span class="glyphicon glyphicon-star-empty">收藏</span>
                </span>
            </div>
        </div>

        <p class="text-center" id="excerpt">摘要: {{ $notification->excerpt }}</p>

        <div class="text-center information-line-2">
            @if($notification->start_date)
                <div class="label-block">
                    <span style="color:darkgreen">起始日期:</span> {{ $notification->start_date }}
                </div>
            @endif

            @if($notification->finish_date)
                <div class="label-block">
                    <span style="color:red">截止日期:</span> {{ $notification->finish_date }}
                    @if($notification->finish_date->diffInDays() < 1)
                        <label class="label label-danger" style="font-size: 14px;">24小时内截止</label>
                    @endif
                </div>
            @endif
        </div>
        <div class="text-center">
            @if($notification->start_date && $notification->finish_date)
                @php
                    function color($c1, $c2, $ratio) {
                        return [
                            $c2[0] * $ratio + $c1[0] * (1 - $ratio),
                            $c2[1] * $ratio + $c1[1] * (1 - $ratio),
                            $c2[2] * $ratio + $c1[2] * (1 - $ratio)
                        ];
                    }

                    $from_begin = $notification->start_date->diffInSeconds();
                    $to_end = $notification->finish_date->diffInSeconds();
                    $to_end_h = $notification->finish_date->diffInHours();
                    if ($to_end_h >= 24) {
                        $time_remain_string = floor($to_end_h / 24)."天".($to_end_h % 24)."小时";
                    } else if ($to_end_h >= 1) {
                        $time_remain_string = ($to_end_h % 24)."小时";
                    } else {
                        $time_remain_string = "不足一小时";
                    }

                    $b_ratio = $from_begin / ($from_begin + $to_end);

                    if ($b_ratio < 0.5) {
                        $c = color([0,255,0], [255,255,0], $b_ratio * 2);
                        $linear_alpha = ($b_ratio * 2) * (0.7 - 0.35) + 0.35;
                    } else {
                        $c = color([255,255,0], [255,0,0], ($b_ratio - 0.5) * 2);
                        $linear_alpha = (2 - $b_ratio * 2) * (0.7 - 0.35) + 0.35;
                    }

                    $color = "rgb(".round($c[0]).", ".round($c[1]).", ".round($c[2]).")";

                    $tooltip_content = "<h5>距离截止：
                        <font style = 'font-weight: bold;color:$color;'>$time_remain_string</font></h5>";
                    if (($notification->finish_date->diffInDays() < 1) && (!$read_at) && $notification->important) {
                        $tooltip_content = $tooltip_content
                            ."<h5>24小时内截止，<i style = 'font-weight:900;color: red;'>请抓紧时间</i></h5>";
                    }
                @endphp
                <div id="progress_div" data-toggle="tooltip" data-html="true"
                     title="{{ $tooltip_content }}">
                    <div id="outer_progress_div" style="height: 100%; width: {{ $b_ratio * 100 }}%;">
                        <div id="inner_progress_div" style="background-color: {{ $color }};width: 100%;
                                background-image: linear-gradient(45deg,rgba(255,255,255,{{ $linear_alpha }}) 25%,
                                transparent 25%,transparent 50%,rgba(255,255,255,{{ $linear_alpha }}) 50%,
                                rgba(255,255,255,{{ $linear_alpha }}) 75%,transparent 75%,transparent);"></div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <article class="col-md-12">
        <div class="well well-lg">{!! $notification->content !!}</div>
    </article>

    @if($notification->important)
        <div class="col-md-12">
            <div class="text-right">
                <h2 class="label-block">
                    @if($read_at)
                        <label class="label label-success">
                            <span class="glyphicon glyphicon-ok"></span>
                            已确认阅读
                        </label>
                    @else
                        <div id="scrollBar" style="position: fixed;bottom: 40px;right: 80px;z-index: 99">
                            <div class="progress" style="height: 10px;">
                                <div class="progress-bar progress-bar-info" id="progressBar"
                                     style="width: 0;transition:none"></div>
                            </div>
                            <label id="confirmRead" class="label label-danger slow_down">
                                <span class="glyphicon glyphicon-question-sign"></span>
                                是否仔细阅读
                            </label>
                        </div>

                        <script>
                            $(function () {
                                var maxProgress = 0;
                                var qSign = '<span class="glyphicon glyphicon-question-sign"></span>';
                                $("#confirmRead").mouseenter(function () {
                                    $(this).html(qSign + "请先完成阅读");
                                }).mouseleave(function () {
                                    $(this).html(qSign + "是否仔细阅读");
                                });

                                $(window).scroll(function () {
                                    var total = $(document).height() - $(window).height();
                                    var vis = $(window).scrollTop();
                                    $("#progressBar").css("width", (maxProgress = Math.max(maxProgress, 100 * vis / total)) + "%");

                                    var left = total - vis;
                                    if (left < 100) {
                                        $("#confirmRead").html(qSign + "点击确认阅读").css("background-color", "#5cb85c")
                                            .unbind()
                                            .click(function () {
                                                $.ajax({
                                                    url: "{{ route('notification').'/'.$notification->id .'/read' }}",
                                                    type: "POST",
                                                    headers: {
                                                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                                    },
                                                    success: function (data) {
                                                        window.location.reload();
                                                    }
                                                });
                                            });
                                    }
                                });
                            });
                        </script>
                    @endif
                </h2>
            </div>
        </div>
    @endif

    <div class="col-md-10 col-md-offset-1">
        <div class="panel panel-success">
            <div class="panel-heading">
                <h3 class="panel-title">附件列表</h3>
            </div>
            <div id="attachmentContainer" class="panel-body">
            </div>
        </div>
    </div>
@endsection
