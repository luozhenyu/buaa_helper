@extends('layouts.app')

@push("crumb")
<li><a href="{{ url("/") }}">主页</a></li>
<li class="active">留言管理</li>
@endpush

@push('jsLink')
<!--<script src = "/js/filter.js"></script>-->
@endpush

@section('content')
    <h2>这个是留言面板qaq</h2>
    <h3 style="color:grey;">尚未完成，敬请期待</h3>
    <div id="page_test"></div>
    <div id = "filter_test"></div>
    <script src="{{ url('/js/paginate.js') }}"></script>
    <script src="{{ url('/js/filter.js') }}"></script>
    <script>
        $(function () {
            var c = { xx: "kldj"}.yy;
            console.log((c == null) || (c.length == 0));
            $("#page_test").paginate({
                currentPage: 1,
                lastPage: 20,
                callback: function (page) {
                    console.log(page);
                }
            });

            new Filter({
                ranges: [1,2]
            }).bind("#filter_test");
        });
    </script>
@endsection
