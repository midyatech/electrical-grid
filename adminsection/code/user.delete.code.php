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

	$user_id = $_POST["user_id"];
	$userInfo = $user -> GetInfo($user_id);
	if($userInfo!=null){
		//validate
		//$message->PermissionMessage($user->ValidateUser($USERID, $userInfo[0]["AGENT_ID"], 'user.delete'));
		//
		$result = $user -> SetAsDeleted($user_id);

		//depending on result, take a (redirect) action
		if ($result) {
			//$user_log-> AddRecord("USER",$user_id, "userlog_Admin_delete_user", "Success", "1", NULL);
			header('Location: ../user_list.php');
		} else {
			//$user_log-> AddRecord("USER",$user_id, "userlog_Admin_delete_user", "FAILD", "1", NULL);
			$message -> AddMessage($user -> State, $user -> Message);
		}
	}
} else {
	$message -> AddMessage(1, "no_user_selected");
}

if ($message -> GetMessagesCount() > 0) {
    $message -> PrintMessages("back");
}
?>
