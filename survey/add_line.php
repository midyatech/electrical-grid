<?php
include_once realpath(__DIR__ . '/..').'/include/checksession.php';
include_once realpath(__DIR__ . '/..').'/include/checkpermission.php';
include_once realpath(__DIR__ . '/..').'/include/settings.php';
include_once realpath(__DIR__ . '/..').'/include/header.php';
include_once realpath(__DIR__ . '/..').'/class/Dictionary.php';
include_once realpath(__DIR__ . '/..').'/class/Survey.class.php';
require_once realpath(__DIR__ . '/..') . '/class/Tree.php';

$dictionary = new Dictionary($LANGUAGE);
$dictionary->GetAllDictionary();
$Survey = new Survey();
$area_tree = new Tree("AREA_TREE");

$ServicePoints = $linesArr = $filter = array();

$area_ids = $area_id1 = $area_id2 = $area_id3 = $area_text1 = $area_text2 = $area_text3 = "";

if(isset($_REQUEST["area1"])&&$_REQUEST["area1"]!=NULL){
    $area_id1 = $_REQUEST["area1"];
    $area_text1 = $area_tree->GetPathString($area_id1);
    $area_ids .= $area_id1.", ";
} else {
    $area_id1 = $USERDIR;
    $area_text1 = $area_tree->GetPathString($area_id1);
    $area_ids .= $area_id1.", ";
}

if(isset($_REQUEST["area2"])&&$_REQUEST["area2"]!=NULL){
    $area_id2 = $_REQUEST["area2"];
    $area_text2 = $area_tree->GetPathString($area_id2);
    $area_ids .= $area_id2.", ";
}

if(isset($_REQUEST["area3"])&&$_REQUEST["area3"]!=NULL){
    $area_id3 = $_REQUEST["area3"];
    $area_text3 = $area_tree->GetPathString($area_id3);
    $area_ids .= $area_id3.", ";
}

$area_ids = rtrim($area_ids,', ');
$filter["area_id"] = $area_ids;

$ServicePoints = $Survey->GetServicePointWithLines($filter);

$lines = $Survey->GetLines($area_ids);
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

?>

<!-- <link rel="stylesheet" href="https://unpkg.com/leaflet@1.3.1/dist/leaflet.css"
    integrity="sha512-Rksm5RenBEKSKFjgI3a41vrjkw4EVPlJ3+OiI65vTjIdo9brlAacEuKOiQ5OFh7cOI1bkDwLqdLw3Zg0cRJAAQ=="
    crossorigin="" />
<script src="https://unpkg.com/leaflet@1.3.1/dist/leaflet.js" integrity="sha512-/Nsx9X4HebavoBvEBuyp3I7od5tA0UzAxs+j83KgC8PU0kgB4XiK4Lfe4y4cgBtaRJQEIFCW+oC506aPT2L1zw=="
        crossorigin=""></script> -->
<link rel="stylesheet" href="../osm/leaflet.css" />
<script src="../osm/leaflet.js" ></script>

<script src="../js/OpenMap.js?v=3"></script>


