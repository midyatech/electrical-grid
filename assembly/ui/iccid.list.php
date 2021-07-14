<?php
include_once realpath(__DIR__ . '/../..').'/include/checksession.php';
include_once realpath(__DIR__ . '/../..').'/include/checkpermission.php';
require_once realpath(__DIR__ . '/../..').'/class/HtmlHelper.php';
require_once realpath(__DIR__ . '/../..').'/class/Assembly.class.php';

$html = new HTML ( $LANGUAGE );
$dictionary = new Dictionary ( $LANGUAGE );
$dictionary->GetAllDictionary ();
$Assembly = new Assembly( );

$condition = null;
$StatusArr = array(array(0=>"assembled", 1=>$dictionary->GetValue("assembled")), array(0=>"installed", 1=>$dictionary->GetValue("installed")));
$ActivationStatusArr = array(array(0=>"1", 1=>$dictionary->GetValue("Active")), array(0=>"0", 1=>$dictionary->GetValue("inActive")));

$Model = $Serial_No = $STS_No = $IMEI = $simcard_status_id = $ICCID_pattern = "";
$options = array("class"=>"form-control");

if(isset($_REQUEST["Model"]) && $_REQUEST["Model"] != ""){
    $Model = $_REQUEST["Model"];
    $condition .= "&Model=". $Model;
}

if(isset($_REQUEST["Serial_No"]) && $_REQUEST["Serial_No"] != ""){
    $Serial_No = $_REQUEST["Serial_No"];
    $condition .= "&Serial_No=". $Serial_No;
}

if(isset($_REQUEST["STS_No"]) && $_REQUEST["STS_No"] != ""){
    $STS_No = $_REQUEST["STS_No"];
    $condition .= "&STS_No=". $STS_No;
}

if(isset($_REQUEST["IMEI"]) && $_REQUEST["IMEI"] != ""){
    $IMEI = $_REQUEST["IMEI"];
    $condition .= "&IMEI=". $IMEI;
}

if(isset($_REQUEST["ip_address"]) && $_REQUEST["ip_address"] != ""){
    $ip_address = $_REQUEST["ip_address"];
    $condition .= "&ip_address=". $ip_address;
}

if(isset($_REQUEST["ICCID_pattern"]) && $_REQUEST["ICCID_pattern"] != ""){
    $ICCID_pattern = $_REQUEST["ICCID_pattern"];
    $condition .= "&ICCID_pattern=". $ICCID_pattern;
}

if(isset($_REQUEST["simcard_status_id"]) && $_REQUEST["simcard_status_id"] != ""){
    $simcard_status_id = $_REQUEST["simcard_status_id"];
    $condition .= "&simcard_status_id=". $simcard_status_id;
}

if(isset($_REQUEST["activation_date"]) && $_REQUEST["activation_date"] != ""){
    $activation_date = $_REQUEST["activation_date"];
    $condition .= "&activation_date=". $activation_date;
} else {
    $activation_date = null;
    $condition .= "&activation_date=". $activation_date;
}

if(isset($_REQUEST["from_date"]) && $_REQUEST["from_date"] != ""){
    $from_date = $_REQUEST["from_date"];
    $condition .= "&from_date=". $from_date;
} else {
    $from_date = Date('Y-m-d', strtotime("-30 days"));
    $condition .= "&from_date=". $from_date;
}

if(isset($_REQUEST["to_date"]) && $_REQUEST["to_date"] != ""){
    $to_date = $_REQUEST["to_date"];
    $condition .= "&to_date=". $to_date;
} else {
    $to_date = date("Y-m-d");
    $condition .= "&to_date=". $to_date;
}

if(isset($_REQUEST["status"]) && $_REQUEST["status"] != ""){
    $status = $_REQUEST["status"];
    $condition .= "&status=". $status;
} else {
    $status = "assembled";
    $condition .= "&status=". $status;
}

$cols = array();
$cols[] = array("column"=>"Model");
$cols[] = array("column"=>"Serial_No");
$cols[] = array("column"=>"STS_No");
$cols[] = array("column"=>"IMEI");
$cols[] = array("column"=>"ICCID");
$cols[] = array("column"=>"ip_address");
$cols[] = array("column"=>"activation_date");
$cols[] = array("column"=>"simcard_status");
/*
$cols[] = array("column"=>"ACTION_COL", "style"=>"width:150px","action-type"=>"ajax",
                    "buttons"=> array(
                        array("action-class"=>"assembly_details", "button-icon"=>"fa fa-info-circle", "title"=>$dictionary->GetValue("Details"), "type"=>"button", "url"=>"href=javascript:;")//,//add_assembly.php
                        //,array("action-class"=>"assembly_delete", "button-icon"=>"fa fa-times", "title"=>$dictionary->GetValue("Delete"), "type"=>"button", "url"=>"href=javascript:;")
                    //array("action-class"=>"assembly_edit", "button-icon"=>"fa fa-pencil", "title"=>$dictionary->GetValue("Edit"), "type"=>"link", "url"=>"add_assembly.php"),
                    )
                );
                */

