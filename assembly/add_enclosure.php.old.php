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

?>
<style>
    .enclosure{
        border: 2px solid #666;
        border-radius: 15px !important;
    }
    .col{
        padding-right:100px;
        padding-left:100px;
    }
    .chat-form{
        background-color: #ccc !important;
    }
    .chat-form.option2{
        background-color: #fff !important;
        border: 1px solid #ccc;
    }
    .mt-widget-3{
        background-color:#fff;
    }
    .mt-widget-3 .mt-head{
        margin-bottom: 0;
    }
    .mt-widget-3 .mt-head .mt-head-button{
        padding: 0;
    }
    .form-control{
        padding-left: 2px;
        padding-right: 2px;
    }
    .col-8{
        width: 12.5%;
        float: left;
        padding: 0 10px;
    }
    .mt-body-actions-icons{
        padding: 0 6px;
    }
    .well{
        padding-left: 5px;
        padding-right: 5px;
    }
    .less-padding {
        padding: 2px !important;
        margin: 2px !important;
    }
    .mt-action-buttons .btn {
        padding: 6px 2px;
    }
</style>
<?php
$enclosure_id=$gateway_id=$phase=$meter_id=$ph_1=$ph_2=$ph_3=$ph_4=$ph_5=$ph_6=$ph_7=$ph_8=null;
$ph3_1=$ph3_3=null;
$operation="insert";
$meter=array();
$ph=array();
for($i=0;$i<9;$i++){
        $ph[$i]=1;
    }
if (isset($_GET["enclosure_id"]) && $_GET["enclosure_id"] != ""){
    $enclosure_id = $_GET["enclosure_id"];
}
$enclosure = $Enclosure->GetEnclosureDetails($enclosure_id);
if($enclosure!=null){
    $enclosure_id=$enclosure[0]["enclosure_id"];
    $gateway_id=$enclosure[0]["gateway_id"];
    //$meter_count=$enclosure[0]["meter_count"];
    $meter_sequence=$enclosure[0]["meter_sequence"];
    $operation="edit";
    for($i=0;$i<count($enclosure);$i++){
        switch ($enclosure[$i]["meter_sequence"]) {
            case 1:
                $ph[1]=$enclosure[$i]["phase"];
                $meter[1]=$enclosure[$i]["meter_id"];
                break;
            case 2:
                $ph[2]=$enclosure[$i]["phase"];
                $meter[2]=$enclosure[$i]["meter_id"];
                break;
            case 3:
                $ph[3]=$enclosure[$i]["phase"];
                $meter[3]=$enclosure[$i]["meter_id"];
                break;
            case 4:
                $ph[4]=$enclosure[$i]["phase"];
                $meter[4]=$enclosure[$i]["meter_id"];
                break;
            case 5:
                $ph[5]=$enclosure[$i]["phase"];
                $meter[5]=$enclosure[$i]["meter_id"];
                break;
            case 6:
                $ph[6]=$enclosure[$i]["phase"];
                $meter[6]=$enclosure[$i]["meter_id"];
                break;
            case 7:
                $ph[7]=$enclosure[$i]["phase"];
                $meter[7]=$enclosure[$i]["meter_id"];
                break;
            case 8:
                $ph[8]=$enclosure[$i]["phase"];
                $meter[8]=$enclosure[$i]["meter_id"];
                break;
        }
    }
}

function drawMeters($i,$meter,$ph){
    global $dictionary;
    echo'<div class="col--8 col-md-1 less-padding">
            <div class="mt-widget-3">
                <div class="mt-head bg-white">
                    <img src="../img/meter-bw.png" width="100" style=" display:block;margin:auto;">
                    <div class="mt-head-button">
                        <div class="mt-action-buttons ">
                                <div class="btn-group" data-toggle="buttons">
                                <label class="btn red btn-outline ';
                                echo ($ph[$i]==3)? 'active' : '';
                                echo '">
                                <input type="radio" name="ph'.$i.'" id="ph3_'.$i.'" value="3" autocomplete="off" ';
                                echo ($ph[$i]==3) ? 'checked' : '';
                                echo '>';
                                echo $dictionary->GetValue("Three_Phase");
                                echo '</label>
                                <label class="btn blue btn-outline ';
                                echo ($ph[$i]==1) ? 'active' : '';
                                echo '">
                                    <input type="radio" name="ph'.$i.'" id="ph1_'.$i.'" value="1" autocomplete="off" ';
                                    echo ($ph[$i]==1) ? 'checked' : '';
                                    echo '> ';
                                    echo $dictionary->GetValue("Single_Phase");
                                echo '</label>
                            </div>

                        </div>
                    </div>
                </div>
                <div class="mt-body-actions-icons">
                    <div class="btn-group btn-group btn-group-justified">
                        <input type="text" name="meter_'.$i.'" class="form-control"
                        tabindex="'.($i+2).'"'. ((!isset($meter[$i]) || is_null($meter[$i])) ? ' disabled ' : ''). ' value='.(isset($meter[$i]) ? $meter[$i] : '').'>
                    </div>
                </div>
            </div>
        </div>';
}
?>
<div class="row form">
    <form action="code/enclosure.insert.code.php" mathod="POST" role="form" class="form-horizontal form-row-seperated" name="add_enclosure" id="add_enclosure" >
        <div class="col-lg-12 wizard">
            <div class="row">
                <div class="col-lg-6 col-md-offset-3">
                    <div class="chat-form">
                        <div class="col-lg-12">
                            <input class="form-control input-lg" type="text" id="enclosure_id" name="enclosure_id" autofocus placeholder="<?php echo $dictionary->GetValue("enclosure SN");?>" tabindex="1" value=<?php echo $enclosure_id; ?> >
                        </div>
                    </div>
                </div>
            </div>

            <div class="well enclosure">
                <div class="row">
                    <div class="col-lg-4 col-md-offset-4">
                        <div class="chat-form option2">
                            <div class="col-lg-4">
                                <img src="../img/wifi-router-bw.png" width="100">
                            </div>
                            <div class="col-lg-8">
                                <input class="form-control" type="text" id="gateway_id" name="gateway_id" placeholder="<?php echo $dictionary->GetValue("Gateway SN");?>" style="margin-top:34px"  tabindex="2" disabled="disabled" value=<?php echo $gateway_id; ?> >
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-12" style="height: 40px">
                            &nbsp;
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-2"></div>
                    <?php
                    for($i=1;$i<9;$i++){
                        drawMeters($i,$meter,$ph);
                    }
                    ?>
                    <div class="col-md-2"></div>
                </div>

            </div>

            <div class="row">
                <div class="col-md-12">
                    <?php $html->HiddenField ("operation", $operation);?>
                    <?php $html->HiddenField ("id", $enclosure_id);?>
                    <button type="button" class="btn btn-danger pull-right save_enclosure" > <i class="fa fa-check"></i> <?php echo $dictionary->GetValue("Save");?></button>
                </div>
            </div>

        </div>
    </form>
