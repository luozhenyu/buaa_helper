@extends('layouts.app')

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
</style>
@endpush

@push('jsLink')
<script src="{{ url('/js/file_upload.js') }}"></script>
@endpush

@push('js')
<script>
    $(function () {
        var files = {!! $notification->files->map(function ($item, $key) {return $item->file_info;})->toJson() !!};
        if (files.length > 0) {
            for (var i = 0; i < files.length; i++) {
                $("#attachmentContainer").append(parseFile(files[i]));
            }
        } else {
            $("#attachmentContainer").html("<h3 style='text-align:center;color:gray;margin:0;'>(无附件)</h3>");
        }
    });
</script>
@endpush

@push("crumb")
<li><a href="{{ url("/") }}">主页</a></li>
<li><a href="{{ route('notification') }}">通知中心</a></li>
<li><a href="{{ route('notification').'/draft' }}">草稿箱</a></li>
<li class="active">通知 - {{ $notification->title }}</li>
@endpush

@section('content')
    <div class="col-md-12">
        <h3 class="text-center">
            @if($notification->important)
                <span class="label label-danger">必读</span>
            @endif
            {{ $notification->title }}
        </h3>
        <div class="text-center information-line-1">
            <div class="label-block">
                <label class="label label-info">发布部门</label>
                {{ $notification->department->name }} - {{ $notification->user->name}}
            </div>

            <div class="label-block">
                <label class="label label-success">发布时间</label> {{ \Carbon\Carbon::now()->format('Y年m月d日 H:i:s') }}
            </div>
        </div>

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

                    $tooltip_content = "<h5>距离截止:<font style = 'font-weight: bold;color:$color;'>$time_remain_string</font></h5>";
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

    <div class="col-md-10 col-md-offset-1">
        <div class="panel panel-success">
            <div class="panel-heading">
                <h3 class="panel-title">附件列表</h3>
            </div>
            <div id="attachmentContainer" class="panel-body">
            </div>
        </div>
    </div>

    <form method="POST" action="{{ route('notification')."/{$notification->id}/publish" }}"
          onsubmit="return confirm('此通知将发给 {{ $notification->notifiedUsers->count() }} 人，发布后将无法更改任何信息，是否确认发布此通知？')">
        {{ csrf_field() }}
        <div class="col-md-4 pull-right">
            <a href="{{ route('notification')."/{$notification->id}/modify" }}" class="btn btn-default">
                返回修改
            </a>

            <button type="submit" class="btn btn-primary">
                确认无误并发布
            </button>
        </div>
    </form>
@endsection
