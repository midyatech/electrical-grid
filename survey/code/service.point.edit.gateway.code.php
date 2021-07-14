<?php
include_once realpath(__DIR__ . '/../..').'/include/checkpermission.php';
require_once realpath(__DIR__ . '/../..') . '/class/SystemMessage.php';
require_once realpath(__DIR__ . '/../..') . '/class/Survey.class.php';
require_once realpath(__DIR__ . '/../..') . '/class/Helper.php';
include_once realpath(__DIR__ . '/../..') . '/include/checksession.php';
require_once realpath(__DIR__ . '/../..') . '/include/settings.php';
$survey = new Survey();
$message = New SysetemMessage($LANGUAGE);

$service_point_data =array();
$service_point_data['point_id']= Helper::Request("point_id", true);
$service_point_data['needs_gateway']= Helper::Request("gateway", true);

$result = $survey -> EditServicePoint($service_point_data);

if($result != false){
	//print $result;
}else{
	//echo $survey->Message;
	//error
	$message -> AddMessage($survey->State, $survey->Message);
	//$message -> PrintJsonMessage();
}
?>
