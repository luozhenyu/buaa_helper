"use strict";


$.fn.user_select = function (options) {
    var defaults = {
        data: null,
        callback_change: function(data) { return null; },
        callback_filter: function(data) { return null; },
        department_relation_check: function(x, y) { return false; },
        property_relation_check: function(x, y) { return false; }
    }

    var settings = $.extend(defaults, options);
    var data = settings.data;
    var callback_change = settings.callback_change;
    var callback_filter = settings.callback_filter;
    var department_relation_check = settings.department_relation_check;
    var property_relation_check = settings.property_relation_check;


    function random() {
        return "" + Math.floor(Math.random() * 10000000);
    }

    function parsePanel(parentID, data, choice_array) {

        var displayName = data['display_name'],
            children = data['children'];
        var accordionID = parentID + "-" + random(),
            collapseID = "collapse-" + random();

        var sel = {};
        if (data['name']) {
            var spt = data['name'].split(",");
            spt[0] && (sel.department = parseInt(spt[0]));
            spt[1] && (sel.grade = parseInt(spt[1]));
        }

        switch (children) {
            case null:  //全体成员
                return $("<div>").addClass("panel panel-info").append(
                    $("<div>").addClass("click").addClass("panel-heading").append(
                        $("<h5>").addClass("panel-title").append(
                            $("<a>").text(displayName).css("text-decoration", "none")
                        )
                    ).click(function () {
                        var check = addFilter(choice_array.concat(displayName).join(" - "), sel, 0);
                        if (!(check === null)) {
                            $(this).tooltip({
                                trigger: "manual",
                                placement: "right",
                                title: "<h5 style = 'display: inline'><span>该限制存在冲突</span></h5>" +
                                "<h5><b style = 'color: orangered'>" + check.find("b").html() + "</b></h5>",
                                html: true
                            });
                            $(this).tooltip("show");
                        }
                    })
                );

            case undefined:  //按钮
                return $("<button>").addClass("list-group-item").addClass("slow_down")
                    .text(displayName).click(function () {
                        var check = addFilter(choice_array.concat(displayName).join(" - "), sel, 0);
                        if (!(check === null)) {
                            $(this).tooltip({
                                trigger: "manual",
                                placement: "right",
                                title: "<h5 style = 'display: inline'><span>该限制存在冲突</span></h5>" +
                                "<h5><b style = 'color: orangered'>" + check.find("b").html() + "</b></h5>",
                                html: true
                            });
                            $(this).tooltip("show");
                        }
                    });

            default:  //层级
                var panelHead = $("<div>").addClass("panel-heading").addClass("click").append(
                    $("<h5>").addClass("panel-title").text(displayName)
                ).attr("data-toggle", "collapse").attr("data-parent", "#" + parentID).attr("data-target", "#" + collapseID);
                var panel = $("<div>").addClass("panel panel-default").append(panelHead);

                var panelCollapse = $("<div>").attr("id", collapseID).addClass("panel-collapse collapse");
                //recursion
                var mainPanel = $("<div>").addClass("panel-group").css("margin-bottom", "2px").attr("id", accordionID);
                for (var i = 0; i < children.length; i++) {
                    mainPanel.append(parsePanel(accordionID, children[i], choice_array.concat([displayName])));
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

    // 增加新部门
    // display_name: 显示的名字
    // data: 数据
    // type: 0,department  1,property
    function addFilter(display_name, data, type) {
        var check = null;
        if (type === 0) {
            selectHit.find(".us-select.department").each(function(){
                if (department_relation_check($(this).data("data"), data)) {
                    check = check || $(this);
                }
            })

        } else {

        }

        if (!(check === null)) return check;

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


    //去除限制
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
        var panel = parsePanel(accordionID, departments[i], []);
        mainPanel.append(panel);
    }

    // 被选中内容部分
    var nobodyLabel = $("<h4 class = 'empty_label'>(无任何选中对象)</h4>").css("margin", "2px")
        .css("color", "darkgray").css("text-align", "center");

    var selectHit = $("<div>").css("min-height", "50px")
        .css("box-shadow", "inset 1px 1px 1px rgba(0, 0, 0, 0.1)").css("border-radius", "5px")
        .css("padding", "6px").css("background-color", "#dedede").append(nobodyLabel);


    // 筛选按钮部分
    var filterButton = $("<div></div>").css("padding-top", "6px").css("text-align", "right")
        .append(
            $("<button class = 'btn_query btn btn-primary'></button>")
                .append(" <span class = 'glyphicon glyphicon-filter'></span> 筛选 ")
                .css("margin-bottom", "6px")
        );

    var userSelect = $("<div>").append(selectHit).append(filterButton).append(mainPanel);

    $(this).empty().append(userSelect);
};