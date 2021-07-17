<?php
include '../include/header.php';
require_once realpath(__DIR__ . '/..').'/include/settings.php';
include_once realpath(__DIR__ . '/..').'/include/checksession.php';
include_once realpath(__DIR__ . '/..').'/include/checkpermission.php';
require_once realpath(__DIR__ . '/..').'/class/Enclosure.class.php';
require_once realpath(__DIR__ . '/..').'/class/Dictionary.php';
$dictionary = new Dictionary ( $LANGUAGE );
$dictionary->GetAllDictionary ();
$Enclosure = new Enclosure( );

$enclosure_sn = $enclosure_id = $gateway_id = $transformer = $assembly_order = $feeder = $enclosure_type = $remaining_enclosure  = $ph3_1 = $ph3_3 = null;
$meter = $ph=array();

if (isset($_GET["id"]) && $_GET["id"] != ""){
    $enclosure_id = $_GET["id"];
}

$remaining_enclosure = 1000; //infinity, in case we didn't get the actual number

if (isset($_GET["aoid"]) && isset($_GET["ecid"]) && isset($_GET["tid"])){
    $manually = true;
    $aoid=$_GET["aoid"];
    $ecid=$_GET["ecid"];
    $tid=$_GET["tid"];
    $id=$_GET["id"];

    $remaining_filter = [$aoid, $tid, $ecid];
    //print_r($remaining_filter);
    $remaining_enclosure = $Enclosure->GetTransformerRemainingEnclosureCount($remaining_filter);
}

$enclosure = $Enclosure->GetEnclosureDetails($enclosure_id);

if($enclosure!=null){
    $enclosure_id=$enclosure[0]["enclosure_id"];
    $gateway_id=$enclosure[0]["gateway_id"];
    $gateway_sn=$enclosure[0]["gateway_sn"];
    $enclosure_sn=$enclosure[0]["enclosure_sn"];
    $transformer = $enclosure[0]["transformer_number"];
    $feeder = $enclosure[0]["feeder"];
    $assembly_order = $enclosure[0]["assembly_order"];
    $enclosure_type = $enclosure[0]["enclosure_type"];
    $meter_count = $enclosure[0]["meter_count"];
    $enclosure_configuration_id = $enclosure[0]["enclosure_configuration_id"];
}

/*
function drawMeters($i, $meter, $type){
    global $dictionary;
    echo'<div class="row">
            <div class=" col-lg-12">
                <div class="form-group">
                    <label class="control-label col-sm-4">'.$dictionary->GetValue("meter").' '.$i.'</label>
                    <div class="col-sm-4">
                        <p class="form-control-static"><b>'.$meter.'</b></p>
                    </div>
                    <div class="col-sm-4">
                        <p class="form-control-static"><b> '.$dictionary->GetValue($type).' </b></p>
                    </div>
                </div>
            </div>
        </div>';

}
*/
?>
<div class="row form" style="margin:0; padding: 0;">
    <form action="code/enclosure.insert.code.php" mathod="POST" role="form" class="form-horizontal form-row-seperated" name="add_enclosure" id="add_enclosure" >
            <div class="enclosure">
                <?php if( $enclosure[0]["meter"] == $meter_count ) { ?>
                    <div class="row" style="margin:0; padding: 0;">
                        <div class="col-lg-12" style="margin:0; padding: 0;">
                            <div class="print_area">
                                    <?php
                                    echo $feeder."<br>";
                                    echo "T# ".$transformer."<br>";
                                    echo $enclosure_type."($meter_count)";
                                    ?>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12" style="margin-top: 10px; text-align: center;" >
                            <a class="btn btn-lg blue hidden-print margin-bottom-5" onclick="javascript:window.print();">
                                <?php echo $dictionary->GetValue("print");?>
                                <i class="fa fa-print"></i>
                            </a>
                            <?php if ($manually) {?>
                            <a class="btn btn-lg blue hidden-print margin-bottom-5" href="scan_enclosure_user.php?<?php echo "aoid=$aoid&ecid=$ecid&tid=$tid";?>" >
                                <?php echo $dictionary->GetValue("add_new");?>
                            </a>
                            <?php } else { ?>
                            <a class="btn btn-lg blue hidden-print margin-bottom-5" href="enclosure_meters.php">
                                <?php echo $dictionary->GetValue("add_new");?>
                            </a>
                            <?php }
                            if( $remaining_enclosure == 0 ) {
                                ?>
                                <div class="alert alert-danger assembly_msg" style="text-align: center;">
                                    <?php echo "<b>".$dictionary->GetValue("configuration_assembly_completed")."</b>";?>
                                    <br/><br>
                                    <a class="btn btn-lg blue hidden-print margin-bottom-5" href="assembly_transformer_details.php?aoid=<?php print $aoid; ?>&id=<?php print $tid; ?>">
                                        &nbsp;<i class="fa fa-arrow-left"></i>
                                        <?php echo $dictionary->GetValue("back_to_transformer");?>
                                    </a>
                                </div>
                                <?php
                            }
                            ?>
                        </div>
                    </div>
                <?php } else { ?>
                    <div class="row">
                        <div class="col-lg-12" style="margin-top: 10px; text-align: center;" >
                            <div class="alert alert-danger assembly_msg" style="text-align: center;">
                                <b><?php echo $dictionary->GetValue("meter_count_not_compatible"); ?></b>
                                <br/>
                                <br/>
                                <a class="btn btn-lg blue hidden-print margin-bottom-5" href="enclosure_meters.php?aoid=<?php print $aoid; ?>&ecid=<?php print $ecid; ?>&id=<?php print $tid; ?>&id=<?php print $id; ?>">
                                    &nbsp;<i class="fa fa-plus"></i>
                                    <?php echo $dictionary->GetValue("scan_meters_again");?>
                                </a>
                            </div>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>
    </form>
</div>
<?php
include '../include/footer.php';
?>
<style>
@media print {
    .page-content {
        min-height: 0!important;
        padding: 0!important;
        margin: 0!important;
    }
    .print_area {
        text-align: center;
        width: 140px;
        font-size: 0.8em;
    }
    .assembly_msg {
        display: none;
    }
}
</style>
<script>
    $(document).ready(function(){
        document.getElementById("page-content").style.minHeight = 0;
        window.print();
    });
</script>
<script src="js/enclosure.js"></script>
<link href="../assets/global/plugins/bootstrap-switch/css/bootstrap-switch.min.css" rel="stylesheet" type="text/css">
<script src="../assets/global/plugins/bootstrap-switch/js/bootstrap-switch.min.js" type="text/javascript"></script>
