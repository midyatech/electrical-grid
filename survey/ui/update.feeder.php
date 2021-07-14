<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include_once realpath(__DIR__ . '/../..').'/include/checksession.php';
//include_once realpath(__DIR__ . '/../..').'/include/checkpermission.php';
require_once realpath(__DIR__ . '/../..').'/include/settings.php';
require_once realpath(__DIR__ . '/../..').'/class/Survey.class.php';
require_once realpath(__DIR__ . '/../..').'/class/HtmlHelper.php';

$html = new HTML ( $LANGUAGE );
$Survey = new Survey();

$area_id = $_GET["area_id"];
$station_id = $_GET["station_id"];
$feeder_id = $feeder_name = null;
if (isset($_GET["id"])) {
    $feeder_id = $_GET["id"];
    $feeder = $Survey->GetFeederDetails($feeder_id);
    $feeder_name = $feeder[0]["feeder"];
}

$StationArr = $Survey->GetStationByArea($area_id);

$html->OpenForm ( null, "form4" );
{
    $html->OpenDiv("row");
    {
        $html->OpenSpan(12);
        {
            $html->DrawFormField("select", "station_id", $station_id, $StationArr, array("class"=>"form-control", "optional"=>"true"));
            $html->DrawFormField("text", "feeder", $feeder_name, null, array("class"=>"form-control"));
            $html->HiddenField("feeder_id", $feeder_id);
        }
        $html->CloseSpan();
        $html->OpenSpan(12);
        {
            $html->Button("button", "save_feeder", "save_feeder", ["class"=>"btn btn-primary btn-block save_feeder"]);
        }
        $html->CloseSpan();
    }
    $html->CloseDiv();
}
$html->CloseForm();
?>