<div class="row">
    <form action="add_line.php" role="form" class="" id="service_point" method="post">
        <div class="col-lg-12 wizard">
            <div class="tab-content">

                <div class="portlet solid blue">
                    <div class="portlet-title">
                        <div class="caption">
                            <?php echo $dictionary->GetValue("select_area");?>
                        </div>
                        <div class="tools">
                            <a href="javascript:;" class="<?php echo ($area_ids==null) ? "collapse" : "expand"; ?>" data-original-title="" title=""> </a>
                        </div>
                    </div>
                    <div class="portlet-body" <?php echo ($area_ids==null) ? "" : 'style="display:none;"'; ?> >
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="form-group col-lg-1 col-xs-12">
                                    <label class="control-label"><?php print $dictionary->GetValue("area_id"); ?></label>
                                </div>
                                <div class="form-group col-lg-3 col-xs-12">
                                    <input type="hidden" name="area1" id="area1" value="<?php print $area_id1; ?>">
                                    <input type="text" name="area_text1" id="area_text1" value="<?php print $area_text1; ?>" class="form-control open_area_tree1" readonly="readonly">
                                </div>
                                <div class="form-group col-lg-3 col-xs-12">
                                    <input type="hidden" name="area2" id="area2" value="<?php print $area_id2; ?>">
                                    <input type="text" name="area_text2" id="area_text2" value="<?php print $area_text2; ?>" class="form-control open_area_tree2" readonly="readonly">
                                </div>
                                <div class="form-group col-lg-3 col-xs-12">
                                    <input type="hidden" name="area3" id="area3" value="<?php print $area_id3; ?>">
                                    <input type="text" name="area_text3" id="area_text3" value="<?php print $area_text3; ?>" class="form-control open_area_tree3" readonly="readonly">
                                </div>
                                <div class="form-group col-lg-2 col-xs-12">
                                    <button class="btn green col-xs-12" type="submit"><?php echo $dictionary->GetValue("filter");?></button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="row" style="margin: 5px; padding: 5px; text-align: center; background: #bcd5ed;">
                            <div class="col-lg-2"><img src="../img/transformer-icon.png" width="24px"> <?php print $dictionary->GetValue("transformer"); ?></div>
                            <div class="col-lg-2"><img src="../img/green.png"> <?php print $dictionary->GetValue("connected_to_the_transformer"); ?></div>
                            <div class="col-lg-2"><img src="../img/blue.png"> <?php print $dictionary->GetValue("Electric_Pole_not_connected"); ?></div>
                            <div class="col-lg-2"><img src="../img/red.png"> <?php print $dictionary->GetValue("Twisted_Cable_not_connected"); ?></div>
                            <div class="col-lg-2"><img src="../img/yellow.png"> <?php print $dictionary->GetValue("building_not_connected"); ?></div>
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
    }
</style>

<script src="../js/map_helper.js"></script>
<link rel="stylesheet" href="../osm/Control.FullScreen.css" />
<script src="../osm/Control.FullScreen.js"></script>
<script>

<?php $edit = $dictionary->GetValue("edit"); ?>;
<?php $edit_location = $dictionary->GetValue("edit_location"); ?>;

var Edit = '<?php print $dictionary->GetValue("edit"); ?>';
var Edit_Location = '<?php print $dictionary->GetValue("edit_location"); ?>';

var mapHeight;
var androidInterval;

// var CenrterLat = "36.191175";
// var CenterLong = "44.009134";
var CenrterLat = "9.0778";
var CenterLong = "8.6775";

