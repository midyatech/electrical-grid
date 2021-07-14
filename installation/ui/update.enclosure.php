<?php

// error_reporting(E_ALL);
// ini_set('display_errors', 1);
require_once realpath(__DIR__ . '/../..').'/include/settings.php';
include_once realpath(__DIR__ . '/../..').'/include/checksession.php';
include_once realpath(__DIR__ . '/../..').'/include/checkpermission.php';
require_once realpath(__DIR__ . '/../..').'/class/Enclosure.class.php';
require_once realpath(__DIR__ . '/../..').'/class/Dictionary.php';
require_once realpath(__DIR__ . '/../..').'/class/Assembly.class.php';
require_once realpath(__DIR__ . '/../..').'/class/Helper.php';
$dictionary = new Dictionary ( $LANGUAGE );
$dictionary->GetAllDictionary ();
$Enclosure = new Enclosure( );
$Assembly = new Assembly();


$config = Helper::Request("configuration", true);
$change_reason = Helper::Request("change_reason", true);

$enclosure_id=$gateway_id=null;

$change_reasons = $Enclosure->GetTraceReasons();

$switch_meter =$switch_gateway=$switch_size =$switch_count = $enclosure_sn = $id = null;

$enclosure_sn = $_GET["sn"];
$enclosure = $Assembly->GetEnclosureAttributesBySN($enclosure_sn);

$id = $enclosure_id = $enclosure[0]["enclosure_id"];

$box_type_id = $enclosure[0]["box_type_id"];
$configurations = $Assembly->GetEnclosureConfigurations($box_type_id);


// $phase = $enclosure[0]["phase"];
// $enclosure_type = $enclosure[0]["enclosure_type"];
// $size = $enclosure[0]["enclosure_shape_id"];
$meter_type_id = $enclosure[0]["meter_type_id"];
$no_of_meter = $enclosure[0]["meter"];
$gateway = $enclosure[0]["gateway"];
$meter_configuration = $enclosure[0]["configuration_name"];
$enclosure_configuration_id = $enclosure[0]["enclosure_config_id"];
$assembly_order_id = $enclosure[0]["assembly_order_id"];

$form_disabled = 'disabled';

//special case, if we changed ecnlsure type/config get meter_configuration from selected value not from saved value
if ($config != null) {
    $configuration = $Enclosure->GetConfiguration($config);
    // print_r($configuration);
    $meter_configuration = $configuration[0]["configuration_name"];
    $meter_type_id = $configuration[0]["meter_type_id"];
    $no_of_meter = $configuration[0]["meter"];
    $gateway = $configuration[0]["gateway"];

    $form_disabled = false;
}


if ($meter_type_id ==1 && $no_of_meter > 3) {
    $meter_configuration = "111".$meter_configuration;
}



//enclosure meters array
$meter=array();
if ($enclosure_id != null && $config == null) {
    $enclosure_meters = $Enclosure->GetEnclosureDetails($enclosure_id);
    $gateway_sn = $enclosure_meters[0]["gateway_sn"];
    for($i=1; $i<=6; $i++){
        for ($j=0; $j<count($enclosure_meters); $j++) {
            $meter_id = null;
            if ($enclosure_meters[$j]["meter_sequence"] == $i) {
                $meter_id = $enclosure_meters[$j]["meter_sn"];
                $MeterSerialNumber = $enclosure_meters[$j]["meter_serial_number"];
                break;
            }
        }
        $meter["$i"] = $meter_id;
        if(isset($MeterSerialNumber)){
            $meter_serial_number["$i"] = $MeterSerialNumber;
        } else {
            $meter_serial_number["$i"] = "";
        }
    }
}



