<?php
include_once realpath(__DIR__ . '/..').'/include/checksession.php';
include_once realpath(__DIR__ . '/..').'/include/checkpermission.php';
include_once realpath(__DIR__ . '/..').'/include/header.php';
require_once realpath(__DIR__ . '/..') . '/class/Survey.class.php';
require_once realpath(__DIR__ . '/..') . '/class/Tree.php';
require_once realpath(__DIR__ . '/..').'/class/HtmlHelper.php';

$html = new HTML ( $LANGUAGE );
$dictionary = new Dictionary ( $LANGUAGE );
$dictionary->GetAllDictionary ();
$Survey = new Survey( );
$condition=array();
$Tree = new Tree("AREA_TREE");
$areas = $Tree->GetFullTree();
$area_id="";
$area_text = $Tree->GetPathString($area_id);
$areaArray = $Tree->SelectNodeChildren($area_id);
$condition=array();
//$condition=null;
//$ServicePoints=$Survey->GetServicePoint();

for ($i=0; $i<count($areas); $i++) {
    $areas[$i]["COORDINATES"] = str_replace(array("\r", "\n"), '', $areas[$i]["COORDINATES"]);
}
?>
<style>
#map{
    width: 100%;
    height: 600px;
}
.leaflet-draw-tooltip {
    display: none;
}
.tree ul{
    padding-right: 20px;
}
.tree span{
    padding-right: 0px;
}
.portlet.light {
    padding: 0;
}
.tree {
    padding: 0;
}
</style>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.3.1/dist/leaflet.css"
    integrity="sha512-Rksm5RenBEKSKFjgI3a41vrjkw4EVPlJ3+OiI65vTjIdo9brlAacEuKOiQ5OFh7cOI1bkDwLqdLw3Zg0cRJAAQ=="
    crossorigin="" />
<script src="https://unpkg.com/leaflet@1.3.1/dist/leaflet.js" integrity="sha512-/Nsx9X4HebavoBvEBuyp3I7od5tA0UzAxs+j83KgC8PU0kgB4XiK4Lfe4y4cgBtaRJQEIFCW+oC506aPT2L1zw=="
    crossorigin=""></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet.draw/1.0.2/leaflet.draw.css" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet.draw/1.0.2/leaflet.draw.js"></script>
<?php

