<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include_once realpath(__DIR__ . '/../..').'/include/checksession.php';
//include_once realpath(__DIR__ . '/../..').'/include/checkpermission.php';
require_once realpath(__DIR__ . '/../..').'/include/settings.php';
require_once realpath(__DIR__ . '/../..').'/class/Survey.class.php';
require_once realpath(__DIR__ . '/../..').'/class/HtmlHelper.php';
include_once realpath(__DIR__ . '/../..').'/class/Installation.class.php';


$html = new HTML ( $LANGUAGE );
$Installation = new Installation();
$Survey = new Survey();

$area_id = $_GET["area_id"];
$station_id = $station_name = null;
if (isset($_GET["id"])) {
    $station_id = $_GET["id"];
    $station = $Survey->GetStationDetails($station_id);
    $station_name = $station[0]["station"];
}

$AreaArr = $Installation->GetInstallationArea();

$html->OpenForm ( null, "form4" );
{
    $html->OpenDiv("row");
    {
        $html->OpenSpan(12);
        {
            $html->DrawFormField("select", "area_id", $area_id, $AreaArr, array("class"=>"form-control", "optional"=>"true"));
            $html->DrawFormField("text", "station", $station_name, null, array("class"=>"form-control"));
            $html->HiddenField("station_id", $station_id);
        }
        $html->CloseSpan();
        $html->OpenSpan(12);
        {
            $html->Button("button", "save_station", "save_station", ["class"=>"btn btn-primary btn-block save_station"]);
        }
        $html->CloseSpan();
    }
    $html->CloseDiv();
}
$html->CloseForm();
?>
