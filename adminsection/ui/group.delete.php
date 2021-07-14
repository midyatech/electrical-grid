<?php
include_once realpath(__DIR__ . '/../..').'/include/checkpermission.php';
require_once realpath(__DIR__ . '/../..').'/class/HtmlHelper.php';
require_once realpath(__DIR__ . '/../..').'/class/Group.php';


$group = new Group();
$html = new HTML($LANGUAGE);
$dictionary = new Dictionary($LANGUAGE);
$dictionary->GetAllDictionary();
$description_key = "description_group_delete";
$options = array("class"=>"form-control", "disabled"=>"true", "flow"=>"horizental");

if(isset($_POST['group_id']) && $_POST['group_id'] != '')
{
	$group_id = $_POST['group_id'];
	$groupInfo = $group -> GetInfo($group_id);

	$operation = 'delete';
	$formAction = 'code/group.delete.code.php';

	$html->OpenWidget ($dictionary -> GetValue("Group_Delete") ." - ".$groupInfo[0]["GROUP_NAME"]);
	{
		$html->OpenForm ( $formAction, "form2", "horizental" );
		{
			$html->HelpMessage($description_key);
			$html->DrawFormField ( "text", "name", is_null($groupInfo) ? NULL : $groupInfo[0]["GROUP_NAME"], NULL, $options );

			$controls = array (
				array ( "type"=>"button", "name"=>"Delete", "value"=>"Delete", "list"=>NULL, "options"=>array ("class" => "btn green  delete_group" )),
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
   $(function() {
	   $("body").on("click", ".delete_group", function(){
		   $('#form2').submit();
	   });

        // $(".delete_group").confirm({
        //     text: '<i class="fa fa-times-circle fa-3x"></i>',
        //     confirm: function(button) {
        //         $('#form2').submit();
        //     },
        //     cancel: function(button) {
        //         // do something
        //     },
        //     confirmButton: " Yes ",
        //     cancelButton: " No ",
        //     post: true
        //     });
    });
</script>
