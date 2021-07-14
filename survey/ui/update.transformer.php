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
$transformer_id = $_GET["id"];

$transformer_types = $Survey->GetTransformerTypes();
$transformer_privacy = $Survey->GetTransformerPrivacy();

$StationArr = $Survey->GetStationByArea();

$transformer = $Survey->GetTransformerDetails($transformer_id);
$station = $transformer[0]["station_id"];
$feeder = $transformer[0]["feeder_id"];
$FeederArr = $Survey->GetFeeders(null, $station);


$html->OpenForm ( null, "form5" );
{
    $html->OpenDiv("row");
    {
        $html->OpenSpan(12);
        {
            $html->DrawFormField("select", "station_id", $station, $StationArr, array("class"=>"form-control", "optional"=>"true"));
        }
        $html->CloseSpan();
        $html->OpenSpan(12);
        {
            $html->DrawFormField("select", "feeder_id", $feeder, $FeederArr, array("class"=>"form-control", "optional"=>"true"));
        }
        $html->CloseSpan();
        $html->OpenSpan(12);
        {
            $html->DrawFormField("text", "capacity_id", $transformer[0]["capacity_id"], NULL, array("class"=>"form-control", "optional"=>"true"));
        }
        $html->CloseSpan();

        $html->OpenSpan(12);
        {
            $html->DrawFormField("text", "transformer_number", $transformer[0]["original_transformer_number"], null, array("class"=>"form-control", "optional"=>"true"));
        }
        $html->CloseSpan();


        $html->OpenSpan(12);
        {
            $html->DrawFormField("select", "transformer_type_id", $transformer[0]["transformer_type_id"], $transformer_types, array("class"=>"form-control","optional"=>true, "dictionary"=>true));
        }
        $html->CloseSpan();


        $html->OpenSpan(12);
        {
            $html->DrawFormField("select", "transformer_privacy_id", $transformer[0]["transformer_privacy_id"], $transformer_privacy, array("class"=>"form-control","optional"=>true, "dictionary"=>true));
        }
        $html->CloseSpan();

        $html->OpenSpan(12);
        {
            $html->HiddenField("transformer_id", $transformer_id);
            $html->Button("button", "save_transformer", "save_transformer", ["class"=>"btn btn-primary btn-block save_transformer"]);
        }
        $html->CloseSpan();
    }
    $html->CloseDiv();
}
$html->CloseForm();
?>
<script>
// $(function() {
//     $("body").on("change", "#form5 #station_id", function(){
//         station_id = $("#form5 #station_id").val();
//         FillFeeder(area_id, station_id, "form5 #feeder_id");
//     });
// });
</script>