<?php
/*this page is used to get feed for doc out temp data-table*/
require_once realpath(__DIR__ . '/../..').'/include/settings.php';
include_once realpath(__DIR__ . '/../..').'/include/checksession.php';
include_once realpath(__DIR__ . '/../..').'/include/checkpermission.php';
require_once realpath(__DIR__ . '/../..').'/class/Survey.class.php';
require_once realpath(__DIR__ . '/../..').'/class/Dictionary.php';
require_once realpath(__DIR__ . '/../..') . '/class/coordinates.php';

$dictionary = new Dictionary ( $LANGUAGE );
$dictionary->GetAllDictionary ();
$Survey = new Survey( );

$pageSize = 10;
$startingRecord = 0;
$totalRecords = 0;

/*Table data definition*/
$cols = array();
$cols[] = array("column"=>"sequence");
$cols[] = array("column"=>"point_type");
$cols[] = array("column"=>"point_id");
$cols[] = array("column"=>"single_phase_consumers");
$cols[] = array("column"=>"three_phase_consumers");
$cols[] = array("column"=>"transformer");
if($MAPCORDINATES == 'latlng') {
    $cols2[] = array("column"=>"latitude");
    $cols2[] = array("column"=>"longitude");
} else if($MAPCORDINATES == 'utm') {
    $cols2[] = array("column"=>"x");
    $cols2[] = array("column"=>"y");
}
// $cols[] = array("column"=>"latitude");
// $cols[] = array("column"=>"longitude");
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

    if( $sortColIndex == 0 ) {
        $sortCol = "point_id";
    } else if( $sortColIndex == 4 ) {
        $sortCol = "transformer_number";
    } else {
        $sortCol = $cols[$sortColIndex]["column"];
    }

    $order[$sortCol] = $direction;
}
/*End Datatables params*/

//filters
$filter = array();

if(isset($_REQUEST["user_id"])&&$_REQUEST["user_id"]!=NULL){
    $filter["service_point.user_id"] = $_REQUEST["user_id"];
}

if(isset($_REQUEST["area_id"])&&$_REQUEST["area_id"]!=NULL){
    $filter["service_point.area_id"] = $_REQUEST["area_id"];
}

$allSurvicePoints = $Survey->GetServicePoint($filter, $order, $startingRecord, $pageSize, $totalRecords);

if( $allSurvicePoints )
{
    for ( $counter=0; $counter < count( $allSurvicePoints ); $counter++ ){
        if($MAPCORDINATES == 'utm') {
            $utm = ll2utm($allSurvicePoints[$counter]['latitude'], $allSurvicePoints[$counter]['longitude']);
            $allSurvicePoints[$counter]['x'] = $utm["x"];
            $allSurvicePoints[$counter]['y'] = $utm["y"];
        }
        $allSurvicePoints[$counter]['sequence'] = $counter+1;
        $allSurvicePoints[$counter]['point_type'] = $dictionary->GetValue($allSurvicePoints[$counter]['point_type']);

        if( $allSurvicePoints[$counter]['point_type_id'] == 4 ){
            $allSurvicePoints[$counter]['transformer'] = $allSurvicePoints[$counter]['station_id']."/".$allSurvicePoints[$counter]['feeder_id']."/".$allSurvicePoints[$counter]['capacity_id']."/".$allSurvicePoints[$counter]['transformer_number']. " [". $allSurvicePoints[$counter]['transformer_generated_number'] ."]";
        } else {
            $allSurvicePoints[$counter]['transformer'] = "";
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
