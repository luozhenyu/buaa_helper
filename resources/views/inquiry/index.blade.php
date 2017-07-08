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
        $(".select-panel-main").user_select({
            data: data,
            callback_change: function(data){
                console.log(data)
            },
            callback_filter: function(data){
                console.log(JSON.stringify(data));
            }


        });
    });

</script>
@endpush

@section('content')
    {{--<h2>这个是留言面板qaq</h2>--}}
    {{--<h3 style="color:grey;">尚未完成，敬请期待</h3>--}}
    <div class="container">
        <div class="col-md-6">

            <div style = "max-height: 500px;overflow: auto;">
                <div class="select-panel-main" style = "border: 1px solid black">
                </div>
            </div>

        </div>
    </div>
@endsection
