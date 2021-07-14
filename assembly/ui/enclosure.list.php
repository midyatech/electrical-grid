<?php
// error_reporting(E_ALL);
// ini_set('display_errors', 1);
include_once realpath(__DIR__ . '/../..').'/include/checksession.php';
include_once realpath(__DIR__ . '/../..').'/include/checkpermission.php';
require_once realpath(__DIR__ . '/../..').'/class/HtmlHelper.php';
require_once realpath(__DIR__ . '/../..').'/class/Enclosure.class.php';

$html = new HTML ( $LANGUAGE );
$dictionary = new Dictionary ( $LANGUAGE );
$dictionary->GetAllDictionary ();
$options = array("class"=>"form-control");
$Enclosure = new Enclosure( );
$description_key = "description_enclosure";
$condition=null;
$transformer_number=$gateway_id=$enclosure_id=$meter_id=$enclosure_sn=$assembly_order=$enclosure_type=$includes_gateway=$simcard_status_id=null;

$AssemblyOrderArr = $Enclosure->getActiveAssemblyOrdersArr();
$hasGatewayArr = array(array("1", $dictionary->GetValue("includes_gateway")), array("0", $dictionary->GetValue("does_not_include_gateway")));
$enclosureTypeArr = array(array("1", $dictionary->GetValue("single_phase")), array("2", $dictionary->GetValue("three_phase")), array("3", $dictionary->GetValue("three_phase_CT")));
$ActivationStatusArr = array(array(0=>"1", 1=>$dictionary->GetValue("Active")), array(0=>"0", 1=>$dictionary->GetValue("inActive")));

$from_date = date("Y-m-d");//, time() - 2628000 );
$to_date = date("Y-m-d");

if(isset($_REQUEST["from_date"]) && $_REQUEST["from_date"] != ""){
    $from_date = $_REQUEST["from_date"];
}
$condition .= "&from_date=". $from_date;

if(isset($_REQUEST["to_date"]) && $_REQUEST["to_date"] != ""){
    $to_date = $_REQUEST["to_date"];
}
$condition .= "&to_date=". $to_date;

if(isset($_REQUEST["assembly_order"]) && $_REQUEST["assembly_order"] != ""){
    $assembly_order = $_REQUEST["assembly_order"];
    $condition .= "&assembly_order=". $assembly_order;
}

if(isset($_REQUEST["transformer_number"]) && $_REQUEST["transformer_number"] != ""){
    $transformer_number = $_REQUEST["transformer_number"];
    $condition .= "&transformer_number=". $transformer_number;
}

if(isset($_REQUEST["simcard_status_id"]) && $_REQUEST["simcard_status_id"] != ""){
    $simcard_status_id = $_REQUEST["simcard_status_id"];
    $condition .= "&simcard_status_id=". $simcard_status_id;
}

if(isset($_REQUEST["enclosure_sn"]) && $_REQUEST["enclosure_sn"] != ""){
    $enclosure_sn = $_REQUEST["enclosure_sn"];
    $condition .= "&enclosure_sn=". $enclosure_sn;
}

if(isset($_REQUEST["gateway_id"])&&$_REQUEST["gateway_id"]!=NULL){
    $gateway_id = $_REQUEST["gateway_id"];
    $condition .= "&gateway_id=". $gateway_id;
}

if(isset($_REQUEST["includes_gateway"])&&$_REQUEST["includes_gateway"]!=NULL){
    $includes_gateway = $_REQUEST["includes_gateway"];
    $condition .= "&includes_gateway=". $includes_gateway;
}

if(isset($_REQUEST["enclosure_type"])&&$_REQUEST["enclosure_type"]!=NULL){
    $enclosure_type = $_REQUEST["enclosure_type"];
    $condition .= "&enclosure_type=". $enclosure_type;
}

$cols = array();
$cols[] = array("column"=>"enclosure_sn");
$cols[] = array("column"=>"gateway_sn");
$cols[] = array("column"=>"enclosure_type");
$cols[] = array("column"=>"configuration_name");
$cols[] = array("column"=>"assembly_order_code");
$cols[] = array("column"=>"transformer_number");
$cols[] = array("column"=>"transformer_generated_number");
$cols[] = array("column"=>"timestamp");
$cols[] = array("column"=>"meter_count");
$cols[] = array("column"=>"NAME");

if($user-> CheckPermission($USERID, "permission_add_enclosure") == 1) {
    $buttons = array(
        // array("action-class"=>"enclosure_trace", "button-icon"=>"fa fa-eye", "title"=>$dictionary->GetValue("Trace"), "type"=>"button", "url"=>"href=javascript:;"),
        array("action-class"=>"enclosure_details", "button-icon"=>"fa fa-info-circle", "title"=>$dictionary->GetValue("Details"), "type"=>"button", "url"=>"href=javascript:;"),
        array("action-class"=>"enclosure_delete", "button-icon"=>"fa fa-times", "title"=>$dictionary->GetValue("Delete"), "type"=>"button", "url"=>"href=javascript:;")
    );
} else {
    $buttons = array(
        array("action-class"=>"enclosure_details", "button-icon"=>"fa fa-info-circle", "title"=>$dictionary->GetValue("Details"), "type"=>"button", "url"=>"href=javascript:;")
    );
}



