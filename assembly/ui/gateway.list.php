<?php
include_once realpath(__DIR__ . '/../..').'/include/checksession.php';
include_once realpath(__DIR__ . '/../..').'/include/checkpermission.php';
require_once realpath(__DIR__ . '/../..').'/class/HtmlHelper.php';
require_once realpath(__DIR__ . '/../..').'/class/Enclosure.class.php';

$html = new HTML ( $LANGUAGE );
$dictionary = new Dictionary ( $LANGUAGE );
$dictionary->GetAllDictionary ();
$Enclosure = new Enclosure( );

$options = array("class"=>"form-control");
$ActivationStatusArr = array(array(0=>"1", 1=>$dictionary->GetValue("Active")), array(0=>"0", 1=>$dictionary->GetValue("inActive")));


$gateway_sn = $enclosure_sn = $ICCID = $condition = $simcard_status_id = $ip_address = NULL;
/*
$from_date = date("2019-01-01");
$to_date = date("Y-m-d");
*/
$from_activation_date = $to_activation_date = NULL;

if(isset($_REQUEST["from_activation_date"]) && $_REQUEST["from_activation_date"] != ""){
    $from_activation_date = $_REQUEST["from_activation_date"];
}
$condition .= "&from_activation_date=". $from_activation_date;

if(isset($_REQUEST["to_activation_date"]) && $_REQUEST["to_activation_date"] != ""){
    $to_activation_date = $_REQUEST["to_activation_date"];
}
$condition .= "&to_activation_date=". $to_activation_date;

if(isset($_REQUEST["gateway_sn"])&&$_REQUEST["gateway_sn"]!=NULL){
    $gateway_sn = $_REQUEST["gateway_sn"];
    $condition .= "&gateway_sn=". $gateway_sn;
}

if(isset($_REQUEST["enclosure_sn"]) && $_REQUEST["enclosure_sn"] != ""){
    $enclosure_sn = $_REQUEST["enclosure_sn"];
    $condition .= "&enclosure_sn=". $enclosure_sn;
}

if(isset($_REQUEST["ICCID"]) && $_REQUEST["ICCID"] != ""){
    $ICCID = $_REQUEST["ICCID"];
    $condition .= "&ICCID=". $ICCID;
}

if(isset($_REQUEST["simcard_status_id"]) && $_REQUEST["simcard_status_id"] != ""){
    $simcard_status_id = $_REQUEST["simcard_status_id"];
    $condition .= "&simcard_status_id=". $simcard_status_id;
}

if(isset($_REQUEST["ip_address"]) && $_REQUEST["ip_address"] != ""){
    $ip_address = $_REQUEST["ip_address"];
    $condition .= "&ip_address=". $ip_address;
}

$cols = array();
$cols[] = array("column"=>"gateway_sn");
$cols[] = array("column"=>"enclosure_sn");
$cols[] = array("column"=>"ICCID");
$cols[] = array("column"=>"simcard_status");
$cols[] = array("column"=>"activation_date");
$cols[] = array("column"=>"ip_address");
$cols[] = array("column"=>"ACTION_COL", "style"=>"width:150px","action-type"=>"ajax",
                    "buttons"=> array(
                        array("action-class"=>"gateway_trace", "button-icon"=>"fa fa-list", "title"=>$dictionary->GetValue("Trace"), "type"=>"button", "url"=>"href=javascript:;"),
                    )
                );
/*
$cols[] = array("column"=>"ACTION_COL", "style"=>"width:150px","action-type"=>"ajax",
                    "buttons"=> array(
                        array("action-class"=>"gateway_details", "button-icon"=>"fa fa-info-circle", "title"=>$dictionary->GetValue("Details"), "type"=>"button", "url"=>"href=javascript:;"),
                        array("action-class"=>"gateway_edit", "button-icon"=>"fa fa-pencil", "title"=>$dictionary->GetValue("Edit"), "type"=>"button", "url"=>"href=javascript:;"),
                        array("action-class"=>"gateway_delete", "button-icon"=>"fa fa-times", "title"=>$dictionary->GetValue("Delete"), "type"=>"button", "url"=>"href=javascript:;")
                    )
                );
*/

$tableOptions = array();
$tableOptions["tableClass"]= "table-hover table-bordered table-condensed table-striped";
$tableOptions["key"]=array("id"=>"gateway_id");

$wactions = array(
    array("type"=>"literal", "value"=>'<a href="ui/gateway.list.export.php?'.$condition.'" class="btn green" target="blank"><i class="fa fa-file-excel-o"></i></a>')
);

$html->OpenDiv("row");
{
    $html->OpenSpan(12);
    {
        $html->OpenWidget ("gateway_list", $wactions, array('collapse' => true, 'fullscreen'=>true,'table-actions'=>"example1"));
        {
            $html->OpenForm ( null, "form3" );
            {
                $html->OpenDiv("row");
                {
                    $html->OpenSpan(2);
                    {
                        $html->DrawFormField ( "text", "from_activation_date", $from_activation_date, NULL, array("class"=>"form-control date-picker", "data-date-format"=>"yyyy-mm-dd", "readonly"=>"readonly") );
                    }
                    $html->CloseSpan();
                    $html->OpenSpan(2);
                    {
                        $html->DrawFormField ( "text", "to_activation_date", $to_activation_date, NULL, array("class"=>"form-control date-picker", "data-date-format"=>"yyyy-mm-dd", "readonly"=>"readonly") );
                    }
                    $html->CloseSpan();
                    $html->OpenSpan(2);
                    {
                        $html->DrawFormField ( "text", "gateway_sn", $gateway_sn, NULL, $options );
                    }
                    $html->CloseSpan();
                    $html->OpenSpan(2);
                    {
                        $html->DrawFormField ( "text", "enclosure_sn", $enclosure_sn, NULL, $options );
                    }
                    $html->CloseSpan();
                    $html->OpenSpan(2);
                    {
                        $html->DrawFormField ( "text", "ICCID", $ICCID, NULL, $options );
                    }
                    $html->CloseSpan();
                    $html->OpenSpan(2);
                    {
                        $html->DrawFormField ( "select", "simcard_status_id", $simcard_status_id, $ActivationStatusArr, array("class"=>"form-control", "optional"=>"true"));
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
                                    <button class="btn green filter_gateway_list" type="button"><?php echo $dictionary->GetValue("filter");?></button>
                                </span>
                                <span class="input-group-btn">
                                    <button class="btn default clear_filter_gateway_list" type="button"><?php echo $dictionary->GetValue("clear");?></button>
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

            $html->Datatable("example", "api/list.gateway.php?".$condition, $cols, $tableOptions);
        }
        $html->CloseWidget();
    }
    $html->CloseSpan();
}
$html->CloseDiv();
?>

<script>
$(function() {

    $('body').on('click', '.gateway_trace', function () {
        id = $(this).data('id');
        window.location.href = "../trace/gateway_trace.php?id=" + id;
    });
});
</script>