<?php if( isset($ServicePoints) && $ServicePoints ) { ?>
    CenrterLat = "<?php print $ServicePoints[0]["LAT_CENERT"]; ?>";
    CenterLong = "<?php print $ServicePoints[0]["LONG_CENERT"]; ?>";
<?php } ?>
var map = new Map("map", CenrterLat, CenterLong, true);
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
        $station_id = $ServicePoints[$i]['station_id'];
        $feeder_id = $ServicePoints[$i]['feeder_id'];
        $capacity_id = $ServicePoints[$i]['capacity_id'];
        $transformer_number = $ServicePoints[$i]['transformer_number'];
        $area_name = $ServicePoints[$i]['NODE_NAME'];
        $timestamp = $ServicePoints[$i]['timestamp'];
        //$sequence = $ServicePoints[$i]['sequence'];
        $gateway = $ServicePoints[$i]['needs_gateway'];
        $transformer_id = $ServicePoints[$i]['transformer_id'];
        if ($ServicePoints[$i]['in_line'] == 1 && $point_type_id != 4) {
            $point_type_id = "-1";
        }
        $PopUpString =  '#'.$point_id.'<br />'.
                            $point_type.'<br />'.
                            $dictionary->GetValue("single_phase_consumers").': <b>'.$single_phase_consumers.'</b><br />'.
                            $dictionary->GetValue("three_phase_consumers").': <b>'.$three_phase_consumers.'</b><br />'.
                            $dictionary->GetValue("accuracy_id").': <b>'.$accuracy_id.'</b><br />'.
                            $dictionary->GetValue("date").': <b>'.$timestamp.'</b><br>'.
                            '<br /><a href="add_survey.php?point_id='.$point_id.'">'.$edit.'&nbsp;<i class="fa fa-pencil"></i></a><br /><a href="edit_location.php?point_id='.$point_id.'">'.$edit_location.'&nbsp;<i class="fa fa-map-marker"></i></a><br /><br />';

        if( $point_type_id < 4 && $ServicePoints[$i]["latitude"] != null && $ServicePoints[$i]["longitude"] != null){
            ?>
            point_id = "<?php print $point_id;?>";
            color = GetStatusColor(<?php print $point_type_id; ?>)
            popupString = '<?php print $PopUpString; ?>';
            map.addMarker(<?php print $ServicePoints[$i]["latitude"]; ?>,
                            <?php print $ServicePoints[$i]["longitude"]; ?>,
                            layerGroup,
                            color,
                            popupString,
                            null,
                            point_id,
                            null,
                            {
                                "event":"click",
                                "function": function(e) {
                                    onMarkerClick(e);
                                }
                            },
                            //extra:
                            {
                                "attr": {
                                    "position": "<?php print $ServicePoints[$i]["point_used"]; ?>"
                                }
                            }
            );
        <?php
        } else if( $point_type_id == 4 && $ServicePoints[$i]["latitude"] != null && $ServicePoints[$i]["longitude"] != null) {
            $PopUpString = "";
            $transformer_id = $ServicePoints[$i]['point_id'];
            $station_id = $ServicePoints[$i]['station_id'];
            $feeder_id = $ServicePoints[$i]['feeder_id'];
            $capacity_id = $ServicePoints[$i]['capacity_id'];
            $transformer_number = $ServicePoints[$i]['transformer_number'];
            $timestamp = $ServicePoints[$i]['timestamp'];
            $PopUpString = "#".$transformer_id."<br>".$station_id."/".$feeder_id."/".$capacity_id."/".$transformer_number."</b><br />".$timestamp.
                            "<br /><a href='add_survey.php?point_id=".$transformer_id."'>".$edit."&nbsp;<i class='fa fa-pencil'></i></a><br /><a href='edit_location.php?point_id=".$transformer_id."'>".$edit_location."&nbsp;<i class='fa fa-map-marker'></i></a><br /><br />";

                if ($ServicePoints[$i]['in_line'] == 1) {
                    $transformer_icon = "transformer-icon-green";
                } else {
                    $transformer_icon = "transformer-icon";
                }
            ?>
            popUpString = "<?php echo $PopUpString; ?>";
            transformer_id = "<?php echo $transformer_id;?>";

            color = {img: "<?php print $transformer_icon; ?>", "size":16 };//GetStatusColor(< ?php print $point_type_id; ?>)
            // ?>
            // popUpString = "< ?php echo $PopUpString; ?>";
            // transformer_id = "< ?php echo $transformer_id;?>";
            // color = {img: "transformer-icon", "size":24 }; //GetStatusColor(< ?php print $point_type_id; ?>)
            map.addMarker(<?php print $ServicePoints[$i]["latitude"]; ?>, <?php print $ServicePoints[$i]["longitude"]; ?>, layerGroup, color,
                popUpString,
                null,
                transformer_id,
                null,
                {
                    "event":"click",
                    "function": function(e) {
                        onMarkerClick(e);
                    }
                }, {
                    "attr": {
                        "position": "0"//always zero
                    }
                }
            );
            <?php
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
}
?>


var lineMode = 0;
var iconSize = 12;
var selectedLine = null;
var linePoints = [];
var linePointIds = [];
var changedMarkers = [];
var newLine = null;

