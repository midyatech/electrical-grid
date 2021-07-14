$(document).ready(function() {

    $(".refresh_location").on("click", function(){
        getCoordinates();
    });

    $("body").on("click", ".scan_barcode", function() {
        Android.scanBarcode();
    });

	$(".open_area_tree").on( "click", function() {
		OpenTreeModal("area");
	});

    $("body").on("click", ".get_enclosure", function() {
        sn = $("#enclosure_sn").val();
        point_id = $(this).data("point_id");
        $("section#add_enclosure").load("ui/add.enclosure.php?sn="+sn+"&point_id="+point_id);
    });

    $("body").on("click", ".get_edit_enclosure", function() {
        sn = $("#enclosure_sn").val();
        window.location.href = "edit_enclosure.php?&sn=" + sn;
    });

    $("body").on("click", ".point_full_details", function() {
        point_id = $(this).data("full_details");
        OpenModal("../survey/ui/point.full.detail.php", {"point_id": point_id}, "modal-full");
    });

    $(".update_enclosure").on("click", function () {
        var form = $(this).closest('add_enclosure').get(0);
        var url = "code/enclosure.update.code.php"; //$(form).attr("action ");
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
            if (msg != "" && !isNaN(msg)) {
                //window.location.assign("enclosure_print.php?id="+msg);
                window.location.assign("edit_enclosure.php");
            } else {
                if (GetLocalStatus(true, msg)) {
                    //print receipt
                } else {
                    //error, stay here
                }
                var errBeep = new Audio('../media/beep-4.wav');
                errBeep.play();
            }
            HideLoader();
        });

        request.fail(function(jqXHR, textStatus) {
            HideLoader();
            ShowToastr("error", jqXHR.statusText);
        });
    });

    $("body").on("click", ".insert_installed_point_enclosure", function() {
        var transformer_id = $("#transformer_id").val();
        var enclosure_id = $(this).data("enclosure_id");
        var point_id = $(this).data("point_id");
        var request = $.ajax({
            url: "code/installed.point.enclosure.insert.code.php?transformer_id="+transformer_id+"&enclosure_id=" + enclosure_id + "&point_id=" + point_id,
            type: "GET",
            processData: false,
            contentType: false
        });
        request.done(function(msg) {
            //console.log("done")
            if (GetLocalStatus(true, msg)) {
                //success
                color = GetInstallationStatusColor(msg);
                ChangePointIcon(point_id, color);
                if(msg == 1){
                    $("#deactivate_section").css('display','block');
                    $("#already_installed_section").css('display','none');
                } else {
                    $("#deactivate_section").css('display','none');
                    $("#already_installed_section").css('display','block');
                }
            } else {
                //something wrong happened
            }
            HideLoader();
        });
        request.fail(function(jqXHR, textStatus) {
            //error
            ShowToastr("error", jqXHR.statusText);
            HideLoader();
        });
    });

    $("body").on("click", ".installation_status", function() {
        var point_id = $(this).data("point_id");
        var status_type_id = $(this).data("status_type");
        var request = $.ajax({
            url: "code/installation.status.update.code.php?point_id=" + point_id + "&status_type_id=" + status_type_id,
            type: "GET",
            processData: false,
            contentType: false
        });
        request.done(function(msg) {
            //console.log("done")
            if (GetLocalStatus(true, msg)) {
                //success
                color = GetInstallationStatusColor(msg);
                ChangePointIcon(point_id, color);
                OpenPointModal(point_id);
            } else {
                //something wrong happened
            }
            HideLoader();
        });
        request.fail(function(jqXHR, textStatus) {
            //error
            ShowToastr("error", jqXHR.statusText);
            HideLoader();
        });
    });

    $("body").on("click", ".add_problem", function() {
        var point_id = $(this).data("point_id");
        var installation_problem_id = $("#installation_problem_id").val();
        var create_notes = $("#create_notes").val();
        var request = $.ajax({
            url: "code/installation.problem.insert.code.php?point_id=" + point_id + "&installation_problem_id=" + installation_problem_id + "&create_notes=" + create_notes,
            type: "GET",
            processData: false,
            contentType: false
        });
        request.done(function(msg) {
            //console.log("done")
            if (GetLocalStatus(true, msg)) {
                //success
                color = GetInstallationStatusColor(msg);
                ChangePointIcon(point_id, color);
                OpenPointModal(point_id);
            } else {
                //something wrong happened
            }
            HideLoader();
        });
        request.fail(function(jqXHR, textStatus) {
            //error
            ShowToastr("error", jqXHR.statusText);
            HideLoader();
        });
    });

    $("body").on("click", ".add_comment", function() {
        var point_id = $(this).data("point_id");
        var comment = $("#comment").val();
        OpenConfirmModal(null, function() {
            var request = $.ajax({
                url: "code/installation.comment.insert.code.php?point_id=" + point_id + "&comment=" + comment,
                type: "GET",
                processData: false,
                contentType: false
            });
            request.done(function(msg) {
                if (GetLocalStatus(true, msg)) {
                    //success
                    OpenPointModal(point_id);
                } else {
                    //something wrong happened
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

    $("#myModal").on("click", ".delete_installed_enclosure", function() {
        var id = $(this).data("id");
        var point_id = $(this).data("point_id");
        OpenConfirmModal(null, function() {
            var request = $.ajax({
                url: "code/installed.point.enclosure.delete.code.php?installed_point_enclosure_id=" + id + "&point_id=" + point_id,
                type: "GET",
                processData: false,
                contentType: false
            });
            request.done(function(msg) {
                //console.log("done")
                if (GetLocalStatus(true, msg)) {
                    //success
                    EnclosurePointList(point_id);
                    color = GetInstallationStatusColor(msg);
                    ChangePointIcon(point_id, color);
                    if(msg == 1){
                        $("#deactivate_section").css('display','block');
                        $("#already_installed_section").css('display','none');
                    } else {
                        $("#deactivate_section").css('display','none');
                        $("#already_installed_section").css('display','block');
                    }
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

    $("#myModal").on("click", ".inactive_followup_state", function() {
        var id = $(this).data("id");
        var point_id = $(this).data("point_id");
        var state = $(this).data("state");
        OpenConfirmModal(null, function() {
            var request = $.ajax({
                url: "code/installation.problem.update.code.php?problem_report_id=" + id + "&point_id=" + point_id + "&state=" + state,
                type: "GET",
                processData: false,
                contentType: false
            });
            request.done(function(msg) {
                //console.log("done")
                if (GetLocalStatus(true, msg)) {
                    //success
                    color = GetInstallationStatusColor(msg);
                    ChangePointIcon(point_id, color);
                    OpenPointModal(point_id);
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

	$('#myModal').on('click', '.select_node_button', function () {
		$node_id = $(this).data("id");
		$node_text = $(this).data("text");
		SelectNode($node_id, "#area", $node_text, "#area_text", "myModal");
	});

	//Listen to fill area tree node
    $('#myModal').on('click', '.tree.AREA_TREE li > span', function(e) {
        $currentSpan = $(this);
        OpenNode($currentSpan, "area");
    });


});



/**/
//Open Tree Dialog
function OpenModal($address, $data, parameter){
    parameter = parameter || "modal-lg";

    $("#myModal .modal-dialog .modal-title").html('');
    $("#myModal .modal-body").html("");
    $("#myModal .modal-body").load($address, $data);
    SetModalTitle("myModal", "","icon-ok-sign");
    $("#myModal").modal();

    $("#myModal .modal-dialog").removeClass("modal-full");
    $("#myModal .modal-dialog").addClass(parameter);
    $("#myModal").modal({ backdrop: 'static', keyboard: false });

    //fix relative position when closing
    $("#myModal").css( "position", "" );
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