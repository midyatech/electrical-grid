<?php
include_once realpath(__DIR__ . '/../..').'/include/settings.php';
include_once realpath(__DIR__ . '/../..').'/include/checksession.php';
include_once realpath(__DIR__ . '/../..').'/include/checkpermission.php';
require_once realpath(__DIR__ . '/../..').'/class/HtmlHelper.php';
include_once realpath(__DIR__ . '/../..').'/class/Installation.class.php';
include_once realpath(__DIR__ . '/../..').'/class/Survey.class.php';
require_once realpath(__DIR__ . '/../..') . '/class/Tree.php';
require_once realpath(__DIR__ . '/../..') . '/class/Dictionary.php';

$html = new HTML ( $LANGUAGE );
$dictionary = new Dictionary ( $LANGUAGE );
$dictionary->GetAllDictionary ();
$options = array("class"=>"form-control");
$listoptions = array("class"=>"form-control","optional"=>"true");
$Survey = new Survey();
$Installation = new Installation();
$description_key = "description_Survey";
$area_tree = new Tree("AREA_TREE");

$filter = array();

if(isset($_REQUEST["point_id"]) && $_REQUEST["point_id"] > 0) {
    $filter['point_id'] =  $_REQUEST["point_id"];

    $tableOptions = array();
    $tableOptions["tableClass"]= "table-hover table-bordered table-striped";
    $tableOptions["ordering"]= "false";
    $tableOptions["paging"]="false";
    $tableOptions["footer"]="true";

    $enclosures = $Installation -> getPointInclosures($filter);

    $meters = $Installation -> GetInstalledItems($filter);

    $html->OpenDiv("row");
    {
        $html->OpenSpan(6);
        {
            print "<h2>".$dictionary->GetValue("point_details")."</h2>".
                    "<p><b>Area Name:</b> ".$enclosures[0]['NODE_NAME'].'</p>'.
                    "<p><b>Station:</b> ".$enclosures[0]['station'].'</p>'.
                    "<p><b>Feeder:</b> ".$enclosures[0]['feeder'].'</p>'.
                    "<p><b>Point ID:</b> ".$enclosures[0]['point_id'].'</p>';
        }
        $html->CloseSpan();
        $html->OpenSpan(6);
        {
            print "<p style='margin-top: 66px;'><b>Transformer Number:</b> ".$enclosures[0]['transformer_number'].' ['.$enclosures[0]['transformer_generated_number'].']</p>'.
                    "<p><b>Latitude:</b> ".$enclosures[0]['latitude'].'</p>'.
                    "<p><b>Longitude:</b> ".$enclosures[0]['longitude'].'</p>';
        }
        $html->CloseSpan();
    }
    $html->CloseDiv();

    // print "<h3><b>Point's Detailes</b></h3>".
    //       "<p><b>Area Name:</b> ".$enclosures[0]['NODE_NAME'].'</p>'.
    //       "<p><b>Station:</b> ".$enclosures[0]['station'].'</p>'.
    //       "<p><b>Feeder:</b> ".$enclosures[0]['feeder'].'</p>'.
    //       "<p><b>Point ID:</b> ".$enclosures[0]['point_id'].'</p>'.
    //       "<p><b>Transformer Number:</b> ".$enclosures[0]['transformer_number'].'</p>'.
    //       "<p><b>Latitude:</b> ".$enclosures[0]['latitude'].'</p>'.
    //       "<p><b>Longitude:</b> ".$enclosures[0]['longitude'].'</p>';

    if ($enclosures[0]["enclosure_id"] != "") {
        $cols = array();
        // $cols[] = array("column"=>"enclosure_id");
        $cols[] = array("column"=>"enclosure_sn");
        $cols[] = array("column"=>"enclosure_type");
        $cols[] = array("column"=>"installed_time");
        $cols[] = array("column"=>"gateway_sn");
        $cols[] = array("column"=>"Meter1");
        $cols[] = array("column"=>"Meter2");
        $cols[] = array("column"=>"Meter3");
        $cols[] = array("column"=>"Meter4");
        $cols[] = array("column"=>"Meter5");
        $cols[] = array("column"=>"Meter6");

        echo '<h2>'.$dictionary->GetValue("installed_enclosures").'</h2>';
        $html->Table($enclosures, $cols, $tableOptions);
    }

    if ($meters) {

        $mcols = array();
        $mcols[] = array("column"=>"enclosure_sn");
        $mcols[] = array("column"=>"SN");
        $mcols[] = array("column"=>"Type");
        $mcols[] = array("column"=>"Model");
        $mcols[] = array("column"=>"Plant_No");
        $mcols[] = array("column"=>"Serial_No");
        $mcols[] = array("column"=>"IMEI");
        $mcols[] = array("column"=>"ICCID");
        $mcols[] = array("column"=>"STS_No");
        $mcols[] = array("column"=>"MODEM_SN");
        $mcols[] = array("column"=>"SIM_IMSI");
        $mcols[] = array("column"=>"SONO");
        $mcols[] = array("column"=>"simcard_status");
        $mcols[] = array("column"=>"activation_date");

        echo '<h2>'.$dictionary->GetValue("installed_items").'</h2>';
        $html->Table($meters, $mcols, $tableOptions);
    }


    $Installationcomments = $Installation->GetInstallationComments($filter);
    $cols2 = array();
    $cols2[] = array("column"=>"NAME", "title"=>"Name");
    $cols2[] = array("column"=>"comments", "title"=>"Comments");
    $cols2[] = array("column"=>"comment_time", "title"=>"Comment Time");
    $tableOptions = array();
    $tableOptions["tableClass"]= "table-bordered table-striped";
    if($Installationcomments) {
        echo '<br/><h2>'.$dictionary->GetValue("comments").'</h2>';
        $html->Table($Installationcomments, $cols2, $tableOptions);
    }
}