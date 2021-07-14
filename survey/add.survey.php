<?php
include_once realpath(__DIR__ . '/..').'/include/checksession.php';
include_once realpath(__DIR__ . '/..').'/include/checkpermission.php';
include_once realpath(__DIR__ . '/..').'/include/settings.php';
include_once realpath(__DIR__ . '/..').'/class/Dictionary.php';
include_once realpath(__DIR__ . '/..').'/class/Tree.php';
include_once realpath(__DIR__ . '/..').'/class/Survey.class.php';

$Tree = new Tree("AREA_TREE");
$Survey = new Survey();
$dictionary = new Dictionary($LANGUAGE);
$dictionary->GetAllDictionary();

$UserCoordinates = "[]";

if(isset($_REQUEST["id"]) && $_REQUEST["id"] != ""){
    $area_id = $_REQUEST["id"];
} else {
    $area_id = $USERACCESS;
}

if ($area_id != null) {
    $UserAccessInfo = $Tree -> GetNodeInfo($area_id);
    $UserCoordinates = $UserAccessInfo[0]["COORDINATES"];
}

//opened from update grid
if(isset($_REQUEST["ns"]) && $_REQUEST["ns"] == "1"){
    $ns = 1;
} else {
    $ns = null;
}

$condition = array();
$condition["service_point.area_id"] = $area_id;

$ServicePoints = $Survey->GetServicePoint($condition);
for ($i=0; $i<count($ServicePoints); $i++) {
    $ServicePoints[$i]["sequence"] = $i+1;
}

$operation = "save";
$operationCalssName = "save_service_point";
$point_id = NULL;

$station_id = $feeder_id = 0;

//updating point
if( isset($_REQUEST["point_id"]) && $_REQUEST["point_id"] != NULL ){
    $operation = "edit";
    $operationCalssName = "edit_service_point";
    $point_id = $_REQUEST["point_id"];

    $filter["service_point.point_id"] = $point_id;
    $pointDetails = $Survey->GetServicePoint($filter);
    $lat = $pointDetails[0]["latitude"];
    $lng = $pointDetails[0]["longitude"];
    $area_id = $pointDetails[0]["area_id"];
}

