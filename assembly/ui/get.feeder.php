<?php
include_once realpath(__DIR__ . '/../..').'/include/checksession.php';
//include_once realpath(__DIR__ . '/../..').'/include/checkpermission.php';
require_once realpath(__DIR__ . '/../..').'/include/settings.php';
require_once realpath(__DIR__ . '/../..').'/class/Assembly.class.php';

$Assembly = new Assembly();

$station_id = NULL;
if(isset($_GET["station_id"]) && $_GET["station_id"] != ""){
    $station_id = $_GET["station_id"];
}
    $FeederArr = $Assembly->GetFeederByStation($station_id);
    if(count($FeederArr) > 0){
        $data = array();
        ob_clean();
        header('Content-type: application/json');
        echo json_encode( $FeederArr );
    }
?>
