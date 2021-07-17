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
	$userInfo = $user -> Login($username, $password);
	if(is_array($userInfo)){
		//require_once realpath(__DIR__ . '/../..') . '/class/UserLog.class.php';
        //$user_log = new User_Log();
		$_SESSION['user_id'] = $userInfo[0]["USER_ID"] ;
		$_SESSION['user_name'] = $userInfo[0]["NAME"] ;
		$_SESSION['language'] = $userInfo[0]["UI_LANGUAGE"];
		$_SESSION['user_department_node_id'] = $userInfo[0]["user_department_node_id"];
		$_SESSION['user_access_node_id'] = $userInfo[0]["user_access_node_id"];
		$_SESSION['org_dir_node_id'] = $userInfo[0]["org_dir_node_id"];
		$_SESSION['web_browse'] = $userInfo[0]["WEB_BROWSE"];
		$_SESSION['map_coordinates'] = $userInfo[0]["map_coordinates"];
		$_SESSION['warehouse_id'] = $userInfo[0]["warehouse_id"];

		if($userInfo[0]["user_picture"] != null && $userInfo[0]["user_picture"] != ""){
		    $_SESSION["user_pic"] = ROOT_DIR."/".$uploader::BASE_PATH.$uploader::USER_PIC_PATH.$userInfo[0]["user_picture"];
		}

		//$log_id = $user_log-> AddRecord("USER", $_SESSION['user_id'], "userlog_login", "SUCCESS", "1", $_SESSION['user_name']);
		if($userInfo[0]["CHANGE_PASSWORD"]==1) {
			header("Location: ../../change_password.php");
		} else if($userInfo[0]["WEB_BROWSE"] == 0) {
			header("Location: ../../survey/add_survey.php");
		} else if($userInfo[0]["WEB_BROWSE"] == 2) {
			header("Location: ../../survey/add_gateway.php");
		} else if($userInfo[0]["WEB_BROWSE"] == 3) {
			header("Location: ../../assembly/assembly_list.php");
		} else if($userInfo[0]["WEB_BROWSE"] == 4) {
			header("Location: ../../installation/installation_summary.php");
		} elseif($userInfo[0]["WEB_BROWSE"] == 5) {
			header("Location: ../../survey/service_point_area_summary.php");
		} else if($userInfo[0]["WEB_BROWSE"] == 6) {
			header("Location: ../../installation/installation_summary.php");
		} else if($userInfo[0]["WEB_BROWSE"] == 11) {
			header("Location: ../../assembly/enclosure_list.php");
		} else {
			//header("Location: ../../assembly/assembly_list.php");
			header("Location: ../../home.php");
		}
	} else {
		$_SESSION['login_state']=$user->State;
		$_SESSION['login_message']=$user->Message;
		$user->SaveFailedLogin($username, $user->Message, $_SERVER['REMOTE_ADDR']);
		header("Location: ../user_login.php");
	}

} else {
	header("Location: ../user_login.php");
}
