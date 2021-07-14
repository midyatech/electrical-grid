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

$service_point_data['station_id']= Helper::Post("station_id", true);
$service_point_data['feeder_id']= Helper::Post("feeder_id", true);
$service_point_data['capacity_id']= Helper::Post("capacity_id", true);
$service_point_data['transformer_number']= Helper::Post("transformer_number", true);
$service_point_data['transformer_type_id']= Helper::Post("transformer_type_id", true);
$service_point_data['transformer_privacy_id']= Helper::Post("transformer_privacy_id", true);

$area_id= Helper::Post("area_id", true);

$result = $survey -> EditServicePoint($service_point_data);

if($result != false){
    header("Location: ../service_point_area_detail.php?area=".$area_id);
}else{
	//error
	$message -> AddMessage($survey->State, $survey->Message);
	echo $survey->Message;
	//$message -> PrintJsonMessage();
}
?>
