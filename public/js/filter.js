(function ($) {
    $.fn.CreateFilter = function (options) {
        var defaults = {
            key: "",
            ranges: [],
            limits: [],
            filter_trigger: function (data) {
                return null;
            }
        };

        var settings = $.extend(defaults, options);
        var key = "filter_" + settings.key;

        var ranges = settings.ranges;
        var limits = settings.limits;
        var filter_trigger = settings.filter_trigger;

        var acc_num = 0;
        var get_acc = function () {
            var acc = "acc_" + settings.key + "_";
            if (arguments.length > 0)
                return acc + arguments[0]
            else return acc + (acc_num++);
        }

        var col_num = 0;
        var get_col = function () {
            var col = "col_" + settings.key + "_";
            if (arguments.length > 0)
                return col + arguments[0]
            else return col + (col_num++);
        }

        $(this).empty();
        $(this).append($("<div id = '" + key + "'></div>"));
        var main_box = $(this).find("#" + key);

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
                        .append("<div class = 'panel-group' id = '" + get_acc() + "'></div>")
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

        var select_div = main_box.find(".select_div .panel-group");


        console.log(ranges.length)
        if (ranges.length > 0) {

            select_div.append(
                $("<div class = 'panel panel-default'></div>")
                    .append(
                        $("<div class = 'panel-heading click' data-toggle = 'collapse' data-parent = '#"
                            + get_acc(acc_num - 1) + "' href = '#range_select'></div>")
                            .append($("<h5 class = 'panel-title'>选定范围</h5>"))
                    )
                    .append(
                        $("<div id = 'range_select' class = 'panel-collapse collapse'></div>")
                    )
            )
            var range_select = $(this).find("#range_select");
            for (var i = 0; i < ranges.length; i++) {
                
            }
        }

        if (limits.length > 0) {

        }

    }
})(window.jQuery);