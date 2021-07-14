<?php
require_once realpath(__DIR__ . '/../..') . '/include/settings.php';
require_once realpath(__DIR__ . '/../..') . '/class/User.php';
require_once realpath(__DIR__ . '/../..') . '/class/SystemMessage.php';
require_once realpath(__DIR__ . '/../..') . '/class/UserLog.class.php';
$user_log = new User_Log();
$user = new User();
$message = New SysetemMessage($LANGUAGE);

if (isset($_POST['user_id']) && $_POST['user_id'] != "") {
	$user_id = $_POST['user_id'];
	$unique_id = $_POST['unique_id'];
	$user_info = $user -> GetInfo($user_id);
	if($user_info[0]["USER_STATUS_ID"] == 6 && $user_info[0]["UNIQUE_ID"] == NULL)
	{
		$status = 7; //waiting confirmation
		$result = $user -> Edit($user_id, NULL,NULL, NULL, NULL,NULL, NULL,$status, NULL, NULL,$unique_id);
		if ($result) {
			$_SESSION['login_state']= 1;
			$_SESSION['login_message'] = "waiting_confirmation";
		
			header('Location: ../../index.php');
		} else {
			echo "get_mac_faild";
			$message -> AddMessage($user -> State, $user -> Message);
		}
	}
	else
	{
		echo "User_has_sent_mac_before";
	}
} else {
	$message -> AddMessage(1, "no_user_selected");
}

if ($message -> GetMessagesCount() > 0) {
    $message -> PrintMessages("back");
}
?>