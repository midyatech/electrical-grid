<?php
require_once realpath(__DIR__ . '/../..') . '/include/checkpermission.php';
require_once realpath(__DIR__ . '/../..') . '/class/SystemMessage.php';
require_once realpath(__DIR__ . '/../..') . '/class/Assembly.class.php';
require_once realpath(__DIR__ . '/../..') . '/class/Helper.php';
require_once realpath(__DIR__ . '/../..') . '/include/checksession.php';
require_once realpath(__DIR__ . '/../..') . '/include/settings.php';
require_once realpath(__DIR__ . '/../..') . '/class/UserLog.class.php';

$Assembly = new Assembly();
$message = New SysetemMessage($LANGUAGE);
$User_Log = new User_Log();

$assembly_order_id = Helper::Request("id", true);

$assembly_order_data['assembly_order_id'] = $assembly_order_id;

if(isset($_REQUEST["area"]) && $_REQUEST["area"] != "" ) {
	$assembly_order_data['area_id']= Helper::Request("area", true);
}

if(isset($_REQUEST["station_id"]) && $_REQUEST["station_id"] != "" ) {
	$assembly_order_data['station_id']= Helper::Request("station_id", true);
}

if(isset($_REQUEST["feeder_id"]) && $_REQUEST["feeder_id"] != "" ) {
	$assembly_order_data['feeder_id']= Helper::Request("feeder_id", true);
}

if(isset($_REQUEST["start_date"]) && $_REQUEST["start_date"] != "" ) {
	$assembly_order_data['start_date']= Helper::Request("start_date", true);
}

if(isset($_REQUEST["notes"]) && $_REQUEST[""] != "notes" ) {
	$assembly_order_data['notes']= Helper::Request("notes", true);
}

if(isset($_REQUEST["assembly_order_code"]) && $_REQUEST["assembly_order_code"] != "" ) {
	$assembly_order_data['assembly_order_code']= Helper::Request("assembly_order_code", true);
}

if(isset($_REQUEST["status"]) && $_REQUEST["status"] != "" ) {
	$assembly_order_data['status_id']= Helper::Request("status", true);
}

$result = $Assembly->UpdateAssemlyOrder($assembly_order_data);

$LogData = array();
$LogData["USER_ID"] = $USERID;
$LogData["TIMESTAMP"] = date("Y-m-d H:i:s");
$LogData["MODULE_ID"] = 2;
$LogData["OPERATION_ID"] = 8;
$LogData["KEY_DATA"] = $assembly_order_id;
$LogData["NEW_DATA"] = json_encode($assembly_order_data);
$LogData["OLD_DATA"] = $User_Log->GetJsonData("assembly_order", ["assembly_order_id"=>$assembly_order_id]);
$LogData["CRUD_OPERATION_ID"] = 2;
$LogData["TABLE_NAME"] = "assembly_order";
$LogData["RECORD_ID"] = $assembly_order_id;
$User_Log->AddRecord($LogData);

if ($result != false) {
	//print $result;
} else {
	//echo $Assembly->Message;
	//error
	print $message->AddMessage($Assembly->State, $Assembly->Message);
	//$message -> PrintJsonMessage();
}

header("Location: ../assembly_list.php?status=0");
?>
