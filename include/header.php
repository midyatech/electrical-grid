<?php
require_once realpath(__DIR__ . '/..').'/include/config.php';
require_once realpath(__DIR__ . '/..').'/include/checksession.php';
require_once realpath(__DIR__ . '/..').'/include/settings.php';
require_once realpath(__DIR__ . '/..').'/class/HtmlHelper.php';
require_once realpath(__DIR__ . '/..').'/class/User.php';
require_once realpath(__DIR__ . '/..').'/class/Notification.php';

$user = new User();
$html = new HTML($LANGUAGE);
$notification = new Notification();
$dictionary = new Dictionary($LANGUAGE);
$dictionary->GetAllDictionary();

if (session_id() == ''){session_start();}
if(isset($_SESSION["language"]) && $_SESSION["language"]=="ARABIC"){
    $dir="rtl";
    $dir2="-rtl";
}else if(isset($_SESSION["language"]) && $_SESSION["language"]=="KURDISH"){
    $dir="rtl";
    $dir2="-rtl";
}
if(isset($_SESSION["language"]) && $_SESSION["language"]=="ENGLISH"){
    $dir2="";
    $dir="";

}
?>
<!DOCTYPE html>
<html lang="en" dir="<?php echo $dir;?>">
    <head>
        <meta charset="utf-8" />
        <title>Electric Grid Survey</title>
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta content="width=device-width, initial-scale=1" name="viewport" />
        <meta content="" name="description" />
        <meta content="" name="author" />
        <link rel="shortcut icon" href="favicon.ico" />

        <!-- BEGIN GLOBAL MANDATORY STYLES -->
        <link href="http://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700&subset=all" rel="stylesheet" type="text/css" />
        <link href="../assets/global/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
        <link href="../assets/global/plugins/simple-line-icons/simple-line-icons.min.css" rel="stylesheet" type="text/css" />
        <link href="../assets/global/plugins/bootstrap/css/bootstrap<?php echo $dir2;?>.min.css" rel="stylesheet" type="text/css" />
        <link href="../assets/global/plugins/bootstrap-switch/css/bootstrap-switch<?php echo $dir2;?>.min.css" rel="stylesheet" type="text/css" />

        <!-- END GLOBAL MANDATORY STYLES -->
		<link href="../assets/global/plugins/bootstrap-wysihtml5/bootstrap-wysihtml5.css" rel="stylesheet" type="text/css" />
        <link href="../assets/global/plugins/bootstrap-markdown/css/bootstrap-markdown.min.css" rel="stylesheet" type="text/css" />
        <link href="../assets/global/plugins/bootstrap-summernote/summernote.css" rel="stylesheet" type="text/css" />
        <link href="../assets/global/plugins/datatables/datatables.min.css" rel="stylesheet" type="text/css">
        <link href="../assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap<?php echo $dir2;?>.css" rel="stylesheet" type="text/css">
        <link href="../assets/global/plugins/bootstrap-toastr/toastr<?php echo $dir2;?>.min.css" rel="stylesheet" type="text/css">

        <!-- BEGIN THEME GLOBAL STYLES -->
        <link href="../assets/global/css/components<?php echo $dir2;?>.min.css" rel="stylesheet" id="style_components" type="text/css" />
        <link href="../assets/global/css/plugins<?php echo $dir2;?>.min.css" rel="stylesheet" type="text/css" />
        <!-- END THEME GLOBAL STYLES -->

        <!-- BEGIN THEME LAYOUT STYLES -->
        <link href="../assets/layouts/layout/css/layout<?php echo $dir2;?>.min.css" rel="stylesheet" type="text/css" />
        <link href="../assets/layouts/layout/css/themes/darkblue<?php echo $dir2;?>.min.css" rel="stylesheet" type="text/css" id="style_color" />
        <link href="../assets/layouts/layout/css/custom<?php echo $dir2;?>.min.css" rel="stylesheet" type="text/css" />
        <link href="../assets/layouts/layout/css/reset.css" rel="stylesheet" type="text/css" />

        <link href="../assets/apps/css/todo-2<?php echo $dir2;?>.min.css" rel="stylesheet" type="text/css">

        <!-- END THEME LAYOUT STYLES -->

        <!-- project css -->
        <link href="../assets/global/css/tree.css" rel="stylesheet">
        <?php if($dir2=="-rtl"){ ?>
        <link href="../assets/global/css/tree-rtl.css" rel="stylesheet">
        <?php }?>
        <link href="../assets/lib/bootstrap_datepicker/bootstrap-datepicker3.min.css" rel="stylesheet"-->
        <!-- end project css -->


		<link href="../assets/global/plugins/dropzone/dropzone.min.css" rel="stylesheet" type="text/css" />
        <link href="../assets/global/plugins/dropzone/basic.min.css" rel="stylesheet" type="text/css" />

        <script src="../assets/global/plugins/jquery.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>

    </head>
    <!-- END HEAD -->

    <body class="page-header-fixed page-sidebar-closed-hide-logo page-container-bg-solidXX page-content-white"><!--page-container-bg-solid-->
        <div id="load"></div>

        <!-- BEGIN HEADER -->
        <div class="page-header navbar navbar-fixed-top">
            <!-- BEGIN HEADER INNER -->
            <div class="page-header-inner ">
                <!-- BEGIN LOGO -->
                <div class="page-logo">
                    <?php
                        /*
                        if($_SESSION['web_browse']==0){
                            $link = "survey/add_survey.php";
                        }else{
                            $link = "enclosure/add_enclosure.php";
                        }
                        */
                        $link = "../user/user_login.php";
                    ?>
                    <a href="<?php echo $FOLDERNAME.$link;?>">
                        <img src="../assets/layouts/layout/img/logo.png" alt="logo" class="logo-default" /> </a>
                    <div class="menu-toggler sidebar-toggler">
                        <span></span>
                    </div>
                </div>
                <!-- END LOGO -->
                <!-- BEGIN RESPONSIVE MENU TOGGLER -->
                <a href="javascript:;" class="menu-toggler responsive-toggler" data-toggle="collapse" data-target=".navbar-collapse">
                    <span></span>
                </a>
                <!-- END RESPONSIVE MENU TOGGLER -->
                <!-- BEGIN TOP NAVIGATION MENU -->
                <div class="top-menu" style="display:none">
                    <ul class="nav navbar-nav pull-right">
                        <!-- BEGIN NOTIFICATION DROPDOWN -->
                        <!-- DOC: Apply "dropdown-dark" class after below "dropdown-extended" to change the dropdown styte -->
                        <li class="dropdown dropdown-extended dropdown-notification" id="header_notification_bar">
                            <?php //include 'application.notification.php';?>
                        </li>
                        <!-- END NOTIFICATION DROPDOWN -->
                        <!-- BEGIN INBOX DROPDOWN -->
                        <!-- DOC: Apply "dropdown-dark" class after below "dropdown-extended" to change the dropdown styte -->
                        <li class="dropdown dropdown-extended dropdown-inbox" id="header_inbox_bar">
                            <?php //include 'doc.in.notification.php';?>
                        </li>
                        <!-- END INBOX DROPDOWN -->
                        <!-- BEGIN USER LOGIN DROPDOWN -->
                        <!-- DOC: Apply "dropdown-dark" class after below "dropdown-extended" to change the dropdown styte -->
                        <li class="dropdown dropdown-user">
                            <a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
                                <img alt="" class="img-circle" src="<?php echo $user_pic;?>" />
                                <span class="username username-hide-on-mobile"> <?php echo $USERNAME;?> </span>
                                <i class="fa fa-angle-down"></i>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-default">
                                <li>
                                    <a href="../user/user_detail.php">
                                        <i class="icon-user"></i> <?php echo $dictionary->GetValue("user_profile");?> </a>
                                </li>
                                <li>
                                    <a href="../logout.php">
                                        <i class="icon-key"></i> <?php echo $dictionary->GetValue("logout");?> </a>
                                </li>
                            </ul>
                        </li>
                        <!-- END USER LOGIN DROPDOWN -->
                        <!-- BEGIN QUICK SIDEBAR TOGGLER -->
                        <!-- DOC: Apply "dropdown-dark" class after below "dropdown-extended" to change the dropdown styte -->
                        <li class="dropdown dropdown-quick-sidebar-toggler">
                            <a href="javascript:;" class="dropdown-toggle">
                                <i class="icon-logout"></i>
                            </a>
                        </li>
                        <!-- END QUICK SIDEBAR TOGGLER -->
                    </ul>
                </div>
                <!-- END TOP NAVIGATION MENU -->
            </div>
            <!-- END HEADER INNER -->
        </div>
        <!-- END HEADER -->
        <!-- BEGIN HEADER & CONTENT DIVIDER -->
        <div class="clearfix"> </div>
        <!-- END HEADER & CONTENT DIVIDER -->
        <!-- BEGIN CONTAINER -->
        <div class="page-container">

        <?php include 'sidebar.php' ?>

        <!-- BEGIN CONTENT -->
        <div class="page-content-wrapper">
            <!-- BEGIN CONTENT BODY -->
            <div class="page-content">
                <!-- BEGIN PAGE HEADER-->
