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
    //$project_items = $assembly->getAssemblyOrderItems($id, 0);
    //$project_items_extra = $assembly->getAssemblyOrderItems($id, 1);
    $order = $assembly->GetAssemblyOrder($id);
    $code = $order[0]["assembly_order_code"];
    $notes = $order[0]["notes"];
    $start_date = $order[0]["start_date"];
}

$tableoptions = array();

$actions = array (
    array("type"=>"button", "name"=>"Print", "value"=>"", "list"=>null, "options"=>array ("class" => "btn btn-icon-only btn-default print_order", "icon"=>"fa fa-print"))
);

require 'ui/assembly.breadcrumbs.php';

$html->OpenWidget($code, $actions, array('collapse' => true, 'fullscreen'=>true));
{
    $html->OpenDiv("row");
    {
        echo '<section id="stats">';
        require 'ui/assembly.stats.php';
        echo '</section>';
    }
    $html->CloseDiv();
}
$html->CloseWidget();

require '../include/footer.php';
?>
<script src="../assets/global/scripts/datatable.js" type="text/javascript"></script>
<script src="../assets/global/plugins/datatables/datatables.min.js" type="text/javascript"></script>
<script src="../assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js" type="text/javascript"></script>
<script src="js/assembly.js?v=2"></script>
<script src="../js/printThis.js?v=4"></script>
<script>
$(function () {
    $("body").on("click", ".print_order", function(){
        $("section#stats").printThis({
            importCSS: true,
            pageTitle: "",
        });
    });
    $("body").on("click", ".assembly_transformer_details", function(){
        id = $(this).data("id");
        aoid = "<?php print $id; ?>";
        window.location.assign("assembly_transformer_details.php?aoid="+aoid+"&id=" + id);
    });
    $("body").on("click", ".change_gateway_status", function() {
        OpenModal("ui/change.iccid.status.php?id="+<?php echo $id; ?>);
    });

    function OpenModal($address) {
    $("#myModal .modal-dialog .modal-title").html('');
    $("#myModal .modal-body").html("");
    $("#myModal .modal-body").load($address);
    SetModalTitle("myModal", "", "icon-plus-sign");
    $("#myModal .modal-dialog").removeClass("modal-lg");
    $("#myModal .modal-dialog").removeClass("modal-full");
    $("#myModal").modal({ backdrop: 'static', keyboard: false });
    //fix relative position when closing
    $("#myModal").css("position", "");
}
});

</script>