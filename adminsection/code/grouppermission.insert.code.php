<?php
include_once realpath(__DIR__ . '/../..').'/include/checkpermission.php';
require_once realpath(__DIR__ . '/../..') . '/include/settings.php';
require_once realpath(__DIR__ . '/../..') . '/class/Group.php';
require_once realpath(__DIR__ . '/../..') . '/class/SystemMessage.php';
// require_once realpath(__DIR__ . '/../..') . '/class/UserLog.class.php';
// $user_log = new User_Log();
$group = new Group();
$message = New SysetemMessage($LANGUAGE);

if (isset($_POST['id']) && $_POST['id'] != "") {

	$operation = $form = $permissionLevel = $log_note = NULL;
	$id = $_POST["id"];
	if (isset($_POST["operation"]))
	{
		$operation = $_POST["operation"];
		if ($operation == 'Insert') {
			if (isset($_POST["permission"]))
				$form = $_POST["permission"];
		}
		else{
			if (isset($_POST["group_permissionList"]))
				$form = $_POST["group_permissionList"];
			}
	}

	if (isset($_POST["permissionLevel"]))
		$permissionLevel = $_POST["permissionLevel"];
	$log_note = $id." ".$permissionLevel;
	$result = $group -> AddPermissionToGroup($operation, $id, $form);

	//depending on result, take a (redirect) action
	/*if ($result) {
		//$user_log-> AddRecord("GROUP_PERMISSION",NULL, "userlog_admin_".$operation."_group_permission", "Success", "1",$log_note );
		//header('Location: ../group_list.php');
	} else {
		//$user_log-> AddRecord("GROUP_PERMISSION",NULL, "userlog_admin_".$operation."_group_permission", "FAILD", "1",$log_note );
		//$message -> AddMessage($group -> State, $group -> Message);
	}*/
} else {
	$message -> AddMessage(1, "no_group_selected");
}

if ($message -> GetMessagesCount() > 0) {
    $message -> PrintMessages("back");
}
?>
