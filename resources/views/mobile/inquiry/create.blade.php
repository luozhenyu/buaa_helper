@extends('layouts.mobile')

@section('title','创建问题')

@section('content')
    <form class="form-horizontal" role="form" method="POST" action="{{ url('/mobile/inquiry') }}">
        {{ csrf_field() }}

        <input type="hidden" name="access_token" value="{{ $access_token }}">

        <div class="form-group{{ $errors->has('title') ? ' has-error' : '' }}">
            <label for="title" class="col-md-4 control-label">标题</label>
            <div class="col-md-6">
                <input id="title" type="text" class="form-control" name="title" placeholder="可不填"
                       value="{{ old('title') }}">
                @if ($errors->has('title'))
                    <span class="help-block">
                        <strong>{{ $errors->first('title') }}</strong>
                    </span>
                @endif
            </div>
        </div>

        <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
            <label for="type" class="col-md-4 control-label">问题类型</label>
            <div class="col-md-6">
                <select class="form-control" id="type" name="type">
                    <option>问题咨询</option>
                    <option>意见反馈</option>
                    <option>bug反馈</option>
                </select>
                @if ($errors->has('type'))
                    <span class="help-block">
                        <strong>{{ $errors->first('type') }}</strong>
                    </span>
                @endif
            </div>
        </div>

        <div class="form-group{{ $errors->has('content') ? ' has-error' : '' }}">
            <label for="content" class="col-md-4 control-label">问题描述</label>
            <div class="col-md-6">
                <textarea id="content" type="text" class="form-control" name="content" style="resize: none;" rows="5"
                          required>{{ old('content') }}</textarea>
                @if ($errors->has('content'))
                    <span class="help-block">
                        <strong>{{ $errors->first('content') }}</strong>
                    </span>
                @endif
            </div>
        </div>

        <div class="form-group">
            <button type="submit" class="btn btn-primary btn-block">
                保存
            </button>
        </div>
    </form>
@endsection