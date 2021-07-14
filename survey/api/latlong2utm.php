<?php
/*this page is used to get feed for doc out temp data-table*/
include_once realpath(__DIR__ . '/../..').'/include/checksession.php';
require_once realpath(__DIR__ . '/../..').'/class/coordinates.php';

$lat = $long = "";

if(isset($_REQUEST["lat"]) && $_REQUEST["lat"] != "" && isset($_REQUEST["long"]) && $_REQUEST["long"] != "")
    $lat = $_REQUEST["lat"];
    $long = $_REQUEST["long"];

    $utm = ll2utm($lat, $long);

    echo $utm["x"].", ".$utm["y"];

?>
