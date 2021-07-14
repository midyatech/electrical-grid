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
<section id="enclosure_details"></section>
<script>
    var manually = "<?php echo $manually;?>";
    var aoid = "<?php echo $aoid;?>";
    var ecid = "<?php echo $ecid;?>";
    var tid = "<?php echo $tid;?>";
    var scan_target_id;

    $(document).ready(function(){
        $(".scan_enclosure_sn").on("click", function(){
            sn = $("#enclosure_sn").val();
            $("#enclosure_details").load("ui/update.enclosure.php?sn="+sn);
        });

        $("body").on("change", "#add_meters #enclosure_configuration_id", function() {

            configuration = $(this).val();
            enclosure_id = $("#enclosure_id").val();
            change_reason = $("#change_reason").val();
            $("#enclosure_details").load("ui/update.enclosure.php?sn="+sn+"&configuration="+configuration+"&change_reason="+change_reason);

            // msgArray = ["Are you sure you to change the enclosure's configuration? This will result in clearing all enclosure meters."];
            // OpenConfirmModal(msgArray, function () {
            //     var request = $.ajax({
            //         url: "code/enclosure.update.config.code.php?enclosure_id="+enclosure_id+"&configuration="+configuration,
            //         type: 'GET',
            //         processData: false,
            //         contentType: false
            //     });
            //     request.done(function (msg) {
            //         SetLocalStatus(msg);
            //         //show message anyway
            //         if (GetLocalStatus(true, msg)) {
            //             //success, clear form
            //             //reload table
            //             location.reload();
            //         } else {
            //             //error, stay here
            //         }
            //         HideLoader();
            //     });
            //     request.fail(function (jqXHR, textStatus) {
            //         HideLoader();
            //         ShowToastr("error", jqXHR.statusText);
            //     });
            // });
        });


        $("body").on("click", ".save_meters", function (){
            id = $("#enclosure_id").val();
            var url = "code/update.enclosure.code.php";
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
                // //show message anyway
                // if (msg != "" && !isNaN(msg)) {
                //     //location.reload();
                // } else {
                //     if (GetLocalStatus(true, msg)) {
                //         //print receipt
                //         url = "update_enclosure.php?id="+id;
                //         if (manually == 1) {
                //             url += "&aoid="+aoid+"&ecid="+ecid+"&tid="+tid;
                //         }
                //         location.href = url;
                //     } else {
                //         //error, stay here
                //     }
                // }
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