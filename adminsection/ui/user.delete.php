<?php
include_once realpath(__DIR__ . '/../..').'/include/checkpermission.php';
require_once realpath(__DIR__ . '/../..').'/class/HtmlHelper.php';
require_once realpath(__DIR__ . '/../..').'/class/User.php';
require_once realpath(__DIR__ . '/../..').'/class/Tree.php';

$user = new User();
$html = new HTML($LANGUAGE);
$dictionary = new Dictionary($LANGUAGE);
$dictionary->GetAllDictionary();
$tree = new Tree("DIR_TREE");
$description_key = "description_user_delete";

$userStatusArr = array(
    array(0=>"1", 1=>$dictionary -> GetValue("STATUS_ACTIVE")),
    array(0=>"2", 1=>$dictionary -> GetValue("STATUS_INACTIVE")),
    array(0=>"3", 1=>$dictionary -> GetValue("STATUS_DELETED")),
    array(0=>"4", 1=>$dictionary -> GetValue("STATUS_LOCKED")),
    array(0=>"5", 1=>$dictionary -> GetValue("STATUS_CHANGE_PASSWORD"))
);
$languageArr = array(array(0=>"KURDISH", 1=>"KURDISH"), array(0=>"ARABIC", 1=>"ARABIC"),array(0=>"ENGLISH", 1=>"ENGLISH"));
$operatingSystemArray = array(array(0=>"0" , 1=>"ANDROID"), array(0=>"1" , 1=>"WINDOWS"));

if(isset($_POST['user_id']) && $_POST['user_id'] != '')
{
	$user_id = $_POST['user_id'];
	$userInfo = $user -> GetInfo($user_id);
	if($userInfo!=null){
		//validate
		//$message->PermissionMessage($user->ValidateUser($USERID, $userInfo[0]["user_department_node_id"], 'user.delete'));
		//
        $options = array("class"=>"form-control", "disabled"=>"true", "flow"=>"horizental");
		$operation = 'delete';
		$formAction = 'code/user.delete.code.php';

		$html->OpenWidget ($dictionary -> GetValue("User_Delete") ." - ".$userInfo[0]["NAME"]);
		{
			$html->OpenForm ( $formAction, "form2", "horizental" );
	        {

				$html->DrawFormField ( "text", "name", is_null($userInfo) ? NULL : $userInfo[0]["NAME"], NULL, $options );
				$html->DrawFormField ( "text", "loginName", is_null($userInfo) ? NULL : $userInfo[0]["LOGIN"], NULL, $options );
                $html->DrawFormField ( "text", "DIRECTORATE_TEXT", is_null($userInfo) ? NULL : $tree -> GetPathString($userInfo[0]["user_department_node_id"]), NULL, $options );
                $html->DrawFormField ( "text", "ACCESS_DIR", is_null($userInfo) ? NULL : $tree -> GetPathString($userInfo[0]["user_access_node_id"]), NULL, $options );
				$html->DrawFormField ( "text", "user_status", is_null($userInfo) ? NULL : $userInfo[0]["USER_STATUS"], NULL, $options );
               	$html->DrawFormField ( "text", "FAILED_ATTEMPT_NUM", is_null($userInfo) ? NULL : $userInfo[0]["FAILED_ATTEMPT_NUM"], NULL, $options );
				$html->DrawFormField ( "radio", "language", is_null($userInfo) ? "KURDISH" : $userInfo[0]["UI_LANGUAGE"], $languageArr, $options );

				$controls = array (
								array ( "type"=>"submit", "name"=>"Change", "value"=>"Delete", "list"=>NULL, "options"=>array ("class" => "btn green")),
								array ( "type"=>"hidden", "name"=>"user_id", "value"=>$user_id, "list"=>NULL, "options"=>NULL ),
								array ( "type"=>"hidden", "name"=>"operation", "value"=>$operation, "list"=>NULL, "options"=>NULL )
							);
			}
            $html->CloseForm ($controls);
        }
        $html->CloseWidget();
    }
}
?>
<script>
$(function() {
    $(".delete_user").confirm({
        text:  '<i class="fa fa-times-circle fa-3x"></i>',
        confirm: function(button) {
            $('#form2').submit();
        },
        cancel: function(button) {
            // do something
        },
        confirmButton: " Yes ",
        cancelButton: " No ",
        post: true
        });
});
</script>
