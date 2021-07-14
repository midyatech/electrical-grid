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
$meter=array();

$switch_meter =$switch_gateway=$switch_size =$switch_count = $enclosure_sn = $id = null;


if (isset($_GET["id"]) && $_GET["id"] != ""){
    $enclosure_id = $_GET["id"];
}
if ($enclosure_id ==-1) {
    //get last inserted enclosure
    $enclosure_id = $Enclosure->GetLastEnclosureByUser($USERID);
}
if (isset($enclosure_id)) {
    $enclosure = $Enclosure->GetEnclosureDetails($enclosure_id);
    $id = $enclosure[0]["enclosure_id"];
}

if (isset($_GET["aoid"])) {
    $aoid = $_GET["aoid"];
    $order = $Assembly->GetAssemblyOrder($aoid);
    //print_r($order);
    $project_items = $Assembly->getAssemblyOrderItems($aoid);
}


$filter = array();
$filter["assembly_order_id"]= $aoid;
$filter["user_id"] = $USERID;
$work_item = $Assembly->GetTeamStack($filter, 1);
print_r($work_item);


$no_of_meter = 3;
if (isset($enclosure) && $enclosure) {
    $operation="edit";

    $enclosure_sn = $enclosure[0]["enclosure_sn"];
    $switch_meter = $enclosure[0]["meter_type_id"];

    $gateway_id = $enclosure[0]["gateway_id"];
    if( $gateway_id ){
        $switch_gateway = 1;
    } else {
        $switch_gateway = 0;
    }

    $switch_count = count($enclosure);

    if( $switch_count > 3 ){
        $switch_size = 1;
        $no_of_meter = 6;
    } else {
        $switch_size = 0;
        if($enclosure[0]["meter_type_id"] == 1 ){
            $no_of_meter = 3;
        } else if($enclosure[0]["meter_type_id"] == 2){
            $no_of_meter = 2;
        } else if($enclosure[0]["meter_type_id"] == 3){
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

} else {
    if (isset($_SESSION["switch_meter"])) {
        $switch_meter = $_SESSION["switch_meter"];
    }
    if (isset($_SESSION["switch_gateway"])) {
        $switch_gateway = $_SESSION["switch_gateway"];
    }
    if (isset($_SESSION["switch_size"])) {
        $switch_size = $_SESSION["switch_size"];
    }
    if (isset($_SESSION["switch_count"])) {
        $switch_count = $_SESSION["switch_count"];
    }
}

// print $switch_meter;
// print $switch_gateway;
// print $switch_size;
// print $switch_count;
//print_r($meter);

function drawMeters($i, $meter){
    global $dictionary;
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
?>
<div class="row">
    <div class="col-lg-12">
        <div class="well">
            <div class="row">
                <div class="col-lg-4">
                    <?php $html->DrawFormField("label", "assembly_order_code", $order[0]["assembly_order_code"], null, $options);?>
                </div>
                <div class="col-lg-4">
                    <?php $html->DrawFormField ("label", "start_date", $order[0]["start_date"], null, array() );?>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row form">
    <form action="code/enclosure.insert.code.php" mathod="POST" role="form" class="form-horizontal form-row-seperated" name="add_enclosure" id="add_enclosure" >
    <div class="col-lg-3">
        <div class="well" style="margin-top:81px; background-color:#fff; border:solid 1px #999">
            <div class="row">
                <div class="col-lg-12">
                    <?php
                    //print_r($work_item);
                    $html->DrawFormField("label", "enclosure_type", $work_item[0]["enclosure_type"], null, array());
                    $html->DrawFormField("label", "meters_count", $work_item[0]["meter_type"], null, array());
                    $html->DrawFormField("label", "meters_count", $work_item[0]["meter"], null, array());

                    if ($work_item[0]["gateway"]==1) {
                        $html->DrawFormField("label", "enclosure_type", "Yes", null, array());
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-9">
        <div class="col-lg-12 wizard">
            <div class="row">
                <div class="col-lg-6 col-md-offset-3">
                    <div class="chat-form">
                        <div class="col-lg-12">
                            <input class="form-control input-lg" type="text" id="enclosure_sn" name="enclosure_sn" autofocus placeholder="<?php echo $dictionary->GetValue("enclosure SN");?>"
                                tabindex="1" value=<?php echo $enclosure_sn; ?>
                                <?php echo ($id!=null ? 'disabled' : ''); ?> >
                            <input type="hidden" name="enclosure_id" value="<?php echo $id;?>" >
                        </div>
                    </div>
                </div>
            </div>

            <div class="well enclosure">
                <div class="row gateway">
                    <div class="col-md-10 col-lg-4 col-md-offset-1 col-lg-offset-4">
                        <div class="chat-form option2">
                            <div class="col-lg-4">
                                <img src="../img/wifi-router-bw.png" style="width:100%; max-width: 100px">
                            </div>
                            <div class="col-lg-8">
                                <input class="form-control" type="text" id="gateway_id" name="gateway_id" placeholder="<?php echo $dictionary->GetValue("Gateway SN");?>" style="margin-top:10px"  tabindex="2" value=<?php echo $gateway_id; ?> >
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row meters row1">
                    <?php
                    for($i=1;$i<=3;$i++){ //$no_of_meter
                        drawMeters($i,$meter);
                    }
                    ?>
                </div>
                <div class="row meters row2">
                    <?php
                    for($i=4;$i<=6;$i++){
                        drawMeters($i,$meter);
                    }
                    ?>
                </div>
            </div>
            <div class="row">
                <div class="col-md-3">
                    <a href="add_enclosure.php" class="btn btn-block btn-lg default" style="margin-bottom: 10px"><i class="fa fa-plus"></i> <?php echo $dictionary->GetValue("add_new");?></a>
                </div>
                <div class="col-md-3">
                    <a href="add_enclosure.php<?php echo ($id!=null ? '?id='.$id: '');?>" class="btn btn-block btn-lg default" style="margin-bottom: 10px"><i class="fa fa-refresh"></i> <?php echo $dictionary->GetValue("reload");?></a>
                </div>
                <div class="col-md-3">
                    <a href="add_enclosure.php?id=-1" class="btn btn-block btn-lg default"><i class="fa fa-arrow-left"></i> <?php echo $dictionary->GetValue("Back_to_Last_Enclosure");?></a>
                </div>
                <div class="col-md-3">
                    <button type="button" class="btn btn-lg btn-block btn-danger save_enclosure" > <i class="fa fa-check"></i> <?php echo $dictionary->GetValue("Save");?></button>
                </div>
            </div>
        </div>
    </div>
    </form>
</div>


<?php
function drawKnob($data)
{
    $name = $data["enclosure_type"];
    $id = $data["enclosure_type_id"];
    $knobMax = $max = $data["enclosures_count"];
    $knobVal = $val = $data["manufactured_count"] != null ? $data["manufactured_count"] : 0;
    if ($val > $max) {
        $knobMax = $knobVal;
    }

    $percentage = $val*100/$max;
    if ($percentage > 100) {
        $color = "#ff0000";
    } else {
        if ($percentage >= 75) {
            $color = "#00F729";
        } else if ($percentage >= 50) {
            $color = "#F7C000";
        } else {
            $color = "#F75800"; //";
        }
    }
    echo '<div class="col-lg-1 col-md-2 col-sm-3 col-xs-4 knob-wrapper">
            <div>'.$name.'</div>
            <input type="text" value="'.$knobVal.'" class="knob" id="'.$id.'" data-max="'.$knobMax.'" data-fgColor="'.$color.'"
                    data-angleOffset="-125" data-angleArc="250" data-rotation="clockwise" data-width="98%" readonly>
            <div class="knob-text">'.$val .'/'.$max.'</div>
        </div>';
}



?>

<div class="row form">
    <div class="col-lg-12">
        <div class="well" style="background-color:#fff; border:solid 1px #999; padding: 20px !important; border: solid 1px silver;margin-top: 20px;">
            <div class="row">
                <div class="col-lg-12">
                    <div class="row">
                        <?php
                        for ($i=0; $i<count($project_items); $i++) {
                            drawKnob($project_items[$i]);
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
</div>

<script>
    $(function() {
        $(".knob").knob();
    });
</script>

<script>

    function ChangeVisibleMeterCount(count) {
        $('#m1, #m2, #m3, #m4, #m5, #m6').hide();
        for (i=1; i<=count; i++) {
            $("#m"+i).show();
        }
    }

    function EnableCountButton(count, direction, resetvalues=0) {

        direction = direction || 1;
        resetvalues = resetvalues || 0;
        $(".mc_1, .mc_2, .mc_3, .mc_4, .mc_5, .mc_6").show();

        if (direction == 1) {
            //to hide all buttons after {count}
            for (i=6; i>count; i--) {
                $(".mc_"+i).hide();
                if (resetvalues==1) {
                    $("#meter_"+i).val('');
                }
            }
        }
        if (direction == -1) {
            //mainly to hide the button 1,2,3
            for (i=count; i>0; i--) {
                $(".mc_"+i).hide();
                if (resetvalues==1) {
                    $("#meter_"+i).val('');
                }
            }
        }
    }

    function ResetSelectedCount(selected) {
        selected = selected || 1;
        $("#mc_1, #mc_2, #mc_3, #mc_4, #mc_5, #mc_6").prop('checked', true);
        $(".mc_1, .mc_2, .mc_3, .mc_4, .mc_5, .mc_6").removeClass("active");


        $('#mc_'+selected).prop('checked', true);
        $(".mc_"+selected).addClass("active");

        //$("#meter_1, #meter_2, #meter_3, #meter_4, #meter_5, #meter_6").val('');

        //ChangeVisibleMeterCount(1);

        // selValue = $('input[name=meter_count]:checked').val();
        // alert(selValue)
    }

    function DisableSwitch(switch_id, state)
    {
        if (state==1) {
            //enable
            $("#"+switch_id).removeAttr("disabled");
            $('#'+switch_id).parent().removeClass("disabled");
        } else {
            //disabl
            $("#"+switch_id).attr("disabled", "disabled");
            $('#'+switch_id).prop('checked', false);
            $('#'+switch_id).parent().addClass("disabled");
        }
    }

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
        return $("input[name='meter_count']:checked").val();
    }

    function DisableSave(state)
    {
        if (state) {
            $(".save_enclosure").attr("disabled", "disabled");
        } else {
            $(".save_enclosure").removeAttr("disabled");
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

        $('input[type=text]').each(function () {
            //loop over all inputs except current one
            if ($(this).attr("id") != $(control).attr("id")) {
                if ($(this).val() == $(control).val()) {
                    errBeep.play();
                    $(control).focus();
                    return false;
                }
            }
        });
    }

    function GatewayState()
    {
        if($('#gateway_switch').is(':checked')) {
            $(".row.gateway").show();
        } else {
            $(".row.gateway").hide();
        }
    }

    function PhaseState(resetvalues)
    {
        resetvalues = resetvalues || 0;
        if ($('#phase_switch').is(':checked')) {
            //three phase
            DisableSwitch("size_switch", 0);
            //enable meter type
            DisableSwitch("meter_switch", 1);
            //allow two meters only
            EnableCountButton(2, 1, resetvalues);

            ChangeVisibleMeterCount(2);

            //select one meter by default
            ResetSelectedCount();

        } else {
            //default single phase
            DisableSwitch("size_switch", 1);
            //disable ct meter
            DisableSwitch("meter_switch", 0);
            //enable gateway
            DisableSwitch("gateway_switch", 1);

            //buttons and visible depend on size
            if ($('#size_switch').is(':checked')) {
                //large enclosure
                ChangeVisibleMeterCount(6);
                EnableCountButton(3, -1, resetvalues)
                ResetSelectedCount(4);
            } else {
                //small enclosure count (3 meters)
                EnableCountButton(3);
                ChangeVisibleMeterCount(3);
            }
        }
    }

    function MeterTypeState(resetvalues)
    {
        resetvalues = resetvalues || 0;
        if($('#meter_switch').is(':checked')) {
            //three phase ct meter
            ChangeVisibleMeterCount(1);
            EnableCountButton(1, 1 , resetvalues);
            ResetSelectedCount();

            //disable gateway switch
            DisableSwitch("gateway_switch", 0);
            //hide gateway field
            $(".row.gateway").hide();
        } else {
            //customer meter
            //either single phase or three phase

            if ($('#phase_switch').is(':checked')) {
                //three phase
                //three phase normal
                ChangeVisibleMeterCount(2);
                EnableCountButton(2, 1, resetvalues);
                //enable gateway switch
                DisableSwitch("gateway_switch", 1);
            } else {
                //do nothing, it should be handeled by phase state
            }
        }
    }

    function SizeState(resetvalues)
    {
        resetvalues = resetvalues || 0;
        if($('#size_switch').is(':checked')) {
            //large enclosure
            ChangeVisibleMeterCount(6);
            EnableCountButton(3, -1, resetvalues)
            ResetSelectedCount(4);

        } else {
            //small enclosure
            ChangeVisibleMeterCount(3);
            EnableCountButton(3, 1, resetvalues);
            ResetSelectedCount();
        }
    }

    function IinitFields(meter_count)
    {
        GatewayState();
        SizeState(0);
        PhaseState(0);
        MeterTypeState(0);
        //Select count of meters
        ResetSelectedCount(meter_count);
    }


    function ValidatCompletion(atuofocus)
    {
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
            $('.save_enclosure').focus();
            return true;
        }
    }

    $(document).ready(function(){

        var meter_count = "<?php echo $switch_count;?>";
        IinitFields(meter_count);


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
                                // //$(".save_enclosure").click();
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

        $(".save_enclosure").on("mousedown", function(){
            //$(".save_enclosure").attr("disabled", "disabled");
            if(!ValidatCompletion(true)) {
                event.preventDefault();
            }
        })

        $(".save_enclosure").on("keydown", function(){
            if(!ValidatCompletion(true)) {
                event.preventDefault();
            }
        })

        $('#phase_switch').change(function() {
            PhaseState(1);
        });

        $('#meter_switch').change(function() {
            MeterTypeState(1);
        });

        $('#size_switch').change(function() {
            SizeState(1);
        });

        $('#gateway_switch').change(function() {
            GatewayState()
        });

        $('#mc_1, #mc_2, #mc_3, #mc_4, #mc_5, #mc_6').change(function() {
            if (! $(this)[0].hasAttribute("disabled")) {
                count = $(this).val();
                //ChangeVisibleMeterCount(count);
            }
        });


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

        $("input[tabindex]").on("blur", function(){

            //check duplicate values
            if ($(this).val() != "") {
                ValidateDuplicates($(this));
            }

            //check missing values
            ValidatCompletion(false);
            return true;
        });

    });
</script>