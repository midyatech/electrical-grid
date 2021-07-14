<?php
require_once realpath(__DIR__ . '/../..').'/include/checksession.php';
require_once realpath(__DIR__ . '/../..').'/class/HtmlHelper.php';
//include_once realpath(__DIR__ . '/../..').'/include/checkpermission.php';
require_once realpath(__DIR__ . '/../..').'/include/settings.php';
require_once realpath(__DIR__ . '/../..').'/class/Assembly.class.php';

$html = new HTML($LANGUAGE);
$dictionary = new Dictionary($LANGUAGE);

$dictionary = new Dictionary($LANGUAGE);
$Assembly = new Assembly();


$orders = null;
if (isset($_GET["id"]) && $_GET["id"] != "") {
    $assembly_order_id = $_GET["id"];
    $orders = $Assembly->getAssemblyOrders();
}


$cols = array();
$cols[] = array("column"=>"assembly_order_code");
$cols[] = array("column"=>"start_date");
$cols[] = array("column"=>"notes");
$cols[] = array("column"=>"ACTION_COL", "style"=>"width:50px","action-type"=>"link",
        "buttons"=> array(
            array("button-icon"=>"fa fa-check", "class"=>"", "title"=>$dictionary->GetValue("Select"), "action-url"=>"add_enclosure.php")
        )
    );

$tableOptions = array();
$tableOptions["tableClass"]= "table-bordered table-striped";
$tableOptions["key"]=array("aoid"=>"assembly_order_id");

$html->Table($orders, $cols, $tableOptions);

?>
