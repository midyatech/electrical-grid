<?php
include '../include/header.php';
require_once realpath(__DIR__ . '/..').'/include/settings.php';
include_once realpath(__DIR__ . '/..').'/include/checksession.php';
include_once realpath(__DIR__ . '/..').'/include/checkpermission.php';
require_once realpath(__DIR__ . '/..').'/class/Enclosure.class.php';
require_once realpath(__DIR__ . '/..').'/class/Dictionary.php';
require_once realpath(__DIR__ . '/..').'/class/Assembly.class.php';
require_once realpath(__DIR__ . '/..').'/class/Helper.php';

$dictionary = new Dictionary ( $LANGUAGE );
$dictionary->GetAllDictionary ();
$Enclosure = new Enclosure( );
$Assembly = new Assembly();

require 'ui/assembly.breadcrumbs.php';


$filter = array();
$filter["user_id"] = $USERID;
$enclosure_id = null;

$user_task = $Assembly->GetCurrentTeamTask($filter);
if (!$user_task) {
    //print "IF";
    //$enclosure = $Assembly->GetTeamStack($filter, 1);
    $enclosure_configuration_id = Helper::Request("ecid");
    $assembly_order_id = Helper::Request("aoid");
    $transformer_id = Helper::Request("tid");
    $filter = [$enclosure_configuration_id, $assembly_order_id, $transformer_id];
    $enclosure = $Assembly->GetEnclosureTaskDetails($filter);
} else {
    $enclosure = $user_task;
}
if (isset($enclosure[0]["enclosure_id"])) {
    $enclosure_id = $enclosure[0]["enclosure_id"];
}

//print_r($enclosure);

$order = $Assembly->GetAssemblyOrder($assembly_order_id);
$is_extra_stock = $order[0]["is_extra_stock"];

$remaining_filter = [$assembly_order_id, $transformer_id, $enclosure_configuration_id];
$remaining_enclosure = $Enclosure->GetTransformerRemainingEnclosureCount($remaining_filter);

//$remaining_filter = [$assembly_order_id, $transformer_id, $enclosure_configuration_id];
//$reserved_enclosure = $Enclosure->GetTransformerReservedEnclosureCount($remaining_filter);
//print_r($reserved_enclosure);

$phase = $enclosure[0]["phase"];
$enclosure_type = $enclosure[0]["enclosure_type"];
$meter_type_id = $enclosure[0]["meter_type_id"];
$meter_type = $enclosure[0]["meter_type"];
$size = $enclosure[0]["enclosure_shape_id"];
$no_of_meter = $enclosure[0]["meter"];
$gateway = $enclosure[0]["gateway"];
$meter_configuration = $enclosure[0]["configuration_name"];
//$enclosure_configuration_id = $enclosure[0]["enclosure_config_id"];
//$assembly_order_id = $enclosure[0]["assembly_order_id"];
$transformer_number = $enclosure[0]["transformer_number"];
$transformer_id = $enclosure[0]["transformer_id"];


if ($meter_type_id ==1 && $no_of_meter > 3) {
    $meter_configuration = "111".$meter_configuration;
}

function DrawEnclosure($enclosure_type, $gateway, $meters_config, $meter_type_id)
{
    $meters = str_split($meters_config);
    $meters_count = count($meters);
    if ($meter_type_id == 1) {
        $m_class = "col-xs-4";
        $img = "mk32h";
        $label = "RST";
    } else if ($meter_type_id == 2) {
        $m_class = "col-xs-6";
        $img = "mk10d";
        $label = "RST";
    } else {
        $m_class = "col-xs-12";
        $img = "mk10a";
    }

    if ($gateway == 1) {
        print '<div class="row"><div class="'.$m_class.' gateway"><div><img src="../img/meters/gw30.png" ></div></div></div>';
    }

    print '<div class="row">';
    for ($i=0; $i<count($meters); $i++) {
        if ($meters[$i] == 0) {
            print '<div class="'.$m_class.' inactive meter"><div><img src="../img/meters/noitem.jpg"></div></div>';
        } else {
            print '<div class="'.$m_class.' active meter"><div><img src="../img/meters/'.$img.'.png" ></div></div>';
        }
    }
    print '</div>';
}



