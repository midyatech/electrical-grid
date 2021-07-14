<?php
include_once realpath(__DIR__ . '/..').'/include/checksession.php';
include_once realpath(__DIR__ . '/..').'/include/checkpermission.php';
include_once realpath(__DIR__ . '/..').'/include/settings.php';
include_once realpath(__DIR__ . '/..').'/include/header.php';
include_once realpath(__DIR__ . '/..').'/class/Dictionary.php';
include_once realpath(__DIR__ . '/..').'/class/Installation.class.php';

$Installation = new Installation();
$dictionary = new Dictionary($LANGUAGE);
$dictionary->GetAllDictionary();

$station_id = $feeder_id = $transformer_id = NULL;
$condition = $condition1 = $ServicePoints = $TransformerArr = array();

$AreaArr = $Installation->GetInstallationArea();
$StationArr = $Installation->GetStationByArea();

if(isset($_GET["area_id"]) && $_GET["area_id"] != ""){
    $area_id = $_GET["area_id"];
    $StationArr = $Installation->GetStationByArea($area_id);
}

if(isset($_GET["station_id"]) && $_GET["station_id"] != ""){
    $station_id = $_GET["station_id"];
}

if(isset($_GET["feeder_id"]) && $_GET["feeder_id"] != ""){
    $feeder_id = $_GET["feeder_id"];
    $condition["feeder_id"] = $feeder_id;
    $condition["ponit_count"] = array("Operator"=>">","Value"=>0, "Type"=>"int");
    $TransformerArr = $Installation->GetTransformerArr($condition);
}

$FeederArr = $Installation->GetFeederByStation($station_id);


if( isset($_REQUEST["transformer_id"]) && $_REQUEST["transformer_id"] != NULL ){
    $transformer_id = $_REQUEST["transformer_id"];
    $condition1["transformer_id"] = $transformer_id;

    $ServicePoints = $Installation->GetServicePointInstallation($condition1);
    for ($i=0; $i<count($ServicePoints); $i++) {
        $ServicePoints[$i]["color"] = GetColor($ServicePoints[$i]["installation_status_id"], $ServicePoints[$i]["point_type_id"]);
    }

    $CenterTransformer = $Installation->GetServicePointInstallation(array("service_point.point_id"=>$transformer_id));
}

$operation = "save";
$operationCalssName = "save_service_point";
$point_id = NULL;

if( isset($_REQUEST["point_id"]) && $_REQUEST["point_id"] != NULL ){
    $operation = "edit";
    $operationCalssName = "edit_service_point";
    $point_id = $_REQUEST["point_id"];
}

function GetColor($installation_stats_id, $point_type=null)
{
    if ($installation_stats_id == -2) {
        //edited
        $color_status = "red";
    }
    if ($installation_stats_id == -1) {
        //edited
        $color_status = "yellow";
    } if ($installation_stats_id == 1) {
        //new
        if ($point_type == 4) {
            $color_status = "transformer-icon";
        } else {
            $color_status = "blue";
        }
    } else if ($installation_stats_id == 2) {
        //completed
        if ($point_type == 4) {
            $color_status = "transformer-icon-green";
        } else {
            $color_status = "green";
        }
    } else if ($installation_stats_id == 3) {
        //deleted
        $color_status = "grey";
    }
    return $color_status;
}
?>

<!-- <link rel="stylesheet" href="https://unpkg.com/leaflet@1.3.1/dist/leaflet.css"
    integrity="sha512-Rksm5RenBEKSKFjgI3a41vrjkw4EVPlJ3+OiI65vTjIdo9brlAacEuKOiQ5OFh7cOI1bkDwLqdLw3Zg0cRJAAQ=="
    crossorigin="" />
<script src="https://unpkg.com/leaflet@1.3.1/dist/leaflet.js" integrity="sha512-/Nsx9X4HebavoBvEBuyp3I7od5tA0UzAxs+j83KgC8PU0kgB4XiK4Lfe4y4cgBtaRJQEIFCW+oC506aPT2L1zw=="
        crossorigin=""></script> -->
<link rel="stylesheet" href="../osm/leaflet.css" />
<script src="../osm/leaflet.js" ></script>
<script src="../js/OpenMap.js"></script>
<link rel="stylesheet" href="../osm/Control.FullScreen.css" />
<script src="../osm/Control.FullScreen.js"></script>


