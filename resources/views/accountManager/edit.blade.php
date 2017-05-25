@extends('layouts.app')

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

        @role('admin')
        <div class="form-group{{ $errors->has('role') ? ' has-error' : '' }}">
            <label for="role" class="col-md-4 control-label">账号类型</label>
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
