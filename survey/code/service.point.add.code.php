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

if(isset($_REQUEST["area_id"]) && $_REQUEST["area_id"] != ""){
	$area_id = Helper::Post("area_id", true);
	$link = "?id=".$area_id;
} else {
	$area_id = $USERDIR;
	$link = "";
}

$service_point_data =array();
$service_point_data['point_id']= Helper::Post("point_id", true);
$service_point_data['type_id']= Helper::Post("type_id", true);
$service_point_data['single_phase']= Helper::Post("single_phase", true);
$service_point_data['three_phase']= Helper::Post("three_phase", true);
$service_point_data['accuracy']= Helper::Post("accuracy", true);
$service_point_data['longitude']= Helper::Post("longitude", true);
$service_point_data['latitude']= Helper::Post("latitude", true);

if( $service_point_data["latitude"] > 0 &&  $service_point_data["longitude"] > 0 ) {
	$utm = ll2utm($service_point_data["latitude"], $service_point_data["longitude"]);
	$service_point_data['x']= $utm["x"];
	$service_point_data['y']= $utm["y"];
}

$service_point_data['area_id']= $area_id;
$service_point_data['user_id']= $USERID;



$service_point_data['station_id']= Helper::Post("station_id", true);
$service_point_data['feeder_id']= Helper::Post("feeder_id", true);
$service_point_data['capacity_id']= Helper::Post("capacity_id", true);
$service_point_data['transformer_number']= Helper::Post("transformer_number", true);

$service_point_data['not_from_survey']= Helper::Post("not_from_survey", true);

$service_point_data['transformer_type_id']= Helper::Post("transformer_type_id", true);
$service_point_data['transformer_privacy_id']= Helper::Post("transformer_privacy_id", true);



$result = $survey -> AddServicePoint($service_point_data);

if(isset($_REQUEST["update_grid"]) && $_REQUEST["update_grid"] == "1"){
	if($result != false){
		print $result;
	}else{
		echo "err";
		//echo $survey->Message;
		//error
			// $message -> AddMessage($survey->State, $survey->Message);
			// $message -> PrintJsonMessage();
	}
} else {
	header("Location: ../add_survey.php".$link);
}


?>
