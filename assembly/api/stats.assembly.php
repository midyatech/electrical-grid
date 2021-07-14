<?php
/*this page is used to get feed for doc out temp data-table*/
require_once realpath(__DIR__ . '/../..').'/include/settings.php';
include_once realpath(__DIR__ . '/../..').'/include/checksession.php';
// include_once realpath(__DIR__ . '/../..').'/include/checkpermission.php';
require_once realpath(__DIR__ . '/../..').'/class/Assembly.class.php';
require_once realpath(__DIR__ . '/../..').'/class/Dictionary.php';
$dictionary = new Dictionary ( $LANGUAGE );
$dictionary->GetAllDictionary ();
$Assembly = new Assembly( );

$pageSize = 10;
$startingRecord = 0;
$totalRecords = 0;

$id = $_REQUEST['id'];
/*Table data definition*/
$cols = array();
$cols[] = array("column"=>"iccids");
$cols[] = array("column"=>"serial_number");
$cols[] = array("column"=>"activation_date");
$cols[] = array("column"=>"simcard_status");
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
// $filter = array();
// if(isset($_REQUEST["Model"]) && $_REQUEST["Model"] != ""){
//     $filter["Model"] = $_REQUEST["Model"];
// }

// if(isset($_REQUEST["Serial_No"]) && $_REQUEST["Serial_No"] != ""){
//     $filter["Serial_No"] = $_REQUEST["Serial_No"];
// }

// if(isset($_REQUEST["STS_No"]) && $_REQUEST["STS_No"] != ""){
//     $filter["STS_No"] = $_REQUEST["STS_No"];
// }

 $iccids = $Assembly->getOrderIccids($id,$condition, $order, $startingRecord, $pageSize, $totalRecords);

if ($iccids) {
    for($i=0; $i<count($iccids); $i++){
        $iccids[$i]["iccids"] = json_decode('"&zwnj;"').$iccids[$i]["iccids"];
    }
}



header('Content-type: application/json');
echo '{';
echo '"draw":'.$draw.',';
echo '"recordsTotal":'.$totalRecords.',';
echo '"recordsFiltered":'.$totalRecords.',';
echo '"data": ';
if(count($iccids) > 0){
    echo json_encode($iccids);
}else{
    echo "{}";
}
echo "}";
?>
