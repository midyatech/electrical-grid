<?php
include_once realpath(__DIR__ . '/../..').'/include/checksession.php';
include_once realpath(__DIR__ . '/../..').'/include/checkpermission.php';
require_once realpath(__DIR__ . '/../..').'/class/HtmlHelper.php';
require_once realpath(__DIR__ . '/../..').'/class/Installation.class.php';
require_once realpath(__DIR__ . '/../..') . '/class/Tree.php';

$html = new HTML ( $LANGUAGE );
$dictionary = new Dictionary ( $LANGUAGE );
$dictionary->GetAllDictionary ();
$options = array("class"=>"form-control");
$Installation = new Installation( );
$area_tree = new Tree("AREA_TREE");

$station_id = $feeder_id = $transformer_id = $from_date = $to_date = $condition = $filter = $enclosure_sn = $Plant_No = $Serial_No = NULL;

$FeederArr = $TransformerArr =  array();

$StationArr = $Installation->GetStationByArea();
$AreaArr = $Installation->GetInstallationArea();

if(isset($_REQUEST["area_id"]) && $_REQUEST["area_id"] != ""){
    $area_id = $_REQUEST["area_id"];
    // $condition["t.area_id"] = $condition1["area_id"]  = $area_id;
    // $filter .= "&t.area_id=".$area_id;
    $filter .= "&area_path=".$area_tree->GetNodePath($area_id);
    $StationArr = $Installation->GetStationByArea($area_id);
} else {
    $area_id = $USERACCESS;
    $filter .= "&area_path=".$area_tree->GetNodePath($area_id);
}

// if( isset($_REQUEST["area"] ) && $_REQUEST["area"] != NULL ) {
//     $area_id = $_REQUEST["area"];
//     $filter .= "&area_path=".$area_tree->GetNodePath($area_id);
// } else {
//     $area_id = $USERACCESS;
//     $filter .= "&area_path=".$area_tree->GetNodePath($area_id);
// }
// $area_text = $area_tree->GetPathString($area_id);

if(isset($_REQUEST["station_id"]) && $_REQUEST["station_id"] != ""){
    $station_id = $_REQUEST["station_id"];
    $condition["station_id"] = $station_id;
    $filter .= "&station_id=".$station_id;
    $FeederArr = $Installation->GetFeederByStation($station_id);
}

