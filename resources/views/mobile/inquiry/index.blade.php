@extends('layouts.mobile')

@section('title','我的问题')

@section('content')
    <nav class="navbar navbar-default" role="navigation">
        <div class="container-fluid">
            <a class="btn btn-default navbar-btn pull-right"
               href="{{ url()->current().'/create?access_token='.$access_token }}">
                <span class="glyphicon glyphicon-plus"></span>创建问题
            </a>
        </div>
    </nav>


    @foreach($auth_user->inquiries as $inquiry)
        <a href="{{ url()->current().'/'.$inquiry->id .'?access_token='.$access_token }}">
            <div class="panel panel-primary">
                <div class="panel-heading">
                    <h3 class="panel-title">{{ $inquiry->title }}</h3>
                </div>
                <div class="panel-body">
                    {{ $inquiry->content }}
                </div>
            </div>
        </a>
    @endforeach
@endsection
