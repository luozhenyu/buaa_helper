function Filter(options) {
    this.key = "";
    this.ranges = [];
    this.limits = [];

    var defaults = {
        key: "",
        ranges: [],
        limits: [],
        callbacks: {
            filter_trigger: function (data) {
                return null;
            }
        }
    }
    this.settings = $.extend(defaults, options);

    this.bind = function (filter, options) {
        var settings = $.extend(defaults, options);
        var key = "filter_" + settings.key;

        var ranges = settings.ranges;
        var limits = settings.limits;
        var filter_trigger = settings.callbacks.filter_trigger;
        var box = $(filter);


        box.empty();
        box.append($("<div id = '" + key + "'></div>"));
        var main_box = box.find("#" + key);

        main_box.append(
            $("<div></div>")
                .append(
                    $("<div class = 'selected_container'></div>").css("margin-top", "12px")
                        .css("box-shadow", "inset 1px 1px 1px rgba(0, 0, 0, 0.1)").css("border-radius", "5px")
                        .css("padding", "6px").css("background-color", "#dedede")
                        .append(
                            $("<h4 class = 'empty_label'>(无任何选中对象)</h4>").css("margin", "2px")
                                .css("color", "darkgray").css("text-align", "center")
                        )
                )
                .append(
                    $("<div></div>").css("padding-top", "6px").css("text-align", "right")
                        .append(
                            $("<button class = 'btn_query btn btn-primary'></button>")
                                .append(" <span class = 'glyphicon glyphicon-filter'></span> 筛选 ")
                        )
                )
                .append(
                    $("<div class = 'select_div'></div>")
                )
        );

        var check_selected = function (element) {
            var emp = $(element).find(".empty_label");
            if ($(element).find(".selected_element").length > 0) {
                if (!emp.hasClass("hidden")) emp.addClass("hidden");
            } else {
                emp.removeClass("hidden");
            }
        }

        var select_div = main_box.find(".select_div").css("padding-top", "8px");


        console.log(ranges.length)
        if (ranges.length > 0) {

            select_div.append(
                $("<div class = 'panel panel-default'></div>")
                    .append(
                        $("<div class = 'panel-heading click' data-toggle = 'collapse' href = '#range_select'></div>")
                            .append($("<h5 class = 'panel-title'>选定范围</h5>"))
                    )
                    .append(
                        $("<div id = 'range_select' class = 'panel-collapse collapse'></div>")
                    )
            )
            var range_select = box.find("#range_select");
            var ss = $("<ul class = 'list-group'></ul>")
            for (var i = 0; i < ranges.length; i++) {

                ss.append("<li class = 'list-group-item slow_down click'>i</li>");
            }
            range_select.append(ss);


            var make_list = function (data, pre, dp) {
                var lst = $("<ul class = 'list-group'></ul>");
                if (data.allow_all) {
                    lst.append(
                        $("<li class = 'list-group-item slow_down click base range' " +
                            "value = '" + pre.join(",") + "' name = '" + dp + " - " + data.all_name + "'></li>")
                            .append($("<b></b>").append(data.all_name))
                    )
                }
                for (var i = 0;i < data.list.length;i++) {
                    var ele = data.list[i];
                    var p = pre; p.push(ele.value);
                    var d = dp + " - " + ele.display_name;
                    var div =  $("<li class = 'list-group-item slow_down click'></li>")

                    if ((ele.child == null) || (ele.child.length == 0)) {
                        div = div.addClass("base").addClass("range").attr("value", p).attr("name", d);
                    } else {
                        div.append(
                            $()
                        )
                    }
                    lst.append(div)
                }
            }
        }

        if (limits.length > 0) {

        }
    }

    this.getResult = function () {

    }
}