?>
<style>
.meter, .gateway {
    text-align: center;
}
.gateway div{
    max-width: 100px;
    border: solid 1px #bbb;
    margin: 10px;
    display: inline-block;
}
.meter div{
    max-width: 150px;
    border: solid 1px #bbb;
    margin: 20px 0 20px 0;
    display: inline-block;
    background: #fff;
}
.gateway div img{
    width: 100%;
}
.meter div img {
    width: 100%;
}
</style>
<div class="row">
    <?php
        if( $remaining_enclosure > 0  || $user_task || $is_extra_stock == 1) {
            if ($enclosure || $is_extra_stock == 1) {
                if ($enclosure[0]["team_name"] != "") {
                    echo "<h3 class='col-lg-6 col-md-offset-0 col-lg-offset-3'>".$enclosure[0]["team_name"]." \ ".$enclosure[0]["NAME"]."</h3>";
                }
            ?>
                <div class="well col-lg-6 col-md-offset-0 col-lg-offset-3">
                    <?php
                    DrawEnclosure($enclosure_type, $gateway, $meter_configuration, $meter_type_id);
                    ?>
                    <div class="row">
                        <div class="col-md-8 col-md-offset-2">
                            <br>
                            <?php
                            if (!$user_task) {
                                $add_enclosure_display = "block;";
                                $add_enclosure_sn_display = "none;";
                            } else {
                                $add_enclosure_display = "none;";
                                $add_enclosure_sn_display = "block;";
                            }
                            ?>

                            <form action="code/enclosure.insert.code.php" mathod="POST" role="form" class="form-horizontal form-row-seperated" style="display: <?php echo $add_enclosure_display;?>" name="add_enclosure" id="add_enclosure" >
                                <?php
                                $html->HiddenField("assembly_order_id", $assembly_order_id);
                                $html->HiddenField("enclosure_configuration_id", $enclosure_configuration_id);
                                $html->HiddenField("transformer_id", $transformer_id);
                                ?>
                                <div class="form-group alert">
                                    <div class="input-group" style="width: 100%">
                                        <button class="btn btn-block green insert_enclosure" type="button">&nbsp;<i class="fa fa-check"></i>&nbsp;<?php echo $dictionary->GetValue("build_enclosure");?></button>
                                    </div>
                                </div>
                            </form>
                            <form action="code/enclosure.save.code.php" mathod="POST" role="form" class="form-horizontal form-row-seperated" style="display: <?php echo $add_enclosure_sn_display;?>" name="add_enclosure_sn" id="add_enclosure_sn" >
                                <div class="form-group alert">
                                    <div class="input-group">
                                        <span class="input-group-btn">
                                            <button class="btn default scan_barcode_e" data-target_id="enclosure_sn" type="button">&nbsp;<i class="fa fa-barcode"></i>&nbsp;</button>
                                        </span>
                                        <input type="text" id="enclosure_sn" name="enclosure_sn" class="form-control" placeholder="<?php echo $dictionary->GetValue("enclosure_sn");?>" autofocus>
                                        <span class="input-group-btn">
                                            <button class="btn green save_enclosure_user" type="button">&nbsp;<i class="fa fa-check"></i>&nbsp;</button>
                                        </span>
                                    </div>
                                    <br/>
                                    <div class="input-group" style="width: 100%">
                                        <button class="btn btn-block red revocation_enclosure" id="revocation_enclosure" data-id="<?php print $enclosure_id ?>" type="button">&nbsp;<i class="fa fa-close"></i>&nbsp;<?php echo $dictionary->GetValue("revocation_enclosure");?></button>
                                    </div>
                                    <?php
                                        $html->HiddenField("assembly_order_id", $assembly_order_id);
                                        $html->HiddenField("enclosure_configuration_id", $enclosure_configuration_id);
                                        $html->HiddenField("transformer_id", $transformer_id);
                                        $html->HiddenField("enclosure_id", $enclosure_id);
                                    ?>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="col-lg-12 alert alert-info">
                        <div class="row col-lg-offset-2">
                            <span class="col-xs-6"><?php $html->DrawFormInput("label", "transformer_number", $dictionary->GetValue("transformer_number")." <br/> <b>".$transformer_number."</b>", NULL, NULL); ?></span>
                            <span class="col-xs-6"><?php $html->DrawFormInput("label", "meter_type", $dictionary->GetValue("meter_type")." <br/> <b>".$meter_type."</b>", NULL, NULL); ?></span>
                            <span class="col-xs-6"><?php $html->DrawFormInput("label", "enclosure_type", $dictionary->GetValue("enclosure_type")." <br/> <b>".$enclosure_type."</b>", NULL, NULL); ?></span>
                            <span class="col-xs-6"><?php $html->DrawFormInput("label", "meter_configuration", $dictionary->GetValue("meter_configuration")." <br/> <b>".$meter_configuration."</b>", NULL, NULL); ?></span>
                            <span class="col-xs-6"><?php $html->DrawFormInput("label", "assembly_order_id", $dictionary->GetValue("assembly_order_id")." <br/> <b>".$assembly_order_id."</b>", NULL, NULL); ?></span>
                        </div>
                    </div>
                </div>
            <?php
            } else {
                ?>
                <div class="alert alert-danger"><?php echo $dictionary->GetValue("you_dont_have_tasks");?></div>
                <?php
            }
        } else {
            print "@";
            ?>
            <div class="col-md-12">
                <div class="alert alert-danger">
                    <?php echo $dictionary->GetValue("configuration_assembly_completed");?>
                </div>
            </div>
            <div class="col-md-4 col-md-offset-4">
                <button class="btn btn-lg btn-block blue back_to_transformer" type="button" data-aoid="<?php print $_GET["aoid"]; ?>" data-aoid="<?php print $transformer_id; ?>" data-tid="<?php print $transformer_id; ?>">&nbsp;<i class="fa fa-arrow-left"></i>&nbsp;&nbsp;&nbsp;<?php echo $dictionary->GetValue("back_to_transformer");?></button>
            </div>
            <?php
        }
        ?>
</div>
<?php
include '../include/footer.php';
?>
<script src="js/enclosure.js?v=4"></script>
<script>

$("body").on("click", ".scan_barcode_e", function() {
    Android.scanBarcode();
});


function SetBarcodeValue(sn){
    $("#enclosure_sn").val(sn)
    $("#enclosure_sn").focus();
}
</script>