@extends('layouts.app')

@section('content')
    <h1>{{ $errmsg }}</h1>
    @if(isset($redirect))
        即将跳转<a href="{{ $redirect }}">{{ $redirect }}</a>
    @endif
@endsection