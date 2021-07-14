/**
 * Page load script section
 */
$(document).ready(function ()  {

	/**
	 * List-control events listener section
	 */
	$("#portlet_Users").on("click",".user_add", function(){
		$("#UserContent").load("ui/user.add.php", function(){
			$("#UserContent").scrollintoview({duration: "500"});
		});
	});

	$("#portlet_Users").on("click", ".user_edit", function(){
		$user_id = $(this).data("user_id");
		$("#UserContent").load("ui/user.edit.php", {"user_id":$user_id}, function(){
			$("#UserContent").scrollintoview({duration: "500"});
		});
	});

	$("#portlet_Users").on("click", ".user_delete", function(){
		$user_id = $(this).data("user_id");
		$("#UserContent").load("ui/user.delete.php", {"user_id":$user_id}, function(){
			$("#UserContent").scrollintoview({duration: "500"});
		});
	});

	$("#portlet_Users").on("click", ".user_detail", function(){
		$user_id = $(this).data("user_id");
		$("#UserContent").load("ui/user.detail.php", {"user_id":$user_id}, function(){
			$("#UserContent").scrollintoview({duration: "500"});
		});

	});
	$("#portlet_Users").on("click", ".reset_user", function(){
		$user_id = $(this).data("user_id");
		$.post("code/user.update.code.php", {"user_id":$user_id, "attemptNum": 0, "user_status": 1}).done(function( data ) {
	    	alert( "User Reset Successfully");
		});
	});

	$("#portlet_Users").on("click", ".manage_group", function(){
		$user_id = $(this).data("user_id");
		$("#UserContent").load("ui/user.add.group.php", {"user_id":$user_id}, function(){
			$("#UserContent").scrollintoview({duration: "500"});
		});
	});

	// $(".identfy_user").on("click",function(){
	// 	$user_id = $(this).data("user_id");
	//
	// 	$("#UserContent").load("ui/user.edit.status.php", {"user_id":$user_id});
	// });
	//
	// $(".status_active").on("click",function(){
	// 	$user_id = $("#user_id").val();
	// 	$("#UserContent").load("code/user.update.status.code.php", {"user_id":$user_id, "user_status":1});
	// });
	//
	// $(".status_locked").on("click",function(){
	// 	$user_id = $("#user_id").val();
	//
	// 	$("#UserContent").load("code/user.update.status.code.php", {"user_id":$user_id, "user_status":4});
	// });

	/*$(".status_delete").on("click",function(){
		$user_id = $("#user_id").val();
		$("#UserContent").load("code/user.update.status.code.php", {"user_id":$user_id, "user_status":3});
	});*/

	$(".status_delete").on("click",function(){
		var id = $(this).data("id");
		$.confirm({
            text:  '<i class="fa fa-times-circle fa-3x"></i>',
		    confirm: function(button) {
		     	$user_id = $("#user_id").val();
            	$("#UserContent").load("code/user.update.status.code.php", {"user_id":$user_id, "user_status":3});
		    },
		    cancel: function(button) {
		        // do something
		    },
            confirmButton: " Yes ",
            cancelButton: " No ",
		    post: true
		});
	});


	$(".status_reset").on("click",function(){
		$user_id = $("#user_id").val();
		$("#UserContent").load("code/user.update.status.code.php", {"user_id":$user_id, "user_status":6});
	});

	$("body").on('click', "#UserContent #btn_add", function(e) {
		$("input[name='operation']").val("Insert");
		$data = $('form').serializeArray();
		var $user_id = $('#id').val();

		$.post("code/usergroup.insert.code.php", $data).done(function( response ) {
	    	$message = response;
			$("#UserContent").load("ui/user.add.group.php", {"user_id":$user_id}, function( response, status, xhr ){
				$("#UserContent").focus();
				$("#UserContent").prepend($message)
			});
		});

	});

	$("body").on('click', "#UserContent #btn_remove", function() {
		$("input[name='operation']").val("Delete");
		$data = $('form').serializeArray();
		var $user_id = $('#id').val();

		$.post("code/usergroup.insert.code.php", $data).done(function( response ) {
	    	$message = response;
			$("#UserContent").load("ui/user.add.group.php", {"user_id":$user_id}, function( response, status, xhr ){
				$("#UserContent").focus();
				$("#UserContent").prepend($message)
			});
		});

		// $("#UserContent").load("code/usergroup.insert.code.php", $data, function(){
		// 			$("#UserContent").load("ui/user.add.group.php", {"user_id":$user_id});
		// 			$("#UserContent").focus();
		// });
	});

	$(".clear_filter_users").click(function(){
		window.location.assign("user_log_list.php");
	})

	//Listne to open directorate tree event
	$("body").on( "click", ".open_agent_tree", function() {
		OpenTreeModal("dir.1");
	});

	$("body").on( "click", ".open_dir_filter_tree", function() {
		OpenTreeModal("dir.2");
	});


	$("body").on( "click", ".open_access_tree", function() {
		OpenTreeModal("dir.access");
	});

	//get all agent trees
	$("body").on('click', '.tree.DIR_TREE li > span', function (e) {
		$currentSpan = $(this);
		OpenNode($currentSpan, "dir");
	});

	//selecting dir
	$("body").on('click', '.DIR_TREE.tree_1 .select_node_button', function () {
		$node_id = $(this).data("id");
		$node_text = $(this).data("text");
		SelectNode($node_id, "#DIRECTORATE", $node_text, "#DIRECTORATE_TEXT");
	});

	$("body").on('click', '.DIR_TREE.tree_2 .select_node_button', function () {
		$node_id = $(this).data("id");
		$node_text = $(this).data("text");
		SelectNode($node_id, "#user_dir", $node_text, "#user_dir_text");
	});

	//selecting access dir
	$("body").on('click', '.DIR_TREE.tree_access .select_node_button', function () {
		$node_id = $(this).data("id");
		$node_text = $(this).data("text");
		SelectNode($node_id, "#ACCESS_DIR", $node_text, "#ACCESS_DIR_TEXT");
	});

	// $("body").on( "click", ".open_agent_filter_tree", function() {
	// 	OpenTreeModal("dir.filter");
	// });

	/*//Listen to select directorate node
	$('.ACCESS_DIR_TREE .select_node_button').on('click', function () {
		$node_id = $(this).data("id");
		$node_text = $(this).data("text");
		SelectNode($node_id, "#ACCESS_DIRECTORATE", $node_text, "#ACCESS_DIRECTORATE_TEXT");
	});*/

	//LIsten to select operation type in user log
	$('#MODULE_ID').on("change", function () {
		var MODULE_ID = $("#MODULE_ID").val();
	    $("#OPERATION_TYPE").load("ui/user.get.operation.by.module.php", {"MODULE_ID":MODULE_ID});
	});



	//open waiting user list
	$('.list_waiting_users').on("click", function () {
		window.location.href = "user_waiting_list.php";
	});
	$('.list_active_users').on("click", function () {
		window.location.href = "user_list.php";
	});

});
/**
 * End Page load script section
 */


//Open Tree Dialog
function OpenTreeModal($tree_name){

	$("#myModal .modal-body").load("../tree/ui/tree.php", {"tree_name":$tree_name});
	SetModalTitle("myModal", "","icon-ok-sign");
	$("#myModal").modal();
}
