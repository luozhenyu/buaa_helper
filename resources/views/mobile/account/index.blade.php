@extends('layouts.mobile')

@section('title','个人信息')

@section('content')
    <p>学号：{{ $auth_user->number }}</p>
    <p>姓名：{{ $auth_user->name }}</p>
    <p>邮箱：{{ $auth_user->email or '无' }}</p>
    <p>手机号码：{{ $auth_user->phone or '无' }}</p>

    <a class="btn btn-primary btn-block" href="{{ url('/mobile/account/profile?access_token=' . $access_token) }}">
        修改信息
    </a>

    <a class="btn btn-primary btn-block" href="{{ url('/mobile/account/password?access_token=' . $access_token) }}">
        修改密码
    </a>
@endsection