function DrawEnclosure($gateway, $meters_config, $meter_type_id)
{
    global $meter_serial_number;
    global $dictionary;
    global $meter;
    global $gateway_sn;
    global $form_disabled;

    if ($meter_type_id == 1) {
        $m_class = "col-sm-4";
        $img = "mk32h";
        $label = "RST";
    } else if ($meter_type_id == 2) {
        $m_class = "col-sm-6";
        $img = "mk10d";
        $label = "RST";
    } else {
        $m_class = "col-sm-12";
        $img = "mk10a";
    }


    $tabIndex = 0;
    if ($gateway == 1) {
        $tabIndex++;
        print '<div class="row"><div class="'.$m_class.' gateway"><div><img src="../img/meters/gw30.png" >';
        print '<span class="item_label">Gateway</span>';

        //print '<input class="form-control editable" type="text" id="gateway_id" name="gateway_id" autocomplete="off" placeholder="'.$dictionary->GetValue("Gateway SN").'" tabindex="'.$tabIndex.'" '. ($gateway_sn ? 'value='.$gateway_sn.' disabled' : '') .'>';

        print '<div class="input-group">';
        print '<input class="form-control editable" type="text" '.$form_disabled.' id="gateway_id" name="gateway_id" autocomplete="off" placeholder="'.$dictionary->GetValue("Gateway SN").'" tabindex="'.$tabIndex.'" '. ($gateway_sn ? 'value='.$gateway_sn : '') .'>';
        print '<span class="input-group-btn"><button class="btn btn-info scan_barcode" data-target_id="gateway_id" type="button"><i class="fa fa-barcode"></i></button></span>';
        print '</div>';

        //print '<a class="btn btn-sm enclosure_trace" data-id="275411" href="javascript:;" title="Trace"><i class="fa fa-crosshairs"></i> </a>';
        print '</div></div></div>';
    }
    $meters = str_split($meters_config);

    $meters_count = count($meters);

    print '<div class="row">';
    for ($i=0; $i<count($meters); $i++) {
        print '<div class="'.$m_class.' inactive meter"><div>';
        if ($meters[$i] == 0) {
            $disabled = true;
            print '<img src="../img/meters/noitem.jpg">';
            //print '<span class="item_label">Meter '.($i+1).'</span>';
        } else {
            ($meter[$i+1]) ? $disabled = true : $disabled = false;
            //$disabled = false;
            $tabIndex++;
            print '<img src="../img/meters/'.$img.'.png" >';
            print '<span class="item_label">Meter '.($i+1).'</span>';

            //print '<input type="text" id="meter_'.$i.'" name="meter_'.$i.'" '. ($disabled ? 'disabled' : '') .' class="form-control meter-control editable" autocomplete="off" tabindex="'.$tabIndex.'" value="'.$meter[$i+1].'">';
            print '<div class="input-group">';
            print '<input type="text" '.$form_disabled.' id="meter_'.$i.'" name="meter_'.$i.'" class="form-control meter-control editable" autocomplete="off" tabindex="'.$tabIndex.'" value="'.$meter[$i+1].'">';
            print '<span class="input-group-btn"><button class="btn btn-info scan_barcode" data-target_id="meter_'.$i.'" type="button"><i class="fa fa-barcode"></i></button></span>';
            print '</div>';

            //print '<a class="btn btn-sm enclosure_trace" data-id="275411" href="javascript:;" title="Trace"><i class="fa fa-crosshairs"></i> </a>';

            //print '<input type="text" '. ($disabled ? 'disabled' : '') .' class="form-control meter-control" value="SN:'.$meter_serial_number[$i+1].'">';
            print '<input type="text" readonly class="form-control meter-control" value="SN:'.$meter_serial_number[$i+1].'">';
        }
        print '</div></div>';
    }
    print '</div>';
}
?>
<style>
.meter, .gateway {
    text-align: center;
}

.meter > div .item_label, .gateway > div .item_label {
    display: none;
}

.meter > div .input-group-btn, .gateway > div .input-group-btn {
    display: none;
}

.gateway > div, .meter > div{
    max-width: 150px;
    border: solid 1px #bbb;
    margin: 20px 0;
    display: inline-block;
}
/* .meter > div{
    max-width: 150px;
    border: solid 1px #bbb;
    margin: 20px 0;
    display: inline-block;
} */

.enclosure_trace {
    position: absolute;
    right: 0;
    top: 150px;
    color: red;
}

.enclosurebox .enclosure_trace {
    position: absolute;
    right: 20px;
    top: 10px;
}
.meter > div, .gateway > div {
    position: relative;
}

.enclosurebox {
    position:relative;
}