$(function() {

    //Get first load page height
    mapHeight = $( window ).height() - 150;

    //hide map by default
    $("#map").css("height", mapHeight);

    cos = ",";
    if ("undefined" !== typeof Android) {
        getCoordinates();
        androidInterval = setInterval(function(){
            //cos = Android.askCoordinates();
            //cos = "36.8888,38.8888";
            getCoordinates();
        }, 30000);
    }

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

    //add new icon to map
    map.addCustomControl("../img/line.png", "Start New Line",
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
    map.addCustomControl("../img/check.png", "End Line",
        function(e){
            //onclick
            SaveLine(linePointIds);
        });

    $("body").on("click", ".delete_line", function(){
        id = $(this).data("id");
        DeleteLine(id);
    });

});

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

function onMarkerClick(e)
{
    point_id = e.target.properties.point_id
    if (lineMode == 1) {
        //map.map.closePopup();
        addLinePoints(e, point_id)
    }
}


function enableLine()
{
    lineMode = 1 - lineMode;
    if (lineMode == 0) {
        resetLine();
    }
}

function addLinePoints(e, point_id)
{
    if (lineMode == 1) {
        point = e.latlng;
        //check if point is already in middle position of another line
        //console.log(e.target.properties.position)
        //alert(e.target.properties.position)
        //if (e.target.properties.position == 0) {
            if (!linePoints.includes(point)) {
                //only allow unique points in a single line
                linePoints.push(point);
                linePointIds.push(point_id);

                changedMarkers.push(e)
                highlightMarker(e, "add");

                //if we have now two points, draw line
                if (linePoints.length > 1) {
                    drawLine();
                }
            }
        //} else {
        //    alert("Can't start new line here.")
        //}
    }
}

function drawLine()
{
    var pointList = [];
    for (i=0; i<linePoints.length; i++) {
        var point = new L.LatLng(linePoints[i]["lat"], linePoints[i]["lng"]);
        pointList.push(point);
    }
    if (newLine != null) {
        map.map.removeLayer(polyline);
    }
    newLine = map.addLine(pointList, "red", "", {opacity: 0.5, weight: 5})
}

function resetLine () {
    lineMode = 0;
    linePoints = [];
    linePointIds = [];
    for (i=0; i<changedMarkers.length; i++) {
        highlightMarker(changedMarkers[i], "remove");
    }
    if (undefined === newLine) {
        //...
    } else {
        map.map.removeLayer(newLine);
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

function SaveLine(line) {
    lineArray = JSON.stringify(line)
    //transformer_id = $("#transformer_id").val();
    confirm_message = "are_you_sure?";
    OpenConfirmModal([confirm_message], function() {
        var request = $.ajax({
            url: "code/line.save.code.php",
            type: "POST",
            data: {line: lineArray }
        });
        request.done(function(msg) {
            if (GetLocalStatus(true, msg)) {
                //success
                location.reload();
            } else {
                //error
            }
            HideLoader();
        });
        request.fail(function(jqXHR, textStatus) {
            //error
            ShowToastr("error", jqXHR.statusText);
            HideLoader();
        });
    });
}

function DeleteLine(id)
{
    OpenConfirmModal(["{Are you sure you want to delete this line and all children lines?}"], function() {
        var request = $.ajax({
            url: "code/line.delete.code.php?id=" + id,
            type: "GET",
            processData: false,
            contentType: false
        });
        request.done(function(msg) {
            if (GetLocalStatus(true, msg)) {
                //success
                location.reload();
            } else {
                //something wrong happened
                SetLocalStatus(msg);
                GetLocalStatus();
            }
            HideLoader();
        });
        request.fail(function(jqXHR, textStatus) {
            //error
            ShowToastr("error", jqXHR.statusText);
            HideLoader();
        });
    });
}

function ChangePointIcon(point_id, color)
{
    map.setMarkerIconByAttr("point_id",  point_id.toString(), color);
}

function getCoordinates(){
    cos = Android.askCoordinates();
    //cos = "36.8888,38.8888";
    //cos="36.21448693,44.12170499";
    if(cos.trim() != "," && cos.trim() != "null,null" && cos.trim() != "(,)"){
        setCoordinates(cos);
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
</script>
<script src="js/survey.js?v=1"></script>
<?php
require_once '../include/footer.php';
?>