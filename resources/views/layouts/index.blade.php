@extends('layouts.app_full')

@push("css")
<style>
    /* 背景图 */
    @media (min-width: 1200px) {
        #back_div {
            background: url({{ url('/img/bk2.jpg') }}) -80px -280px;
            background-size: 2450px;
        }

        #main_div, #content_div {
            min-height: 580px;
        }
    }

    @media (min-width: 992px) and (max-width: 1199px) {
        #back_div {
            background: url({{ url('/img/bk2.jpg') }}) -80px -280px;
            background-size: 1750px;
        }

        #main_div, #content_div {
            min-height: 550px;
        }
    }

    @media (max-width: 991px) and (min-width: 768px) {
        #back_div {
            background: url({{ url('/img/bk2.jpg') }}) -600px -200px;
            background-size: 1600px;
        }

        #main_div, #content_div {
            min-height: 470px;
        }
    }

    @media (max-width: 767px) {
        #back_div {
            background: url("{{ url('/img/bk2.jpg') }}") -800px -200px;
            background-size: 1600px;
        }

        #main_div, #content_div {
            min-height: 430px;
        }
    }

    @media (max-width: 767px) {
        #back_div {
            background: url("{{ url('/img/bk2.jpg') }}") -800px -200px;
            background-size: 1600px;
        }

        #main_div, #content_div {
            min-height: 380px;
        }
    }

    @media (max-width: 365px) {
        #main_div, #content_div {
            min-height: 440px;
        }
    }

    #back_div {
        filter: alpha(opacity=1);
        -moz-opacity: 0.01;
        opacity: 0.01;
    }

    #main_div {
        margin-bottom: 15px;
        height: 80%;
    }

    /* 背景区，内容区 */
    #back_div, #content_div, .background_div, .content_div {
        position: absolute;
        left: 0;
        top: 0;
        right: 0;
        bottom: 0;
        height: 100%
    }

    #content_div {
        width: 100%;
    }

</style>
@stack("css_2")
@endpush


@push("js")
<script>

    function no_px(st) {
        return parseInt(st.substr(0, st.length - 2));
    }
    $(function () {
        main_height_adjust();
        $(window).resize(function () { main_height_adjust(); })
    });
    function main_height_adjust() {
        var main_bottom = $(window).height() - no_px($("footer").css("height")) - no_px($("#main_div").css("margin-bottom"));
        var main_top = $("#main_div").offset().top;
        var main_height = main_bottom - main_top;
        console.log(main_height);
        $("#main_div").css("height", (main_height) + "px");
    }

</script>
@stack("js_2")
@endpush


@section("content_full")
    <div id="main_div" style="position:relative">
        <div id="back_div"></div>
        <table id="content_div">
            <tr>
                @yield("content_full_2")
            </tr>
        </table>

    </div>
@endsection