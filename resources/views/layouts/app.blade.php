<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <link rel="stylesheet" href="{{ url('/css/bootstrap.min.css') }}">
    <link href="//cdn.bootcss.com/highcharts/5.0.11/css/highcharts.css" rel="stylesheet">
@stack('cssLink')
@stack('css')
<!-- 公共样式表 -->
    <style>
        body {
            background-color: #ddeffd;
        }

        a, button.btn {
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

    <script src="{{ url('/js/jquery.min.js') }}"></script>
    <script src="{{ url('/js/bootstrap.min.js') }}"></script>
    <script src="//cdn.bootcss.com/highcharts/5.0.11/highcharts.js"></script>
    <!-- MathJac 配置信息 -->
    <script type="text/x-mathjax-config">
		MathJax.Hub.Config({
			tex2jax: {
			  inlineMath: [['$','$'], ['\\(','\\)']],
			  processEscapes: true,
			  skipTags: ['script', 'noscript', 'style', 'textarea','code','a','img','link'],
			  ignoreClass: "no_math"
			},
			TeX: {
					equationNumbers: {
						autoNumber: ["AMS"],
						useLabelIds: true
					}
				},
			"HTML-CSS": {
				linebreaks: {
					automatic: true
				},
				scale: 85
			},
			SVG: {
				linebreaks: {
					automatic: true
				}
			}
		});





    </script>
    <script type="text/javascript"
            src="//cdn.bootcss.com/mathjax/2.7.1/MathJax.js?config=TeX-AMS-MML_HTMLorMML"></script>
    <!-- 公共script（以后可以放置心跳包和websocket等） -->
    <script>
        $(document).ready(function () {
            MathJax.Hub.Queue(["Typeset", MathJax.Hub]);
        })
    </script>
    @stack('jsLink')
    @stack('js')
</head>
<body>
<div id="app">
    <!-- Header -->
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
                    <img src="{{ url('/img/bf1846a7275ad028.png') }}">
                </a>
            </div>

            <div class="collapse navbar-collapse" id="app-navbar-collapse">
                <!-- Left Side Of Navbar -->
                <ul class="nav navbar-nav">
                    @if (!Auth::guest())
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
                                <span class="	glyphicon glyphicon-comment"></span>
                                留言管理
                            </a>
                        </li>

                        <li>
                            <a href="{{ url('/notification') }}">
                                <span class="glyphicon glyphicon-bullhorn"></span>
                                通知中心
                            </a>
                        </li>

                    @endif

                </ul>

                <!-- Right Side Of Navbar -->
                <ul class="nav navbar-nav navbar-right">
                    <!-- Authentication Links -->
                    @if (Auth::guest())
                        <li><a href="{{ url('/login') }}">登录</a></li>
                        <li><a href="{{ url('/register') }}">注册</a></li>
                    @else
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
                                       onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
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
                <ol class="breadcrumb">@stack("crumb")</ol>
                <div class="jumbotron" style="background-color: white;padding: 12px;">@yield('content')</div>


            </div>
        </div>
    </div>


    <!-- Footer Part -->
    <style>
        footer.foot-wrap {
            /* background-color: #373f48; */
            border-top: 1px solid #dadada;
        }

    </style>

    <footer class="container-fluid foot-wrap">
        <p align="center" style="margin-top: 5px;color:#878B91;">
            Copyright &copy;2017 BeiHang University
        </p>
    </footer>

</div>

</body>
</html>
