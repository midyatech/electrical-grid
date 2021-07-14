<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);
include_once realpath(__DIR__ . '/../..').'/include/checkpermission.php';
require_once realpath(__DIR__ . '/../..') . '/include/settings.php';
require_once realpath(__DIR__ . '/../..') . '/class/Helper.php';
require_once realpath(__DIR__ . '/../..') . '/class/Enclosure.class.php';
require_once realpath(__DIR__ . '/../..') . '/class/SystemMessage.php';
require_once realpath(__DIR__ . '/../..') . '/class/UserLog.class.php';

$Enclosure = new Enclosure();
$message = new SysetemMessage($LANGUAGE);
$User_Log = new User_Log();

$result = false;

if (isset($_REQUEST['enclosure_id']) && $_REQUEST['enclosure_id'] != "" &&
    isset($_REQUEST['configuration']) && $_REQUEST['configuration'] != "") {
    $enclosure_id = $_REQUEST['enclosure_id'];
    $configuration = $_REQUEST['configuration'];

    $result=$Enclosure->UpdateEcnclosureConfig($enclosure_id, $configuration);
}


// $enclosure_data["status_id"] = 1;


// $LogData = array();
// $LogData["USER_ID"] = $USERID;
// $LogData["TIMESTAMP"] = date("Y-m-d H:i:s");
// $LogData["MODULE_ID"] = 2;
// $LogData["OPERATION_ID"] = 13;
// $LogData["KEY_DATA"] = $enclosure_id;
// $LogData["NEW_DATA"] = json_encode($enclosure_data);
// $LogData["OLD_DATA"] = $User_Log->GetJsonData("enclosure", ["enclosure_id"=>$enclosure_id]);
// $LogData["CRUD_OPERATION_ID"] = 2;
// $LogData["TABLE_NAME"] = "enclosure";
// $LogData["RECORD_ID"] = $enclosure_id;
// $User_Log->AddRecord($LogData);

if ($result != false) {
    //print $result;
} else {
    //error
    $message -> AddMessage($Enclosure->State, $Enclosure->Message);
    $message -> PrintJsonMessage();
}