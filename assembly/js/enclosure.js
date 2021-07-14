$(document).ready(function() {


    $(".select_assembly_order").click(function(){
        aoid = $("#assembly_order_id").val();
        OpenModal("ui/assembly.order.list.php?id="+aoid)
    });

    $(".clear_filter_enclosure_list").on("click", function() {
        window.location.assign("enclosure_list.php");
    });

	$(".filter_enclosure_list").on("click", function() {
        $("#form3").submit();
    });

    $(".clear_filter_meter_list").on("click", function() {
        window.location.assign("meter_list.php");
    });

	$(".filter_meter_list").on("click", function() {
        $("#form3").submit();
    });

    $(".clear_filter_gateway_list").on("click", function() {
        window.location.assign("gateway_list.php");
    });

	$(".filter_gateway_list").on("click", function() {
        $("#form3").submit();
    });

	$("body").on("click", ".enclosure_details", function() {
        var id = $(this).data("id");
        window.location.assign("enclosure_meters.php?id="+id);
    });

	$("body").on("click", ".enclosure_delete", function() {
		var id = $(this).data("id");
        OpenConfirmModal(null, function() {
            var request = $.ajax({
                url: "code/enclosure.delete.code.php?id=" + id,
                type: "GET",
                processData: false,
                contentType: false
            });
            request.done(function(msg) {
                //console.log("done")
                if (GetLocalStatus(true, msg)) {
                    //success
                    window.location.assign("enclosure_list.php");
                } else {
                    //something wrong happened
                    SetLocalStatus(msg);
                    GetLocalStatus();
                }
                HideLoader();
            });
            request.fail(function(jqXHR, textStatus) {
                //error
                ShowToastr("error", jqXHR.statusText);
                HideLoader();
            });
        });
    });

	$(".insert_enclosure").on("click", function () {
        var form = $(this).closest('#add_enclosure').get(0);
        var url = "code/enclosure.insert.code.php"; //$(form).attr("action ");
        msgArray ="";
        ShowLoader($("body"));
        var formData = new FormData($("#add_enclosure")[0]);
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
            console.log(msg)
            if (msg != "" && !isNaN(msg)) {
                //console.log("1-"+msg);
                $("form#add_enclosure_sn").show();
                $("form#add_enclosure").hide();
                $("#enclosure_id").val(msg);
                $("#revocation_enclosure").data("id",msg);
                $("#enclosure_sn").focus();
            } else {
                //console.log("2-"+msg);
                //location.reload();
                if (GetLocalStatus(true, msg)) {
                    //console.log("3-"+msg);
                    //print receipt
                    $("#enclosure_id").val(msg);
                } else {
                    //console.log("4-"+msg);
                    //error, stay here
                }
                // var errBeep = new Audio('../media/beep-4.wav');
                // errBeep.play();
            }
            HideLoader();
        });

        request.fail(function(jqXHR, textStatus) {
            HideLoader();
            ShowToastr("error", jqXHR.statusText);
        });
    });

	$(".back_to_transformer").on("click", function () {
        aoid = $(this).data("aoid");
        tid = $(this).data("tid");;
        location.assign("assembly_transformer_details.php?aoid="+aoid+"&id="+tid);
    });

	$(".save_enclosure").on("click", function () {
        enclosure_sn = $("#enclosure_sn").val();
        if (enclosure_sn != "") {
            var form = $(this).closest('#add_enclosure_sn').get(0);
            var url = "code/enclosure.save.code.php"; //$(form).attr("action ");
            msgArray ="";
            ShowLoader($("body"));
            var formData = new FormData($("#add_enclosure_sn")[0]);
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
                if (msg == "") {
                    location.reload();
                } else {
                    //location.reload();
                    if (GetLocalStatus(true, msg)) {
                        //print receipt
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
        } else {
            console.log("Empty");
            var errBeep = new Audio('../media/beep-4.wav');
            errBeep.play();
        }
    });

    $(".save_enclosure_user").on("click", function () {
        enclosure_sn = $("#enclosure_sn").val();

        assembly_order_id = $("#assembly_order_id").val();
        enclosure_configuration_id = $("#enclosure_configuration_id").val();
        transformer_id = $("#transformer_id").val();

        if (enclosure_sn != "") {
            var form = $(this).closest('#add_enclosure_sn').get(0);
            var url = "code/enclosure.save.code.php"; //$(form).attr("action ");
            msgArray ="";
            ShowLoader($("body"));
            var formData = new FormData($("#add_enclosure_sn")[0]);
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
                if (msg == "") {
                    //location.reload();
                    window.location.assign("enclosure_meters.php?aoid="+assembly_order_id+"&ecid="+enclosure_configuration_id+"&tid="+transformer_id+"&sn="+enclosure_sn);
                } else {
                    //location.reload();
                    if (GetLocalStatus(true, msg)) {
                        //print receipt
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
        } else {
            console.log("Empty");
            var errBeep = new Audio('../media/beep-4.wav');
            errBeep.play();
        }
    });

    $("#add_enclosure_sn").keypress(function(e) {
        enclosure_sn = $(this).val();
        //Enter key
        if (e.which == 13) {
            if(enclosure_sn != ""){
                var form = $(this).closest('#add_enclosure_sn').get(0);
                var url = "code/enclosure.save.code.php"; //$(form).attr("action ");
                msgArray ="";
                ShowLoader($("body"));
                var formData = new FormData($("#add_enclosure_sn")[0]);
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
                        location.reload();
                    } else {
                        location.reload();
                        if (GetLocalStatus(true, msg)) {
                            //print receipt
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
                return false;
            } else {
                var errBeep = new Audio('../media/beep-4.wav');
                errBeep.play();
                return false;
            }
        }
    });

    $("body").on("click", ".revocation_enclosure", function() {
        var id = $(this).data("id");
            var request = $.ajax({
                url: "code/enclosure.cancel.code.php?id=" + id,
                type: 'GET',
                processData: false,
                contentType: false
            });
            request.done(function(msg) {
                //console.log("done")
                if (msg == "") {
                    //success
                    location.reload();
                } else {
                    //something wrong happened
                    SetLocalStatus(msg);
                    GetLocalStatus();
                }
                HideLoader();
            });
            request.fail(function(jqXHR, textStatus) {
                //error
                ShowToastr("error", jqXHR.statusText);
                HideLoader();
            });
    });


    $("#upload_result").on("click", ".upload_data", function() {

        var form = $(this).closest('#form2').get(0);
        var formData = new FormData($("#form2")[0]);
        var url = $(form).attr("action");
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
            alert("Upload completed")
            window.location.reload();
            //HideLoader();

        });
        request.fail(function(jqXHR, textStatus) {
            HideLoader();
            ShowToastr("error", jqXHR.statusText);
        });
    });

});



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
