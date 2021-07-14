<?php
include '../include/header.php';
require_once realpath(__DIR__ . '/..').'/include/settings.php';
include_once realpath(__DIR__ . '/..').'/include/checksession.php';
include_once realpath(__DIR__ . '/..').'/include/checkpermission.php';
require_once realpath(__DIR__ . '/..').'/class/Enclosure.class.php';
require_once realpath(__DIR__ . '/..').'/class/Dictionary.php';

$dictionary = new Dictionary ( $LANGUAGE );
$dictionary->GetAllDictionary ();
$Enclosure = new Enclosure();

$enclosure = $meter = array();
$sn = $enclosure_id = $enclosure_sn = $gateway_id = $latitude = $longitude = $assembly_order_id = NULL;

if (isset($_GET["sn"]) && $_GET["sn"] != ""){
    $sn = $_GET["sn"];

    $EnclosureResult = $Enclosure->SerialSerach($sn);
    $enclosure_id = $EnclosureResult[0]["enclosure_id"];
    if($enclosure_id){
        $enclosure = $Enclosure->GetEnclosureDetails($enclosure_id);
    }
}

if (isset($enclosure) && $enclosure) {
    $enclosure_id = $enclosure[0]["enclosure_id"];
    $enclosure_sn = $enclosure[0]["enclosure_sn"];
    $gateway_id = $enclosure[0]["gateway_id"];
    $assembly_order_id = $enclosure[0]["assembly_order_id"];
    $transformer_details = $enclosure[0]["transformer_details"];

    $point_id = $enclosure[0]["point_id"];
    $latitude = $enclosure[0]["latitude"];
    $longitude = $enclosure[0]["longitude"];

    if( count($enclosure) > 3 ){
        $no_of_meter = 6;
    } else {
        if($enclosure[0]["meter_type_id"] == 1 ){
            $no_of_meter = 3;
        } else if($enclosure[0]["meter_type_id"] == 2){
            $no_of_meter = 2;
        } else if($enclosure[0]["meter_type_id"] == 3){
            $no_of_meter = 1;
        }
    }

    for($i=1; $i<=6; $i++){
        for ($j=0; $j<count($enclosure); $j++) {
            $meter_id = null;
            if ($enclosure[$j]["meter_sequence"] == $i) {
                $meter_id = $enclosure[$j]["meter_id"];
                break;
            }
        }
        $meter["$i"] = $meter_id;
    }
}

function drawMeters($i, $meter){
    global $dictionary;
    echo'<div class="col-md-4 meter" id="m'.$i.'">
            <div class="mt-widget-3">';
                if(isset($meter["$i"])){
                echo '<div class="mt-head bg-white" style="text-align: center">
                        <img src="../img/meter-bw.png" class="meter-img">
                        <div class="mt-head-button">
                        '.$i.'
                        </div>
                    </div>
                    <div class="mt-body-actions-icons">
                        <div class="btn-group btn-group btn-group-justified">
                            <input type="text" id="meter_'.$i.'" name="meter_'.$i.'" class="form-control meter-control" value="'.(isset($meter["$i"]) ? $meter["$i"] : '').'" disabled>
                        </div>
                    </div>';
                } else {
                echo '<div class="mt-head bg-white" style="text-align: center">
                        <div class="mt-head-button">
                        <br/><br/>
                        '.$i.'
                        <br/><br/>
                        <br/>
                        </div>
                    </div>
                    <div class="mt-body-actions-icons">
                    </div>';
                }
        echo '</div>
        </div>';
}
?>

<link href="../assets/layouts/layout/css/enclosure.css" rel="stylesheet" type="text/css" />

<link rel="stylesheet" href="https://unpkg.com/leaflet@1.3.1/dist/leaflet.css"
    integrity="sha512-Rksm5RenBEKSKFjgI3a41vrjkw4EVPlJ3+OiI65vTjIdo9brlAacEuKOiQ5OFh7cOI1bkDwLqdLw3Zg0cRJAAQ=="
    crossorigin="" />
<script src="https://unpkg.com/leaflet@1.3.1/dist/leaflet.js" integrity="sha512-/Nsx9X4HebavoBvEBuyp3I7od5tA0UzAxs+j83KgC8PU0kgB4XiK4Lfe4y4cgBtaRJQEIFCW+oC506aPT2L1zw=="
        crossorigin=""></script>
<script src="../js/OpenMap.js"></script>

