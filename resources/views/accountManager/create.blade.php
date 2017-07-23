@extends('layouts.app')

@push('cssLink')
    <link rel="stylesheet" href="{{ url('/components/bootstrap-select/dist/css/bootstrap-select.min.css') }}">
@endpush

@push('jsLink')
    <script src="{{ url('/components/bootstrap-select/dist/js/bootstrap-select.min.js') }}"></script>
    <script src="{{ url('/components/bootstrap-select/dist/js/i18n/defaults-zh_CN.js') }}"></script>

    <script src="{{ url('/js/file_upload.js') }}"></script>
@endpush

@push('js')
    <script>
        $(function () {
            $("#department").selectpicker("val", "{{ old('department' )}}");
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
                    @foreach(\App\Models\Department::orderBy('number')->get() as $department)
                        <option value="{{ $department->number }}">{{ $department->display_name }}</option>
                    @endforeach
                </select>

                @if ($errors->has('department'))
                    <span class="help-block">
                        <strong>{{ $errors->first('department') }}</strong>
                    </span>
                @endif
            </div>
        </div>

        <div class="form-group{{ $errors->has('role') ? ' has-error' : '' }}">
            <label for="role" class="col-md-4 control-label">账号类型</label>
            <div class="col-md-6">
                <select class="selectpicker form-control{{ $errors->has('role') ? ' has-error' : '' }}" id="role"
                        name="role">
                    @foreach($userType as $type)
                        <option value="{{ ($role = (new $type)->role)->name }}">{{ $role->display_name }}</option>
                    @endforeach
                </select>
                @if ($errors->has('role'))
                    <span class="help-block">
                        <strong>{{ $errors->first('role') }}</strong>
                    </span>
                @endif
            </div>
        </div>

        <div class="form-group">
            <div class="col-md-8 col-md-offset-4">
                <button class="btn btn-primary">
                    <span class="glyphicon glyphicon-ok"></span> 保存并创建用户
                </button>
            </div>
        </div>
    </form>
@endsection
