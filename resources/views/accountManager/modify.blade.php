@extends('layouts.app')

@push('css')
<style>
    @media (max-width: 991px) {
        #native_place_container > .col-xs-4.col-md-2:nth-of-type(1) {
            padding-left: 0px;
        }
    }

    #native_place_container > .col-xs-4.col-md-2 {
        padding-right: 0px;
    }
</style>
@endpush

@push('cssLink')
<link rel="stylesheet" href="{{ url('/components/bootstrap-select/dist/css/bootstrap-select.min.css') }}">
@endpush

@push('jsLink')
<script src="{{ url('/components/bootstrap-select/dist/js/bootstrap-select.min.js') }}"></script>
<script src="{{ url('/components/bootstrap-select/dist/js/i18n/defaults-zh_CN.js') }}"></script>
@endpush

@push('js')
<script>
    $(function () {
        $("#department").selectpicker("val", "{{ $user->department_id }}");
        $("#role").selectpicker("val", "{{ $user->roles->first()->name }}");

        $("#grade").selectpicker("val", "{{ ($grade = $user->propertyValue('grade'))? $grade->name :null }}");
        $("#class").selectpicker("val", "{{ ($class = $user->propertyValue('class'))? $class->name :null }}");
        $("#political_status").selectpicker("val", "{{ ($political_status = $user->propertyValue('political_status'))? $political_status->name :null }}");
        $("#native_place").selectpicker("val", "{{ ($native_place = $user->propertyValue('native_place'))? $native_place->name :null }}");
        $("#financial_difficulty").selectpicker("val", "{{ ($financial_difficulty = $user->propertyValue('financial_difficulty'))? $financial_difficulty->name :null }}");

        @permission('delete_user')
        $("#btn_del").click(function () {
            if (confirm('你真的要删除此账号吗？')) {
                $.ajax({
                    url: "{{ route('accountManager').'/'.$user->id }}",
                    type: "DELETE",
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    error: function (event) {
                        alert("您没有权限访问！");
                    },
                    success: function (data) {
                        alert(data);
                        window.close();
                    }
                });
            }
        });
        @endpermission
    });
</script>
@endpush

@push("crumb")
<li><a href="{{ url("/") }}">主页</a></li>
<li><a href="{{ url("/account_manager") }}">用户管理</a></li>
<li class="active">用户信息修改</li>
@endpush

@section('content')
    <form class="form-horizontal" role="form" method="POST" action="{{ route('accountManager').'/'.$user->id }}">
        <input type="hidden" name="_method" value="PUT"/>
        {{ csrf_field() }}

        <div class="form-group">
            <label for="number" class="col-md-4 control-label">学号／工号</label>
            <div class="col-md-6">
                <input id="number" type="text" class="form-control" value="{{ $user->number }}" disabled>
            </div>
        </div>

        <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
            <label for="name" class="col-md-4 control-label">姓名</label>
            <div class="col-md-6">
                <input id="name" type="text" class="form-control" name="name"
                       value="{{ $user->name }}" required autocomplete="off">
                @if ($errors->has('name'))
                    <span class="help-block">
                        <strong>{{ $errors->first('name') }}</strong>
                    </span>
                @endif
            </div>
        </div>

        <div class="form-group{{ $errors->has('department') ? ' has-error' : '' }}">
            <label for="department" class="col-md-4 control-label">院系或部门</label>
            <div class="col-md-6">
                @permission('modify_all_user')
                <select class="selectpicker form-control{{ $errors->has('department') ? ' has-error' : '' }}"
                        id="department" name="department">
                    @foreach(\App\Models\Department::get() as $department)
                        <option value="{{ $department->id }}">{{ ($department->number<100?$department->number.'-':'').$department->name }}</option>
                    @endforeach
                </select>
                @endpermission
                @permission('modify_owned_user')
                <select class="selectpicker form-control{{ $errors->has('department') ? ' has-error' : '' }}"
                        id="department" name="department" disabled>
                    @php($department = Auth::user()->department)
                    <option value="{{ $department->id }}">{{ ($department->number<100?$department->number.'-':'').$department->name }}</option>
                </select>
                @endpermission

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
                       value="{{ $user->email }}" autocomplete="off">

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
                       value="{{ $user->phone }}" autocomplete="off">
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
                        id="grade" name="grade">
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
                        id="class" name="class">
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
                        id="political_status" name="political_status">
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
        <div class="form-group{{ $errors->has('native_place') ? ' has-error' : '' }}">
            <label for="native_place" class="col-md-4 control-label">{{ $native_place->display_name }}</label>
            <div class="container" id = "native_place_container">
                @php($cities = \App\Models\City::doesntHave('parent')->get())

                <div class="col-md-2 col-xs-4">
                    <select class="selectpicker form-control{{ $errors->has('native_place') ? ' has-error' : '' }}"
                            id="native_place" name="native_place">
                        @foreach($cities as $value)
                            <option value="{{ $value->code }}">{{ $value->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2 col-xs-4">
                    <select class="selectpicker form-control{{ $errors->has('native_place') ? ' has-error' : '' }}"
                            id="native_place" name="native_place">
                        @foreach($cities as $value)
                            <option value="{{ $value->code }}">{{ $value->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2 col-xs-4">
                    <select class="selectpicker form-control{{ $errors->has('native_place') ? ' has-error' : '' }}"
                            id="native_place" name="native_place">
                        @foreach(\App\Models\City::doesntHave('parent')->get() as $value)
                            <option value="{{ $value->code }}">{{ $value->name }}</option>
                        @endforeach
                    </select>
                </div>

                @if ($errors->has('native_place'))
                    <span class="help-block">
                        <strong>{{ $errors->first('native_place') }}</strong>
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
                        id="financial_difficulty" name="financial_difficulty">
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
            <label for="role" class="col-md-4 control-label">
                <span class="glyphicon glyphicon-warning-sign" style="color: orange"></span>
                账号类型
            </label>
            <div class="col-md-6">
                <select class="selectpicker form-control{{ $errors->has('role') ? ' has-error' : '' }}"
                        id="role" name="role">
                    @foreach(\App\Models\Role::get() as $role)
                        <option value="{{ $role->name }}">{{ $role->display_name }}</option>
                    @endforeach
                </select>
                @if ($errors->has('role'))
                    <span class="help-block">
                        <strong>{{ $errors->first('role') }}</strong>
                    </span>
                @endif
            </div>
        </div>
        @endrole

        @permission('delete_user')
        <div class="form-group">
            <div class="col-md-6 col-md-offset-4">
                <div class="checkbox">
                    <label>
                        <input type="checkbox" id="clear_pwd" name="clear_pwd" {{ old('clear_pwd')?'checked':'' }}>清除此账户密码
                    </label>
                </div>
            </div>
        </div>
        @endpermission

        <div class="form-group">
            <div class="col-md-8 col-md-offset-4">
                <button type="submit" class="btn btn-primary">
                    <span class="glyphicon glyphicon-ok"></span> 保存信息
                </button>
                @permission('delete_user')
                <button id="btn_del" type="button" class="btn btn-danger">
                    <span class="glyphicon glyphicon-remove-circle"></span> 删除账号
                </button>
                @endpermission
            </div>
        </div>
    </form>
@endsection