if(isset($_REQUEST["feeder_id"]) && $_REQUEST["feeder_id"] != ""){
    $feeder_id = $_REQUEST["feeder_id"];
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

if( isset($_REQUEST["enclosure_sn"]) && $_REQUEST["enclosure_sn"] != NULL ){
    $enclosure_sn = $_REQUEST["enclosure_sn"];
    $filter .= "&enclosure_sn=".$enclosure_sn;
}

if( isset($_REQUEST["Plant_No"]) && $_REQUEST["Plant_No"] != NULL ){
    $Plant_No = $_REQUEST["Plant_No"];
    $filter .= "&Plant_No=".$Plant_No;
}

if( isset($_REQUEST["Serial_No"]) && $_REQUEST["Serial_No"] != NULL ){
    $Serial_No = $_REQUEST["Serial_No"];
    $filter .= "&Serial_No=".$Serial_No;
}

if( isset($_REQUEST["from_date"]) && $_REQUEST["from_date"] != NULL ){
    $from_date = $_REQUEST["from_date"];
    $filter .= "&from_date=".$from_date;
} else{
    $from_date = date("Y-m-01");
    $filter .= "&from_date=".$from_date;
}

if( isset($_REQUEST["to_date"]) && $_REQUEST["to_date"] != NULL ){
    $to_date = $_REQUEST["to_date"];
    $filter .= "&to_date=".$to_date;
} else {
    $to_date = date("Y-m-d");
    $filter .= "&to_date=".$to_date;
}

$cols = array();
//$cols[] = array("column"=>"installed_time");
$cols[] = array("column"=>"Plant_No", "title"=>"Plant No.");
$cols[] = array("column"=>"Serial_No", "title"=>"Serial No.");
$cols[] = array("column"=>"Model");
// $cols[] = array("column"=>"coordinates", "title"=>"Coordinates");
$cols[] = array("column"=>"latitude");
$cols[] = array("column"=>"longitude");
$cols[] = array("column"=>"governerate", "title"=>"Grand Parent Zone");
$cols[] = array("column"=>"subdistrict", "title"=>"Parent Zone");
$cols[] = array("column"=>"NODE_NAME", "title"=>"Zone");
$cols[] = array("column"=>"station");
$cols[] = array("column"=>"feeder");
$cols[] = array("column"=>"point_id");
$cols[] = array("column"=>"enclosure_sn");

$cols[] = array("column"=>"ACTION_COL", "style"=>"width:150px","action-type"=>"ajax",
                    "buttons"=> array(
                        array("action-class"=>"meter_trace", "button-icon"=>"fa fa-list", "title"=>$dictionary->GetValue("Trace"), "type"=>"button", "url"=>"href=javascript:;"),
                    )
                );


$tableOptions = array();
$tableOptions["tableClass"]= "table-hover table-bordered table-condensed table-striped";
$tableOptions["key"]=array("id"=>"meter_id");
$tableOptions["order"]=array(0=>"desc");

$html->OpenDiv("row");
{
    $html->OpenSpan(12);
    {
        $actions = array (
            array("type"=>"literal", "value"=>'<a href="./ui/meter.installation.list.export.php?'.$filter.'" class="btn green" target="blank"><i class="fa fa-file-excel-o"></i></a>')
        );
        $html->OpenWidget ("meter_installation_list", $actions, array('collapse' => true, 'fullscreen'=>true,'table-actions'=>"example1"));
        {
            ?>
            <form action="" role="form" class="" id="meter_installation_list" method="post">
                <div class="col-lg-12 wizard">
                    <div class="tab-content">
                        <?php
                        $html->OpenDiv("row");
                        {
                            // $html->OpenSpan(4);
                            // {
                            //     $dirTree = array (
                            //         array ( "type"=>"hidden", "name"=>"area", "value"=>$area_id, "list"=>NULL, "options"=>NULL ),
                            //         array ( "type"=>"text", "name"=>"area_text", "value"=>$area_text, "list"=>NULL, "options"=>array("class"=>"form-control open_area_tree", "tree"=>"", "readonly"=>"readonly") )
                            //     );
                            //     $html->DrawGenericFormField ( "area_id", $dirTree, null, array("class"=>"form-control", "disabled"=>true));
                            // }
                            // $html->CloseSpan();
                            $html->OpenSpan(4);
                            {
                                $html->DrawFormField("select", "area_id", $area_id, $AreaArr, array("class"=>"form-control", "optional"=>"true"));
                            }
                            $html->CloseSpan();
                            $html->OpenSpan(2);
                            {
                                $html->DrawFormField("select", "station_id", $station_id, $StationArr, array("class"=>"form-control", "optional"=>"true"));
                            }
                            $html->CloseSpan();
                            $html->OpenSpan(2);
                            {
                                $html->DrawFormField("select", "feeder_id", $feeder_id, $FeederArr, array("class"=>"form-control", "optional"=>"true"));
                            }
                            $html->CloseSpan();
                            $html->OpenSpan(2);
                            {
                                $html->DrawFormField("select", "transformer_id", $transformer_id, $TransformerArr, array("class"=>"form-control", "optional"=>"true"));
                            }
                            $html->CloseSpan();
                        }
                        $html->CloseDiv();

                        $html->OpenDiv("row");
                        {
                            $html->OpenSpan(2);
                            {
                                $html->DrawFormField ( "text", "enclosure_sn", $enclosure_sn, NULL, $options );
                            }
                            $html->CloseSpan();
                            $html->OpenSpan(2);
                            {
                                $html->DrawFormField ( "text", "Plant_No", $Plant_No, NULL, $options );
                            }
                            $html->CloseSpan();
                            $html->OpenSpan(2);
                            {
                                $html->DrawFormField ( "text", "Serial_No", $Serial_No, NULL, $options );
                            }
                            $html->CloseSpan();
                            $html->OpenSpan(2);
                            {
                                $html->DrawFormField("text", "from_date", $from_date, null, array("class"=>"form-control date-picker", "data-date-format"=>"yyyy-mm-dd", "readonly"=>"readonly"));
                            }
                            $html->CloseSpan();
                            $html->OpenSpan(2);
                            {
                                $html->DrawFormField("text", "to_date", $to_date, null, array("class"=>"form-control date-picker", "data-date-format"=>"yyyy-mm-dd", "readonly"=>"readonly"));
                            }
                            $html->CloseSpan();
                            $html->OpenSpan(2);
                            {
                                ?>
                                <div class="form-group">
                                    <label>&nbsp;</label>
                                    <div class="input-group">
                                        <span class="input-group-btn">
                                            <button class="btn green type="submit"><?php echo $dictionary->GetValue("filter");?></button>
                                        </span>
                                    </div>
                                </div>
                                <?php
                            }
                            $html->CloseSpan();
                        }
                        $html->CloseDiv();
                        ?>
                    </div>
                </div>
            </form>
            <?php
            $html->Datatable("example", "api/list.meter.installation.php?".$filter, $cols, $tableOptions);
        }
        $html->CloseWidget();
    }
    $html->CloseSpan();
}
$html->CloseDiv();
?>
<script>
$(function() {

    $('body').on('click', '.meter_trace', function () {
        id = $(this).data('id');
        window.location.href = "../trace/meter_trace.php?id=" + id;
    });


    $('#area_id').on('change', function () {
        // area_id = $(this).val();
        // //FillFeeder(area_id);
        // window.location.href = "meter_installation_list.php?&area_id=" + area_id;
        $("#meter_installation_list").submit();
    });

    $('#station_id').on('change', function () {
        // station_id = $(this).val();
        // area_id = $('#area_id').val();
        // //FillFeeder(station_id);
        // window.location.href = "meter_installation_list.php?&station_id=" + station_id + "&area_id=" + area_id;
        $("#meter_installation_list").submit();
    });

    $('#feeder_id').on('change', function () {
        // feeder_id = $(this).val();
        // station_id = $("#station_id").val();
        // area_id = $('#area_id').val();
        // window.location.href = "meter_installation_list.php?&station_id=" + station_id + "&area_id=" + area_id + "&feeder_id=" + feeder_id;
        $("#meter_installation_list").submit();
    });

    $('#transformer_id').on('change', function() {
        // transformer_id = this.value;
        // station_id = $("#station_id").val();
        // feeder_id = $("#feeder_id").val();
        // area_id = $('#area_id').val();
        // window.location.href = "meter_installation_list.php?transformer_id=" + transformer_id + "&station_id=" + station_id + "&area_id=" + area_id + "&feeder_id=" + feeder_id;
        $("#meter_installation_list").submit();
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
