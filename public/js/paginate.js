(function ($) {
    $.fn.paginate = function (options) {
        var defaults = {
            total: 10,
            at: 1,
            left_length: 4,
            right_length: 4,
            mid_length: 3,
            page_generate: function (page) {
                return null;
            },
            call_backs: function (btn) {
                return null;
            }
        };
        var settings = $.extend(defaults, options)

        var btns = [];
        var call_backs = settings.call_backs;
        var make_page = settings.page_generate;
        var total = settings.total;
        var at = settings.at;
        var left_length = settings.left_length;
        var right_length = settings.right_length;
        var mid_length = settings.mid_length;


        //头部<<
        if (at == 1)
            btns.push("<li class = 'disabled'><span>&laquo;</span></li>");
        else {
            var lnk = make_page(at - 1);
            if (lnk == null) {
                btns.push("<li><span class = 'click slow_down' rel = 'prev' page = '" + (at - 1) + "'>&laquo;</span></li>");
            } else {
                btns.push("<li><a href = '" + lnk + "' rel = 'prev' page = '" + (at - 1) + "'>&laquo;</a></li>");
            }
        }


        //中部
        var last_state = true;
        for (i = 1; i <= total; i++) {
            if (i == at) {
                btns.push("<li class = 'active'><span>" + i + "</span></li>");
                last_state = true;
            } else if ((i <= left_length) || (i >= (total - right_length + 1)) || ((i >= (at - mid_length)) && (i <= (at + mid_length)))) {
                var lnk = make_page(i);
                if (lnk == null) {
                    btns.push("<li><span class = 'click slow_down' page = '" + (i) + "'>" + i + "</span></li>");
                } else {
                    btns.push("<li><a href = '" + make_page(i) + "' page = '" + (i) + "'>" + i + "</a></li>");
                }
                last_state = true;
            } else if (last_state) {
                btns.push("<li class = 'disabled'><span>...</span></li>");
                last_state = false;
            }
        }

        //尾部>>
        if (at == total)
            btns.push("<li class = 'disabled'><span>&raquo;</span></li>");
        else {
            var lnk = make_page(at + 1);
            if (lnk == null) {
                btns.push("<li><span class = 'click slow_down' rel = 'prev' page = '" + (at + 1) + "'>&raquo;</span></li>");
            } else {
                btns.push("<li><a href='" + make_page(at + 1) + "' rel = 'prev' page = '" + (at + 1) + "'>&raquo;</a></li>");
            }
        }


        //生成分页元素
        this.empty();
        if (total > 1) {
            this.append("<ul class = 'pagination'></ul>");
            var ul = this.find("ul");
            for (i = 0; i < btns.length; i++) ul.append(btns[i]);
        }
        this.find("ul > li > *").click(function () {
            if ($(this).parent().hasClass("disabled")) return;
            if ($(this).parent().hasClass("active")) return;
            var pg = parseInt($(this).attr("page"));
            call_backs(pg);
        })

        return this;
    }
})(window.jQuery);