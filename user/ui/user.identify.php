<?php
include_once realpath(__DIR__ . '/../..').'/include/settings.php';
require_once realpath(__DIR__ . '/../..').'/class/HtmlHelper.php';
require_once realpath(__DIR__ . '/../..').'/class/User.php';
require_once realpath(__DIR__ . '/../..') . '/class/SystemMessage.php';

$message = New SysetemMessage($LANGUAGE);
$user = new User();
$html = new HTML($LANGUAGE);
$dictionary = new Dictionary($LANGUAGE);
$dictionary->GetAllDictionary();


if(isset($_REQUEST['user_id']) && $_REQUEST['user_id'] != '')
{
	$user_id = $_REQUEST['user_id'];
	if(isset($_REQUEST['unique_id']) && $_REQUEST['unique_id']!='')
	{
		$unique_id = $_REQUEST["unique_id"];
		$userInfo = $user -> GetInfo($user_id);
	
		$operation = 'update';
		$formMethod = 'post';
		$formAction = 'code/user.identify.code.php';
		$options = array("class"=>"form-control");
		$html->OpenWidget ("validate_client_machine", "blue");
		{	
			$html->OpenForm ( $formAction );
			{	
			   $html->OpenSpan(6, "col-sm-offset-1 pull-".$from_align);
				{
					$controls = array (
									array ( "type"=>"submit", "name"=>"GetMac", "value"=>"Get Mac", "list"=>NULL, "options"=>array ("class" => "btn  btn-default")),
									array ( "type"=>"hidden", "name"=>"user_id", "value"=>$user_id, "list"=>NULL, "options"=>NULL ),
									array ( "type"=>"hidden", "name"=>"unique_id", "value"=>$unique_id, "list"=>NULL, "options"=>NULL ),
									array ( "type"=>"hidden", "name"=>"operation", "value"=>$operation, "list"=>NULL, "options"=>NULL )
								);
					$html->DrawFormActions ( "", $controls );
				}
				$html->CloseSpan(); 
			}
			$html->CloseForm ();
		}
		$html->CloseWidget();
	}
	else{
		//no unique id found
		//do something
		$message->AddMessage("1", "web_browse_not_allowed_for_this_user");
		$message->PrintMessages("back");
	}
	
	
}
?>
