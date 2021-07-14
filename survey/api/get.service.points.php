<?php
/*this page is used to get feed for doc out temp data-table*/
include_once realpath(__DIR__ . '/../..').'/include/checksession.php';
require_once realpath(__DIR__ . '/../..').'/class/Survey.class.php';

//$dictionary = new Dictionary ( $LANGUAGE );
//$dictionary->GetAllDictionary ();
$Survey = new Survey( );
if(isset($_GET['id']) && $_GET['id'] != ""){
    $area_id = $_GET['id'];
    $condition["area_id"]= $area_id;
    $ServicePoints=$Survey->GetServicePoint($condition);
};
for ($i=0; $i<count($ServicePoints); $i++) {
    $ServicePoints[$i]["sequence"] = $i+1;
}
header('Content-type: application/json');
if(count($ServicePoints) > 0){
    echo json_encode($ServicePoints, JSON_FORCE_OBJECT);
}else{
	echo "";
}
?>
