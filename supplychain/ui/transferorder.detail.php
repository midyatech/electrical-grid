<style>
    p.form-control {
        margin: 0;
    }
</style>
<?php
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

include_once realpath(__DIR__ . '/../..').'/include/checksession.php';
//include_once realpath(__DIR__ . '/../..').'/include/checkpermission.php';
require_once realpath(__DIR__ . '/../..').'/class/HtmlHelper.php';
require_once realpath(__DIR__ . '/../..').'/class/Dictionary.php';
require_once realpath(__DIR__ . '/../..').'/class/SupplyChain.class.php';
//require_once realpath(__DIR__ . '/../..') . '/class/User.php';

$dictionary= new Dictionary("ENGLISH");
$html = new HTML('ENGLISH');
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




    $html->OpenDiv("row");
    {
        $html->OpenSpan(12);
        {

                $html->OpenForm( "transfer_order.php", "form2", "horizental");
                {
                    $html->OpenDiv("row");
                    {
                        $html->OpenSpan(6);
                        {
                            $html->DrawFormField ( "label", "order_type", "Receive", null, array("class"=>"form-control", "flow"=>"horizental", "optional"=>true) );
                            $html->DrawFormField ( "label", "from_warehouse", "Warehouse 1", null, array("class"=>"form-control", "flow"=>"horizental", "optional"=>true) );
                        }
                        $html->CloseSpan();
                        $html->OpenSpan(6);
                        {
                            $html->DrawFormField ( "label", "date", "2020-04-15", null, array("class"=>"form-control", "flow"=>"horizental", "optional"=>true) );
                            $html->DrawFormField ( "label", "from_warehouse", "Warehouse 2", null, array("class"=>"form-control", "flow"=>"horizental", "optional"=>true) );
                        }
                        $html->CloseSpan();
                    }
                    $html->CloseDiv();

                }
                $html->CloseForm();
        }
        $html->CloseSpan();

        $html->OpenSpan(12);
        {
            echo "<br>";

                $html->OpenForm( "transfer_order.php", "form2", "horizental");
                {

                    echo '<br><br>';

                    $tableOptions = array();
                    $tableOptions["tableClass"]= "table-hover table-bordered table-condensed table-striped";
                    $tableOptions["key"]=array("id"=>"Serial_No");
                    $tableOptions["order"]=array(1, "DESC");

                    $cols = array();
                    $cols[] = array("column"=>"item_type");
                    $cols[] = array("column"=>"serial_number");
                    $cols[] = array("column"=>"quantity");
                    $items = array(
                        ["item_type"=>'meter', "serial_number"=>'EA0115602', "quantity"=>1],
                        ["item_type"=>'meter', "serial_number"=>'EA0115603', "quantity"=>1],
                        ["item_type"=>'meter', "serial_number"=>'EA0115604', "quantity"=>1],
                        ["item_type"=>'meter', "serial_number"=>'EA0115605', "quantity"=>1],
                        ["item_type"=>'meter', "serial_number"=>'EA0115606', "quantity"=>1],
                        ["item_type"=>'meter', "serial_number"=>'EA0115607', "quantity"=>1],
                        ["item_type"=>'meter', "serial_number"=>'EA0115608', "quantity"=>1],
                        ["item_type"=>'meter', "serial_number"=>'EA0115609', "quantity"=>1],
                    );
                    $html->Table($items, $cols, $tableoptions);
                }
                $html->CloseForm();

        }
        $html->CloseSpan();
    }
    $html->CloseDiv();



    ?>

