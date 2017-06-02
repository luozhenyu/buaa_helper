@extends('layouts.app')

@php
    $files = collect();
    foreach ($notification->files as $file) {
        $files->push($file->downloadInfo());
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
        height: 4px;
        background-color: #eeeeee;
        margin-bottom: 15px;
    }

    #inner_progress_div {
        height: 100%;
    }

    @if(!$read_at)
    #confirmRead:hover {
        background-color: #39c05f;
    }
    @endif
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
            $("#attachmentContainer").html("<h3 style = \"text-align:center;color:gray;margin:0px;\">(无附件)</h3>");

        }

    });
</script>
@endpush

@push("crumb")
<li><a href="{{ url("/") }}">主页</a></li>
<li><a href="{{ url("/notification") }}">通知中心</a></li>
<li class="active">通知 - {{ $notification->title }}</li>
@endpush

@section('content')

    <div class = "col-md-12">
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
                <!--<label class="label label-primary" id="star">-->
                <button id = "star" class = "btn btn-primary btn-xs" style = "font-size: 14px;">
                    <span class="glyphicon glyphicon-star-empty">收藏</span>
                </button>
                <!--</label>-->
            </div>
        </div>
        <div class="text-center information-line-2">
            @if($notification->start_time)
                <div class="label-block">
                    <span style="color:darkgreen">起始日期:</span> {{ $notification->start_time }}
                </div>
            @endif

            @if($notification->end_time)
                <div class="label-block">
                    <span style="color:red">截止日期:</span> {{ $notification->end_time }}
                    @if($notification->end_time->diffInDays() < 1)
                        <label class="label label-danger" style = "font-size: 14px;">24小时内截止</label>
                    @endif

                </div>
            @endif

        </div>
        <div class = "text-center">
            @if($notification->start_time && $notification->end_time)
                @php
                    function color($c1, $c2, $ratio) {
                        return [
                            $c2[0] * $ratio + $c1[0] * (1 - $ratio),
                            $c2[1] * $ratio + $c1[1] * (1 - $ratio),
                            $c2[2] * $ratio + $c1[2] * (1 - $ratio)
                        ];
                    }

                    $from_begin = $notification->start_time->diffInSeconds();
                    $to_end = $notification->end_time->diffInSeconds();
                    $b_ratio = $from_begin / ($from_begin + $to_end);
                    $e_ratio = $to_end / ($from_begin + $to_end);
                    if ($b_ratio < 0.33) $p_style = "success"; else
                    if ($b_ratio < 0.66) $p_style = "warning"; else $p_style = "danger";
                    if ($b_ratio < 0.5) {
                        $c = color([0,255,0], [255,255,0], $b_ratio * 2);
                    } else {
                        $c = color([255,255,0], [255,0,0], ($b_ratio - 0.5) * 2);
                    }
                    $color = "rgb(".round($c[0]).", ".round($c[1]).", ".round($c[2]).")";
                @endphp
                <div id = "progress_div" style = "">
                    <div id = "inner_progress_div" style = "background-color: {{ $color }}; width: {{ $b_ratio * 100 }}%;">
                    </div>
                </div>

            @endif
        </div>
    </div>

    <div class = "col-md-12">
        <article>
            <div class="well well-lg">{!! $notification->content !!}</div>
        </article>
    </div>



    @if($notification->important)
        <div class = "col-md-12">
            <div class="text-right">
                <div class="label-block">
                    <h2>
                        @if($read_at)
                            <label class="label label-success">
                                <span class="glyphicon glyphicon-ok"></span>
                                已确认阅读
                            </label>
                        @else
                            <label class="label label-danger slow_down" id="confirmRead">
                                <span class="glyphicon glyphicon-pencil"></span>
                                我已仔细阅读
                            </label>
                            <script>
                                $(function () {
                                    $("#confirmRead").click(function () {
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
                                });
                            </script>
                        @endif
                    </h2>
                </div>
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
