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

$WarehouseArr = array(
    [0=>'0', 1=>'Warehouse 1'],
    [0=>'1', 1=>'Warehouse 2'],
    [0=>'2', 1=>'Warehouse 3'],
    [0=>'3', 1=>'Warehouse 4']
);


?>
<div class="tabbable-line tabbable-full-width">
    <!-- <ul class="nav nav-tabs">
        <li class="">
            <a href="stock.php"> <h3><b>Stock</b></h3> </a>
        </li>
        <li class="active">
            <a href="transfer_orders.php"> <h3><b>Transfer Orders</b></h3> </a>
        </li>
        <li class="">
            <a href="transfer_order.php"> <h3><b>Create Order</b></h3> </a>
        </li>
    </ul> -->
    <div class="tab-content">

    <?php
    $html->OpenDiv("row");
    {
        $html->OpenSpan(12);
        {
            $actions = array (
                array ( "type"=>"link", "name"=>"Change", "value"=>"Back to Orders", "list"=>"transfer_orders.php", "options"=>array ("class" => "btn grey-cararra group_add", "icon"=>"fa fa-reply")),
            );
            $html->OpenWidget ("Orders Waiting for Confirmation", $actions, array('collapse' => true, 'fullscreen'=>true,'table-actions'=>"example1"));
            {

                $tableOptions = array();
                $tableOptions["tableClass"]= "table-hover table-bordered table-condensed table-striped";
                $tableOptions["key"]=array("id"=>"transfer_order_id");
                $tableOptions["order"]=array(1, "DESC");

                $cols = array();
                $cols[] = array("column"=>"transfer_order_id");
                $cols[] = array("column"=>"transfer_order_date");
                $cols[] = array("column"=>"from_warehouse");
                $cols[] = array("column"=>"ACTION_COL", "style"=>"width:150px","action-type"=>"ajax",
                    "buttons"=> array(
                        array("action-class"=>"transfer_details", "button-icon"=>"fa fa-info-circle", "title"=>$dictionary->GetValue("Details"), "type"=>"button"),
                        array("action-class"=>"transfer_confirm", "button-icon"=>"fa fa-check", "title"=>$dictionary->GetValue("Confirm"), "type"=>"button")
                    )
                );

                $orders = array(
                    ["transfer_order_id"=>'1', "transfer_order_type"=>'Receive', "transfer_order_date"=>'2021-04-15', "from_warehouse"=>'Warehouse 1', "to_warehouse"=>'Warehouse 2'],
                    ["transfer_order_id"=>'1', "transfer_order_type"=>'Receive', "transfer_order_date"=>'2021-04-11', "from_warehouse"=>'Warehouse 3', "to_warehouse"=>'Warehouse 2'],
                );
                $html->Table($orders, $cols, $tableoptions);

            }
            $html->CloseWidget();
        }
        $html->CloseSpan();
    }
    $html->CloseDiv();



    ?>
    </div>
</div>
<script>
$(function() {

    $(".transfer_details").click(function() {
        id = 1;
        OpenModal("ui/transferorder.detail.php", {"transfer_order_id": id}, "modal-lg");
    });

    $(".transfer_confirm").click(function() {
        id = 1;
        OpenConfirmModal(["You are accepting transferring this order's items to your warehouse."], function() {

        });
    });

});
</script>