$tableOptions = array();
$tableOptions["tableClass"]= "table-hover table-bordered table-condensed table-striped";
$tableOptions["key"]=array("id"=>"assembly_order_id");
$tableOptions["order"]=array(1, "DESC");

$wactions = array (
    array("type"=>"literal", "value"=>'<a href="ui/iccid.list.export.php?'.$condition.'" class="btn green" target="blank"><i class="fa fa-file-excel-o"></i></a>')
    // array ( "type"=>"button", "name"=>$dictionary->GetValue('change_simcard_status'), "value"=>$dictionary->GetValue('change_simcard_status'), "", "options"=>array ("class" => "btn green btn-sm change_gateway_status", "icon"=>"fa fa-pencil"))
    );
   // <a href="#" class="btn blue change_gateway_status"><?php echo $dictionary->GetValue("change_gateway_status");</a>

$html->OpenDiv("row");
{
    $html->OpenSpan(12);
    {
        $html->OpenWidget ("iccid_list", $wactions, array('collapse' => true, 'fullscreen'=>true,'table-actions'=>"example1"));
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
                    $html->OpenSpan(2);
                    {
                        $html->DrawFormField ( "select", "status", $status, $StatusArr, array("class"=>"form-control"));
                    }
                    $html->CloseSpan();
                    $html->OpenSpan(2);
                    {
                        $html->DrawFormField ( "text", "STS_No", $STS_No, NULL, $options );
                    }
                    $html->CloseSpan();
                    $html->OpenSpan(4);
                    {
                        $html->DrawFormField ( "textarea", "ICCID_pattern", $ICCID_pattern, null, $options);
                        // $html->DrawFormField()
                    }
                    $html->CloseSpan();
                }
                $html->CloseDiv();

                $html->OpenDiv("row");
                {
                    $html->OpenSpan(2);
                    {
                        $html->DrawFormField ( "text", "Model", $Model, NULL, $options );
                    }
                    $html->CloseSpan();
                    $html->OpenSpan(2);
                    {
                        $html->DrawFormField ( "text", "Serial_No", $Serial_No, NULL, $options );
                    }
                    $html->CloseSpan();
                    $html->OpenSpan(2);
                    {
                        $html->DrawFormField ( "select", "simcard_status_id", $simcard_status_id, $ActivationStatusArr, array("class"=>"form-control", "optional"=>"true"));
                    }
                    $html->CloseSpan();
                    $html->OpenSpan(2);
                    {
                        $html->DrawFormField ( "text", "activation_date", $activation_date, NULL, array("class"=>"form-control date-picker", "data-date-format"=>"yyyy-mm-dd", "readonly"=>"readonly"));
                    }
                    $html->CloseSpan();
                    $html->OpenSpan(2);
                    {
                        $html->DrawFormField ( "text", "IMEI", $IMEI, null, $options);
                    }
                    $html->CloseSpan();
                    $html->OpenSpan(2);
                    {
                        $html->DrawFormField ( "text", "ip_address", $ip_address, null, $options);
                    }
                    $html->CloseSpan();
                    $html->OpenSpan(2);
                    {
                        ?>
                        <div class="form-group">
                            <label>&nbsp;</label>
                            <div class="input-group">
                                <span class="input-group-btn">
                                    <button class="btn green" type="submit"><?php echo $dictionary->GetValue("filter");?></button>
                                </span>
                                <!-- <span class="input-group-btn">
                                    <a href="#" class="btn blue change_gateway_status"><?php echo $dictionary->GetValue("change_gateway_status");?></a>
                                </span> -->
                            </div>
                        </div>
                        <?php
                    }
                    $html->CloseSpan();
                }
                $html->CloseDiv();
                }
                $html->CloseForm();

            $html->Datatable("example", "api/list.iccid.php?".$condition, $cols, $tableOptions);
        }
        $html->CloseWidget();
    }
    $html->CloseSpan();
}
$html->CloseDiv();
?>





