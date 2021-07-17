<?php
// error_reporting(E_ALL);
// ini_set('display_errors', 1);
include_once realpath(__DIR__ . '/../..').'/include/settings.php';
include_once realpath(__DIR__ . '/../..').'/include/checksession.php';
//include_once realpath(__DIR__ . '/../..').'/include/checkpermission.php';
require_once realpath(__DIR__ . '/../..').'/class/HtmlHelper.php';
require_once realpath(__DIR__ . '/../..') . '/class/Dictionary.php';
require_once realpath(__DIR__ . '/../..') . '/class/Assembly.class.php';

$dictionary = new Dictionary($LANGUAGE);
$dictionary->GetAllDictionary();
$html = new HTML($LANGUAGE);
$assembly = new Assembly();

if (isset($id)) {
    //$id = $_GET["id"];
} else if (isset($_GET["id"])) {
    $id = $_GET["id"];
}

$is_stock = null;
if (isset($id)) {
    $Transfomer = $assembly->getAssemblyOrderByTransfomer($id);
    if ($Transfomer) {
        $is_stock = $Transfomer[0]["is_extra_stock"];
    }
    $project_items = $assembly->getAssemblyOrderItems($id, null, null, true);
    $trnasformers = $assembly->getAssemblyTrnaformers($id);
// print_r($trnasformers);
    $items = $assembly->getAssemblyOrderDetails($id);
    $iccids = $assembly->getOrderIccids($id);


    $html->OpenSpan(12);
    {
        $order = $assembly->GetAssemblyOrder($id);
        $code = $order[0]["assembly_order_code"];
        $notes = $order[0]["notes"];
        $start_date = $order[0]["start_date"];

        $data = array();
        if ($order) {
            $data[] = array("item"=>$dictionary->getValue("assembly_order_code"), "value"=>$order[0]["assembly_order_code"]);
            $data[] = array("item"=>$dictionary->getValue("create_date"), "value"=>$order[0]["create_date"]);
            $data[] = array("item"=>$dictionary->getValue("start_date"), "value"=>$order[0]["start_date"]);
            $data[] = array("item"=>$dictionary->getValue("user_name"), "value"=>$order[0]["user_name"]);
            $data[] = array("item"=>$dictionary->getValue("notes"), "value"=>$order[0]["notes"]);
        }

        $cols = array();
        $cols[] = array("column"=>"item", "style"=>"font-weight:bold;");
        $cols[] = array("column"=>"value");

        echo '<h2>Assembly Order</h2>';
        $html->Table($data, $cols, array("header"=>false));
    }
    $html->CloseSpan();

    $html->OpenSpan(12);
    {
    ?>
        <ul class="nav nav-tabs">
            <li class="active">
                <a href="#Transformers" data-toggle="tab" > <b><?php print $dictionary -> GetValue("Assembly_Order"); ?></b> </a>
            </li>
            <!-- <li>
                <a href="#Assembly_Items" data-toggle="tab" aria-expanded="true"> <b><?php print $dictionary -> GetValue("Assembly Items"); ?></b> </a>
            </li>
            <li>
                <a href="#Assembly_Configuration" data-toggle="tab" aria-expanded="true"> <b><?php print $dictionary -> GetValue("Assembly Configuration"); ?></b> </a>
            </li>
            <li>
                <a href="#Summary" data-toggle="tab" aria-expanded="true"> <b><?php print $dictionary -> GetValue("Summary"); ?></b> </a>
            </li> -->
            <li>
                <a href="#iccid_list" data-toggle="tab" aria-expanded="true"> <b><?php print $dictionary -> GetValue("iccid_list"); ?></b> </a>
            </li>
        </ul>
    <?php
    }
    $html->CloseSpan();

    $html->OpenSpan(12);
    {
    ?>
        <div class="tab-content">

            <div class="tab-pane active" id="Transformers">
                <?php
                $html->OpenSpan(12);
                {
                    if (!$is_stock) {
                        $cols = array();
                        $cols[] = array("column"=>"station");
                        $cols[] = array("column"=>"feeder");
                        $cols[] = array("column"=>"transformer_name");
                        $cols[] = array("column"=>"transformer_id");
                        $cols[] = array("column"=>"transformer_generated_number");
                        $cols[] = array("column"=>"enclosures");
                        $cols[] = array("column"=>"ACTION_COL", "style"=>"width:150px","action-type"=>"ajax",
                                            "buttons"=> array(
                                                array("action-class"=>"assembly_transformer_details", "button-icon"=>"fa fa-info-circle", "title"=>$dictionary->GetValue("Details"), "type"=>"button", "url"=>"href=javascript:;")
                                            )
                                        );
                        $transformer_tableoptions["key"]=array("id"=>"transformer_id");

                        echo '<h2>Transformers</h2>';

                        if ($trnasformers) {
                            for($i=0; $i<count($trnasformers); $i++){
                                if( $trnasformers[$i]["enclosures_count"] - $trnasformers[$i]["manufactured_count"] == 0 ){
                                    $color = "success";
                                } else {
                                    $color = "danger";
                                }
                                $trnasformers[$i]["enclosures"] = '<span class="label label-'.$color.'"><b>'.$trnasformers[$i]["manufactured_count"]."/".$trnasformers[$i]["enclosures_count"].'</b></span>';
                            }
                        }
                        $html->Table($trnasformers, $cols, $transformer_tableoptions);
                    } else {
                        echo "<br>";
                        $html->Literal('','<a class="btn btn-primary" href="assembly_transformer_details.php?aoid='.$id.'&id='.$trnasformers[0]["transformer_id"].'">Asseble Enclosures</a>');
                    }
                }
                $html->CloseSpan();
                ?>
            <!-- </div>

            <div class="tab-pane" id="Assembly_Items"> -->
                <?php
                //if (!$is_stock) {
                    $html->OpenSpan(12);
                    {
                        $cols = array();
                        $cols[] = array("column"=>"enclosure_type");
                        $cols[] = array("column"=>"enclosures_count");
                        $cols[] = array("column"=>"manufactured_count");
                        $cols[] = array("column"=>"difference");

                        $TotalEnclosuresCount = $TotalManufacturedCount = 0;
                        if ($project_items) {
                            for($i=0; $i<count($project_items); $i++){
                                $TotalEnclosuresCount += $project_items[$i]["enclosures_count"];
                                $TotalManufacturedCount += $project_items[$i]["manufactured_count"];
                                if( $project_items[$i]["enclosures_count"] - $project_items[$i]["manufactured_count"] == 0 ){
                                    $color = "success";
                                } else {
                                    $color = "danger";
                                }
                                $project_items[$i]["difference"] = '<span class="label label-'.$color.'"><b>'.($project_items[$i]["enclosures_count"] - $project_items[$i]["manufactured_count"]).'</b></span>';
                            }
                            $project_items[$i]["enclosure_type"] = '<b>'.$dictionary->GetValue("total").'</b>';
                            $project_items[$i]["enclosures_count"] = '<b>'.$TotalEnclosuresCount.'</b>';
                            $project_items[$i]["manufactured_count"] = '<b>'.$TotalManufacturedCount.'</b>';
                            if( $TotalEnclosuresCount - $TotalManufacturedCount == 0 ){
                                $color = "success";
                            } else {
                                $color = "danger";
                            }
                            $project_items[$i]["difference"] = '<span class="label label-'.$color.'"><b>'.($TotalEnclosuresCount - $TotalManufacturedCount).'</b></span>';
                        }

                        echo '<h2>Assembly Items</h2>';
                        $html->Table($project_items, $cols, $tableoptions);
                    }
                    $html->CloseSpan();
                //}
                ?>
            <!-- </div>

            <div class="tab-pane" id="Assembly_Configuration"> -->
                <?php
                $html->OpenSpan(12);
                {
                    $TotalEnclosuresCount = $TotalManufacturedCount = 0;
                    for($i=0; $i<count($Transfomer); $i++){
                        $Transfomer[$i]["enclosure_type"] = $Transfomer[$i]["enclosure_type"]." [".$Transfomer[$i]["configuration_name"]."]";
                        $TotalEnclosuresCount += $Transfomer[$i]["enclosures_count"];
                        $TotalManufacturedCount += $Transfomer[$i]["manufactured_count"];
                        if( $Transfomer[$i]["enclosures_count"] - $Transfomer[$i]["manufactured_count"] == 0 ){
                            $color = "success";
                        } else {
                            $color = "danger";
                        }
                        $Transfomer[$i]["difference"] = '<span class="label label-'.$color.'"><b>'.($Transfomer[$i]["enclosures_count"] - $Transfomer[$i]["manufactured_count"]).'</b></span>';
                    }

                    $Transfomer[$i]["enclosure_type"] = '<b>'.$dictionary->GetValue("total").'</b>';
                    $Transfomer[$i]["enclosures_count"] = '<b>'.$TotalEnclosuresCount.'</b>';
                    $Transfomer[$i]["manufactured_count"] = '<b>'.$TotalManufacturedCount.'</b>';
                    if( $TotalEnclosuresCount - $TotalManufacturedCount == 0 ){
                        $color = "success";
                    } else {
                        $color = "danger";
                    }
                    $Transfomer[$i]["difference"] = '<span class="label label-'.$color.'"><b>'.($TotalEnclosuresCount - $TotalManufacturedCount).'</b></span>';

                    $cols = array();
                    $cols[] = array("column"=>"enclosure_type");
                    $cols[] = array("column"=>"enclosures_count");
                    $cols[] = array("column"=>"manufactured_count");
                    $cols[] = array("column"=>"difference");
                    echo '<h2>Assembly Configuration</h2>';
                    $html->Table($Transfomer, $cols, $tableoptions);
                }
                $html->CloseSpan();
                ?>
            <!-- </div>

            <div class="tab-pane" id="Summary"> -->
                <?php
                $html->OpenSpan(12);
                {
                    $data = array();
                    if ($items) {
                        $data[] = array("item"=>$dictionary->getValue("single_phase"), "count"=>$items[0]["single_phase"]);
                        $data[] = array("item"=>$dictionary->getValue("three_phase"), "count"=>$items[0]["three_phase"]);
                        $data[] = array("item"=>$dictionary->getValue("small_enclosure"), "count"=>$items[0]["small_enclosure"]);
                        $data[] = array("item"=>$dictionary->getValue("big_enclosure"), "count"=>$items[0]["big_enclosure"]);
                        $data[] = array("item"=>$dictionary->getValue("gateways"), "count"=>$items[0]["gateway"]);
                        $data[] = array("item"=>$dictionary->getValue("ct"), "count"=>$items[0]["ct"]);
                    }

                    $cols = array();
                    $cols[] = array("column"=>"item");
                    $cols[] = array("column"=>"count");
                    echo '<h2>Summary</h2>';
                    $html->Table($data, $cols, $tableoptions);
                }
                $html->CloseSpan();
                ?>
            </div>

            <div class="tab-pane" id="iccid_list">
                <?php
                $wactions = array (
                    array("type"=>"literal", 'name'=>'', 'list'=>null, 'options'=>null, "value"=>'<a href="ui/assembly.iccid.export.php?id='.$id.'" class="btn green" target="blank"><i class="fa fa-file-excel-o"></i></a>')
                );

                if ($user-> CheckPermission($USERID, "permission_activate_simcards") == 1) {
                    $wactions[] = array ( "type"=>"button", "name"=>$dictionary->GetValue('change_simcard_status'), "value"=>$dictionary->GetValue('change_simcard_status'), 'list'=>null, "options"=>array ("class" => "btn green btn-sm change_gateway_status", "icon"=>"fa fa-pencil"));
                }


                $html->OpenWidget ("iccid_list", $wactions, array('collapse' => false, 'fullscreen'=>false,'table-actions'=>false), "", 'light');
                {
                    $html->OpenSpan(12);
                    {
                        $data = array();
                        if ($iccids) {
                            for($i=0; $i<count($iccids); $i++){
                                $data[] = array("Iccids"=>$iccids[$i]["iccids"], "Iccid Pattern"=>$iccids[$i]["iccid_pattern"], "Model"=>$iccids[$i]["model"], "Serial Number"=>$iccids[$i]["serial_number"], "activation_date"=>$iccids[$i]["activation_date"], "simcard_status"=>$iccids[$i]["simcard_status"]);
                            }
                        }

                        $cols = array();
                        $cols[] = array("column"=>"Iccids");
                        $cols[] = array("column"=>"Iccid Pattern");
                        $cols[] = array("column"=>"Model");
                        $cols[] = array("column"=>"Serial Number");
                        $cols[] = array("column"=>"activation_date");
                        $cols[] = array("column"=>"simcard_status");

                        if ($data) {
                            $html->Table($data, $cols, $tableoptions);
                        }
                    }
                    $html->CloseSpan();
                }
                $html->CloseWidget();
                // // $wactions = array (
                // //     array ( "type"=>"button", "name"=>$dictionary->GetValue('change_simcard_status'), "value"=>$dictionary->GetValue('change_simcard_status'), "", "options"=>array ("class" => "btn green btn-sm change_gateway_status", "icon"=>"fa fa-pencil"))
                // //     );

                // // $html->OpenWidget ("iccid_list", $wactions, array('collapse' => true, 'fullscreen'=>true,'table-actions'=>"example"), "", 'light');
                // // {
                //     $dataTableOptions = array();
                //     $dataTableOptions["tableClass"]= "table-hover table-bordered table-condensed table-striped";
                //     $dataTableOptions["key"]=array("id"=>"assembly_order_id");
                //     $dataTableOptions["order"]=array(1, "DESC");

                //     $cols = array();
                //     $cols[] = array("column"=>"iccids");
                //     $cols[] = array("column"=>"serial_number");
                //     $cols[] = array("column"=>"activation_date");
                //     $cols[] = array("column"=>"simcard_status");
                //     $html->Table($data, $cols, $tableoptions);
                //     $html->Datatable("example2", "api/stats.assembly.php?id=".$id, $cols, $dataTableOptions);
                // // }
                // // $html->CloseWidget();

                ?>
            </div>

        </div>
    <?php
    }
    $html->CloseSpan();
}
?>

