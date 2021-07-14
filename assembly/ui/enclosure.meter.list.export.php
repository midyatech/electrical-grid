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


$filter = array();
if(isset($_REQUEST["from_date"]) && $_REQUEST["from_date"]!=NULL){
    $filter["from_date"] = array("Operator"=>">=","Value"=>$_REQUEST["from_date"]." 00:00:01", "Type"=>"mytext");
}
if(isset($_REQUEST["to_date"]) && $_REQUEST["to_date"]!=NULL){
    $filter["to_date"] = array("Operator"=>"<", "Value"=>$_REQUEST["to_date"]." 23:59:59", "Type"=>"mytext");
}
if(isset($_REQUEST["enclosure_sn"]) && $_REQUEST["enclosure_sn"]!=NULL){
    $filter["enclosure_sn"] = $_REQUEST["enclosure_sn"];
}
if(isset($_REQUEST["gateway_sn"]) && $_REQUEST["gateway_sn"]!=NULL){
    $filter["gateway_sn"] = $_REQUEST["gateway_sn"];
}
if(isset($_REQUEST["meter_sn"]) && $_REQUEST["meter_sn"]!=NULL){
    $filter["meter_sn"] = $_REQUEST["meter_sn"];
}


$data = $Enclosure->GetEnclosureMeters($filter);

$cols = array();
$cols[] = array("column"=>"enclosure_sn");
$cols[] = array("column"=>"gateway_sn");
$cols[] = array("column"=>"meter_sn");
$cols[] = array("column"=>"timestamp");

$title = "ENCLOSURE_METER_LIST";

// if($data!=null){
//     for($i = 0; $i<count($data); $i++)
//     {
//         $data[$i]["ICCID"] = $data[$i]["ICCID"]." ";
//     }
// }

ob_end_clean();
// $excel->DrawHeader($dictionary->GetValue($title), $filter_text);
$excel->DrawExcelTable($data, $cols);
$excel->SaveExcelFile($title."_".date("Ymd"));
?>
