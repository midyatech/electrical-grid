<style>
    p.form-control {
        margin: 0;
    }
</style>
<?php
include_once realpath(__DIR__ . '/../..').'/include/checksession.php';
include_once realpath(__DIR__ . '/../..').'/include/checkpermission.php';
require_once realpath(__DIR__ . '/../..').'/class/HtmlHelper.php';
require_once realpath(__DIR__ . '/../..').'/class/SupplyChain.class.php';
//require_once realpath(__DIR__ . '/../..') . '/class/User.php';

//$user = new User();
$html->OpenDiv("row");
{
    $html->OpenSpan(12);
    {
        $actions = array ();
        $html->OpenWidget ("Current Stock", $actions, array('collapse' => true, 'fullscreen'=>true,'table-actions'=>"example1"));
        {

            $html->OpenDiv("well");
            {
                $html->OpenForm( "transfer_order.php", "form2", "horizental");
                {
                    $html->OpenDiv("row");
                    {
                        $html->OpenSpan(4);
                        {
                            $html->DrawFormField ( "label", "Store", "Warehouse 1", null, array("class"=>"form-control", "flow"=>"horizental", "disabled"=>true) );
                        }
                        $html->CloseSpan();
                        $html->OpenSpan(4);
                        {
                            $html->DrawFormField ( "label", "Code", "WH1", null, array("class"=>"form-control", "flow"=>"horizental", "disabled"=>true) );
                        }
                        $html->CloseSpan();
                        $html->OpenSpan(4);
                        {
                            $html->DrawFormField ( "label", "Location", "Erbil", null, array("class"=>"form-control", "flow"=>"horizental", "disabled"=>true) );
                        }
                        $html->CloseSpan();
                    }
                    $html->CloseDiv();

                }
                $html->CloseForm();
            }
            $html->CloseDiv();


            $tableOptions = array();
            $tableOptions["tableClass"]= "table-hover table-bordered table-condensed table-striped";
            $tableOptions["key"]=array("id"=>"Serial_No");
            $tableOptions["order"]=array(1, "DESC");


            $cols = array();
            $cols[] = array("column"=>"Meter Type");
            $cols[] = array("column"=>"Model");
            $cols[] = array("column"=>"Quantity");

            $meters = array(
                ["Meter Type"=>'Single Phase', "Model"=>'Mk32H', "Quantity"=>'140'],
                ["Meter Type"=>'Three Phase', "Model"=>'MK10M', "Quantity"=>'80'],
                ["Meter Type"=>'CT', "Model"=>'Mk10E', "Quantity"=>'26'],
            );
            echo '<h2>Meters</h2>';
            $html->Table($meters, $cols, $tableoptions);


            $cols = array();
            $cols[] = array("column"=>"Model");
            $cols[] = array("column"=>"Quantity");
            $gateways = array(
                ["Model"=>'GW30', "Quantity"=>'55']
            );
            echo '<h2>Gateways</h2>';
            $html->Table($gateways, $cols, $tableoptions);


            $cols = array();
            $cols[] = array("column"=>"enclosure_type");
            $cols[] = array("column"=>"enclosure_configuration");
            $cols[] = array("column"=>"Quantity");
            $enclosures = array(
                ["enclosure_configuration"=>'011', "Quantity"=>14, "enclosure_type"=>'S1P2M1G'],
                ["enclosure_configuration"=>'010', "Quantity"=>18, "enclosure_type"=>'S1P1M1G'],
                ["enclosure_configuration"=>'010', "Quantity"=>9, "enclosure_type"=>'S1P1M'],
                ["enclosure_configuration"=>'111', "Quantity"=>15, "enclosure_type"=>'S1P3M'],
                ["enclosure_configuration"=>'010', "Quantity"=>22, "enclosure_type"=>'L1P4M'],
                ["enclosure_configuration"=>'01', "Quantity"=>6, "enclosure_type"=>'L3P1M'],
                ["enclosure_configuration"=>'11', "Quantity"=>15, "enclosure_type"=>'L3P2M'],
            );
            echo '<h2>Enclocsures</h2>';
            $html->Table($enclosures, $cols, $tableoptions);

            $cols = array();
            $cols[] = array("column"=>"Item");
            $cols[] = array("column"=>"Code");
            $cols[] = array("column"=>"Quantity");
            $items = array(
                ["Item"=>'Circuit Breaker', "Code"=>"CB", "Quantity"=>'160 pc'],
                ["Item"=>'Bus bar', "Code"=>"BB", "Quantity"=>'200 pc'],
                ["Item"=>'Rail', "Code"=>"CCT", "Quantity"=>'50 m']
            );
            echo '<h2>Other Items</h2>';
            $html->Table($items, $cols, $tableoptions);

        }
        $html->CloseWidget();
    }
    $html->CloseSpan();
}
$html->CloseDiv();
?>
