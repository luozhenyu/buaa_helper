@extends('layouts.app')

@push('css')
<style>
    #td_search_tools {
        vertical-align: top;
        width: 35%;
        min-width: 200px;
        padding-right: 5px;
    }

    table#main {
        width: 100%;
    }

    .list-group li.list-group-item {
        padding: 4px 4px;
    }
    .base, .base.panel-heading {
        background-color: white;
    }
    .base:hover {
        background-color: #eeeeee;
    }

    .panel-group {
        margin-bottom: 0px;
    }

    .selected_content {
        margin-top: 12px;
        box-shadow: inset 1px 1px 1px rgba(0, 0, 0, 0.1);
        background-color: #dedede;
        border-radius: 5px;
        padding: 6px;
    }

    .selected_content > div {
        display: inline-block;
    }

    .select_div {
        overflow: auto;
        max-height: 550px;
        padding: 2px;
        margin-top: 6px;
    }

    #collapse_2 {
        padding: 4px;
        padding-right: 0px;
    }

    #show_hide {
        height: 80px;
        width: 14px;
        background-color: #ececec;
        border-radius: 6px;
        vertical-align: middle;
    }

    #show_hide:hover, #show_hide:focus {
        background-color: #dedede;
        box-shadow: 1px 1px 1px rgba(0, 0, 0, 0.2);
    }

    #show_hide .glyphicon {
        margin-top: 33px;
        font-size: 14px;
    }

    .selected_element {
        padding: 2px 4px;
        border: 1px solid lightgray;
        border-radius: 6px;
        background-color: #f8f8f8;
        margin-right: 5px;
        margin-bottom: 3px;
    }

    .selected_element:hover {
        background-color: white;
    }

    .selected_element .glyphicon {
        color: red;
    }

    .empty_label {
        margin: 2px;
        text-align: center;
        color: darkgray;
    }
</style>
@endpush

@push('js')
<script>
    $(function () {
        $("[data-toggle='tooltip']").tooltip();

        $("#btn_upload").click(function () {
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
        });
    });


    $(function () {
        $("#show_hide").click(function () {
            var btn = $(this).find(".glyphicon");
            if (btn.hasClass("glyphicon-chevron-left")) {
                $("#td_search_tools").fadeOut(200);
                btn.attr("class", "glyphicon glyphicon-chevron-right");
            } else {
                $("#td_search_tools").fadeIn(200);
                btn.attr("class", "glyphicon glyphicon-chevron-left");
            }
        });

        $(".base").click(function () {
            var name = $(this).attr("name");
            var value = $(this).attr("value");
            var flag = true, belongs_to = "";

            $(".selected_element").each(function(){
                if (is_parent($(this).attr("value"), value)) {
                    flag = false;
                    belongs_to = $(this).attr("name");
                }
            })

            if (flag) {
                $(".selected_element").each(function(){
                    if (is_parent(value, $(this).attr("value"))) $(this).remove();
                });
                $(".selected_content").append("<div class = \"selected_element\" " +
                    "name = \"" + name + "\" value = \"" + value + "\">" + name +
                    "<span class = \"glyphicon glyphicon-remove click remove_selected_element\" " +
                    "onclick = \"remove_selection(this.parentNode);\"></span>" +
                    "</div>");
            } else {
                $(this).tooltip({
                    trigger : "manual" ,
                    placement : "right" ,
                    title : "<h5>该用户群组已经被选择</h5>" +
                    "<h5>隶属于：<b style = 'color: orangered'>" + belongs_to + "</b></h5>" ,
                    html : true
                });
                $(this).tooltip("show");
            }
            selection_check();
        });
        $(".base").mouseleave(function(){
            $(this).tooltip("hide");
            $(this).tooltip("destroy");
        })
    });

    function remove_selection(box) {
        box.remove();
        selection_check();
    }
    function selection_check() {
        if ($(".selected_element").length > 0) {
            if (!$(".empty_label").hasClass("hidden")) $(".empty_label").addClass("hidden");
        } else {
            $(".empty_label").removeClass("hidden");
        }
    }
    function is_parent(value1, value2) {
        var a1 = value1.split(",");
        var a2 = value2.split(",");
        if (a2.length < a1.length) return false;
        if ((a1.length == 1) && (a1[0] == "")) return true;
        if ((a2.length == 1) && (a2[0] == "")) return false;

        for (i = 0;i < a1.length;i++) if (a1[i] != a2[i]) return false;
        return true;
    }
</script>
@endpush

@push("crumb")
<li><a href="{{ url("/") }}">主页</a></li>
<li class="active">用户管理</li>
@endpush

