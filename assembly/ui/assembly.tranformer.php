<?php
include_once realpath(__DIR__ . '/../..').'/include/settings.php';
include_once realpath(__DIR__ . '/../..').'/include/checksession.php';
//include_once realpath(__DIR__ . '/../..').'/include/checkpermission.php';
require_once realpath(__DIR__ . '/../..').'/class/HtmlHelper.php';
require_once realpath(__DIR__ . '/../..') . '/class/Dictionary.php';
require_once realpath(__DIR__ . '/../..') . '/class/Assembly.class.php';

$dictionary = new Dictionary($LANGUAGE);
$dictionary->GetAllDictionary();
$html = new HTML($LANGUAGE);
$assembly = new Assembly();

if (isset($id)) {
    $transformer_id = $id;
} else if (isset($_GET["id"])) {
    $transformer_id = $_GET["id"];
}

if (isset($aoid)) {
    $assembly_id = $aoid;
} else if (isset($_GET["aoid"])) {
    $assembly_id = $_GET["aoid"];
}

if (isset($id)) {
    $order = $assembly->GetAssemblyOrder($assembly_id);
    $Transfomer = $assembly->getAssemblyOrderConfigByTransfomer($assembly_id, null, $transformer_id);
    $Enclosures = $assembly->getEnclosuresByTransformer($assembly_id, $transformer_id);

    $TotalEnclosuresCount = $TotalManufacturedCount = 0;
    for($i=0; $i<count($Transfomer); $i++){
        $Transfomer[$i]["enclosure_type"] = $Transfomer[$i]["enclosure_type"]." [".$Transfomer[$i]["configuration_name"]."]";
        $TotalEnclosuresCount += $Transfomer[$i]["enclosures_count"];
        $TotalManufacturedCount += $Transfomer[$i]["manufactured_count"];
        if( $Transfomer[$i]["enclosures_count"] - $Transfomer[$i]["manufactured_count"] == 0 ){
            $color = "success";
        } else {
            $color = "danger";
        }
        $Transfomer[$i]["difference"] = '<span class="label label-'.$color.'"><b>'.($Transfomer[$i]["enclosures_count"] - $Transfomer[$i]["manufactured_count"]).'</b></span>';
    }

    $Transfomer[$i]["enclosure_type"] = '<b>'.$dictionary->GetValue("total").'</b>';
    $Transfomer[$i]["enclosures_count"] = '<b>'.$TotalEnclosuresCount.'</b>';
    $Transfomer[$i]["manufactured_count"] = '<b>'.$TotalManufacturedCount.'</b>';
    if( $TotalEnclosuresCount - $TotalManufacturedCount == 0 ){
        $color = "success";
    } else {
        $color = "danger";
    }
    $Transfomer[$i]["difference"] = '<span class="label label-'.$color.'"><b>'.($TotalEnclosuresCount - $TotalManufacturedCount).'</b></span>';

    $html->OpenSpan(12);
    {
        $cols = array();
        $cols[] = array("column"=>"enclosure_type");
        $cols[] = array("column"=>"enclosures_count");
        $cols[] = array("column"=>"manufactured_count");
        $cols[] = array("column"=>"difference");
        //$cols[] = array("column"=>"remaining_enclosures");
        $cols[] = array("column"=>"ACTION_COL", "style"=>"width:100px","action-type"=>"ajax",
                        "buttons"=> array(
                            array("action-class"=>"add_enclosure", "button-icon"=>"fa fa-plus", "type"=>"button", "title"=>$dictionary->GetValue("add_new_enclosure")
                            ,
                            "filter"=>array("enclosure_config_id"=> ["operator"=>"!=", "value"=>""],
                                            "remaining_enclosures"=>["operator"=>">", "value"=>"0"]
                                            )
                            )
                        )
                    );

        if ($order[0]["is_extra_stock"] == 1) {
            echo '<h3>Assembly Order: <b>'.$order[0]["assembly_order_code"].'</b></h3>';
        } else {
            echo '<h3>Feeder : <b>'.$Transfomer[0]["feeder"].'</b></h3>';
            echo '<h3>Transfomer : <b>'.$Transfomer[0]["transformer_number"].' ['.$Transfomer[0]["transformer_generated_number"].']</b></h3>';
        }

        $tableOptions = array();
        $tableOptions["key"]= ["enclosure_config_id"=>"enclosure_config_id"];
        $html->Table($Transfomer, $cols, $tableOptions);
    }
    $html->CloseSpan();


    $html->OpenSpan(12);
    {
        $cols1 = array();
        $cols1[] = array("column"=>"#");
        $cols1[] = array("column"=>"enclosure_type");
        //$cols1[] = array("column"=>"configuration_name");
        $cols1[] = array("column"=>"enclosure_sn");
        $cols1[] = array("column"=>"gateway_sn");
        $cols1[] = array("column"=>"Meter1");
        $cols1[] = array("column"=>"Meter2");
        $cols1[] = array("column"=>"Meter3");
        $cols1[] = array("column"=>"Meter4");
        $cols1[] = array("column"=>"Meter5");
        $cols1[] = array("column"=>"Meter6");
        $tableOptions1 = array();
        $tableOptions1["tableClass"]= "table-hover table-bordered table-condensed table-striped";

        //print count($Enclosures);
        if( is_array($Enclosures) && count($Enclosures) > 0 ) {
            for($i=0; $i<count($Enclosures); $i++){
                $Enclosures[$i]["#"] = $i+1;
                $Enclosures[$i]["enclosure_type"] = $Enclosures[$i]["enclosure_type"]." [".$Enclosures[$i]["configuration_name"]."]";
            }
            echo '<br/><h2>'.$dictionary->GetValue("enclosures").'</h2>';
            $html->Table($Enclosures, $cols1, $tableOptions1);
        }

        $html->HiddenField("transformer_id", $id);
        $html->HiddenField("assembly_order_id", $assembly_id);
    }
}

?>

