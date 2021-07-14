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
$cols2 = array();
$cols2[] = array("column"=>"NAME", "title"=>"Name");
$cols2[] = array("column"=>"comments", "title"=>"Comments");
$cols2[] = array("column"=>"comment_time", "title"=>"Comment Time");
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

//filters
$filter = array();
if(isset($_REQUEST["point_id"]) && $_REQUEST["point_id"]!=NULL){
    $filter["point_id"] = $_REQUEST["point_id"];
}

// if(isset($_REQUEST["state"]) && $_REQUEST["state"]!=NULL){
//     $filter["state"] = $_REQUEST["state"];
// }

// if(isset($_REQUEST["problem_report_id"]) && $_REQUEST["problem_report_id"]!=NULL){
//     $filter["problem_report_id"] = $_REQUEST["problem_report_id"];
// }


$EnclosureInstallationcomments = $Installation->GetInstallationComments($filter);


header('Content-type: application/json');
echo '{';
echo '"draw":'.$draw.',';
echo '"recordsTotal":'.$totalRecords.',';
echo '"recordsFiltered":'.$totalRecords.',';
echo '"data": ';
if(count($EnclosureInstallationcomments) > 0){
    echo json_encode( $EnclosureInstallationcomments );
}else{
    echo "{}";
}
echo "}";
?>
