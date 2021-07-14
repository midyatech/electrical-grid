<?php
include_once realpath(__DIR__ . '/../..').'/include/checksession.php';
include_once realpath(__DIR__ . '/../..').'/include/checkpermission.php';
require_once realpath(__DIR__ . '/../..').'/class/HtmlHelper.php';
require_once realpath(__DIR__ . '/../..').'/class/Survey.class.php';
require_once realpath(__DIR__ . '/../..') . '/class/Tree.php';

$html = new HTML ( $LANGUAGE );
$dictionary = new Dictionary ( $LANGUAGE );
$dictionary->GetAllDictionary ();
$options = array("class"=>"form-control");
$listoptions = array("class"=>"form-control","optional"=>"true");
$Survey = new Survey( );
$description_key = "description_Survey";
$area_tree = new Tree("AREA_TREE");
$area_id=$actions=null;

$filter = array();
$condition = null;
$count = $sum = 0;
if(isset($_REQUEST["area"])&&$_REQUEST["area"]!=NULL){
    $area_id = $_REQUEST["area"];
    $condition .= "&area_id=". $area_id;
    $area_text = $area_tree->GetPathString($area_id);
}else{
    $area_id=1;
    $condition .= "&area_id=". $area_id;
    $area_text = $area_tree->GetPathString($area_id);
}
if( isset($_POST["to_date"] ) && $_POST["to_date"] != NULL ) {
    $to_date = $_POST["to_date"];
}else{
    $to_date = date("Y-m-d");
}
if( isset($_POST["from_date"] ) && $_POST["from_date"] != NULL ) {
    $from_date = $_POST["from_date"];

}else{
    $from_date = date('Y-m-d', strtotime($to_date . "-30 days"));
}
$filter["from_date"] = $from_date;
$filter["to_date"] = $to_date." 23:59:59";
$filter["area_id"] = $area_id;
$report_data = $Survey->GetServicePointCount($filter);
$condition .= "&from_date=$from_date&to_date=$to_date";

$single_phase_sum = $three_phase_sum = $point_sum = $number_of_consumers_sum = 0;

if($report_data){
    for($i=0; $i<count($report_data); $i++){
        $single_phase_sum += $report_data[$i]["single_phase_consumers"] * $report_data[$i]["service_point_count"];
        $three_phase_sum += $report_data[$i]["three_phase_consumers"] * $report_data[$i]["service_point_count"];
        $point_sum += $report_data[$i]["service_point_count"];
        $number_of_consumers_sum += $report_data[$i]["number_of_consumers"];
    }
}

$cols = array();
$cols[] = array("column"=>"sequence");
$cols[] = array("column"=>"number_of_consumers");
$cols[] = array("column"=>"single_phase_consumers");
$cols[] = array("column"=>"three_phase_consumers");
$cols[] = array("column"=>"service_point_count");

$tableOptions = array();
$tableOptions["tableClass"]= "table-hover table-bordered table-striped";
$tableOptions["ordering"]= "TRUE";
$tableOptions["paging"]="false";
$tableOptions["footer"]="true";
$tableOptions["totals"]=array("1"=>number_format($number_of_consumers_sum, 0, '.', ','), "2"=>number_format($single_phase_sum, 0, '.', ','),"3"=>number_format($three_phase_sum, 0, '.', ','),"4"=>number_format($point_sum, 0, '.', ','));
$html->OpenDiv("row");
{
    $html->OpenSpan(12);
    {
        $html->OpenWidget ("service_point_count", $actions, array('collapse' => true, 'fullscreen'=>true,'table-actions'=>"example1"));
        {
            $html->OpenForm ( null, "form3");
            {
                $html->OpenDiv("row");
                {
                    $html->OpenSpan(4);
                    {
                        $dirTree = array (
                            array ( "type"=>"hidden", "name"=>"area", "value"=>$area_id, "list"=>NULL, "options"=>NULL ),
                            array ( "type"=>"text", "name"=>"area_text", "value"=>$area_text, "list"=>NULL, "options"=>array("class"=>"form-control open_area_tree", "tree"=>"", "readonly"=>"readonly") )
                        );
                        $html->DrawGenericFormField ( "area_id", $dirTree, null, $options);
                    }
                    $html->CloseSpan();
                    $html->OpenSpan(3);
                    {
                        $html->DrawFormField ( "text", "from_date", $from_date, NULL, array("class"=>"form-control date-picker", "data-date-format"=>"yyyy-mm-dd", "readonly"=>"readonly") );
                    }
                    $html->CloseSpan();

                    $html->OpenSpan(3);
                    {
                        $html->DrawFormField ( "text", "to_date", $to_date, NULL, array("class"=>"form-control date-picker", "data-date-format"=>"yyyy-mm-dd", "readonly"=>"readonly") );
                    }
                    $html->CloseSpan();

                    $html->OpenSpan(2);
                    {
                        ?>
                        <div class="form-group">
                            <label>&nbsp;</label>
                            <div class="input-group">
                                <span class="input-group-btn">
                                    <button class="btn green filter_service_point_count" type="submit"><?php echo $dictionary->GetValue("filter");?></button>
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

            $html->Datatable("example", "api/list.service.point.count.php?".$condition, $cols, $tableOptions);
        }
        $html->CloseWidget();
    }
    $html->CloseSpan();
}
$html->CloseDiv();
?>
