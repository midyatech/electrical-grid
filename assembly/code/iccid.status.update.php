<?php
include_once realpath(__DIR__ . '/../..').'/include/checkpermission.php';
require_once realpath(__DIR__ . '/../..') . '/include/settings.php';
require_once realpath(__DIR__ . '/../..') . '/class/Helper.php';
require_once realpath(__DIR__ . '/../..') . '/class/Assembly.class.php';
require_once realpath(__DIR__ . '/../..') . '/class/SystemMessage.php';
require_once realpath(__DIR__ . '/../..') . '/class/UserLog.class.php';

$Assembly = new Assembly();
$message = new SysetemMessage($LANGUAGE);
$User_Log = new User_Log();

$id = $_REQUEST['id'];
// $iccids = $_REQUEST['iccids'];
$order_id = $_REQUEST['order_id'];
$activation_date = $_REQUEST['activation_date'];

// $iccids = rtrim($iccids, ',');
// $iccids_arr = explode (",", $iccids);

// $iccid_string= '';
// $output_array = array();
// for($i=0; $i<count($iccids_arr); $i++) {
//     //$qoute_trim = rtrim(ltrim(rtrim(ltrim($iccids_arr[$i],'"'),'"'),"'"),"'");

//     // $iccids_arr[$i] = str_replace('"&zwnj;"',"",$iccids_arr[$i]);
//     // $iccids_arr[$i] = str_replace("'","",$iccids_arr[$i]);
//     // $iccids_arr[$i] = str_replace("\"","",$iccids_arr[$i]);
//     // $iccids_arr[$i] = str_replace("&nbsp;","",$iccids_arr[$i]);
//     // $iccids_arr[$i] = str_replace("\r","",$iccids_arr[$i]);
//     // $iccids_arr[$i] = str_replace("\n","",$iccids_arr[$i]);
//     // $iccids_arr[$i] = trim($iccids_arr[$i]);
//     $iccids_arr[$i] = preg_replace("/[^0-9]/", "", $iccids_arr[$i]);

//     if ($iccids_arr[$i] != "") {
//         $output_array[] = "'$iccids_arr[$i]'";
//     }
// }


// // $no_linebrakes_string = preg_replace( "/\r|\n/", "", $iccid_string);
// // $final_iccids_string = rtrim($no_linebrakes_string, ',');

// $final_iccids_string = implode(",", $output_array);


$result = $Assembly->UpdateIccidStatus($id, $activation_date, $order_id);

$LogData = array();
$LogData["USER_ID"] = $USERID;
$LogData["TIMESTAMP"] = date("Y-m-d H:i:s");
$LogData["MODULE_ID"] = 2;
$LogData["OPERATION_ID"] = 14;
$LogData["KEY_DATA"] = $order_id;
$LogData["NEW_DATA"] = "{assembly_order_id:$order_id,activation_date:$activation_date,status_id:$id}";
$LogData["OLD_DATA"] = $User_Log->GetJsonData("assembly_order", ["assembly_order_id"=>$order_id]);
$LogData["CRUD_OPERATION_ID"] = 2;
$LogData["TABLE_NAME"] = "assembly_order";
$LogData["RECORD_ID"] = $order_id;
$User_Log->AddRecord($LogData);

if ($result != false) {
	//print $result;
} else {
	//echo $Assembly->Message;
	//error
	$message->AddMessage($Assembly->State, $Assembly->Message);
	//$message -> PrintJsonMessage();
}


