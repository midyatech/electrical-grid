<?php
require_once realpath(__DIR__ . '/../..') . '/class/Tree.php';
require_once realpath(__DIR__ . '/../..') . '/class/User.php';
require_once realpath(__DIR__ . '/../..') . '/class/SystemMessage.php';
require_once realpath(__DIR__ . '/../..') . '/include/settings.php';

$Tree = new Tree("AREA_TREE");
$User = new User("AREA_TREE");
$message = New SysetemMessage($LANGUAGE);

$nodeId = $nodeName = $nodeParentId = $coordinates = $userId = $password = NULL;

if (isset($_POST["nodeId"]) && $_POST["nodeId"] != "")
	$nodeId = $_POST["nodeId"];

if (isset($_POST["nodeName"]) && $_POST["nodeName"] != "")
	$nodeName = $_POST["nodeName"];

if (isset($_POST["area"]) && $_POST["area"] != ""){
	$nodeParentId = $_POST["area"];
	$_SESSION["area_id"] = $nodeParentId;
}

if (isset($_POST["coordinates"]) && $_POST["coordinates"] != "")
	$coordinates = $_POST["coordinates"];

if (isset($_POST["color"]) && $_POST["color"] != "")
	$color = $_POST["color"];

if (isset($_POST["userId"]) && $_POST["userId"] != "")
	$userId = $_POST["userId"];

if (isset($_POST["password"]) && $_POST["password"] != "")
	$password = $_POST["password"];

if($nodeId != NULL){
	$AreaID = $Tree -> EditNode( $nodeId, $nodeName, $nodeParentId, 1, 1, $coordinates, $color );
	$AreaID = $nodeId;
} else {
	$AreaID = $Tree -> AddNode( $nodeName, $nodeParentId, 1, 1, $coordinates, $color );
}

if($AreaID != false){
	if($userId > 0) {
		$User -> Edit($userId, $nodeName, NULL, NULL, $password, 1, 0);
	} else {
		$UserID = $User -> Add($nodeName, $AreaID, $password, 1, "KURDISH" , 0, $AreaID, $AreaID, 0);// Insert User to Zone
		$User->AddToGroup($UserID, 2);// Insert Group to User
	}

	//success, return archive id
	header('Location: ../map_area.php');
}else{
	//error
	$message -> AddMessage($Tree -> State, $Tree -> Message);
	ob_clean();
	header('Content-type: application/json');
	echo json_encode($message->GetMessages());
}
?>
