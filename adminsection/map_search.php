<?php
include_once realpath(__DIR__ . '/..').'/include/header.php';
include_once realpath(__DIR__ . '/..').'/include/settings.php';
include_once realpath(__DIR__ . '/..').'/include/checksession.php';
include_once realpath(__DIR__ . '/..').'/include/checkpermission.php';
require_once realpath(__DIR__ . '/..').'/class/MysqliDB.php';
require_once realpath(__DIR__ . '/..') . '/class/coordinates.php';

$Db = new MysqliDB();

/*
if(isset($_REQUEST["query"]) && $_REQUEST["query"] != ""){
    $query = $_REQUEST["query"];
    $points = $Db->SelectData($query);
}

for($i=0; $i<count($points); $i++){
	$utm = ll2utm($points[$i]["latitude"], $points[$i]["longitude"]);
    $Update .= "UPDATE service_point SET x = ".$utm["x"].", y = ".$utm["y"]." WHERE point_id = ".$points[$i]["point_id"].";<br/>";
}
print $Update;
*/

/*
-- All Transformers Served
SELECT sp.*, point_type
FROM service_point sp
INNER JOIN point_type ON sp.point_type_id = point_type.point_type_id
WHERE sp.point_type_id = 4 AND sp.latitude > 0 AND sp.longitude > 0

-- Assembled
SELECT sp.*, point_type
FROM service_point sp
INNER JOIN point_type ON sp.point_type_id = point_type.point_type_id
INNER JOIN assembly_order_transformers ON sp.point_id = assembly_order_transformers.transformer_id
WHERE sp.point_type_id = 4 AND sp.latitude > 0 AND sp.longitude > 0

-- Installed
SELECT sp.*, point_type
FROM service_point sp
INNER JOIN point_type ON sp.point_type_id = point_type.point_type_id
INNER JOIN (
	SELECT DISTINCT transformer_id FROM installed_point_enclosure
	INNER JOIN (
		SELECT point_id, transformer_id
		FROM line_points p
		INNER JOIN point_line l ON l.line_id = p.line_id
		WHERE is_service_point = 1
	) A ON A.point_id = installed_point_enclosure.point_id
) B ON B.transformer_id = sp.point_id

-- Assembled Not Installed
SELECT sp.*, point_type
FROM service_point sp
INNER JOIN point_type ON sp.point_type_id = point_type.point_type_id
INNER JOIN assembly_order_transformers ON sp.point_id = assembly_order_transformers.transformer_id
WHERE sp.point_type_id = 4 AND sp.latitude > 0 AND sp.longitude > 0
AND sp.point_id NOT IN (
	SELECT DISTINCT transformer_id FROM installed_point_enclosure
	INNER JOIN (
		SELECT point_id, transformer_id
		FROM line_points p
		INNER JOIN point_line l ON l.line_id = p.line_id
		WHERE is_service_point = 1
	) A ON A.point_id = installed_point_enclosure.point_id
)
*/
$query = "";
/*
$query = "call GetTransformers(4)
/*
1 - imported not implemented
2- surveyed
3- work order
4- assembled completed
5- installation completed
* /";
*/

/*
$query = "SELECT sp.*, point_type
            FROM
            industrial_area_transformer sp
            LEFT JOIN point_type ON sp.point_type_id = point_type.point_type_id
            WHERE feeder_line = 'SHADI_F8'";
*/


/*
SELECT sp.*, point_type, station, feeder
FROM service_point sp
INNER JOIN point_type ON sp.point_type_id = point_type.point_type_id
INNER JOIN station ON sp.station_id = station.station_id
INNER JOIN feeder ON sp.feeder_id = feeder.feeder_id
INNER JOIN assembly_order_transformers ON sp.point_id = assembly_order_transformers.transformer_id
WHERE sp.point_type_id = 4 AND sp.latitude > 0 AND sp.longitude > 0
*/
$map_points = array();
$latitude = $longitude = $count = NULL;


