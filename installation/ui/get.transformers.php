<?php
include_once realpath(__DIR__ . '/../..').'/include/settings.php';
include_once realpath(__DIR__ . '/../..').'/include/checksession.php';
require_once realpath(__DIR__ . '/../..').'/class/Installation.class.php';
require_once realpath(__DIR__ . '/../..').'/class/HtmlHelper.php';

$Installation = new Installation();

$feeder_id = NULL;
if(isset($_GET["feeder_id"]) && $_GET["feeder_id"] != ""){
    $feeder_id = $_GET["feeder_id"];
    $filter["feeder_id"] = $feeder_id;
}

$filter["service_point.point_type_id"] = 4;
$filter["ponit_count"] = array("Operator"=>">","Value"=>1, "Type"=>"int");

$TransformerArr = $Installation->GetTransformerArr($filter);

if(count($TransformerArr) > 0){
    $data = array();
    ob_clean();
    header('Content-type: application/json');
    echo json_encode( $TransformerArr );
}
?>