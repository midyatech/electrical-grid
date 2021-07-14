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

$meter=array();
$switch_count = $enclosure_sn = $id = $gateway_id = null;
$no_of_meter = 3;

if (isset($_REQUEST["sn"]) && $_REQUEST["sn"] != ""){
    $enclosure_sn = $_REQUEST["sn"];
    $enclosure = $Enclosure->GetEnclosureDetails(null, $enclosure_sn);
}

if (isset($enclosure) && $enclosure) {
    $id = $enclosure[0]["enclosure_id"];
    $enclosure_sn = $enclosure[0]["enclosure_sn"];
    $gateway_id = $enclosure[0]["gateway_id"];
    $meter_type_id = $enclosure[0]["meter_type_id"];
    $switch_count = count($enclosure);

    if( $switch_count > 3 ){
        $no_of_meter = 6;
    } else {
        if($meter_type_id == 1 ){
            $no_of_meter = 3;
        } else if($meter_type_id == 2){
            $no_of_meter = 2;
        } else if($meter_type_id == 3){
            $no_of_meter = 1;
        }
    }

    for($i=1; $i<=6; $i++){
        for ($j=0; $j<count($enclosure); $j++) {
            $meter_id = null;
            if ($enclosure[$j]["meter_sequence"] == $i) {
                $meter_id = $enclosure[$j]["meter_id"];
                break;
            }
        }
        $meter["$i"] = $meter_id;
    }
}

function drawMeters($i, $meter){
    global $dictionary;
    // echo'<div class="col-md-4 meter1" id="m'.$i.'">
    //         <div class="mt-widget-3">
    //             <div class="mt-head bg-white" style="padding: 0px">
    //                 <!-- <img src="../img/meter-bw.png" class="meter-img"> -->
    //                 <div class="mt-head-button">
    //                 Meter-'.$i.'
    //                 </div>
    //             </div>
    //             <div class="mt-body-actions-icons">
    //                 <div class="btn-group btn-group btn-group-justified" style="margin-bottom: 10px">
    //                     <input type="text" id="meter_'.$i.'" name="meter_'.$i.'" class="form-control meter-control"
    //                     tabindex="'.($i+2).'" value='.(isset($meter["$i"]) ? $meter["$i"] : '').'>
    //                 </div>
    //             </div>
    //         </div>
    //     </div>';
    echo'<div class="col-md-12 meter1" id="m'.$i.'">
            <div class="form-group ">
                <label class="control-label">Meter-'.$i.'</label>
                <div class="input-group">
                    <input type="text" id="meter_'.$i.'" name="meter_'.$i.'" class="form-control meter-control"
                                    tabindex="'.($i+2).'" value='.(isset($meter["$i"]) ? $meter["$i"] : '').'>
                    <span class="input-group-btn">
                        <button style="border-color: #c2cad8" class="btn default scan_barcode_'.$i.'" data-target_id="meter_'.$i.'" type="button">&nbsp;<i class="fa fa-barcode"></i>&nbsp;</button>
                    </span>
                </div>
            </div>
        </div>';
}
?>
<link href="../assets/layouts/layout/css/enclosure.css" rel="stylesheet" type="text/css" />

<div class="row form">
    <form action="edit_enclosure.php" mathod="POST" role="form" class="form-horizontal form-row-seperated" name="add_enclosure" id="add_enclosure" >
    <div class="col-lg-8">
        <div class="col-lg-12 wizard">



            <!-- Enclosure Barcode Search -->
            <?php if(!$id) { ?>
                <div class="tab-pane fade in" id="tab_add">
                    <div class="form-group ">
                        <label class="control-label"><?php echo $dictionary->GetValue("enclosure_id");?>:</label>
                        <div class="input-group">
                            <span class="input-group-btn">
                                <button class="btn default scan_barcode_e" data-target_id="enclosure_sn" type="button">&nbsp;<i class="fa fa-barcode"></i>&nbsp;</button>
                            </span>
                            <input type="text" id="enclosure_sn" name="sn" class="form-control" placeholder="<?php echo $dictionary->GetValue("enclosure_sn");?>">
                            <span class="input-group-btn">
                                <button class="btn green get_edit_enclosure" type="button">&nbsp;<i class="fa fa-check"></i>&nbsp;</button>
                            </span>
                        </div>
                    </div>
                </div>
            <?php } else { ?>
                <div class="well enclosure" style="background-color: #fff">
                    <div class="row meters row1">
                        <!-- Enclosure -->
                        <div class="col-md-12 meter1">
                            <div class="mt-body-actions-icons">
                                <div class="btn-group btn-group btn-group-justified" style="margin-bottom: 10px">
                                    <input class="form-control" type="text" id="enclosure_sn" name="enclosure_sn" autofocus placeholder="<?php echo $dictionary->GetValue("enclosure SN");?>"
                                    tabindex="1" value=<?php echo $enclosure_sn; ?>
                                    <?php echo ($id!=null ? 'disabled' : ''); ?> >
                                    <input type="hidden" name="enclosure_id" value="<?php echo $id;?>" >
                                    <input type="hidden" name="meter_type_id" value="<?php echo $meter_type_id;?>" >
                                </div>
                            </div>
                        </div>

                        <!-- Gateway -->
                        <?php //if($gateway_id) { ?>
                            <div class="col-md-12 meter1">
                                <div class="mt-widget-3">
                                    <div class="mt-head bg-white">
                                        <div class="mt-head-button"><?php print $dictionary->GetValue("gateway"); ?></div>
                                    </div>
                                    <div class="mt-body-actions-icons">
                                        <div class="btn-group btn-group btn-group-justified" style="margin-bottom: 10px">
                                            <div class="input-group">
                                            <input class="form-control" type="text" id="gateway_id" name="gateway_id" placeholder="<?php echo $dictionary->GetValue("Gateway SN");?>"  tabindex="2" value=<?php echo $gateway_id; ?> >
                                            <span class="input-group-btn">
                                                <button style="border-color: #c2cad8; width: 45px; height: 34px; padding-top: 5px;" class="btn default scan_barcode_g" data-target_id="gateway_id" type="button">&nbsp;<i class="fa fa-barcode fa-2x"></i>&nbsp;</button>
                                            </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>


                            <?php //} ?>

                            <!-- Meters -->
                            <?php
                            for($i=1;$i<=$no_of_meter;$i++){ //$no_of_meter
                                drawMeters($i,$meter);
                            }
                            ?>
                    </div>
                    <div class="row meters row2">
                        <?php
                        /*
                        for($i=4;$i<=6;$i++){
                            drawMeters($i,$meter);
                        }
                        */
                        ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <button type="button" class="btn btn-lg btn-block btn-danger pull-right update_enclosure" > <i class="fa fa-check"></i> <?php echo $dictionary->GetValue("Save");?></button>
                    </div>
                </div>

            <?php } ?>
        </div>
    </div>
    </form>
</div>
<script>
var scan_target_id = null;
$(document).ready(function() {

    $("body").on("click", ".scan_barcode_e, .scan_barcode_g, .scan_barcode_1, .scan_barcode_2, .scan_barcode_3, .scan_barcode_4, .scan_barcode_5, .scan_barcode_6", function() {
        scan_target_id = $(this).data("target_id");
        Android.scanBarcode();
    });

});

function SetBarcodeValue(sn){
    $("#"+scan_target_id).val(sn)
    $("#"+scan_target_id).focus();
}

</script>
<?php include '../include/footer.php'; ?>
<script src="js/installation.js"></script>
