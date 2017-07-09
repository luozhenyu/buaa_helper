@extends('layouts.app')

@push('cssLink')
<link rel="stylesheet" href="{{ url('/components/bootstrap-select/dist/css/bootstrap-select.min.css') }}">
@endpush

@push('css')
<style>
    #avatarImg {
        width: 150px;
        height: 150px;
    }
</style>
@endpush

@push('jsLink')
<script src="{{ url('/components/bootstrap-select/dist/js/bootstrap-select.min.js') }}"></script>
<script src="{{ url('/components/bootstrap-select/dist/js/i18n/defaults-zh_CN.js') }}"></script>

<script src="{{ url('/js/file_upload.js') }}"></script>
<script src="{{ url('/js/city_choose.js') }}"></script>
@endpush

@php
    $nativePlace = old('native_place')?:[];
    while (!empty($nativePlace) && !end($nativePlace)) {
        array_pop($nativePlace);
    }
    $tree = ($place = \App\Models\City::where('code', end($nativePlace))->first())? $place->tree() :[];
@endphp

@push('js')
<script>
    $(function () {
        $("#department").selectpicker("val", "{{ old('department' )}}");
        $("#role").selectpicker("val", "{{ old('role')?:'normal' }}");

        $("#grade").selectpicker("val", "{{ old('grade') }}");
        $("#class").selectpicker("val", "{{ old('class') }}");
        $("#political_status").selectpicker("val", "{{ old('political_status') }}");

        {{-- native_place --}}
        @php($place = ['province', 'city', 'area'])
        @if(count($tree) > 0)
        @foreach($tree as $index => $node)
        @if($index > 0)
        $("#{{ $place[$index] }}").childrenCities({
            val: "{{ $tree[$index - 1]->code }}",
            callback: function () {
                $("#{{ $place[$index] }}").selectpicker("val", "{{ $tree[$index]->code }}");
            }
        });
        @else
        $("#{{ $place[$index] }}").childrenCities({
            callback: function () {
                $("#{{ $place[$index] }}").selectpicker("val", "{{ $tree[$index]->code }}");
            }
        });
        @endif
        @endforeach
        @if(count($tree) <= 2)
        $("#{{ $place[count($tree)] }}").childrenCities({
            val: "{{ $tree[count($tree) - 1]->code }}"
        });
        @endif
        @else
         $("#{{ $place[0] }}").childrenCities();
        @endif

        $("#financial_difficulty").selectpicker("val", "{{ old('financial_difficulty') }}");


        $("#avatarSelect").click(function () {
            $(this).upload({
                type: 'avatar',
                success: function (json) {
                    if (json.uploaded) {
                        $("#avatarImg").attr("src", json['url']);
                        $("#avatarInput").val(json['hash']);
                    } else {
                        alert(json.message);
                    }
                }
            });
        });
        $.setCityChoose('#province', '#city', '#area');
    });
</script>
@endpush

@push("crumb")
<li><a href="{{ url("/") }}">主页</a></li>
<li><a href="{{ url("/account_manager") }}">用户管理</a></li>
<li class="active">创建新用户</li>
@endpush

