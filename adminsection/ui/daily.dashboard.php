<?php
include_once realpath(__DIR__ . '/../..').'/include/settings.php';
include_once realpath(__DIR__ . '/../..').'/include/checksession.php';
include_once realpath(__DIR__ . '/../..').'/include/checkpermission.php';
require_once realpath(__DIR__ . '/../..').'/class/HtmlHelper.php';
require_once realpath(__DIR__ . '/../..') . '/class/Dictionary.php';
require_once realpath(__DIR__ . '/../..') . '/class/Tree.php';
require_once realpath(__DIR__ . '/../..').'/class/Survey.class.php';
require_once realpath(__DIR__ . '/../..') . '/class/Assembly.class.php';
require_once realpath(__DIR__ . '/../..') . '/class/Installation.class.php';

$dictionary = new Dictionary($LANGUAGE);
$dictionary->GetAllDictionary();
$area_tree = new Tree("AREA_TREE");
$html = new HTML($LANGUAGE);
$Survey = new Survey( );
$Assembly = new Assembly();
$Installation = new Installation();

$single_phase = $three_phase = $service_point_count = $transformer_number_count = 0;
$station_id = $feeder_id = "";
//filters
$filter = $tableoptions = array();

if( isset($_REQUEST["area"] ) && $_REQUEST["area"] != NULL ) {
    $area_id = $_REQUEST["area"];
    $filter["area_path"] = $area_tree->GetNodePath($area_id);
} else {
    $area_id = $USERACCESS;
    $filter["area_path"] = $area_tree->GetNodePath($area_id);
}
$area_text = $area_tree->GetPathString($area_id);


if( isset($_REQUEST["from_date"] ) && $_REQUEST["from_date"] != NULL ) {
    $from_date = $filter["from_date"] = $_REQUEST["from_date"];
} else {
    $from_date = $filter["from_date"] = date("Y-m-1");
}

if( isset($_REQUEST["to_date"] ) && $_REQUEST["to_date"] != NULL ) {
    $to_date = $_REQUEST["to_date"];
    $filter["to_date"] = $to_date." 23:59:59";
} else {
    $to_date = $filter["to_date"]  = date("Y-m-d");
}

if( isset($_REQUEST["station_id"] ) && $_REQUEST["station_id"] != NULL ) {
    $station_id = $filter["station_id"] = $_REQUEST["station_id"];
}

if( isset($_REQUEST["feeder_id"] ) && $_REQUEST["feeder_id"] != NULL ) {
    $feeder_id = $filter["feeder_id"] = $_REQUEST["feeder_id"];
}


$StationArr = $Installation->GetStationByArea();
$FeederArr = $Installation->GetFeederByStation($station_id);


// Service Point Summary
$ServicePointSummary = $Survey->GetServicePointSummaryByArea($filter);
for ( $i=0; $i < count( $ServicePointSummary ); $i++ ){
    $single_phase += $ServicePointSummary[$i]["single_phase_consumers"];
    $three_phase += $ServicePointSummary[$i]["three_phase_consumers"];
    $transformer_number_count += $ServicePointSummary[$i]["transformer_number_count"];
    $service_point_count += $ServicePointSummary[$i]["service_point_count"];
}

// Assembly Order Summary
$AssemblyOrderSummary = $Assembly->getAssemblyOrderSummaryDashboard($filter);

// Installation Summary
$InstallationSummary = $Installation->getInstallationSummaryDashboard($filter);

