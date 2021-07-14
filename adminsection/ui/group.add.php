<?php
include_once realpath(__DIR__ . '/../..').'/include/checkpermission.php';
require_once realpath(__DIR__ . '/../..').'/class/HtmlHelper.php';
require_once realpath(__DIR__ . '/../..').'/class/Group.php';

$group = new Group();
$html = new HTML($LANGUAGE);
$dictionary = new Dictionary($LANGUAGE);
$dictionary->GetAllDictionary();
$description_key = "description_group_add";

$operation = 'insert';
$formMethod = 'post';
$formAction = 'code/group.insert.code.php';
$options = array("class"=>"form-control", "flow"=>"horizental");

$html->OpenWidget ("Group_Add");
{
	$html->OpenForm ( $formAction, "form2", "horizental" );
	{
		$html->HelpMessage($description_key);
		$html->DrawFormField ( "text", "name", NULL, NULL, $options );

		$controls = array (
						array ( "type"=>"submit", "name"=>"Save", "value"=>"Save", "list"=>NULL, "options"=>array ("class" => "btn green")),
						array ( "type"=>"hidden", "name"=>"operation", "value"=>$operation, "list"=>NULL, "options"=>NULL )
					);
	}
	$html->CloseForm ($controls);
}
$html->CloseWidget();
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
