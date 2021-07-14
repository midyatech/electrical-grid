<?php
require_once realpath(__DIR__ . '/../') . '/include/settings.php';
require_once realpath(__DIR__ . '/../').'/class/User.php';
require_once realpath(__DIR__ . '/../').'/class/Crypt.php';
$user = new User();
/*
$crypt = new Crypt();
$input= "A=#48TvZ";
echo "<br>";
$enc = $crypt->MediaEncrypt($input);
echo "<br>encrypted data: ". $enc."<br>";
echo "<br>".gettype($enc) ."<br>";
$dec = $crypt->MediaDecrypt($enc);
echo "<br>decrypted data: ". $dec."<br>";
*/
/*
$crypt = new Crypt();
print "<br/>".$crypt->MediaDecrypt("1q2w3e4r5t6y7u8ida5r/pYeAB/6kdhN3iMzBA==");
*/
/*
$crypt = new Crypt();
print '<br/>'.$crypt->MediaEncrypt('Behz@d20');
*/


if ( isset($_SESSION['user_id']) && $_SESSION['user_id'] != "" ) {

    /*
    if($_SESSION['web_browse']==0){
        header("Location: ../survey/add_survey.php");
    }else{
		header("Location: ../enclosure/add_enclosure.php");
    }
    */



    if($_SESSION['web_browse'] == 0) {
        header("Location: ../survey/add_survey.php");
    } else if($_SESSION['web_browse'] == 2) {
        header("Location: ../survey/add_gateway.php");
    } else if($_SESSION['web_browse'] == 3) {
        header("Location: ../assembly/assembly_list.php");
    } else if($_SESSION['web_browse'] == 4) {
        header("Location: ../installation/installation_summary.php");
    } elseif($_SESSION['web_browse'] == 5) {
        header("Location: ../survey/service_point_area_summary.php");
    } else if($_SESSION['web_browse'] == 6) {
        header("Location: ../installation/installation_summary.php");
    } else if($_SESSION['web_browse'] == 11) {
        header("Location: ../assembly/enclosure_list.php");
    } else {
        header("Location: ../assembly/service_point_area_summary.php");
    }
/*
    if($_SESSION['web_browse'] == 0) {
        header("Location: ../survey/add_survey.php");
    } else if($_SESSION['web_browse'] == 3) {
        header("Location: ../assembly/scan_enclosure.php");
    } else if($_SESSION['web_browse'] == 4) {
        header("Location: ../installation/dashboard.php");
    } else if($_SESSION['web_browse'] == 5) {
        header("Location: ../survey/service_point_area_summary.php");
    } else if($_SESSION['web_browse'] == 6) {
        header("Location: ../installation/installation.php");
    } else {
        header("Location: ../assembly/scan_enclosure.php");
    }
*/
} else {

    $unique_id =  "";
    if(isset($_POST['unique_id']) && $_POST['unique_id']!='')
    {
        $unique_id = $_POST["unique_id"];
    }elseif(isset($_SESSION['user_unique_id']) && $_SESSION['user_unique_id']!='')
    {
        //if it was faile attempt and return to index
        //or after logging out
        $unique_id = $_SESSION['user_unique_id'];
    }

    ?>
    <html lang="en">

        <head>
            <meta charset="utf-8" />
            <title>User Login</title>
            <meta http-equiv="X-UA-Compatible" content="IE=edge">
            <meta content="width=device-width, initial-scale=1" name="viewport" />
            <meta content="" name="author" />
            <!-- BEGIN GLOBAL MANDATORY STYLES -->
            <link href="http://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700&subset=all" rel="stylesheet" type="text/css" />
            <link href="../assets/global/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
            <link href="../assets/global/plugins/simple-line-icons/simple-line-icons.min.css" rel="stylesheet" type="text/css" />
            <link href="../assets/global/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
            <link href="../assets/global/plugins/bootstrap-switch/css/bootstrap-switch.min.css" rel="stylesheet" type="text/css" />
            <!-- END GLOBAL MANDATORY STYLES -->
            <!-- BEGIN PAGE LEVEL PLUGINS -->
            <link href="../assets/global/plugins/select2/css/select2.min.css" rel="stylesheet" type="text/css" />
            <link href="../assets/global/plugins/select2/css/select2-bootstrap.min.css" rel="stylesheet" type="text/css" />
            <!-- END PAGE LEVEL PLUGINS -->
            <!-- BEGIN THEME GLOBAL STYLES -->
            <link href="../assets/global/css/components.min.css" rel="stylesheet" id="style_components" type="text/css" />
            <link href="../assets/global/css/plugins.min.css" rel="stylesheet" type="text/css" />
            <!-- END THEME GLOBAL STYLES -->
            <!-- BEGIN PAGE LEVEL STYLES -->
            <link href="../assets/pages/css/login-2.min.css" rel="stylesheet" type="text/css" />
            <!-- END PAGE LEVEL STYLES -->
            <!-- BEGIN THEME LAYOUT STYLES -->
            <!-- END THEME LAYOUT STYLES -->
        <!-- END HEAD -->

        <body class=" login">
            <!-- BEGIN LOGO -->
            <div class="logo">
                <a href="index.html">
                    <img src="../assets/layouts/layout/img/logo.png" style="height: 50px;" alt="" /> </a>
            </div>
            <!-- END LOGO -->
            <!-- BEGIN LOGIN -->
            <div class="content">
                <!-- BEGIN LOGIN FORM -->
                <form  action="code/user.login.code.php" class="login-form" method="post">
                    <div class="form-title">
                        <span class="form-title">Welcome.</span>
                        <span class="form-subtitle">Please login.</span>
                    </div>
                    <?php if ( isset($_SESSION['login_message']) ) { ?>
                    <div class="alert alert-danger">
                        <button class="close"></button>
                        <span><?php print $_SESSION['login_message']; ?></span>
                    </div>
                    <?php } ?>
                    <div class="alert alert-danger display-hide">
                        <button class="close" data-close="alert"></button>
                        <span> Enter any username and password. </span>
                    </div>
                    <div class="form-group">
                        <!--ie8, ie9 does not support html5 placeholder, so we just show field title for that-->
                        <label class="control-label visible-ie8 visible-ie9">Username</label>
                        <input class="form-control form-control-solid placeholder-no-fix" type="text" autocomplete="off" placeholder="Username" name="username" /> </div>
                    <div class="form-group">
                        <label class="control-label visible-ie8 visible-ie9">Password</label>
                        <input class="form-control form-control-solid placeholder-no-fix" type="password" autocomplete="off" placeholder="Password" name="password" /> </div>
                    <div class="form-actions">
                        <button type="submit" class="btn red btn-block uppercase">Login</button>
                    </div>
                    <div class="form-actions">
                        <div class="pull-left">
                            <label class="rememberme mt-checkbox mt-checkbox-outline">
                                <input type="checkbox" name="remember" value="1" /> Remember me
                                <span></span>
                            </label>
                        </div>
                        <div class="pull-right forget-password-block">
                            <a href="javascript:;" id="forget-password" class="forget-password">Forgot Password?</a>
                        </div>
                    </div>

            <div class="copyright hide"> 2017 © Midyatech </div>
            <!-- END LOGIN -->
            <!--[if lt IE 9]>
    <script src="../assets/global/plugins/respond.min.js"></script>
    <script src="../assets/global/plugins/excanvas.min.js"></script>
    <script src="../assets/global/plugins/ie8.fix.min.js"></script>
    <![endif]-->
            <!-- BEGIN CORE PLUGINS -->
            <script src="../assets/global/plugins/jquery.min.js" type="text/javascript"></script>
            <script src="../assets/global/plugins/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
            <script src="../assets/global/plugins/js.cookie.min.js" type="text/javascript"></script>
            <script src="../assets/global/plugins/jquery-slimscroll/jquery.slimscroll.min.js" type="text/javascript"></script>
            <script src="../assets/global/plugins/jquery.blockui.min.js" type="text/javascript"></script>
            <script src="../assets/global/plugins/bootstrap-switch/js/bootstrap-switch.min.js" type="text/javascript"></script>
            <!-- END CORE PLUGINS -->
            <!-- BEGIN PAGE LEVEL PLUGINS -->
            <script src="../assets/global/plugins/jquery-validation/js/jquery.validate.min.js" type="text/javascript"></script>
            <script src="../assets/global/plugins/jquery-validation/js/additional-methods.min.js" type="text/javascript"></script>
            <script src="../assets/global/plugins/select2/js/select2.full.min.js" type="text/javascript"></script>
            <!-- END PAGE LEVEL PLUGINS -->
            <!-- BEGIN THEME GLOBAL SCRIPTS -->
            <script src="../assets/global/scripts/app.min.js" type="text/javascript"></script>
            <!-- END THEME GLOBAL SCRIPTS -->
            <!-- BEGIN PAGE LEVEL SCRIPTS -->
            <script src="../assets/pages/scripts/login.min.js" type="text/javascript"></script>
            <!-- END PAGE LEVEL SCRIPTS -->
            <!-- BEGIN THEME LAYOUT SCRIPTS -->
            <!-- END THEME LAYOUT SCRIPTS -->
            <script>
                $(document).ready(function()
                {
                    $('#clickmewow').click(function()
                    {
                        $('#radio1003').attr('checked', 'checked');
                    });
                })
            </script>

    </body>


    </html>
    <?php } ?>