@section('content')
    <form class="form-horizontal" role="form" method="POST"
          action="{{ route('accountManager') }}">
        {{ csrf_field() }}

        <div class="form-group{{ $errors->has('avatar') ? ' has-error' : '' }}">
            <label for="avatar" class="col-md-4 control-label">用户头像</label>
            <div class="col-md-6">
                <img id="avatarImg" src="{{ (new \App\Models\User)->avatarUrl }}" class="img-thumbnail">
                <input id="avatarInput" type="hidden" name="avatar" value="{{ old('avatar') }}">
                <span id="avatarSelect" class="btn btn-default btn-xs">
                    选择图片 {{ \App\Http\Controllers\FileController::uploadLimitHit() }}
                </span>
                @if ($errors->has('avatar'))
                    <span class="help-block">
                <strong>{{ $errors->first('avatar') }}</strong>
            </span>
                @endif
            </div>
        </div>

        <div class="form-group{{ $errors->has('number') ? ' has-error' : '' }}">
            <label for="number" class="col-md-4 control-label">学号／工号</label>
            <div class="col-md-6">
                <input id="number" type="number" class="form-control" name="number"
                       value="{{ old('number') }}"
                       required autocomplete="off">
                @if ($errors->has('number'))
                    <span class="help-block">
                        <strong>{{ $errors->first('number') }}</strong>
                    </span>
                @endif
            </div>
        </div>

        <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
            <label for="name" class="col-md-4 control-label">姓名</label>
            <div class="col-md-6">
                <input id="name" type="text" class="form-control" name="name"
                       value="{{ old('name') }}" required autocomplete="off">
                @if ($errors->has('name'))
                    <span class="help-block">
                        <strong>{{ $errors->first('name') }}</strong>
                    </span>
                @endif
            </div>
        </div>

        <div class="form-group{{ $errors->has('department') ? ' has-error' : '' }}">
            <label for="department" class="col-md-4 control-label">院系</label>
            <div class="col-md-6">
                <select class="selectpicker form-control{{ $errors->has('department') ? ' has-error' : '' }}"
                        id="department" name="department" autocomplete="off" required
                        title="请选取用户所属的院系">
                    @foreach(\App\Models\Department::get() as $department)
                        <option value="{{ $department->id }}">{{ ($department->number<100?$department->number.'-':'').$department->name }}</option>
                    @endforeach
                </select>

                @if ($errors->has('department'))
                    <span class="help-block">
                        <strong>{{ $errors->first('department') }}</strong>
                    </span>
                @endif
            </div>
        </div>

        <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
            <label for="email" class="col-md-4 control-label">邮箱</label>

            <div class="col-md-6">
                <input id="email" type="email" class="form-control" name="email"
                       value="{{ old('email') }}" autocomplete="off">

                @if ($errors->has('email'))
                    <span class="help-block">
                        <strong>{{ $errors->first('email') }}</strong>
                    </span>
                @endif
            </div>
        </div>

        <div class="form-group{{ $errors->has('phone') ? ' has-error' : '' }}">
            <label for="phone" class="col-md-4 control-label">手机号</label>
            <div class="col-md-6">
                <input id="phone" type="text" class="form-control" name="phone"
                       value="{{ old('phone') }}" autocomplete="off">
                @if ($errors->has('phone'))
                    <span class="help-block">
                        <strong>{{ $errors->first('phone') }}</strong>
                    </span>
                @endif
            </div>
        </div>

        {{-- 年级 --}}
        @php($grade = \App\Models\Property::where('name','grade')->firstOrFail())
        <div class="form-group{{ $errors->has('grade') ? ' has-error' : '' }}">
            <label for="grade" class="col-md-4 control-label">{{ $grade->display_name }}</label>
            <div class="col-md-6">
                <select class="selectpicker form-control{{ $errors->has('grade') ? ' has-error' : '' }}"
                        id="grade" name="grade" title="请选择年级">
                    @foreach($grade->propertyValues as $value)
                        <option value="{{ $value->name }}">{{ $value->display_name }}</option>
                    @endforeach
                </select>
                @if ($errors->has('grade'))
                    <span class="help-block">
                <strong>{{ $errors->first('grade') }}</strong>
            </span>
                @endif
            </div>
        </div>

        {{-- 班级 --}}
        @php($class = \App\Models\Property::where('name','class')->firstOrFail())
        <div class="form-group{{ $errors->has('class') ? ' has-error' : '' }}">
            <label for="class" class="col-md-4 control-label">{{ $class->display_name }}</label>
            <div class="col-md-6">
                <select class="selectpicker form-control{{ $errors->has('class') ? ' has-error' : '' }}"
                        id="class" name="class" title="请选择班级">
                    @foreach($class->propertyValues as $value)
                        <option value="{{ $value->name }}">{{ $value->display_name }}</option>
                    @endforeach
                </select>
                @if ($errors->has('class'))
                    <span class="help-block">
                <strong>{{ $errors->first('class') }}</strong>
            </span>
                @endif
            </div>
        </div>

        {{-- 政治面貌 --}}
        @php($political_status = \App\Models\Property::where('name','political_status')->firstOrFail())
        <div class="form-group{{ $errors->has('political_status') ? ' has-error' : '' }}">
            <label for="political_status" class="col-md-4 control-label">{{ $political_status->display_name }}</label>
            <div class="col-md-6">
                <select class="selectpicker form-control{{ $errors->has('political_status') ? ' has-error' : '' }}"
                        id="political_status" name="political_status" title="请选择政治面貌">
                    @foreach($political_status->propertyValues as $value)
                        <option value="{{ $value->name }}">{{ $value->display_name }}</option>
                    @endforeach
                </select>
                @if ($errors->has('political_status'))
                    <span class="help-block">
                <strong>{{ $errors->first('political_status') }}</strong>
            </span>
                @endif
            </div>
        </div>

        {{-- 籍贯 --}}
        @php($native_place = \App\Models\Property::where('name','native_place')->firstOrFail())
        <div class="form-group{{ $errors->has('native_place.*') ? ' has-error' : '' }}">
            <label for="native_place" class="col-md-4 control-label">{{ $native_place->display_name }}</label>
            <div class="container" id="native_place_container">
                <div class="col-md-6{{ $errors->has('native_place') ? ' has-error' : '' }}">
                    <select class="selectpicker" id="province" name="native_place[0]" title="请选择省份"
                            data-width="32%" disabled autocomplete="off">
                    </select>
                    <select class="selectpicker" id="city" name="native_place[1]" title="请选择城市"
                            data-width="32%" disabled autocomplete="off">
                    </select>
                    <select class="selectpicker" id="area" name="native_place[2]" title="请选择地区"
                            data-width="32%" disabled autocomplete="off">
                    </select>
                </div>

                @if ($errors->has('native_place.*'))
                    <span class="help-block">
                <strong>{{ $errors->first('native_place.*') }}</strong>
            </span>
                @endif
            </div>
        </div>

        {{-- 经济困难 --}}
        @php($financial_difficulty = \App\Models\Property::where('name','financial_difficulty')->firstOrFail())
        <div class="form-group{{ $errors->has('financial_difficulty') ? ' has-error' : '' }}">
            <label for="financial_difficulty"
                   class="col-md-4 control-label">{{ $financial_difficulty->display_name }}</label>
            <div class="col-md-6">
                <select class="selectpicker form-control{{ $errors->has('financial_difficulty') ? ' has-error' : '' }}"
                        id="financial_difficulty" name="financial_difficulty" title="是否经济困难">
                    @foreach($financial_difficulty->propertyValues as $value)
                        <option value="{{ $value->name }}">{{ $value->display_name }}</option>
                    @endforeach
                </select>
                @if ($errors->has('financial_difficulty'))
                    <span class="help-block">
                <strong>{{ $errors->first('financial_difficulty') }}</strong>
            </span>
                @endif
            </div>
        </div>

        @role('admin')
        <div class="form-group{{ $errors->has('role') ? ' has-error' : '' }}">
            <label for="role" class="col-md-4 control-label">账号类型</label>
            <div class="col-md-6">
                <select class="selectpicker form-control{{ $errors->has('role') ? ' has-error' : '' }}"
                        id="role" name="role">
                    <option value="{{ $role->name }}">{{ $role->display_name }}</option>
                </select>
                @if ($errors->has('role'))
                    <span class="help-block">
                        <strong>{{ $errors->first('role') }}</strong>
                    </span>
                @endif
            </div>
        </div>
        @endrole

        <div class="form-group">
            <div class="col-md-8 col-md-offset-4">
                <button type="submit" class="btn btn-primary">
                    <span class="glyphicon glyphicon-ok"></span> 保存信息
                </button>
            </div>
        </div>
    </form>
@endsection
