<?php
include_once realpath(__DIR__ . '/../..').'/include/settings.php';
include_once realpath(__DIR__ . '/../..').'/include/checksession.php';
include_once realpath(__DIR__ . '/../..').'/include/checkpermission.php';
require_once realpath(__DIR__ . '/../..').'/class/HtmlHelper.php';
require_once realpath(__DIR__ . '/../..') . '/class/Dictionary.php';
require_once realpath(__DIR__ . '/../..') . '/class/Assembly.class.php';
require_once realpath(__DIR__ . '/../..') . '/class/Chart.php';

$chart = new Chart();
$assembly = new Assembly();

$transformers = array(17245);
$report_data = $assembly->reportGetAssemblyOrderDetails($transformers);

$series = array();
$series[]=array("valueField"=>"single_phase", "name"=>"single_phase" );
$series[]=array("valueField"=>"three_phase", "name"=>"three_phase" );
$series[]=array("valueField"=>"gateways", "name"=>"gateways" );
$series[]=array("valueField"=>"enclosures", "name"=>"enclosures" );

$chart->BarChart($report_data, $series, "text", "chart1", $dictionary->GetValue("Statistics"), $dictionary->GetValue("Transfomers Statistics"), "bar", null);

print $report_data[0]["single_phase"].$report_data[0]["three_phase"];
$pie_data = array();
$pie_data[] = array("type"=>"single", "value"=>$report_data[0]["single_phase"]);
$pie_data[] = array("type"=>"three", "value"=>$report_data[0]["three_phase"]);

$series2 = array();
$series2[]=array("valueField"=>"value", "name"=>"value" );

$chart->PieChart($pie_data, $series2, "type", "chart2", $dictionary->GetValue("Meters"), $dictionary->GetValue("Meters"));

$html->OpenSpan(6);
{
    echo '<div style="width: auto; padding: 20px;">
            <div id="chart1"></div>
        </div>';
}
$html->CloseSpan();
$html->OpenSpan(6);
{
    echo '<div style="width: auto; padding: 20px;">
            <div id="chart2"></div>
        </div>';
}
$html->CloseSpan();

?>

