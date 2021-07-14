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

$switch_meter = $switch_gateway = $switch_size = $switch_count = $enclosure_sn = $id  = $enclosure  = $gateway_sn = null;
$meter_serial_number = array();
$enclosure_id = $aoid = $manually = null;

if (isset($_GET["id"]) && $_GET["id"] != ""){
    $enclosure_id = $_GET["id"];

} else if (isset($_GET["sn"]) && $_GET["sn"] != "") {
    $enclosure_sn = $_GET["sn"];
    $enclosure_id = $Enclosure->GetEnclosureId($enclosure_sn);

    $manually =1;
    $aoid=$_GET["aoid"];
    $ecid=$_GET["ecid"];
    $tid=$_GET["tid"];

}

if ($enclosure_id ==-1) {
    //get last inserted enclosure
    $enclosure_id = $Enclosure->GetLastEnclosureByUser($USERID);

}
if (isset($enclosure_id)) {
    $enclosure = $Enclosure->GetEnclosureDetails($enclosure_id);
    $id = $enclosure[0]["enclosure_id"];
    $meter_count = $enclosure[0]["meter_count"];

}

if (isset($_GET["aoid"])) {
    $aoid = $_GET["aoid"];
    $order = $Assembly->GetAssemblyOrder($aoid);
    $project_items = $Assembly->getAssemblyOrderItems($aoid);

}

$filter = array();
$filter["assembly_order_id"]= $aoid;
$filter["user_id"] = $USERID;
$work_item = $Assembly->GetTeamStack($filter, 1);

$no_of_meter = 3;
if (isset($enclosure) && $enclosure) {
    $operation="edit";

    $enclosure_sn = $enclosure[0]["enclosure_sn"];
    $gateway_sn = $enclosure[0]["gateway_sn"];
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
                $meter_id = $enclosure[$j]["meter_sn"];
                $MeterSerialNumber = $enclosure[$j]["meter_serial_number"];
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

require 'ui/assembly.breadcrumbs.php';

if( ! isset($_GET["id"]) && !isset($_GET["sn"])) {
?>
    <div class="tab-pane fade in" id="tab_add">
        <div class="form-group ">
            <label class="control-label"><?php echo $dictionary->GetValue("enclosure_sn");?>:</label>
            <div class="input-group">
                <span class="input-group-btn">
                    <button class="btn default scan_barcode_e" data-target_id="enclosure_sn" type="button">&nbsp;<i class="fa fa-barcode"></i>&nbsp;</button>
                </span>
                <input type="text" id="enclosure_sn" name="sn"  autocomplete="off"  value="<?php print $enclosure_sn ?>" class="form-control" placeholder="<?php echo $dictionary->GetValue("enclosure_sn");?>" autofocus="autofocus" >
                <span class="input-group-btn">
                    <button class="btn green scan_enclosure_sn" type="button">&nbsp;<i class="fa fa-check"></i>&nbsp;</button>
                </span>
            </div>
        </div>
    </div>
<?php
}

if($enclosure) {
    $_GET["sn"] = $enclosure_sn;
    require_once "ui/enclosure.meters.php";
} else {
    print '<section id="enclosure_details"></section>';
}
?>
<script>
    var manually = "<?php echo $manually;?>";
    var aoid = "<?php echo $aoid;?>";
    var ecid = "<?php echo $ecid;?>";
    var tid = "<?php echo $tid;?>";
    var scan_target_id;

    $(document).ready(function(){
        $(".scan_enclosure_sn").on("click", function(){
            sn = $("#enclosure_sn").val();
            $("#enclosure_details").load("ui/enclosure.meters.php?sn="+sn);
        });

        $("#enclosure_sn").keypress(function(e) {
            //Enter key
            if (e.which == 13) {
                sn = $("#enclosure_sn").val();
                $("#enclosure_details").load("ui/enclosure.meters.php?sn="+sn);
            }
        });

        $("body").on("change", "#add_meters #enclosure_configuration_id", function() {

            configuration = $(this).val();
            enclosure_id = $("#enclosure_id").val()
            msgArray = ["Are you sure you to change the enclosure's configuration? This will result in clearing all enclosure meters."];
            OpenConfirmModal(msgArray, function () {
                var request = $.ajax({
                    url: "code/enclosure.update.config.code.php?enclosure_id="+enclosure_id+"&configuration="+configuration,
                    type: 'GET',
                    processData: false,
                    contentType: false
                });
                request.done(function (msg) {
                    SetLocalStatus(msg);
                    //show message anyway
                    if (GetLocalStatus(true, msg)) {
                        //success, clear form
                        //reload table
                        location.reload();
                    } else {
                        //error, stay here
                    }
                    HideLoader();
                });
                request.fail(function (jqXHR, textStatus) {
                    HideLoader();
                    ShowToastr("error", jqXHR.statusText);
                });
            });
        });


        $("body").on("click", ".save_meters", function (){
            id = $("#enclosure_id").val();
            var form = $(this).closest('#add_enclosuadd_metersre_sn').get(0);
            var url = "code/enclosure.meter.insert.code.php?enclosure_status=3"; //$(form).attr("action ");
            msgArray ="";
            ShowLoader($("body"));
            var formData = new FormData($("#add_meters")[0]);
            var action = url;
            var request = $.ajax({
                url: action,
                type: 'POST',
                processData: false,
                contentType: false,
                data: formData
            });
            // request.done(function(msg) {
            //     //show message anyway
            //     if (msg != "" && !isNaN(msg)) {
            //         //location.reload();
            //     } else {
            //         if (GetLocalStatus(true, msg)) {
            //             //print receipt
            //             url = "enclosure_print.php?id="+id;
            //             if (manually == 1) {
            //                 url += "&aoid="+aoid+"&ecid="+ecid+"&tid="+tid;
            //             }
            //             location.href = url;
            //         } else {
            //             //error, stay here
            //         }
            //     }
            //     HideLoader();
            // });

            request.fail(function(jqXHR, textStatus) {
                HideLoader();
                ShowToastr("error", jqXHR.statusText);
            });
        });

        $("body").on("click", ".failed_meters", function (){
            //status = $(this).data("status");
            id = $("#enclosure_id").val();
            var form = $(this).closest('#add_enclosuadd_metersre_sn').get(0);
            var url = "code/enclosure.meter.insert.code.php?enclosure_status=2"; //$(form).attr("action ");
            msgArray ="";
            ShowLoader($("body"));
            var formData = new FormData($("#add_meters")[0]);
            var action = url;
            var request = $.ajax({
                url: action,
                type: 'POST',
                processData: false,
                contentType: false,
                data: formData
            });
            request.done(function(msg) {
                //show message anyway
                if (msg != "" && !isNaN(msg)) {
                    //location.reload();
                } else {
                    //location.reload();
                    if (GetLocalStatus(true, msg)) {
                        //print receipt
                        location.href = "enclosure_meters.php";
                    } else {
                        //error, stay here
                    }
                }
                HideLoader();
            });

            request.fail(function(jqXHR, textStatus) {
                HideLoader();
                ShowToastr("error", jqXHR.statusText);
            });
        });


        $("#tab_add").on("click", ".scan_barcode_e", function() {
            scan_target_id = $(this).data("target_id");
            Android.scanBarcode();
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
</script>
<?php
include '../include/footer.php';
?>