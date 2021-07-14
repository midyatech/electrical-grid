$(function() {

	$(".add_team").on("click", function() {
        table = $(this).data("table");
        position = $(this).data("position");
        OpenModal("ui/team.add.php?table="+table+"&position="+position);
    });

	$(".team_configuration").on("click", function() {
        team_id = $(this).data("team_id");
        OpenModal("ui/team.configuration.php?team_id="+team_id);
    });

    $("body").on("click", ".configuration_priority_edit", function() {
        team_default_config_id = $(this).data("id");
        team_id = $(this).data("team_id");
        console.log(team_default_config_id);
        OpenSubModal("ui/team.configuration.edit.php?team_default_config_id="+team_default_config_id+"&team_id="+team_id);
    });

    $(".modal").on("click", ".insert_team", function () {
        ShowLoader($("body"));
        var formData = new FormData($("#add_assembly_team")[0]);
        var action = $('#add_assembly_team').attr("action");
        var request = $.ajax({
            url: action,
            type: 'POST',
            processData: false,
            contentType: false,
            data: formData
        });
        request.done(function (msg) {
            SetLocalStatus(msg);
            //show message anyway
            if (GetLocalStatus(true, msg)) {
                //success, clear form
                $('#myModal').modal('hide');
                window.location.assign("teams.php");
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

    $("body").on("change", ".add_configuration", function() {
        var team_id = $("#team_id").val();
        var enclosure_config_id = $(this).val();
            var request = $.ajax({
                url: "code/team.default.config.insert.code.php?team_id="+team_id+"&enclosure_config_id=" + enclosure_config_id,
                type: 'GET',
                processData: false,
                contentType: false
            });
            request.done(function(msg) {
                //console.log("done")
                if (msg == "") {
                    //success
                    //location.reload();
                    OpenModal("ui/team.configuration.php?team_id="+team_id);
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

    $("body").on("click", ".update_config_priority", function() {
        var team_id = $("#team_id").val();
        var priority = $("#priority").val();
        var team_default_config_id = $("#team_default_config_id").val();
            var request = $.ajax({
                url: "code/team.default.config.update.code.php?team_default_config_id="+team_default_config_id+"&priority=" + priority,
                type: 'GET',
                processData: false,
                contentType: false
            });
            request.done(function(msg) {
                //console.log("done")
                if (msg == "") {
                    //success
                    //location.reload();
                    $("#subModal").hide();
                    //$("#subModal").html("");
                    OpenModal("ui/team.configuration.php?team_id="+team_id);
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

	$("body").on("click", ".configuration_delete", function() {
		var id = $(this).data("id");
        var team_id = $(this).data("team_id");
        OpenConfirmModal(null, function() {
            var request = $.ajax({
                url: "code/team.default.config.delete.code.php?team_default_config_id=" + id,
                type: "GET",
                processData: false,
                contentType: false
            });
            request.done(function(msg) {
                //console.log("done")
                if (GetLocalStatus(true, msg)) {
                    //success
                    OpenModal("ui/team.configuration.php?team_id="+team_id);
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

//Open Sub Modal
function OpenSubModal($address){
	//$("#subModal .modal-body").height( $("#myModal .modal-body").height());
    $("#subModal").show();
	$("#subModal .modal-body").load($address);
	SetModalTitle("subModal", "","icon-ok-sign");
	$("#subModal").modal();
}