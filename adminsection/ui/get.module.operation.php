<?php
include_once realpath(__DIR__ . '/../..').'/include/checksession.php';
//include_once realpath(__DIR__ . '/../..').'/include/checkpermission.php';
require_once realpath(__DIR__ . '/../..').'/include/settings.php';
require_once realpath(__DIR__ . '/../..').'/class/UserLog.class.php';

$UserLog = new User_Log();

$MODULE_ID = NULL;
if(isset($_GET["MODULE_ID"]) && $_GET["MODULE_ID"] != ""){
    $MODULE_ID = $_GET["MODULE_ID"];
}
    $ModuleOperaionsArr = $UserLog -> GetOperations($MODULE_ID);
    if(count($ModuleOperaionsArr) > 0){
        $data = array();
        ob_clean();
        header('Content-type: application/json');
        echo json_encode( $ModuleOperaionsArr );
    }
?>
