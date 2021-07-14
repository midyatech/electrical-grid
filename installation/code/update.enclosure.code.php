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

//2 = error
//3 = success


//??
$enclosure_meters_data["enclosure_status"] = 3;


$change_reason = Helper::Request("change_reason", true);


if ((isset($_POST['enclosure_id']) && $_POST['enclosure_id'] != "")) {
    $enclosure_id = $_POST['enclosure_id'];
    $enclosure_meters_data["enclosure_id"] = $enclosure_id;
}


//check gateway availability
if ((isset($_POST['gateway_id']) && $_POST['gateway_id'] != "")) {
    $gateway_sn = $_POST['gateway_id'];
    $gateway_id = $Enclosure->GetGatewayId($gateway_sn);
    if ( $gateway_id > 0 ) {
        $gateway_filter = array();
        $gateway_filter["enclosure_id"] = $enclosure_id;
        $gateway_filter["gateway_id"] = $gateway_id;
        $gateway_is_assembled_in_other_enclosure = $Enclosure->GatewayIsAssembledInOtherEnclosure($gateway_filter);
        if( $gateway_is_assembled_in_other_enclosure ){
            $error = true;
            $message -> AddMessage(0, $gateway_sn." : Gateway installed in other enclosure: ".$gateway_is_assembled_in_other_enclosure[0]["enclosure_sn"]);
        } else {
            $enclosure_meters_data["gateway_id"] = $gateway_id;
        }
    } else {
        $error = true;
        $message -> AddMessage(0, "gateway_not_found");
    }
}



//check meters avaialability and generate meters array
$meter_count = 0;
$error = false;
$meters_data = array();
for($i=0; $i<6; $i++){
    $meter_sn = Helper::Post('meter_'.$i);
    if($meter_sn != null){
        $meter_id = $Enclosure->GetMeterId($meter_sn);
        if ($meter_id) { // && $enclosure_status == 3) {
            print $meter_id;
            $meter_count ++;
            $meter_filter = array();
            $meter_filter["enclosure_id"] = $enclosure_id;
            $meter_filter["meter_id"] = $meter_id;
            $meter_is_assembled_in_other_enclosure = $Enclosure->MeterIsAssembledInOtherEnclosure($meter_filter);
            if( $meter_is_assembled_in_other_enclosure ){
                $error = true;
                $message -> AddMessage(0, $meter_sn." : Meter installed in other enclosure: ".$meter_is_assembled_in_other_enclosure[0]["enclosure_sn"]);
            } else {
                $meters_data[] = array("meter_id"=>$meter_id, "sequence"=>$i+1);
            }
        } else {
            $error = true;
            $message -> AddMessage(0, "meter_not_found");
        }
    }
}



// $enclosure = $Enclosure->GetEnclosureDetails($enclosure_id);
// if($meter_count != $enclosure[0]["meter"]) {
//     $error = true;
//     $message -> AddMessage(0, "meter_count_not_compatible");
// }



//save
if (!$error) {

    $result = $Enclosure->AddEnclosureMeters($enclosure_meters_data, $meters_data, $USERID, true, $change_reason, $enclosure_config_id);

    $LogData = array();
    $LogData["USER_ID"] = $USERID;
    $LogData["TIMESTAMP"] = date("Y-m-d H:i:s");
    $LogData["MODULE_ID"] = 2;
    $LogData["OPERATION_ID"] = 12;
    $LogData["KEY_DATA"] = $enclosure_id;
    $LogData["NEW_DATA"] = json_encode($meters_data);
    $LogData["OLD_DATA"] = $User_Log->GetJsonData("enclosure_meters", ["enclosure_id"=>$enclosure_id]);
    $LogData["CRUD_OPERATION_ID"] = 1;
    $LogData["TABLE_NAME"] = "enclosure_meters";
    $LogData["RECORD_ID"] = $enclosure_id;
    $User_Log->AddRecord($LogData);

    if ($result != false) {
        //print $result;
        $message -> AddMessage($result, $Enclosure->Message);
    } else {
        //error
        $message -> AddMessage($Enclosure->State, $Enclosure->Message); //$Enclosure->Message //"error_saving_enclosure"
        //$message -> PrintJsonMessage();
    }
} else {
    //$message -> PrintJsonMessage();
}

