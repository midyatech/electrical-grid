<?php
include_once realpath(__DIR__ . '/../..').'/include/settings.php';
include_once realpath(__DIR__ . '/../..').'/include/checksession.php';
require_once realpath(__DIR__ . '/../..').'/class/Survey.class.php';
require_once realpath(__DIR__ . '/../..').'/class/HtmlHelper.php';

$html = new HTML ( $LANGUAGE );
$dictionary = new Dictionary ( $LANGUAGE );
$dictionary->GetAllDictionary ();

$Survey = new Survey();


$filter = array();

if (isset($_REQUEST["area_id"]) && $_REQUEST["area_id"] > 0 ) {
    $filter["area_id"] = $_REQUEST["area_id"];
}

if (isset($_REQUEST["station_id"]) && $_REQUEST["station_id"] > 0 ) {
    $filter["station_id"] = $_REQUEST["station_id"];
}

if (isset($_REQUEST["feeder_id"]) && $_REQUEST["feeder_id"] > 0 ) {
    $filter["feeder_id"] = $_REQUEST["feeder_id"];
}

$filter["service_point.point_type_id"] = 4;

$filter["point_id"] = implode( ", ", array_column($Survey->GetAssemblyOrderTransformer(), 'transformer_id'));

$transformers = $Survey->GetTransformer($filter);
//$transformers = $Survey->GetServicePoint($filter);

if ($transformers && $filter) {
    $data = array();
    //$data[0] = NULL;
    for ($i=0; $i<count($transformers); $i++) {
        $trnasofrmer = $transformers[$i]["station_id"]."/".$transformers[$i]["feeder_id"]."/".$transformers[$i]["capacity_id"]."/".$transformers[$i]["transformer_number"];
        $data[]=array("0"=>$transformers[$i]["point_id"], "1"=>$trnasofrmer);
    }
    echo '<div class="well" style="margin-bottom: 0px" >';
    echo '<div>
            <label class=" col-sm-4 control-label">
            </label>
            <div class="col-sm-8">
            <label class="mt-checkbox">
                <input type="checkbox" class="form-control" items-flow="vertical" label-align="opposite" name="checkall" id="checkall" value="1"> <b>Check All</b><span></span>
            </label>
            </div>
        </div>';
    //echo "Total : ".count($transformers);
    $html->DrawFormField("checkbox", "trnasformers[]", null, $data, array("checkBoxList"=>true, "class"=>"form-control trnasformers", "flow"=>"horizental", "items-flow"=>"vertical", "label-align"=>"opposite"));
    echo '</div>';
    $html->Button("button", "calculate_enclosure", "calculate_enclosure", array("class"=>"form-control btn btn-success calculate_enclosure"));
} else {
    echo '<div class="well" >';
    print $dictionary->GetValue("no_data_found");
    echo '</div>';
}
?>
