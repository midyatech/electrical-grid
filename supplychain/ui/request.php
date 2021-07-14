<?php
include_once realpath(__DIR__ . '/../..').'/include/checksession.php';
// include_once realpath(__DIR__ . '/../..').'/include/checkpermission.php';
require_once realpath(__DIR__ . '/../..').'/class/HtmlHelper.php';
require_once realpath(__DIR__ . '/../..').'/class/SupplyChain.class.php';
//require_once realpath(__DIR__ . '/../..') . '/class/User.php';

$sc = new SupplyChain();
$warehouses = $sc->get_warehouses();
$reasons = $sc->get_request_reasons();

$type_title = "";
if (isset($_GET["t"])) {
    $type = $sc->get_request_type($_GET["t"]);
    if ($type) {
        $type_title = $type[0]["request_type"];
    }
}


$html->OpenDiv("row");
{
    $html->OpenSpan(12);
    {
        $html->OpenWidget($type_title, null, array('collapse' => true, 'fullscreen'=>true, "content"=>"form"));
        {
            $html->OpenForm( "transfer_order.php", "form2", "horizental");
            {
                $html->OpenDiv("row");
                {
                    $html->OpenSpan(6);
                    {
                        $html->DrawFormField ( "select", "source_warehouse_id", $source_warehouse, $warehouses, array("class"=>"form-control", "flow"=>"horizental", "optional"=>true) );
                    }
                    $html->CloseSpan();
                    $html->OpenSpan(6);
                    {
                        $html->DrawFormField ( "select", "sdestination_warehouse", $destination_warehouse, $warehouses, array("class"=>"form-control", "flow"=>"horizental", "optional"=>true) );
                    }
                    $html->CloseSpan();

                    $html->OpenSpan(6);
                    {
                        $html->DrawFormField ( "select", "request_reason", $request_reason, $reasons, array("class"=>"form-control", "flow"=>"horizental", "optional"=>true) );
                    }
                    $html->CloseSpan();

                    $html->OpenSpan(6);
                    {
                        $html->DrawFormField("text", "testimated_time", $estimated_time, null, array("class"=>"form-control date-picker", "flow"=>"horizental", "data-date-format"=>"yyyy-mm-dd", "readonly"=>"readonly"));
                        $html->HiddenField("request_type_id", $t);
                    }
                    $html->CloseSpan();

                }
                $html->CloseDiv();
                $formActions = array (
                    array("type"=>"submit", "name"=>"Save", "value"=>"Create", "list"=>null, "options"=>array ("class" => "btn btn-primary")),
                    array("type"=>"reset", "name"=>"Reset", "value"=>"Cancel", "list"=>null, "options"=>array ("class" => "btn btn-default resetform"))
                );

            }
            $html->CloseForm($formActions);
        }
        $html->CloseWidget();
    }
    $html->CloseSpan();

    // if ($created) {
    //     $html->OpenSpan(12);
    //     {
    //         echo "<br>";
    //         $html->OpenWidget("Order Items", null, array('collapse' => true, 'fullscreen'=>true, "content"=>"form"));
    //         {
    //             $html->OpenForm( "transfer_order.php", "form2", "horizental");
    //             {
    //                 $html->OpenDiv("well");
    //                 {
    //                     $html->OpenDiv("row");
    //                     {
    //                         $html->OpenSpan(3);
    //                         {
    //                             $html->DrawFormField ( "select", "item_type", null, $ItemTypesArr, array("class"=>"form-control", "flow"=>"horizental", "optional"=>true) );
    //                         }
    //                         $html->CloseSpan();
    //                         $html->OpenSpan(3);
    //                         {
    //                             $html->DrawFormField ( "text", "serial_number", null, $WarehouseArr, array("class"=>"form-control", "flow"=>"horizental", "optional"=>true) );
    //                         }
    //                         $html->CloseSpan();
    //                         $html->OpenSpan(3);
    //                         {
    //                             $html->DrawFormField ( "text", "quantity", null, $WarehouseArr, array("class"=>"form-control", "flow"=>"horizental", "optional"=>true) );
    //                         }
    //                         $html->CloseSpan();
    //                         $html->OpenSpan(3);
    //                         {
    //                             <div class="form-group">
    //                                 <div class="input-group">
    //                                     <span class="input-group-btn">
    //                                         <button class="btn btn-primary" type="submit" style="width:95%"><?php echo $dictionary->GetValue("add");? ></button>
    //                                     </span>
    //                                 </div>
    //                             </div>
    //                         }
    //                         $html->CloseSpan();
    //                     }
    //                     $html->CloseDiv();

    //                     $html->OpenDiv("row");
    //                     {
    //                         $html->OpenSpan(8);
    //                         {
    //                             echo "<b><< OR >> <br><br></b>";
    //                         }
    //                         $html->CloseSpan();
    //                     }
    //                     $html->CloseDiv();

    //                     $html->OpenDiv("row");
    //                     {

    //                         $html->OpenSpan(9);
    //                         {
    //                             $html->DrawFormField ( "file", "Import from File", null, $WarehouseArr, array("class"=>"form-control", "flow"=>"horizental", "optional"=>true) );
    //                         }
    //                         $html->CloseSpan();
    //                         $html->OpenSpan(3);
    //                         {
    //                             <div class="form-group">
    //                                 <div class="input-group">
    //                                     <span class="input-group-btn">
    //                                         <button class="btn btn-danger" type="submit" style="width:95%"><?php echo $dictionary->GetValue("Import");? ></button>
    //                                     </span>
    //                                 </div>
    //                             </div>
    //                         }
    //                         $html->CloseSpan();
    //                     }
    //                     $html->CloseDiv();
    //                 }
    //                 $html->CloseDiv();

    //                 $formActions = array (
    //                     array("type"=>"link", "name"=>"Finish", "value"=>"Finish Order", "list"=>"transfer_orders.php", "options"=>array ("class" => "btn btn-primary"))
    //                 );

    //                 echo '<br><br>';

    //                 $tableOptions = array();
    //                 $tableOptions["tableClass"]= "table-hover table-bordered table-condensed table-striped";
    //                 $tableOptions["key"]=array("id"=>"Serial_No");
    //                 $tableOptions["order"]=array(1, "DESC");

    //                 $cols = array();
    //                 $cols[] = array("column"=>"item_type");
    //                 $cols[] = array("column"=>"serial_number");
    //                 $cols[] = array("column"=>"quantity");
    //                 $cols[] = array("column"=>"ACTION_COL", "style"=>"width:150px","action-type"=>"ajax",
    //                     "buttons"=> array(
    //                         array("action-class"=>"transfer_details", "button-icon"=>"fa fa-remove", "title"=>$dictionary->GetValue("Delete"), "type"=>"button", 'class'=>'red')
    //                     )
    //                 );
    //                 // $items = array(
    //                 //     ["item_type"=>'meter', "serial_number"=>'EA0115602', "quantity"=>1],
    //                 //     ["item_type"=>'meter', "serial_number"=>'EA0115603', "quantity"=>1],
    //                 //     ["item_type"=>'meter', "serial_number"=>'EA0115604', "quantity"=>1],
    //                 //     ["item_type"=>'meter', "serial_number"=>'EA0115605', "quantity"=>1],
    //                 //     ["item_type"=>'meter', "serial_number"=>'EA0115606', "quantity"=>1],
    //                 //     ["item_type"=>'meter', "serial_number"=>'EA0115607', "quantity"=>1],
    //                 //     ["item_type"=>'meter', "serial_number"=>'EA0115608', "quantity"=>1],
    //                 //     ["item_type"=>'meter', "serial_number"=>'EA0115609', "quantity"=>1],
    //                 // );
    //                 $html->Table($items, $cols, $tableoptions);
    //             }
    //             $html->CloseForm($formActions);

    //         }
    //         $html->CloseWidget();
    //     }
    //     $html->CloseSpan();
    // }
}
$html->CloseDiv();

?>

