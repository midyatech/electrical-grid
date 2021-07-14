<?php
//ob_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once realpath(__DIR__ . '/../..').'/include/settings.php';
include_once realpath(__DIR__ . '/../..').'/include/checksession.php';
include_once realpath(__DIR__ . '/../..').'/include/checkpermission.php';
require_once realpath(__DIR__ . '/../..').'/class/ExcelHelper.php';
require_once realpath(__DIR__ . '/../..').'/class/Installation.class.php';
require_once realpath(__DIR__ . '/../..') . '/class/Tree.php';

$excel = new Excel($LANGUAGE);
$dictionary = new Dictionary ( $LANGUAGE );
$dictionary->GetAllDictionary ();
$options = array("class"=>"form-control");
$Installation = new Installation( );
$area_tree = new Tree("AREA_TREE");


//filters
$filter = array();

if(isset($_REQUEST["area_path"]) && $_REQUEST["area_path"]!=NULL){
    $filter["area_path"] = $_REQUEST["area_path"];
}

if(isset($_REQUEST["station_id"]) && $_REQUEST["station_id"]!=NULL){
    $filter["t.station_id"] = $_REQUEST["station_id"];
}

if(isset($_REQUEST["feeder_id"]) && $_REQUEST["feeder_id"]!=NULL){
    $filter["t.feeder_id"] = $_REQUEST["feeder_id"];
}

if(isset($_REQUEST["transformer_id"]) && $_REQUEST["transformer_id"]!=NULL){
    $filter["line.transformer_id"] = $_REQUEST["transformer_id"];
}

if( isset($_REQUEST["enclosure_sn"]) && $_REQUEST["enclosure_sn"] != NULL ){
    $filter["enclosure_sn"] = $_REQUEST["enclosure_sn"];
}

if( isset($_REQUEST["gateway_sn"]) && $_REQUEST["gateway_sn"] != NULL ){
    $filter["gateway_sn"] = $_REQUEST["gateway_sn"];
}

if( isset($_REQUEST["meter_sn"]) && $_REQUEST["meter_sn"] != NULL ){
    $filter["meter_sn"] = $_REQUEST["meter_sn"];
}

if(isset($_REQUEST["ICCID"]) && $_REQUEST["ICCID"] != ""){
    $filter["ICCID"] = $_REQUEST["ICCID"];
}

if(isset($_REQUEST["from_date"]) && $_REQUEST["from_date"]!=NULL){
    $filter["from_date"] = $_REQUEST["from_date"];
}

if(isset($_REQUEST["to_date"]) && $_REQUEST["to_date"]!=NULL){
    $filter["to_date"] = $_REQUEST["to_date"];
}

if(isset($_REQUEST["activation_date"]) && $_REQUEST["activation_date"]!=NULL){
    $filter["activation_date"] = $_REQUEST["activation_date"];
}

if(isset($_REQUEST["simcard_status_id"]) && $_REQUEST["simcard_status_id"] != ""){
    $filter["simcard_status_id"] = $_REQUEST["simcard_status_id"];
}


$EnclosureInstallation = $Installation->GetEnclosureInstallation($filter, $order, $startingRecord, $pageSize, $totalRecords);


$cols = array();
$cols[] = array("column"=>"installed_time");
$cols[] = array("column"=>"station");
$cols[] = array("column"=>"feeder");
$cols[] = array("column"=>"NODE_NAME");
$cols[] = array("column"=>"point_id");
$cols[] = array("column"=>"latitude");
$cols[] = array("column"=>"longitude");
$cols[] = array("column"=>"transformer_number");
$cols[] = array("column"=>"enclosure_type");
$cols[] = array("column"=>"enclosure_sn");
$cols[] = array("column"=>"gateway_sn");
$cols[] = array("column"=>"Meter1");
$cols[] = array("column"=>"Meter2");
$cols[] = array("column"=>"Meter3");
$cols[] = array("column"=>"Meter4");
$cols[] = array("column"=>"Meter5");
$cols[] = array("column"=>"Meter6");
$title = "ENCLOSURE_INSTALLATION_LIST";

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
$excel->DrawExcelTable($EnclosureInstallation, $cols);
$excel->SaveExcelFile($title."_".date("Ymd"));

?>