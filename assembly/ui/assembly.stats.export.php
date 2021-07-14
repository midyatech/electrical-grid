<?php
//ob_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once realpath(__DIR__ . '/../..').'/include/settings.php';
include_once realpath(__DIR__ . '/../..').'/include/checksession.php';
include_once realpath(__DIR__ . '/../..').'/include/checkpermission.php';
require_once realpath(__DIR__ . '/../..').'/class/ExcelHelper.php';
require_once realpath(__DIR__ . '/../..').'/class/Assembly.class.php';
require_once realpath(__DIR__ . '/../..').'/class/Dictionary.php';

$excel = new Excel($LANGUAGE);

$dictionary = new Dictionary ( $LANGUAGE );
$dictionary->GetAllDictionary ();
$Assembly = new Assembly();


$id = $_REQUEST['id'];

$cols = array();
$cols[] = array("column"=>"iccids");
$cols[] = array("column"=>"serial_number");
$cols[] = array("column"=>"activation_date");
$cols[] = array("column"=>"simcard_status");
$title = "ICCID_LIST";

$iccids = $Assembly->getOrderIccids($id,$condition, $order, $startingRecord, $pageSize, $totalRecords);

if ($iccids) {
    for($i=0; $i<count($iccids); $i++){
        $iccids[$i]["iccids"] = json_decode('" "').$iccids[$i]["iccids"];
    }
}



// if($data!=null){
//     for($i = 0; $i<count($data); $i++)
//     {
//         $data[$i]["ICCID"] = $data[$i]["ICCID"]." ";
//     }
// }
// print_r($data);
// die();
ob_end_clean();
// $excel->DrawHeader($dictionary->GetValue($title), $filter_text);
$excel->DrawExcelTable($iccids, $cols);
$excel->SaveExcelFile($title."_".date("Ymd"));

?>
