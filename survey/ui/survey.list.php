<?php
include_once realpath(__DIR__ . '/../..').'/include/checksession.php';
include_once realpath(__DIR__ . '/../..').'/include/checkpermission.php';
require_once realpath(__DIR__ . '/../..').'/class/HtmlHelper.php';
require_once realpath(__DIR__ . '/../..').'/class/Survey.class.php';

$html = new HTML ( $LANGUAGE );
$dictionary = new Dictionary ( $LANGUAGE );
$dictionary->GetAllDictionary ();
$options = array("class"=>"form-control");
$Survey = new Survey();

$condition = "&user_id=". $USERID;

$cols = array();
$cols[] = array("column"=>"point_type");
$cols[] = array("column"=>"point_detail");
$cols[] = array("column"=>"ACTION_COL", "style"=>"width:150px","action-type"=>"ajax",
                    "buttons"=> array(
                    array("action-class"=>"survey_delete", "button-icon"=>"fa fa-times", "title"=>$dictionary->GetValue("Delete"), "type"=>"button", "url"=>"href=javascript:;"),
                    array("action-class"=>"survey_edit", "button-icon"=>"fa fa-pencil", "title"=>$dictionary->GetValue("Edit"), "type"=>"link", "url"=>"add_survey.php")
                    )
                );

$tableOptions = array();
$tableOptions["tableClass"]= "table-hover table-bordered table-condensed table-striped";
$tableOptions["key"]=array("id"=>"point_id");
$tableOptions["ordering"] = "false";
$html->OpenDiv("row");
{
    $html->OpenSpan(12);
    {
        $html->OpenWidget ("survey_list", NULL, array('collapse' => true, 'fullscreen'=>true,'table-actions'=>"example1"));
        {
            $html->Datatable("example", "api/list.survey.php?".$condition, $cols, $tableOptions);
        }
        $html->CloseWidget();
    }
    $html->CloseSpan();
}
$html->CloseDiv();
?>