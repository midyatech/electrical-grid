<?php
include_once realpath(__DIR__ . '/../..').'/include/checksession.php';
//include_once realpath(__DIR__ . '/..').'/include/checkpermission.php';
include_once realpath(__DIR__ . '/../..').'/include/settings.php';
include_once realpath(__DIR__ . '/../..').'/class/Dictionary.php';
include_once realpath(__DIR__ . '/../..').'/class/HtmlHelper.php';
include_once realpath(__DIR__ . '/../..').'/class/Installation.class.php';
include_once realpath(__DIR__ . '/../..').'/class/Survey.class.php';
require_once realpath(__DIR__ . '/../..') . '/class/User.php';

$user = new User();
$html = new HTML($LANGUAGE);
$Installation = new Installation();
$dictionary = new Dictionary($LANGUAGE);
$dictionary->GetAllDictionary();
$survey = new Survey();

$point_id = $_GET["point_id"];

$enclosure_filter = array();
$enclosure_filter["point_id"] = $point_id;
$installed_enclosures = $Installation->GetInstalledEnclosures($enclosure_filter);

$html->OpenDiv("row");
{
    $cols = array();
    $cols[] = array("column"=>"enclosure_sn");
    $cols[] = array("column"=>"enclosure_type");
    if ($user-> CheckPermission($USERID, "permission_installation") == 1) {
        $cols[] = array("column"=>"ACTION_COL", "style"=>"width:150px","action-type"=>"ajax",
                            "buttons"=> array(
                                array("action-class"=>"delete_installed_enclosure", "button-icon"=>"fa fa-times", "title"=>$dictionary->GetValue("Delete"), "type"=>"button", "url"=>"href=javascript:;")
                            )
                        );
    }

    $tableOptions = array();
    $tableOptions["tableClass"]= "table-hover table-bordered table-condensed table-striped";
    $tableOptions["key"]=array("id"=>"installed_point_enclosure_id", "point_id"=>"point_id");
    $html->OpenDiv("col-xs-12");
    {
        if ($installed_enclosures) {
            $html->Table($installed_enclosures, $cols, $tableOptions);
        }
    }
    $html->CloseDiv();
}
$html->CloseDiv();
?>
