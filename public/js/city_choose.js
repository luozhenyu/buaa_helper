'use strict';

$.fn.childrenCities = function () {
    var params = arguments[0] || {};
    var self = $(this).empty();

    $.get("/city" + (params.val ? "/" + params.val + "/children" : ""), function (json) {
        if (json.length > 0) {
            $.each(json, function (index, elem) {
                self.append("<option value='" + elem["code"] + "'>" + elem["name"] + "</option>");
            });
            self.removeAttr("disabled");
        } else {
            self.attr("disabled", true);
        }
        self.selectpicker('refresh');
        params.callback && params.callback();
    });
    return $(this);
};

$.fn.clearCities = function () {
    return $(this).empty().attr("disabled", true)
        .selectpicker('refresh')
};

$.setCityChoose = function (province, city, area) {
    $(province).on('changed.bs.select', function () {
        $(city).childrenCities({
            val: $(this).val()
        });
        $(area).clearCities();
    });

    $(city).on('changed.bs.select', function () {
        $(area).childrenCities({
            val: $(this).val()
        });
    });
};