$cols[] = array("column"=>"ACTION_COL", "style"=>"width:150px","action-type"=>"ajax",
                    "buttons"=> $buttons
                    /*
                    array(
                        array("action-class"=>"enclosure_details", "button-icon"=>"fa fa-info-circle", "title"=>$dictionary->GetValue("Details"), "type"=>"button", "url"=>"href=javascript:;") ,
                        array("action-class"=>"enclosure_delete", "button-icon"=>"fa fa-times", "title"=>$dictionary->GetValue("Delete"), "type"=>"button", "url"=>"href=javascript:;")
                        //array("action-class"=>"enclosure_edit", "button-icon"=>"fa fa-pencil", "title"=>$dictionary->GetValue("Edit"), "type"=>"link", "url"=>"add_enclosure.php"),
                    )
                    */
                );

$tableOptions = array("table_scrollable"=>true);
$tableOptions["tableClass"]= "table-hover table-bordered table-condensed table-striped";
$tableOptions["key"]=array("id"=>"enclosure_id");
$html->OpenDiv("row");
{
    $html->OpenSpan(12);
    {
        $actions = array (
                array("type"=>"literal", "value"=>'<a href="ui/enclosure.list.export.php?'.$condition.'" class="btn green" target="blank"><i class="fa fa-file-excel-o"></i></a>')
                //,array ( "type"=>"link", "name"=>"Add", "value"=>"Add", "list"=>"scan_enclosure.php", "options"=>array ("class" => "btn green btn-sm add_enclosure", "icon"=>"fa fa-plus"))
                );
        $html->OpenWidget ("enclosure_list", $actions, array('collapse' => true, 'fullscreen'=>true,'table-actions'=>"example1"));
        {
            $html->OpenForm ( null, "form3" );
            {
                $html->OpenDiv("row");
                {
                    $html->OpenSpan(2);
                    {
                        $html->DrawFormField ( "text", "from_date", $from_date, NULL, array("class"=>"form-control date-picker", "data-date-format"=>"yyyy-mm-dd", "readonly"=>"readonly") );
                    }
                    $html->CloseSpan();
                    $html->OpenSpan(2);
                    {
                        $html->DrawFormField ( "text", "to_date", $to_date, NULL, array("class"=>"form-control date-picker", "data-date-format"=>"yyyy-mm-dd", "readonly"=>"readonly") );
                    }
                    $html->CloseSpan();
                    $html->OpenSpan(3);
                    {
                        $html->DrawFormField ( "select", "assembly_order", $assembly_order, $AssemblyOrderArr, array("class"=>"form-control", "optional"=>true) );
                    }
                    $html->CloseSpan();
                    $html->OpenSpan(3);
                    {
                        $html->DrawFormField ( "text", "transformer_number", $transformer_number, NULL, $options );
                    }
                    $html->CloseSpan();
                    $html->OpenSpan(2);
                    {
                        $html->DrawFormField ( "select", "simcard_status_id", $simcard_status_id, $ActivationStatusArr, array("class"=>"form-control", "optional"=>"true"));
                    }
                    $html->CloseSpan();
                }
                $html->CloseDiv();

                $html->OpenDiv("row");
                {
                    $html->OpenSpan(3);
                    {
                        $html->DrawFormField ( "select", "includes_gateway", $includes_gateway, $hasGatewayArr, array("class"=>"form-control", "optional"=>true) );
                    }
                    $html->CloseSpan();
                    $html->OpenSpan(3);
                    {
                        $html->DrawFormField ( "select", "enclosure_type", $enclosure_type, $enclosureTypeArr, array("class"=>"form-control", "optional"=>true) );
                    }
                    $html->CloseSpan();
                    $html->OpenSpan(2);
                    {
                        $html->DrawFormField ( "text", "enclosure_sn", $enclosure_sn, NULL, $options );
                    }
                    $html->CloseSpan();
                    $html->OpenSpan(2);
                    {
                        $html->DrawFormField ( "text", "gateway_id", $gateway_id, NULL, $options );
                    }
                    $html->CloseSpan();


                    $html->OpenSpan(2);
                    {
                        ?>
                        <div class="form-group">
                            <label>&nbsp;</label>
                            <div class="input-group">
                                <span class="input-group-btn">
                                    <button class="btn green filter_enclosure_list" type="button"><?php echo $dictionary->GetValue("filter");?></button>
                                </span>
                                <span class="input-group-btn">
                                    <button class="btn default clear_filter_enclosure_list" type="button"><?php echo $dictionary->GetValue("clear");?></button>
                                </span>
                            </div>
                        </div>
                        <?php
                    }
                    $html->CloseSpan();
                }
                $html->CloseDiv();
            }
            $html->CloseForm();

            $html->Datatable("example", "api/list.enclosure.php?".$condition, $cols, $tableOptions);
        }
        $html->CloseWidget();
    }
    $html->CloseSpan();
}
$html->CloseDiv();
?>
<script>
$(function() {
    $("body").on("click", ".enclosure_trace", function() {
        OpenModal("../supplychain/ui/trace.php");
    });
});
</script>