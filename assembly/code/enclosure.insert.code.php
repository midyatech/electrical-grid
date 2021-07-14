<?php
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

// if (isset($_POST['enclosure_sn']) && $_POST['enclosure_sn'] != "") {
//     $enclosure_sn = $_POST['enclosure_sn'];
//     $enclosure_id = $Enclosure->GetEnclosureId($enclosure_sn);
//     $enclosure_data["enclosure_id"] = $enclosure_id;
// }

if ((isset($_POST['assembly_order_id']) && $_POST['assembly_order_id'] != "")) {
    $enclosure_data["assembly_order_id"] = $_POST['assembly_order_id'];
}

if ((isset($_POST['enclosure_configuration_id']) && $_POST['enclosure_configuration_id'] != "")) {
    $enclosure_data["enclosure_configuration_id"] = $_POST['enclosure_configuration_id'];
}
if (isset($_POST['transformer_id']) && $_POST['transformer_id'] != "") {
    $enclosure_data["transformer_id"] = $_POST['transformer_id'];
}

$enclosure_data["user_id"] = $USERID;
$enclosure_data["status_id"] = 0;

/*
if (isset($_POST["gateway_id"]) && $_POST["gateway_id"] != "") {
    $enclosure_data["gateway_id"] = $_POST["gateway_id"];
}

if (isset($_POST["enclosure_type_id"]) && $_POST["enclosure_type_id"] != "") {
    $enclosure_data["enclosure_type_id"] = $_POST["enclosure_type_id"];
}

if (isset($_POST["phase"]) && $_POST["phase"] != "") {
    $enclosure_data["phase"] = $_POST["phase"];
}
*/

$result=$Enclosure->Add($enclosure_data);

$LogData = array();
$LogData["USER_ID"] = $USERID;
$LogData["TIMESTAMP"] = date("Y-m-d H:i:s");
$LogData["MODULE_ID"] = 2;
$LogData["OPERATION_ID"] = 11;
$LogData["KEY_DATA"] = $result;
$LogData["NEW_DATA"] = json_encode($enclosure_data);
$LogData["OLD_DATA"] = "";
$LogData["CRUD_OPERATION_ID"] = 1;
$LogData["TABLE_NAME"] = "enclosur";
$LogData["RECORD_ID"] = $result;
$User_Log->AddRecord($LogData);

if ($result != false) {
    print $result;
    //$message -> AddMessage($result, $Enclosure->Message);
} else {
    //error
    $message -> AddMessage($Enclosure->State, $Enclosure->Message); //$Enclosure->Message //"error_saving_enclosure"
    $message -> PrintJsonMessage();
}