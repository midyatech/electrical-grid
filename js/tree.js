//This function retrieves the children of clicked node
function OpenNode($currentNodeSpan, $tree_name)
{
	if(!$currentNodeSpan.parent().find("ul").length){
		//children elements don't exists, we need to get them from ajax
		$new_parent_id = $currentNodeSpan.parent().find("a").data("id");

		//get all_selectable option
		$all_selectable = $(".tree > UL").data("all_selectable");

		//first append wrapping UL
		$currentNodeSpan.parent().append("<ul></ul>");
		//then bring node LI from ajax
		$currentNodeSpan.parent().find("ul").load("../tree/ui/tree.node.php", {"PARENT_ID":$new_parent_id,
																				"tree_name":$tree_name,
																				"all_selectable": $all_selectable},
											function(data){
												//after getting chlidren, expand the tree node
												ExpandTreeNode($currentNodeSpan);
											}
										);
	}else{
		//Children elements exist, only expand tree node
		ExpandTreeNode($currentNodeSpan);
	}
}

function OpenNodeCheck($currentNodeSpan, $tree_name)
{
	if(!$currentNodeSpan.parent().find("ul").length){
		//children elements don't exists, we need to get them from ajax
		$new_parent_id = $currentNodeSpan.parent().find("input").data("id") || $currentNodeSpan.parent().find("a").data("id");

		//get all_selectable option
		$all_selectable = $(".tree > UL").data("all_selectable");

		//first append wrapping UL
		$currentNodeSpan.parent().append("<ul></ul>");
		//then bring node LI from ajax
		$currentNodeSpan.parent().find("ul").load("../tree/ui/tree.node.check.php", {"PARENT_ID":$new_parent_id,
																				"tree_name":$tree_name,
																				"all_selectable": $all_selectable},
											function(data){
												//after getting chlidren, expand the tree node
												ExpandTreeNode($currentNodeSpan);
											}
										);
	}else{
		//Children elements exist, only expand tree node
		ExpandTreeNode($currentNodeSpan);
	}
}

//This function toggles show/hide of clicked node
function ExpandTreeNode($currentNodeSpan)
{
	var children = $currentNodeSpan.parent('li').find(' > ul > li');
	if (children.is(":visible")) {
	    children.hide('fast');
	    $currentNodeSpan.attr('title', 'Expand this branch').find(' > i').addClass('fa-plus-square').removeClass('fa-minus-square');
	} else {
	    children.show('fast');
	    $currentNodeSpan.attr('title', 'Collapse this branch').find(' > i').addClass('fa-minus-square').removeClass('fa-plus-square');
	}
	//e.stopPropagation();
}


//This function selects a node and closes the modal
function SelectNode($node_id, $id_field, $node_text, $text_field, $modal_id)
{
	$($id_field).val($node_id);
	$($text_field).val($node_text)

	//make $modal_id optional
	$modal_id = $modal_id || "";

	//default is "myModal"
	var modalId = "myModal";
	modalId = $modal_id;

	//if modal id is not passed, or if submodal is closed use default modal myModal
	if (typeof $modal_id === "" || $('#'+modalId).hasClass('in')== false) {
		modalId = "myModal";
	}

	$('#'+modalId).modal('hide');
}
