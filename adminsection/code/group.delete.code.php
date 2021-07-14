<?php
include_once realpath(__DIR__ . '/../..').'/include/checkpermission.php';
require_once realpath(__DIR__ . '/../..') . '/include/settings.php';
require_once realpath(__DIR__ . '/../..') . '/class/Group.php';
require_once realpath(__DIR__ . '/../..') . '/class/SystemMessage.php';
// require_once realpath(__DIR__ . '/../..') . '/class/UserLog.class.php';
// $user_log = new User_Log();
$group = new Group();
$message = New SysetemMessage($LANGUAGE);


if (isset($_POST['group_id']) && $_POST['group_id'] != "") {

	$group_id = $_POST["group_id"];

	$result = $group -> SetAsDeleted($group_id);

	//depending on result, take a (redirect) action
	if ($result) {
		//$user_log-> AddRecord("GROUP",$group_id, "userlog_Admin_delete_group", "Success", "1", NULL);
		header('Location: ../group_list.php');
	} else {
		//$user_log-> AddRecord("GROUP",$group_id, "userlog_Admin_delete_group", "FAILD", "1", NULL);
		$message -> AddMessage($group -> State, $group -> Message);
	}
} else {
	$message -> AddMessage(1, "no_group_selected");
}

if ($message -> GetMessagesCount() > 0) {
    $message -> PrintMessages("back");
}
?>
