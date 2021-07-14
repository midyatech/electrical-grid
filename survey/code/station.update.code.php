<?php
//include_once realpath(__DIR__ . '/../..').'/include/checkpermission.php';
require_once realpath(__DIR__ . '/../..') . '/class/SystemMessage.php';
require_once realpath(__DIR__ . '/../..') . '/class/Survey.class.php';
require_once realpath(__DIR__ . '/../..') . '/class/Helper.php';
include_once realpath(__DIR__ . '/../..') . '/include/checksession.php';
require_once realpath(__DIR__ . '/../..') . '/include/settings.php';

$survey = new Survey();
$message = New SysetemMessage($LANGUAGE);


$area_id = $_REQUEST["area_id"];
$station_id = $_REQUEST["station_id"];
$station = $_REQUEST["station"];

if ($station_id == "") {
    $survey->AddStation($area_id, $station);
} else {
    $survey->UpdateStation($station_id, $area_id, $station);
}
?>
