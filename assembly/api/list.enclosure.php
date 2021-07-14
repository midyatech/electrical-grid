<?php
/*this page is used to get feed for doc out temp data-table*/
require_once realpath(__DIR__ . '/../..').'/include/settings.php';
include_once realpath(__DIR__ . '/../..').'/include/checksession.php';
//include_once realpath(__DIR__ . '/../..').'/include/checkpermission.php';
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


$allEnclosures = $Enclosure->GetEnclosures($filter, $order, $startingRecord, $pageSize, $totalRecords);

header('Content-type: application/json');
echo '{';
echo '"draw":'.$draw.',';
echo '"recordsTotal":'.$totalRecords.',';
echo '"recordsFiltered":'.$totalRecords.',';
echo '"data": ';
if(count($allEnclosures) > 0){
    echo json_encode( $allEnclosures );
}else{
    echo "{}";
}
echo "}";
?>
