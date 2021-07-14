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
$description_key = "description_assembly";
$condition=null;
$StatusArr = array(array("1", $dictionary->GetValue("active")), array("0", $dictionary->GetValue("inactive")));

$assembly_order_code=null;

if(isset($_REQUEST["assembly_order_code"]) && $_REQUEST["assembly_order_code"] != ""){
    $assembly_order_code = $_REQUEST["assembly_order_code"];
    $condition .= "&assembly_order_code=". $assembly_order_code;
}

if(isset($_REQUEST["status"]) && $_REQUEST["status"] != ""){
    $status = $_REQUEST["status"];
    $condition .= "&status=". $status;
} else {
    $status = 1;
    $condition .= "&status=". $status;
}


$cols = array();
$cols[] = array("column"=>"assembly_order_code");
$cols[] = array("column"=>"create_date");
$cols[] = array("column"=>"start_date");
$cols[] = array("column"=>"NAME");
$cols[] = array("column"=>"ACTION_COL", "style"=>"width:150px","action-type"=>"ajax",
                    "buttons"=> array(
                        array("action-class"=>"assembly_details", "button-icon"=>"fa fa-info-circle", "title"=>$dictionary->GetValue("Details"), "type"=>"button", "url"=>"href=javascript:;")//,//add_assembly.php
                        //,array("action-class"=>"assembly_delete", "button-icon"=>"fa fa-times", "title"=>$dictionary->GetValue("Delete"), "type"=>"button", "url"=>"href=javascript:;")
                    //array("action-class"=>"assembly_edit", "button-icon"=>"fa fa-pencil", "title"=>$dictionary->GetValue("Edit"), "type"=>"link", "url"=>"add_assembly.php"),
                    )
                );

$tableOptions = array();
$tableOptions["tableClass"]= "table-hover table-bordered table-condensed table-striped";
$tableOptions["key"]=array("id"=>"assembly_order_id");
$tableOptions["order"]=array(1, "DESC");

$html->OpenDiv("row");
{
    $html->OpenSpan(12);
    {
        $actions = array (
                array ( "type"=>"link", "name"=>"Add", "value"=>"Add", "list"=>"add_assembly.php", "options"=>array ("class" => "btn green btn-sm add_assembly", "icon"=>"fa fa-plus"))
                );
        $html->OpenWidget ("assembly_list", $actions, array('collapse' => true, 'fullscreen'=>true,'table-actions'=>"example1"));
        {
            $html->OpenForm ( null, "form3" );
            {
                $html->OpenDiv("row");
                {
                    $html->OpenSpan(2);
                    {
                        $html->DrawFormField ( "text", "assembly_order_code", $assembly_order_code, NULL, $options );
                    }
                    $html->CloseSpan();
                    $html->OpenSpan(2);
                    {
                        $html->DrawFormField ( "select", "status", $status, $StatusArr, $options );
                    }
                    $html->CloseSpan();

                    $html->OpenSpan(2);
                    {
                        ?>
                        <div class="form-group">
                            <label>&nbsp;</label>
                            <div class="input-group">
                                <span class="input-group-btn">
                                    <button class="btn green filter_assembly_list" type="submit"><?php echo $dictionary->GetValue("filter");?></button>
                                </span>
                                <span class="input-group-btn">
                                    <button class="btn default clear_filter_assembly_list" type="button"><?php echo $dictionary->GetValue("clear");?></button>
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

            $html->Datatable("example", "api/list.assembly.php?".$condition, $cols, $tableOptions);
        }
        $html->CloseWidget();
    }
    $html->CloseSpan();
}
$html->CloseDiv();
?>