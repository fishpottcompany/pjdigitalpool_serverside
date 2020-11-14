<!doctype html>
<html lang="en">


<!-- Mirrored from demo.riktheme.com/antina/side-menu/index.html by HTTrack Website Copier/3.x [XR&CO'2014], Thu, 27 Aug 2020 09:59:55 GMT -->
<head>
    <meta charset="UTF-8">
    <meta name="description" content="">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <!-- The above 4 meta tags *must* come first in the head; any other head content must come *after* these tags -->

    <!-- Title -->
    <title><?php echo $active_page; ?></title>

    <!-- Favicon -->
    <link rel="icon" href="/img/core-img/favicon.png">

    <!-- These plugins only need for the run this page -->
    <link rel="stylesheet" href="/css/default-assets/mini-event-calendar.min.css">

    <!-- Master Stylesheet [If you remove this CSS file, your file will be broken undoubtedly.] -->
    <link rel="stylesheet" href="/style.css">

    <!-- YIELD CUSTOM TOP SCRIPTS AND STYLES -->
    @yield('top_scripts_and_styles')
    <!-- END YIELD CUSTOM TOP SCRIPTS AND STYLES -->

</head>

<body>
    
    <span id="msg_holder"></span>
    <!-- Preloader -->
    <div id="pagepreloader" class="page-loading clearfix">
        <div class="page-load-inner">
            <div class="preloader-wrap">
                <div class="wrap-2">
                    <!--<div> <img src="/img/core-img/pre.gif" alt="Preloader"></div>-->
                </div>
            </div>
        </div>
    </div>
    <!-- Preloader -->

    <!-- ======================================
    ******* Page Wrapper Area Start **********
    ======================================= -->
    <div class="ecaps-page-wrapper">
        <!-- Sidemenu Area -->
        <div class="ecaps-sidemenu-area">
            <!-- Desktop Logo -->
            <div class="ecaps-logo" >
                <a href="<?php echo url('/'); ?>/admin/dashboard" ><!--<img style="height: 50px; max-height: 50px; width: 100px;" src="/img/core-img/logo.png" alt="Desktop Logo"> <img class="small-logo" src="/img/core-img/logo.png" alt="Mobile Logo">--></a>
            </div>

            <!-- Side Nav -->
            <div class="ecaps-sidenav" id="ecapsSideNav">
                <!-- Side Menu Area -->
                <div class="side-menu-area">
                    <!-- Sidebar Menu -->
                    <nav>
                        <ul class="sidebar-menu" data-widget="tree"> 
                            <li  <?php if(isset($active_page) && $active_page == 'Users'){ echo 'class="active"'; } ?> ><a href="<?php echo url('/'); ?>/admin/users/list"><i class="fa fa-users"  style="color: black;"></i> <span>Users</span></a></li>
                                        
                            <li class="treeview <?php if(isset($active_page) && $active_page == 'Audios'){ echo 'active'; } ?>">
                                <a href="javascript:void(0)"><i class="fa fa-file-audio-o" style="color: black;"></i><span>Audios</span></a>
                                <ul class="treeview-menu">
                                    <li><a href="<?php echo url('/'); ?>/admin/audios/add">Add</a></li>
                                    <li><a href="<?php echo url('/'); ?>/admin/audios/delete">Delete</a></li>
                                </ul>
                            </li>
                            <li class="treeview <?php if(isset($active_page) && $active_page == 'Videos'){ echo 'active'; } ?>">
                                <a href="javascript:void(0)"><i class="fa fa-file-video-o" style="color: black;"></i> <span>Videos</span></a>
                                <ul class="treeview-menu">
                                    <li><a href="<?php echo url('/'); ?>/admin/videos/add">Add</a></li>
                                    <li><a href="<?php echo url('/'); ?>/admin/videos/delete">Delete</a></li>
                                </ul>
                            </li>
                            <li  <?php if(isset($active_page) && $active_page == 'Prayer Requests'){ echo 'class="active"'; } ?> ><a href="<?php echo url('/'); ?>/admin/prequests/list"><i class="fa fa-sign-language"  style="color: black;"></i> <span>Prayer Requests</span></a></li>
                            <li  <?php if(isset($active_page) && $active_page == 'Testimonies'){ echo 'class="active"'; } ?> ><a href="<?php echo url('/'); ?>/admin/testimonies/list"><i class="fa fa-comments"  style="color: black;"></i> <span>Testimonies</span></a></li>
                            <li  <?php if(isset($active_page) && $active_page == 'Feedbacks'){ echo 'class="active"'; } ?> ><a href="<?php echo url('/'); ?>/admin/feedbacks/list"><i class="fa fa-star"  style="color: black;"></i> <span>Feedbacks</span></a></li>
                        </ul>
                    </nav>
                </div>
            </div>
        </div>

        <!-- Page Content -->
        <div class="ecaps-page-content">
            <!-- Top Header Area -->
            <header class="top-header-area d-flex align-items-center justify-content-between">
                <div class="left-side-content-area d-flex align-items-center">
                    <!-- Mobile Logo -->
                    <div class="mobile-logo mr-3 mr-sm-4">
                        <a href="index.html"><img src="/img/core-img/small-logo.png" alt="Mobile Logo"></a>
                    </div>

                    <!-- Triggers -->
                    <div class="ecaps-triggers mr-1 mr-sm-3">
                        <div class="menu-collasped" id="menuCollasped">
                            <i class="zmdi zmdi-menu"></i>
                        </div>
                        <div class="mobile-menu-open" id="mobileMenuOpen">
                            <i class="zmdi zmdi-menu"></i>
                        </div>
                    </div>
                </div>

                <div class="right-side-navbar d-flex align-items-center justify-content-end">
                    <!-- Mobile Trigger -->
                    <div class="right-side-trigger" id="rightSideTrigger">
                        <i class="fa fa-reorder"></i>
                    </div>

                    <!-- Top Bar Nav -->
                    <ul class="right-side-content d-flex align-items-center">

                        <li class="nav-item dropdown">
                            <button type="button" class="btn dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <div id="settings-trigger"><i class="ti-settings font-17"></i></div></button>
                            <div class="dropdown-menu dropdown-menu-right">
                                <!-- User Profile Area -->
                                <div class="user-profile-area">
                                    <a  onclick="user_token_is_no_longer_valid();" style="cursor: pointer" class="dropdown-item"><i class="ti-unlink profile-icon" aria-hidden="true"></i> Sign-out</a>
                                </div>
                            </div>
                        </li>
                    </ul>
                </div>
            </header>

            <!-- Main Content Area -->
            
            <!-- YIELD MAIN PAGE CONTENT AND FOOTER -->
            @yield('main_content_and_footer')
            <!-- END YIELD MAIN PAGE CONTENT AND FOOTER -->
        </div>
    </div>

    <!-- ======================================
    ********* Page Wrapper Area End ***********
    ======================================= -->

    <!-- YIELD BOTTOM SCRIPTS -->
    @yield('bottom_scripts')
    <!-- END YIELD BOTTOM SCRIPTS -->

</body>


<!-- Mirrored from demo.riktheme.com/antina/side-menu/index.html by HTTrack Website Copier/3.x [XR&CO'2014], Thu, 27 Aug 2020 10:02:21 GMT -->
</html>