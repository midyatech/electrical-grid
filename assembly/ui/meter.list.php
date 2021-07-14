<?php
include_once realpath(__DIR__ . '/../..').'/include/checksession.php';
include_once realpath(__DIR__ . '/../..').'/include/checkpermission.php';
require_once realpath(__DIR__ . '/../..').'/class/HtmlHelper.php';
require_once realpath(__DIR__ . '/../..').'/class/Enclosure.class.php';

$html = new HTML ( $LANGUAGE );
$dictionary = new Dictionary ( $LANGUAGE );
$dictionary->GetAllDictionary ();
$options = array("class"=>"form-control");
$Enclosure = new Enclosure( );

$meter_sn = $enclosure_sn = $gateway_sn = $condition = NULL;

$from_date = date("2019-01-01");
$to_date = date("Y-m-d");

if(isset($_REQUEST["from_date"]) && $_REQUEST["from_date"] != ""){
    $from_date = $_REQUEST["from_date"];
}
$condition .= "&from_date=". $from_date;

if(isset($_REQUEST["to_date"]) && $_REQUEST["to_date"] != ""){
    $to_date = $_REQUEST["to_date"];
}
$condition .= "&to_date=". $to_date;

if(isset($_REQUEST["meter_sn"]) && $_REQUEST["meter_sn"] != ""){
    $meter_sn = $_REQUEST["meter_sn"];
    $condition .= "&meter_sn=". $meter_sn;
}

if(isset($_REQUEST["enclosure_sn"]) && $_REQUEST["enclosure_sn"] != ""){
    $enclosure_sn = $_REQUEST["enclosure_sn"];
    $condition .= "&enclosure_sn=". $enclosure_sn;
}

if(isset($_REQUEST["gateway_sn"])&&$_REQUEST["gateway_sn"]!=NULL){
    $gateway_sn = $_REQUEST["gateway_sn"];
    $condition .= "&gateway_sn=". $gateway_sn;
}

$cols = array();
$cols[] = array("column"=>"meter_sn");
$cols[] = array("column"=>"enclosure_sn");
//$cols[] = array("column"=>"gateway_sn");
$cols[] = array("column"=>"timestamp");
$cols[] = array("column"=>"ACTION_COL", "style"=>"width:150px","action-type"=>"ajax",
                    "buttons"=> array(
                        array("action-class"=>"meter_details", "button-icon"=>"fa fa-info-circle", "title"=>$dictionary->GetValue("Details"), "type"=>"button", "url"=>"href=javascript:;"),
                        array("action-class"=>"meter_edit", "button-icon"=>"fa fa-pencil", "title"=>$dictionary->GetValue("Edit"), "type"=>"button", "url"=>"href=javascript:;"),
                        array("action-class"=>"meter_delete", "button-icon"=>"fa fa-times", "title"=>$dictionary->GetValue("Delete"), "type"=>"button", "url"=>"href=javascript:;")
                    )
                );

$tableOptions = array();
$tableOptions["tableClass"]= "table-hover table-bordered table-condensed table-striped";
$tableOptions["key"]=array("id"=>"meter_id");
$html->OpenDiv("row");
{
    $html->OpenSpan(12);
    {
        $html->OpenWidget ("meter_list", NULL, array('collapse' => true, 'fullscreen'=>true,'table-actions'=>"example1"));
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
                        $html->DrawFormField ( "text", "meter_sn", $meter_sn, NULL, $options );
                    }
                    $html->CloseSpan();
                    $html->OpenSpan(2);
                    {
                        $html->DrawFormField ( "text", "enclosure_sn", $enclosure_sn, NULL, $options );
                    }
                    $html->CloseSpan();
                    $html->OpenSpan(2);
                    {
                        //$html->DrawFormField ( "text", "gateway_sn", $gateway_sn, NULL, $options );
                    }
                    $html->CloseSpan();

                    $html->OpenSpan(2);
                    {
                        ?>
                        <div class="form-group">
                            <label>&nbsp;</label>
                            <div class="input-group">
                                <span class="input-group-btn">
                                    <button class="btn green filter_meter_list" type="button"><?php echo $dictionary->GetValue("filter");?></button>
                                </span>
                                <span class="input-group-btn">
                                    <button class="btn default clear_filter_meter_list" type="button"><?php echo $dictionary->GetValue("clear");?></button>
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

            $html->Datatable("example", "api/list.meter.php?".$condition, $cols, $tableOptions);
        }
        $html->CloseWidget();
    }
    $html->CloseSpan();
}
$html->CloseDiv();
?>