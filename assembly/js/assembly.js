$(document).ready(function() {

    $(".add_extra_items").click(function(){
        aid = $("#assembly_order_id").val();
        OpenModal("ui/add.extra.items.php?id="+aid);
    });

    $("body").on("click", ".open_area_tree", function() {
        OpenTreeModal("area");
    });

    $("body").on("click", ".clear_filter_assembly_list", function() {
        window.location.assign("assembly_list.php");
    });

    $("body").on("click", ".assembly_details", function() {
        id = $(this).data("id");
        window.location.assign("assembly_details.php?id=" + id, id);
    });

    $("body").on("click", ".export_btn", function() {
        id = $(this).data("id");
        window.location.assign("ui/assembly.iccid.export.php?id=" + id, id);
    });

    $("body").on("click", ".add_enclosure", function(){
        var enclosure_config_id = $(this).data("enclosure_config_id");
        var assembly_order_id = $("#assembly_order_id").val();
        var transformer_id = $("#transformer_id").val();
        window.location.assign("scan_enclosure_user.php?ecid="+enclosure_config_id+"&aoid="+assembly_order_id+"&tid="+transformer_id);
    });




    $("body").on("click", ".change_assembly_status", function() {
        assembly_order_id = $(this).data("id");
        status = $(this).data("status");
        /*
        alert(assembly_order_id);
        alert(status);
        */
        msgArray = ["Are you sure to change status?"];
        OpenConfirmModal(msgArray, function () {
            var request = $.ajax({
                url: "code/assembly.order.update.code.php?id="+assembly_order_id+"&status="+status,
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

        // OpenConfirmModal(msgArray, function() {
        //     window.location.assign("code/objection.code.php?id="+id+"&type="+type);
        // });

    });

    $("body").on("click", ".add_assembly_order", function() {
        var form = $(this).closest('form').get(0);
        var url = $(form).attr("action");
        if ($(form).valid()) {
            //ShowLoader($("body"));
            var formData = new FormData($("#unit_history")[0]);

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
                if (GetLocalStatus(true, msg)) {
                    //reload
                    //$("#form3").submit(); stay in place
                    window.location.assign("unit.php?unit_id=" + unit_id);
                } else {
                    //error, stay here
                }
                //HideLoader();

            });
            request.fail(function(jqXHR, textStatus) {
                HideLoader();
                ShowToastr("error", jqXHR.statusText);
            });
        }
    });

    $(".modal").on("click", "#add_extra_enclosures", function() {
        aid = $("#assembly_order_id").val();
        var form = $(this).closest('form').get(0);
        var url = $(form).attr("action");
        //if ($(form).valid()) {
            //ShowLoader($("body"));
            var formData = new FormData($("#extra_items_form")[0]);

            var action = url;
            var request = $.ajax({
                url: action,
                type: 'POST',
                processData: false,
                contentType: false,
                data: formData
            });
            request.done(function(msg) {
                window.location.reload();
                //show message anyway
                // //$("section#extra_items").load("ui/get.extra.items.php?id="+aid);
                // $("section#stats").load("ui/assembly.stats.php?id="+aid);

                // if (GetLocalStatus(true, msg)) {
                //     $("#myModal").modal("hide")	;
                // } else {
                //     //error, stay here
                // }
                // //HideLoader();

            });
            request.fail(function(jqXHR, textStatus) {
                HideLoader();
                ShowToastr("error", jqXHR.statusText);
            });
        //}
    });




    /*
        $("body").on("click", ".GetTransfotmer", function() {
            area_id = $("#area").val();
        });

        $("body").on("click", ".GetTransfotmer", function() {
            area_id = $("#area").val();
            var form = $(this).closest('form').get(0);
            var url = $(form).attr("action");
            if ($(form).valid()) {
                //ShowLoader($("body"));
                var formData = new FormData($("#unit_history")[0]);

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
                    if (GetLocalStatus(true, msg)) {
                        //reload
                        //$("#form3").submit(); stay in place
                        window.location.assign("unit.php?unit_id=" + unit_id);
                    } else {
                        //error, stay here
                    }
                    //HideLoader();

                });
                request.fail(function(jqXHR, textStatus) {
                    HideLoader();
                    ShowToastr("error", jqXHR.statusText);
                });
            }
        });
    */





    $('#myModal').on('click', '.tree.AREA_TREE li > span', function(e) {
        $currentSpan = $(this);
        OpenNode($currentSpan, "area");
    });

    $('#myModal').on('click', ' .select_node_button', function() {
        $node_id = $(this).data("id");
        $node_text = $(this).data("text");
        SelectNode($node_id, "#area", $node_text, "#area_text", "myModal");
    });

    //Listen to fill area tree node CHECKBOX
    $('#treeCheck').on('click', '.AREA_TREE li > span', function (e) {
        $currentSpan = $(this);
        OpenNodeCheck($currentSpan, "area");
    });

    // $("body").on("click", ".iccid_status", function() {
    //     iccids = $('#iccids').val();
    //     id = $(this).data("id");
    //     activation_date = $('#activate_date').val();
    //     var request = $.ajax({
    //         url: "code/iccid.status.update.php",
    //         type: 'POST',
    //         data: {id: id, iccids: iccids, activation_date:activation_date}
    //     });
    //     request.done(function(msg) {
    //         if (GetLocalStatus(true, msg)) {
    //                 $("#myModal").modal("hide")	;
    //                 location.reload();
    //             } else {
    //                 //error, stay here
    //             }
    //     });
    //     request.fail(function(msg) {
    //         //HideLoader();
    //         ShowToastr("error", msg);
    //     });
    // });

    $("body").on("click", ".iccid_status", function() {
        // iccids = $('#iccids').val();
        var order_id = $('#order_id').val();
        var id = $(this).data("id");
        var activation_date = $('#activate_date').val();

        msgArray = ["Are you sure to change SIM Card status?"];
        OpenConfirmModal(msgArray, function () {
            var request = $.ajax({
                url: "code/iccid.status.update.php",
                type: 'POST',
                data: { id: id, activation_date: activation_date, order_id: order_id }
            });
            request.done(function (msg) {
                if (GetLocalStatus(true, msg)) {
                    $("#myModal").modal("hide");
                    location.reload();
                } else {
                    //error, stay here
                }
            });
            request.fail(function (msg) {
                //HideLoader();
                ShowToastr("error", msg);
            });
        });
    });

    $("body").on("click", ".change_gateway_status", function() {
        id = $(this).data("id");
        OpenModal("ui/change.iccid.status.php?id="+id);
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

//Open Tree Dialog
function OpenTreeModal($tree_name){
    $("#myModal .modal-dialog .modal-title").html('');
    $("#myModal .modal-body").html("");
    $("#myModal .modal-body").load("../tree/ui/tree.php", {"tree_name":$tree_name});
    SetModalTitle("myModal", "","icon-ok-sign");
    $("#myModal").modal();
    //fix relative position when closing
    $("#myModal").css( "position", "" );
}
