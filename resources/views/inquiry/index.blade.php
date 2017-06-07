@extends('layouts.app')

@push("crumb")
<li><a href="{{ url("/") }}">主页</a></li>
<li class="active">留言管理</li>
@endpush

@section('content')

    <h2>这个是留言面板qaq</h2>
    <h3 style = "color:grey;">尚未完成，敬请期待</h3>
@endsection