@media(max-width:768px)
{
    .gateway > div, .meter > div{
        max-width: none;
        display:block;
        border-width: 0;
        margin: 10px 0;
    }

    .meter > div img, .gateway > div img {
        display: none;
    }
    .meter > div .item_label, .gateway > div .item_label {
        display: block;
    }


    .meter > div .input-group-btn, .gateway > div .input-group-btn {
        display: table-cell;
    }

    .meter, .gateway {
        text-align: left;
    }

    .input-lg {
        width: 100% !important;
    }
}
</style>
<?php
if ($enclosure) {
?>
    <div class="row form">
        <form action="code/enclosure.meter.insert.code.php" mathod="POST" role="form" class="form-horizontal form-row-seperated" name="add_meters" id="add_meters" >
            <div class="col-lg-3">
                <div class="well" style="background-color:#fff; border:solid 1px #999">
                    <div class="row">
                        <div class="col-lg-12">
                            <?php
                            $html->DrawFormField("label", "assembly_order_code", $enclosure[0]["assembly_order_code"], null, array());
                            $html->DrawFormField("label", "start_date", $enclosure[0]["create_date"], null, array());

                            $html->DrawFormField("label", "station", $enclosure[0]["station"], null, array());
                            $html->DrawFormField("label", "feeder", $enclosure[0]["feeder"], null, array());
                            $html->DrawFormField("label", "transformer", $enclosure[0]["transformer_number"], null, array());

                            $html->DrawFormField("label", "enclosure_type", $enclosure[0]["enclosure_type"]. " [".$enclosure[0]["configuration_name"]."]", null, array());
                            // $html->DrawFormField("label", "meter_type", $enclosure[0]["meter_type"], null, array());
                            // $html->DrawFormField("label", "meters_count", $enclosure[0]["meter"], null, array());

                            if ($enclosure[0]["gateway"]==1) {
                                $html->DrawFormField("label", "has_gateway", "Yes", null, array());
                            }
                            ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-9">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="well">
                            <div class="row">
                                <div class="col-lg-12">
                                    <?php
                                    $html->Select("change_reason", $change_reason, $change_reasons, ["class"=>"form-control", "optional"=>'Select Reason for Change..']);
                                    ?>
                                </div>
                                <div class="col-lg-12 enclosurebox">
                                    <div class="form-group">
                                        <input class="form-control input-lg" type="text" id="enclosure_sn" name="enclosure_sn" autofocus placeholder="<?php echo $dictionary->GetValue("enclosure SN");?>"
                                            tabindex="1" value=<?php echo $enclosure_sn; ?>
                                            <?php echo ($id!=null ? 'readonly' : ''); ?> >
                                    </div>
                                    <div class="form-group">
                                        <?php
                                            if ($form_disabled) {
                                                $options = ["class"=>"form-control", "disabled"=>$form_disabled];
                                            } else {
                                                $options = ["class"=>"form-control"];
                                            }
                                            $html->Select("enclosure_configuration_id", $enclosure_configuration_id, $configurations, $options);
                                        ?>
                                    </div>
                                    <input type="hidden" name="enclosure_id" id="enclosure_id" value="<?php echo $id;?>" >
                                    <input type="hidden" name="meter_type_id" value="<?php echo $meter_type_id;?>" >
                                    <!-- <a class="btn btn-sm enclosure_trace" data-id="275411" href="javascript:;" title="Trace"><i class="fa fa-crosshairs"></i> </a> -->
                                </div>
                            </div>
                            <?php
                            DrawEnclosure($gateway, $meter_configuration, $meter_type_id);
                            ?>
                        </div>
                    </div>
                </div>

                <div class="col-lg-12">
                    <div class="row">
                        <div class="col-md-3 pull-right">
                            <button type="button" name="enclosure_status" class="btn btn-lg btn-block btn-success save_meters col-md-3" value="3" > <i class="fa fa-check"></i> <?php echo $dictionary->GetValue("Save");?></button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
<?php
} else {
    ?>
    <div class="alert alert-danger">
    <?php echo $dictionary->GetValue("enclosure_not_found");?>
    </div>
    <?php
}
?>

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
</style>
<!-- <script src="../jquery.knob.min.js"></script> -->
<link href="../assets/global/plugins/bootstrap-switch/css/bootstrap-switch.min.css" rel="stylesheet" type="text/css">
<script src="../assets/global/plugins/bootstrap-switch/js/bootstrap-switch.min.js" type="text/javascript"></script>
<script>

    function EnteredMeterCount()
    {
        var count = 0;
        var d = 0;
        $('input.meter-control').each(function () {
            d++;
            if ($(this).val().length > 0) {
                count++;
            }
        });
        return count;
    }

    function RequiredMeterCount()
    {
        return $("input.meter-control").length;
    }

    function DisableSave(state)
    {
        if (state) {
            $(".save_meters").attr("disabled", "disabled");
        } else {
            $(".save_meters").removeAttr("disabled");
        }
    }

    function FocusFirstEmptyMeter()
    {
        $('input.meter-control').each(function () {
            if ($(this).val().length == 0) {
                $(this).focus();
                return false;
            }
        });
    }

    function FocusLastFilledMeter()
    {
        var errBeep = new Audio('../media/beep-4.wav');
        var last_id = -1;
        $('input.meter-control').each(function () {
            if ($(this).val().length > 0) {
                last_id = $(this).attr("id");
            }
        });
        errBeep.play();
        $("#"+last_id).focus();
    }

    function ValidateDuplicates(control)
    {
        var errBeep = new Audio('../media/beep-4.wav');

        $('input.editable[type=text]').each(function () {
            //loop over all inputs except current one
            if ($(this).attr("id") != $(control).attr("id")) {
                if ($(this).val() == $(control).val()) {
                    errBeep.play();
                    $(control).focus();
                    $(control).select();
                    return false;
                }
            }
        });
    }

    function ValidatCompletion(atuofocus)
    {
        console.log("validating..")
        valid = true;
        var errBeep = new Audio('../media/beep-4.wav');

        if ($("#enclosure_sn").val().length == 0) {
            valid = false;
            if (atuofocus) {
                errBeep.play();
                $("#enclosure_sn").focus();
            }
        }
        if ($('#gateway_switch').is(':checked') && $("#gateway_id").val().length == 0) {
            valid = false;
            if (atuofocus) {
                errBeep.play();
                $("#gateway_id").focus();
            }
        }
        if (EnteredMeterCount() != RequiredMeterCount()) {
            valid = false;
            if (atuofocus) {
                errBeep.play();
                FocusFirstEmptyMeter();
            }
        }

        if (!valid) {
            DisableSave(1);
            return false;
        } else {
            DisableSave(0);
            $('.save_meters').focus();
            return true;
        }
    }

    $(document).ready(function(){


        $("input[tabindex=1]").focus();

        var aoid = "<?php print $assembly_order_id; ?>";
        if (aoid != "") {
            $("#add_enclosure").load("ui/order.summary.php?aoid="+aoid);
        }


        var errBeep = new Audio('../media/beep-4.wav');

        //old default state load
        //ChangeVisibleMeterCount(3);
        //EnableCountButton(3);

        $("input[tabindex]").each(function () {
            $(this).on("keypress", function (event) {
                if( event.keyCode  == 13 || event.keyCode  == 17 || event.keyCode  == 74 ){
                    event.preventDefault();
                }

                if (event.keyCode  === 13)
                {
                    //jumping among text boxes if current filed is filled
                    if ($(this).val().length > 0) {
                        var nextElement = $('[tabindex="' + (this.tabIndex + 1) + '"]');
                        if (EnteredMeterCount() == RequiredMeterCount()) {
                            //try to focus the save only if all meters completed
                            //and othr validations passed

                            if (ValidatCompletion(true)) {
                                // console.log("document.activeElement")
                                // //$(".save_meters").click();
                                // $('[tabindex="9"]').focus();
                            }
                        } else {
                            if (EnteredMeterCount() < RequiredMeterCount()) {
                                //add more meters
                                if (nextElement.length) {
                                    if ($(nextElement).is(':visible')) {
                                        $('[tabindex="' + (this.tabIndex + 1) + '"]').focus();
                                    } else {
                                        $('[tabindex="' + (this.tabIndex + 2) + '"]').focus();
                                    }
                                    event.preventDefault();
                                } else {
                                    $('[tabindex="1"]').focus();
                                }
                            } else {
                                //just focust the last filled meter
                                FocusLastFilledMeter();
                            }
                        }
                        //return false;
                    } else {
                        //in case the current focust field is empty and we already have filled more meters than required
                        if (EnteredMeterCount() > RequiredMeterCount()) {
                            FocusLastFilledMeter();
                        }
                    }
                }
            });
        });

        $(".save_meters").on("mousedown", function(){
            //$(".save_meters").attr("disabled", "disabled");
            if(!ValidatCompletion(true)) {
                event.preventDefault();
            }
        })

        $(".save_meters").on("keydown", function(){
            if(!ValidatCompletion(true)) {
                event.preventDefault();
            }
        })

        $("#add_enclosure").validate({
            ignore: "",
            rules: {
                enclosure_sn: {
                required: true,
                },
                gateway_id: {
                required: true,
                }
            }
            ,
            highlight: function(element) {
                $(element).closest('.btn-group').addClass('has-error');
            },
            unhighlight: function(element) {
                $(element).closest('.btn-group').removeClass('has-error');
            },
            success: function(element) {
                $(element).closest('.btn-group').removeClass('has-error');
            },
            errorPlacement: function(error, element) {}
        });

        //select text on focus
        $("input[tabindex]").on("click", function(){
            $(this).select();
        });



        $("body ").on("change", "#change_reason", function() {
            $('#add_meters input:disabled, #add_meters select:disabled').each(function () {
                $(this).removeAttr('disabled');
            });
        });







    });
</script>

<script>
$(function() {
    $("body").on("click", ".enclosure_trace", function() {
        OpenModal("../supplychain/ui/trace.php");
    });

    $("body").on("click", ".scan_barcode", function() {
        scan_target_id = $(this).data("target_id");
        Android.scanBarcode();
    });

});

function SetBarcodeValue(sn){
    $("#"+scan_target_id).val(sn)
    $("#"+scan_target_id).focus();
}

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
</script>