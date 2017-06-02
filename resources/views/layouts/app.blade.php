@extends('layouts.app_full')

@push('css')
<style>
    #main_content {
        max-width: 100%;
        background-color: white;
        padding: 15px 12px 15px 12px;

        animation: fadeInto 0.6s;
    }

    #crumb {
        animation: fadeInto 0.3s;
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
    <!-- Content Part-->
    <div class="container">
        <div class="row">
            <div class="col-md-10 col-md-offset-1 col-xs-12">
                <ol class="breadcrumb" id="crumb">@stack("crumb")</ol>
                <div id="main_content" class="jumbotron container">@yield('content')</div>
            </div>
        </div>
    </div>
@endsection
