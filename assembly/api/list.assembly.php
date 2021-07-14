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
$cols[] = array("column"=>"assembly_order_code");
$cols[] = array("column"=>"create_date");
$cols[] = array("column"=>"start_date");
$cols[] = array("column"=>"NAME");
$cols[] = array("column"=>"enclosures");
$cols[] = array("column"=>"change_status");
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
$filter = array();

if(isset($_REQUEST["assembly_order_code"]) && $_REQUEST["assembly_order_code"]!=NULL){
    $filter["assembly_order_code"] = array("Operator"=>"LIKE","Value"=> "%".$_REQUEST["assembly_order_code"]."%", "Type"=>"mytext");
}

if(isset($_REQUEST["status"]) && $_REQUEST["status"]!=NULL){  
    if($_REQUEST["status"] == 0 || $_REQUEST["status"] == 1){
        $filter["assembly_order.status_id"] = $_REQUEST["status"];
    } else if($_REQUEST["status"] == 2 || $_REQUEST["status"] == 3){
        $filter["completeness"] = $_REQUEST["status"];
    }
}


$AssemblyOrder = $Assembly->GetAssemblyOrderList($filter, $order, $startingRecord, $pageSize, $totalRecords);


for($i=0; $i<count($AssemblyOrder); $i++) {

    if( $AssemblyOrder[$i]["enclosures_count"] - $AssemblyOrder[$i]["manufactured_count"] == 0 ){
        $color = "success";
    } else {
        $color = "danger";
    }
    $AssemblyOrder[$i]["enclosures"] = '<span class="label label-'.$color.'"><b>'.$AssemblyOrder[$i]["manufactured_count"]."/".$AssemblyOrder[$i]["enclosures_count"].'</b></span>';

    if($AssemblyOrder[$i]["status_id"] == 1){
        $AssemblyOrder[$i]["change_status"] = '<a href="javascript:;" class="btn change_assembly_status" style="color:#36c6d3" data-id="'.$AssemblyOrder[$i]["assembly_order_id"].'" data-status="0" title="'.$dictionary->GetValue("open").'"><i class="fa fa-toggle-on"></i></a>';
    }else{
        $AssemblyOrder[$i]["change_status"] = '<a href="javascript:;" class="btn change_assembly_status" style="color:#ed6b75" data-id="'.$AssemblyOrder[$i]["assembly_order_id"].'" data-status="1" title="'.$dictionary->GetValue("close").'"><i class="fa fa-toggle-off"></i></a>';
    }
}

header('Content-type: application/json');
echo '{';
echo '"draw":'.$draw.',';
echo '"recordsTotal":'.$totalRecords.',';
echo '"recordsFiltered":'.$totalRecords.',';
echo '"data": ';
if(count($AssemblyOrder) > 0){
    echo json_encode( $AssemblyOrder );
}else{
    echo "{}";
}
echo "}";
?>
