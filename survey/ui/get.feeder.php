<?php
// error_reporting(E_ALL);
// ini_set('display_errors', 1);
include_once realpath(__DIR__ . '/../..').'/include/checksession.php';
require_once realpath(__DIR__ . '/../..').'/include/settings.php';
require_once realpath(__DIR__ . '/../..').'/class/Survey.class.php';

$Survey = new Survey();

$station_id = $_GET["station_id"];
$area_id = $_GET["area_id"];

    $FeederArr = $Survey->GetFeeders($area_id, $station_id);
    if(count($FeederArr) > 0){
        $data = array();
        ob_clean();
        header('Content-type: application/json');
        echo json_encode( $FeederArr );
    }
?>