<div class="row">
    <form action="code/service.point.add.code.php" role="form" class="" id="service_point" method="post">
        <div class="col-lg-12 wizard">
            <div class="tab-content">
                <?php /*
                <div class="portlet box green">
                    <div class="portlet-title">
                        <div class="caption">
                            <?php echo $dictionary->GetValue("Code in Map");?>
                        </div>
                        <div class="tools">
                            <a href="javascript:;" class="expand" data-original-title="" title=""> </a>
                        </div>
                    </div>
                    <div class="portlet-body" style="display:none;" >
                        <?php
                        $data = array();
                        //$data[] = array('S.No.'=>'', 'Description'=>'', 'Code'=>'', 'Code_in_Map'=>'');
                        $data[] = array('S.No.'=>'1', 'Description'=>'Small Enclosure 1 CT Meter', 'Code'=>'S3P1CT', 'Code_in_Map'=>'A');
                        $data[] = array('S.No.'=>'2', 'Description'=>'Small Enclosure Single Phase 1 Meter', 'Code'=>'S1P1M', 'Code_in_Map'=>'B');
                        $data[] = array('S.No.'=>'3', 'Description'=>'Small Enclosure Single Phase 2 Meter', 'Code'=>'S1P2M', 'Code_in_Map'=>'C');
                        $data[] = array('S.No.'=>'4', 'Description'=>'Small Enclosure Single Phase 3 Meter', 'Code'=>'S1P3M', 'Code_in_Map'=>'D');
                        $data[] = array('S.No.'=>'5', 'Description'=>'Large Enclosure Single Phase 4 Meter', 'Code'=>'L1P4M', 'Code_in_Map'=>'E');
                        $data[] = array('S.No.'=>'6', 'Description'=>'Large Enclosure Single Phase 5 Meter', 'Code'=>'L1P5M', 'Code_in_Map'=>'F');
                        $data[] = array('S.No.'=>'7', 'Description'=>'Large Enclosure Single Phase 6 Meter', 'Code'=>'L1P6M', 'Code_in_Map'=>'G');
                        $data[] = array('S.No.'=>'8', 'Description'=>'Small Enclosure Single Phase 1 Meter with Gateway', 'Code'=>'S1P1MG', 'Code_in_Map'=>'H');
                        $data[] = array('S.No.'=>'9', 'Description'=>'Small Enclosure Single Phase 2 Meter with Gateway', 'Code'=>'S1P2MG', 'Code_in_Map'=>'I');
                        $data[] = array('S.No.'=>'10', 'Description'=>'Small Enclosure Single Phase 3 Meter with Gateway', 'Code'=>'S1P3MG', 'Code_in_Map'=>'J');
                        $data[] = array('S.No.'=>'11', 'Description'=>'Large Enclosure Single Phase 4 Meter with Gateway', 'Code'=>'L1P4MG', 'Code_in_Map'=>'K');
                        $data[] = array('S.No.'=>'12', 'Description'=>'Large Enclosure Single Phase 5 Meter with Gateway', 'Code'=>'L1P5MG', 'Code_in_Map'=>'L');
                        $data[] = array('S.No.'=>'13', 'Description'=>'Large Enclosure Single Phase 6 Meter with Gateway', 'Code'=>'L1P6MG', 'Code_in_Map'=>'M');
                        $data[] = array('S.No.'=>'14', 'Description'=>'Large Enclosure Three Phase 1 Meter', 'Code'=>'L3P1M', 'Code_in_Map'=>'N');
                        $data[] = array('S.No.'=>'15', 'Description'=>'Large Enclosure Three Phase 2 Meter', 'Code'=>'L3P2M', 'Code_in_Map'=>'O');
                        $data[] = array('S.No.'=>'16', 'Description'=>'Large Enclosure 1 CT Meter', 'Code'=>'L3P1CT', 'Code_in_Map'=>'P');
                        $data[] = array('S.No.'=>'17', 'Description'=>'Large Enclosure Three Phase 1 Meter', 'Code'=>'L3P1MG', 'Code_in_Map'=>'Q');
                        $data[] = array('S.No.'=>'18', 'Description'=>'Large Enclosure Three Phase 2 Meter', 'Code'=>'L3P2MG', 'Code_in_Map'=>'R');

                        $cols = array();
                        $cols[] = array("column"=>"S.No.");
                        $cols[] = array("column"=>"Description");
                        $cols[] = array("column"=>"Code");
                        $cols[] = array("column"=>"Code_in_Map");
                        $html->Table($data, $cols, array());
                        ?>
                    </div>
                </div>
                */ ?>
                <div class="portlet solid blue">
                    <div class="portlet-title">
                        <div class="caption">
                            <?php echo $dictionary->GetValue("Point_Filters");?>
                        </div>
                        <div class="tools">
                            <a href="javascript:;" class="<?php echo ($transformer_id==null) ? "collapse" : "expand"; ?>" data-original-title="" title=""> </a>
                        </div>
                    </div>
                    <div class="portlet-body" <?php echo ($transformer_id==null) ? "" : 'style="display:none;"'; ?> >
                        <?php
                        $html->OpenDiv("row");
                        {
                            $html->OpenSpan(12);
                            {
                                $html->DrawFormField("select", "area_id", $area_id, $AreaArr, array("class"=>"form-control", "optional"=>"true"));
                                $html->DrawFormField("select", "station_id", $station_id, $StationArr, array("class"=>"form-control", "optional"=>"true"));
                                $html->DrawFormField("select", "feeder_id", $feeder_id, $FeederArr, array("class"=>"form-control", "optional"=>"true"));
                                $html->DrawFormField("select", "transformer_id", $transformer_id, $TransformerArr, array("class"=>"form-control", "optional"=>"true"));
                            }
                            $html->CloseSpan();
                        }
                        $html->CloseDiv();
                        ?>
                    </div>
                </div>

                <sectoin id='InstallationSummary'></sectoin>

                <div class="row">
                    <div class="col-md-12">
                        <!-- <button type="button" class="btn btn-lg btn-primary refresh_location" style="position: absolute; z-index:1000; margin: 12px; left: 13px; top: 65px; padding: 10px; font-size: 14px; line-height: 0;"> <i class="fa fa-crosshairs"></i></button> -->
                        <div class="row" style="margin: 5px; padding: 5px; text-align: center; background: #bcd5ed;">
                            <div class="col-lg-2"><img src="../img/transformer-icon.png" width="22px"> <?php print $dictionary->GetValue("transformer"); ?></div>
                            <div class="col-lg-2"><img src="../img/blue.png"> <?php print $dictionary->GetValue("no_any_action"); ?></div>
                            <div class="col-lg-2"><img src="../img/yellow.png"> <?php print $dictionary->GetValue("installed_and_pending"); ?></div>
                            <div class="col-lg-2"><img src="../img/green.png"> <?php print $dictionary->GetValue("completed"); ?></div>
                            <div class="col-lg-2"><img src="../img/grey.png"> <?php print $dictionary->GetValue("deactivated"); ?></div>
                            <div class="col-lg-2"><img src="../img/red.png"> <?php print $dictionary->GetValue("has_problem"); ?></div>
                        </div>
                        <div id="map"></div>
                    </div>
                </div>

            </div>
        </div>
    </form>
