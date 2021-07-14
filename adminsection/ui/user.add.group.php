<?php
include_once realpath(__DIR__ . '/../..').'/include/checkpermission.php';
require_once realpath(__DIR__ . '/../..').'/class/HtmlHelper.php';
require_once realpath(__DIR__ . '/../..').'/class/User.php';
require_once realpath(__DIR__ . '/../..').'/class/Group.php';

$html = new HTML($LANGUAGE);
$user= new User();
$group= new Group();
$dictionary = new Dictionary($LANGUAGE);
$dictionary->GetAllDictionary();
$description_key = "description_user_groups";

include_once realpath(__DIR__ . '/../..').'/include/checkpermission.php';

if(isset($_POST['user_id']) && $_POST['user_id'] != '')
{
	$user_id = $_POST['user_id'];
	$group_list = $user->UserNonAssosiatedGroups($user_id); // id and name
	$user_group_list = $user->GetUserGroups($user_id);
	$optionsOptional = array("multiple"=>"multiple", "style"=>"height:180px", "class"=>"form-control");
	$operation = 'Insert';
	$formMethod = 'post';
	$formAction = 'code/usergroup.insert.code.php';

	$userInfo = $user -> GetInfo($user_id);
	if($userInfo!=null){
		//validate
		//$message->PermissionMessage($user->ValidateUser($USERID, $userInfo[0]["AGENT_ID"], 'user.add.group'));
		//
	}

	$html->OpenWidget ($dictionary -> GetValue("USER_GROUPS") ." - ".$userInfo[0]["NAME"] );//no-bottom-margin
	{
	$html->OpenForm ( $formAction, "form2" );
	{
		$html->OpenDiv("form-body");
		{
		
				$html->HelpMessage($description_key);
				$html->OpenSpan (5);
				{
					?>
					<label><?php echo $dictionary->GetValue("AllGroups");?></label>
	                <div class="clearfix"></div>
					<?php
					$html->Select ( "group[]",NULL, $group_list, $optionsOptional );
				}
				$html->CloseSpan ();

				$html->OpenSpan (1);
				{
				?>
				
					<br />
					<br />
					<input type="button" id="btn_add" name="btn_add"  value=">>" class="btn btn-default btn-icon-only" />
					<br />
					<br />
					<input type="button" id="btn_remove" name="btn_remove"  value="<<" class="btn  btn-default btn-icon-only"/>
					<br />
					<br />
					<input type="hidden" name="id" value="<?php echo $user_id;?>" id="id" >
					<input type="hidden" name="operation" value="<?php echo $operation;?>" id="operation" >
				<?php
				}
				$html->CloseSpan ();

				$html->OpenSpan (5);
				{
					?>
					
					<label><?php echo $dictionary->GetValue("USER_GROUPS");?></label>
	                 <div class="clearfix"></div>
					<?php
					$html->Select ( "user_groupList[]", NULL, $user_group_list, $optionsOptional);
				}
				$html->CloseSpan ();
				echo "<div class='clearfix'></div>";

				}
		$html->CloseDiv("form-body");

		
	}
	$html->CloseForm ();
}
$html->CloseWidget();
}

?>


