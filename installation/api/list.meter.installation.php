<?php
/*this page is used to get feed for doc out temp data-table*/
require_once realpath(__DIR__ . '/../..').'/include/settings.php';
include_once realpath(__DIR__ . '/../..').'/include/checksession.php';
include_once realpath(__DIR__ . '/../..').'/include/checkpermission.php';
require_once realpath(__DIR__ . '/../..').'/class/Installation.class.php';
require_once realpath(__DIR__ . '/../..').'/class/Dictionary.php';
require_once realpath(__DIR__ . '/../..').'/class/Tree.php';

$tree = new Tree("AREA_TREE");
$dictionary = new Dictionary ( $LANGUAGE );
$dictionary->GetAllDictionary ();
$Installation = new Installation( );

$pageSize = 10;
$startingRecord = 0;
$totalRecords = 0;

/*Table data definition*/
$cols = array();
$cols[] = array("column"=>"Plant_No", "title"=>"Plant No.");
$cols[] = array("column"=>"Serial_No", "title"=>"Serial No.");
$cols[] = array("column"=>"Model");
// $cols[] = array("column"=>"coordinates", "title"=>"Coordinates");
$cols[] = array("column"=>"latitude");
$cols[] = array("column"=>"longitude");
$cols[] = array("column"=>"governerate", "title"=>"Grand Parent Zone");
$cols[] = array("column"=>"subdistrict", "title"=>"Parent Zone");
$cols[] = array("column"=>"NODE_NAME", "title"=>"Zone");
$cols[] = array("column"=>"station");
$cols[] = array("column"=>"feeder");
$cols[] = array("column"=>"point_id");
$cols[] = array("column"=>"enclosure_sn");

// $cols[] = array("column"=>"installed_time");
// $cols[] = array("column"=>"station");
// $cols[] = array("column"=>"feeder");
// $cols[] = array("column"=>"NODE_NAME");
// $cols[] = array("column"=>"point_id");
// $cols[] = array("column"=>"latitude");
// $cols[] = array("column"=>"longitude");
// $cols[] = array("column"=>"transformer_number");
// $cols[] = array("column"=>"enclosure_type");
// $cols[] = array("column"=>"enclosure_sn");
// $cols[] = array("column"=>"gateway_sn");
// $cols[] = array("column"=>"Model");
// $cols[] = array("column"=>"Plant_No", "title"=>"Plant No.");
// $cols[] = array("column"=>"Serial_No", "title"=>"Serial No.");
// $cols[] = array("column"=>"STS_No", "title"=>"STS No.");
// $cols[] = array("column"=>"IMEI");
// $cols[] = array("column"=>"ICCID");
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

if( isset($_REQUEST["Plant_No"]) && $_REQUEST["Plant_No"] != NULL ){
    $filter["meter.Plant_No"] = $_REQUEST["Plant_No"];
}

if( isset($_REQUEST["Serial_No"]) && $_REQUEST["Serial_No"] != NULL ){
    $filter["meter.Serial_No"] = $_REQUEST["Serial_No"];
}

if(isset($_REQUEST["from_date"]) && $_REQUEST["from_date"]!=NULL){
    $filter["from_date"] = $_REQUEST["from_date"];
}

if(isset($_REQUEST["to_date"]) && $_REQUEST["to_date"]!=NULL){
    $filter["to_date"] = $_REQUEST["to_date"];
}

$EnclosureInstallation = $Installation->GetMeterInstallation($filter, $order, $startingRecord, $pageSize, $totalRecords);

for($i=0; $i<count($EnclosureInstallation); $i++){
    $EnclosureInstallation[$i]["enclosure_type"] = $EnclosureInstallation[$i]["enclosure_type"]." [".$EnclosureInstallation[$i]["configuration_name"]."]";
    // if ($EnclosureInstallation[$i]["latitude"] != "") {
    //     $EnclosureInstallation[$i]["coordinates"] = $EnclosureInstallation[$i]["latitude"].",".$EnclosureInstallation[$i]["longitude"];
    // } else {
    //     $EnclosureInstallation[$i]["coordinates"] = "";
    // }

    $EnclosureInstallation[$i]["subdistrict"] = $EnclosureInstallation[$i]["governerate"] = "";
    if ($EnclosureInstallation[$i]["NODE_ID"] != "") {
        $node = $tree->GetNodeParent($EnclosureInstallation[$i]["NODE_ID"]);

        $EnclosureInstallation[$i]["subdistrict"] = $node[0]["NODE_NAME"];
        if ($node[0]["NODE_ID"] != "") {
            $parent_node = $tree->GetNodeParent($node[0]["NODE_ID"]);
            $EnclosureInstallation[$i]["governerate"] =  $parent_node[0]["NODE_NAME"];
        }
    }


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
