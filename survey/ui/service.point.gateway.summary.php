<?php
/*this page is used to get feed for doc out temp data-table*/
include_once realpath(__DIR__ . '/../..').'/include/checksession.php';
include_once realpath(__DIR__ . '/../..').'/include/checkpermission.php';
require_once realpath(__DIR__ . '/../..').'/class/Survey.class.php';
include_once realpath(__DIR__ . '/../..').'/class/Dictionary.php';

$Survey = new Survey();
$dictionary = new Dictionary($LANGUAGE);
$dictionary->GetAllDictionary();

//filters
$filter=array();

// if( isset($_REQUEST["station_id"] ) && $_REQUEST["station_id"] != NULL ) {
//     $filter["station_id"] = $_REQUEST["station_id"];
// }

if( isset($_REQUEST["feeder_id"] ) && $_REQUEST["feeder_id"] != NULL ) {
    $filter["feeder_id"] = $_REQUEST["feeder_id"];
}

if( isset($_REQUEST["transformer_id"] ) && $_REQUEST["transformer_id"] != NULL ) {
    $filter["transformer_id"] = $_REQUEST["transformer_id"];
}

$Total = 0;
$With_Out_Gateway = $Has_Gateway = 0;

$GatewaySummary = $Survey->GetServicePointGatewaySummary($filter);

for($i=0; $i<count($GatewaySummary); $i++){
    if($GatewaySummary[$i]["needs_gateway"] == 0 ){
        $With_Out_Gateway += $GatewaySummary[$i]["point_count"]."<br/>";
    }
    if($GatewaySummary[$i]["needs_gateway"] == 1 ){
        $Has_Gateway += $GatewaySummary[$i]["point_count"]."<br/>";
    }
    $Total += $GatewaySummary[$i]["point_count"];
}

print "<div class='row btn blue btn-outline btn-block padding-10' style='margin: 0px 0px 15px 0px; cursor: auto;'>";
    $html->OpenSpan(4);
    {
        print $dictionary->GetValue("With_Out_Gateway")." : ".$With_Out_Gateway;
    }
    $html->CloseSpan();
    $html->OpenSpan(4);
    {
        print $dictionary->GetValue("Has_Gateway")." : ".$Has_Gateway;
    }
    $html->CloseSpan();
    $html->OpenSpan(4);
    {
        print $dictionary->GetValue("Total")." : ".$Total;
    }
    $html->CloseSpan();
print "</div>";
?>
