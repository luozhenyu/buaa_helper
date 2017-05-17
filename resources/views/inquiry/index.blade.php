@extends('layouts.app')

@push("crumb")
<li><a href="{{ url("/") }}">主页</a></li>
<li class="active">留言管理</li>
@endpush

@section('content')
    <div class="panel panel-default">
        <div class="panel-heading">留言管理</div>

        <div class="panel-body">
            主页
        </div>
    </div>
@endsection
