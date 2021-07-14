<?php
include_once realpath(__DIR__ . '/../..').'/include/checkpermission.php';
require_once realpath(__DIR__ . '/../..') . '/class/SystemMessage.php';
require_once realpath(__DIR__ . '/../..') . '/class/Survey.class.php';
require_once realpath(__DIR__ . '/../..') . '/class/Helper.php';
include_once realpath(__DIR__ . '/../..') . '/include/checksession.php';
require_once realpath(__DIR__ . '/../..') . '/include/settings.php';

$survey = new Survey();
$message = New SysetemMessage($LANGUAGE);


$feeder_id = $_REQUEST["id"];
$r = $survey->DeleteFeeder($feeder_id);
if (!$r) {
    $message -> AddMessage(1, $survey->Message);
    $message -> PrintJsonMessage();
}
?>