$html->OpenWidget ("service_point_summary", null, array('collapse' => true, 'fullscreen'=>true,'table-actions'=>"example1"));
{
    $html->Opendiv("row", "treeCheck");
    {
        $html->OpenSpan(3);
        {
            $area_id = $USERDIR; //all tree
            require_once '../tree/ui/tree.check.php';
        }
        $html->CloseSpan();

        $html->OpenSpan (9);
        {
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
                    <div id="map"></div>
                    </div>

                    <div class="tab-pane" id="report">
                        <input type="hidden" id="node_ids">
                        <section id="reportx"></section>
                        <?php
                        $id="";
                        $condition = null;
                        $cols = array();
                        $cols[] = array("column"=>"number_of_consumers");
                        $cols[] = array("column"=>"single_phase_consumers");
                        $cols[] = array("column"=>"three_phase_consumers");
                        $cols[] = array("column"=>"service_point_count");
                        $cols[] = array("column"=>"point_type");
                        $tableOptions = array();
                        $tableOptions["tableClass"]= "table-hover table-bordered table-striped";
                        $tableOptions["ordering"]= "true";
                        $tableOptions["paging"]="true";
                        $tableOptions["footer"]="false";



                        $html->Datatable("example", "api/list.service.point.count.by.area.php".$condition, $cols, $tableOptions);
                        ?>
                    </div>
                </div>
            </div>
        </div>
        <?php
        }
        $html->CloseSpan ();
    }
    $html->CloseDiv();
}
$html->CloseWidget();
?>
<script>
    var map;
    function UpdateCoordinates(str) {
        arr = JSON.parse(str    )
        for(i=0; i<arr.length; i++){
            lat = arr[i][0];
            lng = arr[i][1];
            arr[i] = [lng, lat];
        }
        document.getElementById("coordinates").value = JSON.stringify(arr);
    }

    $(function () {
        $latitude = "9.0778"; //36.191175
        $longitude = "8.6775"; //44.009134

        map = new Map("map", $latitude, $longitude);
        map.zoom(9);
        $('section#report').load('ui/service.point.count.by.area.php');
        height = $( window ).height() - 100;
        $("#map").css("height", height - 64);
        $(".tree").css("height", height);

        //show / hide area
        $("body").on( "change", ":checkbox", function() {
            id  = $(this).attr("id");
            path = $(this).data("path");
            color = $(this).data("color");
            area_name = $(this).data("text");
            var layerGroup = map.addGroup();

            if($(this).is(":checked")){
                //get data
                var request = $.ajax({
                    url: "api/get.service.points.php?id="+id,
                    type: 'GET',
                    processData: false,
                    contentType: false
                });
                request.done(function(data) {
                    if(data != ""){
                        AddPins(data, map, layerGroup, id);
                    }
                });
                polygon = map.addPolygon(JSON.stringify(path), layerGroup, color, id, false, area_name);
                UpdateAreaIds(id, true);


            }else{
                HideMarkersByArea(id, map);
                HidePolygon(id);
                UpdateAreaIds(id, false);
            }

            CheckChildren($(this));
        });
    });

    function CheckChildren(obj){
        if($(obj).is(":checked")){
            checked = true;
        }else{
            checked = false;
        }
        checkboxes = $(obj).siblings().find(":checkbox");
        $(checkboxes).each(function () {
            id  = $(this).attr("id");
            if(checked){
                if($(this).is(":checked")){
                    //child is checked, do nothing
                }else{
                    //child is unchecked, check it
                    $(this).prop('checked', true);
                    path = $(this).data("path");
                    color = $(this).data("color");
                    area_name = $(this).data("text");
                    var layerGroup = map.addGroup();

                    var request = $.ajax({
                        url: "api/get.service.points.php?id="+id,
                        type: 'GET',
                        processData: false,
                        contentType: false
                    });
                    request.done(function(data) {
                        if(data != ""){
                            AddPins(data, map, layerGroup, id);
                        }
                    });
                    polygon = map.addPolygon(JSON.stringify(path), layerGroup, color, id, false, area_name);
                    UpdateAreaIds(id, true);
                }
            }else{
                if($(this).is(":checked")){
                    //uncheck it
                    $(this).prop('checked', false);
                    HideMarkersByArea(id, map);
                    HidePolygon(id);
                    UpdateAreaIds(id, false);
                }else{
                    //do nothing
                }
            }

        });

        ids = $("#node_ids").val();
        $('#example').DataTable().ajax.url('api/list.service.point.count.by.area.php?point_type=1&id=' +ids).load();
    }

    function AddPins(data, map, layerGroup, pin_area){
        for (var i in data) {
            var survey = data[i];
            var point_id = survey["point_id"];
            var area = survey["area_id"];
            var lat = survey["latitude"];
            var long = survey["longitude"];
            var single_phase_consumers = survey["single_phase_consumers"];
            var three_phase_consumers = survey["three_phase_consumers"];
            var station_id = survey["station_id"];
            var feeder_id = survey["feeder_id"];
            var capacity_id = survey["capacity_id"];
            var transformer_number = survey["transformer_number"];
            var accuracy_id = survey["accuracy_id"]+"%";
            var point_type_id=survey["point_type_id"];
            var point_type=survey["point_type"];
            var area_name=survey["NODE_NAME"];
            var sequence=survey["sequence"];
            color = GetStatusColor(point_type_id);

            if(lat != null && long != null){
                for(i=0; i<map.polygons.length; i++){
                    if (pin_area == area) {
                        PopUpString = "";
                        if(point_type_id == 4 ){
                            PopUpString = point_type+'<br /><b>'+
                                            station_id+'/'+feeder_id+'/'+capacity_id+'/'+transformer_number+'</b><br />'+
                                            '<?php print $dictionary->GetValue("sequence"); ?>'+': <b>'+
                                            sequence+'</b><br /><br />'+
                                            '<a href="add_survey.php?point_id='+point_id+'">'+'<?php print $dictionary->GetValue("edit"); ?>'+'&nbsp;<i class="fa fa-pencil"></i></a><br />'+
                                            '<a href="edit_location.php?point_id='+point_id+'">'+'<?php print $dictionary->GetValue("edit_location"); ?>'+'&nbsp;<i class="fa fa-map-marker"></i></a>';

                        } else {
                            PopUpString = point_type+'<br />'+
                                            '<?php print $dictionary->GetValue("single_phase_consumers"); ?>'+': <b>'+
                                            single_phase_consumers+'</b><br />'+'<?php print $dictionary->GetValue("three_phase_consumers"); ?>'+': <b>'+
                                            three_phase_consumers+'</b><br />'+'<?php print $dictionary->GetValue("accuracy_id"); ?>'+': <b>'+
                                            accuracy_id+'</b><br />'+'<?php print $dictionary->GetValue("sequence"); ?>'+': <b>'+
                                            sequence+'</b><br /><br />'+
                                            '<a href="add_survey.php?point_id='+point_id+'">'+'<?php print $dictionary->GetValue("edit"); ?>'+'&nbsp;<i class="fa fa-pencil"></i></a><br />'+
                                            '<a href="edit_location.php?point_id='+point_id+'">'+'<?php print $dictionary->GetValue("edit_location"); ?>'+'&nbsp;<i class="fa fa-map-marker"></i></a>';
                        }
                        marker=map.addMarker(lat, long, layerGroup, color, PopUpString,area);
                    }
                }
            }
        }
    }

    function HideMarkersByArea(area_id, map){
        for(i=0; i<map.markers.length; i++){
            element = map.markers[i];
            if (element.properties["area"] == area_id) {
                map.remove(element);
            }
        }
    }
    function HidePolygon(id){
        for(i=0; i<map.polygons.length; i++){
            element = map.polygons[i];
            if (element.properties["id"] == id) {
                map.remove(element)
            }
        }
    }

    function UpdateAreaIds(id, add){
        ids = $("#node_ids").val();
        if(ids == ""){
            idsArr = [];
        }else{
            idsArr = ids.split(",");
        }
        if (add){
            idsArr.push(id);
        }else{
            //remove
            index = idsArr.indexOf(id);
            if (index > -1) {
                idsArr.splice(index, 1);
            }
        }
        $("#node_ids").val(idsArr.join(","));
    }

</script>
<script src="../assets/global/scripts/datatable.js" type="text/javascript"></script>
<script src="../assets/global/plugins/datatables/datatables.min.js" type="text/javascript"></script>
<script src="../assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js" type="text/javascript"></script>
<script src="js/survey.js?v=1"></script>
<script src="../js/map_helper.js"></script>
<script src="../js/OpenMap.js"></script>
<?php
require_once '../include/footer.php';
?>