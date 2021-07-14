<?php
require_once 'config.php';
if (session_id() == '')
	session_start();

$FOLDERNAME = ROOT_DIR."/";
if (isset($_SESSION['user_pic'])){
	$user_pic = $_SESSION['user_pic'];
}else{
	$user_pic = "";
	//$FOLDERNAME."/assets/global/img/user-icon.png";
}


if (isset($_SESSION['org_dir_node_id']))
{
	$ORGDIR = $_SESSION['org_dir_node_id'];
}
else
{
	$ORGDIR ="";
}

if (!isset($_SESSION['language']))
{
	$_SESSION['language'] = 'ARABIC';
}

if (isset($_SESSION['language']))
{
	$LANGUAGE = $_SESSION['language'];
}

if (!isset($_SESSION['map_coordinates']))
{
	$_SESSION['map_coordinates'] = 'latlng';
}

if (isset($_SESSION['map_coordinates']))
{
	$MAPCORDINATES = $_SESSION['map_coordinates'];
}

if (isset($_SESSION['web_browse']))
{
	$WEB_BROWSE = $_SESSION['web_browse'];
}

if (isset($_SESSION['user_id'])){
	$USERID = $_SESSION['user_id'];
}

if (isset($_SESSION['user_name'])){
	$USERNAME = $_SESSION['user_name'];
}

if (isset($_SESSION['user_department_node_id'])){
	$USERDIR = $_SESSION['user_department_node_id'];
}

if (isset($_SESSION['user_access_node_id'])){
	$USERACCESS = $_SESSION['user_access_node_id'];
}else{
	$USERACCESS = null;
}

if (isset($_SESSION['warehouse_id'])){
	$WAREHOUSEID = $_SESSION['warehouse_id'];
}


if (isset($_SESSION['login_state'])){
	$LOGIN_STATUS = $_SESSION['login_state'];
}

if (isset($_SESSION['login_message'])){
	$LOGIN_MESSAGE = $_SESSION['login_message'];
}

if($LANGUAGE !="ENGLISH"){
	$from_align ="right";
	$to_align ="left";
}else{
	$from_align ="left";
	$to_align ="right";
}

if (isset($_SESSION['access_dir']))
	$ACCESS_DIR = $_SESSION['access_dir'];

if (isset($_SESSION['access_dir_path']))
	$ACCESS_DIR_PATH = $_SESSION['access_dir_path'];

$PREF = NULL;
if( basename($_SERVER['PHP_SELF'], ".php") != "main_dashboard" && basename($_SERVER['PHP_SELF'], ".php") != "sub_dashboard")
	$PREF= "../";
?>
