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
$cols[] = array("column"=>"transformer_number");
$cols[] = array("column"=>"Total_Enclosure");
$cols[] = array("column"=>"Installed_Enclosure");
$cols[] = array("column"=>"Total_Meter");
$cols[] = array("column"=>"Installed_Meter");
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

if(isset($_REQUEST["station_id"]) && $_REQUEST["station_id"]!=NULL){
    $filter["station_id"] = $_REQUEST["station_id"];
}

if(isset($_REQUEST["feeder_id"]) && $_REQUEST["feeder_id"]!=NULL){
    $filter["feeder_id"] = $_REQUEST["feeder_id"];
}

if(isset($_REQUEST["transformer_id"]) && $_REQUEST["transformer_id"]!=NULL){
    $filter["transformer_id"] = $_REQUEST["transformer_id"];
}

$EnclosureInstallationSummary = array();

if(isset($_REQUEST["feeder_id"]) && $_REQUEST["feeder_id"] > 0){
    $EnclosureInstallationSummary = $Installation->GetEnclosureInstallationSummary($filter, $order, $startingRecord, $pageSize, $totalRecords);
}

header('Content-type: application/json');
echo '{';
echo '"draw":'.$draw.',';
echo '"recordsTotal":'.$totalRecords.',';
echo '"recordsFiltered":'.$totalRecords.',';
echo '"data": ';
if(count($EnclosureInstallationSummary) > 0){
    echo json_encode( $EnclosureInstallationSummary );
}else{
    echo "{}";
}
echo "}";
?>
