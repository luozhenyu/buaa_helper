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

        table.bh-account-show-left .bh-account-list-phone,
        table.bh-account-show-left .bh-account-list-email,
        table.bh-account-show-left .bh-account-head-phone,
        table.bh-account-show-left .bh-account-head-email {
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
                        $("#importModal").on("hidden.bs.modal", function () {
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
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr("content")
                    },
                    success: function (json) {
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
                                var btn_modify = $("<td>");

                                dat.url && btn_modify.append(
                                    $("<a>").text("修改")
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
                    selected_data.search = data.search;
                    console.log(selected_data);
                    new_page(1, selected_data);
                }
            });
            new_page(1, selected_data);

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

        {{-- 用户分组管理js --}}
        $(function () {
            function groupRefresh(id, page) {
                var param = page ? ("?page=" + page) : "";
                if (id) {
                    $.get("{{ route('group') }}" + "/" + id + param, function (json) {
                        var name = json.name,
                            data = json.data;

                        var groupContainer = $("#group_container").empty();

                        $("#group_title").text(" - " + name + " - " + json.total + "条记录");

                        for (var i = 0; i < data.length; i++) {
                            groupContainer.append(
                                $("<tr>").append(
                                    $("<td>").append($("<input>").attr("type", "checkbox").data("id", data[i].number))
                                ).append(
                                    $("<td>").text(data[i].department_name)
                                ).append(
                                    $("<td>").text(data[i].number)
                                ).append(
                                    $("<td>").text(data[i].name)
                                )
                            )
                        }

                        $("#group_paginate").addClass("text-center").paginate({
                            currentPage: json.current_page,
                            lastPage: json.last_page,
                            callback: function (page) {
                                groupRefresh(id, page);
                            }
                        });
                        enableButton();
                    });
                } else {
                    $("#group_container").empty();
                    $("#group_title").empty();
                }
            }

            function groupsRefresh() {
                $.get("{{ route('group') }}", function (data) {
                    var groups = data.data;

                    $("#groups_count").text(groups.length);

                    var groupList = $("#group_list").empty();
                    for (var i = 0; i < groups.length; i++) {
                        groupList.append(
                            $("<a>").addClass("list-group-item").attr("href", "javascript:void(0)").data("id", groups[i].id).text(groups[i].name)
                                .click(function () {
                                    var that = $(this),
                                        id = that.data("id"),
                                        text = that.text();
                                    $("#group_delete").unbind().click(function () {
                                        if (confirm("您确定要删除分组 " + text + " 吗，该操作不可恢复？")) {
                                            groupDelete(id);
                                            groupRefresh();
                                        }
                                    });

                                    $("#group_rename").unbind().click(function () {
                                        var name;
                                        if (name = prompt("请输入分组名称:(长度为10字符以内)", text)) {
                                            groupRename(id, name);
                                        }
                                    });

                                    $("#group_insert").unbind().click(function () {
                                        var input;
                                        if (input = prompt("请输入学号:(多个学号以空格隔开)").trim()) {
                                            var numbers = input.split(/\s+/);
                                            groupInsert(id, numbers);
                                        }
                                    });

                                    $("#group_erase").unbind().click(function () {
                                        var numbers = $("#group_container").find("input:checked").map(function () {
                                            return $(this).data("id");
                                        });
                                        numbers = $.makeArray(numbers);
                                        if (confirm("确认要删除选中的" + numbers.length + "项？")) {
                                            groupErase(id, numbers);
                                        }
                                    });

                                    $("#group_select_all").unbind().click(function () {
                                        $("#group_container").find("input:checkbox").prop("checked", true);
                                    });

                                    $("#group_select_none").unbind().click(function () {
                                        $("#group_container").find("input:checkbox").prop("checked", false);
                                    });

                                    $("#group_select_opposite").unbind().click(function () {
                                        $("#group_container").find("input:checkbox").each(function () {
                                            $(this).prop("checked", !$(this).prop("checked"));
                                        });
                                    });

                                    groupRefresh(id);
                                })
                        )
                    }
                })
            }

            function groupCreate(name) {
                $.ajax({
                    url: "{{ route('group') }}",
                    type: "POST",
                    data: {name: name},
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr("content")
                    },
                    success: function (data) {
                        if (data.errmsg) {
                            alert(data.errmsg);
                        } else {
                            groupsRefresh();
                            reloadOnExit();
                        }
                    }
                });
            }

            function groupDelete(id) {
                $.ajax({
                    url: "{{ route('group') }}" + "/" + id,
                    type: "DELETE",
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr("content")
                    },
                    success: function (data) {
                        if (data.errmsg) {
                            alert(data.errmsg);
                        } else {
                            groupsRefresh();
                            disableButton();
                            reloadOnExit();
                        }
                    }
                });
            }

            function groupRename(id, name) {
                $.ajax({
                    url: "{{ route('group') }}" + "/" + id,
                    type: "PUT",
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr("content")
                    },
                    data: {name: name},
                    success: function (data) {
                        if (data.errmsg) {
                            alert(data.errmsg);
                        } else {
                            groupsRefresh();
                            groupRefresh(id);
                            reloadOnExit();
                        }
                    }
                });
            }

            function groupInsert(id, numbers) {
                $.ajax({
                    url: "{{ route('group') }}" + "/" + id + "/insert",
                    type: "POST",
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr("content")
                    },
                    data: {number: numbers},
                    success: function (data) {
                        if (data.errmsg) {
                            alert(data.errmsg);
                        } else {
                            alert(data.msg);
                            groupRefresh(id);
                        }
                    }
                });
            }

            function groupErase(id, numbers) {
                $.ajax({
                    url: "{{ route('group') }}" + "/" + id + "/erase",
                    type: "POST",
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr("content")
                    },
                    data: {number: numbers},
                    success: function (data) {
                        if (data.errmsg) {
                            alert(data.errmsg);
                        } else {
                            alert(data.msg);
                            groupRefresh(id);
                        }
                    }
                });
            }

            function getFunctionButtonId() {
                return [
                    "group_insert", "group_erase",
                    "group_select_all", "group_select_none", "group_select_opposite",
                    "group_rename", "group_delete"
                ];
            }

            function enableButton() {
                var list = getFunctionButtonId();
                for (var i = 0; i < list.length; i++) {
                    $("#" + list[i]).removeAttr("disabled");
                }
            }

            function disableButton() {
                var list = getFunctionButtonId();
                for (var i = 0; i < list.length; i++) {
                    $("#" + list[i]).attr("disabled", true);
                }
            }

            function reloadOnExit() {
                var evt = "hide.bs.modal";
                $("#groupModal").on(evt, function () {
                    if (confirm("您修改了用户分组信息，因此本网页需要重新加载，是否立即刷新？")) {
                        window.location.reload();
                    }
                    $(this).unbind(evt);
                });
            }

            $("#group_create").click(function () {
                var name;
                if (name = prompt("请输入分组名称:(长度为10字符以内)")) {
                    groupCreate(name);
                }
            });

            disableButton();
            groupsRefresh();
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
                        <div style="display: inline-block;">
                            <div class="btn-group">
                                <a type="button" class="btn btn-primary"
                                   href="{{route('accountManager').'/create'}}">创建新用户
                                </a>
                                <button type="button" class="btn btn-success" data-toggle="modal"
                                        data-target="#importModal">
                                    导入Excel
                                </button>
                            </div>
                        </div>
                        <!-- 模态框（Modal） -->
                        <div class="modal fade" id="importModal" tabindex="-1" role="dialog"
                             aria-labelledby="importModalLabel" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal"
                                                aria-hidden="true">&times;
                                        </button>
                                        <h4 class="modal-title" id="importModalLabel">批量导入用户信息</h4>
                                    </div>

                                    <div class="modal-body">
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
                    <!-- BEGIN GROUP -->
                        <div style="display: inline-block;">
                            <div class="btn-group">
                                <button type="button" class="btn btn-info" data-toggle="modal"
                                        data-target="#groupModal">
                                    分组管理
                                </button>
                            </div>
                        </div>
                        <!-- 模态框（Modal） -->
                        <div class="modal fade" id="groupModal" tabindex="-1" role="dialog"
                             aria-labelledby="groupModalLabel" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                                            &times;
                                        </button>
                                        <h4 class="modal-title" id="groupModalLabel">用户分组
                                            <span id="group_title"></span>
                                        </h4>
                                    </div>

                                    <div class="modal-body container">
                                        <div class="col-xs-3">
                                            <div class="list-group-item active">
                                                <h4 class="list-group-item-heading">
                                                    我的分组
                                                </h4>
                                                <span id="groups_count">0</span>/{{ \App\Http\Controllers\GroupController::MAX_GROUPS }}
                                            </div>

                                            <ul class="list-group" id="group_list"></ul>
                                        </div>
                                        <div class="col-xs-9">
                                            <div class="container">
                                                <div class="btn-group btn-group-sm">
                                                    <button class="btn btn-success" id="group_insert">
                                                        <span class="glyphicon glyphicon-plus"></span>
                                                        添加成员
                                                    </button>
                                                    <button class="btn btn-danger" id="group_erase">
                                                        <span class="glyphicon glyphicon-remove"></span>
                                                        删除选中成员
                                                    </button>
                                                </div>

                                                <div class="btn-group btn-group-sm pull-right">
                                                    <button class="btn btn-success" id="group_select_all">
                                                        全选
                                                    </button>
                                                    <button class="btn btn-info" id="group_select_none">
                                                        全不选
                                                    </button>
                                                    <button class="btn btn-primary" id="group_select_opposite">
                                                        反选
                                                    </button>
                                                </div>
                                            </div>

                                            <table class="table table-hover table-condensed">
                                                <thead>
                                                <tr>
                                                    <th></th>
                                                    <th>院系 / 部门</th>
                                                    <th>学号 / 工号</th>
                                                    <th>姓名</th>
                                                </tr>
                                                </thead>
                                                <tbody id="group_container">
                                                </tbody>
                                            </table>
                                            <div id="group_paginate"></div>
                                        </div>
                                        <div class="col-xs-12">
                                            <button class="btn btn-default" id="group_create">
                                                <span class="glyphicon glyphicon-plus"></span>
                                                添加分组
                                            </button>
                                            <button class="btn btn-danger pull-right" id="group_delete">
                                                <span class="glyphicon glyphicon-remove"></span>
                                                删除此分组
                                            </button>
                                            <button class="btn btn-info pull-right" id="group_rename">
                                                <span class="glyphicon glyphicon-wrench"></span>
                                                重命名该组
                                            </button>
                                        </div>
                                    </div>

                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-default" data-dismiss="modal">关闭
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- END GROUP -->
                        <div class="pull-right" style="display: inline-block;">
                            <h5 id="information"></h5>
                        </div>
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