@section('content')

    <table id="main" border="0">
        <tr>
            <td id="td_search_tools">
                <div id="search_tools row">
                    <div>
                        <form class="form-iniline" role="form" method="get"
                              action="{{ route('accountManager') }}">
                            <div class="input-group">
                                <input type="search" class="form-control" name="wd" value="{{ $wd }}"
                                       placeholder="学号／工号／姓名">
                                <span class="input-group-btn">
                                    <button type="submit" class="btn btn-primary">
                                        <span class="glyphicon glyphicon-search"></span> 搜索
                                    </button>
                                </span>
                            </div>
                        </form>
                    </div>
                    <div class="selected_content">
                        <h4 class="empty_label">(无任何选中对象)</h4>
                    </div>
                    <div class="select_div">
                        <div class="panel-group" id="accordion">
                            <div class="panel panel-default">
                                <div class="panel-heading click base slow_down" value=""
                                     name="全校人员">
                                    <h5 class="panel-title">
                                        <b>全校人员</b>
                                    </h5>
                                </div>
                            </div>

                            <div class="panel panel-default">
                                <div class="panel-heading click" data-toggle="collapse" data-parent="#accordion"
                                     href="#collapse_1">
                                    <h5 class="panel-title">
                                        机关部处
                                    </h5>
                                </div>
                                <div id="collapse_1" class="panel-collapse collapse">
                                    <ul class="list-group">
                                        <li class="list-group-item slow_down click base" value="0" name="全校各部门">
                                            <b>全校各部门</b>
                                        </li>
                                        @foreach(\App\Models\Department::where('number', '>=', '100')->get() as $key => $value)
                                            <li class="list-group-item slow_down click base"
                                                value="0,{{ $value->number }}" name="{{ $value->name }}">
                                                {{ $value->name }}
                                            </li>
                                        @endforeach

                                    </ul>
                                </div>
                            </div>

                            <div class="panel panel-default">
                                <div class="panel-heading click" data-toggle="collapse" data-parent="#accordion"
                                     href="#collapse_2">
                                    <h4 class="panel-title">
                                        学生
                                    </h4>
                                </div>
                                <div id="collapse_2" class="panel-collapse collapse"
                                     style="">
                                    <div class="panel-group" id="accordion_2">
                                        @php
                                            $num_2 = 0;
                                        @endphp
                                        <div class="panel panel-default">
                                            <div class="panel-heading click base slow_down" value="1" name="全校学生">
                                                <h4 class="panel-title"><b>全校学生</b></h4>
                                            </div>
                                        </div>
                                        @foreach(\App\Models\Property::where('name','grade')->firstOrFail()->propertyValues as $key => $value)
                                            <div class="panel panel-default">
                                                <div class="panel-heading click" data-toggle="collapse"
                                                     data-parent="#accordion_2"
                                                     href="#collapse_2_{{ $num_2 }}">
                                                    <h4 class="panel-title">{{ $value->display_name }}</h4>
                                                </div>
                                                <div id="collapse_2_{{ $num_2++ }}"
                                                     class="panel-collapse collapse">
                                                    <ul class="list-group">
                                                        <li class="list-group-item slow_down base"
                                                            value="1,{{ $value->name }}"
                                                            name="{{ $value->display_name }} - 全体学生">
                                                            <b>全体学生</b>
                                                        </li>
                                                        @foreach(\App\Models\Department::where('number', '<', '100')->get() as $key_1 => $value_1)
                                                            <li class="list-group-item slow_down click base"
                                                                value="1,{{ $value->name }},{{$value_1->number}}"
                                                                name="{{ $value->display_name." - ".$value_1->number."系" }}">
                                                                ({{  $value_1->number }}) {{ $value_1->name }}
                                                            </li>
                                                        @endforeach
                                                    </ul>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </td>
            <td>
                <div id="show_hide" class="clickable slow_down">
                    <span class="glyphicon glyphicon-chevron-left"></span>
                </div>
            </td>
            <td style="vertical-align: top;">
                <table class="table table-condensed table-hover">
                    <caption>
                        @permission('create_user')
                        <div class="col-xs-12">
                            <div class="btn-group">
                                <a type="button" class="btn btn-primary"
                                   href="{{route('accountManager').'/create'}}">创建新用户
                                </a>
                                <button type="button" class="btn btn-success" data-toggle="modal"
                                        data-target="#myModal">
                                    导入Excel
                                </button>
                            </div>
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
                                        <button type="submit" class="btn btn-success" id="btn_upload">
                                            <span class="glyphicon glyphicon-upload"></span> 立即上传
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endpermission

                    </caption>

                    <thead>
                    <tr>
                        @foreach($orders as $key => $value)
                            <th>
                                <a href="{{ route('accountManager').'?wd='.$wd.'&sort='.$key.'&by='.$value['by'] }}">{{ $value['name'] }}</a>
                            </th>
                        @endforeach
                        <th>账号类型</th>
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
                @if($users->count() === 0)
                    <h2 style="color:gray;text-align:center;">(没有用户)</h2>
                @endif

                <div class="text-center">{{ $users->links() }}</div>
            </td>
        </tr>
    </table>


@endsection
