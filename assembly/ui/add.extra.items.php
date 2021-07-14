<?php
include_once realpath(__DIR__ . '/../..').'/include/settings.php';
include_once realpath(__DIR__ . '/../..').'/include/checksession.php';
//include_once realpath(__DIR__ . '/../..').'/include/checkpermission.php';
require_once realpath(__DIR__ . '/../..').'/class/HtmlHelper.php';
require_once realpath(__DIR__ . '/../..') . '/class/Dictionary.php';
require_once realpath(__DIR__ . '/../..') . '/class/Assembly.class.php';

$html = new HTML($LANGUAGE);
$dictionary = new Dictionary($LANGUAGE);
$dictionary->GetAllDictionary();
$assembly = new Assembly();
$enclosureTypes = $assembly->GetEnclosureConfigurations(); //getEnclosureTypes();

$assembly_order_id = $_GET["id"];

$options = array("class"=>"form-control");

$html->OpenForm( "code/assembly.order.add.items.code.php", "extra_items_form", "vertical");
{
    $html->OpenDiv("row");
    {
        $html->OpenSpan(12);
        {
            $html->DrawFormField("select", "enclosure_config_id", null, $enclosureTypes, $options);
            $html->DrawFormField("text", "count", null, null, $options);
            $html->HiddenField("order_id", $assembly_order_id);
            $html->Button("button", "add_extra_enclosures", "add_enclosures", array("class"=>"btn btn-block btn-primary"));
        }
        $html->CloseSpan();
    }
    $html->CloseDiv();
}
$html->CloseForm();
?>