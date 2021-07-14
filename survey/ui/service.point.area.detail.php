<?php
include_once realpath(__DIR__ . '/../..').'/include/settings.php';
include_once realpath(__DIR__ . '/../..').'/include/checksession.php';
include_once realpath(__DIR__ . '/../..').'/include/checkpermission.php';
require_once realpath(__DIR__ . '/../..').'/class/HtmlHelper.php';
require_once realpath(__DIR__ . '/../..').'/class/Survey.class.php';
require_once realpath(__DIR__ . '/../..') . '/class/Tree.php';
require_once realpath(__DIR__ . '/../..') . '/class/Dictionary.php';
require_once realpath(__DIR__ . '/../..') . '/class/coordinates.php';

$html = new HTML ( $LANGUAGE );
$dictionary = new Dictionary ( $LANGUAGE );
$dictionary->GetAllDictionary ();
$Survey = new Survey( );
$area_tree = new Tree("AREA_TREE");

$filter = $linesArr = array();
$area_id = $area_text = $condition = null;

if(isset($_REQUEST["area"]) && $_REQUEST["area"]!=NULL){
    $area_id = $_REQUEST["area"];
    $area_path = $area_tree->GetNodePath($area_id);
    $filter["area_path"] = $area_path;
    $condition .= "&area_path=". $area_path;
    $area_text = $area_tree->GetPathString($area_id);
}else{
    $area_id=$USERDIR;
    $area_path = $area_tree->GetNodePath($area_id);
    $filter["area_path"] = $area_path;
    $condition .= "&area_path=". $area_path;
    $area_text = $area_tree->GetPathString($area_id);
}

$report_data = $Survey->GetServicePointSummaryByArea($filter);// To Get The Summary
$map_points = $Survey->GetServicePointByArea($filter);// To Get The Points
$map_polygons = $Survey->GetServicePolygonsByArea($filter);// To Darw The Polygon

$linesArr = array();
for ($i=0; $i<count($map_points); $i++) {
    $utm = ll2utm($map_points[$i]['latitude'], $map_points[$i]['longitude']);
    $map_points[$i]['x'] = $utm["x"];
    $map_points[$i]['y'] = $utm["y"];
}

$lines = $Survey->GetLines($area_id);
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

$latitude = $longitude = NULL;
$latitude = $report_data[0]["LAT_CENERT"];
$longitude = $report_data[0]["LONG_CENERT"];

// Summary
for ( $i=0; $i < count( $report_data ); $i++ ){
    $report_data[$i]['NODE_NAME'] = $area_tree->GetOrgPathString($report_data[$i]['NODE_ID']);
    $report_data[$i]["single_phase_consumers"] =number_format($report_data[$i]["single_phase_consumers"], 0, '.', ',');
    $report_data[$i]["three_phase_consumers"] =number_format($report_data[$i]["three_phase_consumers"], 0, '.', ',');
    $report_data[$i]["service_point_count"] =number_format($report_data[$i]["service_point_count"], 0, '.', ',');
    $report_data[$i]["transformer_number_count"] =number_format($report_data[$i]["transformer_number_count"], 0, '.', ',');
}

$cols1 = array();
$cols1[] = array("column"=>"NODE_NAME");
$cols1[] = array("column"=>"single_phase_consumers");
$cols1[] = array("column"=>"three_phase_consumers");
$cols1[] = array("column"=>"service_point_count");
$cols1[] = array("column"=>"transformer_number_count");

$tableOptions1 = array();
$tableOptions1["tableClass"]= "table-hover table-bordered table-striped";
$tableOptions1["ordering"]= "false";
$tableOptions1["paging"]="false";
$tableOptions1["footer"]="true";



// number of consumers
$cols = array();
$cols[] = array("column"=>"number_of_consumers");
$cols[] = array("column"=>"single_phase_consumers");
$cols[] = array("column"=>"three_phase_consumers");
$cols[] = array("column"=>"service_point_count");
$cols[] = array("column"=>"point_type");
$tableOptions = array();
$tableOptions["tableClass"]= "table-hover table-bordered table-striped";
$tableOptions["ordering"]= "false";
$tableOptions["paging"]="false";


$condition2 = "&area_id=". $area_id;

