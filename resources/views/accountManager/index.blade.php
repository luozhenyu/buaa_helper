@extends('layouts.app')

@push('css')
@endpush

@push('js')
<script>
    $(function () {
        $("[data-toggle='tooltip']").tooltip();
    });
    function upload() {
        var formData = new FormData();
        formData.append("file", $("#file")[0].files[0]);
        var clock;
        $.ajax({
            url: "{{ route('accountManager').'/import' }}",
            type: "POST",
            headers: {
                "X-CSRF-TOKEN": $("meta[name='csrf-token']").attr("content")
            },
            data: formData,
            processData: false,
            contentType: false,
            beforeSend: function () {
                $("#tips").fadeIn('slow').text("正在上传，请稍候");
                clock = Date.now();
            },
            success: function (resp) {
                if (resp["errmsg"] === undefined) {
                    clock = Date.now() - clock;
                    $("#tips").text('成功：' + resp['success'] + ' 跳过：' + resp['skip'] + ' 失败：' + resp['fail'] + ' 耗时: ' + clock + 'ms');
                } else {
                    $("#tips").text(resp["errmsg"]);
                }
            },
            error: function () {
                $("#tips").text("请求超时");
            }
        });
    }
</script>
@endpush

@push("crumb")
<li><a href="{{ url("/") }}">主页</a></li>
<li class="active">用户管理</li>
@endpush

@section('content')
    <div class="panel panel-default">
        <div class="panel-heading">用户管理</div>

        <div class="panel-body">
            <table class="table table-condensed table-hover">
                <caption>
                    @permission('create_user')
                    <div class="btn-group">
                        <a type="button" class="btn btn-primary"
                           href="{{route('accountManager').'/create'}}">创建新用户
                        </a>
                        <button type="button" class="btn btn-success" data-toggle="modal"
                                data-target="#myModal">导入Excel
                        </button>
                    </div>
                    <!-- 模态框（Modal） -->
                    <div class="modal fade" id="myModal" tabindex="-1" role="dialog"
                         aria-labelledby="myModalLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal"
                                            aria-hidden="true">&times;
                                    </button>
                                    <h4 class="modal-title" id="myModalLabel">批量导入用户信息</h4>
                                </div>

                                <div class="modal-body">
                                    {{ csrf_field() }}
                                    <div class="form-group">
                                        <a type="button" class="btn btn-info"
                                           href="{{ route('accountManager').'/import' }}">
                                            <span class="glyphicon glyphicon-download"></span> 下载模板
                                        </a>
                                    </div>

                                    <div class="form-group">
                                        <label for="file">用户信息上传【Excel】</label>
                                        <input type="file" id="file" autocomplete="off">
                                        <p class="help-block">请将数据导入至模板后上传</p>
                                    </div>
                                    <div class="alert alert-info" id="tips" style="display: none"></div>
                                </div>

                                <div class="modal-footer">
                                    <button type="button" class="btn btn-default"
                                            data-dismiss="modal">关闭
                                    </button>
                                    <button type="submit" class="btn btn-success" onclick="upload()">
                                        <span class="glyphicon glyphicon-upload"></span> 立即上传
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endpermission

                    <form class="form-inline pull-right " role="form" method="get"
                          action="{{ route('accountManager') }}">
                        <div class="input-group">
                                        <span class="input-group-btn">
                                            <button class="btn btn-default"
                                                    onclick="window.location.href='{{ route('accountManager') }}'">
                                                <span class="glyphicon glyphicon-remove"></span>
                                            </button>
                                        </span>

                            <input type="search" class="form-control" name="wd" value="{{$wd}}" placeholder="学号／工号／姓名">

                            <span class="input-group-btn">
                                <button type="submit" class="btn btn-primary">
                                    <span class="glyphicon glyphicon-search"></span> 搜索
                                </button>
                            </span>
                        </div>
                    </form>

                </caption>
                <thead>
                <tr>
                    <th>
                        <a href="{{route('accountManager').'?wd='.$wd.'&sort=department_id&by='.($sort==='department_id'&&$by==='asc'?'desc':'asc')}}">院系</a>
                    </th>
                    <th>
                        <a href="{{route('accountManager').'?wd='.$wd.'&sort=number&by='.($sort==='number'&&$by==='asc'?'desc':'asc')}}">学号／工号</a>
                    </th>
                    <th>
                        <a href="{{route('accountManager').'?wd='.$wd.'&sort=name&by='.($sort==='name'&&$by==='asc'?'desc':'asc')}}">姓名</a>
                    </th>
                    <th>
                        <a href="{{route('accountManager').'?wd='.$wd.'&sort=role_id&by='.($sort==='role_id'&&$by==='asc'?'desc':'asc')}}">账号类型</a>
                    </th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                @foreach($users as $user)
                    <tr>
                        <td>
                            <span data-toggle="tooltip" title="{{$user->department->name}}">
                                {{ $user->department->number }}
                            </span>
                        </td>
                        <td>{{ $user->number }}</td>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->roles->implode('display_name', ',') }}</td>
                        <td>
                            @permission(['modify_all_user','modify_owned_user'])
                            <button type="button" class="btn btn-info btn-xs"
                                    onclick="window.open('{{ route('accountManager').'/'.$user->id }}')">
                                修改
                            </button>
                            @endpermission
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            <div class="text-center">{{ $users->links() }}</div>
        </div>
    </div>

@endsection
