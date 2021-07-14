<?php
include_once realpath(__DIR__ . '/../..').'/include/checksession.php';
include_once realpath(__DIR__ . '/../..').'/include/checkpermission.php';
require_once realpath(__DIR__ . '/../..').'/class/HtmlHelper.php';
require_once realpath(__DIR__ . '/../..').'/class/Installation.class.php';

$html = new HTML ( $LANGUAGE );
$dictionary = new Dictionary ( $LANGUAGE );
$dictionary->GetAllDictionary ();
$options = array("class"=>"form-control");
$Installation = new Installation( );

$area_id = $station_id = $feeder_id = $transformer_id = $condition = $filter = NULL;

$FeederArr = $TransformerArr =  array();

$StationArr = $Installation->GetStationByArea();
$AreaArr = $Installation->GetInstallationArea();

if(isset($_GET["area_id"]) && $_GET["area_id"] != ""){
    $area_id = $_GET["area_id"];
    // $condition["t.area_id"] = $condition1["area_id"]  = $area_id;
    // $filter .= "&t.area_id=".$area_id;
    $StationArr = $Installation->GetStationByArea($area_id);
}

if(isset($_GET["station_id"]) && $_GET["station_id"] != ""){
    $station_id = $_GET["station_id"];
    $condition["station_id"] = $station_id;
    $filter .= "&station_id=".$station_id;
    $FeederArr = $Installation->GetFeederByStation($station_id);
}

if(isset($_GET["feeder_id"]) && $_GET["feeder_id"] != ""){
    $feeder_id = $_GET["feeder_id"];
    $condition["feeder_id"] = $feeder_id;
    $filter .= "&feeder_id=".$feeder_id;

    $condition["service_point.point_type_id"] = 4;
    $condition["ponit_count"] = array("Operator"=>">","Value"=>1, "Type"=>"int");
    $TransformerArr = $Installation->GetTransformerArr($condition);
}

if( isset($_REQUEST["transformer_id"]) && $_REQUEST["transformer_id"] != NULL ){
    $transformer_id = $_REQUEST["transformer_id"];
    $filter .= "&transformer_id=".$transformer_id;
}

$cols = array();
$cols[] = array("column"=>"transformer_number");
$cols[] = array("column"=>"Total_Enclosure");
$cols[] = array("column"=>"Installed_Enclosure");
$cols[] = array("column"=>"Total_Meter");
$cols[] = array("column"=>"Installed_Meter");

$tableOptions = array();
$tableOptions["tableClass"]= "table-hover table-bordered table-condensed table-striped";
$tableOptions["key"]=array("id"=>"transformer_id");

$html->OpenDiv("row");
{
    $html->OpenSpan(12);
    {
        $html->OpenWidget ("enclosure_installation_summary_list", NULL, array('collapse' => true, 'fullscreen'=>true,'table-actions'=>"example1"));
        {
            ?>
            <form action="code/enclosure.installation.summary.list.php" role="form" class="" id="enclosure_installation_summary_list" method="post">
                <div class="col-lg-12 wizard">
                    <div class="tab-content">
                        <?php
                        $html->OpenDiv("row");
                        {
                            $html->OpenSpan(3);
                            {
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
                        }
                        $html->CloseDiv();
                        ?>
                    </div>
                </div>
            </form>
            <?php
            $html->Datatable("example", "api/list.enclosure.installation.summary.php?".$filter, $cols, $tableOptions);
        }
        $html->CloseWidget();
    }
    $html->CloseSpan();
}
$html->CloseDiv();
?>
<script>
$(function() {
    $('#area_id').on('change', function () {
        area_id = $(this).val();
        //FillFeeder(area_id);
        window.location.href = "enclosure_installation_summary_list.php?&area_id=" + area_id;
    });

    $('#station_id').on('change', function () {
        area_id = $('#area_id').val();
        station_id = $(this).val();
        //FillFeeder(station_id);
        window.location.href = "enclosure_installation_summary_list.php?&area_id=" + area_id + "&station_id=" + station_id;
    });

    $('#feeder_id').on('change', function () {
        area_id = $('#area_id').val();
        feeder_id = $(this).val();
        station_id = $("#station_id").val();
        window.location.href = "enclosure_installation_summary_list.php?&area_id=" + area_id+"&station_id=" + station_id + "&feeder_id=" + feeder_id;
    });

    $('#transformer_id').on('change', function() {
        area_id = $('#area_id').val();
        transformer_id = this.value;
        station_id = $("#station_id").val();
        feeder_id = $("#feeder_id").val();
        window.location.href = "enclosure_installation_summary_list.php?transformer_id=" + transformer_id + "&area_id=" + area_id + "&station_id=" + station_id + "&feeder_id=" + feeder_id;
    });
});
/*
function FillFeeder(station_id){
    if( station_id > 0){
        FillListOptions("ui/get.feeder.php?station_id="+station_id, "feeder_id", true);
    } else {
        FillListOptions("i/get.feeder.php", "feeder_id", true);
    }
}
*/
</script>
