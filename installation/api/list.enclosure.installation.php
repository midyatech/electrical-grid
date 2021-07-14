<?php
/*this page is used to get feed for doc out temp data-table*/
require_once realpath(__DIR__ . '/../..').'/include/settings.php';
include_once realpath(__DIR__ . '/../..').'/include/checksession.php';
include_once realpath(__DIR__ . '/../..').'/include/checkpermission.php';
require_once realpath(__DIR__ . '/../..').'/class/Installation.class.php';
require_once realpath(__DIR__ . '/../..').'/class/Dictionary.php';
$dictionary = new Dictionary ( $LANGUAGE );
$dictionary->GetAllDictionary ();
$Installation = new Installation( );

$pageSize = 10;
$startingRecord = 0;
$totalRecords = 0;

/*Table data definition*/
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
$cols[] = array("column"=>"iccides");
$cols[] = array("column"=>"ip_addresses");
/*End Table data definition*/

/*Datatables params*/
if(isset($_REQUEST["start"])){
    $startingRecord = $_REQUEST["start"];
}
if(isset($_REQUEST["length"])){
    $pageSize = $_REQUEST["length"];
}
if(isset($_REQUEST["draw"])){
    $draw = $_REQUEST["draw"];
}else{
    $draw = 1;
}

$order = array();
if(isset($_REQUEST["order"])){
    if($_REQUEST["order"][0]["dir"] == "asc"){
        $direction = "asc";
    }else{
        $direction = "desc";
    }
    $sortColIndex = $_REQUEST["order"][0]["column"];
    $sortCol = $cols[$sortColIndex]["column"];
    $order[$sortCol] = $direction;
}

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

if(isset($_REQUEST["ip_address"]) && $_REQUEST["ip_address"] != ""){
    $filter["ip_address"] = $_REQUEST["ip_address"];
}
/*
if(isset($_REQUEST["activation_date"]) && $_REQUEST["activation_date"]!=NULL){
    $filter["activation_date"] = $_REQUEST["activation_date"];
}

if(isset($_REQUEST["simcard_status_id"]) && $_REQUEST["simcard_status_id"] != ""){
    $filter["simcard_status_id"] = $_REQUEST["simcard_status_id"];
}
*/
$EnclosureInstallation = $Installation->GetEnclosureInstallation($filter, $order, $startingRecord, $pageSize, $totalRecords);

for($i=0; $i<count($EnclosureInstallation); $i++){
    $EnclosureInstallation[$i]["enclosure_type"] = $EnclosureInstallation[$i]["enclosure_type"]." [".$EnclosureInstallation[$i]["configuration_name"]."]";
}

header('Content-type: application/json');
echo '{';
echo '"draw":'.$draw.',';
echo '"recordsTotal":'.$totalRecords.',';
echo '"recordsFiltered":'.$totalRecords.',';
echo '"data": ';
if(count($EnclosureInstallation) > 0){
    echo json_encode( $EnclosureInstallation );
}else{
    echo "{}";
}
echo "}";
?>
