<?php
include 'include/settings.php'; 

$user_unique_id = $_SESSION['user_unique_id'];

session_destroy();
session_regenerate_id();
session_unset();

unset (
	$_SESSION['user_unique_id'],
	$_SESSION['user_id'],
	$_SESSION['user_name'],
	$_SESSION['language'],
	$_SESSION['agent'],
	$_SESSION['user_os'],
	$_SESSION['login_state'],
	$_SESSION['login_message'], 
	$LANGUAGE, 
	$USERID, 
	$USERNAME, 
	$USERAGENT,
	$USEROS,
	$LOGIN_STATUS,
	$LOGIN_MESSAGE
);
session_start();
$_SESSION['user_unique_id'] = $user_unique_id;
header("Location: user/user_login.php");

?>