</div>
<style>
    .installation-actions li.btn-block {
        margin-left: 0;
        margin-right: 0;
    }
    #map {
        width: 100%;
        height: 500px;
    }
    .leaflet-tooltip.tooltipClass {
        background: rgba(255, 255,255, 0.6);
        border: 1px solid silver;
        margin-left: 3px;
        padding: 0 1px;
        font-size: 11px;
        -webkit-box-shadow: none;
        -moz-box-shadow: none;
        box-shadow: none;
    }
    .leaflet-tooltip-left.tooltipClass::before {
        border-left-color: rgba(255, 255,255, 0.5);
        content: none;
    }
    .leaflet-tooltip-right.tooltipClass::before {
        border-right-color: rgba(255, 255,255, 0.5);
        margin-left: 2px;
        content: none;
    }
</style>

<script src="../js/map_helper.js"></script>
<script>
var mapHeight;
var androidInterval;
var tooltipMode = 0;

// var CenrterLat = "36.191175";
// var CenterLong = "2.009132";
var CenrterLat = "9.0778";
var CenterLong = "8.6775";

<?php if( isset($CenterTransformer) && $CenterTransformer ) { ?>
    CenrterLat = "<?php print $CenterTransformer[0]["latitude"]; ?>";
    CenterLong = "<?php print $CenterTransformer[0]["longitude"]; ?>";
<?php } ?>

var map = new Map("map", CenrterLat, CenterLong, false);
map.center(CenrterLat, CenterLong);
map.zoom(18);

