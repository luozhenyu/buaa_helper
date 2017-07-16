<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="shortcut icon" href="{{ url('/favicon.ico') }}" type="image/x-icon"/>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name') }}</title>
    <link rel="stylesheet" href="{{ url('/components/bootstrap/dist/css/bootstrap.min.css') }}">
    <style>
        .slow-down, button, a {
            -webkit-transition-duration: 0.45s;
            transition-duration: 0.45s;
        }
        .bh-group-list .bh-group-list-item {
            cursor: pointer;
        }
        .bh-group-list .bh-group-list-item.active {
            background-color: #5bc0de;
            border-color: #5bc0de;
        }
        .bh-group-list .bh-group-list-item:hover,
        .bh-group-list .bh-group-list-item:focus {
            background-color: #e9e9e9;
            border-color: #e9e9e9;
        }
        .bh-group-list .bh-group-list-item.active:hover,
        .bh-group-list .bh-group-list-item.active:focus {
            background-color: #7ee8f0;
        }
        .bh-group-manage .bh-group-manage-group,
        .bh-group-manage .bh-group-manage-member {
            margin-bottom: 5px;
        }

        table th, table td {
            text-align: center;
        }

    </style>

    <script src="{{ url('/components/jquery/dist/jquery.min.js') }}"></script>
    <script src="{{ url('/components/bootstrap/dist/js/bootstrap.min.js') }}"></script>
    <script>
        $(function(){
            /*
                group内成员管理按钮
             */
            //删除成员
            $(".bh-group-manage-member-delete").click(function(){
                var rows = $("table tbody tr");
                for (var i = 0; i < rows.length; i++) {
                    var row = rows[i];
                    if (!!$(row).find("td:first-child input[type=checkbox]").prop("checked")) $(row).remove();
                }
                selection_state_update();
            });

            //全选
            $(".bh-group-manage-member-all").click(function(){
                $("table tbody tr td:first-child input[type=checkbox]").prop("checked", true);
                selection_state_update();
            })

            //全不选
            $(".bh-group-manage-member-none").click(function(){
                $("table tbody tr td:first-child input[type=checkbox]").prop("checked", false);
                selection_state_update();
            })

            //反向选择
            $(".bh-group-manage-member-anti").click(function(){
                $("table tbody tr td:first-child input[type=checkbox]").each(function(){
                    $(this).prop("checked", !$(this).prop("checked"));
                })
                selection_state_update();
            })

            /*
                gorup列表管理按钮
            */
            var group_count_limit = 10;
            $(".bh-group-add").click(function(){
                if ($(".bh-group-list-item").length >= group_count_limit) {
                    alert("列表数量已满");
                    return;
                }
                create_new_group({ name: "新建组" });
            });

            $(".bh-group-delete").click(function(){
               $(".bh-group-list-item.active").remove();
               $(".bh-group-member").addClass("hidden");
               $(".bh-group-manage-member").addClass("hidden");
            });

            /*
                创建分组
            */
            //初始化组列表（会清空原有数据）
            var create_new_group_list = function(data){
                $(".bh-group-list").find(".bh-group-list-item").remove();
                create_new_groups(data)
            }

            //一次性批量创建多个组（不会清空原有数据）
            var create_new_groups = function(data) {
                for (var i = 0; i < data.length; i++)
                    $(".bh-group-list").append(make_new_group(data[i]));
            }

            //创建新的组
            var create_new_group = function(settings) {
                $(".bh-group-list").append(make_new_group(settings));
            }

            //生成新的组
            var make_new_group = function(settings){
                var defaults = {
                    name: "",
                    data: []
                }
                settings = $.extend(defaults, settings);

                return $("<li>")
                    .data("group_data", settings.data)
                    .addClass("list-group-item bh-group-list-item slow-down clear-fix")
                    .append(
                        $("<b>")
                            .css("width", "80%")
                            .append(settings.name)
                            .dblclick(function(){
                                if ($(this).find("input").length > 0) return;
                                var value = $(this).text();
                                $(this).parent().find(".badge").addClass("hidden");
                                $(this).empty()
                                    .append(
                                        $("<div>")
                                            .addClass("input-group")
                                            .append(
                                                $("<input>")
                                                    .addClass("form-control")
                                                    .attr("placeholder", "未命名组")
                                                    .val(value)
                                            )
                                            .append(
                                                $("<span>")
                                                    .addClass("input-group-btn")
                                                    .append(
                                                        $("<button>")
                                                            .addClass("btn btn-success")
                                                            .append(
                                                                $("<span>").addClass("glyphicon glyphicon-ok")
                                                            )
                                                            .click(function(){  //组名修改
                                                                var my_item = $(this).parents(".list-group-item");
                                                                var new_name = $(my_item).find("input").val() || "未命名组";
                                                                $(my_item).find("b").empty().append(new_name);
                                                                $(my_item).find("span").removeClass("hidden");
                                                            })
                                                    )
                                            )

                                    )

                            })
                    ).append(
                        $("<span>")
                            .addClass("badge")
                            .append(settings.data.length)
                    ).click(function(){
                        if ($(this).hasClass("active")) return;
                        present_group = $(this);
                        $(".bh-group-list-item").removeClass("active");
                        $(this).addClass("active");
                        $(".bh-group-manage-member").removeClass("hidden");
                        $(".bh-group-member").removeClass("hidden");
                        create_new_table($(this).data("group_data"));
                    })
            }


            /*
                创建数据
             */
            //批量创建数据，用于加载表格（注意，会清除原先的数据），附带数据更新事件触发
            var create_new_table = function(data) {
                $("table tbody").empty();
                create_new_rows(data);
            }

            //直接创建新的多行数据，附带数据更新事件触发（注意：不会清空原有数据）
            var create_new_rows = function(data) {
                for (var i = 0; i < data.length; i++)
                    $("table tbody").append(make_new_row(data[i]));
                selection_state_update();
            }

            //直接创建新的一行数据，附带数据更新事件触发
            var create_new_row = function(settings) {
                $("table tbody").append(make_new_row(settings));
                selection_state_update();
            }

            //生成新的一行数据
            var make_new_row = function(settings){
                var defaults = {
                    checked: false,  //是否选中（默认不选中）
                    department: "",  //学院 / 部门
                    number: "",  //学号 / 工号
                    name: "",  //姓名
                    role: "",  //账户类型
                    data: null  //此栏为附加数据栏，不显示
                }

                settings = $.extend(defaults, settings);
                return $("<tr>")
                    .data("attached_data", settings.data)
                    .append(
                        $("<td>")
                            .append(
                                $("<input>").attr("type", "checkbox").prop("checked", !!settings.checked)
                            ).click(selection_state_update)
                    ).append(
                        $("<td>").append(settings.department)
                    ).append(
                        $("<td>").append(settings.number)
                    ).append(
                        $("<td>").append(settings.name)
                    ).append(
                        $("<td>").append(settings.role)
                    )
            }

            /*
                各类状态检测和维护
             */
            const 全部被选中 = 1;
            const 全部未选中 = -1;
            const 无可选项 = -2;
            const 部分选中 = 0;
            //检测当前的选择状态（用来决定顶部按钮的状况）
            var check_selection_state = function(){
                var boxes = $("table tbody tr td:first-child input[type=checkbox]");
                if (boxes.length === 0) return 无可选项;

                var checked_count = 0;
                for (var i = 0; i < boxes.length; i++) if (!!boxes.prop("checked")) checked_count++;
                var unchecked_count = boxes.length - checked_count;

                if (unchecked_count === 0) return 全部被选中; else
                if (checked_count === 0) return 全部未选中; else return 部分选中;
            }

            //enable和disable按钮
            $.fn.enable = function(){ $(this).removeClass("disabled"); }
            $.fn.disable = function(){ $(this).addClass("disabled"); }

            //当前的组
            var present_group = null;
            //选择状态更新
            var selection_state_update = function(){
                //顶部按钮
                var state = check_selection_state();
                var btn_add = $(".bh-group-manage-member-add");
                var btn_delete = $(".bh-group-manage-member-delete");
                var btn_all = $(".bh-group-manage-member-all");
                var btn_none = $(".bh-group-manage-member-none");
                var btn_anti = $(".bh-group-manage-member-anti");
                if (state === 无可选项) {  //空列表
                    $(btn_add).enable();
                    $(btn_delete).disable();
                    $(btn_all).disable();
                    $(btn_none).disable();
                    $(btn_anti).disable();
                } else if (state === 全部被选中) {  //全部被选中
                    $(btn_add).enable();
                    $(btn_delete).enable();
                    $(btn_all).disable();
                    $(btn_none).enable();
                    $(btn_anti).enable();
                } else if (state === 全部未选中) {  //全部未选中
                    $(btn_add).enable();
                    $(btn_delete).enable();
                    $(btn_all).enable();
                    $(btn_none).disable();
                    $(btn_anti).enable();
                } else if (state === 部分选中) {  //部分被选中
                    $(btn_add).enable();
                    $(btn_delete).enable();
                    $(btn_all).enable();
                    $(btn_none).enable();
                    $(btn_anti).enable();
                }

                //组元素个数显示
                if (!!present_group)
                    $(present_group)
                        .find(".badge")
                        .text($("table tbody tr td:first-child input[type=checkbox]").length);
            }


            /*
                页面初始化动作
             */

            create_new_group_list([
                {
                    name: "蛤蛤蛤",
                    data: [
                        {
                            checked: false,
                            name: "罗震宇",
                            number: "是电话费会计师",
                            department: "2134",
                            role: "北航巨佬"
                        },
                        {
                            checked: false,
                            name: "HansBug",
                            number: "苟利国家生死以",
                            department: "6",
                            role: "北航巨菜"
                        }
                    ]
                },
                {
                    name: "空列表",
                    data: []
                }
            ])
        });
    </script>
