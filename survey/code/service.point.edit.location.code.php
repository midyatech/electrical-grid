<?php
include_once realpath(__DIR__ . '/../..').'/include/checkpermission.php';
require_once realpath(__DIR__ . '/../..') . '/class/SystemMessage.php';
require_once realpath(__DIR__ . '/../..') . '/class/Survey.class.php';
require_once realpath(__DIR__ . '/../..') . '/class/coordinates.php';
require_once realpath(__DIR__ . '/../..') . '/class/Helper.php';
include_once realpath(__DIR__ . '/../..') . '/include/checksession.php';
require_once realpath(__DIR__ . '/../..') . '/include/settings.php';
$survey = new Survey();
$message = New SysetemMessage($LANGUAGE);

$service_point_data =array();
$service_point_data['point_id']= Helper::Post("point_id", true);
$service_point_data['longitude']= Helper::Post("longitude", true);
$service_point_data['latitude']= Helper::Post("latitude", true);
if( $service_point_data["latitude"] > 0 &&  $service_point_data["longitude"] > 0 ) {
	$utm = ll2utm($service_point_data["latitude"], $service_point_data["longitude"]);
	$service_point_data['x']= $utm["x"];
	$service_point_data['y']= $utm["y"];
}

$result = $survey -> EditServicePointLocation($service_point_data);
if($result != false){
	//print $result;
}else{
	//echo $survey->Message;
	//error
	$message -> AddMessage($survey->State, $survey->Message);
	//$message -> PrintJsonMessage();
}
?>