map.addCustomControl("../img/current.png", "Current Location",
        function(e){
            getCoordinates();
        });

map.addCustomControl("../img/tool-tip.png", "Show Single Phase Meters Count",
function(e){
    //onclick
    enableTooltip();
    if (tooltipMode == 1) {
        e.srcElement.classList.add("selectedButton");
    } else {
        e.srcElement.classList.remove("selectedButton");
    }
});

var layerGroup = map.addGroup();

function OpenPointModal(point_id)
{
    OpenModal("ui/install.enclosure.php", {"point_id": point_id});
}
<?php
$area_name = "";
if( isset($ServicePoints) && $ServicePoints )
{
    $j = count( $ServicePoints );
    for ( $i=0; $i < $j; $i++ ){
        $point_id = $ServicePoints[$i]['point_id'];
        if($ServicePoints[$i]["point_type_id"] < 4 && $ServicePoints[$i]["latitude"] != null && $ServicePoints[$i]["longitude"] != null){
        ?>
            color = "<?php print $ServicePoints[$i]["color"]; ?>";
            map.addMarker(<?php print $ServicePoints[$i]["latitude"]; ?>,
                            <?php print $ServicePoints[$i]["longitude"]; ?>,
                            layerGroup, color,
                            null,
                            null, "<?php echo $point_id;?>", null,
                            {
                                "event":"click",
                                "function": function(e) { OpenPointModal("<?php echo $point_id;?>", e); }
                            },
                            {
                                <?php if($ServicePoints[$i]['needs_gateway'] > 0){ ?>
                                "tooltip": "G"
                                <?php } ?>
                                /* "tooltip": "< ?php print $ServicePoints[$i]['codes']; ?>" */
                            }
                        );
        <?php
        } else if($ServicePoints[$i]["point_type_id"] == 4 && $ServicePoints[$i]["latitude"] != null && $ServicePoints[$i]["longitude"] != null){
            $PopUpString = "";
        /*
            $transformer_id = $ServicePoints[$i]['point_id'];
            $station_id = $ServicePoints[$i]['station_id'];
            $feeder_id = $ServicePoints[$i]['feeder_id'];
            $capacity_id = $ServicePoints[$i]['capacity_id'];
            $transformer_number = $ServicePoints[$i]['transformer_number'];

            $PopUpString = $station_id.'/'.$feeder_id.'/'.$capacity_id.'/'.$transformer_number.'</b><br />'.
                            '<a href="installation.php?transformer_id='.$transformer_id.'&station_id='.$station_id.'&feeder_id='.$feeder_id.'">'.$dictionary->GetValue("get_points").'</a>';
        */
        ?>
            icon = "<?php print $ServicePoints[$i]["color"]; ?>";
            color = {img: icon, "size":16 };
            map.addMarker(<?php print $ServicePoints[$i]["latitude"]; ?>,
                            <?php print $ServicePoints[$i]["longitude"]; ?>,
                            layerGroup,
                            color,
                            null,
                            "<?php print $dictionary->GetValue("area_id")." : ".$area_name ?>",
                            "<?php echo $point_id;?>",
                            null,
                            {
                                "event":"click",
                                "function": function(e) { OpenPointModal("<?php echo $point_id;?>", e); }
                            },
                            {
                                "tooltip": "Tr:"+"<?php print $ServicePoints[$i]['transformer_number']; ?>"
                            }
                        );
        <?php
        }
    }
}

?>
$(function() {

    //Get first load page height
    mapHeight = $( window ).height() - 150;

    //hide map by default
    $("#map").css("height", mapHeight);

    cos = ",";
    if ("undefined" !== typeof Android) {
        getCoordinates();
        androidInterval = setInterval(function(){
            cos = Android.askCoordinates();
            //cos = "36.8888,38.8888";
            getCoordinates();
        }, 60000);
    }

    <?php if(isset($_REQUEST["transformer_id"])){ ?>
        transformer_id = "<?php print $_REQUEST["transformer_id"]; ?>";
        $("#InstallationSummary").load("ui/enclosure.installation.summary.php", {"transformer_id":transformer_id});
    <?php } ?>

    $('#area_id').on('change', function () {
        area_id = $(this).val();
        // FillFeeder(area_id);
        window.location.href = "installation.php?&area_id=" + area_id;
    });

    $('#station_id').on('change', function () {
        area_id = $('#area_id').val();
        station_id = $(this).val();
        FillFeeder(station_id);
        //GetTransformer(0, station_id);
    });

    $('#feeder_id').on('change', function () {
        feeder_id = $(this).val();
        area_id = $('#area_id').val();
        station_id = $("#station_id").val();
        //FillTransformer(feeder_id);
        window.location.href = "installation.php?&area_id=" + area_id + "&station_id=" + station_id + "&feeder_id=" + feeder_id;
    });

    $('#transformer_id').on('change', function() {
        transformer_id = this.value;
        station_id = $("#station_id").val();
        feeder_id = $("#feeder_id").val();
        window.location.href = "installation.php?transformer_id=" + transformer_id + "&area_id=" + area_id + "&station_id=" + station_id + "&feeder_id=" + feeder_id;
    });

    //hide default tooltips
    showTooltips(true);
});


