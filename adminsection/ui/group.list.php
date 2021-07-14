<?php
include_once realpath(__DIR__ . '/../..').'/include/checkpermission.php';
require_once realpath(__DIR__ . '/../..').'/class/HtmlHelper.php';
require_once realpath(__DIR__ . '/../..').'/class/Group.php';

$group = new Group();
$html = new HTML($LANGUAGE);
$dictionary = new Dictionary($LANGUAGE);
$dictionary->GetAllDictionary();
$description_key = "description_groups";


/*filter parameters*/
$options = array("class"=>"form-control");
$qs = $name = $status = null;
if(isset($_POST["name"]) && $_POST["name"] !=""){
	$name = $_POST["name"];
	$qs .= "name=".$name;
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
$columnList[] = array("column"=>"GROUP_NAME");
//$columnList[] = array("column"=>"STATUS", "dictionary"=>"true");
$columnList[] = array("column"=>"ACTION_COL", "style"=>"width:200px","action-type"=>"ajax",
					  "buttons"=> array(
							array("action"=>"Detail","action-class"=>"group_detail", "button-icon"=>"info-circle", "title"=>$dictionary->GetValue("Detail")),
							array("action"=>"Edit","action-class"=>"group_edit", "button-icon"=>"pencil-square-o", "title"=>$dictionary->GetValue("Edit")),
							array("action"=>"Delete","action-class"=>"group_delete", "button-icon"=>"times", "title"=>$dictionary->GetValue("Delete")),
							array("action"=>"Manage_Permissions","action-class"=>"manage_grouppermissions", "button-icon"=>"sitemap", "title"=>$dictionary->GetValue("Permission"))
								)
					);
//Prepare other table options for html output
$tableOptions = array();
$tableOptions["key"]=array("group_id"=>"GROUP_ID");
/**
 * End Table Option Section
 */


$actions = array (
 				array ( "type"=>"button", "name"=>"Change", "value"=>"Add", "list"=>NULL, "options"=>array ("class" => "btn blue group_add", "icon"=>"fa icon-plus"))
				);
$html->OpenWidget ("GROUPS", $actions, array('collapse' => true));
{
	$html->HelpMessage($description_key);
	$html->OpenForm ( null, "form3" );
	{
		$html->OpenDiv("row");
		{
			$html->OpenSpan(4);
			{
				$html->DrawFormField ( "text", "name", $name, NULL, $options );
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
							<button class="btn default clear_filter_groups" type="button" style="width:95%"><?php echo $dictionary->GetValue("clear");?></button>
						</span>
					</div>
				</div>
				<?php
			}
			$html->CloseSpan();
			$html->OpenSpan(5);
			{
				//$statusArray = $group->GetGroupsStatus();
				//$html->DrawFormField ( "select", "status", $status, $statusArray, $options + array("dictionary"=>"true", "optional"=>"true") );
			}
			$html->CloseSpan();
		}
		$html->CloseDiv();
	}
	$html->CloseForm();
	$html->Datatable("example", "api/list.groups.php?".$qs, $columnList, $tableOptions);

 }
$html->CloseWidget();
?>
<section id="GroupContent"></section>
