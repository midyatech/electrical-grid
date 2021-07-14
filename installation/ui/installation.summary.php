<?php
include_once realpath(__DIR__ . '/../..').'/include/checksession.php';
include_once realpath(__DIR__ . '/../..').'/include/checkpermission.php';
include_once realpath(__DIR__ . '/../..').'/include/settings.php';
include_once realpath(__DIR__ . '/../..').'/include/header.php';
include_once realpath(__DIR__ . '/../..').'/class/Dictionary.php';
include_once realpath(__DIR__ . '/../..').'/class/Installation.class.php';

$Installation = new Installation();
$dictionary = new Dictionary($LANGUAGE);
$dictionary->GetAllDictionary();

$area_id = $station_id = $feeder_id = $transformer_id = $filter  = NULL;
$condition = $condition1 = $ServicePoints = $FeederArr = $TransformerArr= $linesArr = array();

$StationArr = $Installation->GetStationByArea();
$AreaArr = $Installation->GetInstallationArea();

if(isset($_GET["area_id"]) && $_GET["area_id"] != ""){
    $area_id = $_GET["area_id"];
    // $condition["t.area_id"] = $condition1["area_id"]  = $area_id;
    // $filter .= "&t.area_id=".$area_id;
    $StationArr = $Installation->GetStationByArea($area_id);
}

if(isset($_GET["station_id"]) && $_GET["station_id"] != ""){
    $station_id = $_GET["station_id"];
    $condition["t.station_id"] = $condition1["station_id"]  = $station_id;
    $filter .= "&t.station_id=".$station_id;
    $FeederArr = $Installation->GetFeederByStation($station_id);
}

if(isset($_GET["feeder_id"]) && $_GET["feeder_id"] != ""){
    $feeder_id = $_GET["feeder_id"];
    $condition["t.feeder_id"] = $condition1["feeder_id"] = $feeder_id;
    $filter .= "&t.feeder_id=".$feeder_id;

    $condition1["service_point.point_type_id"] = 4;
    $condition1["ponit_count"] = array("Operator"=>">","Value"=>1, "Type"=>"int");

    $TransformerArr = $Installation->GetTransformerArr($condition1);
}

if( isset($_REQUEST["transformer_id"]) && $_REQUEST["transformer_id"] != NULL ){
    $transformer_id = $_REQUEST["transformer_id"];
    $condition["transformer_id"] = $transformer_id;
    $filter .= "&transformer_id=".$transformer_id;
    $CenterTransformer = $Installation->GetServicePoint(array("service_point.point_id"=>$transformer_id));
}

