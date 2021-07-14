<?php
include_once realpath(__DIR__ . '/../..').'/include/checksession.php';
require_once realpath(__DIR__ . '/../..').'/class/HtmlHelper.php';
//include_once realpath(__DIR__ . '/../..').'/include/checkpermission.php';
require_once realpath(__DIR__ . '/../..').'/include/settings.php';
require_once realpath(__DIR__ . '/../..').'/class/Assembly.class.php';

$html = new HTML($LANGUAGE);
$dictionary = new Dictionary($LANGUAGE);

$dictionary = new Dictionary($LANGUAGE);
$Assembly = new Assembly();

$transformers = array();
$transformer_id = null;
$points = null;
if (isset($_GET["transformer_id"]) && $_GET["transformer_id"] != "") {
    $transformer_id = $_GET["transformer_id"];
    $transformers = $Assembly->getTransformersEnclosures($transformer_id);//[$transformer_id]//[17245]
}

$actions = array (
                array ( "type"=>"button", "name"=>"Change", "value"=>"Add_extra", "list"=>null, "options"=>array ("class" => "btn btn-success add_extra_items", "icon"=>"fa icon-plus"))
            );
$cols = array();
$cols[] = array("column"=>"enclosure_type");
$cols[] = array("column"=>"enclosures_count");
$tableoptions = array();
/*
$cols[] = array("column"=>"ACTION_COL", "style"=>"width:50px","action-type"=>"ajax",
            "buttons"=> array(
                array("action-class"=>"enclosure_delete", "button-icon"=>"fa fa-times", "title"=>$dictionary->GetValue("Delete"), "type"=>"button", "url"=>"href=javascript:;")
            )
        );
*/
$html->Table($transformers, $cols, $tableoptions );

?>
