<?php

//include_once realpath(__DIR__ . '/../..').'/include/checkpermission.php';
require_once realpath(__DIR__ . '/../..') . '/class/SystemMessage.php';
require_once realpath(__DIR__ . '/../..') . '/class/Installation.class.php';
require_once realpath(__DIR__ . '/../..') . '/class/Helper.php';
include_once realpath(__DIR__ . '/../..') . '/include/checksession.php';
require_once realpath(__DIR__ . '/../..') . '/include/settings.php';
require_once realpath(__DIR__ . '/../..') . '/class/UserLog.class.php';
$Installation = new Installation();
$message = New SysetemMessage($LANGUAGE);
$User_Log = new User_Log();

$data =array();
$point_id = Helper::Request("point_id", true);
$data['point_id'] = $point_id;
$data['enclosure_id']= Helper::Request("enclosure_id", true);
$data['transformer_id']= Helper::Request("transformer_id", true);
$data['user_id']= $USERID;

$result = $Installation -> AddInstalledPointEnclosure($data);



$LogData = array();
$LogData["USER_ID"] = $USERID;
$LogData["TIMESTAMP"] = date("Y-m-d H:i:s");
$LogData["MODULE_ID"] = 3;
$LogData["OPERATION_ID"] = 5;
$LogData["KEY_DATA"] = $point_id;
$LogData["NEW_DATA"] = json_encode($data);
$LogData["OLD_DATA"] = "";
$LogData["CRUD_OPERATION_ID"] = 1;
$LogData["TABLE_NAME"] = "installed_point_enclosure";
$LogData["RECORD_ID"] = $result;
$User_Log->AddRecord($LogData);



if($result){
    //print $result;
    $filter["service_point.point_id"] = $point_id;
    $ServicePoint = $Installation->GetServicePoint($filter);
    print $ServicePoint[0]["installation_status_id"];
}else{
	//echo $Installation->Message;
	//error
	$message -> AddMessage($Installation->State, $Installation->Message);
	//$message -> PrintJsonMessage();
}
?>
