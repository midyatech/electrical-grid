/**
 * Page load script section
 */
$(function() {

	/**
	 * List-control events listener section
	 */
	$("#portlet_GROUPS").on("click", ".group_add", function(){
		$("#GroupContent").load("ui/group.add.php", function(){
			$("#GroupContent").scrollintoview({duration: "500"});
		});
	});

	$("#portlet_GROUPS").on("click", ".group_edit", function(){
		$group_id = $(this).data("group_id");
		$("#GroupContent").load("ui/group.edit.php", {"group_id":$group_id}, function(){
			$("#GroupContent").scrollintoview({duration: "500"});
		});
	});

	$("#portlet_GROUPS").on("click", ".group_delete", function(){
		$group_id = $(this).data("group_id");
		$("#GroupContent").load("ui/group.delete.php", {"group_id":$group_id}, function(){
			$("#GroupContent").scrollintoview({duration: "500"});
		});
	});
	$("#portlet_GROUPS").on("click", ".group_detail", function(){
		$group_id = $(this).data("group_id");
		$("#GroupContent").load("ui/group.detail.php", {"group_id":$group_id}, function(){
			$("#GroupContent").scrollintoview({duration: "500"});
		});
	});
	$("#portlet_GROUPS").on("click", ".manage_grouppermissions", function(){
		$group_id = $(this).data("group_id");
		$("#GroupContent").load("ui/group.add.permission.php", {"group_id":$group_id}, function(){
			$("#GroupContent").scrollintoview({duration: "500"});
		});
	});

	$("#GroupContent").on('click', "#btn_add_permission", function() {
		$("input[name='operation']").val("Insert");
		$data = $('form').serializeArray();
		var $group_id = $('#id').val();
		var $modules = $('#modules').val();

		$.post("code/grouppermission.insert.code.php", $data).done(function( response ) {
			$message = response;
			$("#GroupContent").load("ui/group.add.permission.php", {"group_id":$group_id , "modules":$modules}, function( response, status, xhr ){
				$("#UserContent").focus();
				$("#UserContent").prepend($message)
			});
		});

	});

	$("#GroupContent").on('click', "#btn_remove_permission", function() {
		$("input[name='operation']").val("Delete");
		$data = $('form').serializeArray();
		var $group_id = $('#id').val();
		var $modules = $('#modules').val();

		$.post("code/grouppermission.insert.code.php", $data).done(function( response ) {
			$message = response;
			$("#GroupContent").load("ui/group.add.permission.php", {"group_id":$group_id , "modules":$modules}, function( response, status, xhr ){
				$("#UserContent").focus();
				$("#UserContent").prepend($message)
			});
		});

	});

	$("#modules").on('change', function() {
		var $group_id = $('#id').val();
		var $modules = $('#modules').val();
		$("#GroupContent").load("ui/group.add.permission.php",{"group_id":$group_id, "modules":$modules });
	});

	$(".clear_filter_groups").click(function(){
		window.location.assign("group_list.php");
	})

});
/**
 * End Page load script section
 */
