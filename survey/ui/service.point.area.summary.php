<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

include_once realpath(__DIR__ . '/../..').'/include/settings.php';
include_once realpath(__DIR__ . '/../..').'/include/checksession.php';
include_once realpath(__DIR__ . '/../..').'/include/checkpermission.php';
require_once realpath(__DIR__ . '/../..').'/class/HtmlHelper.php';
require_once realpath(__DIR__ . '/../..').'/class/Survey.class.php';
require_once realpath(__DIR__ . '/../..') . '/class/Tree.php';
require_once realpath(__DIR__ . '/../..') . '/class/Dictionary.php';
$html = new HTML ( $LANGUAGE );
$dictionary = new Dictionary ( $LANGUAGE );
$dictionary->GetAllDictionary ();
$options = array("class"=>"form-control");
$listoptions = array("class"=>"form-control","optional"=>"true");
$Survey = new Survey( );
$description_key = "description_Survey";
$area_tree = new Tree("AREA_TREE");
$area_id=null;
$id="";
$filter = $TransformerArr = array();
$area_id = $area_text = $condition  = $feeder_id = $transformer_number  = null;
$count = $sum = 0;


if(isset($_REQUEST["area"])&&$_REQUEST["area"]!=NULL){
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

$NodeInfo = $area_tree->GetNodeInfo( $area_id );
$latitude = $NodeInfo[0]["LAT_CENERT"];
$longitude = $NodeInfo[0]["LONG_CENERT"];

$path_lenght = explode(".", $area_path);

if( isset($_POST["from_date"] ) && $_POST["from_date"] != NULL ) {
    $from_date = $_POST["from_date"];
    $filter["from_date"] = $from_date;
    $condition .= "&from_date=$from_date";

}else{
    $from_date = date('Y-m-01');
    $filter["from_date"] = $from_date;
    $condition .= "&from_date=$from_date";
}

if( isset($_POST["to_date"] ) && $_POST["to_date"] != NULL ) {
    $to_date = $_POST["to_date"];
    $filter["to_date"] = $to_date." 23:59:59";
    $condition .= "&to_date=$to_date";
}else{
    $to_date = date("Y-m-d");
    $filter["to_date"] = $to_date." 23:59:59";
    $condition .= "&to_date=$to_date";
}

$FeederArr = $Survey->GetFeederByStation();
//$TransformerArr = $Survey->GetTransformerArr();
if(isset($_REQUEST["feeder_id"]) && $_REQUEST["feeder_id"] != ""){
    $feeder_id = $_REQUEST["feeder_id"];
    $filter["feeder_id"] = $feeder_id;
    $condition .= "&feeder_id=".$feeder_id;
    $TransformerArr = $Survey->GetTransformerArr(array("feeder_id"=>$feeder_id));
}

if( isset($_REQUEST["transformer_number"]) && $_REQUEST["transformer_number"] != NULL ){
    $transformer_number = $_REQUEST["transformer_number"];
    $filter["transformer_number"] = $transformer_number;
    $condition .= "&transformer_number=".$transformer_number;
}

$report_data = $Survey->GetServicePointSummaryByArea($filter);
$map_points = $Survey->GetServicePointByArea($filter);
$map_polygons = $Survey->GetServicePolygonsByArea($filter);

$single_phase_sum=$three_phase_sum=$point_sum=$transformer_sum= 0;

if($report_data){
    for($i=0; $i<count($report_data); $i++){
        $single_phase_sum +=$report_data[$i]["single_phase_consumers"];
        $three_phase_sum += $report_data[$i]["three_phase_consumers"];
        $point_sum += $report_data[$i]["service_point_count"];
        $transformer_sum += $report_data[$i]["transformer_number_count"];
    }
}

$cols = array();
$cols[] = array("column"=>"NODE_ID", "title"=>"zone_id");
$cols[] = array("column"=>"NODE_NAME");
$cols[] = array("column"=>"single_phase_consumers");
$cols[] = array("column"=>"three_phase_consumers");
$cols[] = array("column"=>"service_point_count");
$cols[] = array("column"=>"transformer_number_count");
$cols[] = array("column"=>"ACTION_COL", "style"=>"width:150px","action-type"=>"ajax",
                    "buttons"=> array(
                    array("action-class"=>"survey_edit", "button-icon"=>"fa fa-info-circle", "title"=>$dictionary->GetValue("detail"), "type"=>"link", "url"=>"service_point_area_detail.php", "target"=>"_blank")
                    )
                );

$tableOptions = array();
$tableOptions["tableClass"]= "table-hover table-bordered table-striped";
$tableOptions["ordering"]= "true";
$tableOptions["paging"]="false";
$tableOptions["footer"]="true";
$tableOptions["key"]=array("area"=>"area");
$tableOptions["totals"]=array("2"=>number_format($single_phase_sum, 0, '.', ','), "3"=>number_format($three_phase_sum, 0, '.', ','),"4"=>number_format($point_sum, 0, '.', ','),"5"=>number_format($transformer_sum, 0, '.', ','));
$html->OpenDiv("row");
{
    $html->OpenSpan(12);
    {
        $html->OpenWidget ("service_point_summary", null, array('collapse' => true, 'fullscreen'=>true,'table-actions'=>"example1"));
        {
            if($id=="")
            {
                $html->OpenForm ( null, "form3");
                {
                    $html->OpenDiv("row");
                    {
                        $html->OpenSpan(3);
                        {
                            $dirTree = array (
                                array ( "type"=>"hidden", "name"=>"area", "value"=>$area_id, "list"=>NULL, "options"=>NULL ),
                                array ( "type"=>"text", "name"=>"area_text", "value"=>$area_text, "list"=>NULL, "options"=>array("class"=>"form-control open_area_tree", "tree"=>"", "readonly"=>"readonly") )
                            );
                            $html->DrawGenericFormField ( "area_id", $dirTree, null, $options);
                        }
                        $html->CloseSpan();

                        $html->OpenSpan(2);
                        {
                            $html->DrawFormField("select", "feeder_id", $feeder_id, $FeederArr, array("class"=>"form-control", "optional"=>"true"));
                        }
                        $html->CloseSpan();

                        $html->OpenSpan(2);
                        {
                            $html->DrawFormField("text", "transformer_number", $transformer_number, null, array("class"=>"form-control", "optional"=>"true"));
                        }
                        $html->CloseSpan();

                        $html->OpenSpan(2);
                        {
                            $html->DrawFormField ( "text", "from_date", $from_date, NULL, array("class"=>"form-control date-picker", "data-date-format"=>"yyyy-mm-dd", "readonly"=>"readonly") );
                        }
                        $html->CloseSpan();

                        $html->OpenSpan(2);
                        {
                            $html->DrawFormField ( "text", "to_date", $to_date, NULL, array("class"=>"form-control date-picker", "data-date-format"=>"yyyy-mm-dd", "readonly"=>"readonly") );
                        }
                        $html->CloseSpan();

                        $html->OpenSpan(1);
                        {
                            ?>
                            <div class="form-group">
                                <label>&nbsp;</label>
                                <div class="input-group">
                                    <span class="input-group-btn">
                                        <button class="btn green filter_service_point_summary" type="submit"><?php echo $dictionary->GetValue("filter");?></button>
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
            ?>
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
                                <a href="#report" data-toggle="tab" aria-expanded="true"> <?php print $dictionary -> GetValue("report"); ?> </a>
                            </li>
                        </ul>
                    </div>
                    <div class="portlet-body">
                        <div class="tab-content">
                            <div class="tab-pane active" id="map_view">
                                <div class="row" style="margin: 5px; padding: 5px; text-align: center; background: #bcd5ed;">
                                    <div class="col-lg-2"><img src="../img/summary.png"> <?php print $dictionary->GetValue("has_survey_point"); ?></div>
                                    <div class="col-lg-2"><img src="../img/green.png"> <?php print $dictionary->GetValue("transformer"); ?></div>
                                    <div class="col-lg-2"><img src="../img/blue.png"> <?php print $dictionary->GetValue("Electric_Pole"); ?></div>
                                    <div class="col-lg-2"><img src="../img/red.png"> <?php print $dictionary->GetValue("Twisted_Cable"); ?></div>
                                    <div class="col-lg-2"><img src="../img/yellow.png"> <?php print $dictionary->GetValue("building"); ?></div>
                                </div>
                                <div id="map"></div>
                            </div>

                            <div class="tab-pane" id="report">
                                <?php
                                $html->Datatable("example", "api/list.service.point.area.summary.php?".$condition, $cols, $tableOptions);
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
<script src="../js/OpenMap.js"></script>
<script src="../js/map_helper.js"></script>
<link rel="stylesheet" href="../osm/Control.FullScreen.css" />
<script src="../osm/Control.FullScreen.js"></script>


<style>
#map{
    width: 100%;
    height: 600px;
}
</style>

<script>
    var Edit = '<?php print $dictionary->GetValue("edit"); ?>';
    var Edit_Location = '<?php print $dictionary->GetValue("edit_location"); ?>';
    var Single_Phase ='<?php print $dictionary->GetValue("single_phase_consumers"); ?>';
    var Three_Phase = '<?php print $dictionary->GetValue("three_phase_consumers"); ?>';
    var Accuracy = '<?php print $dictionary->GetValue("accuracy_id"); ?>';
    var Sequence = '<?php print $dictionary->GetValue("sequence"); ?>';
    var Point_Count = '<?php print $dictionary->GetValue("service_point_count"); ?>';
    var Transformer_Count = '<?php print $dictionary->GetValue("transformer_number_count"); ?>';
    var Detail = '<?php print $dictionary->GetValue("detail"); ?>';
    var Add_Point = '<?php print $dictionary->GetValue("add_point"); ?>';

    <?php
    if( $latitude == "" || $longitude == "" ){
        $MapZoom = "13";
        // if (strpos($area_path, '.3') !== false) { // Sul-Nawand : 35.5653, 45.4223
        //     $latitude = "35.5653";
        //     $longitude = "45.4223";

        // } else if (strpos($area_path, '.4') !== false) { // Duhok-Nawand: 36.8654, 42.9926
        //     $latitude = "36.8654";
        //     $longitude = "42.9926";

        // } else if (strpos($area_path, '.5') !== false) { // Duhok-Nawand: 34.6254, 45.3169
        //     $latitude = "34.6254";
        //     $longitude = "45.3169";

        // } else { // Erbil-Qalat : 36.191175, 44.009134
        //     $latitude = "36.191175";
        //     $longitude = "44.009134";
        // }

        $latitude = "9.0778";
        $longitude = "8.6775";
    } else {
        $MapZoom = "16";
    }
    ?>
    var MapZoom = "<?php print $MapZoom; ?>";
    var latitude = "<?php print $latitude; ?>";
    var longitude = "<?php print $longitude; ?>";

    var map = new Map("map", latitude, longitude, false, false);
    map.zoom(MapZoom);

    var layerGroup = map.addGroup();


    $(function() {
        $('#feeder_id').on('change', function () {
            feeder_id = $(this).val();
            //FillTransformer(feeder_id);
        });
    });

    /*
    function FillTransformer(feeder_id){
        if( feeder_id > 0){
            FillListOptions("ui/get.transformers.php?feeder_id="+feeder_id, "transformer_id", true);
        } else {
            FillListOptions("ui/get.transformers.php", "transformer_id", true);
        }
    }
    */

    <?php
    function AreaHaspoints($area_id, $report_data){
        //echo count($report_data);
        if ($report_data) {
            for($i=0; $i<count($report_data); $i++){
                //print $report_data[$i]["NODE_ID"] ."==". $area_id."<br>";
                if($report_data[$i]["NODE_ID"] == $area_id){
                    return true;
                }
            }
        }
        return false;
    }

    // Draw The polygons
    if($map_polygons && $feeder_id == null && $transformer_number == null){
        for($i=0; $i<count($map_polygons); $i++){
            if(AreaHaspoints($map_polygons[$i]["NODE_ID"], $report_data)){
                $map_polygons[$i]["COLOR"] = "deepskyblue";
            }
            ?>
            var NODE_ID = "<?php print $map_polygons[$i]["NODE_ID"]; ?>";
            var NODE_NAME = "<?php print $map_polygons[$i]["NODE_NAME"]; ?><br/><br/>"+'<a href="service_point_area_detail.php?area='+NODE_ID+'" target="_blank">'+Detail+'&nbsp;<i class="fa fa-info-circle"></i></a>';
            var COORDINATES = "<?php print $map_polygons[$i]["COORDINATES"]; ?>";
            var COLOR = "<?php print $map_polygons[$i]["COLOR"]; ?>";
            if( COORDINATES != "" ){
                map.addPolygon( COORDINATES, layerGroup, COLOR, NODE_ID, false, NODE_NAME);
            }
            <?php
        }
    }



    ?>


    <?php
    if( ( $map_points && count($path_lenght) > 3 ) || $feeder_id || $transformer_number ){
        // Draw The Survey Points
        if ($map_points) {
            for($i=0; $i<count($map_points); $i++){
                ?>
                var lat = "<?php print $map_points[$i]["latitude"]; ?>";
                var long = "<?php print $map_points[$i]["longitude"]; ?>";
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
                var area_id = "<?php print $map_points[$i]["area_id"]; ?>";
                var sequence = <?php print $i; ?>+1;

                PopUpString = "";
                if(point_type_id == 4 ){
                    PopUpString = '<b>'+point_type+'</b><br /><b>'+
                                    station_id+'/'+feeder_id+'/'+capacity_id+'/'+transformer_number+'</b><br />'+
                                    Sequence+': <b>'+sequence+'</b><br /><br />'+
                                    '<a href="add_survey.php?point_id='+point_id+'">'+Edit+'&nbsp;<i class="fa fa-pencil"></i></a><br />'+
                                    '<a href="edit_location.php?point_id='+point_id+'">'+Edit_Location+'&nbsp;<i class="fa fa-map-marker"></i></a>';

                } else {
                    PopUpString = '<b>'+point_type+'</b><br />'+
                                    Single_Phase+': <b>'+single_phase_consumers+'</b><br />'+
                                    Three_Phase+': <b>'+three_phase_consumers+'</b><br />'+
                                    Accuracy+': <b>'+accuracy_id+'</b><br />'+
                                    Sequence+': <b>'+sequence+'</b><br /><br />'+
                                    '<a href="add_survey.php?point_id='+point_id+'">'+Edit+'&nbsp;<i class="fa fa-pencil"></i></a><br />'+
                                    '<a href="edit_location.php?point_id='+point_id+'">'+Edit_Location+'&nbsp;<i class="fa fa-map-marker"></i></a>';
                }

                map.addMarker( lat, long, layerGroup, color, PopUpString, area_id);
                <?php
            }
        }
    } else {
        // Draw the Summary Points
        if ($map_points) {
            for($i=0; $i<count($report_data); $i++){
                ?>
                var NODE_ID = "<?php print $report_data[$i]["NODE_ID"]; ?>";
                var NODE_NAME = "<?php print $report_data[$i]["NODE_NAME"]; ?>";
                var single_phase_consumers = "<?php print number_format($report_data[$i]["single_phase_consumers"], 0, '.', ','); ?>";
                var three_phase_consumers = "<?php print number_format($report_data[$i]["three_phase_consumers"], 0, '.', ','); ?>";
                var service_point_count = "<?php print number_format($report_data[$i]["service_point_count"], 0, '.', ','); ?>";
                var transformer_number_count = "<?php print number_format($report_data[$i]["transformer_number_count"], 0, '.', ','); ?>";
                var lat = "<?php print $report_data[$i]["LAT_CENERT"]; ?>";
                var long = "<?php print $report_data[$i]["LONG_CENERT"]; ?>";
                var color = "summary";

                PopUpString = '<b>'+NODE_NAME+'</b><br />'+
                                    Single_Phase+': <b>'+single_phase_consumers+'</b><br />'+
                                    Three_Phase+': <b>'+three_phase_consumers+'</b><br />'+
                                    Point_Count+': <b>'+service_point_count+'</b><br />'+
                                    Transformer_Count+': <b>'+transformer_number_count+'</b><br /><br />'+
                                    '<a href="service_point_area_detail.php?area='+NODE_ID+'" target="_blank">'+Detail+'&nbsp;<i class="fa fa-info-circle"></i></a>'+
                                    '<br/>'+
                                    '<a href="add_survey.php?id='+NODE_ID+'">'+Add_Point+'&nbsp;<i class="fa fa-plus-circle"></i></a>';

                map.addMarker( lat, long, layerGroup, color, PopUpString, NODE_ID);
                <?php
            }
        }
    }
    ?>
</script>