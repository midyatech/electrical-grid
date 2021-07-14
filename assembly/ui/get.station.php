<?php
include_once realpath(__DIR__ . '/../..').'/include/checksession.php';
//include_once realpath(__DIR__ . '/../..').'/include/checkpermission.php';
require_once realpath(__DIR__ . '/../..').'/include/settings.php';
require_once realpath(__DIR__ . '/../..').'/class/Assembly.class.php';

$Assembly = new Assembly();

if(isset($_GET["area_id"]) && $_GET["area_id"] != "")
{
    $area_id = $_GET["area_id"];

    $StationArr = $Assembly->GetStationByArea($area_id);
    if(count($StationArr) > 0){
        $data = array();
        ob_clean();
        header('Content-type: application/json');
        echo json_encode( $StationArr );
    }
}
?>
