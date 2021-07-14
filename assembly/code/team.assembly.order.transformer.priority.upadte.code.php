<?php
include_once realpath(__DIR__ . '/../..').'/include/checkpermission.php';
require_once realpath(__DIR__ . '/../..') . '/include/settings.php';
require_once realpath(__DIR__ . '/../..') . '/class/Helper.php';
require_once realpath(__DIR__ . '/../..') . '/class/AssemblyTeam.class.php';
require_once realpath(__DIR__ . '/../..') . '/class/SystemMessage.php';

$AssemblyTeam = new AssemblyTeam();
$message = new SysetemMessage($LANGUAGE);
$result = false;

if ((isset($_REQUEST['transformer_id']) && $_REQUEST['transformer_id'] != "")) {
    $transformer_id = $_REQUEST['transformer_id'];
}

$result=$AssemblyTeam->EditAssemblyOrderTransformerpriority($transformer_id);

if ($result != false) {
    header("Location: ../teams.php");
} else {
    //error
    $message -> AddMessage($AssemblyTeam->State, $AssemblyTeam->Message);
    //$message -> PrintJsonMessage();
    print $AssemblyTeam->Message;
}