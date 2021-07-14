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
$point_id = Helper::Request("point_id", true);
$data['problem_report_id'] = Helper::Request("problem_report_id", true);
$data['state'] = Helper::Request("state", true);
//$data['update_notes'] = Helper::Request("update_notes", true);
$data['update_user_id'] = $USERID;

$LogData = array();
$LogData["USER_ID"] = $USERID;
$LogData["TIMESTAMP"] = date("Y-m-d H:i:s");
$LogData["MODULE_ID"] = 3;
$LogData["OPERATION_ID"] = 2;
$LogData["KEY_DATA"] = $data['point_id'];
$LogData["NEW_DATA"] = json_encode($data);
$LogData["OLD_DATA"] = $User_Log->GetJsonData("installation_problem_report", ["problem_report_id"=>$data['problem_report_id']]);
$LogData["CRUD_OPERATION_ID"] = 2;
$LogData["TABLE_NAME"] = "installation_problem_report";
$LogData["RECORD_ID"] = $data['problem_report_id'];
$User_Log->AddRecord($LogData);

$result = $Installation -> EditInstallationProblem($data);


if($result != false){
    //print $result;
    // $filter["service_point.point_id"] = $point_id;
    // $ServicePoint = $Installation->GetServicePoint($filter);
    // print $ServicePoint[0]["installation_status_id"];
    $ServicePoint = $Installation -> GetServicePoint(array("service_point.point_id"=>$point_id));
    print $ServicePoint[0]["installation_status_id"];
} else {
    //echo $Installation->Message;
    //error
    $message -> AddMessage($Installation->State, $Installation->Message);
    //$message -> PrintJsonMessage();
}
?>
