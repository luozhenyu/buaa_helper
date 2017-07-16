@extends('layouts.app')

@push('css')
<style>
    #td_search_tools {
        vertical-align: top;
        width: 35%;
        min-width: 200px;
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
        margin-bottom: 0;
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
        max-height: 470px;
        height: 470px;
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

    #td_show_hide {
        padding: 5px;
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
        font-weight: bold;
    }

    .empty_label {
        margin: 2px;
        text-align: center;
        color: darkgray;
    }

    .tooltip {
        min-width: 180px;
    }

    #table_content td {
        text-align: center;
    }

    table.bh-account-hide-left td#td_search_tools {
        display: none;
    }


</style>
@endpush


@push('jsLink')
<script src="{{ url('/js/paginate.js') }}"></script>
<script src="{{ url('/js/user_select.js') }}"></script>
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
                    $("#tips").fadeIn("slow").text("正在上传，请稍候");
                    clock = Date.now();
                },
                success: function (resp) {
                    if (resp["errmsg"] === undefined) {
                        clock = Date.now() - clock;
                        var tips = $("#tips").html("<p>成功：" + resp["success"] + " 跳过：" + resp["skip"] + " 失败：" + resp["fail"] + " 耗时: " + clock + "ms</p>");
                        if (resp["msg"].length > 0) {
                            tips.append("<h4>错误信息(前10条)</h4>")
                                .append("<div>" + resp["msg"].join("<br>") + "</div>");
                        }
                    } else {
                        $("#tips").html(resp["errmsg"]);
                    }
                    $("#myModal").on("hidden.bs.modal", function () {
                        window.location.reload();
                    });
                },
                error: function () {
                    $("#tips").html("请求超时");
                }
            });
        });

        function new_page(page, data) {
            $.ajax({
                url: "/account_manager/ajax?page=" + page,
                type: 'POST',
                contentType: "application/json; charset=utf-8",
                data: JSON.stringify(data),
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (json) {
                    console.log(json);
                    //return;
                    $("#table_content").empty();
                    $("#information")
                        .empty().append("共搜到 ")
                        .append(
                            $("<b>").append(json.total)
                        )
                        .append(" 条记录");
                    if (json.data.length > 0) {
                        for (var i = 0; i < json.data.length; i++) {
                            var dat = json.data[i];
                            var btn_modify = $("<td></td>");

                            dat.url && btn_modify.append(
                                $("<a></a>").append("修改")
                                    .addClass("btn btn-xs btn-info")
                                    .attr("href", dat.url)
                            );


                            $("#table_content").append(
                                $("<tr></tr>").append(
                                    $("<td>").addClass("bh-account-list-department").append(dat.department_name)
                                ).append(
                                    $("<td>").addClass("bh-account-list-number").append(dat.number)
                                ).append(
                                    $("<td>").addClass("bh-account-list-name").append(dat.name)
                                ).append(
                                    $("<td>").addClass("bh-account-list-role").append(dat.role_display_name)
                                ).append(
                                    $("<td>").addClass("bh-account-list-phone").append(dat.phone || '未填')
                                ).append(
                                    $("<td>").addClass("bh-account-list-email").append(dat.email || '未填')
                                ).append(
                                    btn_modify
                                )
                            )
                        }
                        $("#nobody").addClass("hidden");
                        $("[data-toggle='tooltip']").tooltip();

                        $("#page").paginate({
                            currentPage: json.current_page,
                            lastPage: json.last_page,
                            callback: function (page) {
                                new_page(page, selected_data);
                            }
                        });
                    } else {
                        $("#page").empty();
                        $("#nobody").removeClass("hidden");
                    }
                }
            });
        }


        var selected_data = {type: "student", range: [], property: []};
        //user_select 生成
        $(".bh-account-selector").user_select({
            data: {!! json_encode($selectData) !!},
            callback_filter: function (data) {  //单击筛选
                selected_data.range = data.departments;
                selected_data.property = data.properties;
                new_page(1, selected_data);
            }
        });
        new_page(1, selected_data);
    });
    $(function () {
        $("#show_hide").click(function () {
            if ($("#show_hide .glyphicon").hasClass("glyphicon-chevron-left")) {
                $("#show_hide .glyphicon").removeClass("glyphicon-chevron-left").addClass("glyphicon-chevron-right");
                $("table#main").removeClass("bh-account-show-left").addClass("bh-account-hide-left");
            } else {
                $("#show_hide .glyphicon").removeClass("glyphicon-chevron-right").addClass("glyphicon-chevron-left");
                $("table#main").removeClass("bh-account-hide-left").addClass("bh-account-show-left");
            }
        });
    });

</script>
@endpush

@push("crumb")
<li><a href="{{ url("/") }}">主页</a></li>
<li class="active">用户管理</li>
@endpush

@section('content')
    <table id="main" border="0" class="bh-account-panel bh-account-show-left">
        <tr>
            <td id="td_search_tools">
                <div id="search_tools" style="position: relative;min-height: 580px;">
                    <div style="position: absolute;left: 0px; right: 0px; top: 0px; bottom: 0px;">
                        <div>
                            <form class="form-iniline" role="form" method="get"
                                  action="{{ route('accountManager') }}">
                                <div class="input-group">
                                    <input type="search" class="form-control" name="wd" value="{{ "" }}"
                                           placeholder="学号／工号／姓名">
                                    <span class="input-group-btn">
                                    <button type="submit" class="btn btn-primary">
                                        <span class="glyphicon glyphicon-search"></span> 搜索
                                    </button>
                                </span>
                                </div>
                            </form>
                        </div>
                        <div class="bh-account-selector">
                        </div>
                    </div>
                </div>
            </td>
            <td id="td_show_hide">
                <div id="show_hide" class="clickable slow_down">
                    <span class="glyphicon glyphicon-chevron-left"></span>
                </div>
            </td>
            <td id="user_list" style="vertical-align: top;">
                <table class="table table-condensed table-hover">
                    <caption>
                        @permission('create_user')
                        <div class="col-xs-12">
                            <div style="display: inline-block;">
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
                            <div class="pull-right" style="display: inline-block;">
                                <h5 id="information"></h5>
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
                                            <label>第一步：</label>
                                            <a type="button" class="btn btn-info"
                                               href="{{ route('accountManager').'/import' }}">
                                                <span class="glyphicon glyphicon-download"></span> 下载模板
                                            </a>
                                        </div>

                                        <div class="form-group">
                                            <label for="file">第二步：用户信息上传【支持Excel表格】</label>
                                            <input type="file" id="file" autocomplete="off">
                                            <p class="help-block">请将数据导入至模板后上传</p></div>
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
                        <th>院系 / 部门</th>
                        <th>学号 / 工号</th>
                        <th>姓名</th>

                        <th>账号类型</th>
                        <th class="bh-account-head-phone">手机号码</th>
                        <th class="bh-account-head-email">邮箱地址</th>
                        <th></th>
                    </tr>
                    </thead>

                    <tbody id="table_content"></tbody>
                </table>

                <h2 id="nobody" style="color:gray;text-align:center;" class="hidden">(没有用户)</h2>
                <div id="page" class="text-center"></div>
            </td>
        </tr>
    </table>
@endsection