if(isset($_REQUEST["query"]) && $_REQUEST["query"] != ""){

    $error = false;
    $query = $_REQUEST["query"];
    $wordlist = array("insert", "update", "delete", "drop", "truncate");
    foreach ($wordlist as $word) {
        if (strpos(strtolower($query), $word) !== FALSE) {
            print "Injection Word : ".$word."<br/>";
            $error = true;
        }
    }

    if(! $error){
        $map_points = $Db->SelectData($query);
        $count = count($map_points);
        if (isset($map_points[0]["latitude"])) {
            $latitude = $map_points[0]["latitude"];
            $longitude = $map_points[0]["longitude"];
        }
    }
}


$html->OpenDiv("row");
{
    $html->OpenSpan(12);
    {
        $html->OpenWidget ($count." Points", null, array('collapse' => true, 'fullscreen'=>true));
        {
            $html->OpenForm ( "map_search.php", "form1");
            {
                $html->OpenSpan(12);
                {
                    $html->DrawFormField( "textarea", "query", $query, "", $options = array("class"=>"form-control", "rows"=>"10"));
                }
                $html->CloseSpan();

                $html->OpenSpan(2);
                {
                    ?>
                    <div class="form-group">
                        <div class="input-group">
                            <span class="input-group-btn">
                                <input class="btn green submit" type="submit" value="<?php echo $dictionary->GetValue("search");?>">
                            </span>
                        </div>
                    </div>
                    <?php
                }
                $html->CloseSpan();
            }
            $html->CloseForm();
            ?>
            <div id="map"></div>
            <?php
        }
        $html->CloseWidget();
    }
    $html->CloseSpan();
}
$html->CloseDiv();


?>
<link rel="stylesheet" href="../osm/leaflet.css" />
<script src="../osm/leaflet.js" ></script>
<script src="../js/OpenMap.js?v=1"></script>
<script src="../js/map_helper.js"></script>
<link rel="stylesheet" href="../osm/Control.FullScreen.css" />
<script src="../osm/Control.FullScreen.js"></script>

