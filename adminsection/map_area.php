<?php
require_once '../include/header.php';
require_once '../include/settings.php';
require_once realpath(__DIR__ . '/..') . '/class/Tree.php';
require_once realpath(__DIR__ . '/..') . '/class/User.php';
require_once realpath(__DIR__ . '/../'). '/class/Crypt.php';

$Tree = new Tree("AREA_TREE");
$User = new User("AREA_TREE");
$areas = $Tree->GetFullTree();

$formAction = "code/area.insert.code.php";

$options = array("class"=>"form-control");
$listoptions = array("class"=>"form-control","optional"=>"true");

$password = "";
$_alphaSmall = 'abcefghijkmnpqrstuvwxyz';       // small letters
$_alphaCaps  = "ABCDEFGHJKLMNPQRSTUVWXYZ";      // CAPITAL LETTERS
$_numerics   = '23456789';                      // numerics
$_specialChars = '!@#$%&';                      // Special Characters

$password .= substr(str_shuffle($_alphaSmall), 0, 1);
$password .= substr(str_shuffle($_alphaCaps), 0, 1);
$password .= substr(str_shuffle($_numerics), 0, 1);
$password .= substr(str_shuffle($_alphaSmall), 0, 1);
$password .= substr(str_shuffle($_alphaCaps), 0, 1);
$password .= substr(str_shuffle($_specialChars), 0, 1);
$password .= substr(str_shuffle($_alphaSmall), 0, 1);
$password .= substr(str_shuffle($_alphaCaps), 0, 1);

$password = str_shuffle($password);

$mode = "add";
$userId = "";
$node_id = $nodeName = NULL;
$nodeColor = "red";
$NodeCoordinates = "[]";

$options = array("class"=>"form-control");
$area_id = $area_text = NULL;

if( isset($_SESSION["area_id"]) && $_SESSION["area_id"]!=NULL ){
    $area_id = $_SESSION["area_id"];
}

if(isset($_GET['id']) && $_GET['id'] != ""){
    $node_id = $_GET['id'];
    $filter["LOGIN"] = $node_id;
    $AreaUser = $User->GetUsers($filter);
    if($AreaUser){
        $userId = $AreaUser[0]["USER_ID"];
        $crypt = new Crypt();
        $password = $crypt->MediaDecrypt($AreaUser[0]["PASSWORD"]);
    }
    $mode = "edit";
    $NodeInfo = $Tree -> GetNodeInfo ( $node_id );
    $area_id = $NodeInfo[0]["PARENT_ID"];
    $nodeName = $NodeInfo[0]["NODE_NAME"];
    $NodeCoordinates = $NodeInfo[0]["COORDINATES"];
    $nodeColor = $NodeInfo[0]["COLOR"];
}

$latitude = $longitude = NULL;
$ParentInfo = $Tree -> GetNodeInfo ( $area_id );
$area_path = $Tree->GetNodePath($area_id);
$area_text = $Tree->GetPathString($area_id);

if ( $ParentInfo ){
    $latitude = $ParentInfo[0]["LAT_CENERT"];
    $longitude = $ParentInfo[0]["LONG_CENERT"];
}

for ($i=0; $i<count($areas); $i++) {
    $areas[$i]["COORDINATES"] = str_replace(array("\r", "\n"), '', $areas[$i]["COORDINATES"]);
}

$html->OpenDiv("row");
{
    $html->OpenSpan(12);
    {
        $html->OpenForm ( $formAction, "form3");
        {
            $html->OpenDiv("row");
            {

                echo '<div id="map"></div>';

                $html->OpenSpan(12);
                {
                    $html->DrawFormField ( "text", "coordinates", $NodeCoordinates, NULL, $options );
                }
                $html->CloseSpan();

                $html->OpenSpan(3);
                {
                    $dirTree = array (
                        array ( "type"=>"hidden", "name"=>"area", "value"=>$area_id, "list"=>NULL, "options"=>NULL ),
                        array ( "type"=>"text", "name"=>"area_text", "value"=>$area_text, "list"=>NULL, "options"=>array("class"=>"form-control open_area_tree", "tree"=>"", "readonly"=>"readonly") )
                    );
                    $html->DrawGenericFormField ( "area_id", $dirTree, null, $options);
                }
                $html->CloseSpan();

                $html->OpenSpan(3);
                {
                    $html->DrawFormField ( "text", "nodeName", $nodeName, NULL, array("class"=>"form-control") );
                }
                $html->CloseSpan();

                $html->OpenSpan(3);
                {
                    $html->DrawFormField ( "text", "password", $password, NULL, array("class"=>"form-control", "readonly"=>true) );
                }
                $html->CloseSpan();

                $html->OpenSpan(3);
                {
                    $html->HiddenField("userId", $userId);
                    $html->HiddenField("nodeId", $node_id);
                    $html->HiddenField("color", $nodeColor);
                    ?>
                    <br/><br/>
                    <div class="form-group">
                        <div class="input-group">
                            <span class="input-group-btn">
                                <button class="btn default clear_add_area" type="button"><?php echo $dictionary->GetValue("clear");?></button>
                            </span>
                            <span class="input-group-btn">
                                <button class="btn green add_area" type="button"><?php echo $dictionary->GetValue($mode);?></button>
                            </span>
                        </div>
                    </div>
                    <?php
                }
                $html->CloseSpan();

            }
            $html->CloseDiv();
        }
        $html->CloseForm();
    }
    $html->CloseSpan();
}
$html->CloseDiv();

