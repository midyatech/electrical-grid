<?php
include_once realpath(__DIR__ . '/../..').'/include/checksession.php';
require_once realpath(__DIR__ . '/../..').'/class/HtmlHelper.php';
require_once realpath(__DIR__ . '/../..').'/class/Survey.class.php';
require_once realpath(__DIR__ . '/../..') . '/class/Tree.php';
include_once realpath(__DIR__ . '/../..').'/include/settings.php';

$html = new HTML ( $LANGUAGE );
$dictionary = new Dictionary ( $LANGUAGE );
//$dictionary->GetAllDictionary ();
$options = array("class"=>"form-control");
$listoptions = array("class"=>"form-control","optional"=>"true");
$Survey = new Survey( );
$description_key = "description_Survey";
$area_tree = new Tree("AREA_TREE");
$area_id=null;
$id="";
$filter = array();
$condition = null;
$count = $sum = 0;

if(isset($_REQUEST["id"])&&$_REQUEST["id"]!=NULL){
    $id = $_REQUEST["id"];
    $condition .= "&area_id=". $id;
    $area_text = $area_tree->GetPathString($id);
}
$cols = array();
$cols[] = array("column"=>"number_of_consumers");
$cols[] = array("column"=>"single_phase_consumers");
$cols[] = array("column"=>"three_phase_consumers");
$cols[] = array("column"=>"service_point_count");

$tableOptions = array();
$tableOptions["tableClass"]= "table-hover table-bordered table-striped";
$tableOptions["ordering"]= "false";
//$tableOptions["paging"]="false";
//$tableOptions["footer"]="true";
//$tableOptions["totals"]=array("1"=>$count, "2"=>$sum);
$html->OpenDiv("row");
{
    $html->OpenSpan(12);
    {
        $html->OpenWidget ("service_point_summary", $actions, array('collapse' => true, 'fullscreen'=>true,'table-actions'=>"example1"));
        {
            $html->Datatable("example", "api/list.service.point.count.by.area.php?".$condition, $cols, $tableOptions);
        }
        $html->CloseWidget();
    }
    $html->CloseSpan();
}
$html->CloseDiv();
?>
