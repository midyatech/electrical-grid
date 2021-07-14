<?php
require_once realpath(__DIR__ . '/../..') . '/include/settings.php';
require_once realpath(__DIR__ . '/../..') . '/class/User.php';
require_once realpath(__DIR__ . '/../..') . '/class/SystemMessage.php';

$user = new User();
$message = New SysetemMessage($LANGUAGE);


	
if(isset($_POST['old_password']) && $_POST['old_password'] != '' && isset($_POST['password']) && $_POST['password'] != '' )
{
	$old_password = $password = NULL;

	if (isset($_POST["old_password"]))
		$old_password = $_POST["old_password"];

	if (isset($_POST["password"]))
		$password = $_POST["password"];

	$result = $user -> ChangePassword($USERID, $old_password, $password, false);

	if (!isset($result) || $result) {
		//update chagne password status
		$user->Edit($USERID, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0);
		header('Location: ../../main_dashboard.php');
	} else {
		//something wrong
		//$message->AddMessage($user -> State, $user -> Message);
		$_SESSION['login_state']=$user->State;
		$_SESSION['login_message']=$user->Message;
		header("Location: ../change_password.php");
	}
}

if($message->GetMessagesCount()>0){
	$message->PrintMessages("back");
}
?>