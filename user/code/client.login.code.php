<?php
if (session_id() == '')session_start();
//SESSION FIXATION, Leaves old session intact
session_regenerate_id();

if(isset($_POST['username']) && $_POST['username']!='' && isset($_POST['password']) && $_POST['password']!='') {
	require_once realpath(__DIR__ . '/../..').'/class/User.php';
	require_once realpath(__DIR__ . '/../..').'/class/Uploader.php';
	require_once realpath(__DIR__ . '/../..').'/include/config.php';

	$uploader = new Uploader();
	$user = new User();
	$username = $_POST['username'];
	$password = $_POST['password'];
	$userInfo = $user -> ClientLogin($username, $password);
	if(is_array($userInfo)){

		//require_once realpath(__DIR__ . '/../..') . '/class/UserLog.class.php';
        //$user_log = new User_Log();
		$_SESSION['client_user_id'] = $userInfo[0]["USER_ID"] ;
		$_SESSION['client_user_name'] = $userInfo[0]["NAME"] ;
		$_SESSION['client_language'] = $userInfo[0]["UI_LANGUAGE"];
		$_SESSION['client_dir'] = $userInfo[0]["user_department_node_id"];
		$_SESSION['client_id'] = $userInfo[0]["client_id"] ;

		if($userInfo[0]["user_picture"] != null && $userInfo[0]["user_picture"] != ""){
			$_SESSION["user_pic"] = ROOT_DIR."/".$uploader::BASE_PATH.$uploader::USER_PIC_PATH.$userInfo[0]["user_picture"];
		}

		//$log_id = $user_log-> AddRecord("USER", $_SESSION['user_id'], "userlog_login", "SUCCESS", "1", $_SESSION['user_name']);
		if($userInfo[0]["CHANGE_PASSWORD"]==1){
			header("Location: ../../change_password.php");
		}else{
			header("Location: ../../application/client_applications.php");
		}
	} else {
		$_SESSION['client_login_state']=$user->State;
		$_SESSION['client_login_message']=$user->Message;

		header("Location: ../client_login.php");
	}

} else {
	header("Location: ../client_login.php");

}
