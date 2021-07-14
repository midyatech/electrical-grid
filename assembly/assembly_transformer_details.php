<?php
require_once realpath(__DIR__ . '/..') . '/include/header.php';
include_once realpath(__DIR__ . '/..') . '/include/checksession.php';
include_once realpath(__DIR__ . '/..') . '/include/checkpermission.php';
require_once realpath(__DIR__ . '/..') . '/class/Assembly.class.php';
require_once realpath(__DIR__ . '/..') . '/class/Chart.php';

$chart = new Chart();
$assembly = new Assembly();

$html = new HTML($LANGUAGE);
$dictionary = new Dictionary($LANGUAGE);
$dictionary->GetAllDictionary ();

$StationArr = $assembly->GetStationByArea();
$FeederArr = $assembly->GetFeederByStation();

$code = $notes = $start_date = null;
$id= $project_items = null;
if (isset($_GET["id"])) {
    $id = $_GET["id"];
}

$tableoptions = array();

$actions = ""; /*array (
    array("type"=>"button", "name"=>"Print", "value"=>"", "list"=>null, "options"=>array ("class" => "btn btn-icon-only btn-default print_order", "icon"=>"fa fa-print"))
);
*/

require 'ui/assembly.breadcrumbs.php';

$html->OpenWidget("", $actions, array('collapse' => false, 'fullscreen'=>false));
{
    $html->OpenDiv("row");
    {
        echo '<section id="stats">';
        require 'ui/assembly.tranformer.php';
        echo '</section>';
    }
    $html->CloseDiv();
}
$html->CloseWidget();

require '../include/footer.php';
?>
<script src="js/assembly.js"></script>
<script src="../js/printThis.js?v=1"></script>
<script>
$(function () {
    $("body").on("click", ".print_order", function(){
        $("section#stats").printThis({
            importCSS: true,
            pageTitle: "",
        });
    });
});
</script>