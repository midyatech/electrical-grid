<?php
/*this page is used to get feed for doc out temp data-table*/
require_once realpath(__DIR__ . '/../..').'/include/settings.php';
include_once realpath(__DIR__ . '/../..').'/include/checksession.php';
include_once realpath(__DIR__ . '/../..').'/include/checkpermission.php';
require_once realpath(__DIR__ . '/../..').'/class/Assembly.class.php';
require_once realpath(__DIR__ . '/../..').'/class/Dictionary.php';
$dictionary = new Dictionary ( $LANGUAGE );
$dictionary->GetAllDictionary ();
$Assembly = new Assembly( );

$pageSize = 10;
$startingRecord = 0;
$totalRecords = 0;

/*Table data definition*/
$cols = array();
$cols[] = array("column"=>"Model");
$cols[] = array("column"=>"Serial_No");
$cols[] = array("column"=>"STS_No");
$cols[] = array("column"=>"IMEI");
$cols[] = array("column"=>"ICCID");
$cols[] = array("column"=>"ip_address");
$cols[] = array("column"=>"activation_date");
$cols[] = array("column"=>"simcard_status_id");
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


if(isset($_REQUEST["Model"]) && $_REQUEST["Model"] != ""){
    $filter["Model"] = $_REQUEST["Model"];
}

if(isset($_REQUEST["Serial_No"]) && $_REQUEST["Serial_No"] != ""){
    $filter["Serial_No"] = $_REQUEST["Serial_No"];
}

if(isset($_REQUEST["STS_No"]) && $_REQUEST["STS_No"] != ""){
    $filter["STS_No"] = $_REQUEST["STS_No"];
}

if(isset($_REQUEST["IMEI"]) && $_REQUEST["IMEI"] != ""){
    $filter["IMEI"] = $_REQUEST["IMEI"];
}

if(isset($_REQUEST["ip_address"]) && $_REQUEST["ip_address"] != ""){
    $filter["ip_address"] = $_REQUEST["ip_address"];
}

if(isset($_REQUEST["ICCID_pattern"]) && $_REQUEST["ICCID_pattern"] != ""){
    $iccids = $_REQUEST["ICCID_pattern"];
    $iccids = rtrim($iccids, ',');
    $iccids_arr = explode (",", $iccids);

    $iccid_string = '';
    $output_array = array();
    
    for($i=0; $i<count($iccids_arr); $i++) {
        $iccids_arr[$i] = preg_replace("/[^0-9]/", "", $iccids_arr[$i]);
        
        if ($iccids_arr[$i] != "") {
            $output_array[] = "'$iccids_arr[$i]'";
        }
    }
    $final_iccids_string = implode(",", $output_array);
    // print 'api'.$final_iccids_string;
    $filter["ICCID_pattern"] = $final_iccids_string;
}

if(isset($_REQUEST["simcard_status_id"]) && $_REQUEST["simcard_status_id"] != ""){
    $filter["simcard_status_id"] = $_REQUEST["simcard_status_id"];
}

if(isset($_REQUEST["activation_date"]) && $_REQUEST["activation_date"] != ""){
    $filter["activation_date"] = $_REQUEST["activation_date"];
}

if(isset($_REQUEST["from_date"]) && $_REQUEST["from_date"]!=NULL){
    $filter["from_date"] = $_REQUEST["from_date"];
}

if(isset($_REQUEST["to_date"]) && $_REQUEST["to_date"]!=NULL){
    $filter["to_date"] = $_REQUEST["to_date"];
}

if(isset($_REQUEST["status"]) && $_REQUEST["status"]!=NULL){
    $filter["status"] = $_REQUEST["status"];
}

$ICCID = $Assembly->getICCID($filter, $order, $startingRecord, $pageSize, $totalRecords);

if(count($ICCID) > 0){
    for($i=0; $i<count($ICCID); $i++){
        $ICCID[$i]["Serial_No"] = json_decode('"&zwnj;"').$ICCID[$i]['Serial_No'];
        $ICCID[$i]["STS_No"] = json_decode('"&zwnj;"').$ICCID[$i]['STS_No'];
        $ICCID[$i]["IMEI"] = json_decode('"&zwnj;"').$ICCID[$i]['IMEI'];
        $ICCID[$i]["ICCID"] = json_decode('"&zwnj;"').$ICCID[$i]['ICCID'];
        $ICCID[$i]["ip_address"] = json_decode('"&zwnj;"').$ICCID[$i]['ip_address'];
        $ICCID[$i]["activation_date"] = json_decode('"&zwnj;"').$ICCID[$i]['activation_date'];
        $ICCID[$i]["gateway_status_id"] = json_decode('"&zwnj;"').$ICCID[$i]['gateway_status_id'];
    }
}

header('Content-type: application/json');
echo '{';
echo '"draw":'.$draw.',';
echo '"recordsTotal":'.$totalRecords.',';
echo '"recordsFiltered":'.$totalRecords.',';
echo '"data": ';
if(count($ICCID) > 0){
    echo json_encode( $ICCID );
}else{
    echo "{}";
}
echo "}";
?>
