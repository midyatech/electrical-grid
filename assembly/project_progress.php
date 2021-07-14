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

$AssemblyOrders = $Assembly->getActiveAssemblyOrdersArr();
$TransformerArr = array();//$Assembly->getAssemblyOrdersTransformerArr($aoid);
?>

<div class="row form">
    <div class="col-lg-12">
        <div class="well" style="background-color:#fff; border:solid 1px #999; padding: 20px !important; border: solid 1px silver">
            <div class="row">
                <div class="col-lg-12">
                    <div class="row">
                        <?php
                        $html->OpenSpan(6);
                        {
                            $html->DrawFormField("select", "assembly_order", null, $AssemblyOrders, array("class"=>"form-control", "optional"=>"All", "flow"=>"horizental"));
                        }
                        $html->CloseSpan();
                        $html->OpenSpan(6);
                        {
                            $html->DrawFormField("select", "transformer_id", null, $TransformerArr, array("class"=>"form-control", "optional"=>"All", "flow"=>"horizental"));
                        }
                        $html->CloseSpan();
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-xs-12">
        <section id="add_enclosure"></section>
    </div>
</div>

<link href="../assets/layouts/layout/css/enclosure.css" rel="stylesheet" type="text/css" />
<style>
.progress {
    margin-bottom: 10px
}
.knob-wrapper {
    height: 262px;
    margin-bottom: 20px;

}
.knob-wrapper div{
    text-align: center;
}
.knob-text {
    text-align: center;
    margin-bottom: 20px;
    margin-top: -50px;
}
.e_name {

}
</style>

<script>
    $(document).ready(function(){
        $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
            e.relatedTarget // previous active tab
            console.log(e.target)
            tab = $(e.target).attr('href');
            aoid = (tab.split("_"))[1];
            LoadOrder(aoid, "")
        });

        LoadOrder("<?php echo $aoid;?>", "");
    });

    $('#assembly_order').on('change', function () {
        aoid = $(this).val();
        //aoid = $("#transformer_id").val();
        LoadOrder(aoid, "");
    });

    $('#assembly_order').on('change', function () {
        aoid = $(this).val();
        FillTransformer(aoid);
        LoadOrder(aoid, "");
    });

    $('#transformer_id').on('change', function () {
        transformer_id = $(this).val();
        aoid = $("#assembly_order").val();
        LoadOrder(aoid, transformer_id);
    });

    function LoadOrder(aoid, transformer_id)
    {
        $("#add_enclosure").load("ui/order.summary.php?aoid="+aoid+"&transformer_id="+transformer_id);
    }


    function FillTransformer(aoid){
        if( aoid > 0){
            FillListOptions("ui/get.transformer.php?aoid="+aoid, "transformer_id", "All");
        } else {
            FillListOptions("ui/get.transformer.php", "transformer_id", "All");
        }
    }
</script>
<?php
include '../include/footer.php';
?>
<script src="../jquery.knob.min.js"></script>
<script src="js/enclosure.js?v=1"></script>
<link href="../assets/global/plugins/bootstrap-switch/css/bootstrap-switch.min.css" rel="stylesheet" type="text/css">
<script src="../assets/global/plugins/bootstrap-switch/js/bootstrap-switch.min.js" type="text/javascript"></script>
