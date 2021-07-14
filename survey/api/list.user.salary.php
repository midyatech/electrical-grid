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
$cols[] = array("column"=>"NAME");
$cols[] = array("column"=>"month");
$cols[] = array("column"=>"points");
$cols[] = array("column"=>"salary");

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
$month_id = NULL;
if(isset($_REQUEST["month_id"])&&$_REQUEST["month_id"]!=NULL){
    $filter["month_id"] = $_REQUEST["month_id"];

    $report_data = $Survey->GetMonthSalaries($filter, $order);
    if($report_data){
        for($i=0; $i<count($report_data); $i++){
            $report_data[$i]["points"] =number_format($report_data[$i]["points"], 0, '.', ',');
            $report_data[$i]["salary"] =number_format($report_data[$i]["salary"], 0, '.', ',')." IQD";
        }

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
