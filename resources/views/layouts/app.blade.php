@extends('layouts.app_full')

@push('css')
<style>
    ol.breadcrumb {
        background-color: #b4dcfc;
    }

    ol.breadcrumb li a {
        font-weight: bold;
        text-decoration: none;
    }

    ol.breadcrumb li.active {
        font-weight: bold;
    }

    .breadcrumb > li + li:before {
        color: #285e8e;
        content: "\";
    }

    #crumb {
        animation: fadeInto 0.25s ease;
    }
    #crumb:empty {
        display: none;
    }

    #main_content {
        max-width: 100%;
        background-color: white;
        padding: 15px 12px 15px 12px;

        animation: fadeInto 0.6s ease;
    }

    @keyframes fadeInto {
        from {
            opacity: 0;
        }
        to {
            opacity: 1;
        }
    }

</style>
@endpush

@section('content_full')
    <div class="container">
        <div class="row">
            <div class="col-md-10 col-md-offset-1 col-xs-12">
                <ol class="breadcrumb" id="crumb">@stack("crumb")</ol>
                <div id="main_content" class="jumbotron container">@yield('content')</div>
            </div>
        </div>
    </div>
@endsection
