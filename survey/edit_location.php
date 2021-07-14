<?php
include_once realpath(__DIR__ . '/..').'/include/checksession.php';
include_once realpath(__DIR__ . '/..').'/include/checkpermission.php';
include_once realpath(__DIR__ . '/..').'/include/settings.php';
include_once realpath(__DIR__ . '/..').'/include/header.php';
include_once realpath(__DIR__ . '/..').'/class/Dictionary.php';
include_once realpath(__DIR__ . '/..').'/class/Tree.php';
include_once realpath(__DIR__ . '/..').'/class/Survey.class.php';

$Tree = new Tree("AREA_TREE");
$Survey = new Survey();
$dictionary = new Dictionary($LANGUAGE);
$dictionary->GetAllDictionary();
$point_id = $edit_point_id = NULL;
$UserCoordinates = "[]";
if ($USERACCESS != null) {
    $UserAccessInfo = $Tree -> GetNodeInfo($USERACCESS);
    $UserCoordinates = $UserAccessInfo[0]["COORDINATES"];
}
if( isset($_REQUEST["point_id"]) && $_REQUEST["point_id"] != NULL ){
    $operation = "edit";
    $operationCalssName = "edit_service_point_location";
    $point_id = $edit_point_id = $_REQUEST["point_id"];
}
$condition = array();
$condition["service_point.point_id"] = $point_id;
$ServicePoints = $Survey->GetServicePoint($condition);


$condition_1 = array();
$condition_1["service_point.area_id"] = $ServicePoints[0]["area_id"];

$AllServicePoints = $Survey->GetServicePoint($condition_1);
for ($i=0; $i<count($AllServicePoints); $i++) {
    $AllServicePoints[$i]["sequence"] = $i+1;
}
?>

<!-- <link rel="stylesheet" href="https://unpkg.com/leaflet@1.3.1/dist/leaflet.css"
    integrity="sha512-Rksm5RenBEKSKFjgI3a41vrjkw4EVPlJ3+OiI65vTjIdo9brlAacEuKOiQ5OFh7cOI1bkDwLqdLw3Zg0cRJAAQ=="
    crossorigin="" />
<script src="https://unpkg.com/leaflet@1.3.1/dist/leaflet.js" integrity="sha512-/Nsx9X4HebavoBvEBuyp3I7od5tA0UzAxs+j83KgC8PU0kgB4XiK4Lfe4y4cgBtaRJQEIFCW+oC506aPT2L1zw=="
        crossorigin=""></script> -->
<link rel="stylesheet" href="../osm/leaflet.css" />
<script src="../osm/leaflet.js" ></script>

<script src="../js/OpenMap.js?v=1"></script>

<div class="row form">
    <form action="code/service.point.edit.location.code.php" role="form" class="form-horizontal form-row-seperated" id="service_point" method="post">
        <input type="hidden" id="longitude" name="longitude" value=<?php echo $ServicePoints[0]["longitude"];?>>
        <input type="hidden" id="latitude" name="latitude" value=<?php echo $ServicePoints[0]["latitude"];?>>
        <input type="hidden" id="point_id" name="point_id" value=<?php echo $point_id;?>>
        <div id="map"></div>
        <button type="button" class="btn btn-danger <?php print $operationCalssName; ?>"> <i class="fa fa-check"></i> <?php echo $dictionary->GetValue($operation);?></button>
    </form>
</div>

<style>
    #map{
        width: 100%;
        height: 500px;
        /*background-color: yellow;*/
    }
</style>
<script src="../js/map_helper.js"></script>
<script>
var mapHeight;
var map = new Map("map", <?php echo $ServicePoints[0]["latitude"];?>, <?php echo $ServicePoints[0]["longitude"];?>, false);
map.zoom(16);
map.addPolygon("<?php print $UserCoordinates; ?>", null,"red", "p1");
var layerGroup = map.addGroup();

