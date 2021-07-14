<?php
/*this page is used to get feed for doc out temp data-table*/
require_once realpath(__DIR__ . '/../..').'/include/settings.php';
include_once realpath(__DIR__ . '/../..').'/include/checksession.php';
include_once realpath(__DIR__ . '/../..').'/include/checkpermission.php';
require_once realpath(__DIR__ . '/../..').'/class/Enclosure.class.php';
require_once realpath(__DIR__ . '/../..').'/class/Dictionary.php';
$dictionary = new Dictionary ( $LANGUAGE );
$dictionary->GetAllDictionary ();
$Enclosure = new Enclosure( );

$pageSize = 10;
$startingRecord = 0;
$totalRecords = 0;

/*Table data definition*/
$cols = array();
$cols[] = array("column"=>"gateway_sn");
$cols[] = array("column"=>"enclosure_sn");
$cols[] = array("column"=>"ICCID");
$cols[] = array("column"=>"simcard_status");
$cols[] = array("column"=>"activation_date");
$cols[] = array("column"=>"ip_address");
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
/*End Datatables params*/

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

$AllGateway = $Enclosure->GetGateway($filter, $order, $startingRecord, $pageSize, $totalRecords);

header('Content-type: application/json');
echo '{';
echo '"draw":'.$draw.',';
echo '"recordsTotal":'.$totalRecords.',';
echo '"recordsFiltered":'.$totalRecords.',';
echo '"data": ';
if(count($AllGateway) > 0){
    echo json_encode( $AllGateway );
}else{
    echo "{}";
}
echo "}";
?>
