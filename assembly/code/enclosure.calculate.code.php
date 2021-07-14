<?php
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

$transformer_ids = Helper::Request("transformer_id", true);

$CalculatedEnclosure = $Assembly->AddCalculatedEnclosureTypes($transformer_ids);

$LogData = array();
$LogData["USER_ID"] = $USERID;
$LogData["TIMESTAMP"] = date("Y-m-d H:i:s");
$LogData["MODULE_ID"] = 2;
$LogData["OPERATION_ID"] = 9;
$LogData["KEY_DATA"] = $transformer_ids;
$LogData["NEW_DATA"] = $transformer_ids;
$LogData["OLD_DATA"] = "";
$LogData["CRUD_OPERATION_ID"] = 1;
$LogData["TABLE_NAME"] = "service_point_enclosure_type";
$LogData["RECORD_ID"] = "";
$User_Log->AddRecord($LogData);

if ($CalculatedEnclosure) {
	//print $CalculatedEnclosure;
} else {
	//echo $Assembly->Message;
	//error
	$message->AddMessage($Assembly->State, $Assembly->Message);
	//$message -> PrintJsonMessage();
}
//header("Location: ../dashboard.php?id=$id");
?>