$html->OpenDiv("row");
{
    $html->OpenSpan(12);
    {
        $html->OpenWidget ("dashboard", null, array('collapse' => true, 'fullscreen'=>true,'table-actions'=>"example1"));
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
                        $html->DrawGenericFormField ( "area_id", $dirTree, null, array("class"=>"form-control", "disabled"=>true));
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
                                    <button class="btn green" type="submit"><?php echo $dictionary->GetValue("filter");?></button>
                                </span>
                            </div>
                        </div>
                        <?php
                    }
                    $html->CloseSpan();
                }
                $html->CloseDiv();

                // Summary
                $html->OpenDiv("row");
                {
                    $html->OpenSpan(12);
                    {
                        $data = array();

                        // Survey
                        $data[] = array(
                            "System"=>$dictionary->getValue("Survey Summary"),
                            "enclosure_count"=>"",
                            "small_enclosure"=>"",
                            "big_enclosure"=>"",
                            "meter_count"=>"",
                            "single_phase"=>number_format($single_phase, 0, '.', ','),
                            "three_phase"=>number_format($three_phase, 0, '.', ','),
                            "ct"=>number_format($transformer_number_count, 0, '.', ','),
                            "gateways"=>"",
                            "service_point_count"=>number_format($service_point_count, 0, '.', ',')
                        );

                        // Assembly
                        $data[] = array(
                                    "System"=>$dictionary->getValue("Assembly Summary"),
                                    "enclosure_count"=>number_format($AssemblyOrderSummary[0]["enclosure_count"], 0, '.', ','),
                                    "small_enclosure"=>number_format($AssemblyOrderSummary[0]["small_enclosure"], 0, '.', ','),
                                    "big_enclosure"=>number_format($AssemblyOrderSummary[0]["big_enclosure"], 0, '.', ','),
                                    "meter_count"=>number_format($AssemblyOrderSummary[0]["meter_count"], 0, '.', ','),
                                    "single_phase"=>number_format($AssemblyOrderSummary[0]["single_phase"], 0, '.', ','),
                                    "three_phase"=>number_format($AssemblyOrderSummary[0]["three_phase"], 0, '.', ','),
                                    "ct"=>number_format($AssemblyOrderSummary[0]["ct"], 0, '.', ','),
                                    "gateways"=>number_format($AssemblyOrderSummary[0]["gateways"], 0, '.', ','),
                                    "service_point_count"=>""
                                );

                        // Installation
                        $data[] = array(
                                        "System"=>$dictionary->getValue("Installation Summary"),
                                        "enclosure_count"=>number_format($InstallationSummary[0]["enclosure_count"], 0, '.', ','),
                                        "small_enclosure"=>number_format($InstallationSummary[0]["small_enclosure"], 0, '.', ','),
                                        "big_enclosure"=>number_format($InstallationSummary[0]["big_enclosure"], 0, '.', ','),
                                        "meter_count"=>number_format($InstallationSummary[0]["meter_count"], 0, '.', ','),
                                        "single_phase"=>number_format($InstallationSummary[0]["single_phase"], 0, '.', ','),
                                        "three_phase"=>number_format($InstallationSummary[0]["three_phase"], 0, '.', ','),
                                        "ct"=>number_format($InstallationSummary[0]["ct"], 0, '.', ','),
                                        "gateways"=>number_format($InstallationSummary[0]["gateways"], 0, '.', ','),
                                        "service_point_count"=>""
                                );

                        $cols = array();
                        $cols[] = array("column"=>"System");
                        $cols[] = array("column"=>"enclosure_count");
                        $cols[] = array("column"=>"small_enclosure");
                        $cols[] = array("column"=>"big_enclosure");
                        $cols[] = array("column"=>"meter_count");
                        $cols[] = array("column"=>"single_phase");
                        $cols[] = array("column"=>"three_phase");
                        $cols[] = array("column"=>"ct");
                        $cols[] = array("column"=>"gateways");
                        $cols[] = array("column"=>"service_point_count");
                        echo '<h2>Summary</h2>';
                        $html->Table($data, $cols, $tableoptions);
                    }
                    $html->CloseSpan();

                }
                $html->CloseDiv();


                // Survey Summary
                $html->OpenDiv("row");
                {

                    $html->OpenSpan(6);
                    {
                        $survey_data = array();
                        $survey_data[] = array("Survey"=>$dictionary->getValue("single_phase"), "count"=>number_format($single_phase, 0, '.', ','));
                        $survey_data[] = array("Survey"=>$dictionary->getValue("three_phase"), "count"=>number_format($three_phase, 0, '.', ','));
                        $survey_data[] = array("Survey"=>$dictionary->getValue("transformer_number_count"), "count"=>number_format($transformer_number_count, 0, '.', ','));
                        $survey_data[] = array("Survey"=>$dictionary->getValue("service_point_count"), "count"=>number_format($service_point_count, 0, '.', ','));
                        //$survey_data[] = array("Survey"=>$dictionary->getValue("gateways"), "count"=>$gateway;


                        $survey_cols = array();
                        $survey_cols[] = array("column"=>"Survey");
                        $survey_cols[] = array("column"=>"count");
                        echo '<h2>Survey Summary</h2>';
                        $html->Table($survey_data, $survey_cols, $tableoptions);
                    }
                    $html->CloseSpan();

                }
                $html->CloseDiv();


                // Assembly Summary
                $html->OpenDiv("row");
                {

                    $html->OpenSpan(6);
                    {
                        $assembly_data = array();
                        $assembly_data[] = array("Assembly"=>$dictionary->getValue("enclosure_count"), "count"=>number_format($AssemblyOrderSummary[0]["enclosure_count"], 0, '.', ','));
                        $assembly_data[] = array("Assembly"=>$dictionary->getValue("small_enclosure"), "count"=>number_format($AssemblyOrderSummary[0]["small_enclosure"], 0, '.', ','));
                        $assembly_data[] = array("Assembly"=>$dictionary->getValue("big_enclosure"), "count"=>number_format($AssemblyOrderSummary[0]["big_enclosure"], 0, '.', ','));
                        $assembly_data[] = array("Assembly"=>$dictionary->getValue("meter_count"), "count"=>number_format($AssemblyOrderSummary[0]["meter_count"], 0, '.', ','));
                        $assembly_data[] = array("Assembly"=>$dictionary->getValue("single_phase"), "count"=>number_format($AssemblyOrderSummary[0]["single_phase"], 0, '.', ','));
                        $assembly_data[] = array("Assembly"=>$dictionary->getValue("three_phase"), "count"=>number_format($AssemblyOrderSummary[0]["three_phase"], 0, '.', ','));
                        $assembly_data[] = array("Assembly"=>$dictionary->getValue("ct"), "count"=>number_format($AssemblyOrderSummary[0]["ct"], 0, '.', ','));
                        $assembly_data[] = array("Assembly"=>$dictionary->getValue("gateways"), "count"=>number_format($AssemblyOrderSummary[0]["gateways"], 0, '.', ','));

                        $assembly_cols = array();
                        $assembly_cols[] = array("column"=>"Assembly");
                        $assembly_cols[] = array("column"=>"count");
                        echo '<h2>Assembly Summary</h2>';
                        $html->Table($assembly_data, $assembly_cols, $tableoptions);
                    }
                    $html->CloseSpan();

                }
                $html->CloseDiv();


                // Installation Summary
                $html->OpenDiv("row");
                {

                    $html->OpenSpan(6);
                    {
                        $installation_data = array();
                        $installation_data[] = array("Installation"=>$dictionary->getValue("enclosure_count"), "count"=>number_format($InstallationSummary[0]["enclosure_count"], 0, '.', ','));
                        $installation_data[] = array("Installation"=>$dictionary->getValue("small_enclosure"), "count"=>number_format($InstallationSummary[0]["small_enclosure"], 0, '.', ','));
                        $installation_data[] = array("Installation"=>$dictionary->getValue("big_enclosure"), "count"=>number_format($InstallationSummary[0]["big_enclosure"], 0, '.', ','));
                        $installation_data[] = array("Installation"=>$dictionary->getValue("meter_count"), "count"=>number_format($InstallationSummary[0]["meter_count"], 0, '.', ','));
                        $installation_data[] = array("Installation"=>$dictionary->getValue("single_phase"), "count"=>number_format($InstallationSummary[0]["single_phase"], 0, '.', ','));
                        $installation_data[] = array("Installation"=>$dictionary->getValue("three_phase"), "count"=>number_format($InstallationSummary[0]["three_phase"], 0, '.', ','));
                        $installation_data[] = array("Installation"=>$dictionary->getValue("ct"), "count"=>number_format($InstallationSummary[0]["ct"], 0, '.', ','));
                        $installation_data[] = array("Installation"=>$dictionary->getValue("gateways"), "count"=>number_format($InstallationSummary[0]["gateways"], 0, '.', ','));

                        $installation_cols = array();
                        $installation_cols[] = array("column"=>"Installation");
                        $installation_cols[] = array("column"=>"count");
                        echo '<h2>Installation Summary</h2>';
                        $html->Table($installation_data, $installation_cols, $tableoptions);
                    }
                    $html->CloseSpan();

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
?>
<script src="js/area.js"></script>

<script>
$(function() {
    $('#station_id').on('change', function () {
        $("#form3").submit();
        // station_id = $(this).val();
        // window.location.href = "daily_dashboard.php?&station_id=" + station_id;
    });
/*
    $('#feeder_id').on('change', function () {
        feeder_id = $(this).val();
        station_id = $("#station_id").val();
        window.location.href = "daily_dashboard.php?&station_id=" + station_id + "&feeder_id=" + feeder_id;
    });
*/
});
</script>