//adding point from update grid
if( isset($_REQUEST["transformer_id"]) && $_REQUEST["transformer_id"] != NULL ){
    $transformer_id = $_REQUEST["transformer_id"];

    $filter["service_point.point_id"] = $transformer_id;
    $pointDetails = $Survey->GetServicePoint($filter);
    $lat = $pointDetails[0]["latitude"];
    $lng = $pointDetails[0]["longitude"];
    $area_id = $pointDetails[0]["area_id"];
    $feeder_id = $pointDetails[0]["feeder_id"];
    $station_id = $pointDetails[0]["station_id"];
}
if( isset($_REQUEST["feeder_id"]) && $_REQUEST["feeder_id"] != NULL ){
    $feeder_id = $_REQUEST["feeder_id"];

    $feeder = $Survey->GetFeederDetails($feeder_id);
    $feeder_id = $feeder[0]["feeder_id"];
    $station_id = $feeder[0]["station_id"];

    if( isset($_REQUEST["transformer_id"]) && $_REQUEST["transformer_id"] != NULL ){
        //take area id from transformer
    } else {
        $area_id = $feeder[0]["area_id"];
    }
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



if (!isset($lat) || !isset($lng)) {
    if (isset($_GET["lat"]) && isset($_GET["lng"])) {
        $lat = $_GET["lat"];
        $lng = $_GET["lng"];
    } else {
        // $lat = "36.191175";
        // $lng = "44.009134";
        $lat = "    "; //36.191175
        $lng = "8.6775"; //44.009134

    }
}
?>


<!-- <link rel="stylesheet" href="https://unpkg.com/leaflet@1.3.1/dist/leaflet.css"
    integrity="sha512-Rksm5RenBEKSKFjgI3a41vrjkw4EVPlJ3+OiI65vTjIdo9brlAacEuKOiQ5OFh7cOI1bkDwLqdLw3Zg0cRJAAQ=="
    crossorigin="" />
<script src="https://unpkg.com/leaflet@1.3.1/dist/leaflet.js" integrity="sha512-/Nsx9X4HebavoBvEBuyp3I7od5tA0UzAxs+j83KgC8PU0kgB4XiK4Lfe4y4cgBtaRJQEIFCW+oC506aPT2L1zw=="
        crossorigin=""></script>
 -->


<div class="row form">
    <form action="code/service.point.add.code.php" role="form" class="form-horizontal form-row-seperated" id="service_point" method="post">
        <input type="hidden" id="type_id" name="type_id">
        <input type="hidden" id="type_label">
        <input type="hidden" id="accuracy" name="accuracy">
        <input type="hidden" id="point_id" name="point_id" value="<?php print $point_id; ?>">
        <input type="hidden" id="area_id" name="area_id" value="<?php print $area_id; ?>">
        <input type="hidden" id="not_from_survey" name="not_from_survey" value="<?php print $ns; ?>">
        <!-- Erbil -->
        <input type="hidden" id="latitude" name="latitude" value="<?php echo $lat;?>">
        <input type="hidden" id="longitude" name="longitude" value="<?php echo $lng;?>">
        <!-- Sul
        <input type="hidden" id="latitude" name="latitude" value="35.55492304">
        <input type="hidden" id="longitude" name="longitude" value="45.42148608">
        -->
        <input type="hidden" id="gps_accuracy" name="gps_accuracy" value="0">
        <div-- class="col-lg-12 wizard">
            <ul class="nav nav-tabs" id="wizard_tabs" style="display:none">
                <li class="active"><a href="#step1" data-toggle="tab">1</a></li>
                <li><a href="#step2" data-toggle="tab">2</a></li>
                <li><a href="#step3" data-toggle="tab">3</a></li>
                <li><a href="#step4" data-toggle="tab">4</a></li>
                <li><a href="#step5" data-toggle="tab">5</a></li>
                <li><a href="#step6" data-toggle="tab">6</a></li>
                <li><a href="#step7" data-toggle="tab">7</a></li>
            </ul>

            <div class="tab-content">

                <div class="tab-pane active" id="step1">

                    <?php if($operation == "edit") { ?>
                        <div class="form-actions center">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="row">
                                        <div class="col-md-offset-0 col-md-12">
                                            <button type="button" class="btn btn-block btn-lg btn-danger survey_delete" data-id="<?php print $point_id; ?>"> <i class="fa fa-trash fa-2x"></i> <?php echo $dictionary->GetValue("delete");?></button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php } ?>

                    <div class="row">
                        <div class="col-lg-12">
                            <a class="dashboard-stat dashboard-stat-v2 blue next1" href="#" data-type="1" data-label="<?php echo $dictionary->GetValue("Electric_Pole");?>">
                                <div class="visual">
                                    <img src="../img/post.png" >
                                </div>
                                <div class="details">
                                    <div class="number">
                                        <span><?php echo $dictionary->GetValue("Electric_Pole");?></span>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <div class="col-lg-12">
                            <a class="dashboard-stat dashboard-stat-v2 red next1" href="#" data-type="2" data-label="<?php echo $dictionary->GetValue("Twisted_Cable");?>">
                                <div class="visual">
                                    <img src="../img/twisted.png" >
                                </div>
                                <div class="details">
                                    <div class="number">
                                        <span><?php echo $dictionary->GetValue("Twisted_Cable");?></span>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <div class="col-lg-12">
                            <a class="dashboard-stat dashboard-stat-v2 yellow-crusta next1" href="#" data-type="3" data-label="<?php echo $dictionary->GetValue("building");?>">
                                <div class="visual">
                                    <img src="../img/building.png" >
                                </div>
                                <div class="details">
                                    <div class="number">
                                        <span><?php echo $dictionary->GetValue("building");?></span>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <div class="col-lg-12">
                            <a class="dashboard-stat dashboard-stat-v2 green next1" href="#" data-type="4" data-label="Transformer">
                                <div class="visual">
                                    <img src="../img/transformer.png" >
                                </div>
                                <div class="details">
                                    <div class="number">
                                        <span><?php echo $dictionary->GetValue("Transformer");?></span>
                                    </div>
                                </div>
                            </a>
                        </div>

                    </div>
                </div>


                <div class="tab-pane fade form" id="step2">
                    <div class="form-body">
                        <div class="form-group" style="margin:10px 0; background-color:#FF91A4">
                            <label class="control-label col-md-3">
                                <img src="../img/1phase.png" height="60px"> <?php echo $dictionary->GetValue("Single_Phase");?>
                            </label>
                            <div class="col-md-9">
                                <input type="number" name="single_phase" id="single_phase" class="form-control" value="0">
                            </div>
                        </div>

                        <div class="form-group" style="margin:10px 0; background-color:#ADD8E6">
                            <label class="control-label col-md-3">
                                <img src="../img/3phase.png" height="60px"> <?php echo $dictionary->GetValue("Three_Phase");?>
                            </label>
                            <div class="col-md-9">
                                <input type="number" name="three_phase" id="three_phase" class="form-control" value="0">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <ul class="list-inline pull-right">
                                    <li><button type="button" class="btn btn-default prev"><< <?php echo $dictionary->GetValue("previous");?></button></li>
                                    <li><button type="button" class="btn btn-primary btn-info-full next"><?php echo $dictionary->GetValue("Next");?> >></button></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>


                <div class="tab-pane fade" id="step3">
                    <h3><?php echo $dictionary->GetValue("Accuracy");?></h3>
                    <a href="#" class="next3" data-accuracy="100" style="text-decoration: none;"><div class="alert alert-success" style="padding: 20px">
                        <h2 style="display:inline-block">100% &nbsp;</h2> <?php echo $dictionary->GetValue("100_description");?> </div></a>
                    <a href="#" class="next3" data-accuracy="50" style="text-decoration: none;"><div class="alert alert-warning" style="padding: 20px">
                        <h2 style="display:inline-block">50% &nbsp;&nbsp;</h2> <?php echo $dictionary->GetValue("50_description");?> </div></a>
                    <a href="#" class="next3" data-accuracy="0" style="text-decoration: none;"><div class="alert alert-danger" style="padding: 20px">
                        <h2 style="display:inline-block">0% &nbsp;&nbsp;&nbsp;</h2> <?php echo $dictionary->GetValue("0_description");?> </div></a>
                    <div class="form-actions right">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-md-offset-3 col-md-9">
                                        <button type="button" class="btn btn-default prev"> << <?php echo $dictionary->GetValue("previous");?></button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>


                <div class="tab-pane fade form" id="step4">
                    <div class="form-body">
                    <div class="row">
                        <?php
                            $options = array("class"=>"form-control");
                            $transformer_types = $Survey->GetTransformerTypes();
                            $transformer_privacy = $Survey->GetTransformerPrivacy();

                            $html->OpenSpan(12);
                            {
                                $html->OpenSpan(2);
                                {
                                    $html->DrawFormField("text", "station_id", $station_id, NULL, array("class"=>"form-control", "readonly"=>true));
                                }
                                $html->CloseSpan();

                                $html->OpenSpan(2);
                                {
                                    $html->DrawFormField("text", "feeder_id", $feeder_id, NULL, array("class"=>"form-control", "readonly"=>true));
                                }
                                $html->CloseSpan();

                                $html->OpenSpan(2);
                                {
                                    $html->DrawFormField("text", "capacity_id", NULL, NULL, $options);
                                }
                                $html->CloseSpan();

                                $html->OpenSpan(2);
                                {
                                    $html->DrawFormField("text", "transformer_number", NULL, null, $options);
                                }
                                $html->CloseSpan();


                                $html->OpenSpan(2);
                                {
                                    $html->DrawFormField("select", "transformer_type_id", NULL, $transformer_types, array_merge($options, ["optional"=>true, "dictionary"=>true]));
                                }
                                $html->CloseSpan();


                                $html->OpenSpan(2);
                                {
                                    $html->DrawFormField("select", "transformer_privacy_id", NULL, $transformer_privacy, array_merge($options, ["optional"=>true, "dictionary"=>true]));
                                }
                                $html->CloseSpan();
                            }
                            $html->CloseSpan();
                        ?>
                        </div>
                    </div>
                    <div class="form-actions right">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-md-offset-3 col-md-9">
                                        <button type="button" class="btn btn-default prev4"> << <?php echo $dictionary->GetValue("previous");?></button>
                                        <button type="button" class="btn btn-primary btn-info-full next4"><?php echo $dictionary->GetValue("Next");?> >></button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="tab-pane fade" id="step5">
                    <div class="form-actions right">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-md-offset-3 col-md-9">
                                        <button type="button" class="btn btn-default prev5"><< <?php echo $dictionary->GetValue("previous");?></button>
                                        <button type="button" class="btn btn-primary btn-info-full next5"><?php echo $dictionary->GetValue("Next");?> >></button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>


                <div class="tab-pane fade" id="step6">
                    <div class="form-body">
                        <h3 class="form-section"><?php echo $dictionary->GetValue("Confirmation");?></h3>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label col-md-3"><?php echo $dictionary->GetValue("Type");?>:</label>
                                    <div class="col-md-9">
                                        <p class="form-control-static"> <b id="confirm_type"></b> </p>
                                    </div>
                                </div>
                            </div>
                            <!--/span-->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label col-md-3"><?php echo $dictionary->GetValue("Single_Phase_Count");?>:</label>
                                    <div class="col-md-9">
                                        <p class="form-control-static">
                                            <b id="confirm_single_phase"></b>
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <!--/span-->
                        </div>
                        <!--/row-->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label col-md-3"><?php echo $dictionary->GetValue("Three_Phase_Count");?>:</label>
                                    <div class="col-md-9">
                                        <p class="form-control-static"> <b id="confirm_three_phase"></b> </p>
                                    </div>
                                </div>
                            </div>
                            <!--/span-->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label col-md-3"><?php echo $dictionary->GetValue("Accurecy");?>:</label>
                                    <div class="col-md-9">
                                        <p class="form-control-static"> <b id="confirm_accuracy"></b> </p>
                                    </div>
                                </div>
                            </div>
                            <!--/span-->
                        </div>
                        <!--/row-->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label col-md-3"><?php echo $dictionary->GetValue("latitude");?>:</label>
                                    <div class="col-md-9">
                                        <p class="form-control-static"> <b id="confirm_latitude"></b> </p>
                                    </div>
                                </div>
                            </div>
                            <!--/span-->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label col-md-3"><?php echo $dictionary->GetValue("longitude");?>:</label>
                                    <div class="col-md-9">
                                        <p class="form-control-static"> <b id="confirm_longitude"></b> </p>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label col-md-3"><?php echo $dictionary->GetValue("x");?>:</label>
                                    <div class="col-md-9">
                                        <p class="form-control-static"> <b id="confirm_x"></b> </p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label col-md-3"><?php echo $dictionary->GetValue("y");?>:</label>
                                    <div class="col-md-9">
                                        <p class="form-control-static"> <b id="confirm_y"></b> </p>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label col-md-3"><?php echo $dictionary->GetValue("gps_accuracy");?>:</label>
                                    <div class="col-md-9">
                                        <p class="form-control-static"> <b id="confirm_gps_accuracy"></b> </p>
                                    </div>
                                </div>
                            </div>

                            <!--/span-->
                        </div>
                        <!--/row-->
                    </div>
                    <div class="form-actions right">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-md-offset-3 col-md-9">
                                        <button type="button" class="btn btn-default prev6"> << <?php echo $dictionary->GetValue("previous");?></button>
                                        <button type="button" class="btn btn-danger save_point <?php //print $operationCalssName; ?>"> <i class="fa fa-check"></i> <?php echo $dictionary->GetValue($operation);?></button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>


                <div class="tab-pane fade" id="step7">
                    <div class="form-body">
                        <h3 class="form-section"><?php echo $dictionary->GetValue("Confirmation");?></h3>
                        <div class="row">

                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="control-label col-md-3"><?php echo $dictionary->GetValue("transformer_number");?>:</label>
                                    <div class="col-md-6">
                                        <p class="form-control-static"> <b id="confirm_transformer"></b> </p>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="control-label col-md-3"><?php echo $dictionary->GetValue("transformer_type");?>:</label>
                                    <div class="col-md-6">
                                        <p class="form-control-static"> <b id="confirm_transformer_type"></b> </p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="control-label col-md-3"><?php echo $dictionary->GetValue("transformer_privacy");?>:</label>
                                    <div class="col-md-6">
                                        <p class="form-control-static"> <b id="confirm_transformer_privacy"></b> </p>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="control-label col-md-3"><?php echo $dictionary->GetValue("latitude");?>:</label>
                                    <div class="col-md-9">
                                        <p class="form-control-static"> <b id="confirm_latitude_2"></b> </p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="control-label col-md-3"><?php echo $dictionary->GetValue("longitude");?>:</label>
                                    <div class="col-md-9">
                                        <p class="form-control-static"> <b id="confirm_longitude_2"></b> </p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="control-label col-md-3"><?php echo $dictionary->GetValue("x");?>:</label>
                                    <div class="col-md-9">
                                        <p class="form-control-static"> <b id="confirm_x"></b> </p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="control-label col-md-3"><?php echo $dictionary->GetValue("y");?>:</label>
                                    <div class="col-md-9">
                                        <p class="form-control-static"> <b id="confirm_y"></b> </p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="control-label col-md-3"><?php echo $dictionary->GetValue("gps_accuracy");?>:</label>
                                    <div class="col-md-9">
                                        <p class="form-control-static"> <b id="confirm_gps_accuracy_2"></b> </p>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <!--/row-->
                    </div>
                    <div class="form-actions right">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-md-offset-3 col-md-9">
                                        <button type="button" class="btn btn-default prev7"> << <?php echo $dictionary->GetValue("previous");?></button>
                                        <button type="button" class="btn btn-danger save_point <?php //print $operationCalssName; ?>"> <i class="fa fa-check"></i> <?php echo $dictionary->GetValue($operation);?></button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <!-- <button type="button" class="btn btn-lg btn-primary refresh_location" style="position: absolute; right: 15; z-index:1000; margin: 12px;"> <i class="fa fa-crosshairs"></i></button> -->
                        <div class="row" style="margin: 5px; padding: 5px; text-align: center; background: #bcd5ed;">
                            <div class="col-lg-3"><img src="../img/green.png"> <?php print $dictionary->GetValue("transformer"); ?></div>
                            <div class="col-lg-3"><img src="../img/blue.png"> <?php print $dictionary->GetValue("Electric_Pole"); ?></div>
                            <div class="col-lg-3"><img src="../img/red.png"> <?php print $dictionary->GetValue("Twisted_Cable"); ?></div>
                            <div class="col-lg-3"><img src="../img/yellow.png"> <?php print $dictionary->GetValue("building"); ?></div>
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


<script>
    var map;
    $(function() {

        var mapHeight;
        var androidInterval;
        var accuracyCircle;
        //"36.191175", "44.009134" Erbil
        //"35.55492304", "45.42148608" Sul

        var iconSize = 12;
        var lat = "<?php echo $lat;?>";
        var lng = "<?php echo $lng;?>";

        //Get first load page height
        mapHeight = $( window ).height() - 150;

        //hide map by default
        $("#map").css("height", mapHeight);


        map = new Map("map", lat, lng , true);
        map.zoom(16);

        setTimeout(function() {
            map.map.invalidateSize();
        }, 1000);


        map.addPolygon("<?php print $UserCoordinates; ?>", null,"red", "p1");

        var layerGroup = map.addGroup();
        //map.addMarker("36.2346", "43.9479", map.addGroup(), "red", "test", "2");

        <?php
        if( $ServicePoints )
        {
            $j = count( $ServicePoints );
            /*
            if( $ServicePoints[0]["area_id"] == 755 ) {
                $j = $j - 4000;
            }

            for ( $i=500; $i < $j-400; $i++ ){
            for ( $i=0; $i < $j; $i++ ){
            */

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
                $color = $ServicePoints[$i]['COLOR'];
                $sequence = $ServicePoints[$i]['sequence'];

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

                //$PopUpString .= '<a href="add_survey.php?point_id='.$point_id.'">'.$dictionary->GetValue("edit").'<i class="fa fa-pencil"></i></a>';

                if($ServicePoints[$i]["latitude"] != null && $ServicePoints[$i]["longitude"] != null){
                ?>
                    color = GetStatusColor(<?php print $point_type_id; ?>)
                    map.addMarker(<?php print $ServicePoints[$i]["latitude"]; ?>, <?php print $ServicePoints[$i]["longitude"]; ?>, layerGroup, color, '<?php print $PopUpString; ?>', "<?php print $dictionary->GetValue("area_id")." : ".$area_name ?>");
                <?php
                }
            }
        }
        ?>

    cos = ",";
    if ("undefined" !== typeof Android) {
        getCoordinates();
        androidInterval = setInterval(function(){
            cos = Android.askCoordinates();
            //cos = "36.8888,38.8888";
            getCoordinates();
        }, 10000);
    }

    // $(".refresh_location").on("click", function(){
    //     getCoordinates();
    // });

    //add new icon to map
    map.addCustomControl("../img/current.png", "Current Location",
        function(e){
            getCoordinates();
        });

    //add new icon to map
    map.addCustomControl("../img/resize.png", "Change Icon Size",
        function(e){
            //onclick
            if (iconSize == 12) {
                $(".leaflet-marker-icon").addClass("small");
                iconSize = 4;
            } else {
                $(".leaflet-marker-icon").removeClass("small");
                iconSize = 12;
            }
        });


    //disable clicking save multiple times
    $("body").off("click", "button.save_point");
    $("body").on("click", "button.save_point", function(){
        $button = $(this);
        $button.prop('disabled', true);
        // $("#service_point").submit();
        setTimeout(function(){
            $button.removeAttr('disabled');
        }, 5000);

        SavePoint()
    });
});


