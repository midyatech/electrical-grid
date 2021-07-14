<?php
//ob_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once realpath(__DIR__ . '/../..').'/include/settings.php';
include_once realpath(__DIR__ . '/../..').'/include/checksession.php';
include_once realpath(__DIR__ . '/../..').'/include/checkpermission.php';
require_once realpath(__DIR__ . '/../..').'/class/ExcelHelper.php';
require_once realpath(__DIR__ . '/../..').'/class/Assembly.class.php';
require_once realpath(__DIR__ . '/../..').'/class/Dictionary.php';

$excel = new Excel($LANGUAGE);

$dictionary = new Dictionary ( $LANGUAGE );
$dictionary->GetAllDictionary ();
$assembly = new Assembly( );


//filters
$filter = array();

if(isset($_REQUEST["from_date"]) && $_REQUEST["from_date"]!=NULL && isset($_REQUEST["to_date"]) && $_REQUEST["to_date"]!=NULL){
    $filter["gateway_sn.timestamp#1"] = array("Operator"=>">=","Value"=>$_REQUEST["from_date"]." 00:00:01", "Type"=>"mytext");
    $filter["gateway_sn.timestamp#2"] = array("Operator"=>"<", "Value"=>$_REQUEST["to_date"]." 23:59:59", "Type"=>"mytext");
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


$cols = array();
$cols[] = array("column"=>"Model");
$cols[] = array("column"=>"Serial_No");
$cols[] = array("column"=>"STS_No");
$cols[] = array("column"=>"IMEI");
$cols[] = array("column"=>"ICCID");
$cols[] = array("column"=>"ip_address");
$cols[] = array("column"=>"activation_date");
$cols[] = array("column"=>"simcard_status");

$title = "ICCID_LIST";


//filters
$filter = array();

if(isset($_REQUEST["Model"]) && $_REQUEST["Model"] != ""){
    $filter["Model"] = $_REQUEST["Model"];
}

if(isset($_REQUEST["Serial_No"]) && $_REQUEST["Serial_No"] != ""){
    $filter["Serial_No"] = $_REQUEST["Serial_No"];
}

if(isset($_REQUEST["STS_No"]) && $_REQUEST["STS_No"] != ""){
    $filter["STS_No"] = $_REQUEST["STS_No"];
}

if(isset($_REQUEST["IMEI"]) && $_REQUEST["IMEI"] != ""){
    $filter["IMEI"] = $_REQUEST["IMEI"];
}

if(isset($_REQUEST["ip_address"]) && $_REQUEST["ip_address"] != ""){
    $filter["ip_address"] = $_REQUEST["ip_address"];
}

if(isset($_REQUEST["ICCID_pattern"]) && $_REQUEST["ICCID_pattern"] != ""){
    $iccids = $_REQUEST["ICCID_pattern"];
    $iccids = rtrim($iccids, ',');
    $iccids_arr = explode (",", $iccids);

    $iccid_string = '';
    $output_array = array();
    
    for($i=0; $i<count($iccids_arr); $i++) {
        $iccids_arr[$i] = preg_replace("/[^0-9]/", "", $iccids_arr[$i]);
        
        if ($iccids_arr[$i] != "") {
            $output_array[] = "'$iccids_arr[$i]'";
        }
    }
    $final_iccids_string = implode(",", $output_array);
    // print 'api'.$final_iccids_string;
    $filter["ICCID_pattern"] = $final_iccids_string;
}

if(isset($_REQUEST["simcard_status_id"]) && $_REQUEST["simcard_status_id"] != ""){
    $filter["simcard_status_id"] = $_REQUEST["simcard_status_id"];
}

if(isset($_REQUEST["activation_date"]) && $_REQUEST["activation_date"] != ""){
    $filter["activation_date"] = $_REQUEST["activation_date"];
}

if(isset($_REQUEST["from_date"]) && $_REQUEST["from_date"]!=NULL){
    $filter["from_date"] = $_REQUEST["from_date"];
}

if(isset($_REQUEST["to_date"]) && $_REQUEST["to_date"]!=NULL){
    $filter["to_date"] = $_REQUEST["to_date"];
}

if(isset($_REQUEST["status"]) && $_REQUEST["status"]!=NULL){
    $filter["status"] = $_REQUEST["status"];
}

$data = $assembly->getICCID($filter, $order, $startingRecord, $pageSize, $totalRecords);

if(count($data) > 0){
    for($i=0; $i<count($data); $i++){
        $data[$i]["Serial_No"] = $data[$i]['Serial_No']." ";
        $data[$i]["STS_No"] = $data[$i]['STS_No']." ";
        $data[$i]["IMEI"] = $data[$i]['IMEI']." ";
        $data[$i]["ICCID"] = $data[$i]['ICCID']." ";
        $data[$i]["ip_address"] = $data[$i]['ip_address']." ";
        $data[$i]["activation_date"] = $data[$i]['activation_date']." ";
        $data[$i]["simcard_status"] = $data[$i]['simcard_status'];
    }
}

// print_r($data);
// die();
ob_end_clean();
// $excel->DrawHeader($dictionary->GetValue($title), $filter_text);
$excel->DrawExcelTable($data, $cols);
$excel->SaveExcelFile($title."_".date("Ymd"));

?>