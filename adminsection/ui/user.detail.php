<?php
include_once realpath(__DIR__ . '/../..').'/include/checkpermission.php';
//include_once realpath(__DIR__ . '/../..').'/include/settings.php';
require_once realpath(__DIR__ . '/../..').'/class/HtmlHelper.php';
require_once realpath(__DIR__ . '/../..').'/class/User.php';
require_once realpath(__DIR__ . '/../..').'/class/Tree.php';

$user = new User();
$html = new HTML($LANGUAGE);
$tree = new Tree("DIR_TREE");
$dictionary = new Dictionary($LANGUAGE);
$dictionary->GetAllDictionary();
$description_key = "description_user_detail";

$languageArr = array(array(0=>"KURDISH", 1=>"KURDISH"), array(0=>"ARABIC", 1=>"ARABIC"), array(0=>"ENGLISH", 1=>"ENGLISH"));
$operatingSystemArray = array(array(0=>"0" , 1=>"ANDROID"), array(0=>"1" , 1=>"WINDOWS"));

if(isset($_POST['user_id']) && $_POST['user_id'] != '')
{
	$user_id = $_POST['user_id'];
    $userInfo = $user -> GetInfo($user_id);
    if($userInfo!=null){
    	//validate
    	//$message->PermissionMessage($user->ValidateUser($USERID, $userInfo[0]["user_department_node_id"], 'user.edit'));
    	//
        $options = array("class"=>"form-control", "disabled"=>"true", "flow"=>"horizental");
    	$operation = 'view';

    	$html->OpenWidget ($dictionary -> GetValue("User_Detail") ." - ".$userInfo[0]["NAME"]);
        {
    		$html->OpenForm ( "", "form2", "horizental");
        	{
        		$html->HelpMessage($description_key);

				$html->DrawFormField ( "text", "name", is_null($userInfo) ? NULL : $userInfo[0]["NAME"], NULL, $options );
				$html->DrawFormField ( "text", "loginName", is_null($userInfo) ? NULL : $userInfo[0]["LOGIN"], NULL, $options );
                $html->DrawFormField ( "text", "DIRECTORATE_TEXT", is_null($userInfo) ? NULL : $tree -> GetPathString($userInfo[0]["user_department_node_id"]), NULL, $options );
                $html->DrawFormField ( "text", "ACCESS_DIR", is_null($userInfo) ? NULL : $tree -> GetPathString($userInfo[0]["user_access_node_id"]), NULL, $options );
				$html->DrawFormField ( "text", "user_status", is_null($userInfo) ? NULL : $userInfo[0]["USER_STATUS"], NULL, $options );
               	$html->DrawFormField ( "text", "FAILED_ATTEMPT_NUM", is_null($userInfo) ? NULL : $userInfo[0]["FAILED_ATTEMPT_NUM"], NULL, $options );
				$html->DrawFormField ( "radio", "language", is_null($userInfo) ? "KURDISH" : $userInfo[0]["UI_LANGUAGE"], $languageArr, $options );

        	}
        	$html->CloseForm ();
        }
        $html->CloseWidget();
    }
}
?>
