<?php
/*this page lists all users for a specific directorate, used for filling lists with ajax*/
//include_once realpath(__DIR__ . '/../..').'/include/checkpermission.php';
require_once realpath(__DIR__ . '/../..').'/include/settings.php';
require_once realpath(__DIR__ . '/../..').'/class/User.php';
require_once realpath(__DIR__ . '/../..').'/class/SystemMessage.php';

$user = new User();
$message = New SysetemMessage($LANGUAGE);

if(isset($_GET["id"]) && strlen($_GET["id"]) !=""){
    $data = $user->GetUsersByDir($_GET["id"]);
    if(count($data) > 0){
        header('Content-type: application/json');
        echo json_encode( $data );
    }
}
?>
