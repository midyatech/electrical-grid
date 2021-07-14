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

if ((isset($_POST['data_type']) && $_POST['data_type'] != "")) {
    $data_type = $_POST['data_type'];
    $number  = $_POST['import_number'];
    $json_data = $_POST['data'];
    $data = json_decode($json_data, true);
    //print_r($data);

    switch($data_type)
    {
        case 'enclosure':
            //$Enclosure->ImportData($data_type, $data);
            break;
        case 'meter':
            $Enclosure->ImportData($data_type, $number, $data);
            break;
        case 'gateway':
            $Enclosure->ImportData($data_type, $number, $data);
            break;
    }

}



$enclosure_data["user_id"] = $USERID;
$enclosure_data["status_id"] = 0;
$result=$Enclosure->Add($enclosure_data);

if ($result != false) {
    //print $result;
    $message -> AddMessage($result, $Enclosure->Message);
} else {
    //error
    $message -> AddMessage($Enclosure->State, $Enclosure->Message); //$Enclosure->Message //"error_saving_enclosure"
    $message -> PrintJsonMessage();
}