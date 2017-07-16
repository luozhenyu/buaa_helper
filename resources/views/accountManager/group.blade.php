<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="shortcut icon" href="{{ url('/favicon.ico') }}" type="image/x-icon"/>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name') }}</title>
    <link rel="stylesheet" href="{{ url('/components/bootstrap/dist/css/bootstrap.min.css') }}">

    <script src="{{ url('/components/jquery/dist/jquery.min.js') }}"></script>
    <script src="{{ url('/components/bootstrap/dist/js/bootstrap.min.js') }}"></script>
</head>
<body>
<div class="col-xs-4 list-group bh-group">
    <li class="list-group-item active">
        <h4 class="list-group-item-heading">
            我的分组（{{ $groups->count() }}/10）
        </h4>
    </li>
    @foreach($groups as $group)
        <a class="list-group-item" href = "http://www.baidu.com" target="_blank">
            {{ $group->name }}
            <span class="badge">{{ $group->users->count() }}</span>
        </a>
    @endforeach
</div>
<div class="col-md-8">
    <button class="btn btn-primary">
        <span class = "glyphicon glyphicon-user"></span>
        添加成员
    </button>
    <button class="btn btn-info">
        <span class = "glyphicon glyphicon-plus"></span>
        添加分组
    </button>
    <button class="btn btn-danger">
        <span class = "glyphicon glyphicon-remove"></span>
        删除分组
    </button>

</div>
</body>
</html>
