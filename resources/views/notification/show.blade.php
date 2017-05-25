@extends('layouts.app')

@php
    $files = collect();
    foreach ($notification->files as $file) {
        $files->push(\App\Http\Controllers\FileController::getArray($file));
    }
@endphp

@push('css')
<style>
    .label-block {
        display: inline-block;
        margin: 10px 5px 20px 5px;
    }

    .label-block > label {
        text-shadow: black 2px 2px 1px;
        font-size: 14px;
    }

    #star:hover {
        background-color: #00a0e9;
    }

    @if(!$read)
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
        setStarState(star,{{ $star? 'true': 'false' }});

        var files = JSON.parse("{!! addslashes($files->toJson()) !!}");
        for (var i = 0; i < files.length; i++) {
            $("#attachmentContainer").append(parseFile(files[i], false));
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
    <div class="panel panel-default">
        <div class="panel-heading">查看通知</div>

        <div class="panel-body">
            <h3 class="text-center">
                {{ ($notification->important? '[必读] ' : '') . $notification->title }}
            </h3>
            <div class="text-center">
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
                    <label class="label label-primary" id="star">
                        <span class="glyphicon glyphicon-star-empty">收藏</span>
                    </label>
                </div>
            </div>
            <div class="text-center">
                @if($notification->start_time)
                    <div class="label-block">
                        <span style="color:darkgreen">起始日期:</span> {{ $notification->start_time }}
                    </div>
                @endif

                @if($notification->end_time)
                    <div class="label-block">
                        <span style="color:red">截止日期:</span> {{ $notification->end_time }}
                    </div>
                @endif
            </div>


            <article class="col-md-12">
                <div class="well well-lg">{!! $notification->content !!}</div>
            </article>

            @if($notification->important)
                <div class="text-right">
                    <div class="label-block">
                        <h2>
                            @if($read)
                                <label class="label label-success">
                                    <span class="glyphicon glyphicon-ok">已确认阅读</span>
                                </label>
                            @else
                                <label class="label label-danger" id="confirmRead">
                                    <span class="glyphicon glyphicon-unchecked">我已仔细阅读</span>
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
        </div>
    </div>
@endsection
