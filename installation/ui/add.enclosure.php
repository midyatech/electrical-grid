<?php
require_once realpath(__DIR__ . '/../..').'/include/settings.php';
include_once realpath(__DIR__ . '/../..').'/include/checksession.php';
include_once realpath(__DIR__ . '/../..').'/include/checkpermission.php';
require_once realpath(__DIR__ . '/../..').'/class/Enclosure.class.php';
require_once realpath(__DIR__ . '/../..').'/class/Dictionary.php';
require_once realpath(__DIR__ . '/../..').'/class/HtmlHelper.php';

$html = new HTML ( $LANGUAGE );
$dictionary = new Dictionary ( $LANGUAGE );
$dictionary->GetAllDictionary ();
$Enclosure = new Enclosure( );


$enclosure_id=$gateway_id=null;
$operation="insert";
$meter=array();

$enclosure_sn = "-1";

if (isset($_GET["sn"]) && $_GET["sn"] != ""){
    $enclosure_sn = $_GET["sn"];
}

if (isset($_GET["point_id"]) && $_GET["point_id"] != ""){
    $point_id = $_GET["point_id"];
}

$enclosure_meter = $Enclosure->GetEnclosureDetails(null, $enclosure_sn);

$enclosure_id = $enclosure_meter[0]["enclosure_id"];
$enclosure_installed = $enclosure_meter[0]["enclosure_installed"];

$html->OpenDiv("row");
{
    $html->OpenDiv("col-xs-12");
    {
        if ($enclosure_meter) {

            echo "<br/>";
            echo "<div class='col-xs-12 well' style='padding: 20px; line-height: 2em, text-align: center'' id='installed_point_enclosure'>";
                echo "<b>".$dictionary->GetValue("enclosure_id").":</b> ". $enclosure_meter[0]["enclosure_id"]."<br>";
                echo "<b>".$dictionary->GetValue("enclosure_sn").":</b> ". $enclosure_meter[0]["enclosure_sn"]."<br>";
                echo "<b>".$dictionary->GetValue("enclosure_type").":</b> ". $enclosure_meter[0]["enclosure_type"]."<br>";
                echo "<b>".$dictionary->GetValue("gateway_id").":</b> ". $enclosure_meter[0]["gateway_id"]."<br>";
                echo "<b>".$dictionary->GetValue("meter_type").":</b> ". $enclosure_meter[0]["meter_type"]."<br>";
                echo "<b>".$dictionary->GetValue("meter_count").":</b> ". count($enclosure_meter)."<br>";

                echo "<br/>";
                if(!$enclosure_installed){
                    $html->OpenDiv("col-xs-12");
                    {
                        $html->DrawFormInput("button", "Install", "Install", NULL, array("class"=>"btn btn-success col-xs-12 insert_installed_point_enclosure", "data-enclosure_id"=>$enclosure_id, "data-point_id"=>$point_id));
                    }
                    $html->CloseDiv();
                } else {
                    echo "<br/>";
                    echo "<div class='col-xs-12  alert alert-danger' style='padding: 20px; line-height: 2em; text-align: center'>";
                    print $dictionary->GetValue("enclosure_previously_installed");
                    echo "</div>";
                }
            echo "</div>";
        } else {
            echo "<br/>";
            echo "<div class='col-xs-12  alert alert-danger' style='padding: 20px; line-height: 2em; text-align: center' id='installed_point_enclosure'>";
            print $dictionary->GetValue("not_found_enclosure");
            echo "</div>";
        }
    }
    $html->CloseDiv();
}
$html->CloseDiv();
?>
<script>

$(function() {
    $("body").on("click", ".insert_installed_point_enclosure", function() {
        point_id = $(this).data("point_id");
        EnclosurePointList(point_id);
    });

});

function EnclosurePointList(point_id){
    $("#installed_point_enclosure").hide();
    $("#installed_enclosures").load("ui/installed.enclosure.point.sublist.php?point_id="+point_id);
}
</script>