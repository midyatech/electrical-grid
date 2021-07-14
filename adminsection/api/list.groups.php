<?php
/*this page is used to get feed for groups data-table*/
include_once realpath(__DIR__ . '/../..').'/include/checksession.php';
include_once realpath(__DIR__ . '/../..').'/include/checkpermission.php';
require_once realpath(__DIR__ . '/../..').'/include/settings.php';
require_once realpath(__DIR__ . '/../..').'/class/Group.php';
require_once realpath(__DIR__ . '/../..').'/class/SystemMessage.php';

$group = new Group();
$message = New SysetemMessage($LANGUAGE);

$pageSize = 10;
$startingRecord = 0;
$totalRecords = 0;

$order = array();

/*Table data definition*/
$columnList = array();
$columnList[] = array("column"=>"GROUP_NAME");
//$columnList[] = array("column"=>"STATUS", "dictionary"=>"true");
$columnList[] = array("column"=>"ACTION_COL", "style"=>"width:200px","action-type"=>"ajax");
$tableOptions = array();
$tableOptions["key"]=array("group_id"=>"GROUP_ID");
$tableOptions["tableClass"]= "table-hover table-bordered table-striped";
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
if(isset($_REQUEST["order"])){
    if($_REQUEST["order"][0]["dir"] == "asc"){
        $direction = "asc";
    }else{
        $direction = "desc";
    }
    $sortColIndex = $_REQUEST["order"][0]["column"];
    $sortCol = $columnList[$sortColIndex]["column"];
    $order[$sortCol] = $direction;
}
/*End Datatables params*/

//filter
$filter = array('`GROUP`.`STATUS_ID`'=>"1");
if(isset($_REQUEST["name"]) && $_REQUEST["name"] !=""){
    $filter["GROUP_NAME"] =array("Operator"=>"LIKE","Value"=> "%".$_REQUEST["name"]."%", "Type"=>"mytext");
}
// if(isset($_REQUEST["status"]) && $_REQUEST["status"] !=""){
// 	$filter["`USER`.`USER_STATUS_ID`"] = $_REQUEST["status"];
// }

$allGroups = $group -> GetGroups($filter, $order, $startingRecord, $pageSize, $totalRecords);

//ajax output
ob_clean();
header('Content-type: application/json');
echo '{';
echo '"draw":'.$draw.',';
echo '"recordsTotal":'.$totalRecords.',';
echo '"recordsFiltered":'.$totalRecords.',';
echo '"data": ';
if($allGroups !=false){
    echo json_encode( $allGroups );
}else{
	echo "{}";
}
echo "}";

?>
