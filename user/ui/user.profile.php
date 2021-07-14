<?php
require_once realpath(__DIR__ . '/../..').'/class/User.php';
require_once realpath(__DIR__ . '/../..').'/class/Uploader.php';
require_once realpath(__DIR__ . '/../..').'/class/Document.class.php';
require_once realpath(__DIR__ . '/../..').'/class/ClientApplication.php';
require_once realpath(__DIR__ . '/../..').'/class/Client.php';

$ca = new ClientApplication();
$uploader = new Uploader();
$user = new User();
$user_info = $user->GetInfo($USERID);
$condition = array();
$condition2 = array();
$condition["client_application_trace.client_application_trace_status_id"]=1;
$condition2["client_application_trace.client_application_trace_status_id"]=2;
$client_request=COUNT($ca->GetClientApplicationsTrace($condition2));
$new_request=COUNT($ca->GetClientApplicationsTrace($condition));
$app_counter=$client_request+$new_request;
$condition["client_application_trace.is_read"]=0;
$condition2["client_application_trace.is_read"]=0;
$client_request_unread=COUNT($ca->GetClientApplicationsTrace($condition2));
$new_request_unread=COUNT($ca->GetClientApplicationsTrace($condition));
$app_counter_unread=$client_request_unread+$new_request_unread;
$document = new Document();
if($user_info[0]["user_picture"] != null && $user_info[0]["user_picture"] != ""){
    //user picture
    $user_pic = $FOLDERNAME.$uploader::BASE_PATH.$uploader::USER_PIC_PATH.$user_info[0]["user_picture"];
}

$status_count = $document->GetInOutTraceListCount($USERDIR);
?>
<div class="profile-sidebar" style="width:250px">
    <!-- PORTLET MAIN -->
    <div class="portlet light">
        <!-- SIDEBAR USERPIC -->
        <div class="profile-userpic">
            <img src="<?php echo $user_pic;?>" class="img-responsive" alt=""> </div>
        <!-- END SIDEBAR USERPIC -->
        <!-- SIDEBAR USER TITLE -->
        <div class="profile-usertitle">
            <div class="profile-usertitle-name"><?php echo $user_info[0]["NAME"]; ?></div>
            <div class="profile-usertitle-job"> <?php //echo $user_info[0]["NODE_NAME"]; ?> </div>
        </div>

      <div class="profile-usermenu">
            <ul class="nav">
                <li class="active">
                    <a href="../user/user_detail.php">
                        <i class="icon-user"></i> <?php echo $dictionary->GetValue("user_profile");?> </a>
                </li>
            </ul>
        </div>
        <!-- END MENU -->
    </div>
    <!-- END PORTLET MAIN -->
    <!-- PORTLET MAIN -->
    <div class="portlet light ">
        <!-- STAT -->
        <div class="row list-separated profile-stat">
            <div class="col-md-4 col-sm-4 col-xs-6">
                <div class="uppercase profile-stat-title"> <?php print $status_count[0]["inbox_unread_count"]."/".$status_count[0]["inbox_count"]; ?> </div>
                <div class="uppercase profile-stat-text"> <a href="../document/doc_in_trace.php"><?php echo $dictionary->GetValue("Inbox"); ?></a> </div></div>

                <div class="col-md-4 col-sm-4 col-xs-6">
                <div class="uppercase profile-stat-title"> <?php print $app_counter_unread."/".$app_counter;
                ?> </div>
                <div class="uppercase profile-stat-text"> <a href="../document/client_applications.php"><?php echo $dictionary->GetValue("client_applications"); ?></a> </div></div>

                <div class="col-md-4 col-sm-4 col-xs-6">
                <div class="uppercase profile-stat-title"> 0/0
                </a> </div>
                <div class="uppercase profile-stat-text"> <a href="client_applications.php?client_application_trace_status_id=6"><?php echo $dictionary->GetValue("Messages"); ?></a> </div></div>
        </div>

    </div>
    <!-- END PORTLET MAIN -->
</div>
