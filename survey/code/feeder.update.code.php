<?php
//include_once realpath(__DIR__ . '/../..').'/include/checkpermission.php';
require_once realpath(__DIR__ . '/../..') . '/class/SystemMessage.php';
require_once realpath(__DIR__ . '/../..') . '/class/Survey.class.php';
require_once realpath(__DIR__ . '/../..') . '/class/Helper.php';
include_once realpath(__DIR__ . '/../..') . '/include/checksession.php';
require_once realpath(__DIR__ . '/../..') . '/include/settings.php';

$survey = new Survey();
$message = New SysetemMessage($LANGUAGE);


$feeder_id = $_REQUEST["feeder_id"];
$station_id = $_REQUEST["station_id"];
$feeder = $_REQUEST["feeder"];

if ($feeder_id == "") {
    $survey->AddFeeder($station_id, $feeder);
} else {
    $survey->UpdateFeeder($feeder_id, $station_id, $feeder);
}
?>
