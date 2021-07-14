<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include_once realpath(__DIR__ . '/../..').'/include/settings.php';
include_once realpath(__DIR__ . '/../..').'/include/checksession.php';
require_once realpath(__DIR__ . '/../..').'/class/Survey.class.php';
require_once realpath(__DIR__ . '/../..').'/class/HtmlHelper.php';

$Survey = new Survey();

// $feeder_id = NULL;
// if(isset($_GET["feeder_id"]) && $_GET["feeder_id"] != ""){
//     $feeder_id = $_GET["feeder_id"];
//     $filter["feeder_id"] = $feeder_id;
// }

$feeder_id = $_GET["feeder_id"];
$station_id = $_GET["station_id"];
$area_id = $_GET["area_id"];

// $TransformerArr = $Survey->GetTransformerArr($filter);
$TransformerArr = $Survey->GetTransformers($area_id, $station_id, $feeder_id);
if(count($TransformerArr) > 0){
    $data = array();
    ob_clean();
    header('Content-type: application/json');
    echo json_encode( $TransformerArr );
}
?>