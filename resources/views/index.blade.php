@extends('layouts.app')

@push("crumb")
<li class="active">主页</li>
@endpush

@section('content')
    <div style = "text-align: center;">
        <h2>欢迎使用 {{ config('app.name', 'Laravel') }}</h2>

    </div>


@endsection
