<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>
    <link rel="stylesheet" href="{{ url('/components/bootstrap/dist/css/bootstrap.min.css') }}">
    {{-- <link rel="stylesheet" href="{{ url('/components/highcharts/css/highcharts.css') }}"> --}}
@stack('cssLink')
@stack('css')
<!-- 公共样式表 -->
    <style>
        body {
            background-color: #ddeffd;
        }

        tr th:not(:empty):hover {
            background-color: #f6f6f6;
        }

        tr th a:hover {
            text-decoration: none;
            color: darkgreen;
        }

        tr th {
            text-align: center;
        }

        a, button.btn, tr th {
            -webkit-transition-duration: 0.45s;
            transition-duration: 0.45s;
        }

        td > a, th > a {
            color: black;
        }

        .breadcrumb > li + li:before {
            color: #285e8e;
            content: "\";
        }
    </style>

    <style>
        /* header背景色 北航蓝 */
        .navbar {
            background-color: #0066cc;
        }

        /* 标题链接（正常状态） */
        ul.navbar-nav > li a:link, ul.navbar-nav > li a:visited, ul.navbar-nav > li a:active {
            color: #e3e3e3;
            text-decoration: none;
        }

        /* 标题链接（激活状态） */
        ul.navbar-nav > li a:hover {
            color: red;
            text-decoration: none;
        }

        /* 下拉菜单（正常状态） */
        ul.dropdown-menu > li a:visited, ul.dropdown-menu > li a:link, ul.dropdown-menu > li a:active {
            color: gray;
        }

        /* 下拉菜单（激活状态） */
        ul.dropdown-menu > li a:hover {
            color: black;
        }

        /* 左侧图片 */
        a.navbar-brand {
            padding-top: 8px;
        }
    </style>

    <style>
        ol.breadcrumb:empty {
            display: none;
        }

        ol.breadcrumb {
            background-color: #b4dcfc;
        }

        ol.breadcrumb li a {
            font-weight: bold;
            text-decoration: none;
        }

        ol.breadcrumb li.active {
            font-weight: bold;
        }
    </style>
    <style>
        footer.foot-wrap {
            /* background-color: #373f48; */
            border-top: 1px solid #dadada;
        }
    </style>

    <script src="{{ url('/components/jquery/dist/jquery.min.js') }}"></script>
    <script src="{{ url('/components/bootstrap/dist/js/bootstrap.min.js') }}"></script>


    <script>
        $("li.active").click(function(){
            alert("xxx");
        })
    </script>
    @stack('jsLink')
    @stack('js')


</head>
<body>
<div id="app">
    <!-- Header -->
    <nav class="navbar navbar-default navbar-static-top">
        <div class="container">
            <div class="navbar-header">

                <!-- Collapsed Hamburger -->
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse"
                        data-target="#app-navbar-collapse">
                    <span class="sr-only">Toggle Navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>

                <!-- Branding Image -->
                <a class="navbar-brand" href="{{ url('/') }}">
                <!--{{ config('app.name', 'Laravel') }}-->
                    <img src="{{ url('/img/buaa-logo.png') }}">
                </a>
            </div>

            <div class="collapse navbar-collapse" id="app-navbar-collapse">
                <!-- Left Side Of Navbar -->
                <ul class="nav navbar-nav">
                </ul>

                <!-- Right Side Of Navbar -->
                <ul class="nav navbar-nav navbar-right">
                    <!-- Authentication Links -->
                    @if (Auth::guest())
                        <li><a href="{{ url('/login') }}">登录</a></li>
                        <li><a href="{{ url('/register') }}">注册</a></li>
                    @else
                        @permission(['view_all_user','view_owned_user'])
                        <li>
                            <a href="{{ route('accountManager') }}">
                                <span class="glyphicon glyphicon-folder-close"></span>
                                用户管理
                            </a>
                        </li>
                        @endpermission

                        <li>
                            <a href="{{ url('/inquiry') }}">
                                <span class="glyphicon glyphicon-comment"></span>
                                留言管理
                            </a>
                        </li>

                        <li>
                            <a href="{{ url('/notification') }}">
                                <span class="glyphicon glyphicon-bullhorn"></span>
                                通知中心
                            </a>
                        </li>

                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button"
                               aria-expanded="false">
                                <span class="glyphicon glyphicon-user"></span>
                                {{ Auth::user()->name }} <span class="caret"></span>
                            </a>

                            <ul class="dropdown-menu" role="menu">
                                <li>
                                    <a href="{{ url('/account') }}">
                                        个人中心
                                    </a>
                                </li>
                                <li class="divider"></li>
                                <li>
                                    <a href="{{ url('/logout') }}"
                                       onclick="event.preventDefault();document.getElementById('logout-form').submit();">
                                        登出
                                    </a>

                                    <form id="logout-form" action="{{ url('/logout') }}" method="POST"
                                          style="display: none;">
                                        {{ csrf_field() }}
                                    </form>
                                </li>
                            </ul>
                        </li>
                    @endif
                </ul>
            </div>
        </div>
    </nav>

    <!-- Content Part -->
    <div class="container">
        <div class="row">
            <div class="col-md-10 col-md-offset-1 col-xs-12">
                <ol class="breadcrumb">@stack("crumb")</ol>
                <div class="jumbotron" style="background-color: white;padding: 12px;">@yield('content')</div>
            </div>
        </div>
    </div>

    <!-- Footer Part -->
    <footer class="container-fluid foot-wrap text-center" style="color:#878B91;">
        <p style="margin-top: 5px;">
            Copyright &copy; 2017 - {{ date("Y") }} BeiHang University 保留所有权利。
        <p>京ICP备****号-*</p>
    </footer>
</div>

</body>
</html>
