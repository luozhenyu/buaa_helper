@extends('layouts.app')

@push("crumb")
<li class="active">主页</li>
@endpush

@section('content')
    <div class="panel panel-default">
        <div class="panel-heading">主页</div>

        <div class="panel-body">

            <div class="container">
                <div class="row">
                    <p>$\int_{0}^{2}{x dx} = 2$</p>
                </div>

            </div>

        </div>
    </div>
@endsection
