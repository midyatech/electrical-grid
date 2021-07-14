<?php
require_once realpath(__DIR__ . '/../..') . '/include/settings.php';
require_once realpath(__DIR__ . '/../..').'/class/HtmlHelper.php';
require_once realpath(__DIR__ . '/../..').'/class/Tree.php';

if(isset($_REQUEST["tree_name"]) && $_REQUEST["tree_name"]!="")
{
	$treeType = $_REQUEST["tree_name"];
	if($treeType=="location")
		$treeType = "area";
	if($treeType=="access_dir")
		$treeType = "dir";
	if($treeType=="category_dir")
		$treeType = "category";

	$all_selectable = 1;
	if(isset($_REQUEST["all_selectable"])){
		$all_selectable = $_REQUEST["all_selectable"];
	}

	$treeName = strtoupper($treeType)."_TREE";
	$tree = new Tree($treeName);
	$html = new HTML($LANGUAGE);

	$parent_node_id = $_REQUEST["PARENT_ID"];

	$nodes = $tree->GetNodeChildren($parent_node_id);

	if($nodes!=NULL && count($nodes)>0){
		foreach ( $nodes as $node){
            $path = $tree->GetPathString($node["NODE_ID"]);
			$html->DrawTreeNodeCheck($node["NODE_NAME"], $node["NODE_ID"], $path, "none", $node["SELECTABLE"], $all_selectable, $node["COORDINATES"], $node["COLOR"]);
		}
	}
}
?>
