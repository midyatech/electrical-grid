<?php
// error_reporting(E_ALL);
// ini_set('display_errors', 1);
include_once realpath(__DIR__ . '/../..').'/include/checksession.php';
//include_once realpath(__DIR__ . '/../..').'/include/checkpermission.php';
require_once realpath(__DIR__ . '/../..').'/include/settings.php';
require_once realpath(__DIR__ . '/../..').'/class/Survey.class.php';
require_once realpath(__DIR__ . '/../..').'/class/HtmlHelper.php';
include_once realpath(__DIR__ . '/../..').'/class/Dictionary.php';

$dictionary = new Dictionary($LANGUAGE);
$dictionary->GetAllDictionary();
$html = new HTML ( $LANGUAGE );
$Survey = new Survey();
$transformer_id = $_GET["id"];

$transformer = $Survey->GetTransformerDetails($transformer_id);

$html->OpenDiv("bordered");
{
    $html->OpenDiv("row");
    {
        $html->OpenSpan(2);
        {
            $html->DrawFormField("label", "area", $transformer[0]["area"], null, array());
            $html->DrawFormField("label", "number", $transformer[0]["transformer_number"], null, array());
        }
        $html->CloseSpan();
        $html->OpenSpan(2);
        {
            $html->DrawFormField("label", "station", $transformer[0]["station"], null, array());
            $html->DrawFormField("label", "gps", $transformer[0]["latitude"].",".$transformer[0]["longitude"], null, array());
        }
        $html->CloseSpan();
        $html->OpenSpan(2);
        {
            $html->DrawFormField("label", "feeder", $transformer[0]["feeder"], null, array());
            $html->DrawFormField("label", "ID", $transformer[0]["transformer_id"], null, array());
        }
        $html->CloseSpan();

        $html->OpenSpan(6);
        {
            $html->Button("button", "update_transformer", "update_transformer", ["class"=>"btn btn-primary pull-right change_transformer", "data-id"=>$transformer_id]);
            $html->DrawFormField("label", "capacity_id", $transformer[0]["capacity_id"], null, array());
        }
        $html->CloseSpan();
    }
    $html->CloseDiv();

}
$html->CloseDiv();
?>
