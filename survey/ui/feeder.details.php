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
$feeder_id = $_GET["id"];

$feeder = $Survey->GetFeederDetails($feeder_id);
$html->OpenDiv("bordered");
{
    $html->OpenDiv("row");
    {

        $html->OpenSpan(2);
        {
            $html->DrawFormField("label", "area", $feeder[0]["area"], null, array());
        }
        $html->CloseSpan();
        $html->OpenSpan(2);
        {
            $html->DrawFormField("label", "sation", $feeder[0]["station"], null, array());
        }
        $html->CloseSpan();
        $html->OpenSpan(2);
        {
            $html->DrawFormField("label", "feeder", $feeder[0]["feeder"], null, array());
        }
        $html->CloseSpan();

        $html->OpenSpan(2);
        {
            $html->DrawFormField("label", "id", $feeder[0]["feeder_id"], null, array());
        }
        $html->CloseSpan();

        $html->OpenSpan(4);
        {
            $html->Button("button", "add_transformer", "add_transformer", ["class"=>"btn btn-primary pull-right add_transformer"]);
            $html->Button("button", "delete_feeder", "delete_feeder", ["class"=>"btn btn-danger pull-right delete_feeder", "style"=>"margin-right: 5px", "data-id"=>$feeder_id]);
            $html->Button("button", "update_feeder", "update_feeder", ["class"=>"btn btn-primary pull-right change_feeder", "style"=>"margin-right: 5px", "data-id"=>$feeder_id]);
            // $html->Button("button", "add_feeder", "add_feeder", ["class"=>"btn btn-primary pull-right add_feeder", "style"=>"margin-right: 5px", "data-id"=>$feeder_id]);
        }
        $html->CloseSpan();
    }
    $html->CloseDiv();
}
$html->CloseDiv();
?>
