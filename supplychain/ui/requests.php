<?php
include_once realpath(__DIR__ . '/../..').'/include/checksession.php';
// include_once realpath(__DIR__ . '/../..').'/include/checkpermission.php';
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
<div class="row">
    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
        <a class="dashboard-stat dashboard-stat-v2 blue" href="request.php?t=1">
            <div class="visual">
                <i class="fa fa-upload"></i>
            </div>
            <div class="details">
                <div class="number">
                    Request Sending Items
                </div>
                <div class="desc">  </div>
            </div>
        </a>
    </div>
    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
        <a class="dashboard-stat dashboard-stat-v2 red" href="request.php?t=2">
            <div class="visual">
                <i class="fa fa-download"></i>
            </div>
            <div class="details">
                <div class="number">
                Request Receiving Items </div>
                <div class="desc">   </div>
            </div>
        </a>
    </div>
    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
        <a class="dashboard-stat dashboard-stat-v2 blue" href="request.php?t=3">
            <div class="visual">
                <i class="fa fa-upload"></i>
            </div>
            <div class="details">
                <div class="number">
                Send Items
                </div>
                <div class="desc">  </div>
            </div>
        </a>
    </div>
    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
        <a class="dashboard-stat dashboard-stat-v2 red" href="request.php?t=4">
            <div class="visual">
                <i class="fa fa-download"></i>
            </div>
            <div class="details">
                <div class="number">
                Receive Items </div>
                <div class="desc">  </div>
            </div>
        </a>
    </div>
</div>
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
                // array ( "type"=>"link", "name"=>"Change", "value"=>"Receive Items", "list"=>"transfer_order.php?type=1", "options"=>array ("class" => "btn grey-cararra group_add", "icon"=>"fa fa-download")),
                // array ( "type"=>"link", "name"=>"Change", "value"=>"Send Items", "list"=>"transfer_order.php?type=2", "options"=>array ("class" => "btn grey-cararra group_add", "icon"=>"fa fa-send")),
                // array ( "type"=>"link", "name"=>"Change", "value"=>"Pending Requests (2)", "list"=>"pending_orders.php", "options"=>array ("class" => "btn red-thunderbird group_add", "icon"=>"fa fa-check-square-o")),

            );
            $html->OpenWidget ("Order List", $actions, array('collapse' => true, 'fullscreen'=>true,'table-actions'=>"example1"));
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
                            $html->DrawFormField ( "select", "order_type", $assembly_order, $OrderTypesArr, array("class"=>"form-control", "optional"=>true) );
                        }
                        $html->CloseSpan();
                        $html->OpenSpan(2);
                        {
                            $html->DrawFormField ( "select", "from_warehouse", $assembly_order, $WarehouseArr, array("class"=>"form-control", "optional"=>true) );
                        }
                        $html->CloseSpan();
                        $html->OpenSpan(2);
                        {
                            $html->DrawFormField ( "select", "to_warehouse", $assembly_order, $WarehouseArr, array("class"=>"form-control", "optional"=>true) );
                        }
                        $html->CloseSpan();

                        $html->OpenSpan(2);
                        {
                            ?>
                            <div class="form-group">
                                <label>&nbsp;</label>
                                <div class="input-group">
                                    <span class="input-group-btn">
                                        <button class="btn green filter_enclosure_list" type="submit"><?php echo $dictionary->GetValue("filter");?></button>
                                    </span>
                                    <span class="input-group-btn">
                                        <button class="btn default clear_filter_enclosure_list" type="submit"><?php echo $dictionary->GetValue("clear");?></button>
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

                echo '<br><br>';

                $tableOptions = array();
                $tableOptions["tableClass"]= "table-hover table-bordered table-condensed table-striped";
                $tableOptions["key"]=array("id"=>"transfer_order_id");
                $tableOptions["order"]=array(1, "DESC");

                $cols = array();
                $cols[] = array("column"=>"transfer_order_id");
                $cols[] = array("column"=>"transfer_order_type");
                $cols[] = array("column"=>"transfer_order_date");
                $cols[] = array("column"=>"from_warehouse");
                $cols[] = array("column"=>"to_warehouse");
                $cols[] = array("column"=>"ACTION_COL", "style"=>"width:150px","action-type"=>"ajax",
                    "buttons"=> array(
                        array("action-class"=>"transfer_details", "button-icon"=>"fa fa-info-circle", "title"=>$dictionary->GetValue("Details"), "type"=>"button")
                    )
                );

                $orders = array(
                    ["transfer_order_id"=>'1', "transfer_order_type"=>'Receive', "transfer_order_date"=>'2021-04-15', "from_warehouse"=>'Warehouse 1', "to_warehouse"=>'Warehouse 2'],
                    ["transfer_order_id"=>'2', "transfer_order_type"=>'Destroy', "transfer_order_date"=>'2021-04-16', "from_warehouse"=>'Warehouse 2', "to_warehouse"=>'-'],
                    ["transfer_order_id"=>'3', "transfer_order_type"=>'Request', "transfer_order_date"=>'2021-04-16', "from_warehouse"=>'Warehouse 4', "to_warehouse"=>'Warehouse 2'],
                    ["transfer_order_id"=>'4', "transfer_order_type"=>'Receive', "transfer_order_date"=>'2021-04-19', "from_warehouse"=>'Warehouse 1', "to_warehouse"=>'Warehouse 2'],
                    ["transfer_order_id"=>'5', "transfer_order_type"=>'Assemble', "transfer_order_date"=>'2021-04-21', "from_warehouse"=>'Warehouse 2', "to_warehouse"=>'-']
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
    })
});
</script>