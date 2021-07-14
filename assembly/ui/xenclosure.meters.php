<?php
require_once realpath(__DIR__ . '/../..').'/include/settings.php';
include_once realpath(__DIR__ . '/../..').'/include/checksession.php';
include_once realpath(__DIR__ . '/../..').'/include/checkpermission.php';
require_once realpath(__DIR__ . '/../..').'/class/Enclosure.class.php';
require_once realpath(__DIR__ . '/../..').'/class/Dictionary.php';
require_once realpath(__DIR__ . '/../..').'/class/Assembly.class.php';
$dictionary = new Dictionary ( $LANGUAGE );
$dictionary->GetAllDictionary ();
$Enclosure = new Enclosure( );
$Assembly = new Assembly();

$enclosure_id=$gateway_id=null;
$operation="insert";
//$meter=array();
$switch_meter =$switch_gateway=$switch_size =$switch_count = $enclosure_sn = $id = null;

//if (isset($_GET["sn"]) && $_GET["sn"] != ""){
    $enclosure_sn = $_GET["sn"];
    $enclosure = $Assembly->GetEnclosureAttributesBySN($enclosure_sn);
    $id = $enclosure[0]["enclosure_id"];

    $phase = $enclosure[0]["phase"];
    $enclosure_type = $enclosure[0]["enclosure_type"];
    $meter_type_id = $enclosure[0]["meter_type_id"];
    $size = $enclosure[0]["enclosure_shape_id"];
    $no_of_meter = $enclosure[0]["meter"];
    $gateway = $enclosure[0]["gateway"];
    $meter_configuration = $enclosure[0]["configuration_name"];
    $enclosure_configuration_id = $enclosure[0]["enclosure_config_id"];
    $assembly_order_id = $enclosure[0]["assembly_order_id"];
    if ($meter_type_id ==1 && $no_of_meter > 3) {
        $meter_configuration = "111".$meter_configuration;
    }

/*
} else {
    $enclosure_sn = $id = $phase = $enclosure_type = $meter_type_id = $size = $no_of_meter = $gateway = $meter_configuration = $enclosure_configuration_id = $assembly_order_id = "";
    $enclosure[0]["assembly_order_code"] = $enclosure[0]["create_date"] = $enclosure[0]["station"] = $enclosure[0]["feeder"] = $enclosure[0]["transformer_number"] = $enclosure[0]["enclosure_type"] = $enclosure[0]["configuration_name"] = $enclosure[0]["gateway"] = "";
}
*/
function drawMeters($i, $meter, $img){
    echo'<div class="col-md-4 meter" id="m'.$i.'">
            <div class="mt-widget-3">
                <div class="mt-head bg-white">

                    <div class="mt-head-button">
                    '.$i.'<img src="../img/meter-bw.png" class="meter-img">
                    </div>
                </div>
                <div class="mt-body-actions-icons">
                    <div class="btn-group btn-group btn-group-justified">
                        <input type="text" id="meter_'.$i.'" name="meter_'.$i.'" class="form-control meter-control"
                        tabindex="'.($i+2).'" value='.(isset($meter["$i"]) ? $meter["$i"] : '').'>
                    </div>
                </div>
            </div>
        </div>';
}


