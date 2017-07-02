"use strict";


$.fn.user_select = function (options) {
    //默认设置
    var defaults = {
        data: null,
        callback_change: function(data) { return null; },
        callback_filter: function(data) { return null; },
        department_relation_check: function(parent, child) {
            //全体成员
            if (parent.department === -1) return true;
            if (child.department === -1) return false;

            //全部部门
            if ((parent.department === 100) && (child.department >= 100)) return true;
            if ((child.department === 100) && (parent.department >= 100)) return false;

            //全体学生
            if ((parent.department === 0) && (parent.grade === undefined) && (child.department < 100)) return true;
            if ((child.department === 0) && (child.grade === undefined) && (parent.department < 100)) return false;

            //整个年级
            if ((parent.grade === child.grade) && (parent.department === 0)) return true;
            if ((parent.grade === child.grade) && (child.department === 0)) return false;

            //相同元素
            if ((parent.department === child.department) && (parent.grade === child.grade)) return true;

            return false;
        },
        property_relation_check: function(x, y) { return false; },
        conflict_template: function(conflict_name, conflict_data){
            return $("<div>").append(
                $("<h5>").text("该限制存在冲突")
            ).append(
                $("<h5>").text(conflict_name).css("font-weight", "bold").css("color", "orangered")
            ).html();
        }
    }

    var settings = $.extend(defaults, options);
    var data = settings.data;  // 数据集
    var callback_change = settings.callback_change;  // 选择项发生改变时的回调函数
    var callback_filter = settings.callback_filter;  // 点击筛选时的回调函数
    var department_relation_check = settings.department_relation_check;  // 范围选择部分的关系判定
    var property_relation_check = settings.property_relation_check;  // 属性选择部分的关系判定
    var conflict_template = settings.conflict_template;  // 冲突提示的生成模板


    //生成随机数，作为accordationID
    function random() {
        return "" + Math.floor(Math.random() * 10000000);
    }

    // 生成树结构
    // parentID: 父节点的accordionID
    // data: 该层次节点的数据
    // choice_array: 各层次父亲节点（用于生成标签文字）
    // mode: 生成模式 0,department 1,property
    function parsePanel(parentID, data, choice_array, mode) {

        var displayName = data['display_name'],
            children = data['children'];
        var accordionID = parentID + "-" + random(),
            collapseID = "collapse-" + random();
        var sel = {};

        if (data['name']) {
            var spt = data['name'].split(",");
            if (mode === 0) {
                spt[0] && (sel.department = parseInt(spt[0]));
                spt[1] && (sel.grade = parseInt(spt[1]));
            } else if (mode === 1) {
                spt[0] && (sel[""] = parseInt(spt[0]));
            }
        }

        console.log("xxx");
        switch (children) {
            case null:  //全体成员
                return $("<div>").addClass("panel panel-info").append(
                    $("<div>").addClass("click").addClass("panel-heading").append(
                        $("<h5>").addClass("panel-title").append(
                            $("<a>").text(displayName).css("text-decoration", "none")
                        )
                    ).click(function () {
                        buttonTriggered({
                            display_name: choice_array.concat(displayName).join(" - "),
                            data: sel,
                            mode: mode,
                            element: $(this)
                        });
                    }).mouseleave(function(){
                        var btn = $(this);
                        setTimeout(function () {
                            btn.tooltip("hide");
                            btn.tooltip("destroy");
                        }, 150);
                    })
                );

            case undefined:  //按钮
                return $("<button>").addClass("list-group-item").addClass("slow_down")
                    .text(displayName).click(function () {
                        buttonTriggered({
                            display_name: choice_array.concat(displayName).join(" - "),
                            data: sel,
                            mode: mode,
                            element: $(this)
                        });
                    }).mouseleave(function(){
                        var btn = $(this);
                        setTimeout(function () {
                            btn.tooltip("hide");
                            btn.tooltip("destroy");
                        }, 150);
                    });

            default:  //层级

                var panelHead = $("<div>").addClass("panel-heading").addClass("click").append(
                    $("<h5>").addClass("panel-title").text(displayName)
                ).attr("data-toggle", "collapse").attr("data-parent", "#" + parentID).attr("data-target", "#" + collapseID);
                var panel = $("<div>").addClass("panel").addClass((mode === 0) ? "panel-default" : "panel-primary")
                    .append(panelHead);


                var panelCollapse = $("<div>").attr("id", collapseID).addClass("panel-collapse collapse");
                //recursion
                var mainPanel = $("<div>").addClass("panel-group").css("margin-bottom", "2px").attr("id", accordionID);
                for (var i = 0; i < children.length; i++) {

                    mainPanel.append(parsePanel(accordionID, children[i], choice_array.concat([displayName]), mode));
                }
                var panelBody = $("<div>").addClass("panel-body").css("padding", "2px").append(mainPanel);


                panelCollapse.append(panelBody);
                panel.append(panelCollapse);
                return panel;
        }
    }

    //获取全部条件信息
    function getData() {
        var departments = [];
        selectHit.find(".us-select.department").each(function(){
            departments.push($(this).data("data"));
        });

        var properties = [];
        selectHit.find(".us-select.property").each(function(){
            properties.push($(this).data("data"));
        })

        return { departments: departments, properties: properties };
    }

    //按钮按下响应事件
    function buttonTriggered(data) {
        var check = addFilter(data.display_name, data.data, data.mode);

        // check: 检测到的从属于的已选元素，如果不存在返回null
        if (!(check === null)) {
            $(data.element).tooltip({
                trigger: "manual",
                //placement: "right",
                title: conflict_template(check.find("b").html(), check.data("data")),
                html: true
            });
            $(data.element).tooltip("show");
        }
    }

    // 增加新部门
    // display_name: 显示的名字
    // data: 数据
    // type: 0,department  1,property
    function addFilter(display_name, data, type) {
        var check = null, select_el, check_method;
        if (type === 0) {
            select_el = ".us-select.department";
            check_method = department_relation_check;
        } else {
            select_el = ".us-select.property";
            check_method = property_relation_check;
        }

        //检测是否已被包含
        selectHit.find(select_el).each(function(){
            if (check_method($(this).data("data"), data)) check = check || $(this);
        });
        //如果被包含则退出
        if (!(check === null)) return check;
        //清空所有包含的已选元素
        selectHit.find(select_el).each(function(){
            if (check_method(data, $(this).data("data"))) $(this).remove();
        });

        console.log(type);
        var filter_element = $("<div>").addClass("us-select").addClass((type === 0) ? "department" : "property")
            .append(
                $("<b>").text(display_name).css("color", (type === 0) ? "black" : "darkblue")
            )
            .append(
                $("<span class = 'glyphicon glyphicon-remove click'></span>")
                    .click(function(){
                        removeFilter(this);
                    }).css("color", "red")
            ).css("border-radius", "6px").css("padding", "2px 4px").css("display", "inline-block")
            .css("margin-right", "5px").css("margin-bottom", "3px")
            .css("border", "1px solid lightgray").css("background-color", "#f8f8f8")
            .data("data", data);

        selectHit.append(filter_element);
        nobodyStateCheck();
        callback_change(getData());
        return null;
    }


    //去除条件
    // cross: 该条件右侧红色的叉，据此定位元素
    function removeFilter(cross) {
        $(cross).parents(".us-select").remove();
        nobodyStateCheck();
        callback_change(getData());
    }

    //用于检测是否显示无选择项提示
    function nobodyStateCheck() {
        var department_cnt = $(".us-select.department").length;
        var property_cnt = $(".us-select.property").length;
        var total_cnt = department_cnt + property_cnt;
        if (total_cnt === 0)
            nobodyLabel.show();
        else nobodyLabel.hide();
    }


    var departments = data['department'],
        properties = data['property'];


    // 选择部分
    var accordionID = "accordion-" + random();
    var mainPanel = $("<div>").addClass("panel-group").attr("id", accordionID).css("margin-bottom", "0px");

    for (var i = 0; i < departments.length; i++) {
        var panel = parsePanel(accordionID, departments[i], [], 0);
        mainPanel.append(panel);
    }

    /*console.log("yyy");
    for (var i = 0; i < properties.length; i++) {
        console.log(properties[i]);
        var panel = parsePanel(accordionID, properties[i], [], 1);
        mainPanel.append(panel);
    }*/

    // 被选中内容部分
    // 无选中文字显示
    var nobodyLabel = $("<h4 class = 'empty_label'>(无任何选中对象)</h4>").css("margin", "2px")
        .css("color", "darkgray").css("text-align", "center");

    // 显示框
    var selectHit = $("<div>").css("min-height", "50px")
        .css("box-shadow", "inset 1px 1px 1px rgba(0, 0, 0, 0.1)").css("border-radius", "5px")
        .css("padding", "6px").css("background-color", "#dedede").append(nobodyLabel);


    // 筛选按钮部分
    var filterButton = $("<div></div>").css("padding-top", "6px").css("text-align", "right")
        .append(
            $("<button class = 'btn_query btn btn-primary'></button>")
                .append(" <span class = 'glyphicon glyphicon-filter'></span> 筛选 ")
                .css("margin-bottom", "6px")
        ).click(function(){
            callback_filter(getData());
        });

    // 整个组件
    var userSelect = $("<div>").append(selectHit).append(filterButton).append(mainPanel);

    $(this).empty().append(userSelect);
};