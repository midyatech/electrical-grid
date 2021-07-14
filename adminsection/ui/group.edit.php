<?php
include_once realpath(__DIR__ . '/../..').'/include/checkpermission.php';
require_once realpath(__DIR__ . '/../..').'/class/HtmlHelper.php';
require_once realpath(__DIR__ . '/../..').'/class/Group.php';


$group = new Group();
$html = new HTML($LANGUAGE);
$dictionary = new Dictionary($LANGUAGE);
$dictionary->GetAllDictionary();
$description_key = "description_group_edit";
$options = array("class"=>"form-control", "flow"=>"horizental");

if(isset($_POST['group_id']) && $_POST['group_id'] != '')
{
	$group_id = $_POST['group_id'];
	$groupInfo = $group -> GetInfo($group_id);

	$operation = 'update';
	$formMethod = 'post';
	$formAction = 'code/group.update.code.php';

	$html->OpenWidget ($dictionary -> GetValue("Group_Edit") ." - ".$groupInfo[0]["GROUP_NAME"]);
	{
		$html->OpenForm ( $formAction, "form2", "horizental" );
		{
			$html->HelpMessage($description_key);
			$html->DrawFormField ( "text", "name", is_null($groupInfo) ? NULL : $groupInfo[0]["GROUP_NAME"], NULL, $options );
			$controls = array (
							array ( "type"=>"submit", "name"=>"Change", "value"=>"Save Changes", "list"=>NULL, "options"=>array ("class" => "btn green")),
							array ( "type"=>"hidden", "name"=>"operation", "value"=>$operation, "list"=>NULL, "options"=>NULL ),
							array ( "type"=>"hidden", "name"=>"group_id", "value"=>$group_id, "list"=>NULL, "options"=>NULL )
						);
		}
		$html->CloseForm ($controls);
	}
	$html->CloseWidget();
}
?>

<script>
$(document).ready(function(){
    $('#form2').validate(
    {
    	rules: {
	        name: {
	            required: true
	        }
      	},
		highlight: function(element) {
		  $(element).closest('.form-group').addClass('has-error');
		},
		unhighlight: function(element) {
		  $(element).closest('.form-group').removeClass('has-error');
		},
		success: function(element) {
		  $(element).closest('.form-group').removeClass('has-error');
		},
		errorPlacement: function(error, element) {}
    });
}); // end document.ready
</script>
