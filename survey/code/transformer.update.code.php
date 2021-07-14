<?php
include_once realpath(__DIR__ . '/../..').'/include/checkpermission.php';
require_once realpath(__DIR__ . '/../..') . '/class/SystemMessage.php';
require_once realpath(__DIR__ . '/../..') . '/class/Survey.class.php';
require_once realpath(__DIR__ . '/../..') . '/class/Helper.php';
include_once realpath(__DIR__ . '/../..') . '/include/checksession.php';
require_once realpath(__DIR__ . '/../..') . '/include/settings.php';
$survey = new Survey();
$message = New SysetemMessage($LANGUAGE);

$transformer_id= Helper::Request("transformer_id", true);
// $feeder_id= Helper::Request("feeder_id", true);
// $station_id= Helper::Post("station_id", true);
$point_data = array();
$point_data['station_id']= Helper::Post("station_id", true);
$point_data['feeder_id']= Helper::Post("feeder_id", true);
$point_data['capacity_id']= Helper::Post("capacity_id", true);
$point_data['transformer_number']= Helper::Post("transformer_number", true);
$point_data['transformer_type_id']= Helper::Post("transformer_type_id", true);
$point_data['transformer_privacy_id']= Helper::Post("transformer_privacy_id", true);



$result = $survey -> UpdateTransformerFeeder($transformer_id, $point_data);
if($result != false){
}else{
	//error
	$message -> AddMessage($survey->State, $survey->Message);
	echo $survey->Message;
	$message -> PrintJsonMessage();
}
?>
