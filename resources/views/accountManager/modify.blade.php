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
@endpush

@push('js')
    <script>
        $(function () {
            $("#department").selectpicker("val", "{{ $user->department->number }}");

            @if($user instanceof \App\Models\Student)
            $("#grade").selectpicker("val", "{{ $user->grade }}");
            $("#class").selectpicker("val", "{{ $user->class }}");
            $("#political_status").selectpicker("val", "{{ $user->political_status }}");
            $("#native_place").selectpicker("val", "{{ $user->native_place }}");
            $("#financial_difficulty").selectpicker("val", "{{ $user->financial_difficulty }}");
            @endif

            @permission('delete_user')
            $("#btn_del").click(function () {
                if (confirm('你真的要删除此账号吗？')) {
                    $.ajax({
                        url: "{{ route('accountManager').'/'.$user->id }}",
                        type: "DELETE",
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        error: function () {
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
        <input type="hidden" name="_method" value="PUT">
        {{ csrf_field() }}

        <div class="form-group">
            <label for="avatar" class="col-md-4 control-label">用户头像</label>
            <div class="col-md-6">
                <img id="avatarImg" src="{{ $user->avatarUrl }}" class="img-thumbnail">
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
                <input id="name" type="text" class="form-control" name="name" value="{{ $user->name }}"
                       required autocomplete="off">
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
                @permission(['modify_all_student', 'modify_admin'])
                <select class="selectpicker form-control{{ $errors->has('department') ? ' has-error' : '' }}"
                        id="department" name="department">
                    @foreach(\App\Models\Department::orderBy('number')->get() as $department)
                        <option value="{{ $department->number }}">{{ $department->display_name }}</option>
                    @endforeach
                </select>
                @else
                    <select class="selectpicker form-control{{ $errors->has('department') ? ' has-error' : '' }}"
                            id="department" name="department">
                        @php
                            $department = $user->department
                        @endphp
                        <option value="{{ $department->number }}">{{ $department->display_name }}</option>
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
                <input id="email" type="email" class="form-control" name="email" value="{{ $user->email }}" disabled>

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
                <input id="phone" type="text" class="form-control" name="phone" value="{{ $user->phone }}" disabled>

                @if ($errors->has('phone'))
                    <span class="help-block">
                        <strong>{{ $errors->first('phone') }}</strong>
                    </span>
                @endif
            </div>
        </div>

        @if($user instanceof \App\Models\Student)
            <div class="form-group{{ $errors->has('grade') ? ' has-error' : '' }}">
                <label for="grade" class="col-md-4 control-label">年级</label>
                <div class="col-md-6">
                    <select class="selectpicker form-control{{ $errors->has('grade') ? ' has-error' : '' }}"
                            id="grade" name="grade" title="请选择年级">
                        @if($grade = \App\Models\Property::where('name','grade')->first())
                            @foreach($grade->propertyValues as $value)
                                <option value="{{ $value->name }}">{{ $value->display_name }}</option>
                            @endforeach
                        @endif
                    </select>
                    @if ($errors->has('grade'))
                        <span class="help-block">
                        <strong>{{ $errors->first('grade') }}</strong>
                    </span>
                    @endif
                </div>
            </div>


            <div class="form-group{{ $errors->has('class') ? ' has-error' : '' }}">
                <label for="class" class="col-md-4 control-label">班级</label>
                <div class="col-md-6">
                    <select class="selectpicker form-control{{ $errors->has('class') ? ' has-error' : '' }}"
                            id="class" name="class" title="请选择班级">
                        @if($class = \App\Models\Property::where('name','class')->first())
                            @foreach($class->propertyValues as $value)
                                <option value="{{ $value->name }}">{{ $value->display_name }}</option>
                            @endforeach
                        @endif
                    </select>
                    @if ($errors->has('class'))
                        <span class="help-block">
                        <strong>{{ $errors->first('class') }}</strong>
                    </span>
                    @endif
                </div>
            </div>

            <div class="form-group{{ $errors->has('political_status') ? ' has-error' : '' }}">
                <label for="political_status" class="col-md-4 control-label">政治面貌</label>
                <div class="col-md-6">
                    <select class="selectpicker form-control{{ $errors->has('political_status') ? ' has-error' : '' }}"
                            id="political_status" name="political_status" title="请选择政治面貌">
                        @if($political_status = \App\Models\Property::where('name','political_status')->first())
                            @foreach($political_status->propertyValues as $value)
                                <option value="{{ $value->name }}">{{ $value->display_name }}</option>
                            @endforeach
                        @endif
                    </select>
                    @if ($errors->has('political_status'))
                        <span class="help-block">
                        <strong>{{ $errors->first('political_status') }}</strong>
                    </span>
                    @endif
                </div>
            </div>

            <div class="form-group{{ $errors->has('native_place') ? ' has-error' : '' }}">
                <label for="native_place" class="col-md-4 control-label">籍贯</label>
                <div class="col-md-6">
                    <select class="selectpicker form-control{{ $errors->has('native_place') ? ' has-error' : '' }}"
                            id="native_place" name="native_place" title="请选择籍贯">
                        @if($native_place = \App\Models\Property::where('name','native_place')->first())
                            @foreach($native_place->propertyValues as $value)
                                <option value="{{ $value->name }}">{{ $value->display_name }}</option>
                            @endforeach
                        @endif
                    </select>
                    @if ($errors->has('native_place'))
                        <span class="help-block">
                        <strong>{{ $errors->first('native_place') }}</strong>
                    </span>
                    @endif
                </div>
            </div>

            <div class="form-group{{ $errors->has('financial_difficulty') ? ' has-error' : '' }}">
                <label for="financial_difficulty" class="col-md-4 control-label">经济情况</label>
                <div class="col-md-6">
                    <select class="selectpicker form-control{{ $errors->has('financial_difficulty') ? ' has-error' : '' }}"
                            id="financial_difficulty" name="financial_difficulty" title="请选择经济情况">
                        @if($financial_difficulty = \App\Models\Property::where('name','financial_difficulty')->first())
                            @foreach($financial_difficulty->propertyValues as $value)
                                <option value="{{ $value->name }}">{{ $value->display_name }}</option>
                            @endforeach
                        @endif
                    </select>
                    @if ($errors->has('financial_difficulty'))
                        <span class="help-block">
                        <strong>{{ $errors->first('financial_difficulty') }}</strong>
                    </span>
                    @endif
                </div>
            </div>
        @endif

        <div class="form-group">
            <label for="role" class="col-md-4 control-label">
                账号类型
            </label>
            <div class="col-md-6">
                <select class="selectpicker form-control" disabled>
                    <option>{{ $user->role->display_name }}</option>
                </select>
            </div>
        </div>

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
