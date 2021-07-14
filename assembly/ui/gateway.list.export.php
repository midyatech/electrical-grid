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

if(isset($_REQUEST["from_activation_date"]) && $_REQUEST["from_activation_date"]!=NULL && isset($_REQUEST["to_activation_date"]) && $_REQUEST["to_activation_date"]!=NULL){
    $filter["gateway_sn.activation_date#1"] = array("Operator"=>">=","Value"=>$_REQUEST["from_activation_date"]." 00:00:01", "Type"=>"mytext");
    $filter["gateway_sn.activation_date#2"] = array("Operator"=>"<", "Value"=>$_REQUEST["to_activation_date"]." 23:59:59", "Type"=>"mytext");
}

if(isset($_REQUEST["gateway_sn"]) && $_REQUEST["gateway_sn"]!=NULL){
    $filter["gateway_sn"] = $_REQUEST["gateway_sn"];
}

if(isset($_REQUEST["enclosure_sn"]) && $_REQUEST["enclosure_sn"]!=NULL){
    $filter["enclosure.enclosure_sn"] = $_REQUEST["enclosure_sn"];
}

if(isset($_REQUEST["ICCID"]) && $_REQUEST["ICCID"]!=NULL){
    $filter["gateway_sn.ICCID"] = $_REQUEST["ICCID"];
}

if(isset($_REQUEST["simcard_status_id"]) && $_REQUEST["simcard_status_id"]!=NULL){
    $filter["gateway_sn.simcard_status_id"] = $_REQUEST["simcard_status_id"];
}

if(isset($_REQUEST["ip_address"]) && $_REQUEST["ip_address"]!=NULL){
    $filter["gateway_sn.ip_address"] = $_REQUEST["ip_address"];
}

print_r($filter);
print $data = $Enclosure->GetGateway($filter);

$cols = array();
$cols[] = array("column"=>"gateway_sn");
$cols[] = array("column"=>"enclosure_sn");
$cols[] = array("column"=>"ICCID");
$cols[] = array("column"=>"simcard_status");
$cols[] = array("column"=>"activation_date");
$cols[] = array("column"=>"ip_address");
$title = "GATEWAY_LIST";

if($data!=null){
    for($i = 0; $i<count($data); $i++)
    {
        $data[$i]["ICCID"] = $data[$i]["ICCID"]." ";
    }
}
// print_r($data);
// die();
ob_end_clean();
// $excel->DrawHeader($dictionary->GetValue($title), $filter_text);
$excel->DrawExcelTable($data, $cols);
$excel->SaveExcelFile($title."_".date("Ymd"));

?>
