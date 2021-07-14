<?php
/*this page is used to get feed for doc out temp data-table*/
include_once realpath(__DIR__ . '/../..').'/include/checksession.php';
include_once realpath(__DIR__ . '/../..').'/include/checkpermission.php';
require_once realpath(__DIR__ . '/../..').'/class/Survey.class.php';

//$dictionary = new Dictionary ( $LANGUAGE );
//$dictionary->GetAllDictionary ();
$Survey = new Survey( );


$pageSize = 10;
$startingRecord = 0;
$totalRecords = 0;

/*Table data definition*/
$cols = array();
//$cols[] = array("column"=>"sequence");
$cols[] = array("column"=>"number_of_consumers");
$cols[] = array("column"=>"single_phase_consumers");
$cols[] = array("column"=>"three_phase_consumers");
$cols[] = array("column"=>"service_point_count");

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
if(isset($_REQUEST["area_id"])&&$_REQUEST["area_id"]!=NULL){
    $filter["area_id"] = $_REQUEST["area_id"];
}
if( isset($_GET["from_date"] ) && $_GET["from_date"] != NULL ) {
    $filter["from_date"] = $_GET["from_date"];
}
if( isset($_GET["to_date"] ) && $_GET["to_date"] != NULL ) {
    $filter["to_date"] = $_GET["to_date"]." 23:59:59";
}
$report_data = $Survey->GetServicePointCount($filter,$order);
for($i=0; $i<count($report_data); $i++){
    $report_data[$i]["sequence"] = $i+1;
    $report_data[$i]["service_point_count"] =number_format($report_data[$i]["service_point_count"], 0, '.', ',');
    $report_data[$i]["number_of_consumers"] =number_format($report_data[$i]["number_of_consumers"], 0, '.', ',');
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
