<?php
include_once realpath(__DIR__ . '/../..').'/include/checksession.php';
include_once realpath(__DIR__ . '/../..').'/include/checkpermission.php';
require_once realpath(__DIR__ . '/../..').'/class/HtmlHelper.php';
require_once realpath(__DIR__ . '/../..').'/class/SupplyChain.class.php';
//require_once realpath(__DIR__ . '/../..') . '/class/User.php';

$OrderTypesArr = array(
    [0=>'1', 1=>'Receive'],
    [0=>'2', 1=>'Request'],
    [0=>'3', 1=>'Return'],
    [0=>'4', 1=>'Assemble'],
    [0=>'5', 1=>'Destroy']
);

$ItemTypesArr = array(
    [0=>'1', 1=>'Enclosure'],
    [0=>'2', 1=>'Meter'],
    [0=>'3', 1=>'Gateway'],
    [0=>'4', 1=>'Busbar'],
    [0=>'5', 1=>'Crircuit Breaker'],
    [0=>'6', 1=>'Rail'],
);

$WarehouseArr = array(
    [0=>'1', 1=>'Warehouse 1'],
    [0=>'2', 1=>'Warehouse 2'],
    [0=>'3', 1=>'Warehouse 3'],
    [0=>'4', 1=>'Warehouse 4']
);

$target_warehouse_label = "Target Warehouse";
if (isset($_REQUEST["type"]) || isset($_REQUEST["type"])) {
    $order_type = $_REQUEST["type"];
}


$created = false;
if (isset($_REQUEST["order_type"]) || isset($_REQUEST["item_type"])) {
    $created = true;
    if (isset($_REQUEST["order_type"])) {
        $order_type= $_SESSION["order_type"] = $_REQUEST["order_type"] ;
        $target_warehouse = $_SESSION["target_warehouse"] = $_REQUEST["target_warehouse"];
    }

    if (isset($_REQUEST["item_type"])) {
        $order_type = $_SESSION["order_type"];
        $target_warehouse = $_SESSION["target_warehouse"];
    }
}


if ($order_type == 1) {
    $target_warehouse_label = "From Warehouse";
} else if ($order_type == 2) {
    $target_warehouse_label = "To Warehouse";
}




$items = array();
if (isset($_REQUEST["item_type"])){
    $items = $_SESSION["items"];
    foreach ($ItemTypesArr as $item) {
        if ($item[0] == $_REQUEST["item_type"]) {
            $type = $item[1];
        }
    }
    $items[] = ["item_type"=>$type, "serial_number"=>$_REQUEST["serial_number"], "quantity"=>$_REQUEST["quantity"]];
}

