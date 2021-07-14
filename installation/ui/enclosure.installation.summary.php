<?php
/*this page is used to get feed for doc out temp data-table*/
include_once realpath(__DIR__ . '/../..').'/include/checksession.php';
include_once realpath(__DIR__ . '/../..').'/include/checkpermission.php';
require_once realpath(__DIR__ . '/../..').'/class/Installation.class.php';
include_once realpath(__DIR__ . '/../..').'/class/Dictionary.php';

$Installation = new Installation();
$dictionary = new Dictionary($LANGUAGE);
$dictionary->GetAllDictionary();

//filters
$filter=array();

if( isset($_REQUEST["transformer_id"] ) && $_REQUEST["transformer_id"] != NULL ) {
    $filter["transformer_id"] = $_REQUEST["transformer_id"];
}

$Total_Enclosure = $Installed_Enclosure = $Total_Meter = $Installed_Meter = 0;

$EnclosureInstallationSummary = $Installation->GetEnclosureInstallationSummary($filter);

if(is_array($EnclosureInstallationSummary) && count($EnclosureInstallationSummary) > 0 ) {
    $Total_Enclosure = $EnclosureInstallationSummary[0]["Total_Enclosure"];
    $Installed_Enclosure = $EnclosureInstallationSummary[0]["Installed_Enclosure"];
    $Total_Meter = $EnclosureInstallationSummary[0]["Total_Meter"];
    $Installed_Meter = $EnclosureInstallationSummary[0]["Installed_Meter"];
}

print "<div class='row btn blue btn-outline btn-block padding-10' style='margin: 0px 0px 15px 0px; cursor: auto; line-height: 2em'>";
    $html->OpenSpan(6);
    {
        print $dictionary->GetValue("Enclosures")." : ".$Installed_Enclosure." / ".$Total_Enclosure;
    }
    $html->CloseSpan();
    $html->OpenSpan(6);
    {
        print $dictionary->GetValue("Meters")." : ".$Installed_Meter." / ".$Total_Meter;
    }
    $html->CloseSpan();
print "</div>";
?>
