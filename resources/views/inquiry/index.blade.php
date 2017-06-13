@extends('layouts.app')

@push("crumb")
<li><a href="{{ url("/") }}">主页</a></li>
<li class="active">留言管理</li>
@endpush

@section('content')
    <h2>这个是留言面板qaq</h2>
    <h3 style = "color:grey;">尚未完成，敬请期待</h3>
    <div id = "page_test"></div>
    <script src = "/js/paginate.js"></script>
    <script>
        $(function(){
            $("#page_test").paginate({
                total: 100,
                at: 3,
                call_backs: px
            })
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
