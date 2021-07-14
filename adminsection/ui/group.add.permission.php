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
$description_key = "description_group_permissions";

if(isset($_POST['group_id']) && $_POST['group_id'] != '')
{
	$group_id = $_POST['group_id'];
	$groupInfo = $group -> GetInfo($group_id);
	$module_code = NULL;

	$permission_list = $group->GroupNonAssosiatedPermissions($group_id, $module_code);
	$group_permission_list = $group->GetGroupPermissions($group_id);

	if(!is_array($group_permission_list))
		$group_permission_list = NULL;
	if(!is_array($permission_list))
		$permission_list = NULL;

	$optionsOptional = array("multiple"=>"multiple", "style"=>"height:180px; min-width:300px", "class"=>"form-control", "dictionary"=>"true");

	$operation = 'Insert';
	$formMethod = 'post';
	$formAction = 'code/grouppermission.insert.code.php';

	$html->OpenWidget ($dictionary -> GetValue("GROUP_PERMISSIONS") ." - ".$groupInfo[0]["GROUP_NAME"]);
	{
		$html->OpenForm ( $formAction, $formMethod);
		{
			$html->OpenDiv("form-body");
			{

				$html->HelpMessage($description_key);

				$html->OpenSpan (5);
				{
					?>
					<label><?php echo $dictionary->GetValue("AllPermissions");?></label>
                    <div class="clearfix"></div>
					<?php
					$html->Select ( "permission[]",NULL, $permission_list, $optionsOptional );
				}
				$html->CloseSpan ();

				$html->OpenSpan (1);
				{
				?>
					<br />
					<br />
					<input type="button"  id="btn_add_permission" name="add" value=">>" class="btn btn-default btn-icon-only"  />

					</button>
					<br />
					<br />
					<input type="button" id="btn_remove_permission" name="remove"   value="<<" class="btn btn-default btn-icon-only" />

					</button>
					<br />
					<br />
					<input type="hidden" name="id" value="<?php echo $group_id;?>" id="id" >
					<input type="hidden" name="operation" value="<?php echo $operation;?>" id="operation" >

				<?php
				}
				$html->CloseSpan ();

				$html->OpenSpan (5);
				{
					?>
					<label><?php echo $dictionary->GetValue("ADDED_PERMISSIOS");?></label>
                    <div class="clearfix"></div>
					<?php
					$html->Select (  "group_permissionList[]", NULL, $group_permission_list, $optionsOptional);
				}
				$html->CloseSpan ();
				echo "<div class='clearfix'></div>";

			}
			$html->CloseDiv ();
		}
		$html->CloseForm ();
	}
	$html->CloseWidget();
}
?>
