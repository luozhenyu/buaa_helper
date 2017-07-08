@extends('layouts.app')

@push('css')
<style>
    @media (max-width: 991px) {
        #native_place_container > .col-xs-4.col-md-2:nth-of-type(1) {
            padding-left: 0;
        }
    }

    #native_place_container > .col-xs-4.col-md-2 {
        padding-right: 0;
    }

    #avatarImg {
        width: 150px;
        height: 150px;
    }
</style>
@endpush

@push('cssLink')
<link rel="stylesheet" href="{{ url('/components/bootstrap-select/dist/css/bootstrap-select.min.css') }}">
@endpush

@push('jsLink')
<script src="{{ url('/components/bootstrap-select/dist/js/bootstrap-select.min.js') }}"></script>
<script src="{{ url('/components/bootstrap-select/dist/js/i18n/defaults-zh_CN.js') }}"></script>

<script src="{{ url('/js/file_upload.js') }}"></script>
<script src="{{ url('/js/city_choose.js') }}"></script>
@endpush

@php($tree = ($place = \App\Models\City::where('code',$user->getProperty('native_place'))->first())? $place->tree() :[])
@push('js')
<script>
    $(function () {
        $("#department").selectpicker("val", "{{ $user->department_id }}");
        $("#role").selectpicker("val", "{{ $user->roles->first()->name }}");

        $("#grade").selectpicker("val", "{{ $user->getProperty('grade') }}");
        $("#class").selectpicker("val", "{{ $user->getProperty('class') }}");
        $("#political_status").selectpicker("val", "{{ $user->getProperty('political_status') }}");

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

        $("#financial_difficulty").selectpicker("val", "{{ $user->getProperty('financial_difficulty') }}");


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

        @permission('modify_owned_user')
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
        @endpermission
        $.setCityChoose('#province', '#city', '#area');
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

        <div class="form-group{{ $errors->has('avatar') ? ' has-error' : '' }}">
            <label for="avatar" class="col-md-4 control-label">用户头像</label>
            <div class="col-md-6">
                <img id="avatarImg" src="{{ $user->avatarUrl }}" class="img-thumbnail">
                @permission('modify_owned_user')
                <input id="avatarInput" type="hidden" name="avatar"
                       value="{{ ($avatarFile = $user->avatarFile)? $avatarFile->hash :'' }}">
                <span id="avatarSelect" class="btn btn-default btn-xs">
                    选择图片 {{ \App\Http\Controllers\FileController::uploadLimitHit() }}
                </span>
                @if ($errors->has('avatar'))
                    <span class="help-block">
                        <strong>{{ $errors->first('avatar') }}</strong>
                    </span>
                @endif
                @endpermission
            </div>
        </div>

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
                       value="{{ $user->name }}" required
                       autocomplete="off" {{ Entrust::can('modify_all_user')?'':'disabled' }}>
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
                       value="{{ $user->email }}"
                       autocomplete="off" {{ Entrust::can('modify_all_user')?'':'disabled' }}>

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
                       value="{{ $user->phone }}"
                       autocomplete="off" {{ Entrust::can('modify_all_user')?'':'disabled' }}>
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
                    <span class="glyphicon glyphicon-ok"></span>&nbsp;保存头像及信息
                </button>
                @permission('delete_user')
                <button id="btn_del" type="button" class="btn btn-danger">
                    <span class="glyphicon glyphicon-remove-circle"></span>&nbsp;删除账号
                </button>
                @endpermission
            </div>
        </div>
    </form>
@endsection
