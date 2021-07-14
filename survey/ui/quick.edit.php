<?php
include_once realpath(__DIR__ . '/../..').'/include/checksession.php';
include_once realpath(__DIR__ . '/../..').'/include/checkpermission.php';
include_once realpath(__DIR__ . '/../..').'/include/settings.php';
include_once realpath(__DIR__ . '/../..').'/class/Dictionary.php';
include_once realpath(__DIR__ . '/../..').'/class/HtmlHelper.php';
include_once realpath(__DIR__ . '/../..').'/class/Installation.class.php';
include_once realpath(__DIR__ . '/../..').'/class/Survey.class.php';


$html = new HTML($LANGUAGE);
$Installation = new Installation();
$dictionary = new Dictionary($LANGUAGE);
$dictionary->GetAllDictionary();
$Survey = new Survey();

$options = array("class"=>"form-control", "optional"=>true);
$filter = array();
$point_id = $station_id = $feeder_id = $capacity_id = $transformer_number = $transformer_type_id = $transformer_privacy_id = $area_id = "";
if( isset($_REQUEST["point_id"]) && $_REQUEST["point_id"] > 0 ) {
    $filter["service_point.point_id"] = $_REQUEST["point_id"];
    $Point = $Survey->GetServicePoint($filter);

    $point_id = $Point[0]["point_id"];
    $station_id = $Point[0]["station_id"];
    $feeder_id = $Point[0]["feeder_id"];
    $capacity_id = $Point[0]["capacity_id"];
    $transformer_number = $Point[0]["transformer_number"];
    $transformer_type_id = $Point[0]["transformer_type_id"];
    $transformer_privacy_id = $Point[0]["transformer_privacy_id"];
    $area_id = $Point[0]["area_id"];
}

if($Point) {

    $StationArr = $Survey->GetStation();
    $FeederArr = $Survey->GetFeeder($station_id);
    $TransformerTypesArr = $Survey->GetTransformerTypes();
    $TransformerPrivacyArr = $Survey->GetTransformerPrivacy();

    $html->OpenDiv("row");
    {
        $html->OpenForm ( "code/update.transformer.code.php", "form3");
        {
            $html->OpenSpan(12);
            {
                $html->OpenSpan(6);
                {
                    $html->DrawFormField("select", "station_id", $station_id, $StationArr, $options);
                }
                $html->CloseSpan();

                $html->OpenSpan(6);
                {
                    $html->DrawFormField("select", "feeder_id", $feeder_id, $FeederArr, $options);
                }
                $html->CloseSpan();

                $html->OpenSpan(6);
                {
                    $html->DrawFormField("text", "capacity_id", $capacity_id, NULL, $options);
                }
                $html->CloseSpan();

                $html->OpenSpan(6);
                {
                    $html->DrawFormField("text", "transformer_number", $transformer_number, NULL, $options);
                }
                $html->CloseSpan();


                $html->OpenSpan(6);
                {
                    $html->DrawFormField("select", "transformer_type_id", $transformer_type_id, $TransformerTypesArr, array_merge($options, ["optional"=>true, "dictionary"=>true]));
                }
                $html->CloseSpan();


                $html->OpenSpan(6);
                {
                    $html->DrawFormField("select", "transformer_privacy_id", $transformer_privacy_id, $TransformerPrivacyArr, array_merge($options, ["optional"=>true, "dictionary"=>true]));
                }
                $html->CloseSpan();
            }
            $html->CloseSpan();

            $controls = array (
                            array ( "type"=>"submit", "name"=>"Change", "value"=>"save_changes", "list"=>NULL, "options"=>array ("class" => "btn green")),
                            array ( "type"=>"hidden", "name"=>"point_id", "value"=>$point_id, "list"=>NULL, "options"=>NULL ),
                            array ( "type"=>"hidden", "name"=>"area_id", "value"=>$area_id, "list"=>NULL, "options"=>NULL )
                        );
        }
        $html->CloseForm ($controls);
    }
    $html->CloseDiv();
} else {
    print $dictionary->GetValue("no_data_found");
}
?>
<script>
$(function() {
    $('#station_id').on('change', function () {
        station_id = $(this).val();
        FillFeeder(station_id);
    });
});

function FillFeeder(station_id){
    if( station_id > 0){
        FillListOptions("ui/get.feeder.php?station_id="+station_id, "feeder_id", true);
    } else {
        FillListOptions("ui/get.feeder.php", "feeder_id", true);
    }
}
</script>