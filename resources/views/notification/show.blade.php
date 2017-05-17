@extends('layouts.app')

@php($auth_user = Auth::user())
@php($canStar = $auth_user->notifications()->find($notification->id))
@php($canRead = $notification->important && $auth_user->notifications()->contains('id', $notification->id))
@php($hasRead = $auth_user->read_notifications()->find($notification->id))

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

    @if($canStar)
    #star:hover {
        background-color: #00a0e9;
    }

    @endif
    @if($canRead && !$hasRead)
    #read:hover {
        background-color: #39c05f;
    }
    @endif
</style>
@endpush

@push('js')
<script>
    $(function () {
                @if($canStar)

        var star = document.getElementById("star");

        function setStarState(self, stared) {
            if (stared) {
                self.innerHTML = '<span class="glyphicon glyphicon-star">已收藏</span>';
            } else {
                self.innerHTML = '<span class="glyphicon glyphicon-star-empty">收藏</span>';
            }
            self.dataset.stared = stared;
        }

        star.onclick = function () {
            var stared = !(star.dataset.stared === "true");
            var url = "{{ route('notification').'/'.$notification->id }}";
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
        setStarState(star,{{ $notification->stared_users()->find($auth_user->id)?'true':'false' }});
                @endif

                @if($canRead && !$hasRead)
        var read = document.getElementById("read");
        read.onclick = function () {
            var url = "{{ route('notification').'/'.$notification->id .'/read' }}";
            $.ajax({
                url: url,
                type: "POST",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (data) {
                    window.location.reload();
                }
            });
        };
        @endif
    })
    ;
</script>
@endpush

@push("crumb")
    <li><a href = "{{ url("/") }}">主页</a></li>
    <li><a href = "{{ url("/notification") }}">通知中心</a></li>
    <li class = "active">通知 - {{$notification->title}}</li>
@endpush

@section('content')
                <div class="panel panel-default">
                    <div class="panel-heading">查看通知</div>

                    <div class="panel-body">
                        <h3 class="text-center">
                            {{ ($notification->important?'[必读] ':'').$notification->title }}
                        </h3>
                        <div class="text-center">
                            <div class="label-block">
                                <label class="label label-info">发布部门</label> {{ $notification->department->name }}
                            </div>

                            <div class="label-block">
                                <label class="label label-warning">作者</label> {{ $notification->user->name }}
                            </div>

                            <div class="label-block">
                                <label class="label label-success">更新时间</label> {{ $notification->updated_at->format('Y年m月d日 H:i:s') }}
                            </div>
                            @if($canStar)
                                <div class="label-block">
                                    <label class="label label-primary" id="star">
                                        <span class="glyphicon glyphicon-star-empty">收藏</span>
                                    </label>
                                </div>
                            @endif
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

                        <div class="text-right">
                            <div class="label-block">
                                @if($canRead)
                                    <h2>
                                        @if(!$hasRead)
                                            <label class="label label-danger" id="read">
                                                <span class="glyphicon glyphicon-unchecked">我已仔细阅读</span>
                                            </label>
                                        @else
                                            <label class="label label-success">
                                                <span class="glyphicon glyphicon-ok">已确认阅读</span>
                                            </label>
                                        @endif
                                    </h2>
                                @endif
                            </div>
                        </div>

                        <div class="col-md-10 col-md-offset-1">
                            <div class="panel panel-success">
                                <div class="panel-heading">
                                    <h3 class="panel-title">附件列表</h3>
                                </div>
                                <div id="filesContainer" class="panel-body">
                                    {!! \App\Http\Controllers\NotificationController::insertFile($notification->files) !!}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
@endsection