?>
<link rel="stylesheet" href="../osm/leaflet.css" />
<script src="../osm/leaflet.js" ></script>

<link rel="stylesheet" href="../osm/leaflet.draw.css" />
<script src="../osm/leaflet.draw.js"></script>
<script src="../osm/Edit.Poly.js"></script>
<script src="../js/OpenMap.js"></script>
<script src="../js/map_helper.js"></script>

<script src="js/area.js?v=1"></script>
<style>
#map{
    width: 100%;
    height: 600px;
}
.leaflet-draw-tooltip {
    display: none;
}
.mapPopup{
    width: 200px;
    height: 100px;
}
</style>

<script>
    var mode = "<?php echo $mode;?>";
    var map;
    function UpdateCoordinates(str) {
        fixedStr = SwitchCoordinates(str);
        document.getElementById("coordinates").value = fixedStr;
    }

    $(function () {

        <?php
        if( $latitude == "" || $longitude == "" ){

            // if (strpos($area_path, '.3.') !== false) { // Sul-Nawand : 35.5653, 45.4223
            //     $latitude = "35.5653";
            //     $longitude = "45.4223";

            // } else if (strpos($area_path, '.4.') !== false) { // Duhok-Nawand: 36.8654, 42.9926
            //     $latitude = "36.8654";
            //     $longitude = "42.9926";

            // } else { // Erbil-Qalat : 36.191175, 44.009134
            //     $latitude = "36.191175";
            //     $longitude = "44.009134";
            // }

            $latitude = "9.0778";
            $longitude = "8.6775";
        }
        ?>

        var latitude = "<?php print $latitude; ?>";
        var longitude = "<?php print $longitude; ?>";

        var map = new Map("map", latitude, longitude, true, true);
        map.zoom(15);


        // Truncate value based on number of decimals
        var _round = function (num, len) {
            return Math.round(num * (Math.pow(10, len))) / (Math.pow(10, len));
        };
        // Helper method to format LatLng object (x.xxxxxx, y.yyyyyy)
        var strLatLng = function (latlng) {
            return "(" + _round(latlng.lat, 6) + ", " + _round(latlng.lng, 6) + ")";
        };

        // Generate popup content based on layer type
        // - Returns HTML string, or null if unknown object
        var getPopupContent = function (layer) {
            if (layer instanceof L.Polygon) {
                var geo = layer.toGeoJSON();
                return JSON.stringify(geo.geometry.coordinates[0]);
            }
            return null;
        };

        if(mode == "add"){
            // Object created - bind popup to layer, add to feature group
            map.map.on(L.Draw.Event.CREATED, function (event) {
                var layer = event.layer;
                var content = getPopupContent(layer);
                if (content !== null) {
                    layer.bindPopup(content);
                }
                map.drawnItems.addLayer(layer);
                UpdateCoordinates(JSON.stringify(layer.toGeoJSON().geometry.coordinates[0]));
            });

            // Object(s) edited - update popups
            map.map.on(L.Draw.Event.EDITED, function (event) {
                var layers = event.layers,
                    content = null;
                layers.eachLayer(function (layer) {
                    content = getPopupContent(layer);
                    if (content !== null) {
                        layer.setPopupContent(content);
                        UpdateCoordinates(JSON.stringify(layer.toGeoJSON().geometry.coordinates[0]));
                    }
                });
            });
        }


        <?php
        for ($i=0; $i<count($areas); $i++) {
        ?>
            var PopUpString = GeneratePopUpString("<?php echo $areas[$i]["NODE_ID"];?>", "<?php echo $areas[$i]["NODE_NAME"];?>");
        <?php
                if(isset($_GET["id"]) && ($areas[$i]["NODE_ID"] == $_GET["id"])){
                //edit polygon
                ?>
                editPolygon = map.addPolygon("<?php echo $areas[$i]["COORDINATES"]; ?>", null,"blue", "<?php echo $areas[$i]["NODE_NAME"];?>", true);
                console.log(editPolygon.editing._enabled)
                editPolygon.editing.enable();
                editPolygon.on('edit', function() {
                    //console.log(polygon.editing.latlngs[0][0])
                    polygonPath = []
                    latlngs = editPolygon.editing.latlngs[0][0];
                    for(i=0; i<latlngs.length; i++){
                        polygonPath[i] = [latlngs[i]["lat"], latlngs[i]["lng"]]
                    }
                    console.log(polygonPath)
                    document.getElementById("coordinates").value = JSON.stringify(polygonPath);
                });
                <?php
            }else{
                ?>
                map.addPolygon("<?php echo $areas[$i]["COORDINATES"]; ?>", null,"<?php echo $areas[$i]["COLOR"];?>", "<?php echo $areas[$i]["NODE_NAME"];?>", false, PopUpString);
                <?php
            }
        }
        ?>
    });
</script>

<?php
require_once '../include/footer.php';
?>