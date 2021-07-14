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
require_once realpath(__DIR__ . '/../..').'/class/Tree.php';

$tree = new Tree("AREA_TREE");
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

if( isset($_REQUEST["Plant_No"]) && $_REQUEST["Plant_No"] != NULL ){
    $filter["meter.Plant_No"] = $_REQUEST["Plant_No"];
}

if( isset($_REQUEST["Serial_No"]) && $_REQUEST["Serial_No"] != NULL ){
    $filter["meter.Serial_No"] = $_REQUEST["Serial_No"];
}

if(isset($_REQUEST["from_date"]) && $_REQUEST["from_date"]!=NULL){
    $filter["from_date"] = $_REQUEST["from_date"];
}

if(isset($_REQUEST["to_date"]) && $_REQUEST["to_date"]!=NULL){
    $filter["to_date"] = $_REQUEST["to_date"];
}

$EnclosureInstallation = $Installation->GetMeterInstallation($filter, $order, $startingRecord, $pageSize, $totalRecords);

for($i=0; $i<count($EnclosureInstallation); $i++){
    $EnclosureInstallation[$i]["enclosure_type"] = $EnclosureInstallation[$i]["enclosure_type"]." [".$EnclosureInstallation[$i]["configuration_name"]."]";
    // if ($EnclosureInstallation[$i]["latitude"] != "") {
    //     $EnclosureInstallation[$i]["coordinates"] = $EnclosureInstallation[$i]["latitude"].",".$EnclosureInstallation[$i]["longitude"];
    // } else {
    //     $EnclosureInstallation[$i]["coordinates"] = "";
    // }

    $EnclosureInstallation[$i]["subdistrict"] = $EnclosureInstallation[$i]["governerate"] = "";
    if ($EnclosureInstallation[$i]["NODE_ID"] != "") {
        $node = $tree->GetNodeParent($EnclosureInstallation[$i]["NODE_ID"]);

        $EnclosureInstallation[$i]["subdistrict"] = $node[0]["NODE_NAME"];
        if ($node[0]["NODE_ID"] != "") {
            $parent_node = $tree->GetNodeParent($node[0]["NODE_ID"]);
            $EnclosureInstallation[$i]["governerate"] =  $parent_node[0]["NODE_NAME"];
        }
    }


}

$cols = array();
$cols[] = array("column"=>"Plant_No", "title"=>"Plant No.");
$cols[] = array("column"=>"Serial_No", "title"=>"Serial No.");
$cols[] = array("column"=>"Model");
$cols[] = array("column"=>"latitude");
$cols[] = array("column"=>"longitude");
$cols[] = array("column"=>"governerate", "title"=>"Grand Parent Zone");
$cols[] = array("column"=>"subdistrict", "title"=>"Parent Zone");
$cols[] = array("column"=>"NODE_NAME", "title"=>"Zone");
$cols[] = array("column"=>"station");
$cols[] = array("column"=>"feeder");
$cols[] = array("column"=>"point_id");
$cols[] = array("column"=>"enclosure_sn");
$title = "METER_INSTALLATION_LIST";

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