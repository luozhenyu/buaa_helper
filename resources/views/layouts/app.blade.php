@extends('layouts.app_full')

@push('css')
<style>
    #main_content {
        max-width: 100%;
        background-color: white;
        padding-left: 12px;
        padding-right: 12px;
        padding-top: 15px;
        padding-bottom: 15px;
    }

    #main_content:empty, #crumb:empty {
        display: none;
    }

    .alpha_hide {
        filter: alpha(opacity=1);
        -moz-opacity: 0.01;
        opacity: 0.01;
    }
</style>
@endpush

@push('js')
<script>
    $(function () {
        var ce = $("#crumb").is(":empty");
        var me = $("#main_content").is(":empty");

        if (!ce) {
            $("#crumb").fadeTo(300, 1);
            $("#crumb").removeClass("alpha_hide");
            setTimeout(function () {
                if (!me) {
                    $("#main_content").fadeTo(200, 1);
                    $("#main_content").removeClass("alpha_hide");
                }
            }, 100)
        } else {
            if (!me) {
                $("#main_content").fadeTo(200, 1);
                $("#main_content").removeClass("alpha_hide");
            }
        }
    });
</script>
@endpush

@section('content_full')
    <!-- Content Part-->
    <div class="container">
        <div class="row">
            <div class="col-md-10 col-md-offset-1 col-xs-12">
                <ol class="breadcrumb alpha_hide" id="crumb">@stack("crumb")</ol>
                <div id="main_content" class="jumbotron container alpha_hide">@yield('content')</div>
            </div>
        </div>
    </div>
@endsection