$cols2 = array();
$cols2[] = array("column"=>"sequence");
$cols2[] = array("column"=>"point_id");
$cols2[] = array("column"=>"point_type");
$cols2[] = array("column"=>"single_phase_consumers");
$cols2[] = array("column"=>"three_phase_consumers");
$cols2[] = array("column"=>"transformer");
if($MAPCORDINATES == 'latlng') {
    $cols2[] = array("column"=>"latitude");
    $cols2[] = array("column"=>"longitude");
} else if($MAPCORDINATES == 'utm') {
    $cols2[] = array("column"=>"x");
    $cols2[] = array("column"=>"y");
}

$tableOptions2 = array();
$tableOptions2["tableClass"]= "table-hover table-bordered table-condensed table-striped";
$tableOptions2["key"]=array("id"=>"point_id");
$tableOptions2["ordering"] = "true";


$html->OpenDiv("row");
{
    $html->OpenSpan(12);
    {
        $html->OpenWidget ($area_text, null, array('collapse' => true, 'fullscreen'=>true,'table-actions'=>"example1"));
        {
            ?>
                <div class="portlet light">
                    <div class="portlet-title tabbable-line">
                        <div class="tools">
                            <a href="" class="fullscreen" data-original-title="" title=""> </a>
                        </div>
                        <ul class="nav nav-tabs pull-left">
                            <li class="active">
                                <a href="#summary" data-toggle="tab" > <?php print $dictionary -> GetValue("service_point_summary"); ?> </a>
                            </li>
                            <li>
                                <a href="#list" data-toggle="tab" aria-expanded="true"> <?php print $dictionary -> GetValue("survey_list"); ?> </a>
                            </li>
                        </ul>
                    </div>
                    <div class="portlet-body">
                        <div class="tab-content">
                            <div class="tab-pane active" id="summary">
                                <?php
                                print "<b>".$dictionary->GetValue("service_point_summary")."</b><br/><br/>";
                                $html->Table($report_data, $cols1, $tableOptions1);//Summury
                                print "<br/>";
                                $html->Datatable("example", "api/list.service.point.count.by.area.php?id=".$area_id, $cols, $tableOptions);// number of consumers
                                ?>
                                <div class="row" style="margin: 5px; padding: 5px; text-align: center; background: #bcd5ed;">
                                    <div class="col-lg-3"><img src="../img/green.png"> <?php print $dictionary->GetValue("transformer"); ?></div>
                                    <div class="col-lg-3"><img src="../img/blue.png"> <?php print $dictionary->GetValue("Electric_Pole"); ?></div>
                                    <div class="col-lg-3"><img src="../img/red.png"> <?php print $dictionary->GetValue("Twisted_Cable"); ?></div>
                                    <div class="col-lg-3"><img src="../img/yellow.png"> <?php print $dictionary->GetValue("building"); ?></div>
                                </div>
                                <div id="map"></div>
                            </div>

                            <div class="tab-pane" id="list">
                                <?php
                                $html->Datatable("example1", "api/list.survey.admin.php?".$condition2, $cols2, $tableOptions2);
                                ?>
                            </div>
                        </div>
                    </div>
                </div>

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

<link rel="stylesheet" href="../osm/leaflet.draw.css" />
<script src="../osm/leaflet.draw.js"></script>
<script src="../osm/Edit.Poly.js"></script>
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
</style>

<script>
    colors = ["#ff0000", "#ff8000", "#ffff00", "#80ff00", "#00ffff", "#800000", "#0040ff", "#bf00ff", "#8000ff"];
    colors = ["#ff0000"];
    var iconSize = 12;
    var selectedLine = null;
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

    <?php // Draw the Polygons
    if($map_polygons){
        for($i=0; $i<count($map_polygons); $i++){
            ?>
            var NODE_ID = "<?php print $map_polygons[$i]["NODE_ID"]; ?>";
            var NODE_NAME = "<?php print $map_polygons[$i]["NODE_NAME"]; ?>";
            var COORDINATES = "<?php print $map_polygons[$i]["COORDINATES"]; ?>";
            var COLOR = "deepskyblue";//"<?php //print $map_polygons[$i]["COLOR"]; ?>";
            if( COORDINATES != "" ){
                map.addPolygon( COORDINATES, layerGroup, COLOR, NODE_ID, false, NODE_NAME);
            }
            <?php
        }
    }
    ?>


    <?php // Draw the Points
    if($map_points){
        for($i=0; $i<count($map_points); $i++){
            ?>
            var lat = "<?php print $map_points[$i]["latitude"]; ?>";
            var long = "<?php print $map_points[$i]["longitude"]; ?>";
            var x = "<?php print $map_points[$i]["x"]; ?>";
            var y = "<?php print $map_points[$i]["y"]; ?>";
            var point_type_id = <?php print $map_points[$i]["point_type_id"]; ?>;
            color = GetStatusColor(point_type_id);

            var point_id = <?php print $map_points[$i]["point_id"]; ?>;
            var single_phase_consumers = "<?php print $map_points[$i]["single_phase_consumers"]; ?>";
            var three_phase_consumers = "<?php print $map_points[$i]["three_phase_consumers"]; ?>";
            var accuracy_id = "<?php print $map_points[$i]["accuracy_id"]; ?>"+"%";
            var point_type = "<?php print $dictionary->GetValue($map_points[$i]["point_type"]); ?>";
            var station_id = "<?php print $map_points[$i]["station_id"]; ?>";
            var feeder_id = "<?php print $map_points[$i]["feeder_id"]; ?>";
            var capacity_id = "<?php print $map_points[$i]["capacity_id"]; ?>";
            var transformer_number = "<?php print $map_points[$i]["transformer_number"]; ?>";
            var transformer_generated_number = "<?php print $map_points[$i]["transformer_generated_number"]; ?>";
            var area_id = "<?php print $map_points[$i]["area_id"]; ?>";
            var sequence = <?php print $i; ?>+1;


            PopUpString = "";
            if(point_type_id == 4 ){
                PopUpString = "#"+point_id+'<br><b>'+point_type+'</b><br /><b>'+
                                station_id+'/'+feeder_id+'/'+capacity_id+'/'+transformer_number+' ['+transformer_generated_number+']</b><br />'+
                                '<?php print $dictionary->GetValue("sequence"); ?>'+': <b>'+
                                sequence+'</b><br /><br />'+
                                '<a class="quick_edit" data-id="'+point_id+'">'+'<?php print $dictionary->GetValue("quick_edit"); ?>'+'&nbsp;<i class="fa fa-pencil"></i></a><br />'+
                                '<a href="add_survey.php?id=<?php print $area_id; ?>&point_id='+point_id+'">'+'<?php print $dictionary->GetValue("edit"); ?>'+'&nbsp;<i class="fa fa-pencil"></i></a><br />'+
                                '<a href="edit_location.php?point_id='+point_id+'">'+'<?php print $dictionary->GetValue("edit_location"); ?>'+'&nbsp;<i class="fa fa-map-marker"></i></a><br />'+
                                '<a href="#" class="point_full_details" data-full_details="'+point_id+'">'+'<?php print $dictionary->GetValue("point_full_detail"); ?>'+'&nbsp;<i class="fa fa-info-circle"></i></a>';
                icon = {img: "transformer-icon", "size": 18 }
            } else {
                PopUpString = "#"+point_id+'<br><b>'+point_type+'</b><br />'+
                                '<?php print $dictionary->GetValue("single_phase_consumers"); ?>'+': <b>'+
                                single_phase_consumers+'</b><br />'+'<?php print $dictionary->GetValue("three_phase_consumers"); ?>'+': <b>'+
                                three_phase_consumers+'</b><br />'+'<?php print $dictionary->GetValue("accuracy_id"); ?>'+': <b>'+
                                accuracy_id+'</b><br />'+'<?php print $dictionary->GetValue("sequence"); ?>'+': <b>'+
                                sequence+'</b><br /><br />'+
                                <?php if($MAPCORDINATES == 'utm') { ?>
                                    'UTM: '+x+", "+y+ '<br /><br />'+
                                <?php } else if($MAPCORDINATES == 'latlng') { ?>
                                    'LatLng: '+ lat+", "+long+"<br /><br />"+
                                <?php } ?>
                                '<a href="add_survey.php?id=<?php print $area_id; ?>&point_id='+point_id+'">'+'<?php print $dictionary->GetValue("edit"); ?>'+'&nbsp;<i class="fa fa-pencil"></i></a><br />'+
                                '<a href="edit_location.php?point_id='+point_id+'">'+'<?php print $dictionary->GetValue("edit_location"); ?>'+'&nbsp;<i class="fa fa-map-marker"></i></a><br />'+
                                '<a href="#" class="point_full_details" data-full_details="'+point_id+'">'+'<?php print $dictionary->GetValue("point_full_detail"); ?>'+'&nbsp;<i class="fa fa-info-circle"></i></a>';
                icon = {"size": 12, "img": "../img/" + color }
            }
            map.addMarker( lat, long, layerGroup, icon, PopUpString, area_id, point_id, false);

            <?php
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
                                '<br>'+
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

</script>