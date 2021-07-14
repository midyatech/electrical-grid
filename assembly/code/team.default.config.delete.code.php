<?php
include_once realpath(__DIR__ . '/../..').'/include/checkpermission.php';
require_once realpath(__DIR__ . '/../..') . '/include/settings.php';
require_once realpath(__DIR__ . '/../..') . '/class/Helper.php';
require_once realpath(__DIR__ . '/../..') . '/class/AssemblyTeam.class.php';
require_once realpath(__DIR__ . '/../..') . '/class/SystemMessage.php';

$AssemblyTeam = new AssemblyTeam();
$message = new SysetemMessage($LANGUAGE);
$result = false;

if ((isset($_REQUEST['team_default_config_id']) && $_REQUEST['team_default_config_id'] != "")) {
    $team_config_data["team_default_config_id"] = $_REQUEST['team_default_config_id'];
}

$result=$AssemblyTeam->DeleteTeamConfig($team_config_data);

if ($result != false) {
    //print $result;
} else {
    //error
    $message -> AddMessage($AssemblyTeam->State, $AssemblyTeam->Message);
    $message -> PrintJsonMessage();
}