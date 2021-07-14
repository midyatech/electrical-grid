<?php
include_once realpath(__DIR__ . '/../..').'/include/checkpermission.php';
require_once realpath(__DIR__ . '/../..').'/class/HtmlHelper.php';
require_once realpath(__DIR__ . '/../..').'/class/User.php';
require_once realpath(__DIR__ . '/../..').'/class/Tree.php';
require_once realpath(__DIR__ . '/../..').'/class/Uploader.php';

$user = new User();
$html = new HTML($LANGUAGE);
$tree = new Tree("DIR_TREE");
$uploader = new Uploader();

$dictionary = new Dictionary($LANGUAGE);
$dictionary->GetAllDictionary();
$description_key = "description_user_profile";

$coordinatesArr = array(array(0=>"latlng", 1=>"LatLng"), array(0=>"utm", 1=>"UTM"));

$user_id = $USERID;
$userInfo = $user -> GetInfo($user_id);
$user_group_list = $user->GetUserGroups($user_id);
$operation = 'update';
$formMethod = 'post';
$formAction = 'code/user.update.code.php';
$options = array("class"=>"form-control", "flow"=>"horizental");
$options_readonly = array("class"=>"form-control", "disabled"=>"true", "flow"=>"horizental");


$html->OpenWidget ($dictionary -> GetValue("settings"));
{
	$html->OpenForm ( $formAction, "form2", "horizental" );
	{
		$html->OpenWidget ("map_settings", null, null, 'light condensed', null);
		{
			$html->DrawFormField ( "radio", "map_coordinates", is_null($userInfo) ? "latlng" : $userInfo[0]["map_coordinates"], $coordinatesArr, $options );
		}
		$html->CloseWidget();
        $controls = array (
            array ( "type"=>"submit", "name"=>"Change", "value"=>"Save Changes", "list"=>NULL, "options"=>array ("class" => "btn green")),
            array ( "type"=>"hidden", "name"=>"user_id", "value"=>$user_id, "list"=>NULL, "options"=>NULL ),
            array ( "type"=>"hidden", "name"=>"operation", "value"=>$operation, "list"=>NULL, "options"=>NULL )
        );
        echo '<div class="clearfix"></div>';
	}
	$html->CloseForm ($controls);
}
$html->CloseWidget();
?>
<script>
$(document).ready(function(){

}); // end document.ready
</script>
