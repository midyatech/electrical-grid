<?php
include_once realpath(__DIR__ . '/../..').'/include/checksession.php';
//include_once realpath(__DIR__ . '/../..').'/include/checkpermission.php';
require_once realpath(__DIR__ . '/../..').'/include/settings.php';
require_once realpath(__DIR__ . '/../..').'/class/Assembly.class.php';

$Assembly = new Assembly();

$aoid = NULL;
if(isset($_GET["aoid"]) && $_GET["aoid"] != ""){
    $aoid = $_GET["aoid"];
}
    $TransformerArr = $Assembly->getAssemblyOrdersTransformerArr($aoid);

    if(count($TransformerArr) > 0){
        $data = array();
        ob_clean();
        header('Content-type: application/json');
        echo json_encode( $TransformerArr );
    }
?>
