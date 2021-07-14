<?php
require_once realpath(__DIR__ . '/../..') . '/class/Helper.php';
require_once realpath(__DIR__ . '/../..') . '/include/settings.php';

$filter = array();
$field = 'USER.USER_ID';

if(isset($_POST["USER_ID"]) && $_POST["USER_ID"] != ""){
	$user_id = Helper::Post("USER_ID");
	Helper::FilterArray($filter, $field, $user_id);
}
else
{
	//all users, don't filter
	$user_id= NULL;
}

$field = 'OPERATION_TYPE';
$operation = Helper::Post($field);
Helper::FilterArray($filter, $field, $operation);

$field = 'DATE_FORMAT(`USER_LOG`.`DATE`, "%Y-%m-%d")';
$date = Helper::Post("DATE");

Helper::FilterArray($filter, $field, $date);
?>