function DrawEnclosure($gateway, $meters_config, $meter_type_id)
{
    global $meter_serial_number;
    global $dictionary;
    global $meter;
    global $gateway_sn;

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


    $tabIndex = 0;
    if ($gateway == 1) {
        $tabIndex++;
        print '<div class="row"><div class="'.$m_class.' gateway"><div><img src="../img/meters/gw30.png" >
                <input class="form-control editable" type="text" id="gateway_id" name="gateway_id" placeholder="'.$dictionary->GetValue("Gateway SN").'" tabindex="'.$tabIndex.'" '. ($gateway_sn ? 'value='.$gateway_sn.' disabled' : '') .'>
                </div></div></div>';
    }
    $meters = str_split($meters_config);

    //$meters_count = count($meters);
    print '<div class="row">';
    for ($i=0; $i<count($meters); $i++) {
        print '<div class="'.$m_class.' inactive meter"><div>';
        if ($meters[$i] == 0) {
            $disabled = true;
            print '<img src="../img/meters/noitem.jpg">';
        } else {
            ($meter[$i+1]) ? $disabled = true : $disabled = false;
            $disabled = false;
            $tabIndex++;
            print '<img src="../img/meters/'.$img.'.png" >';
            print '<input type="text" id="meter_'.$i.'" name="meter_'.$i.'" '. ($disabled ? 'disabled' : '') .' class="form-control meter-control editable" tabindex="'.$tabIndex.'" value="'.$meter[$i+1].'">';
            if ($meter_serial_number[$i+1] != "") {
                print '<input type="text" '. ($disabled ? 'disabled' : '') .' class="form-control meter-control" value="SN:'.$meter_serial_number[$i+1].'">';
            }
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
.gateway div{
    max-width: 150px;
    border: solid 1px #bbb;
    margin: 10px;
    display: inline-block;
}
.meter div{
    max-width: 150px;
    border: solid 1px #bbb;
    margin: 20px 0 20px 0;
    display: inline-block;
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
                                <input class="form-control input-lg" type="text" id="enclosure_sn" name="enclosure_sn" autofocus placeholder="<?php echo $dictionary->GetValue("enclosure SN");?>"
                                    tabindex="1" value=<?php echo $enclosure_sn; ?>
                                    <?php echo ($id!=null ? 'disabled' : ''); ?> >
                                <input type="hidden" name="enclosure_id" id="enclosure_id" value="<?php echo $id;?>" >
                                <input type="hidden" name="meter_type_id" value="<?php echo $meter_type_id;?>" >
                            </div>
                        <?php
                        DrawEnclosure($gateway, $meter_configuration, $meter_type_id);
                        ?>
                        </div>
                    </div>
                </div>
            </div>

            <?php if( ! isset($_GET["id"]) ) { ?>
                <div class="col-lg-12">
                    <div class="row">
                        <?php /*
                        <div class="col-md-3">
                            <a href="add_enclosure.php" class="btn btn-block btn-lg default" style="margin-bottom: 10px"><i class="fa fa-plus"></i> <?php echo $dictionary->GetValue("add_new");?></a>
                        </div>
                        <div class="col-md-3">
                            <a href="add_enclosure.php<?php echo ($id!=null ? '?id='.$id: '');?>" class="btn btn-block btn-lg default" style="margin-bottom: 10px"><i class="fa fa-refresh"></i> <?php echo $dictionary->GetValue("reload");?></a>
                        </div>
                        <div class="col-md-3">
                            <a href="add_enclosure.php?id=-1" class="btn btn-block btn-lg default"><i class="fa fa-arrow-left"></i> <?php echo $dictionary->GetValue("Back_to_Last_Enclosure");?></a>
                        </div>
                        <div class="col-md-3 col-xs-offset-9">
                            <button type="button" class="btn btn-lg btn-block btn-danger save_meters" disabled > <i class="fa fa-check"></i> <?php echo $dictionary->GetValue("Save");?></button>
                        </div>
                        */ ?>
                        <!--div class="col-md-3">
                            <button type="button" name="enclosure_status" class="btn btn-lg btn-block btn-danger failed_meters col-md-3" value="2" > <i class="fa fa-warning"></i> <?php //echo $dictionary->GetValue("failed");?></button>
                        </div-->
                        <div class="col-md-3 pull-right">
                            <button type="button" name="enclosure_status" class="btn btn-lg btn-block btn-success save_meters col-md-3" value="3" > <i class="fa fa-check"></i> <?php echo $dictionary->GetValue("Save");?></button>
                        </div>
                    </div>
            </div>
            <?php } ?>
        </div>
        </form>
    </div>

    <div class="row"  style="margin-top: 20px">
        <div class="col-xs-12">
            <section id="add_enclosure"></section>
        </div>
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
.e_name {

}
</style>
<script src="../jquery.knob.min.js"></script>
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

        /*$("input[tabindex]").on("blur", function(){

            //check duplicate values
            if ($(this).val() != "") {
                ValidateDuplicates($(this));
            }

            //check missing values
            ValidatCompletion(false);
            return true;
        });*/

    });
</script>