<style>
    #map{
        width: 100%;
        height: 600px;
    }
    .divicon {
        border: 0px dashed red;
        background-color: rgba(255, 0, 0, 0.1);
        padding-left: 4px !important;
        font-size: 8px;
        font-weight:bold;
    }

    .leaflet-marker-icon.small {
        width: 6px !important;
        height: 6px !important;
        margin-left: -3px !important;
        margin-top: -3px !important;
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

    colors = ["#ff0000", "#ff8000", "#ffff00", "#80ff00", "#00ffff", "#800000", "#0040ff", "#bf00ff", "#8000ff"];
    colors = ["#ff0000"];
    var iconSize = 12;

    var tooltipMode = 0;
    var latitude = "<?php print $latitude; ?>";
    var longitude = "<?php print $longitude; ?>";
    var map = new Map("map", latitude, longitude, false, false);
    map.zoom(16);

    var layerGroup = map.addGroup();

    //add resize icon to map
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

        $(".submit").on("click", function(){
            $("#form1").submit();
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
    <?php // Draw the Points

    /*
    if($map_points){
        for($i=0; $i<count($map_points); $i++){
            ?>
            var lat = "<?php print $map_points[$i]["latitude"]; ?>";
            var long = "<?php print $map_points[$i]["longitude"]; ?>";

            var id = <?php print $map_points[$i]["id"]; ?>;
                PopUpString = "#"+id;
                icon = {img: "transformer-icon", "size": 18 };

            map.addMarker( lat, long, layerGroup, icon, PopUpString, null, id, false);
            <?php
        }
    }
    */
    if($map_points){
        for($i=0; $i<count($map_points); $i++){
            if (!isset($map_points[$i]["latitude"]) ||
                ($map_points[$i]["latitude"]==null && $map_points[$i]["longitude"]==null && $map_points[$i]["x"]!=null)) {
                $latlng = utm2ll($map_points[$i]["x"], $map_points[$i]["y"], 38, true);
                /*
                $json = json_decode($latlng, true);
                $map_points[$i]["latitude"] = $json["attr"]["lat"];
                $map_points[$i]["longitude"] = $json["attr"]["lon"];
                */
                $map_points[$i]["latitude"] = $latlng["lat"];
                $map_points[$i]["longitude"] = $latlng["long"];

            } else if(!isset($map_points[$i]["x"]) && ($map_points[$i]["latitude"]!=null && $map_points[$i]["longitude"]!=null)) {

                $utm = ll2utm($map_points[$i]["latitude"], $map_points[$i]["longitude"]);
                $map_points[$i]["x"] = $utm["x"];
                $map_points[$i]["y"] = $utm["y"];
            }
            ?>
            var lat = "<?php print $map_points[$i]["latitude"]; ?>";
            var long = "<?php print $map_points[$i]["longitude"]; ?>";
            var point_type_id = "<?php print isset($map_points[$i]["point_type_id"]) ? $map_points[$i]["point_type_id"] : ""; ?>";

            var point_id = <?php print $map_points[$i]["point_id"]; ?>;
            <?php $Tr = (int) filter_var($map_points[$i]["transformer_number"], FILTER_SANITIZE_NUMBER_INT); ?>
            var transformer_number = <?php ($Tr > 0) ? print $Tr : print "0"; ?>;
            PopUpString = "";
            if(point_type_id == 4 ){
                PopUpString = "#"+point_id+"<br/>"+transformer_number;
                icon = {img: "transformer-icon", "size": 18 };
            } else {
                PopUpString = "#"+point_id;
                icon = {"size": 12, "img": "../img/blue"};
            }
            map.addMarker( lat, long, layerGroup, icon, PopUpString, null, point_id, false, null,
                            {
                                "tooltip": "<?php (isset($map_points[$i]['transformer_number'])) ? print $map_points[$i]['transformer_number'] : ''; ?>"
                            }
                );
            <?php
        }
    }
    ?>

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
<table class="table rtl table-hover table-bordered table-condensed table-striped  dataTable no-footer">
    <tr>
        <td>point_id</td>
        <td>feeder_id</td>
        <td>feeder</td>
        <td>station_id</td>
        <td>station</td>
        <td>transformer_number</td>
        <td>Lat</td>
        <td>Long</td>
        <td>X</td>
        <td>Y</td>
    </tr>
    <?php for($i=0; $i<count($map_points); $i++) { ?>
    <tr>
        <td><?php (isset($map_points[$i]["point_id"])) ? print $map_points[$i]["point_id"] : ""; ?></td>
        <td><?php (isset($map_points[$i]["feeder_id"])) ? print $map_points[$i]["feeder_id"] : ""; ?></td>
        <td><?php (isset($map_points[$i]["feeder"])) ? print $map_points[$i]["feeder"] : ""; ?></td>
        <td><?php (isset($map_points[$i]["station_id"])) ? print $map_points[$i]["station_id"] : ""; ?></td>
        <td><?php (isset($map_points[$i]["station"])) ? print $map_points[$i]["station"] : ""; ?></td>
        <td><?php (isset($map_points[$i]["transformer_number"])) ? print $map_points[$i]["transformer_number"] : ""; ?></td>
        <td><?php (isset($map_points[$i]["latitude"])) ? print $map_points[$i]["latitude"] : ""; ?></td>
        <td><?php (isset($map_points[$i]["longitude"])) ? print $map_points[$i]["longitude"] : ""; ?></td>
        <td><?php (isset($map_points[$i]["x"])) ? print $map_points[$i]["x"] : ""; ?></td>
        <td><?php (isset($map_points[$i]["y"])) ? print $map_points[$i]["y"] : ""; ?></td>
    </tr>
    <?php } ?>
</table>
<?php
include '../include/footer.php';
?>