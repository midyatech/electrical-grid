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
$FilterEnclosureType = array();
$meter_count = 0;

if ( isset($_POST['enclosure_id']) && $_POST['enclosure_id'] != "" ) {

    $enclosure_data = array();
    $enclosure_data["user_id"] =$USERID;

    if (isset($_POST['enclosure_id']) && $_POST['enclosure_id'] != "") {
        $enclosure_data["enclosure_id"] = $_POST['enclosure_id'];
    }

    if (isset($_POST['gateway_id']) && $_POST['gateway_id'] != "") {
        $enclosure_data["gateway_id"] = Helper::Post('gateway_id',true);
        $FilterEnclosureType["getway"] = 1;
    } else {
        $enclosure_data["gateway_id"] = "";
        $FilterEnclosureType["getway"] = 0;
    }

    if (isset($_POST['meter_type_id']) && $_POST['meter_type_id'] != "" ){
        $meter_type_id = Helper::Post('meter_type_id',true);
        $no_of_meter = 6;
        if($meter_type_id == 1 ){ //1 phase
            $no_of_meter = 3;
            $FilterEnclosureType["meter_type_id"] = 1;
        } else if($meter_type_id == 2){ //3 phase
            $no_of_meter = 2;
            $FilterEnclosureType["meter_type_id"] = 2;
        } else if($meter_type_id == 3){ //ct meter
            $no_of_meter = 1;
            $FilterEnclosureType["meter_type_id"] = 3;
        }
    }

    $meters_data = array();
    for($i=1; $i<=6; $i++){
        $meter_id = Helper::Post('meter_'.$i);
        if($meter_id != null){
            $meter_count++;
            $meters_data[] = array("meter_id"=>$meter_id, "meter_type_id"=>$meter_type_id, "sequence"=>$i);
        }
    }

    $FilterEnclosureType["Meter"] = $meter_count ;
    $EnclosureType = $Enclosure->GetEnclosureType($FilterEnclosureType);
    $enclosure_data["enclosure_type_id"] = $EnclosureType[0]["enclosure_type_id"];

    if (count($meters_data) > 0) {
        $result=$Enclosure->Add($enclosure_data, $meters_data);
    } else {
        $message -> AddMessage(2, "wrong_meters_number");
    }

} else {
    $message -> AddMessage(2, "no_enclosure_sn");
}

if ($result != false) {
    print $result;
} else {
    //error
    $message -> AddMessage($Enclosure->State, $Enclosure->Message); //$Enclosure->Message //"error_saving_enclosure"
    $message -> PrintJsonMessage();
}