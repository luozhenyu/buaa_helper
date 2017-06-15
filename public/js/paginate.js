
"use strict";

$.fn.paginate = function () {

    function UrlWindow(paginator) {
        this.paginator = paginator;

        this.getStart = function () {
            return this.paginator.getUrlRange(1, 2);
        };

        this.getFinish = function () {
            return this.paginator.getUrlRange(this.lastPage() - 1, this.lastPage());
        };

        this.getSmallSlider = function () {
            return {
                'first': this.paginator.getUrlRange(1, this.lastPage()),
                'slider': null,
                'last': null
            };
        };

        this.hasPages = function () {
            return this.paginator.lastPage() > 1;
        };

        this.currentPage = function () {
            return this.paginator.currentPage();
        };

        this.lastPage = function () {
            return this.paginator.lastPage();
        };

        this.getSliderTooCloseToBeginning = function (window) {
            return {
                'first': this.paginator.getUrlRange(1, window + 2),
                'slider': null,
                'last': this.getFinish()
            };
        };

        this.getSliderTooCloseToEnding = function (window) {
            var last = this.paginator.getUrlRange(
                this.lastPage() - (window + 2),
                this.lastPage()
            );

            return {
                'first': this.getStart(),
                'slider': null,
                'last': last
            };
        };

        this.getFullSlider = function (onEachSide) {
            return {
                'first': this.getStart(),
                'slider': this.getAdjacentUrlRange(onEachSide),
                'last': this.getFinish()
            };
        };

        this.getAdjacentUrlRange = function (onEachSide) {
            return this.paginator.getUrlRange(
                this.currentPage() - onEachSide,
                this.currentPage() + onEachSide
            );
        };

        this.getUrlSlider = function (onEachSide) {
            var window = onEachSide * 2;

            if (!this.hasPages()) {
                return {'first': null, 'slider': null, 'last': null};
            }

            if (this.currentPage() <= window) {
                return this.getSliderTooCloseToBeginning(window);
            }

            if (this.currentPage() > (this.lastPage() - window)) {
                return this.getSliderTooCloseToEnding(window);
            }

            return this.getFullSlider(onEachSide);
        };

        this.get = function (onEachSide) {
            onEachSide = onEachSide || 3;
            if (this.paginator.lastPage() < (onEachSide * 2) + 6) {
                return this.getSmallSlider();
            }

            return this.getUrlSlider(onEachSide);
        };
    }

    UrlWindow.make = function (paginator, onEachSide) {
        onEachSide = onEachSide || 3;
        return new UrlWindow(paginator).get(onEachSide);
    };

    function Paginator(lastPage, currentPage, callback) {
        this._lastPage = lastPage;
        this._currentPage = currentPage;
        this.callback = callback;


        this.isValidPageNumber = function (page) {
            return typeof page === "number" && page >= 1;
        };

        this.setCurrentPage = function (currentPage) {
            return this.isValidPageNumber(currentPage) ? currentPage : 1;
        };

        this.url = function (page) {
            var self = this;
            return function () {
                self.callback(page);
            };
        };

        this.getUrlRange = function (start, end) {
            var map = {};
            for (var i = start; i <= end; i++) {
                map[i] = this.url(i);
            }
            return map;
        };

        this.currentPage = function () {
            return this._currentPage;
        };

        this.lastPage = function () {
            return this._lastPage;
        };

        this.onFirstPage = function () {
            return this.currentPage() <= 1;
        };

        this.hasMorePages = function () {
            return this.currentPage() < this.lastPage();
        };

        this.hasPages = function () {
            return this.currentPage() !== 1 || this.hasMorePages();
        };

        this.previousPageUrl = function previousPageUrl() {
            if (this.currentPage() > 1) {
                return this.url(this.currentPage() - 1);
            }
        };

        this.nextPageUrl = function () {
            if (this.hasMorePages()) {
                return this.url(this.currentPage() + 1);
            }
        };

        this.elements = function () {
            var window = UrlWindow.make(this);

            return [
                window['first'],
                window['slider'] ? '...' : null,
                window['slider'],
                window['last'] ? '...' : null,
                window['last']
            ];
        };
    }

    var paginator, defaultConfig = {
        currentPage: 1,
        lastPage: 0,
        callback: function (page) {
        }
    };

    this.init = function (conf) {
        var currentPage = conf.currentPage || defaultConfig.currentPage;
        var lastPage = conf.lastPage || defaultConfig.lastPage;
        var callback = conf.callback || defaultConfig.callback;

        var self = this;
        paginator = new Paginator(lastPage, currentPage, function (page) {
            self.paginate({
                currentPage: page,
                lastPage: lastPage,
                callback: callback
            });
            callback(page);
        });
    };

    this.createLinks = function (paginator) {
        if (paginator.hasPages()) {
            var ul = $("<ul>").addClass("pagination");

            //Previous Page Link
            var li = $("<li>");
            if (paginator.onFirstPage()) {
                li.addClass("disabled").append($("<span>").html("&laquo;"));
            } else {
                li.append($("<span>").attr("rel", "prev").html("&laquo;").click(paginator.previousPageUrl()));
            }
            ul.append(li);

            //Pagination Elements
            var elements = paginator.elements();
            for (var i = 0; i < elements.length; i++) {
                var element = elements[i];

                //"Three Dots" Separator
                if (typeof element === "string") {
                    ul.append($("<li>").addClass("disabled").append($("<span>").text(element)));
                }

                //Array Of Links
                if (typeof element === "object") {
                    for (var page in element) {
                        if (element.hasOwnProperty(page)) {
                            var url = element[page];
                            li = $("<li>");
                            if (parseInt(page) === paginator.currentPage()) {
                                li.addClass("active").append($("<span>").text(page));
                            } else {
                                li.append($("<span>").text(page).click(url));
                            }
                            ul.append(li);
                        }
                    }
                }
            }

            //Next Page Link
            li = $("<li>");
            if (paginator.hasMorePages()) {
                li.append($("<span>").attr("rel", "next").html("&raquo;").click(paginator.nextPageUrl()));
            } else {
                li.addClass("disabled").append($("<span>").html("&raquo;"));
            }
            ul.append(li);

            this.empty().append(ul);
        }
    };

    this.init(arguments[0] || {});
    this.createLinks(paginator);
};