if($feeder_id){
    $ServicePoints = $Installation->GetServicePoint($condition);
    for ($i=0; $i<count($ServicePoints); $i++) {
        $ServicePoints[$i]["color"] = GetColor($ServicePoints[$i]["installation_status_id"], $ServicePoints[$i]["point_type_id"]);
    }

    $lines = $Installation->GetLines(array("transformer_id"=>$transformer_id));
    if ($lines) {
        $line_id = 0;
        for ($j=0; $j<count($lines); $j++) {
            if ($lines[$j]["latitude"] != null && $lines[$j]["longitude"] != null) {
                if ($lines[$j]["line_id"] != $line_id) {
                    $line_id = $lines[$j]["line_id"];
                    if ($lines[$j]["latitude"] != null && $lines[$j]["longitude"] != null) {
                        $linesArr[$line_id] = array([$lines[$j]["latitude"], $lines[$j]["longitude"]]);
                    }
                } else {
                    $linesArr[$line_id][] = [$lines[$j]["latitude"], $lines[$j]["longitude"]];
                }
            }
        }
    }
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

<div class="row">
    <form action="code/service.point.add.code.php" role="form" class="" id="service_point" method="post">

        <div class="col-lg-12 wizard">
            <div class="tab-content">

                <?php
                $html->OpenDiv("row");
                {
                    $html->OpenSpan(3);
                    {
                        $html->DrawFormField("select", "area_id", $area_id, $AreaArr, array("class"=>"form-control", "optional"=>"true"));
                    }
                    $html->CloseSpan();
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
                        $html->DrawFormField("select", "transformer_id", $transformer_id, $TransformerArr, array("class"=>"form-control", "optional"=>"true"));
                    }
                    $html->CloseSpan();
                }
                $html->CloseDiv();
                /*
                <div class="row">
                    <div class="col-md-12">
                        <div id="map"></div>
                    </div>
                </div>
                */
                ?>
            </div>
        </div>
    </form>


    <div class="portlet light">
        <div class="portlet-title tabbable-line">
            <div class="tools">
                <a href="" class="fullscreen" data-original-title="" title=""> </a>
            </div>
            <ul class="nav nav-tabs pull-left">
                <li class="active">
                    <a href="#map_view" data-toggle="tab" > <?php print $dictionary -> GetValue("map"); ?> </a>
                </li>
                <li>
                    <a href="#list" data-toggle="tab" aria-expanded="true"> <?php print $dictionary -> GetValue("list"); ?> </a>
                </li>
                <li>
                    <a href="#summary" data-toggle="tab" aria-expanded="true"> <?php print $dictionary -> GetValue("summary"); ?> </a>
                </li>
            </ul>
        </div>
        <div class="portlet-body">
            <div class="tab-content">
                <div class="tab-pane active" id="map_view">
                    <div class="row" style="margin: 5px; padding: 5px; text-align: center; background: #bcd5ed;">
                        <div class="col-lg-2"><img src="../img/transformer-icon.png" width="22px"> <?php print $dictionary->GetValue("transformer"); ?></div>
                        <div class="col-lg-2"><img src="../img/blue.png"> <?php print $dictionary->GetValue("no_any_action"); ?></div>
                        <div class="col-lg-2"><img src="../img/yellow.png"> <?php print $dictionary->GetValue("installed_and_pending"); ?></div>
                        <div class="col-lg-2"><img src="../img/green.png"> <?php print $dictionary->GetValue("completed"); ?></div>
                        <div class="col-lg-2"><img src="../img/red.png"> <?php print $dictionary->GetValue("has_problem"); ?></div>
                    </div>
                    <div id="map"></div>
                </div>

                <div class="tab-pane" id="list">
                    <?php
                    if($feeder_id){
                        $cols = array();
                        $cols[] = array("column"=>"installation_status");
                        $cols[] = array("column"=>"point_count");
                        $tableOptions = array();
                        $tableOptions["tableClass"]= "table-hover table-bordered table-condensed table-striped";
                        $tableOptions["key"]=array("id"=>"point_id");

                        $ServicePointSummary = $Installation->GetServicePointSummary($condition);
                        for($i=0; $i<count($ServicePointSummary); $i++){
                            switch ($ServicePointSummary[$i]["installation_status"]) {
                                case -2:
                                    $status = $dictionary->GetValue("problem");
                                    break;
                                case -1:
                                    $status = $dictionary->GetValue("in_progress");
                                    break;
                                case 1:
                                    $status = $dictionary->GetValue("not_completed");
                                    break;
                                case 2:
                                    $status = $dictionary->GetValue("completed");
                                    break;
                                case 3:
                                    $status = $dictionary->GetValue("deleted");
                                    break;
                            }
                            $ServicePointSummary[$i]["installation_status"] = $status;
                        }

                        $html->Table($ServicePointSummary, $cols, $tableOptions);
                    }
                    ?>
                </div>

                <div class="tab-pane" id="summary">
                    <?php
                    if($feeder_id){
                        // Installed Meter
                        $cols1 = array();
                        $cols1[] = array("column"=>"meter_type");
                        $cols1[] = array("column"=>"Installed_Meter");
                        $tableOptions1 = array();
                        $tableOptions1["tableClass"]= "table-hover table-bordered table-condensed table-striped";
                        $InstallationMeterSummary = $Installation->GetInstallationMeterSummary($condition);
                        $html->Table($InstallationMeterSummary, $cols1, $tableOptions1);


                        // Installed Enclosure
                        $cols2 = array();
                        $cols2[] = array("column"=>"enclosure_type");
                        $cols2[] = array("column"=>"enclosure_count");
                        $tableOptions2 = array();
                        $tableOptions2["tableClass"]= "table-hover table-bordered table-condensed table-striped";
                        $InstallationMeterSummary = $Installation->GetInstallationEnclosureSummary($condition);
                        $html->Table($InstallationMeterSummary, $cols2, $tableOptions2);
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>

</div>
<link rel="stylesheet" href="../osm/leaflet.css" />
<script src="../osm/leaflet.js" ></script>
<script src="../js/OpenMap.js?v=1"></script>
<script src="../js/map_helper.js"></script>
<link rel="stylesheet" href="../osm/Control.FullScreen.css" />
<script src="../osm/Control.FullScreen.js"></script>
<style>
    .installation-actions li.btn-block {
        margin-left: 0;
        margin-right: 0;
    }
    #map {
        width: 100%;
        height: 500px;
    }

    .leaflet-marker-icon.small {
        width: 6px !important;
        height: 6px !important;
        margin-left: -3px !important;
        margin-top: -3px !important;
    }

    .divicon {
        border: 0px dashed red;
        background-color: rgba(255, 0, 0, 0.1);
        padding-left: 4px !important;
        font-size: 8px;
        font-weight:bold;
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
<script>
var mapHeight;
var androidInterval;
var iconSize = 12;

// var CenrterLat = "36.191175";
// var CenterLong = "44.009134";
var CenrterLat = "9.0778";
var CenterLong = "8.6775";

<?php if( isset($CenterTransformer) && $CenterTransformer ) { ?>
    CenrterLat = "<?php print $CenterTransformer[0]["latitude"]; ?>";
    CenterLong = "<?php print $CenterTransformer[0]["longitude"]; ?>";
<?php } else if( isset($ServicePoints) && $ServicePoints ) { ?>
    CenrterLat = "<?php print $ServicePoints[0]["LAT_CENERT"]; ?>";
    CenterLong = "<?php print $ServicePoints[0]["LONG_CENERT"]; ?>";
<?php } ?>

var map = new Map("map", CenrterLat, CenterLong, false);
map.center(CenrterLat, CenterLong);
map.zoom(18);

var layerGroup = map.addGroup();

//add new icon to map
map.addCustomControl("../img/resize.png", "Change Icon Size",
    function(e){
        //onclick
        if (iconSize == 12) {
            $(".leaflet-marker-icon").addClass("small");
            iconSize = 6;
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
<?php
$area_name = "";
if( isset($ServicePoints) && $ServicePoints )
{
    $j = count( $ServicePoints );
    for ( $i=0; $i < $j; $i++ ){
        $point_id = $ServicePoints[$i]['point_id'];
        $transformer_id = $ServicePoints[$i]['transformer_id'];

        // if point type is transformer
        if( $ServicePoints[$i]['point_type_id'] == -1 ) {
            $PopUpString = "";
            $station_id = $ServicePoints[$i]['station_id'];
            $feeder_id = $ServicePoints[$i]['feeder_id'];
            $capacity_id = $ServicePoints[$i]['capacity_id'];
            $transformer_number = $ServicePoints[$i]['transformer_number'];


            $PopUpString = "#".$transformer_id."<br/><br/>".$station_id.'/'.$feeder_id.'/'.$capacity_id.'/'.$transformer_number.'</b><br />'.
            '<a href="installation_summary.php?transformer_id='.$transformer_id.'&station_id='.$station_id.'&feeder_id='.$feeder_id.'">'.$dictionary->GetValue("get_points").'</a>';

            if($ServicePoints[$i]["latitude"] != null && $ServicePoints[$i]["longitude"] != null){
            ?>
            icon = "<?php print $ServicePoints[$i]["color"]; ?>";
            color = {img: icon, "size":16 };
            map.addMarker(<?php print $ServicePoints[$i]["latitude"]; ?>, <?php print $ServicePoints[$i]["longitude"]; ?>, layerGroup, color, '<?php print $PopUpString; ?>', "<?php print $dictionary->GetValue("area_id")." : ".$area_name ?>", <?php print $point_id; ?>, false, null,
                            {
                                "tooltip": "Tr:"+"<?php (isset($transformer_number)) ? print $transformer_number : ''; ?>"
                            });
            <?php
            }
        } else { // if point type is not transformer
            if($ServicePoints[$i]["latitude"] != null && $ServicePoints[$i]["longitude"] != null){
                $transformer_number = $ServicePoints[$i]['transformer_number'];
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
                                "tooltip": "<?php (isset($transformer_number)) ? print $transformer_number : ''; ?>"
                            });
            <?php
            }
        }
    }
}

if (count($linesArr) > 0) {
    foreach ($linesArr as $id =>$points){
        echo 'var pointList = [];';
        for ($i=0; $i<count($points); $i++) {
            echo 'var point = new L.LatLng('.$points[$i][0].', '.$points[$i][1].');';
            echo 'pointList.push(point);';
        }
        //var pointB = new L.LatLng(linePoints[1]["lat"], linePoints[1]["lng"]);
        //echo 'var color = colors[Math.floor(Math.random()*colors.length)];';
        ?>
        line_id = "<?php echo $id;?>";
        linePopupStr = '<div>#'+line_id+
                        '<br><br><a href="javascript:;" class="delete_line" data-id="'+line_id+'">'+
                        '<i class="fa fa-times"></i> Delete Line</a>'+
                        '</div>';
        color = 'green';
        map.addLine(pointList, color, linePopupStr,
                {
                    opacity: 0.8,
                    popup: false,
                    popupopen: function(e) {
                        selectLine(e)
                    }
                });
        <?php
    }
}
?>

var tooltipMode = 0;

$(function() {

    //Get first load page height
    mapHeight = $( window ).height() - 150;

    //hide map by default
    $("#map").css("height", mapHeight);

    $('#area_id').on('change', function () {
        area_id = $(this).val();
        //FillFeeder(area_id);
        window.location.href = "installation_summary.php?&area_id=" + area_id;
    });

    $('#station_id').on('change', function () {
        area_id = $("#area_id").val();
        station_id = $(this).val();
        //FillFeeder(station_id);
        window.location.href = "installation_summary.php?&area_id=" + area_id + "&station_id=" + station_id;
    });

    $('#feeder_id').on('change', function () {
        feeder_id = $(this).val();
        area_id = $("#area_id").val();
        station_id = $("#station_id").val();
        window.location.href = "installation_summary.php?&area_id=" + area_id + "&station_id=" + station_id + "&feeder_id=" + feeder_id;
    });

    $('#transformer_id').on('change', function() {
        transformer_id = this.value;
        station_id = $("#station_id").val();
        feeder_id = $("#feeder_id").val();
        area_id = $("#area_id").val();
        window.location.href = "installation_summary.php?transformer_id=" + transformer_id + "&area_id=" + area_id + "&station_id=" + station_id + "&feeder_id=" + feeder_id;
    });
});
/*
function FillFeeder(station_id){
    if( station_id > 0){
        FillListOptions("ui/get.feeder.php?station_id="+station_id, "feeder_id", true);
    } else {
        FillListOptions("i/get.feeder.php", "feeder_id", true);
    }
}
*/
function selectLine(e)
{
    if (selectedLine != null) {
        selectedLine.setStyle({
            color: 'green',
            weight: 5,
            opacity: 0.8
        });
    }
    selectedLine = e.target;
    selectedLine.setStyle({
        weight:7,
        color:'#3297FD',
        fillOpacity:1,
    });
}

function OpenPointModal(point_id)
{
    OpenModal("ui/install.enclosure.php", {"point_id": point_id, "readonly": 1});
}

function EnclosurePointList(point_id){
    $("#installed_point_enclosure").hide();
    $("#installed_enclosures").load("ui/installed.enclosure.point.sublist.php?point_id="+point_id);
}


$(function() {
    //hide default tooltips
    showTooltips(true);
});


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
</script>