</div>

<script>
$(document).ready(function(){




    $("#add_enclosure").validate({
        ignore: "",
        rules: {
            enclosure_id: {
            required: true,
            number: true
            },
            gateway_id: {
            required: true,
            number: true
            },
            meter_1: {
            number: true
            },
            meter_2: {
            number: true
            },
            meter_3: {
            number: true
            },
            meter_4: {
            number: true
            },
            meter_5: {
            number: true
            },
            meter_6: {
            number: true
            },
            meter_7: {
            number: true
            },
            meter_8: {
            number: true
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

    //enclosure and gateway text boxes
    if($('[tabindex=1]').val().length>0){
        $('[tabindex=1]').attr('disabled',true);
        $('[tabindex=2]').attr('disabled',false);
    }

    //enable or disable next meter
    // $("input[tabindex]").on("change", function(){
    //     if($('[tabindex="' + (this.tabIndex) + '"]').val().length>0)
    //     {
    //         $('[tabindex="' + (this.tabIndex + 1) + '"]').attr('disabled',false);
    //         $('[tabindex="' + (this.tabIndex + 1) + '"]').focus();
    //     }
    //     else
    //     {
    //         for(var i = 1; i < 9; i++ )
    //             {
    //                 $('[tabindex="' + (this.tabIndex + i) + '"]').attr('disabled',true);
    //                 $('[tabindex="' + (this.tabIndex + i) + '"]').val('');
    //         }
    //     }
    // });

    $("input[tabindex]").on("blur", function(){
        //if($('[tabindex="' + (this.tabIndex) + '"]').val().length>0)
        if($(this).val().length > 0)
        {
            $('[tabindex="' + (this.tabIndex + 1) + '"]').attr('disabled',false);
            $('[tabindex="' + (this.tabIndex + 1) + '"]').focus();
        }
        else
        {
            for(var i = this.tabIndex; i < 11; i++ ){
                if(i!=1)
                $('[tabindex="' + (i) + '"]').attr('disabled',true);
                $('[tabindex="' + (i) + '"]').val('');
            }
        }
    });

    //select text on focus
    $("input[tabindex]").on("click", function(){
        $(this).select();
    });

    //enable on click
    $("input[type=text]").on("pointerdown", function(){
        var attr = $(this).attr('disabled');
        if (typeof attr !== typeof undefined && attr !== false) {
            if($('input[tabindex="' + (this.tabIndex - 1) + '"]').val().length > 0){
                $(this).attr('disabled',false);
            }
        }
    });

});

$("input[tabindex]").each(function () {
    $(this).on("keypress", function (event) {
        if( event.keyCode  == 13 || event.keyCode  == 17 || event.keyCode  == 74 ){
            event.preventDefault();
        }

        if (event.keyCode  === 13 && $(this).val().length > 0)
        {
            var nextElement = $('[tabindex="' + (this.tabIndex + 1) + '"]');
            nextElement.attr('disabled',false);
            if (nextElement.length) {
                $('[tabindex="' + (this.tabIndex + 1) + '"]').focus();
                event.preventDefault();
            } else{
                $('[tabindex="1"]').focus();
            }
            return false;
        }
    });
});
</script>
<?php
include '../include/footer.php';
?>
<script src="js/enclosure.js"></script>
<link href="../assets/global/plugins/bootstrap-switch/css/bootstrap-switch.min.css" rel="stylesheet" type="text/css">
<script src="../assets/global/plugins/bootstrap-switch/js/bootstrap-switch.min.js" type="text/javascript"></script>
