'use strict';

$.fn.childrenCities = function () {
    var self = $(this);
    $.get(arguments[0] ? "/city/" + arguments[0] + "/children" : "/city", function (json) {
        self.empty();
        if (json.length > 0) {
            $.each(json, function (index, elem) {
                self.append("<option value='" + elem["code"] + "'>" + elem["name"] + "</option>");
            });
            self.removeAttr("disabled");
        } else {
            self.attr("disabled", true);
        }
        self.selectpicker('refresh');
    });
};

$.setCityChoose = function (id0, id1, id2) {
    $(id0).childrenCities();

    $(id0).on('changed.bs.select', function () {
        $(id1).childrenCities($(this).val());
        $(id2).empty().attr("disabled", true).selectpicker('refresh');
    });

    $(id1).on('changed.bs.select', function () {
        $(id2).childrenCities($(this).val());
    });
};

