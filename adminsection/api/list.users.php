<?php
/*this page is used to get feed for users data-table*/
include_once realpath(__DIR__ . '/../..').'/include/checksession.php';
include_once realpath(__DIR__ . '/../..').'/include/checkpermission.php';
require_once realpath(__DIR__ . '/../..').'/include/settings.php';
require_once realpath(__DIR__ . '/../..').'/class/User.php';
require_once realpath(__DIR__ . '/../..').'/class/SystemMessage.php';
require_once realpath(__DIR__ . '/../..').'/class/Tree.php';

$user = new User();
$tree = new Tree("DIR_TREE");
$message = New SysetemMessage($LANGUAGE);
$dictionary = new Dictionary($LANGUAGE);
$dictionary->GetAllDictionary();

$pageSize = 10;
$startingRecord = 0;
$totalRecords = 0;

$order = array();

/*Table data definition*/
$columnList = array();
$columnList[] = array("column"=>"NAME");
$columnList[] = array("column"=>"LOGIN");
$columnList[] = array("column"=>"DIR_NAME");
$columnList[] = array("column"=>"USER_STATUS", "dictionary"=>"true");
$columnList[] = array("column"=>"ACTION_COL", "style"=>"width:200px","action-type"=>"ajax");
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
$filter = array();
if(isset($_REQUEST["name"]) && $_REQUEST["name"] !=""){
    $filter["name"] =array("Operator"=>"LIKE","Value"=> "%".$_REQUEST["name"]."%", "Type"=>"mytext");
}
if(isset($_REQUEST["login"]) && $_REQUEST["login"] !=""){
    $filter["login"] =array("Operator"=>"LIKE","Value"=> "%".$_REQUEST["login"]."%", "Type"=>"mytext");
}
if(isset($_REQUEST["user_dir"]) && $_REQUEST["user_dir"] !=""){
	$filter["user_department_node_id"] = $_REQUEST["user_dir"];
}
if(isset($_REQUEST["status"]) && $_REQUEST["status"] !=""){
	$filter["`USER`.`USER_STATUS_ID`"] = $_REQUEST["status"];
}

$allUsers = $user -> GetUsers($filter, $order, $startingRecord, $pageSize, $totalRecords);
//manipulate data before sending
if($allUsers != null){
    for($i=0; $i<count($allUsers); $i++){
        $allUsers[$i]["DIR_NAME"] = $tree->GetPathString($allUsers[$i]["user_department_node_id"]);
        $allUsers[$i]["USER_STATUS"] = $dictionary->GetValue($allUsers[$i]["USER_STATUS"]);
    }
}
//Complete paging variables
//$totalPages = ceil($totalRecords/$pageSize)-1;

//ajax output
ob_clean();
header('Content-type: application/json');
echo '{';
echo '"draw":'.$draw.',';
echo '"recordsTotal":'.$totalRecords.',';
echo '"recordsFiltered":'.$totalRecords.',';
echo '"data": ';
if($allUsers !=false){
    echo json_encode( $allUsers );
}else{
	echo "{}";
}
echo "}";

?>