<?php
// To Other Points
if( $AllServicePoints )
{
    for ( $i=0; $i < count( $AllServicePoints ); $i++ ){
        $PopUpString = "";
        $point_id = $AllServicePoints[$i]['point_id'];
        $point_type_id = $AllServicePoints[$i]['point_type_id'];
        $single_phase_consumers = $AllServicePoints[$i]['single_phase_consumers'];
        $three_phase_consumers = $AllServicePoints[$i]['three_phase_consumers'];
        $accuracy_id = $AllServicePoints[$i]['accuracy_id'];
        $point_type = $AllServicePoints[$i]['point_type'];
        $station_id = $AllServicePoints[$i]['station_id'];
        $feeder_id = $AllServicePoints[$i]['feeder_id'];
        $capacity_id = $AllServicePoints[$i]['capacity_id'];
        $transformer_number = $AllServicePoints[$i]['transformer_number'];
        $area_name = $AllServicePoints[$i]['NODE_NAME'];
        $color = $AllServicePoints[$i]['COLOR'];
        $sequence = $AllServicePoints[$i]['sequence'];

        if( $point_type_id == 4 ){
            $PopUpString = $point_type.'<br /><b>'.
                            $station_id.'/'.$feeder_id.'/'.$capacity_id.'/'.$transformer_number.'</b><br />'.
                            $dictionary->GetValue("sequence").': <b>'.
                            $sequence.'</b><br /><br />'.
                            '<a href="add_survey.php?point_id='.$point_id.'">'.$dictionary->GetValue("edit").'&nbsp;<i class="fa fa-pencil"></i></a><br />'.
                            '<a href="edit_location.php?point_id='.$point_id.'">'.$dictionary->GetValue("edit_location").'&nbsp;<i class="fa fa-map-marker"></i></a>';

        } else {
            $PopUpString = $point_type.'<br />'.
                            $dictionary->GetValue("single_phase_consumers").': <b>'.
                            $single_phase_consumers.'</b><br />'.
                            $dictionary->GetValue("three_phase_consumers").': <b>'.
                            $three_phase_consumers.'</b><br />'.
                            $dictionary->GetValue("accuracy_id").': <b>'.
                            $accuracy_id.'</b><br />'.
                            $dictionary->GetValue("sequence").': <b>'.
                            $sequence.'</b><br /><br />'.
                            '<a href="add_survey.php?point_id='.$point_id.'">'.$dictionary->GetValue("edit").'&nbsp;<i class="fa fa-pencil"></i></a><br />'.
                            '<a href="edit_location.php?point_id='.$point_id.'">'.$dictionary->GetValue("edit_location").'&nbsp;<i class="fa fa-map-marker"></i></a>';
        }

        if($AllServicePoints[$i]["latitude"] != null && $AllServicePoints[$i]["longitude"] != null && $AllServicePoints[$i]["point_id"] != $edit_point_id){
        ?>
            color = GetStatusColor(<?php print $point_type_id; ?>)
            map.addMarker(<?php print $AllServicePoints[$i]["latitude"]; ?>, <?php print $AllServicePoints[$i]["longitude"]; ?>, layerGroup, color, '<?php print $PopUpString; ?>', "<?php print $dictionary->GetValue("area_id")." : ".$area_name ?>");
        <?php
        }
    }
}

// To Edit Point
if( $ServicePoints )
{
    if($ServicePoints[0]["latitude"] != null && $ServicePoints[0]["longitude"] != null){
    ?>
        map.addMarkerD(<?php print $ServicePoints[0]["latitude"]; ?>, <?php print $ServicePoints[0]["longitude"]; ?>);
    <?php
    }
}
?>
draggable_marker.on('dragend', function(event){
        var marker2 = event.target;
        var position = marker2.getLatLng();
        marker2.setLatLng(new L.LatLng(position.lat, position.lng),{draggable:'true'});
        $("#latitude").val(position.lat);
        $("#longitude").val(position.lng);
        $("#confirm_latitude").text($("#latitude").val());
        $("#confirm_longitude").text($("#longitude").val());
    });
$(function() {
    //Get first load page height
    mapHeight = $( window ).height() - 150;

    //hide map by default
    $("#map").css("height", mapHeight);
});
</script>
<script src="js/survey.js?v=2"></script>
<?php
require_once '../include/footer.php';
?>