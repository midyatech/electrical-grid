<?php
include_once realpath(__DIR__ . '/../..').'/include/checksession.php';
//include_once realpath(__DIR__ . '/../..').'/include/checkpermission.php';
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

$ItemTypesArr = array(
    [0=>'1', 1=>'Enclosure'],
    [0=>'2', 1=>'Meter'],
    [0=>'3', 1=>'Gateway'],
    [0=>'4', 1=>'Busbar'],
    [0=>'5', 1=>'Crircuit Breaker'],
    [0=>'6', 1=>'Rail'],
);

$items = array();


?>
<div class="tabbable-line tabbable-full-width">

    <div class="tab-content">

    <?php
    $html->OpenDiv("row");
    {
        $html->OpenSpan(12);
        {
            $actions = array ();
            $html->OpenWidget ("Advanced Search", $actions, array('collapse' => true, 'fullscreen'=>true,'table-actions'=>"example1"));
            {
                $html->OpenForm ( null, "form3" );
                {
                    $html->OpenDiv("row");
                    {
                        $html->OpenSpan(2);
                        {
                            $html->DrawFormField ( "select", "item_type", $assembly_order, $ItemTypesArr, array("class"=>"form-control", "optional"=>true) );
                        }
                        $html->CloseSpan();
                        $html->OpenSpan(2);
                        {
                            $html->DrawFormField ( "text", "serial_number", null, $WarehouseArr, array("class"=>"form-control", "optional"=>true) );
                        }
                        $html->CloseSpan();
                        $html->OpenSpan(2);
                        {
                            $html->DrawFormField ( "text", "code", null, $WarehouseArr, array("class"=>"form-control", "optional"=>true) );
                        }
                        $html->CloseSpan();
                        $html->OpenSpan(2);
                        {
                            $html->DrawFormField ( "text", "Model", null, $WarehouseArr, array("class"=>"form-control", "optional"=>true) );
                        }
                        $html->CloseSpan();
                        $html->OpenSpan(2);
                        {
                            $html->DrawFormField ( "text", "Plant No", null, $WarehouseArr, array("class"=>"form-control", "optional"=>true) );
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
                $cols[] = array("column"=>"code");
                $cols[] = array("column"=>"item_type");
                $cols[] = array("column"=>"serial_number");
                $cols[] = array("column"=>"Model");
                $cols[] = array("column"=>"PlantNo");
                $cols[] = array("column"=>"ACTION_COL", "style"=>"width:150px","action-type"=>"ajax",
                    "buttons"=> array(
                        array("action-class"=>"enclosure_trace", "button-icon"=>"fa fa-crosshairs", "title"=>$dictionary->GetValue("Trace"), "type"=>"button", "url"=>"href=javascript:;"),
                        )
                );

                if (isset($_REQUEST["item_type"])) {
                    $items = array(
                        ["item_type"=>'meter', "serial_number"=>'EA0115602', "code"=>'1', 'Model'=>'Mk32H', 'PlantNo'=>'EA0115601   '],
                        ["item_type"=>'meter', "serial_number"=>'EA0115602', "code"=>'2', 'Model'=>'Mk32H', 'PlantNo'=>'EA0115601   '],
                        ["item_type"=>'meter', "serial_number"=>'EA0115602', "code"=>'3', 'Model'=>'Mk32H', 'PlantNo'=>'EA0115601   '],
                        ["item_type"=>'meter', "serial_number"=>'EA0115602', "code"=>'4', 'Model'=>'Mk32H', 'PlantNo'=>'EA0115601   '],
                        ["item_type"=>'meter', "serial_number"=>'EA0115602', "code"=>'5', 'Model'=>'Mk32H', 'PlantNo'=>'EA0115601   '],
                        ["item_type"=>'meter', "serial_number"=>'EA0115602', "code"=>'6', 'Model'=>'Mk32H', 'PlantNo'=>'EA0115601   '],
                        ["item_type"=>'meter', "serial_number"=>'EA0115602', "code"=>'7', 'Model'=>'Mk32H', 'PlantNo'=>'EA0115601   '],
                        ["item_type"=>'meter', "serial_number"=>'EA0115602', "code"=>'8', 'Model'=>'Mk32H', 'PlantNo'=>'EA0115601   '],
                        ["item_type"=>'meter', "serial_number"=>'EA0115602', "code"=>'9', 'Model'=>'Mk32H', 'PlantNo'=>'EA0115601   '],
                        ["item_type"=>'meter', "serial_number"=>'EA0115602', "code"=>'10', 'Model'=>'Mk32H', 'PlantNo'=>'EA0115601  '],
                    );
                }

                $html->Table($items, $cols, $tableoptions);

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
    $("body").on("click", ".enclosure_trace", function() {
        OpenModal("../supplychain/ui/trace.php");
    });
});
</script>