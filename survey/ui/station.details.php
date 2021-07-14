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
$station_id = $_GET["id"];

$station = $Survey->GetStationDetails($station_id);
$html->OpenDiv("bordered");
{
    $html->OpenDiv("row");
    {
        $html->OpenSpan(2);
        {
            $html->DrawFormField("label", "area", $station[0]["area"], null, array());
        }
        $html->CloseSpan();
        $html->OpenSpan(2);
        {
            $html->DrawFormField("label", "sation", $station[0]["station"], null, array());
        }
        $html->CloseSpan();

        $html->OpenSpan(2);
        {
            $html->DrawFormField("label", "id", $station[0]["station_id"], null, array());
        }
        $html->CloseSpan();
        $html->OpenSpan(6);
        {
            $html->Button("button", "add_feeder", "add_feeder", ["class"=>"btn btn-primary pull-right add_feeder", "data-id"=>""]);
            $html->Button("button", "delete_station", "delete_station", ["class"=>"btn btn-danger pull-right delete_station", "style"=>"margin-right: 5px", "data-id"=>$station_id]);
            $html->Button("button", "update_station", "update_station", ["class"=>"btn btn-primary pull-right change_station", "style"=>"margin-right: 5px", "data-id"=>$station_id]);
            $html->Button("button", "add_station", "add_station", ["class"=>"btn btn-primary pull-right add_station", "style"=>"margin-right: 5px", "data-id"=>$station_id]);
        }
        $html->CloseSpan();

    }
    $html->CloseDiv();
}
$html->CloseDiv();
?>