$_SESSION["items"] = $items;
?>
<div class="tabbable-line tabbable-full-width">

    <div class="tab-content">

    <?php

    $html->OpenDiv("row");
    {
        $html->OpenSpan(12);
        {
            $html->OpenWidget("Transfer Order", null, array('collapse' => true, 'fullscreen'=>true, "content"=>"form"));
            {
                $html->OpenForm( "transfer_order.php", "form2", "horizental");
                {
                    $html->OpenDiv("row");
                    {
                        $html->OpenSpan(6);
                        {
                            $html->DrawFormField ( "select", "order_type", $order_type, $OrderTypesArr, array("class"=>"form-control", "flow"=>"horizental", "optional"=>true) );
                        }
                        $html->CloseSpan();
                        $html->OpenSpan(6);
                        {
                            $html->DrawFormField ( "select", $target_warehouse_label, $target_warehouse, $WarehouseArr, array("class"=>"form-control", "flow"=>"horizental", "optional"=>true) );
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

        if ($created) {
            $html->OpenSpan(12);
            {
                echo "<br>";
                $html->OpenWidget("Order Items", null, array('collapse' => true, 'fullscreen'=>true, "content"=>"form"));
                {
                    $html->OpenForm( "transfer_order.php", "form2", "horizental");
                    {
                        $html->OpenDiv("well");
                        {
                            $html->OpenDiv("row");
                            {
                                $html->OpenSpan(3);
                                {
                                    $html->DrawFormField ( "select", "item_type", null, $ItemTypesArr, array("class"=>"form-control", "flow"=>"horizental", "optional"=>true) );
                                }
                                $html->CloseSpan();
                                $html->OpenSpan(3);
                                {
                                    $html->DrawFormField ( "text", "serial_number", null, $WarehouseArr, array("class"=>"form-control", "flow"=>"horizental", "optional"=>true) );
                                }
                                $html->CloseSpan();
                                $html->OpenSpan(3);
                                {
                                    $html->DrawFormField ( "text", "quantity", null, $WarehouseArr, array("class"=>"form-control", "flow"=>"horizental", "optional"=>true) );
                                }
                                $html->CloseSpan();
                                $html->OpenSpan(3);
                                {
                                    ?>
                                    <div class="form-group">
                                        <div class="input-group">
                                            <span class="input-group-btn">
                                                <button class="btn btn-primary" type="submit" style="width:95%"><?php echo $dictionary->GetValue("add");?></button>
                                            </span>
                                        </div>
                                    </div>
                                    <?php
                                }
                                $html->CloseSpan();
                            }
                            $html->CloseDiv();

                            $html->OpenDiv("row");
                            {
                                $html->OpenSpan(8);
                                {
                                    echo "<b><< OR >> <br><br></b>";
                                }
                                $html->CloseSpan();
                            }
                            $html->CloseDiv();

                            $html->OpenDiv("row");
                            {

                                $html->OpenSpan(9);
                                {
                                    $html->DrawFormField ( "file", "Import from File", null, $WarehouseArr, array("class"=>"form-control", "flow"=>"horizental", "optional"=>true) );
                                }
                                $html->CloseSpan();
                                $html->OpenSpan(3);
                                {
                                    ?>
                                    <div class="form-group">
                                        <div class="input-group">
                                            <span class="input-group-btn">
                                                <button class="btn btn-danger" type="submit" style="width:95%"><?php echo $dictionary->GetValue("Import");?></button>
                                            </span>
                                        </div>
                                    </div>
                                    <?php
                                }
                                $html->CloseSpan();
                            }
                            $html->CloseDiv();
                        }
                        $html->CloseDiv();

                        $formActions = array (
                            array("type"=>"link", "name"=>"Finish", "value"=>"Finish Order", "list"=>"transfer_orders.php", "options"=>array ("class" => "btn btn-primary"))
                        );

                        echo '<br><br>';

                        $tableOptions = array();
                        $tableOptions["tableClass"]= "table-hover table-bordered table-condensed table-striped";
                        $tableOptions["key"]=array("id"=>"Serial_No");
                        $tableOptions["order"]=array(1, "DESC");

                        $cols = array();
                        $cols[] = array("column"=>"item_type");
                        $cols[] = array("column"=>"serial_number");
                        $cols[] = array("column"=>"quantity");
                        $cols[] = array("column"=>"ACTION_COL", "style"=>"width:150px","action-type"=>"ajax",
                            "buttons"=> array(
                                array("action-class"=>"transfer_details", "button-icon"=>"fa fa-remove", "title"=>$dictionary->GetValue("Delete"), "type"=>"button", 'class'=>'red')
                            )
                        );
                        // $items = array(
                        //     ["item_type"=>'meter', "serial_number"=>'EA0115602', "quantity"=>1],
                        //     ["item_type"=>'meter', "serial_number"=>'EA0115603', "quantity"=>1],
                        //     ["item_type"=>'meter', "serial_number"=>'EA0115604', "quantity"=>1],
                        //     ["item_type"=>'meter', "serial_number"=>'EA0115605', "quantity"=>1],
                        //     ["item_type"=>'meter', "serial_number"=>'EA0115606', "quantity"=>1],
                        //     ["item_type"=>'meter', "serial_number"=>'EA0115607', "quantity"=>1],
                        //     ["item_type"=>'meter', "serial_number"=>'EA0115608', "quantity"=>1],
                        //     ["item_type"=>'meter', "serial_number"=>'EA0115609', "quantity"=>1],
                        // );
                        $html->Table($items, $cols, $tableoptions);
                    }
                    $html->CloseForm($formActions);

                }
                $html->CloseWidget();
            }
            $html->CloseSpan();
        }
    }
    $html->CloseDiv();



    ?>
    </div>
</div>
