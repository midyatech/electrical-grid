<?php
include '../include/header.php';
require_once realpath(__DIR__ . '/..').'/include/settings.php';
include_once realpath(__DIR__ . '/..').'/include/checksession.php';
include_once realpath(__DIR__ . '/..').'/include/checkpermission.php';
require_once realpath(__DIR__ . '/..').'/class/Enclosure.class.php';
require_once realpath(__DIR__ . '/..').'/class/Dictionary.php';
require_once realpath(__DIR__ . '/..').'/class/Assembly.class.php';
$dictionary = new Dictionary ( $LANGUAGE );
$dictionary->GetAllDictionary ();
$Enclosure = new Enclosure( );
$Assembly = new Assembly();

$enclosure_id=$gateway_id=null;
$operation="insert";
$meter=array();

$id = $aoid = null;
if (isset($_GET["id"]) && $_GET["id"] != ""){
    $enclosure_id = $_GET["id"];
}
if (isset($_GET["aoid"])) {
    print $aoid = $_GET["aoid"];
    $order = $Assembly->GetAssemblyOrder($aoid);
    //print_r($order);
    $project_items = $Assembly->getAssemblyOrderItems($aoid);
}

if ($enclosure_id ==-1) {
    //get last inserted enclosure
    $enclosure_id = $Enclosure->GetLastEnclosureByUser($USERID);
    if ($enclosure_id) {
        $enclosure = $Enclosure->GetEnclosureDetails($enclosure_id);
        $id = $enclosure[0]["enclosure_id"];
        $aoid = $enclosure[0]["assembly_order_id"];
    }
}

$assemblyOrders = $Assembly->getActiveAssemblyOrders();

?>
<div class="row form">
    <div class="col-xs-2">
        <ul class="nav nav-tabs tabs-left">
            <?php
            for ($i=0; $i<count($assemblyOrders); $i++) {

                if ($assemblyOrders[$i]["required_count"] > 0) {
                    $perc = floor($assemblyOrders[$i]["manufactured_count"]*100 / $assemblyOrders[$i]["required_count"]);
                    if ($perc < 50) {
                        $color = "danger";
                    } else if ($perc < 75) {
                        $color = "warning";
                    } else {
                        $color = "success";
                    }

                    $progres = '<div class="progress">
                                    <div class="progress-bar  progress-bar-'.$color.'" role="progressbar"
                                            aria-valuenow="'.$assemblyOrders[$i]["manufactured_count"].'" aria-valuemin="0"
                                            aria-valuemax="'.$assemblyOrders[$i]["required_count"].'"
                                            style="width: '.$perc.'%;">'.$perc.'
                                    </div>
                                </div>';


                    print '<li '. ((($assemblyOrders[$i]["assembly_order_id"] == $aoid) || ($aoid== null && $i==0)) ? 'class="active"' : '')  .'>
                                <a href="#tab_'.$assemblyOrders[$i]["assembly_order_id"].'" data-toggle="tab"> '.
                                (($assemblyOrders[$i]["assembly_order_code"] == null) ? "#".$assemblyOrders[$i]["assembly_order_id"] : $assemblyOrders[$i]["assembly_order_code"]).'<br>'.
                                '<div class=""><div class=""></div></div>'.
                                $progres.
                                ' </a>
                            </li>';

                }
            }
            ?>
        </ul>
    </div>
    <div class="col-xs-10">
        <section id="add_enclosure"></section>
    </div>
</div>

<link href="../assets/layouts/layout/css/enclosure.css" rel="stylesheet" type="text/css" />
<style>
.progress {
    margin-bottom: 10px
}
.knob-wrapper {
    /*height: 135px;*/
    margin-bottom: 20px;
}
.knob-wrapper div{
    text-align: center;
}
.knob-text {
    text-align: center;
    margin-bottom: 20px;
    margin-top: -35px;
}
</style>

<script>
    $(document).ready(function(){
        $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
            e.relatedTarget // previous active tab
            console.log(e.target)
            tab = $(e.target).attr('href');
            aoid = (tab.split("_"))[1];
            LoadOrder(aoid)
        });

        LoadOrder("<?php echo $aoid;?>");
    });

    function LoadOrder(aoid)
    {
        $("#add_enclosure").load("ui/order.summary.php?aoid="+aoid)
    }
</script>
<?php
include '../include/footer.php';
?>
<script src="../jquery.knob.min.js"></script>
<script src="js/enclosure.js?v=1"></script>
<link href="../assets/global/plugins/bootstrap-switch/css/bootstrap-switch.min.css" rel="stylesheet" type="text/css">
<script src="../assets/global/plugins/bootstrap-switch/js/bootstrap-switch.min.js" type="text/javascript"></script>
