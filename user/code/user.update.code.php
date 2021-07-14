<?php
require_once realpath(__DIR__ . '/../..') . '/include/settings.php';
require_once realpath(__DIR__ . '/../..') . '/class/User.php';
require_once realpath(__DIR__ . '/../..') . '/class/Uploader.php';
require_once realpath(__DIR__ . '/../..') . '/class/SystemMessage.php';
//require_once realpath(__DIR__ . '/../..') . '/class/UserLog.class.php';
//$user_log = new User_Log();
$user = new User();
$uploader = new Uploader();
$message = New SysetemMessage($LANGUAGE);

if (isset($_POST['user_id']) && $_POST['user_id'] != "") {

	$user_id = $USERID;
	$userInfo = $user -> GetInfo($user_id);
	$uiLanguage = $LANGUAGE;
	$map_coordinates = $MAPCORDINATES;
	$name = $password = $department = $user_picture = NULL;

	if (isset($_POST["name"]) && $_POST["name"] != ''){
		$name = $_POST["name"];
	}

	if (isset($_POST["password"]) && $_POST["password"] != ''){
		$password = $_POST["password"];
	}

    if (isset($_POST["language"]))
	{
        $uiLanguage = $_POST["language"];
	}

	if (isset($_POST["map_coordinates"]))
	{
        $map_coordinates = $_POST["map_coordinates"];
	}

	if (!empty($_FILES['user_pic']))
	{
		$file=$_FILES['user_pic'];
		$attachment = $uploader -> UploadFile($file, true, "", "user_pic");
		if($attachment !==false){
			$user_picture = $attachment;
		}
	}

	$result = $user -> Edit($user_id, $name, NULL, NULL, $password, NULL, NULL, $uiLanguage, $map_coordinates, $user_picture);

	if ($result) {
		$_SESSION['language'] = $uiLanguage;
		$_SESSION['map_coordinates'] = $map_coordinates;
		if (!empty($_FILES['user_pic'])){
			$_SESSION['user_pic'] = ROOT_DIR."/".$uploader::BASE_PATH.$uploader::USER_PIC_PATH.$user_picture;
		}
	} else {
		$message -> AddMessage($user -> State, $user -> Message);
	}
	header('Location: ../user_detail.php');
} else {
	$message -> AddMessage(1, "no_user_selected");
}

if ($message -> GetMessagesCount() > 0) {
    $message -> PrintMessages("back");
}
?>
