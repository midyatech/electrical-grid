<?php
if (session_id() == ''){session_start();}
require_once 'settings.php';
require_once $_SERVER['DOCUMENT_ROOT'].$FOLDERNAME.'class/HtmlHelper.php';
require_once $_SERVER['DOCUMENT_ROOT'].$FOLDERNAME.'class/User.php';
require_once $_SERVER['DOCUMENT_ROOT'].$FOLDERNAME.'class/SystemMessage.php';
require_once $_SERVER['DOCUMENT_ROOT'].$FOLDERNAME.'class/Tree.php';

$user = new User();
$html = new HTML($LANGUAGE);
$message = New SysetemMessage($LANGUAGE);
$dir_tree = new Tree("AREA_TREE");

$form = $level = NULL;

if(isset($_REQUEST["page_attr"]) && $_REQUEST["page_attr"] !='' ){
	$form =  basename($_SERVER['PHP_SELF'], ".php").".".$_REQUEST["page_attr"];
}else{
	$form =  basename($_SERVER['PHP_SELF'], ".php");
}
// print $form;

$operation_name = $user-> GetOperationName($form);
//print "[$operation_name]";
//$has_application_permission = $user-> CheckPermission($USERID, "permission_client_application");
//$has_application_permission = 1;//this is for test only, remove this line in production
$level = $user-> CheckPermission($USERID, $operation_name);
//$level = 1;
if($level == 0)
{
	$message->AddMessage(1, "You_Do_not_have_permission");
	$message->PrintMessages("");
    $html->Link("button", "back", ROOT_DIR.'/index.php', array ("class" => "btn default", "style"=>"margin: 0 auto;display: block; width:150px") );
	die();
}
?>
