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

// $assembly_order_data['area_id']= Helper::Post("area", true);
// $assembly_order_data['station_id']= Helper::Post("station_id", true);
// $assembly_order_data['feeder_id']= Helper::Post("feeder_id", true);
$assembly_order_data['start_date']= Helper::Post("start_date", true);
$assembly_order_data['notes']= Helper::Post("notes", true);
$assembly_order_data['user_id']= $USERID;
$assembly_order_data['create_date']= date("Y-m-d H:i:s");
$assembly_order_data['assembly_order_code']= Helper::Post("assembly_order_code", true);

// if (!empty($_POST['trnasformers'])) {
//     foreach ($_POST['trnasformers'] as $check) {
//         $assembly_order_data['trnasformers'][] = $check;
//     }
// }

$id = $Assembly->AddVanStock($assembly_order_data);

$LogData = array();
$LogData["USER_ID"] = $USERID;
$LogData["TIMESTAMP"] = date("Y-m-d H:i:s");
$LogData["MODULE_ID"] = 2;
$LogData["OPERATION_ID"] = 7;
$LogData["KEY_DATA"] = $id;
$LogData["NEW_DATA"] = json_encode($assembly_order_data);
$LogData["OLD_DATA"] = "";
$LogData["CRUD_OPERATION_ID"] = 1;
$LogData["TABLE_NAME"] = "assembly_order";
$LogData["RECORD_ID"] = $id;
$User_Log->AddRecord($LogData);

if ($id != false) {
	//print $result;
} else {
	//echo $Assembly->Message;
	//error
	$message->AddMessage($Assembly->State, $Assembly->Message);
	//$message -> PrintJsonMessage();
}
header("Location: ../add_vanstock.php?id=$id");
?>
