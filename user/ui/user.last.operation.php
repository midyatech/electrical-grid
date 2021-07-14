<?php
include_once realpath(__DIR__ . '/../..').'/include/checkpermission.php';
require_once realpath(__DIR__ . '/../..') . '/class/HtmlHelper.php';
require_once realpath(__DIR__ . '/../..') . '/class/UserLog.class.php';

$user_log = new User_Log();
$html = new HTML($LANGUAGE);
$dictionary = new Dictionary($LANGUAGE);
$dictionary -> GetAllDictionary();

/**
 * Paging Section
 */
//Create paging variables
$pageSize = 10;
$startingRecord = 0;
$pageNum = 0;
if(isset($_REQUEST["pageNum"])){
	$pageNum = $_REQUEST["pageNum"];
	$startingRecord = $pageNum * $pageSize;
}


//initating variable for reference parameter &$recordsCount
$totalRecords = 0;
//Get page uri for paging links
if (strpos($_SERVER["REQUEST_URI"],'?') !== false){
	$pageAddress = strtok($_SERVER["REQUEST_URI"],'?');
}else{
	$pageAddress = $_SERVER["REQUEST_URI"];
}
/**
 * End Paging Section
 */


$filter =array();
$filter["USER.USER_ID"] = $_SESSION["user_id"];
$filter["lastoperations"] = "true";
$printed_op = $user_log -> SearchUserLog($filter, NULL, $startingRecord, $pageSize, $totalRecords);

$filter2 =array();
$filter2["USER.USER_ID"] = $_SESSION["user_id"];
$filter2["INVOICE_STATUS_ID"] = array("Operator"=>"!=", "Value"=>2);
$failed_print = $user_log -> SearchUserLog($filter2);


//Complete paging variables
$totalPages = ceil($totalRecords/$pageSize)-1;



$columnList = array();
$columnList[] = array("column"=>"DATE");
$columnList[] = array("column"=>"ACTION_NAME");
$columnList[] = array("column"=>"NOTES");
$columnList[] = array("column"=>"INVOICE_ID");
$columnList[] = array("column"=>"CLIENT_ID");
$columnList[] = array("column"=>"ACTION_COL", "style"=>"width:100px","action-type"=>"ajax",
			  "buttons"=> array(
					array("action"=>"Print","action-class"=>"invoice_print", "button-icon"=>"icon-print", "title"=>$dictionary->GetValue("print"), "filter"=>array("INVOICE_STATUS_ID"=>array("value"=>"2", "operator"=>"!=")))

						)
			);

$tableOptions = array();
$tableOptions["tableClass"]= "table-bordered table-striped";
$tableOptions["key"]=array("order_id"=>"TO_ID");

$html->OpenWidget ("print_failed_invoices", "blue");
{
	$description_key = "description_failed_print_invoices";
	$html->HelpMessage($description_key);
	$html->Table($failed_print, $columnList, $tableOptions);
}
$html->CloseWidget();

$html->OpenWidget ("last_operation", "blue");
{
	$html->OpenForm ( "" );
	{
		$description_key = "description_last_operations";
		$html->HelpMessage($description_key);
		$html->Table($printed_op, $columnList, $tableOptions);
		$html->DrawPagination($pageAddress, $pageNum, $totalPages, $totalRecords);
	}
	$html->CloseForm ();
}
$html->CloseWidget();
?>