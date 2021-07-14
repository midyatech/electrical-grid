<?php
//ob_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once realpath(__DIR__ . '/../..').'/include/settings.php';
include_once realpath(__DIR__ . '/../..').'/include/checksession.php';
include_once realpath(__DIR__ . '/../..').'/include/checkpermission.php';
require_once realpath(__DIR__ . '/../..').'/class/ExcelHelper.php';
require_once realpath(__DIR__ . '/../..').'/class/Enclosure.class.php';
require_once realpath(__DIR__ . '/../..').'/class/Dictionary.php';

$excel = new Excel($LANGUAGE);

$dictionary = new Dictionary ( $LANGUAGE );
$dictionary->GetAllDictionary ();
$Enclosure = new Enclosure( );


//filters
$filter = array();
if(isset($_REQUEST["from_date"]) && $_REQUEST["from_date"]!=NULL && isset($_REQUEST["to_date"]) && $_REQUEST["to_date"]!=NULL){
    $filter["enclosure.timestamp#1"] = array("Operator"=>">=","Value"=>$_REQUEST["from_date"]." 00:00:01", "Type"=>"mytext");
    $filter["enclosure.timestamp#2"] = array("Operator"=>"<", "Value"=>$_REQUEST["to_date"]." 23:59:59", "Type"=>"mytext");
}
if(isset($_REQUEST["assembly_order"]) && $_REQUEST["assembly_order"]!=NULL){
    $filter["assembly_order.assembly_order_id"] = $_REQUEST["assembly_order"];
}
if(isset($_REQUEST["transformer_number"]) && $_REQUEST["transformer_number"]!=NULL){
    $filter["service_point.transformer_number"] = $_REQUEST["transformer_number"];
}
if(isset($_REQUEST["simcard_status_id"]) && $_REQUEST["simcard_status_id"] != ""){
    $filter["simcard_status_id"] = $_REQUEST["simcard_status_id"];
}
if(isset($_REQUEST["enclosure_sn"]) && $_REQUEST["enclosure_sn"]!=NULL){
    $filter["enclosure.enclosure_sn"] = $_REQUEST["enclosure_sn"];
}
if(isset($_REQUEST["gateway_id"]) && $_REQUEST["gateway_id"]!=NULL){
    $filter["gateway_sn"] = $_REQUEST["gateway_id"];
}
if(isset($_REQUEST["includes_gateway"]) && $_REQUEST["includes_gateway"]!=NULL){
    $filter["enclosure_type.gateway"] = $_REQUEST["includes_gateway"];
}
if(isset($_REQUEST["enclosure_type"]) && $_REQUEST["enclosure_type"]!=NULL){
    $filter["enclosure_type.meter_type_id"] = $_REQUEST["enclosure_type"];
}


$data = $Enclosure->GetEnclosures($filter, $order, $startingRecord, $pageSize, $totalRecords);


$cols = array();
$cols[] = array("column"=>"enclosure_sn");
$cols[] = array("column"=>"gateway_sn");
$cols[] = array("column"=>"enclosure_type");
$cols[] = array("column"=>"configuration_name");
$cols[] = array("column"=>"assembly_order_code");
$cols[] = array("column"=>"transformer_number");
$cols[] = array("column"=>"transformer_generated_number");
$cols[] = array("column"=>"timestamp");
$cols[] = array("column"=>"meter_count");
$cols[] = array("column"=>"NAME");
$title = "ENCLOSURE_LIST";

// if($data!=null){
//     for($i = 0; $i<count($data); $i++)
//     {
//         $data[$i]["ICCID"] = $data[$i]["ICCID"]." ";
//     }
// }
// print_r($data);
// die();
ob_end_clean();
// $excel->DrawHeader($dictionary->GetValue($title), $filter_text);
$excel->DrawExcelTable($data, $cols);
$excel->SaveExcelFile($title."_".date("Ymd"));

?>
