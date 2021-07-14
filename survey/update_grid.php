<?php
// error_reporting(E_ALL);
// ini_set('display_errors', 1);
include_once realpath(__DIR__ . '/..').'/include/checksession.php';
include_once realpath(__DIR__ . '/..').'/include/settings.php';
include_once realpath(__DIR__ . '/..').'/include/header.php';
include_once realpath(__DIR__ . '/..').'/class/Dictionary.php';
include_once realpath(__DIR__ . '/..').'/class/Installation.class.php';
include_once realpath(__DIR__ . '/..').'/class/Survey.class.php';
require_once realpath(__DIR__ . '/..') . '/class/Tree.php';
include_once realpath(__DIR__ . '/..').'/include/checkpermission.php';

$survey = new Survey();
$Installation = new Installation();
$dictionary = new Dictionary($LANGUAGE);
$area_tree = new Tree("AREA_TREE");

$dictionary->GetAllDictionary();

$station_id = $feeder_id = $transformer_id = NULL;
$condition = $condition1 = $ServicePoints = $TransformerArr = array();

$AreaArr = $Installation->GetInstallationArea();
// $StationArr = $Installation->GetStationByArea();

if(isset($_REQUEST["area_id"])&&$_REQUEST["area_id"]!=NULL){
    $area_id = $_REQUEST["area_id"];
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

if(isset($_REQUEST["area_id"]) && $_REQUEST["area_id"] != ""){
    $area_id = $_REQUEST["area_id"];
    $StationArr = $Installation->GetStationByArea($area_id);
}

if(isset($_REQUEST["station_id"]) && $_REQUEST["station_id"] != ""){
    $station_id = $_REQUEST["station_id"];
    $FeederArr = $Installation->GetFeederByStation($station_id);

}

if(isset($_REQUEST["feeder_id"]) && $_REQUEST["feeder_id"] != ""){
    $feeder_id = $_REQUEST["feeder_id"];
    $condition["feeder_id"] = $feeder_id;
    $TransformerArr = $survey->GetTransformers($area_id, $station_id, $feeder_id);
}


if( isset($_REQUEST["transformer_id"]) && $_REQUEST["transformer_id"] != NULL ){
    $transformer_id = $_REQUEST["transformer_id"];
    $condition1["transformer_id"] = $transformer_id;
}

?>

<link rel="stylesheet" href="../osm/leaflet.css" />
<script src="../osm/leaflet.js" ></script>
<script src="../js/OpenMap.js?v=5"></script>
<link rel="stylesheet" href="../osm/Control.FullScreen.css" />
<script src="../osm/Control.FullScreen.js"></script>


<style>
    .bordered {
        border: 1px solid #5cd1db;
        margin-bottom: 20px;
        padding: 15px;
    }
    .map{
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
<?php
$html->OpenDiv("row");
{
    $html->OpenSpan(12);
    {
        $html->OpenWidget ("", $wactions, array('collapse' => true, 'fullscreen'=>true,'table-actions'=>"example1"));
        {
            $html->OpenForm ( null, "form3" );
            {
                $html->OpenDiv("row");
                {
                    $html->OpenSpan(3);
                    {
                        // $dirTree = array (
                        //     array ( "type"=>"hidden", "name"=>"area_id", "value"=>$area_id, "list"=>NULL, "options"=>NULL ),
                        //     array ( "type"=>"text", "name"=>"area_text", "value"=>$area_text, "list"=>NULL, "options"=>array("class"=>"form-control open_area_tree", "tree"=>"", "readonly"=>"readonly") )
                        // );
                        // $html->DrawGenericFormField ( "area_id", $dirTree, null, $options);
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

                    // $html->OpenSpan(1);
                    // {
                    //     ? >
                    //     <div class="form-group">
                    //         <label>&nbsp;</label>
                    //         <div class="input-group">
                    //             <span class="input-group-btn">
                    //                 <button class="btn green filter_service_point_summary" type="submit"><?php echo $dictionary->GetValue("filter");? ></button>
                    //             </span>
                    //         </div>
                    //     </div>
                    //     <?php
                    // }
                    // $html->CloseSpan();
                }
                $html->CloseDiv();
            }
            $html->CloseForm();
        }
        $html->CloseWidget();
    }
    $html->CloseSpan();
}
$html->CloseDiv();

$html->OpenDiv("row");
{
    $html->OpenSpan(12);
    {
        echo '<section id="station"></section>';
        echo '<section id="feeder"></section>';
        echo '<section id="transformer"></section>';
        echo '<section id="themap"></section>';
    }
    $html->CloseSpan();
}
$html->CloseDiv();

?>

<script>
$(function() {

    $("#area_id").on("change",function() {
        area_id = $(this).val();
        SelectArea(area_id);
    });
    $('#station_id').on('change', function () {
        station_id = $(this).val();
        SelectStation(station_id);
    });
    $('#feeder_id').on('change', function () {
        feeder_id = $(this).val();
        station_id = $("#station_id").val();
        area_id = $('#area_id').val();

        ShowDetails(station_id, feeder_id, "");
        FillTransformer(area_id, station_id, feeder_id);
    });
    $('#transformer_id').on('change', function () {
        transformer_id = $(this).val();
        feeder_id = $('#feeder_id').val();
        station_id = $("#station_id").val();
        area_id = $('#area_id').val();

        ShowDetails(station_id, feeder_id, transformer_id);
    });


    $('section').on('click', '.add_station', function() {
        area_id = $('#area_id').val();
        OpenModal('ui/update.station.php?area_id='+area_id, null, 'modal-md');
    });
    $('section').on('click', '.change_station', function() {
        station_id = $(this).data('id');
        area_id = $('#area_id').val();
        OpenModal('ui/update.station.php?id='+station_id+'&area_id='+area_id, null, 'modal-md');
    });
    $('section').on('click', '.delete_station', function() {
        station_id = $(this).data('id');
        DeleteStation(station_id);
    });

    $('section').on('click', '.add_feeder', function() {
        station_id = $("#station_id").val();
        area_id = $('#area_id').val();
        OpenModal('ui/update.feeder.php?area_id='+area_id+'&station_id='+station_id, null, 'modal-md');
    });
    $('section').on('click', '.change_feeder', function() {
        feeder_id = $(this).data('id');
        station_id = $("#station_id").val();
        area_id = $('#area_id').val();
        OpenModal('ui/update.feeder.php?id='+feeder_id+'&area_id='+area_id+'&station_id='+station_id, null, 'modal-md');
    });
    $('section').on('click', '.delete_feeder', function() {
        feeder_id = $(this).data('id');
        DeleteFeeder(feeder_id);
    });

    $('section').on('click', '.change_transformer', function() {
        transformer_id = $(this).data('id');
        OpenModal('ui/update.transformer.php?id='+transformer_id, null, 'modal-md');
    });


    $('body').on('click', '.add_transformer', function () {
        latlangStr = $('p#gps').html();
        if (latlangStr != undefined) {
            latlng = latlangStr.split(",");
        } else {
            latlng = ["", ""];
        }
        OpenAddPoint(latlng[0], latlng[1])
    });



    $('body').on('change', '#form5 #station_id', function () {
        station_id = $(this).val();
        FillFeeder(area_id, station_id, "form5 #feeder_id");
    });


    $("body").on('click', '.save_station', function() {
        SaveStation();
    });

    $("body").on('click', '.save_feeder', function() {
        SaveFeeder();
    });

    $("body").on('click', '.save_transformer', function() {
        SaveTransformer();
    });

});

function SelectArea(area_id)
{
    ShowDetails("", "", "");
    FillStation(area_id);
    FillFeeder(area_id, "", "feeder_id");
    FillTransformer(area_id, "" , "");
}

function SelectStation(station_id)
{
    area_id = $('#area_id').val();
    ShowDetails(station_id, "", "");
    FillFeeder(area_id, station_id, "feeder_id");
    FillTransformer(area_id, station_id, "");
}

function ShowDetails(station_id, feeder_id, transformer_id)
{
    GetStation(station_id);
    GetFeeder(feeder_id);
    GetTransformer(transformer_id);

    GetMap(feeder_id, transformer_id);
}

function GetStation(station_id)
{
    if (station_id!= "") {
        $("section#station").load('ui/station.details.php?id='+station_id);
    } else {
        $("section#station").html('');
    }
}

function GetFeeder(feeder_id)
{
    if (feeder_id!= "") {
        $("section#feeder").load('ui/feeder.details.php?id='+feeder_id);
    } else {
        $("section#feeder").html('');
    }
}

function GetTransformer(transformer_id)
{
    if (transformer_id!= "") {
        $("section#transformer").load('ui/transformer.details.php?id='+transformer_id, function( response, status, xhr ) {
            // if ( response != "" ) {
            //     var gps = $("#gps").html();
            //     if (gps != "" && gps != ",") {
            //         LoadMap(gps);
            //     }
            // }
        });
    } else {
        $("section#transformer").html('');
    }
}

function GetMap(feeder_id, transformer_id)
{
    // console.log(feeder)
    // console.log(transformer_id)
    if (transformer_id != "") {
        $("section#themap").load('ui/update.grid.map.php?transformer_id='+transformer_id);
    } else if (feeder_id != "") {
        $("section#themap").load('ui/update.grid.map.php?feeder_id='+feeder_id);
    } else {
        $("section#themap").html('');
    }
}

function FillStation(area_id){
    FillListOptions("ui/get.station.php?area_id="+area_id, "station_id", true);
}

function FillFeeder(area_id, station_id, selector){
    FillListOptions("ui/get.feeder.php?area_id="+area_id+"&station_id="+station_id, selector, true);
}

function FillTransformer(area_id, station_id, feeder_id){
    FillListOptions("ui/get.transformers.php?area_id="+area_id+"&station_id="+station_id+"&feeder_id="+feeder_id, "transformer_id", true);
}

// function LoadMap(latlng)
// {
//     latlngarr = latlng.split(",");
//     var map = new Map("map", latlngarr[0], latlngarr[1], false, false);
//     map.zoom(16);
//     map.addMarker( latlngarr[0], latlngarr[1], 0, {img: "transformer-icon", "size": 18 }, null, null, null, false);
// }

function OpenAddPoint(lat, lng)
{
    transformer_id = $("#form3 #transformer_id").val();
    feeder_id = $("#form3 #feeder_id").val();
    OpenModal("add.survey.php?transformer_id="+transformer_id+"&feeder_id="+feeder_id+"&lat="+lat+"&lng="+lng+"&ns=1", null, "modal-full");
}


function SaveStation()
{
    area_id = $("#area_id").val();

    var formData = new FormData($("#form4")[0]);
    var url = "code/station.update.code.php";
    var request = $.ajax({
        url: url,
        type: 'POST',
        processData: false,
        contentType: false,
        data: formData
    });
    request.done(function(msg) {
        //show message anyway
        if (GetLocalStatus(true, msg)) {
            //reload
            SelectArea(area_id);
            $("#myModal").modal('hide');
        } else {
            //error, stay here
        }
        //HideLoader();
    });
    request.fail(function(jqXHR, textStatus) {
        HideLoader();
        ShowToastr("error", jqXHR.statusText);
    });
}

function SaveFeeder()
{
    station_id = $("#station_id").val();

    var formData = new FormData($("#form4")[0]);
    var url = "code/feeder.update.code.php";
    var request = $.ajax({
        url: url,
        type: 'POST',
        processData: false,
        contentType: false,
        data: formData
    });
    request.done(function(msg) {
        //show message anyway
        if (GetLocalStatus(true, msg)) {
            //reload
            SelectStation(station_id);
            $("#myModal").modal('hide');
        } else {
            //error, stay here
        }
        //HideLoader();
    });
    request.fail(function(jqXHR, textStatus) {
        HideLoader();
        ShowToastr("error", jqXHR.statusText);
    });
}

function SaveTransformer()
{
    station_id = $("#station_id").val();

    var formData = new FormData($("#form5")[0]);
    var url = "code/transformer.update.code.php";
    var request = $.ajax({
        url: url,
        type: 'POST',
        processData: false,
        contentType: false,
        data: formData
    });
    request.done(function(msg) {
        //show message anyway
        if (GetLocalStatus(true, msg)) {
            //reload
            GetTransformer(transformer_id)
            $('#myModal').modal('hide');
        } else {
            //error, stay here
        }
        //HideLoader();
    });

    request.fail(function(jqXHR, textStatus) {
        HideLoader();
        ShowToastr("error", jqXHR.statusText);
    });
}

function SavePoint()
{
    transformer_id = $("#form3 #transformer_id").val();

    var formData = new FormData($("form#service_point")[0]);
    formData.append('update_grid', '1');

    var url = "code/service.point.add.code.php";
    var request = $.ajax({
        url: url,
        type: 'POST',
        processData: false,
        contentType: false,
        data: formData
    });
    request.done(function(msg) {
        GetTransformer(transformer_id)
        $('#myModal').modal('hide');
    });
    request.fail(function(jqXHR, textStatus) {
        HideLoader();
        ShowToastr("error", jqXHR.statusText);
    });
}

function SaveLine(line) {
    feeder_id = $("#feeder_id").val();
    transformer_id = $("#transformer_id").val();

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
                //location.reload();
                GetMap(feeder_id, transformer_id);
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
    feeder_id = $("#feeder_id").val();
    transformer_id = $("#form3 #transformer_id").val();

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
                GetMap(feeder_id, transformer_id);
                // GetTransformer(transformer_id)
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


function DeleteStation(id)
{
    area_id = $("#area_id").val();

    OpenConfirmModal(["{Are you sure you want to delete this station? All sub-feeders need to be deleted first!}"], function() {
        var request = $.ajax({
            url: "code/station.delete.code.php?id=" + id,
            type: "GET",
            processData: false,
            contentType: false
        });
        request.done(function(msg) {
            if (GetLocalStatus(true, msg)) {
                //success
                SelectArea(area_id);
            } else {
                //something wrong happened
                SetLocalStatus(msg);
                // GetLocalStatus();
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

function DeleteFeeder(id)
{
    station_id = $("#station_id").val();

    OpenConfirmModal(["{Are you sure you want to delete this feeder? All linked transformers need to be deleted first!}"], function() {
        var request = $.ajax({
            url: "code/feeder.delete.code.php?id=" + id,
            type: "GET",
            processData: false,
            contentType: false
        });
        request.done(function(msg) {
            if (GetLocalStatus(true, msg)) {
                //success
                SelectStation(station_id);
            } else {
                //something wrong happened
                SetLocalStatus(msg);
                //GetLocalStatus();
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
</script>
<script src="js/survey.js?v=5"></script>
<script src="../js/map_helper.js"></script>
<script src="../assets/global/scripts/datatable.js" type="text/javascript"></script>
<script src="../assets/global/plugins/datatables/datatables.min.js" type="text/javascript"></script>
<script src="../assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js" type="text/javascript"></script>
<?php
require_once '../include/footer.php';
?>