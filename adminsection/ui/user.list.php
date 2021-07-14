<?php
include_once realpath(__DIR__ . '/../..').'/include/checkpermission.php';
require_once realpath(__DIR__ . '/../..').'/class/HtmlHelper.php';
require_once realpath(__DIR__ . '/../..').'/class/User.php';
require_once realpath(__DIR__ . '/../..').'/class/Helper.php';
require_once realpath(__DIR__ . '/../..').'/class/Tree.php';
require_once realpath(__DIR__ . '/../..').'/class/SystemMessage.php';

$tree = new Tree("DIR_TREE");
$message = New SysetemMessage($LANGUAGE);
$user = new User();
$html = new HTML($LANGUAGE);
$dictionary = new Dictionary($LANGUAGE);
$dictionary->GetAllDictionary();
$description_key = "description_users";

$statusArray = $user->GetUsersStatus();

/*filter parameters*/
$options = array("class"=>"form-control");
$qs = $name = $login = $dir_id = $dir_text = $status = null;
if(isset($_POST["name"]) && $_POST["name"] !=""){
	$name = $_POST["name"];
	$qs .= "name=".$name;
}
if(isset($_POST["login"]) && $_POST["login"] !=""){
	$login = $_POST["login"];
	$qs .= "&login=".$login;
}
if(isset($_POST["user_dir"]) && $_POST["user_dir"] !=""){
	$dir_id = $_POST["user_dir"];
	$dir_text = $tree->GetPathString($dir_id);
	$qs .= "&user_dir=".$dir_id;
}
if(isset($_POST["status"]) && $_POST["status"] !=""){
	$status = $_POST["status"];
	$qs .= "&status=".$status;
}
/*filter parameters*/


/**
 * Table Option Section
 */
$columnList = array();
$columnList[] = array("column"=>"NAME");
$columnList[] = array("column"=>"LOGIN");
$columnList[] = array("column"=>"DIR_NAME");
$columnList[] = array("column"=>"USER_STATUS","dictionary"=>"true");
$columnList[] = array("column"=>"ACTION_COL", "width"=>"200px","action-type"=>"ajax",
					"buttons"=> array(
						array("action"=>"Detail","action-class"=>"user_detail", "button-icon"=>"info-circle", "title"=>$dictionary->GetValue("Detail")),
						array("action"=>"Edit","action-class"=>"user_edit", "button-icon"=>"pencil-square-o", "title"=>$dictionary->GetValue("Edit")),
						array("action"=>"Delete","action-class"=>"user_delete", "button-icon"=>"times", "title"=>$dictionary->GetValue("Delete")),
						array("action"=>"Manage","action-class"=>"reset_user", "button-icon"=>"refresh", "title"=>$dictionary->GetValue("Reset")),
						array("action"=>"Manage","action-class"=>"manage_group", "button-icon"=>"group", "title"=>$dictionary->GetValue("Manage_Groups"))
					)
				);

$tableOptions = array();
$tableOptions["key"]=array("user_id"=>"USER_ID");

$actions = array (
 				array ( "type"=>"button", "name"=>"Change", "value"=>"Add", "list"=>NULL, "options"=>array ("class" => "btn blue user_add", "icon"=>"fa icon-plus"))
			);

$html->OpenWidget ("Users", $actions, array('collapse' => true));
{
	$html->HelpMessage($description_key);

	$html->OpenForm ( null, "form3" );
	{
		$html->OpenDiv("row");
		{
			$html->OpenSpan(2);
			{
				$html->DrawFormField ( "text", "name", $name, NULL, $options );
			}
			$html->CloseSpan();
			$html->OpenSpan(2);
			{
				$html->DrawFormField ( "text", "login", $login, NULL, $options );
			}
			$html->CloseSpan();
			$html->OpenSpan(3);
			{
				$dirTree = array (
						array ( "type"=>"hidden", "name"=>"user_dir", "value"=>$dir_id, "list"=>NULL, "options"=>NULL ),
						array ( "type"=>"text", "name"=>"user_dir_text", "value"=>$dir_text, "list"=>NULL, "options"=>array("class"=>"form-control open_dir_filter_tree", "tree"=>"", "readonly"=>"readonly") )
				);
				$html->DrawGenericFormField ( "dir_id", $dirTree );
			}
			$html->CloseSpan();
			$html->OpenSpan(3);
			{
				$html->DrawFormField ( "select", "status", $status, $statusArray, $options + array("dictionary"=>"true", "optional"=>"true") );
			}
			$html->CloseSpan();
			$html->OpenSpan(2);
			{
				?>
				<div class="form-group">
					<label>&nbsp;</label>
					<div class="input-group">
						<span class="input-group-btn">
							<button class="btn green" type="submit" style="width:95%"><?php echo $dictionary->GetValue("filter");?></button>
						</span>
						<span class="input-group-btn">
							<button class="btn default clear_filter_users" type="button" style="width:95%"><?php echo $dictionary->GetValue("clear");?></button>
						</span>
					</div>
				</div>
				<?php
			}
			$html->CloseSpan();
		}
		$html->CloseDiv();
	}
	$html->CloseForm();

	$html->Datatable("example", "api/list.users.php?".$qs, $columnList, $tableOptions);
}
$html->CloseWidget();
?>
<div id="UserContent"></div>
