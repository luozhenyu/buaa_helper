@extends('layouts.app_full')

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
