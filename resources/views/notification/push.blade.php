@extends('layouts.app')

@php($auth_user=Auth::user())

@push('cssLink')
<link rel="stylesheet" href="//cdn.bootcss.com/bootstrap-select/1.12.1/css/bootstrap-select.min.css">
@endpush

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
</style>
@endpush

@push('jsLink')
<script src="//cdn.bootcss.com/bootstrap-select/1.12.1/js/bootstrap-select.min.js"></script>
<script src="//cdn.bootcss.com/bootstrap-select/1.12.1/js/i18n/defaults-zh_CN.js"></script>
@endpush

@push('js')
<script>
    function array_has_item(array, item) {
        for (var i = 0; i < array.length; i++) {
            if (array[i] == item)
                return true;
        }
        return false;
    }

    function getUserList() {
        var userList = document.getElementById("userList");
        var children = userList.children;
        var userData = [];
        for (var i = 0; i < children.length; i++) {
            userData.push(children[i].dataset.id);
        }
        return userData;
    }

    function parseJson() {
        $('input[name="send2college"]').val(JSON.stringify($('#send2college').selectpicker('val')));
        $('input[name="send2department"]').val(JSON.stringify($('#send2department').selectpicker('val')));
        $('input[name="send2user"]').val(JSON.stringify(getUserList()));
    }

    $(function () {
        $("#send2college").selectpicker("val", [{{ implode(',', $notified_college) }}]);
        $("#send2department").selectpicker("val", [{{ implode(',', $notified_department) }}]);

        $("#queryBtn").click(function () {
            $.ajax({
                url: "{{route('notification').'/search_user'}}",
                type: "POST",
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content")
                },
                data: {
                    list: $("#queryArea").val()
                },
                success: function (list) {
                    var userData = getUserList();
                    var count = 0;
                    var userList = document.getElementById("userList");
                    for (var i = 0; i < list.length; i++) {
                        if (!array_has_item(userData, list[i])) {
                            userList.innerHTML += '<li class="list-group-item col-md-4" data-id="' + list[i]['id'] + '">' + list[i]['number']
                                + '<span class="badge" onclick="this.parentNode.remove()">删除</span></li>';
                            count++;
                        }
                    }
                    $("#queryArea").val('').attr('placeholder', '成功添加 ' + count + ' 项');
                }
            });
        });
    });

</script>
@endpush

@push("crumb")
<li><a href="{{ url("/") }}">主页</a></li>
<li><a href="{{ url("/notification") }}">通知中心</a></li>
<li class="active">推送 - {{$notification->title}}</li>
@endpush

@section('content')
    <div class="panel panel-default">
        <div class="panel-heading">查看通知</div>

        <div class="panel-body">
            <h3 class="text-center">
                {{$notification->title}}
            </h3>
            <div class="text-center">
                <div class="label-block">
                    <label class="label label-info">发布部门</label> {{ $notification->department->name }}
                </div>

                <div class="label-block">
                    <label class="label label-warning">作者</label> {{ is_null($notification->user)?$notification->user_id:$notification->user->name }}
                </div>

                <div class="label-block">
                    <label class="label label-success">更新时间</label> {{ $notification->updated_at->format('Y年m月d日 H:i:s') }}
                </div>
            </div>

            <form class="form-horizontal" role="form" method="POST"
                  action="{{ route('notification').'/'. $notification->id.'/push'}}" onsubmit="parseJson()">
                {!! csrf_field() !!}

                <div class="panel panel-info">
                    <div class="panel-heading">
                        <h3 class="panel-title">批量发送</h3>
                    </div>
                    <div class="panel-body">
                        <div class="row col-md-12">
                            <div class="col-md-5">
                                <div class="form-group">
                                    <input type="hidden" name="send2college">
                                    <label for="send2college" class="control-label">院系</label>
                                    <select class="selectpicker form-control{{ $errors->has('send2college') ? ' has-error' : '' }}"
                                            id="send2college" multiple
                                            title="选择接收通知的院系" data-selected-text-format="count > 3"
                                            data-actions-box="true">
                                        @foreach($departments as $department)
                                            @if($department->number < 100)
                                                <option value="{{ $department->id }}">{{ $department->number.'-'.$department->name }}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                    @if ($errors->has('send2college'))
                                        <span class="help-block">
                                                        <strong>{{ $errors->first('send2college') }}</strong>
                                                    </span>
                                    @endif
                                </div>
                            </div>

                            <div class="col-md-5 col-md-offset-1">
                                <div class="form-group">
                                    <input type="hidden" name="send2department">
                                    <label for="send2department" class="control-label">部门</label>
                                    <select class="selectpicker form-control{{ $errors->has('send2department') ? ' has-error' : '' }}"
                                            id="send2department"
                                            multiple title="选择接收通知的部门"
                                            data-selected-text-format="count > 3"
                                            data-actions-box="true">
                                        @foreach($departments as $department)
                                            @if($department->number >= 100)
                                                <option value="{{ $department->id }}">{{ $department->name }}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                    @if ($errors->has('send2department'))
                                        <span class="help-block">
                                                        <strong>{{ $errors->first('send2department') }}</strong>
                                                    </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="panel panel-warning">
                    <div class="panel-heading">
                        <h3 class="panel-title">精准投递</h3>
                    </div>
                    <div class="panel-body">
                        <div class="form-group col-md-12{{ $errors->has('send2user') ? ' has-error' : '' }}">
                            <input type="hidden" name="send2user">
                            <label for="send2user">工号／学号
                                <span class="text-muted">【多条数据请用逗号、分号或任意空白符分割】</span>
                            </label>
                            <button type="button" class="btn btn-xs btn-success pull-right" id="queryBtn">
                                查询并添加
                            </button>
                            <input class="form-control" id="queryArea" autocomplete="off">
                            <div class="center-block">
                                <ul class="list-group" id="userList">
                                    @foreach($notified_users as $notified_user)
                                        <li class="list-group-item col-md-4"
                                            data-id="{{ $notified_user->id }}">
                                            {{$notified_user->number}}
                                            <span class="badge" onclick="this.parentNode.remove()">删除</span>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                            @if ($errors->has('send2user'))
                                <span class="help-block">
                                                <strong>{{ $errors->first('send2user') }}</strong>
                                            </span>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="col-md-12">
                    <button type="submit" class="btn btn-primary pull-right">
                        提交
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
