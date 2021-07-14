<?php
require_once realpath(__DIR__ . '/../..') . '/include/settings.php';
require_once realpath(__DIR__ . '/../..').'/class/HtmlHelper.php';
require_once realpath(__DIR__ . '/../..').'/class/Tree.php';
if(isset($_POST["tree_name"]) && $_POST["tree_name"]!="")
{
	$treeType = $_POST["tree_name"];

	if($treeType=="location")
		$treeType = "area";

	if($treeType == 'access_dir')
	$treeType = "dir";

	if($treeType == 'category_dir')
		$treeType = "category";

	if (strpos($treeType, '.') !== FALSE){
		$tree_name_array = explode( '.', $treeType );
		$treeType = $tree_name_array[0];
		$tree_number = $tree_name_array[1];
		$treeName = strtoupper($treeType)."_TREE";
		$treeClass = $treeName.' tree_'.$tree_number;
	}else{
		$treeName = strtoupper($treeType)."_TREE";
		$treeClass = strtoupper($_POST["tree_name"])."_TREE";
	}

	//whether add check button next to all nodes or only next to selectable nodes
	$all_selectable = 1;
	if(isset($_POST["all_selectable"])){
		$all_selectable = $_POST["all_selectable"];
	}

	if(isset($_POST["branch"])){
		if($_POST["branch"]=="access"){
			$starting_node = $USERACCESS;
		}else if($_POST["branch"]=="branch"){
			//start tree from current dir
			$starting_node = $ORGDIR;
		}else{
			//branch all
			$starting_node = 1;
		}
	}else{
		//start tree from top
		$starting_node = 1;
	}

	$tree = new Tree($treeName);
	$html = new HTML($LANGUAGE);

	// $starting_node = 1;
	// if(strtolower($treeType) == "dir")
	// 	if($subtree){
	// 	$starting_node = $ORGDIR;
	// }
	$top_node = $tree->GetNodeInfo($starting_node);
	$path = $tree->GetPathString($top_node[0]["NODE_ID"]);
?>
	<div class="tree <?php echo $treeClass; ?> well rtl-tree" >
		<ul data-all_selectable='<?php print $all_selectable;?>'>
		<?php
		if($top_node != null){
			$html->DrawTreeNode($top_node[0]["NODE_NAME"], $top_node[0]["NODE_ID"], $path, "list-item", $top_node[0]["SELECTABLE"], $all_selectable);
		}else{
			$html->alert("access_denied");
		}
		?>
		</ul>
	</div>
<?php
}
?>
