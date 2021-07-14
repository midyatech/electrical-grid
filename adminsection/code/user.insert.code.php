<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include_once realpath(__DIR__ . '/../..').'/include/checkpermission.php';
require_once realpath(__DIR__ . '/../..') . '/include/settings.php';
require_once realpath(__DIR__ . '/../..') . '/class/User.php';
require_once realpath(__DIR__ . '/../..') . '/class/SystemMessage.php';
//require_once realpath(__DIR__ . '/../..') . '/class/UserLog.class.php';
//$user_log = new User_Log();
$user = new User();
$message = New SysetemMessage($LANGUAGE);

if (isset($_POST['operation']) && $_POST['operation'] == "insert") {

	$name = $loginName = $password =  $uiLanguage = $uniqe_id =$log_note= $department=$accessDepartment= NULL;
	$web_browse = $op= 0;

	if (isset($_POST["name"]))
		$name = $_POST["name"];

	if (isset($_POST["loginName"]))
		$loginName = $_POST["loginName"];

	if (isset($_POST["password"]))
		$password = $_POST["password"];

	if (isset($_POST["DIRECTORATE"]))
		$department = $_POST["DIRECTORATE"];

	if (isset($_POST["ACCESS_DIR"]))
		$Access = $_POST["ACCESS_DIR"];

	//if (isset($_POST["status"]))
	//	$status = $_POST["status"];

	if (isset($_POST["language"]))
		$uiLanguage = $_POST["language"];

	if (isset($_POST["op"]))
		$op = $_POST["op"];

	/*if (isset($_POST["web_browse"]) && $_POST["web_browse"] == 1 )
	{
		$web_browse = $_POST["web_browse"];
		$status = 1;
	}
	else
	{
		$web_browse = 0;
		$status = 6;
	}
*/
	//validate
	//$message->PermissionMessage($user->ValidateUser($USERID, $department, 'user.add'));
	//
	//
	$status = 1;
	$user_id = $user -> Add($name, $loginName, $password, $status, $uiLanguage, 0, $Access, $department);

	//depending on result, take a (redirect) action
	if ($user_id) {
		//echo $user_id;
		//$log_id = $user_log-> AddRecord("USER",$user_id, "userlog_Admin_add_user", "SUCCESS", "1", $log_note);
		//$user_log->AddNotificationDetail($log_id, $_SESSION["agent"], $_SESSION["user_id"],"1");

		//die();
		header('Location: ../user_list.php');
	} else {
		//$user_log-> AddRecord("USER",NULL, "userlog_Admin_add_user", "FAILD", "1", $log_note);
		$message -> AddMessage($user -> State, $user -> Message);
	}
} else {
	$message -> AddMessage(1, "no_operation_selected");
}

if ($message -> GetMessagesCount() > 0) {
    $message -> PrintMessages("back");
}
?>
