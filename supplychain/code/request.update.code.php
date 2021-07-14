<?php
require_once realpath(__DIR__ . '/../..').'/include/checkpermission.php';
require_once realpath(__DIR__ . '/../..') . '/class/SystemMessage.php';
require_once realpath(__DIR__ . '/../..') . '/class/Helper.php';
require_once realpath(__DIR__ . '/../..') . '/include/checksession.php';
require_once realpath(__DIR__ . '/../..') . '/include/settings.php';
require_once realpath(__DIR__ . '/../..') . '/class/SupplyChain.class.php';
// require_once realpath(__DIR__ . '/../..') . '/class/UserLog.class.php';
// $User_Log = new User_Log();

$sc = new SupplyChain();
$message = New SysetemMessage($LANGUAGE);

$data = array();
$data["source_warehouse_id"] = Helper::post("source_warehouse_id");
$data["destination_warehouse_id"] = Helper::post("destination_warehouse_id");
$data["estimated_receive_time"] = Helper::post("estimated_receive_time");
$data["request_type_id"] = Helper::post("request_type_id");
$data["request_reason_id"] = Helper::post("request_reason_id");
$data["request_time"] = date("Y-m-d H:i:s");

switch($data["request_type_id"]) {
    case 1:
        $data["is_confirmed"] = 0;
        break;
    case 2:
        $data["is_confirmed"] = 0;
        break;
    case 3:
        $data["is_confirmed"] = 0;
        break;
    case 4:
        $data["is_confirmed"] = 0;
        break;
    default:
        $data["is_confirmed"] = 0;
        break;
}

if(Helper::post("request_id", true)) {
    $id = $sc->UpdateRequest($data, ["request_id"=>Helper::post("request_id", true)]);
} else {
    $id = $sc->AddRequest($data);
}

if ($result != false) {
	//print $result;
} else {
	//echo $Assembly->Message;
	//error
	$message->AddMessage($Assembly->State, $Assembly->Message);
	//$message -> PrintJsonMessage();
}
header("Location: ../dashboard.php?id=$id");
?>
