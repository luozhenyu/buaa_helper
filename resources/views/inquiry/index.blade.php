@extends('layouts.app')

@push("crumb")
<li><a href="{{ url("/") }}">主页</a></li>
<li class="active">留言管理</li>
@endpush

@push('cssLink')
<link rel="stylesheet" href="{{ url('/css/user_select.css') }}">
@endpush

@push('jsLink')
<script src="{{ url('/js/paginate.js') }}"></script>
<script src="{{ url('/js/user_select.js') }}"></script>
@endpush

@push('js')
<script>

    $(function () {
        var data = {!! json_encode($data) !!};
        console.log(data);
        $(".select-panel-main").user_select(data, function (value) {
            console.log(value);
        });
    });

</script>
@endpush

@section('content')
    {{--<h2>这个是留言面板qaq</h2>--}}
    {{--<h3 style="color:grey;">尚未完成，敬请期待</h3>--}}
    <div class="container">
        <div class="col-md-4">
            <div class="input-group">
                <input type="search" class="form-control" name="wd" placeholder="学号／工号／姓名">
                <span class="input-group-btn">
                    <button type="submit" class="btn btn-primary">
                        <span class="glyphicon glyphicon-search"></span> 搜索
                    </button>
                </span>
            </div>

            <div class="select-field">
                <h4 class="select-hit">(未选中对象)</h4>
            </div>

            <div style="padding-top: 6px;text-align: right;">
                <button class="btn btn-primary">
                    <span class="glyphicon glyphicon-filter"></span>筛选
                </button>
            </div>

            <div class="select-panel-main">
            </div>
        </div>
        <div class="col-md-8">1</div>
    </div>
@endsection
