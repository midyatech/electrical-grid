$(function() {

	$(".add_area").on("click", function() {
        $("#form3").submit();
	});

    $(".clear_add_area").on("click", function() {
        window.location.assign("map_area.php");
    });

	//souce category add
	$(".open_area_tree").on( "click", function() {
		OpenTreeModal("area");
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
