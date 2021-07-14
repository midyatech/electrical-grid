<?php
include_once realpath(__DIR__ . '/../..').'/include/checkpermission.php';
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
$data['point_id'] = Helper::Request("point_id", true);
$data['installation_problem_id'] = Helper::Request("installation_problem_id", true);
$data['create_notes'] = Helper::Request("create_notes", true);
$data['create_user_id'] = $USERID;

$result = $Installation -> AddInstallationProblem($data);

$LogData = array();
$LogData["USER_ID"] = $USERID;
$LogData["TIMESTAMP"] = date("Y-m-d H:i:s");
$LogData["MODULE_ID"] = 3;
$LogData["OPERATION_ID"] = 1;
$LogData["KEY_DATA"] = $data['point_id'];
$LogData["NEW_DATA"] = json_encode($data);
$LogData["OLD_DATA"] = "";
$LogData["CRUD_OPERATION_ID"] = 1;
$LogData["TABLE_NAME"] = "installation_problem_report";
$LogData["RECORD_ID"] = $result;
$User_Log->AddRecord($LogData);

if($result != false){
    //print $result;
    // $filter["service_point.point_id"] = $point_id;
    // $ServicePoint = $Installation->GetServicePoint($filter);
    // print $ServicePoint[0]["installation_status_id"];
    print -2;
} else {
	//echo $Installation->Message;
	//error
	$message -> AddMessage($Installation->State, $Installation->Message);
	//$message -> PrintJsonMessage();
}
?>
