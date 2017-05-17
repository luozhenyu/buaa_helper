@extends('layouts.mobile')

@section('title', $inquiry->title)

@section('content')
    <p>{{ $inquiry->title }}</p>
    <p>{{ $inquiry->created_at }}</p>
    <p>{{ $inquiry->content }}</p>

@endsection
