<?php
include_once realpath(__DIR__ . '/..').'/include/checksession.php';
include_once realpath(__DIR__ . '/..').'/include/checkpermission.php';
include_once realpath(__DIR__ . '/..').'/include/settings.php';
include_once realpath(__DIR__ . '/..').'/include/header.php';
include_once realpath(__DIR__ . '/..').'/class/Dictionary.php';
include_once realpath(__DIR__ . '/..').'/class/Survey.class.php';

$Survey = new Survey();
$dictionary = new Dictionary($LANGUAGE);
$dictionary->GetAllDictionary();

$station_id = $feeder_id = $transformer_id = NULL;
$condition = $condition1 = $ServicePoints = $TransformerArr = $FeederArr = array();

$radius_distance = 50;

if(isset($_REQUEST["station_id"]) && $_REQUEST["station_id"] != ""){
    $station_id = $_REQUEST["station_id"];
    $condition["station_id"] = $condition1["station_id"] = $station_id;
    $FeederArr = $Survey->GetFeederByStation($station_id);
}

if(isset($_REQUEST["feeder_id"]) && $_REQUEST["feeder_id"] != ""){
    $feeder_id = $_REQUEST["feeder_id"];
    $condition["feeder_id"] = $condition1["feeder_id"] = $feeder_id;

    $condition1["ponit_count"] = array("Operator"=>">","Value"=>0, "Type"=>"int");
    $condition1["service_point.point_type_id"] = 4;
    $TransformerArr = $Survey->GetTransformerArr($condition1);
}
if (count($TransformerArr) > 0) {
    $optional_value = "All";
} else {
    $optional_value = "--";
}

if( isset($_REQUEST["transformer_id"]) && $_REQUEST["transformer_id"] != NULL ){
    $transformer_id = $_REQUEST["transformer_id"];
    $condition["transformer_id"] = $transformer_id;
}

if( isset($_REQUEST["radius_distance"]) && $_REQUEST["radius_distance"] != NULL ){
    $radius_distance = $_REQUEST["radius_distance"];
}

if(isset($_REQUEST["center"]) && $_REQUEST["center"] != ""){
    $map_center = $Survey->GetServicePoint(array("service_point.point_id"=>$_REQUEST["center"]));
} else {
    if ($transformer_id != null) {
        $map_center = $Survey->GetServicePoint(array("l.transformer_id"=>$transformer_id));
    }
}

if($feeder_id != null || $transformer_id != null){
    //$ServicePoints = $Survey->GetServicePoint($condition);
    $ServicePoints = $Survey->GetGatewayServicePoint($condition);
}