function ChangePointIcon(point_id, color)
{
    map.setMarkerIconByAttr("point_id", point_id.toString(), color);
}

function GetInstallationStatusColor(status_id){
    switch (status_id) {
        case "-2":
            color = "red";
            break;
        case "-1":
            color = "yellow";
            break;
        case "1":
            color = "blue";
            break;
        case "2":
            color = "green";
            break;
        case "3":
            color = "grey";
            break;
    }

    return color;
}

function EnclosurePointList(point_id){
    $("#installed_point_enclosure").hide();
    $("#installed_enclosures").load("ui/installed.enclosure.point.sublist.php?point_id="+point_id);
}

function getCoordinates(){
    if ("undefined" !== typeof Android) {
        cos = Android.askCoordinates();
        //cos = "36.8888,38.8888";
        //cos="36.21448693,44.12170499";
        if(cos.trim() != "," && cos.trim() != "null,null" && cos.trim() != "(,)"){
            setCoordinates(cos);
            CheckDistance(cos);
        }
    }
}

function setCoordinates(cos){
    setTimeout(function () {
        latlong = cos.split(",");
        lat = latlong[0];
        long = latlong[1];

        map.center(lat, long);
    }, 1500);
}

function enableTooltip()
{
    tooltipMode = 1 - tooltipMode;
    if (tooltipMode == 0) {
        showTooltips(false);
    } else {
        showTooltips(true);
    }
}

function showTooltips(show)
{
    if (show) {
        map.map.eachLayer(function(l) {
            if (l.getTooltip) {
                var toolTip = l.getTooltip();
                if (toolTip) {
                    this.map.map.openTooltip(toolTip);
                }
            }
        });
    } else {
        map.map.eachLayer(function(l) {
            if (l.getTooltip) {
                var toolTip = l.getTooltip();
                if (toolTip) {
                    this.map.map.closeTooltip(toolTip);
                }
            }
        });
    }
}

function FillFeeder(station_id){
    if( station_id > 0){
        FillListOptions("ui/get.feeder.php?station_id="+station_id, "feeder_id", true);
    } else {
        FillListOptions("i/get.feeder.php", "feeder_id", true);
    }
}

function FillTransformer(feeder_id){
    if( feeder_id > 0){
        FillListOptions("ui/get.transformers.php?feeder_id="+feeder_id, "transformer_id", true);
    } else {
        FillListOptions("ui/get.transformers.php", "transformer_id", true);
    }
}

function SetBarcodeValue(sn){
    $("#enclosure_sn").val(sn);
}

function CheckDistance(current_cos)
{
    //console.log(current_cos)
    latlong = cos.split(",");
    c_lat = latlong[0];
    c_long = latlong[1];
    cLatLng = L.latLng(c_lat, c_long);
    //console.log(cLatLng);
    if ($('#point_coordinates').length) {
        point_cos = $('#point_coordinates').val();
        latlong = point_cos.split(",");
        p_lat = latlong[0];
        p_long = latlong[1];
        pLatLng = L.latLng(p_lat, p_long);
        var distance = pLatLng.distanceTo(cLatLng);
        if (distance > 5) {
            //distance.toFixed(1)
            $("#message").html('<div class="alert alert-danger">You are standing <b>'+Math.round(distance)+' m</b> from the current point.</div>');
        }
    }
}

</script>
<script src="js/installation.js?v=1"></script>
<script src="../assets/global/scripts/datatable.js" type="text/javascript"></script>
<script src="../assets/global/plugins/datatables/datatables.min.js" type="text/javascript"></script>
<script src="../assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js" type="text/javascript"></script>
<?php
require_once '../include/footer.php';
?>