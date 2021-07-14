<?php
/*this page is used to get feed for doc out temp data-table*/
include_once realpath(__DIR__ . '/../..').'/include/checksession.php';
include_once realpath(__DIR__ . '/../..').'/include/checkpermission.php';
require_once realpath(__DIR__ . '/../..').'/class/Survey.class.php';
require_once realpath(__DIR__ . '/../..').'/class/Tree.php';
//$dictionary = new Dictionary ( $LANGUAGE );
//$dictionary->GetAllDictionary ();
$Survey = new Survey( );
$area_tree = new Tree("AREA_TREE");
$pageSize = 10;
$startingRecord = 0;
$totalRecords = 0;

/*Table data definition*/
$cols = array();
$cols[] = array("column"=>"NODE_ID");
$cols[] = array("column"=>"single_phase_consumers");
$cols[] = array("column"=>"three_phase_consumers");
$cols[] = array("column"=>"service_point_count");
$cols[] = array("column"=>"transformer_number_count");
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
$filter=array();

if( isset($_GET["area_path"] ) && $_GET["area_path"] != NULL ) {
    $filter["area_path"] = $_GET["area_path"];
}

if( isset($_GET["from_date"] ) && $_GET["from_date"] != NULL ) {
    $filter["from_date"] = $_GET["from_date"];
}

if( isset($_GET["to_date"] ) && $_GET["to_date"] != NULL ) {
    $filter["to_date"] = $_GET["to_date"]." 23:59:59";
}

if( isset($_GET["feeder_id"] ) && $_GET["feeder_id"] != NULL ) {
    $filter["feeder_id"] = $_GET["feeder_id"];
}

if( isset($_GET["transformer_number"] ) && $_GET["transformer_number"] != NULL ) {
    $filter["transformer_number"] = $_GET["transformer_number"];
}

$report_data = $Survey->GetServicePointSummaryByArea($filter, $order, $startingRecord, $pageSize, $totalRecords);
for ( $i=0; $i < count( $report_data ); $i++ ){
    //$report_data[$i]['NODE_ID'] = $area_tree->GetOrgPathString($report_data[$i]['NODE_ID']);
    $report_data[$i]['NODE_NAME'] = $area_tree->GetOrgPathString($report_data[$i]['NODE_ID']);
    $report_data[$i]["single_phase_consumers"] =number_format($report_data[$i]["single_phase_consumers"], 0, '.', ',');
    $report_data[$i]["three_phase_consumers"] =number_format($report_data[$i]["three_phase_consumers"], 0, '.', ',');
    $report_data[$i]["service_point_count"] =number_format($report_data[$i]["service_point_count"], 0, '.', ',');
    $report_data[$i]["transformer_number_count"] =number_format($report_data[$i]["transformer_number_count"], 0, '.', ',');
    $report_data[$i]['area'] = $report_data[$i]['NODE_ID'];
}
header('Content-type: application/json');
echo '{';
echo '"draw":'.$draw.',';
echo '"recordsTotal":'.$totalRecords.',';
echo '"recordsFiltered":'.$totalRecords.',';
echo '"data": ';
if(count($report_data) > 0){
    echo json_encode( $report_data );
}else{
	echo "{}";
}
echo "}";
?>