<?php
$html->OpenDiv("row");
{
    $html->OpenSpan(12);
    {
        ?>
        <div class="row form">
            <div class="col-lg-6 col-md-offset-3 wizard">
                <div class="row">
                    <div class="col-lg-10 col-md-offset-1">
                        <div class="chat-form">
                            <div class="col-lg-9">
                                <input class="form-control input-lg" type="text" id="sn" name="sn" placeholder="<?php echo $dictionary->GetValue("SN");?>" value="<?php echo $sn; ?>" >
                            </div>
                            <div class="col-lg-3">
                                <button type="button" class="btn btn-lg btn-block btn-success search_sn"> <i class="fa fa-search"></i> <?php echo $dictionary->GetValue("Search"); ?></button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php
        if($enclosure_id){
            $html->OpenWidget ("enclosure_details", null, array('collapse' => true, 'fullscreen'=>true,));
            {
                ?>
                <div class="row form">
                    <div class="col-lg-6 col-md-offset-3 wizard">
                        <div class="row">
                            <div class="col-lg-10 col-md-offset-1">
                                <div class="chat-form">
                                    <div class="col-lg-12">
                                        <input class="form-control input-lg" type="text" id="enclosure_sn" name="enclosure_sn" placeholder="<?php echo $dictionary->GetValue("enclosure SN");?>" value="<?php echo $enclosure_sn; ?>" disabled >
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="well enclosure">
                            <?php if($gateway_id){ ?>
                            <div class="row gateway">
                                <div class="col-md-10 col-lg-4 col-md-offset-1 col-lg-offset-4">
                                    <div class="chat-form option2">
                                        <div class="col-lg-4">
                                            <img src="../img/wifi-router-bw.png" style="width:100%; max-width: 100px">
                                        </div>
                                        <div class="col-lg-8">
                                            <input class="form-control" type="text" id="gateway_id" name="gateway_id" placeholder="<?php echo $dictionary->GetValue("Gateway SN");?>" style="margin-top:34px"  tabindex="2" value="<?php echo $gateway_id; ?>" disabled >
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php } ?>
                            <div class="row meters row1">
                                <?php
                                for($i=1;$i<=$no_of_meter;$i++){
                                    drawMeters($i,$meter);
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
                <?php
            }
            $html->CloseWidget();

            $html->OpenWidget ("map", null, array('collapse' => true, 'fullscreen'=>true,));
            {
                print "<div id='map'></div>";
            }
            $html->CloseWidget();


            $html->OpenWidget ("assembly_order", null, array('collapse' => true, 'fullscreen'=>true,));
            {

                $order = $Enclosure->GetAssemblyOrder($assembly_order_id);
                $code = $order[0]["assembly_order_code"];
                $notes = $order[0]["notes"];
                $start_date = $order[0]["start_date"];

                $data = array();
                if ($order) {
                    $data[] = array("item"=>$dictionary->getValue("assembly_order_code"), "value"=>$order[0]["assembly_order_code"]);
                    $data[] = array("item"=>$dictionary->getValue("create_date"), "value"=>$order[0]["create_date"]);
                    $data[] = array("item"=>$dictionary->getValue("start_date"), "value"=>$order[0]["start_date"]);
                    $data[] = array("item"=>$dictionary->getValue("user_name"), "value"=>$order[0]["user_name"]);
                    $data[] = array("item"=>$dictionary->getValue("notes"), "value"=>$order[0]["notes"]);
                    $data[] = array("item"=>$dictionary->getValue("transformer"), "value"=>$transformer_details);
                }

                $cols = array();
                $cols[] = array("column"=>"item", "style"=>"font-weight:bold;");
                $cols[] = array("column"=>"value");

                //echo '<h2>Assembly Order</h2>';
                $html->Table($data, $cols, array("header"=>false));



            }
            $html->CloseWidget();


            $html->OpenWidget ("shipping", null, array('collapse' => true, 'fullscreen'=>true,));
            {
            }
            $html->CloseWidget();
        }
    }
    $html->CloseSpan();
}
$html->CloseDiv();
?>

<style>
    #map {
        width: 100%;
        height: 500px;
    }
</style>
<script src="../js/map_helper.js"></script>
<script>
// var Lat = "36.191175";
// var Long = "44.009134";
var Lat = "9.0778";
var Long = "8.6775";

var point_id = null;

<?php if( $latitude && $longitude ) { ?>
    var map = new Map("map", Lat, Long, true);
    map.center("<?php print $latitude; ?>", "<?php print $longitude; ?>");
    map.zoom(12);
    var layerGroup = map.addGroup();
    map.addMarker("<?php print $latitude; ?>", "<?php print $longitude; ?>", layerGroup, "red", null, null, "<?php print $point_id; ?>", null);
<?php } ?>


$(document).ready(function(){
    $(".search_sn").on("click", function(){
        sn = $("#sn").val();
        if(sn) {
            window.location.href = "enclosure_tracing.php?sn=" + sn;
        }
    });

});
</script>
<?php include '../include/footer.php'; ?>
<script src="js/enclosure.js?v=1"></script>
