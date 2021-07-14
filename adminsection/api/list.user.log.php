<?php
/*this page is used to get feed for users data-table*/
include_once realpath(__DIR__ . '/../..').'/include/checksession.php';
include_once realpath(__DIR__ . '/../..').'/include/checkpermission.php';
require_once realpath(__DIR__ . '/../..').'/include/settings.php';
require_once realpath(__DIR__ . '/../..').'/class/User.php';
require_once realpath(__DIR__ . '/../..').'/class/UserLog.class.php';
require_once realpath(__DIR__ . '/../..').'/class/SystemMessage.php';
require_once realpath(__DIR__ . '/../..').'/class/Tree.php';

$user = new User();
$UserLog = new User_Log();
$tree = new Tree("DIR_TREE");
$message = New SysetemMessage($LANGUAGE);
$dictionary = new Dictionary($LANGUAGE);
$dictionary->GetAllDictionary();

$pageSize = 10;
$startingRecord = 0;
$totalRecords = 0;

$order = array();

/*Table data definition*/
$cols = array();
$cols[] = array("column"=>"NAME");
$cols[] = array("column"=>"TIMESTAMP");
$cols[] = array("column"=>"MODULE_NAME");
$cols[] = array("column"=>"MODULE_OPERATION");
// $cols[] = array("column"=>"NOTES");
$cols[] = array("column"=>"KEY_DATA");
$cols[] = array("column"=>"NEW_DATA");
//$cols[] = array("column"=>"OLD_DATA");
$cols[] = array("column"=>"CRUD_OPERATION");
$cols[] = array("column"=>"TABLE_NAME");
$cols[] = array("column"=>"RECORD_ID");
// $cols[] = array("column"=>"RESULT");
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
    $sortCol = $cols[$sortColIndex]["column"];
    $order[$sortCol] = $direction;
}
/*End Datatables params*/

//filter
$filter = array();
if(isset($_REQUEST["from_date"]) && $_REQUEST["from_date"]!=NULL){
    $filter["from_date"] = $_REQUEST["from_date"];
}
if(isset($_REQUEST["to_date"]) && $_REQUEST["to_date"]!=NULL){
    $filter["to_date"] = $_REQUEST["to_date"];
}
if(isset($_REQUEST["USER"]) && $_REQUEST["USER"] !=""){
	$filter["USER.NAME"] = array("Operator"=>"LIKE","Value"=> "%".$_REQUEST["USER"]."%", "Type"=>"mytext");
}
if(isset($_REQUEST["MODULE_ID"]) && $_REQUEST["MODULE_ID"] !=""){
	$filter["USER_LOG.MODULE_ID"] = $_REQUEST["MODULE_ID"];
}
if(isset($_REQUEST["MODULE_OPERATION_ID"]) && $_REQUEST["MODULE_OPERATION_ID"] !=""){
	$filter["MODULE_OPERATION_ID"] = $_REQUEST["MODULE_OPERATION_ID"];
}
if(isset($_REQUEST["KEY_DATA"]) && $_REQUEST["KEY_DATA"] !=""){
	$filter["KEY_DATA"] = $_REQUEST["KEY_DATA"];
}
// if(isset($_REQUEST["CRUD_OPERATION"]) && $_REQUEST["CRUD_OPERATION"] !=""){
// 	$filter["CRUD_OPERATION"] = $_REQUEST["CRUD_OPERATION"];
// }

// print_r($filter);
$allUserLogs = $UserLog -> SearchUserLog($filter, $order, $startingRecord, $pageSize, $totalRecords);

//manipulate data before sending

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
if($allUserLogs !=false){
    echo json_encode( $allUserLogs );
}else{
	echo "{}";
}
echo "}";

?>
