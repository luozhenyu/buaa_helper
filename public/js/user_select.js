"use strict";


$.fn.user_select = function (data, callback) {

    function random() {
        return "" + Math.floor(Math.random() * 10000000);
    }

    function parsePanel(parentID, data, callback) {

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
                        callback(sel);
                    })
                );

            case undefined:  //按钮
                return $("<button>").addClass("list-group-item")
                    .text(displayName).click(function () {
                        callback(sel);
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
                    mainPanel.append(parsePanel(accordionID, children[i], callback));
                }
                var panelBody = $("<div>").addClass("panel-body").css("padding", "2px").append(mainPanel);

                panelCollapse.append(panelBody);
                panel.append(panelCollapse);
                return panel;
        }
    }


    var departments = data['department'],
        properties = data['property'];

    var accordionID = "accordion-" + random();
    var mainPanel = $("<div>").addClass("panel-group").attr("id", accordionID);

    for (var i = 0; i < departments.length; i++) {
        var panel = parsePanel(accordionID, departments[i], callback);
        mainPanel.append(panel);
    }

    $(this).empty().append(mainPanel);
};