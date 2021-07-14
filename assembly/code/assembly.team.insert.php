<?php
include_once realpath(__DIR__ . '/../..').'/include/checksession.php';
include_once realpath(__DIR__ . '/../..').'/include/checkpermission.php';
require_once realpath(__DIR__ . '/../..') . '/class/SystemMessage.php';
require_once realpath(__DIR__ . '/../..') . '/include/settings.php';
include_once realpath(__DIR__ . '/../..').'/class/AssemblyTeam.class.php';
require_once realpath(__DIR__ . '/../..') . '/class/Helper.php';
require_once realpath(__DIR__ . '/../..') . '/class/UserLog.class.php';

$AssemblyTeam = new AssemblyTeam();
$message = New SysetemMessage($LANGUAGE);
$User_Log = new User_Log();

$team_data["team_name"]= Helper::Post("team_name", true);
$team_data["user_id"]= Helper::Post("user_id", true);
$team_data["table_number"]= Helper::Post("table_number", true);
$team_data["position_number"]= Helper::Post("position_number", true);

$InserrtAssemblyTeam = $AssemblyTeam -> AddAssemblyTeam( $team_data );

if($InserrtAssemblyTeam != false){
	//success, return archive id
	header('Location: ../assembly_team.php');
}else{
	//error
	$message -> AddMessage($Tree -> State, $Tree -> Message);
	ob_clean();
	header('Content-type: application/json');
	echo json_encode($message->GetMessages());
}
?>
