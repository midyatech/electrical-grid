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

if (isset($_GET["feeder_id"])) {
    $feeder_id = $_GET["feeder_id"];
    $ServicePoints = $Survey->GetGridPoints($feeder_id);
    $lines = $Survey->GetGridLines($feeder_id);

    //get center of map
    $transformers = $Survey->GetTransformers(null, null, $feeder_id, 0);
    if ($transformers) {
        $sumLat = $sumLng = 0;
        for ($i = 0; $i<count($transformers); $i++) {
            $sumLat += $transformers[$i]["latitude"];
            $sumLng += $transformers[$i]["longitude"];
        }
        $lat = $sumLat/ count($transformers);
        $lng = $sumLng/ count($transformers);

    }
}

if (isset($_GET["transformer_id"])) {
    $transformer_id = $_GET["transformer_id"];
    $transformer = $Survey->GetTransformerDetails($transformer_id);
    $ServicePoints = $Survey->GetGridPoints(null, $transformer_id);
    $lines = $Survey->GetGridLines(null, $transformer_id);
    $lat = $transformer[0]["latitude"];
    $lng = $transformer[0]["longitude"];
}

$linesArr = array();
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
<?php
    $html->OpenDiv("row");
    {
        $html->OpenSpan(12);
        {
            echo '<div id="linemap" class="map"></div>';
        }
        $html->CloseSpan();
    }
    $html->CloseDiv();
?>
<?php $edit = $dictionary->GetValue("edit"); ?>;
<?php $edit_location = $dictionary->GetValue("edit_location"); ?>;
<script>



    var lineMode = 0;
    var iconSize = 12;
    var selectedLine = null;
    var linePoints = [];
    var linePointIds = [];
    var changedMarkers = [];
    var newLine = null;
    var linemap = null;
    var lat;
    var lng;

    $(function() {

        lat = "<?php echo $lat;?>";
        lng = "<?php echo $lng;?>";


        $("#linemap").html('<div id="linemap" class="map"></div>');

        if (linemap != undefined) { linemap.remove(); }

        linemap = new Map("linemap", lat, lng, false, false);
        linemap.zoom(16);
        linemap.addMarker( lat, lng, 0, {img: "transformer-icon", "size": 18 }, null, null, null, false);
        linemap.map.invalidateSize();

        var layerGroup = linemap.addGroup();

        //add new icon to map
        linemap.addCustomControl("../img/resize.png", "Change Icon Size",
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
        linemap.addCustomControl("../img/line.png", "Start New Line",
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
        linemap.addCustomControl("../img/check.png", "End Line",
            function(e){
                //onclick
                SaveLine(linePointIds);
            });

                    //add new icon to map
        linemap.addCustomControl("../img/marker_add.png", "Add Point",
            function(e){
                //onclick
                OpenAddPoint(lat, lng);
            });



        $("body").on("click", ".delete_line", function(){
            id = $(this).data("id");
            DeleteLine(id);
        });



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
                                    $dictionary->GetValue("date").': <b>'.$timestamp.'</b><br>';
                                    //'<br /><a target="blank" href="add_survey.php?point_id='.$point_id.'">'.$edit.'&nbsp;<i class="fa fa-pencil"></i></a><br /><a target="blank" href="edit_location.php?point_id='.$point_id.'">'.$edit_location.'&nbsp;<i class="fa fa-map-marker"></i></a><br /><br />';

                if( $point_type_id < 4 && $ServicePoints[$i]["latitude"] != null && $ServicePoints[$i]["longitude"] != null){
                    ?>
                    point_id = "<?php print $point_id;?>";
                    color = GetStatusColor(<?php print $point_type_id; ?>)
                    popupString = '<?php print $PopUpString; ?>';
                    linemap.addMarker(<?php print $ServicePoints[$i]["latitude"]; ?>,
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
                    $PopUpString = "#".$transformer_id."<br>".$station_id."/".$feeder_id."/".$capacity_id."/".$transformer_number."</br><br />".$timestamp;
                                    //"<br /><a target='blank' href='add_survey.php?point_id=".$transformer_id."'>".$edit."&nbsp;<i class='fa fa-pencil'></i></a><br /><a target='blank' href='edit_location.php?point_id=".$transformer_id."'>".$edit_location."&nbsp;<i class='fa fa-map-marker'></i></a><br /><br />";

                        if ($ServicePoints[$i]['in_line'] == 1) {
                            $transformer_icon = "transformer-icon-green";
                        } else {
                            $transformer_icon = "transformer-icon";
                        }
                    ?>
                    popUpString = "<?php echo $PopUpString; ?>";
                    transformer_id = "<?php echo $transformer_id;?>";

                    color = {img: "<?php print $transformer_icon; ?>", "size":16 };//GetStatusColor(< ?php print $point_type_id; ?>)
                    linemap.addMarker(<?php print $ServicePoints[$i]["latitude"]; ?>, <?php print $ServicePoints[$i]["longitude"]; ?>, layerGroup, color,
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
                    ?>
                    line_id = "<?php echo $id;?>";
                    linePopupStr = '<div>#'+line_id+
                                    '<br><br><a href="javascript:;" class="delete_line" data-id="'+line_id+'">'+
                                    '<i class="fa fa-times"></i> Delete Line</a>'+
                                    '</div>';
                    color = 'green';
                    linemap.addLine(pointList, color, linePopupStr,
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
        //linemap.linemap.closePopup();
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
        linemap.linemap.removeLayer(polyline);
    }
    newLine = linemap.addLine(pointList, "red", "", {opacity: 0.5, weight: 5})
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
        linemap.linemap.removeLayer(newLine);
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
    linemap.setMarkerIconByAttr("point_id",  point_id.toString(), color);
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

        linemap.center(lat, long);
    }, 1500);
}
</script>