function getCoordinates(){
    cos = Android.askCoordinates();//
    //cos = "36.8888,38.8888";
    if(cos.trim() != "," && cos.trim() != "null,null" && cos.trim() != "(,)"){
        //$("#latlong").val("");
        setCoordinates(cos);
        //SaveLocation(cos);
    }
}

function setCoordinates(cos){
    setTimeout(function () {
        latlong = cos.split(",");
        lat = latlong[0];
        long = latlong[1];
        accuracy = latlong[2];

        //if no location, get it from session
        // if(cos.trim() == "," || cos.trim() == "null,null" || cos.trim() == "(,)"){
        //     cos = '<?php //echo $_SESSION["location"];?>';
        //     if(cos.indexOf(",")> 0){
        //         latlong = cos.split(",");
        //         lat = latlong[0];
        //         long = latlong[1];
        //     }
        // }
        //$("#latlong").val(cos);
        $("#latitude").val(lat);
        $("#longitude").val(long);
        $("#gps_accuracy").val(accuracy);

        $("#confirm_latitude").text($("#latitude").val());
        $("#confirm_longitude").text($("#longitude").val());
        $("#confirm_gps_accuracy").text($("#gps_accuracy").val());

        var request = $.ajax({
            url: "api/latlong2utm.php?lat="+lat+"&long="+long,
            type: 'POST',
            processData: false,
            contentType: false,
            data: {}
        });
        request.done(function(msg) {
            xy = msg.split(",")
            x = xy[0];
            y = xy[1];
            $("#confirm_x").text(x);
            $("#confirm_y").text(y);
        });


        // if($("#latlong").val().length > 0){
        //     $("#latlong").css("background-color", "");
        // }else{
        //     $("#latlong").css("background-color", "#e8554e");
        // }

        map.center(lat, long);

        if (accuracyCircle != undefined) {
            map.map.removeLayer(accuracyCircle);
        }
        accuracyCircle = map.addCircle([lat, long], accuracy, 'red');
    }, 1500);
}
</script>
<script src="js/survey.js?v=12"></script>