$StationArr = $Survey->GetStationByArea();
$DistanceArr = array(array(0=>10, 1=>"10 m"), array(0=>20, 1=>"20 m"), array(0=>30, 1=>"30 m"), array(0=>40, 1=>"40 m"), array(0=>50, 1=>"50 m"), array(0=>60, 1=>"60 m"), array(0=>70, 1=>"70 m"), array(0=>80, 1=>"80 m"), array(0=>90, 1=>"90 m"), array(0=>100, 1=>"100 m"))
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

                <?php
                $html->OpenDiv("row");
                {
                    $html->OpenSpan(3);
                    {
                        $html->DrawFormField("select", "station_id", $station_id, $StationArr, array("class"=>"form-control", "optional"=>"true"));
                    }
                    $html->CloseSpan();
                    $html->OpenSpan(3);
                    {
                        $html->DrawFormField("select", "feeder_id", $feeder_id, $FeederArr, array("class"=>"form-control", "optional"=>"true"));
                    }
                    $html->CloseSpan();
                    $html->OpenSpan(3);
                    {
                        $html->DrawFormField("select", "transformer_id", $transformer_id, $TransformerArr, array("class"=>"form-control", "optional"=>$optional_value));
                    }
                    $html->CloseSpan();
                    $html->OpenSpan(3);
                    {
                        $html->DrawFormField("select", "radius_distance", $radius_distance, $DistanceArr, array("class"=>"form-control"));
                    }
                    $html->CloseSpan();
                }
                $html->CloseDiv();
                print "<sectoin id='GatewaySummary'></sectoin>";
                ?>

                <div class="row">
                        <div class="col-md-12"><div class="row" style="margin: 5px; padding: 5px; text-align: center; background: #bcd5ed;">
                            <div class="col-lg-4"><img src="../img/transformer-icon.png" width="24px"> <?php print $dictionary->GetValue("transformer"); ?></div>
                            <div class="col-lg-4"><img src="../img/blue.png"> <?php print $dictionary->GetValue("point_without_gateway"); ?></div>
                            <div class="col-lg-4"><img src="../img/red.png"> <?php print $dictionary->GetValue("point_with_gateway"); ?></div>
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
    .selectedMarker {
        background: #F40E0E;
        border: solid 1px #9D0016;
    }
    .selectedButton {
        background-color: #ccc !important;/*#f4f4f4*/
    }
    .leaflet-marker-icon.small {
        width: 6px !important;
        height: 6px !important;
        margin-left: -3px !important;
        margin-top: -3px !important;
        padding: 0;
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

// var CenrterLat = "36.191175";
// var CenterLong = "44.009134";
var CenrterLat = "9.0778";
var CenterLong = "8.6775";
<?php if( isset($map_center) && $map_center ) { ?>
    CenrterLat = "<?php print $map_center[0]["latitude"]; ?>";
    CenterLong = "<?php print $map_center[0]["longitude"]; ?>";
<?php } else if( isset($ServicePoints) && $ServicePoints ) { ?>
    CenrterLat = "<?php print $ServicePoints[0]["latitude"]; ?>";
    CenterLong = "<?php print $ServicePoints[0]["longitude"]; ?>";
<?php } ?>
var map = new Map("map", CenrterLat, CenterLong, false, false);
map.center(CenrterLat, CenterLong);
map.zoom(18);

var layerGroup = map.addGroup();

<?php
$area_name = "";
if( isset($ServicePoints) && $ServicePoints )
{
    $j = count( $ServicePoints );

    for ( $i=0; $i < $j; $i++ ){
        $PopUpString = "";
        $point_id = $ServicePoints[$i]['point_id'];
        $point_type_id = $ServicePoints[$i]['point_type_id'];
        $single_phase_consumers = $ServicePoints[$i]['single_phase_consumers'];
        $three_phase_consumers = $ServicePoints[$i]['three_phase_consumers'];
        $accuracy_id = $ServicePoints[$i]['accuracy_id'];
        $point_type = $ServicePoints[$i]['point_type'];
        /*
        $station_id = $ServicePoints[$i]['station_id'];
        //$_feeder_id = $ServicePoints[$i]['feeder_id'];
        $capacity_id = $ServicePoints[$i]['capacity_id'];
        $transformer_number = $ServicePoints[$i]['transformer_number'];
        */

        $station_id1 = $ServicePoints[$i]['station_id1'];
        $_feeder_id1 = $ServicePoints[$i]['feeder_id1'];
        $capacity_id1 = $ServicePoints[$i]['capacity_id1'];
        $transformer_number1 = $ServicePoints[$i]['transformer_number1'];



        $area_name = $ServicePoints[$i]['NODE_NAME'];
        //$sequence = $ServicePoints[$i]['sequence'];
        $gateway = $ServicePoints[$i]['needs_gateway'];
        $_transformer = $ServicePoints[$i]['transformer_id'];
        //$color = $ServicePoints[$i]['color'];
        if( $gateway ){
            $color = "red";
            $inverse_gateway = 0;
            $popup_gateway_text = $dictionary->GetValue("remove_gateway");
        } else {
            $color = "blue";
            $inverse_gateway = 1;
            $popup_gateway_text = $dictionary->GetValue("add_gateway");
        }
            $PopUpString =  "#".$point_id.'<br />'.
                            $point_type.'<br />'.
                            $dictionary->GetValue("single_phase_consumers").': <b>'.
                            $single_phase_consumers.'</b><br />'.
                            $dictionary->GetValue("three_phase_consumers").': <b>'.
                            $three_phase_consumers.'</b><br />'.
                            $dictionary->GetValue("accuracy_id").': <b>'.
                            $accuracy_id.'</b><br />'.
                            $dictionary->GetValue("transformer").': <b>'.
                            $station_id1.'/'.$_feeder_id1.'/'.$capacity_id1.'/'.$transformer_number1.'</b><br />'.
                            '<br />'.
                            '<a href="#" data-point_id="'.$point_id.'" data-gateway="'.$inverse_gateway.'" data-transformer_id="'.$_transformer.'" class="btn btn-default add_gateway"><i class="fa fa-signal"></i>&nbsp;'.$popup_gateway_text.'</a>';
        if( $point_type_id < 4 && $ServicePoints[$i]["latitude"] != null && $ServicePoints[$i]["longitude"] != null){
        ?>
            single_phase_consumers = "<?php echo $single_phase_consumers > 0 ?$single_phase_consumers : ""; ?>";
            gateway = "<?php echo $gateway;?>";
            color = "<?php print $color; ?>";//"blue";//GetStatusColor(<?php //print $point_type_id; ?>)
            popupString = '<?php print $PopUpString; ?>';
            map.addMarker(<?php print $ServicePoints[$i]["latitude"]; ?>,
                            <?php print $ServicePoints[$i]["longitude"]; ?>,
                            layerGroup,
                            color,
                            popupString,
                            null,
                            "<?php print $point_id; ?>",
                            null,
                            {
                                "event":"click",
                                "function": function(e) {
                                    onMarkerClick(e);
                                }
                            }, {
                                "tooltip": single_phase_consumers,
                                "attr": {
                                    "gateway":gateway
                                }
                            }
            );
        <?php
        } else if( $point_type_id == 4 && $ServicePoints[$i]["latitude"] != null && $ServicePoints[$i]["longitude"] != null){
            $PopUpString = "";
            $_transformer = $ServicePoints[$i]['transformer_id'];
            $station_id = $ServicePoints[$i]['station_id'];
            $_feeder_id = $ServicePoints[$i]['feeder_id'];
            $capacity_id = $ServicePoints[$i]['capacity_id'];
            $transformer_number = $ServicePoints[$i]['transformer_number'];

                $PopUpString = "#".$point_id.'<br />'.$station_id.'/'.$_feeder_id.'/'.$capacity_id.'/'.$transformer_number.'</b><br />'.
                                '<a href="add_gateway.php?transformer_id='.$_transformer.'&station_id='.$station_id.'&feeder_id='.$_feeder_id.'">'.$dictionary->GetValue("get_points").'</a>';
            ?>
            color = {img: "transformer-icon", "size":24 }; //GetStatusColor(< ?php print $point_type_id; ?>)
            map.addMarker(<?php print $ServicePoints[$i]["latitude"]; ?>, <?php print $ServicePoints[$i]["longitude"]; ?>, layerGroup, color, '<?php print $PopUpString; ?>', null, null, null,
                {
                    "event":"click",
                    "function": function(e) {
                        onMarkerClick(e);
                    }
                }
            );
        <?php
        }
    }
}

?>
$(function() {

    station_id = "<?php print $station_id; ?>";
    feeder_id = "<?php print $feeder_id; ?>";
    transformer_id = "<?php print $transformer_id; ?>";

    //Get first load page height
    mapHeight = $( window ).height() - 150;

    //hide map by default
    $("#map").css("height", mapHeight);
    //add new icon to map
    map.addCustomControl("../img/ln.png", "Calculate Distance",
        function(e){
            //onclick the button: enable line mode, and change bg color
            enableLine();
            if (lineMode == 1) {
                e.srcElement.classList.add("selectedButton");
            } else {
                e.srcElement.classList.remove("selectedButton");
            }
        });

    //add new icon to map
    map.addCustomControl("../img/signal.png", "Show Gateway Coverage",
        function(e){
            //onclick the button: enable line mode, and change bg color
            enableCoverage();
            if (coverageMode == 1) {
                e.srcElement.classList.add("selectedButton");
            } else {
                e.srcElement.classList.remove("selectedButton");
            }
        });

    //add new icon to map
    map.addCustomControl("../img/resize.png", "Change Icon Size",
        function(e){
            //onclick
            if (iconSize == 12) {
                $(".leaflet-marker-icon").addClass("small");
                iconSize = 8;
            } else {
                $(".leaflet-marker-icon").removeClass("small");
                iconSize = 12;
            }
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


    if (feeder_id != "" || transformer_id != "") {
        $("#GatewaySummary").load("ui/service.point.gateway.summary.php", {"feeder_id":feeder_id, "transformer_id":transformer_id});
    }


    $('#station_id').on('change', function () {
        station_id = $(this).val();
        window.location.href = "add_gateway.php?&station_id=" + station_id;
        //FillFeeder(station_id);
    });

    $('#feeder_id').on('change', function () {
        feeder_id = $(this).val();
        station_id = $("#station_id").val();
        window.location.href = "add_gateway.php?&station_id=" + station_id + "&feeder_id=" + feeder_id;
    });

    $('#transformer_id').on('change', function() {
        transformer_id = this.value;
        station_id = $("#station_id").val();
        feeder_id = $("#feeder_id").val();
        window.location.href = "add_gateway.php?transformer_id=" + transformer_id + "&station_id=" + station_id + "&feeder_id=" + feeder_id;
    });

    $('#radius_distance').on('change', function() {
        radius_distance = this.value;
        station_id = $("#station_id").val();
        feeder_id = $("#feeder_id").val();
        transformer_id = $("#transformer_id").val();
        window.location.href = "add_gateway.php?transformer_id=" + transformer_id + "&station_id=" + station_id + "&feeder_id=" + feeder_id + "&radius_distance=" + radius_distance;
    });

    //hide default tooltips
    showTooltips(false);

});

var tooltipMode = 0;
var lineMode = 0;
var coverageMode = 0;
var linePoints = [];
var circles = [];
var changedMarkers = [];
var distanceLine;
var iconSize = 12;

function onMarkerClick(e)
{
    if (lineMode == 1) {
        map.map.closePopup();
        addLinePoints(e)
    }
}


function enableLine()
{
    lineMode = 1 - lineMode;
    if (lineMode == 0) {
        resetLine();
    }
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

function enableCoverage()
{
    coverageMode = 1 - coverageMode;
    if (coverageMode == 1) {
        showCoverage(true);
    } else {
        showCoverage(false);
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

function showCoverage(show)
{
    var distance = "<?php print $radius_distance ?>";
    if (show) {
        for (i=0; i<map.markers.length; i++) {
            p = map.markers[i];
            if(p.properties.gateway == 1) {
                coverageCircle = map.addCircle([p._latlng["lat"], p._latlng["lng"]], distance, 'red');
                circles.push(coverageCircle);
            }
        }

    } else {
        //hide
        for (i=0; i<circles.length; i++) {
            map.map.removeLayer(circles[i]);
        }
    }
}

function addLinePoints(e)
{
    if (lineMode == 1) {
        //console.log(linePoints)
        point = e.latlng;
        if (linePoints.length < 2) {
            linePoints.push(point);
            changedMarkers.push(e)
            highlightMarker(e, "add");

            //if we have now two points, draw line
            if (linePoints.length == 2) {
                drawLine();
            }
        } else {
            resetLine();
        }
    }
}

function drawLine()
{
    var pointA = new L.LatLng(linePoints[0]["lat"], linePoints[0]["lng"]);
    var pointB = new L.LatLng(linePoints[1]["lat"], linePoints[1]["lng"]);
    var pointList = [pointA, pointB];
    distance = pointA.distanceTo(pointB);
    distanceStr = Math.round(distance) +" m";
    distanceLine = map.addLine(pointList, "red", distanceStr, {"popup":true})
}

function resetLine () {
    lineMode = 0;
    linePoints = [];
    for (i=0; i<changedMarkers.length; i++) {
        highlightMarker(changedMarkers[i], "remove");
    }
    if (distanceLine != undefined) {
        map.map.removeLayer(distanceLine);
    }
    $(".selectedButton").removeClass("selectedButton");
}

function highlightMarker(e, action)
{
    if (action =="add") {
        $(e.target._icon).addClass('selectedMarker');
    } else {
        $(e.target._icon).removeClass('selectedMarker');
    }
}

function ChangePointIcon(point_id, color)
{
    map.setMarkerIconByAttr("point_id",  point_id.toString(), color);
}

</script>
<script src="js/survey.js?v=6"></script>
<?php
require_once '../include/footer.php';
?>