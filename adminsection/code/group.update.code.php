<?php
include_once realpath(__DIR__ . '/../..').'/include/checkpermission.php';
require_once realpath(__DIR__ . '/../..') . '/include/settings.php';
require_once realpath(__DIR__ . '/../..') . '/class/SystemMessage.php';
require_once realpath(__DIR__ . '/../..') . '/class/Group.php';
//require_once realpath(__DIR__ . '/../..') . '/class/UserLog.class.php';

//$user_log = new User_Log();
$group = new Group();
$message = New SysetemMessage($LANGUAGE);

if (isset($_POST['group_id']) && $_POST['group_id'] != "") {

	$name = $status = $log_note = NULL;
	$group_id = $_POST["group_id"];
	if (isset($_POST["name"]))
		$name = $_POST["name"];
	if (isset($_POST["status"]))
		$status = $_POST["status"];
	$log_note =$name." ".$status;
	$result = $group -> Edit($group_id, $name, $status);

	//depending on result, take a (redirect) action
	if ($result) {
		//$user_log-> AddRecord("GROUP",$group_id, "userlog_Admin_edit_group", "Success", "1",$log_note );
		header('Location: ../group_list.php');
	} else {
		//$user_log-> AddRecord("GROUP",$group_id, "userlog_Admin_edit_group", "FAILD", "1",$log_note );
		$message -> AddMessage($group -> State, $group -> Message);
	}
} else {
	$message -> AddMessage(1, "no_group_selected");
}

if ($message -> GetMessagesCount() > 0) {
    $message -> PrintMessages("back");
}
?>
