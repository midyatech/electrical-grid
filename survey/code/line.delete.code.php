<?php
//include_once realpath(__DIR__ . '/../..').'/include/checkpermission.php';
require_once realpath(__DIR__ . '/../..') . '/class/SystemMessage.php';
require_once realpath(__DIR__ . '/../..') . '/class/Survey.class.php';
require_once realpath(__DIR__ . '/../..') . '/class/Helper.php';
include_once realpath(__DIR__ . '/../..') . '/include/checksession.php';
require_once realpath(__DIR__ . '/../..') . '/include/settings.php';

$survey = new Survey();
$message = New SysetemMessage($LANGUAGE);

// $service_point_data =array();
// $service_point_data['point_id']= Helper::Post("point_id", true);
// $service_point_data['longitude']= Helper::Post("longitude", true);
// $service_point_data['latitude']= Helper::Post("latitude", true);
$line_id = $_REQUEST["id"];
$survey->RemoveLine($line_id);
?>
