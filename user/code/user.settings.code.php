<?php
require_once realpath(__DIR__ . '/../..') . '/include/settings.php';
require_once realpath(__DIR__ . '/../..') . '/class/User.php';
require_once realpath(__DIR__ . '/../..') . '/class/SystemMessage.php';

$user = new User();
$message = New SysetemMessage($LANGUAGE);

if (isset($_POST['user_id']) && $_POST['user_id'] != "") {
		
	$user_id = $_POST['user_id'];
	
	if (isset($_POST["language"]) && $_POST["language"] != $LANGUAGE) {
		$ui_language = $_POST["language"];
		$result = $user -> Edit($user_id, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, $ui_language);
		$_SESSION['language'] = $ui_language;
	}
	
	if(isset($_POST['old_password']) && $_POST['old_password'] != '' && isset($_POST['password']) && $_POST['password'] != '' )
	{
		$old_password = $password = NULL;
	
		if (isset($_POST["old_password"]))
			$old_password = $_POST["old_password"];
	
		if (isset($_POST["password"]))
			$password = $_POST["password"];
	
		$result = $user -> ChangePassword($user_id, $old_password, $password, false);
	}
	
	if (!isset($result) || $result) {
		header('Location: ../../main_dashboard.php');
	} else {
		//something wrong
		$message->AddMessage($user -> State, $user -> Message);
	}
	
} else {
	$message->AddMessage(1, "no_user_selected"); 
}

if($message->GetMessagesCount()>0){
	$message->PrintMessages("back");
}
?>