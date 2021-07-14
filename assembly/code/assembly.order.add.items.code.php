<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once realpath(__DIR__ . '/../..').'/include/checkpermission.php';
require_once realpath(__DIR__ . '/../..') . '/class/SystemMessage.php';
require_once realpath(__DIR__ . '/../..') . '/class/Assembly.class.php';
require_once realpath(__DIR__ . '/../..') . '/class/Helper.php';
require_once realpath(__DIR__ . '/../..') . '/include/checksession.php';
require_once realpath(__DIR__ . '/../..') . '/include/settings.php';
require_once realpath(__DIR__ . '/../..') . '/class/UserLog.class.php';

$Assembly = new Assembly();
$message = New SysetemMessage($LANGUAGE);
$User_Log = new User_Log();

$assembly_order_data = array();
$assembly_order_data['assembly_order_id']= Helper::Post("order_id", true);
$assembly_order_data['enclosure_config_id']= Helper::Post("enclosure_config_id", true);
$assembly_order_data['count']= Helper::Post("count", true);

$id = $Assembly->AddAssemblyExtraItems($assembly_order_data);
if ($result != false) {
	//print $result;
} else {
	//echo $Assembly->Message;
	//error
	$message->AddMessage($Assembly->State, $Assembly->Message);
	//$message -> PrintJsonMessage();
}
//header("Location: ../dashboard.php?id=$id");
?>
