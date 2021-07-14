<?php
include_once realpath(__DIR__ . '/../..').'/include/checkpermission.php';
require_once realpath(__DIR__ . '/../..') . '/include/settings.php';
require_once realpath(__DIR__ . '/../..') . '/class/Enclosure.class.php';
require_once realpath(__DIR__ . '/../..') . '/class/SystemMessage.php';
require_once realpath(__DIR__ . '/../..') . '/class/UserLog.class.php';

$Enclosure = new Enclosure();
$message = New SysetemMessage($LANGUAGE);
$User_Log = new User_Log();

if (isset($_GET["id"]) && $_GET["id"] != ""){
	$enclosure_id = $_GET["id"];
}

$result = $Enclosure ->CancelEnclosure($enclosure_id);

$LogData = array();
$LogData["USER_ID"] = $USERID;
$LogData["TIMESTAMP"] = date("Y-m-d H:i:s");
$LogData["MODULE_ID"] = 2;
$LogData["OPERATION_ID"] = 10;
$LogData["KEY_DATA"] = $enclosure_id;
$LogData["NEW_DATA"] = "";
$LogData["OLD_DATA"] = "[{enclosure_id:$enclosure_id}]";
$LogData["CRUD_OPERATION_ID"] = 3;
$LogData["TABLE_NAME"] = "enclosur";
$LogData["RECORD_ID"] = $enclosure_id;
$User_Log->AddRecord($LogData);

if(!$result){
	$message -> AddMessage($Enclosure -> State, $Enclosure -> Message);
	ob_clean();
	header('Content-type: application/json');
	echo json_encode($message->GetMessages());
}
?>
