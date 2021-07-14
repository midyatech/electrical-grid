<?php
/*this page is used to get feed for doc out temp data-table*/
require_once realpath(__DIR__ . '/../..').'/include/settings.php';
include_once realpath(__DIR__ . '/../..').'/include/checksession.php';
include_once realpath(__DIR__ . '/../..').'/include/checkpermission.php';
require_once realpath(__DIR__ . '/../..').'/class/Survey.class.php';
require_once realpath(__DIR__ . '/../..').'/class/Dictionary.php';
$dictionary = new Dictionary ( $LANGUAGE );
$dictionary->GetAllDictionary ();
$Survey = new Survey( );

$pageSize = 10;
$startingRecord = 0;
$totalRecords = 0;

/*Table data definition*/
$cols = array();
$cols[] = array("column"=>"point_type");
$cols[] = array("column"=>"point_detail");
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
    $sortColIndex = $_REQUEST["order"][0]["column"];
    $sortCol = $cols[$sortColIndex]["column"];
    $order[$sortCol] = $direction;
}
/*End Datatables params*/

//filters
$filter = array();
if(isset($_REQUEST["user_id"])&&$_REQUEST["user_id"]!=NULL){
    $filter["user_id"] = $_REQUEST["user_id"];
}

$allSurvicePoints = $Survey->GetServicePoint($filter, NULL, $startingRecord, $pageSize, $totalRecords);

if( $allSurvicePoints )
{
    for ( $counter=0; $counter < count( $allSurvicePoints ); $counter++ ){

        $allSurvicePoints[$counter]['point_type'] = $dictionary->GetValue($allSurvicePoints[$counter]['point_type']);

        if( $allSurvicePoints[$counter]['point_type_id'] == 4 ){
            $allSurvicePoints[$counter]['point_detail'] = $allSurvicePoints[$counter]['station_id']."/".$allSurvicePoints[$counter]['feeder_id']."/".$allSurvicePoints[$counter]['capacity_id']."/".$allSurvicePoints[$counter]['transformer_number'];
        } else {
            $allSurvicePoints[$counter]['point_detail'] = $allSurvicePoints[$counter]['single_phase_consumers']." - ".$allSurvicePoints[$counter]['three_phase_consumers'];
        }
    }
}

header('Content-type: application/json');
echo '{';
echo '"draw":'.$draw.',';
echo '"recordsTotal":'.$totalRecords.',';
echo '"recordsFiltered":'.$totalRecords.',';
echo '"data": ';
if(count($allSurvicePoints) > 0){
    echo json_encode( $allSurvicePoints );
}else{
    echo "{}";
}
echo "}";
?>
