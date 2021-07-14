<?php
include_once realpath(__DIR__ . '/../..').'/include/checkpermission.php';
require_once realpath(__DIR__ . '/../..').'/class/HtmlHelper.php';
require_once realpath(__DIR__ . '/../..').'/class/Group.php';


$group = new Group();
$html = new HTML($LANGUAGE);
$dictionary = new Dictionary($LANGUAGE);
$dictionary->GetAllDictionary();
$description_key = "description_group_detail";
$options = array("class"=>"form-control", "disabled"=>"true", "flow"=>"horizental");

if(isset($_POST['group_id']) && $_POST['group_id'] != '')
{
	$group_id = $_POST['group_id'];
	$groupInfo = $group -> GetInfo($group_id);

	$operation = 'view';
	$formMethod = 'post';
	$formAction = 'code/group.update.code.php';

	$html->OpenWidget ($dictionary -> GetValue("Group_Detail") ." - ".$groupInfo[0]["GROUP_NAME"]);
	{
		$html->OpenForm ( $formAction, "form2", "horizental" );
		{
			$html->HelpMessage($description_key);
			$html->DrawFormField ( "text", "name", is_null($groupInfo) ? NULL : $groupInfo[0]["GROUP_NAME"], NULL, $options );
		}
		$html->CloseForm ();
	}
	$html->CloseWidget();
}
?>
