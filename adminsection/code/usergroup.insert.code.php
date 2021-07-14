<?php
include_once realpath(__DIR__ . '/../..').'/include/checkpermission.php';
require_once realpath(__DIR__ . '/../..') . '/include/settings.php';
require_once realpath(__DIR__ . '/../..') . '/class/User.php';
require_once realpath(__DIR__ . '/../..') . '/class/SystemMessage.php';
//require_once realpath(__DIR__ . '/../..') . '/class/UserLog.class.php';
//$user_log = new User_Log();
$user = new User();
$message = New SysetemMessage($LANGUAGE);

if (isset($_POST['id']) && $_POST['id'] != "") {

	$operation = $group = $log_note = NULL;

	$id = $_POST["id"];
	if (isset($_POST["operation"])){
		$operation = $_POST["operation"];
		if ($operation == 'Insert') {
			if (isset($_POST["group"]))
				$group = $_POST["group"];
		}
		else{
			if (isset($_POST["user_groupList"]))
				$group = $_POST["user_groupList"];
		}
	}
	$userInfo = $user -> GetInfo($id);
	if($userInfo!=null){
		//validate
		//$message->PermissionMessage($user->ValidateUser($USERID, $userInfo[0]["AGENT_ID"], 'user.add.group'));
		//
	}
	$log_note = $id;
	$result = $user -> AddUserToGroup($operation, $id, $group);
	//depending on result, take a (redirect) action
	/*if ($result) {
		//$user_log-> AddRecord("USER_GROUP",NULL, "userlog_Admin_".$operation."_user_group", "Success", "1",$log_note );
		//header('Location: ../user_list.php');

	} else {
		//$user_log-> AddRecord("USER_GROUP",NULL, "userlog_Admin_".$operation."_user_group", "FAILD", "1",$log_note );
		//$message -> AddMessage($user -> State, $user -> Message);
	}*/
} else {
	$message -> AddMessage(1, "no_user_selected");
}

if ($message -> GetMessagesCount() > 0) {
    $message -> PrintMessages("back");
}
?>
