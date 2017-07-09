@extends('layouts.app')

@push("crumb")
<li><a href="{{ url("/") }}">主页</a></li>
<li class="active">留言管理</li>
@endpush

@push("css")
<style>
    a, button, .slow-down {
        -webkit-transition-duration: 0.45s;
        transition-duration: 0.45s;
    }

    .bh-inquiry-title {
        text-align: center;
        margin: 8px 0 13px 0;
        font-size: 30px;
        font-weight: bold;
    }

    .bh-inquiry-subtitle {
        text-align: center;
        margin: 4px 0 8px 0;
        font-size: 18px;
        color: gray;
    }

    .bh-inquiry-plate .panel-heading .panel-title {
        padding: 5px;
        font-size: 18px;
        font-weight: bold;
    }

    .bh-inquiry-plate .panel-body {
        width: 100%;
        padding: 4px;
    }

    .bh-inquiry-plate .panel-body > .bh-inquiry-block {
        display: inline-block;
        vertical-align: middle;
        border: 1px solid gray;
        border-radius: 5px;
        padding: 6px;
        white-space: nowrap;

        margin: 5px 0.2%;
        width: 24.1%;
    }

    .bh-inquiry-plate .panel-body > .bh-inquiry-block:hover {
        border: 1px solid #2277da;
        background-color: #eeeeee;
        cursor: pointer;
    }

    .bh-inquiry-plate .panel-body > .bh-inquiry-block > a {
        text-decoration: none;
        color: black;
    }

    @media (max-width: 1132px) {
        .bh-inquiry-plate .panel-body > .bh-inquiry-block {
            margin-right: 0.6%;
            margin-left: 0.6%;
            width: 31.5%;
        }
    }

    @media (max-width: 692px) {
        .bh-inquiry-plate .panel-body > .bh-inquiry-block {
            margin-right: 1%;
            margin-left: 1%;
            width: 47.5%;
        }
    }

    @media (max-width: 532px) {
        .bh-inquiry-plate .panel-body > .bh-inquiry-block {
            margin-right: 1.3%;
            margin-left: 1.3%;
            width: 97%;
        }
    }

    .bh-inquiry-plate .panel-body > .bh-inquiry-block .bh-inquiry-block-inner {
        margin-right: 45px;
    }

    .bh-inquiry-plate .panel-body > .bh-inquiry-block .bh-inquiry-block-icon,
    .bh-inquiry-plate .panel-body > .bh-inquiry-block .bh-inquiry-block-content {
        display: inline-block;
        vertical-align: middle;
    }

    .bh-inquiry-plate .panel-body > .bh-inquiry-block .bh-inquiry-block-icon > img {
        border-radius: 8px;
        width: 45px;
    }

    .bh-inquiry-plate .panel-body > .bh-inquiry-block .bh-inquiry-block-content > * {
        white-space: normal;
        margin-right: 7px;
        font-size: 18px;
    }
</style>
@endpush

@section('content')
    <h3 class="bh-inquiry-title">板块列表</h3>
    <h5 class="bh-inquiry-subtitle">请选择一个留言的板块
    </h5>
    <div class="panel panel-info bh-inquiry-plate">
        <div class="panel-heading">
            <h5 class="panel-title">学院</h5>
        </div>
        <div class="panel-body">
            @foreach(\App\Models\Department::where('number', '<', '100')->get() as $department)
                <div class="bh-inquiry-block slow-down">
                    <a href="{{ route('inquiry') . "/{$department->number}" }}">
                        <div class="bh-inquiry-block-inner">
                            <div class="bh-inquiry-block-icon">
                                <img src="{{ $department->avatar->url }}" alt="{{ $department->name }}">
                            </div>

                            <div class="bh-inquiry-block-content">
                                <h5>{{ $department->name }}</h5>
                            </div>

                            <div>
                                <span style="color: blue;">{{ $department->inquiries()->where('updated_at','>=',\Carbon\Carbon::today())->count() }}</span>
                                / {{ $department->inquiries->count() }}
                            </div>
                        </div>
                    </a>
                </div>
            @endforeach
        </div>
    </div>

    <div class="panel panel-info bh-inquiry-plate">
        <div class="panel-heading">
            <h5 class="panel-title">机关部处</h5>
        </div>
        <div class="panel-body">
            @foreach(\App\Models\Department::where('number', '>', '100')->get() as $department)
                <div class="bh-inquiry-block slow-down">
                    <a href="{{ route('inquiry') . "/{$department->number}" }}">
                        <div class="bh-inquiry-block-inner">
                            <div class="bh-inquiry-block-icon">
                                <img src="{{ $department->avatar->url }}" alt="{{ $department->name }}">
                            </div>
                            <div class="bh-inquiry-block-content">
                                <h5>{{ $department->name }}</h5>
                            </div>
                        </div>
                    </a>
                </div>
            @endforeach
        </div>
    </div>
@endsection