</head>
<body>
<div class="col-xs-3 list-group bh-group-list">
    <li class="list-group-item active bh-group-list-head">
        <h4 class="list-group-item-heading">
            我的分组（{{ $groups->count() }} / 10）
        </h4>
    </li>
</div>
<div class="col-xs-9">
    <div class = "bh-group-manage">
        <div class = "btn-group btn-group-sm bh-group-manage-group">
            <button class="btn btn-success bh-group-add">
                <span class = "glyphicon glyphicon-plus"></span>
                添加分组
            </button>
            <button class="btn btn-danger bh-group-delete">
                <span class = "glyphicon glyphicon-remove"></span>
                删除分组
            </button>
        </div>

        <div class = "btn-group btn-group-sm bh-group-manage-member hidden">
            <button class="btn btn-success bh-group-manage-member-add">
                <span class = "glyphicon glyphicon-plus"></span>
                添加成员
            </button>
            <button class="btn btn-warning bh-group-manage-member-delete">
                <span class = "glyphicon glyphicon-minus"></span>
                删除成员
            </button>
            <button class="btn btn-success bh-group-manage-member-all">
                全选
            </button>
            <button class="btn btn-info bh-group-manage-member-none">
                全不选
            </button>
            <button class="btn btn-primary bh-group-manage-member-anti">
                反选
            </button>
        </div>
    </div>

    <div class = "bh-group-member hidden">
        <table class = "table table-hover table-condensed">
            <thead>
                <th></th>
                <th>学院 / 部门</th>
                <th>学号 / 工号</th>
                <th>姓名</th>
                <th>账号类型</th>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>
</div>
</body>
</html>
