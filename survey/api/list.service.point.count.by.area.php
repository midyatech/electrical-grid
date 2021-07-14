<?php
/*this page is used to get feed for doc out temp data-table*/
include_once realpath(__DIR__ . '/../..').'/include/settings.php';
include_once realpath(__DIR__ . '/../..').'/include/checksession.php';
require_once realpath(__DIR__ . '/../..').'/class/Survey.class.php';
require_once realpath(__DIR__ . '/../..').'/class/HtmlHelper.php';

$html = new HTML ( $LANGUAGE );
$dictionary = new Dictionary ( $LANGUAGE );
$dictionary->GetAllDictionary ();

$Survey = new Survey( );


$pageSize = 10;
$startingRecord = 0;
$totalRecords = 0;

/*Table data definition*/
$cols = array();
$cols[] = array("column"=>"number_of_consumers");
$cols[] = array("column"=>"single_phase_consumers");
$cols[] = array("column"=>"three_phase_consumers");
$cols[] = array("column"=>"service_point_count");
$cols[] = array("column"=>"point_type");

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

$area_data=array();
//filters
if(isset($_GET['id']) && $_GET['id'] != ""){
    $area_data=explode(",",$_GET['id']);
}else{
    $area_data=$_SESSION["user_department_node_id"];
}

$report_data = $Survey->GetServicePointCountByArea($area_data);
if( $report_data ) {
    for($i=0; $i<count($report_data); $i++){
        $report_data[$i]["point_type"] = $dictionary->GetValue($report_data[$i]["point_type"]);
    }
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
