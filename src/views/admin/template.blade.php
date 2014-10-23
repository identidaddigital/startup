<?php
        $menu = Config::get('startup::menu_admin');
?>
<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>@yield('title', 'Admin')</title>

    <!-- Bootstrap Core CSS -->
    <link href="{{ asset('assets/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">

    <!-- MetisMenu CSS -->
    <link href="{{ asset('assets/idcomar/css/plugins/metisMenu/metisMenu.min.css') }}" rel="stylesheet">

    <link rel="stylesheet" href="{{ asset('assets/idcomar/css/admin.css') }}" media="all">
    <!-- Custom CSS -->
    <link href="{{ asset('assets/idcomar/css/sb-admin-2.css') }}" rel="stylesheet">

    <!-- Custom Fonts -->
    <link href="{{ asset('assets/idcomar/font-awesome-4.1.0/css/font-awesome.min.css') }}" rel="stylesheet" type="text/css">

    <!-- jQuery Version 1.11.0 -->
    <script src="{{ asset('assets/idcomar/js/jquery-1.11.0.js') }}"></script>

    {{ Rapyd::head() }}
    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
    @yield('head','')
</head>

<body>

    @if (!Sentry::check())
        <div id="wrapper">
            @yield('content', '')
        </div>
    @else
        <div id="wrapper">

            <!-- Navigation -->
            <nav class="navbar navbar-default navbar-static-top" role="navigation" style="margin-bottom: 0">
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                    <a class="navbar-brand" href="{{ URL::route('indexDashboard') }}">@yield('logo', 'Administrador')</a>
                </div>
                <!-- /.navbar-header -->

                <ul class="nav navbar-top-links navbar-right">

                    <!-- /.dropdown -->
                    <li class="dropdown">
                        <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                            <i class="fa fa-user fa-fw"></i>  <i class="fa fa-caret-down"></i>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a href="{{ current_user_profile() }}"><i class="fa fa-user fa-fw"></i> {{trans('admin.header.user_profle')}}</a>
                            </li>
                            <li><a href="#"><i class="fa fa-gear fa-fw"></i> {{trans('admin.header.settings')}}</a>
                            </li>
                            <li class="divider"></li>
                            <li><a href="{{URL::route('logout')}}"><i class="fa fa-sign-out fa-fw"></i> {{trans('admin.header.logout')}}</a>
                            </li>
                        </ul>
                        <!-- /.dropdown-user -->
                    </li>
                    <!-- /.dropdown -->
                </ul>
                <!-- /.navbar-top-links -->

                <div class="navbar-default sidebar" role="navigation">
                    <div class="sidebar-nav navbar-collapse">
                        <ul class="nav" id="side-menu">

                            <li>
                                <a href="{{URL::route('indexDashboard')}}"><i class="fa fa-dashboard fa-fw"></i> {{trans('admin.menu.dashboard')}}</a>
                            </li>
                            @include('admin.partials.menu-items', array('items'=>$menu))

                            
                        </ul>
                    </div>
                    <!-- /.sidebar-collapse -->
                </div>
                <!-- /.navbar-static-side -->
            </nav>

            <!-- Page Content -->
            <div id="page-wrapper">
                <div class="row">
                    <div class="col-lg-12">
                        @yield('title_page', page_content_title())
                        {{ displayAlert() }}
                        @yield('content', '')
                    </div>
                    <!-- /.col-lg-12 -->
                </div>
                <!-- /.row -->
            </div>
            <!-- /#page-wrapper -->

        </div>
        <!-- /#wrapper -->    
    @endif


    <script src="{{ asset('packages/mrjuliuss/syntara/assets/js/dashboard/base.js') }}"></script>

    <!-- Bootstrap Core JavaScript -->
    <script src="{{ asset('assets/bootstrap/js/bootstrap.min.js') }}"></script>

    <!-- Metis Menu Plugin JavaScript -->
    <script src="{{ asset('assets/idcomar/js/plugins/metisMenu/metisMenu.min.js') }}"></script>

    <!-- Custom Theme JavaScript -->
    <script src="{{ asset('assets/idcomar/js/sb-admin-2.js') }}"></script>

</body>

</html>