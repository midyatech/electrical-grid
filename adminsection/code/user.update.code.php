<?php
include_once realpath(__DIR__ . '/../..').'/include/checkpermission.php';
require_once realpath(__DIR__ . '/../..') . '/include/settings.php';
require_once realpath(__DIR__ . '/../..') . '/class/User.php';
require_once realpath(__DIR__ . '/../..') . '/class/SystemMessage.php';
//require_once realpath(__DIR__ . '/../..') . '/class/UserLog.class.php';
//$user_log = new User_Log();
$user = new User();
$message = New SysetemMessage($LANGUAGE);

if (isset($_POST['user_id']) && $_POST['user_id'] != "") {

	$name = $loginName = $password = $department = $accessDepartment = $status = $attemptNum = $uiLanguage =$unique_id = NULL;
	$web_browse = $op= 0;
	$user_id = $_POST["user_id"];
	$user_info = $user->GetInfo($user_id);

	if (isset($_POST["name"]) && $_POST["name"] != '')
		$name = $_POST["name"];

	if (isset($_POST["loginName"]) && $_POST["loginName"] != '')
		$loginName = $_POST["loginName"];

	if (isset($_POST["user_status"]))
		$status = $_POST["user_status"];

	if($status == 6){
		$unique_id = '';
	}

    if (isset($_POST["DIRECTORATE"]))
		$department = $_POST["DIRECTORATE"];

    if (isset($_POST["attemptNum"]))
		$attemptNum = $_POST["attemptNum"];

    if (isset($_POST["language"]))
        $uiLanguage = $_POST["language"];

	if (isset($_POST["web_browse"]) && $_POST["web_browse"] == 1 )
	{
		$web_browse = $_POST["web_browse"];
		//$status = 1;
	}
	else
	{
		$web_browse = 0;
		//$status = 6;
	}


	//validate
	//$message->PermissionMessage($user->ValidateUser($USERID, $department, 'user.edit'));
	//

	if (isset($_POST["password"]) && $_POST["password"] != '') {
	    $password = $_POST["password"];
        $old_password = NULL;
        $result2 = $user -> ChangePassword($user_id, $old_password, $password, true);
        if($result2)
		{
			//$user_log-> AddRecord("USER",$user_id, "userlog_Admin_change_password", "SUCCESS", "1", NULL);
		}
       else{
		  	//$user_log-> AddRecord("USER",$user_id, "userlog_Admin_change_password", "FAILD", "1", NULL);
            $message -> AddMessage($user -> State, $user -> Message);
        }
	}


    //$log_note = $name." ".$loginName." ".$department." ".$accessDepartment." ".$status." ".$uiLanguage." ".$unique_id;
	$result = $user -> Edit($user_id, $name, $loginName, NULL, NULL, $status, $attemptNum, $uiLanguage, NULL, $web_browse, $unique_id, $department);

	//depending on result, take a (redirect) action
	if ($result) {
		//$user_log-> AddRecord("USER",$user_id, "userlog_Admin_edit_user", "SUCCESS", "1", $log_note);
		//update sessions if updating current user
		if($_SESSION["user_id"] == $user_id){
			$_SESSION["agent"] = $department;
			$_SESSION["language"] = $uiLanguage;
		}
		header('Location: ../user_list.php');
	} else {
		//$user_log-> AddRecord("USER",$user_id, "userlog_Admin_edit_user", "FAILD", "1", $log_note);
		$message -> AddMessage($user -> State, $user -> Message);
	}
} else {
	$message -> AddMessage(1, "no_user_selected");
}

if ($message -> GetMessagesCount() > 0) {
    $message -> PrintMessages("back");
}
?>
