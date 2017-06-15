@extends('layouts.app')

@push("crumb")
<li><a href="{{ url("/") }}">主页</a></li>
<li class="active">留言管理</li>
@endpush

@push('jsLink')
<script src = "/js/filter.js"></script>
@endpush

@section('content')
    <h2>这个是留言面板qaq</h2>
    <h3 style = "color:grey;">尚未完成，敬请期待</h3>
    <div id = "page_test"></div>
    <div id = "filter_test" style = "width: 300px"></div>
    <script src = "/js/paginate.js"></script>
    <script>
        $(function(){
            $("#page_test").paginate({
                total: 100,
                at: 3,
                call_backs: px
            })

            $("#filter_test").CreateFilter({
                ranges: [1]
            });
        });

        function px(page) {

            $("#page_test").paginate({
                total: 100,
                at: page,
                call_backs: px
            });
        }
    </script>

@endsection
