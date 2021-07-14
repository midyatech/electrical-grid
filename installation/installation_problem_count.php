<?php
include_once realpath(__DIR__ . '/..').'/include/header.php';
include_once realpath(__DIR__ . '/..').'/include/checksession.php';
include_once realpath(__DIR__ . '/..').'/include/checkpermission.php';
require_once realpath(__DIR__ . '/..').'/class/HtmlHelper.php';
require_once realpath(__DIR__ . '/..').'/class/Installation.class.php';

$html = new HTML ( $LANGUAGE );
$dictionary = new Dictionary ( $LANGUAGE );
$dictionary->GetAllDictionary ();
$Installation = new Installation( );

$InstallationProblem = $Installation->GetInstallationProblemReportCount();

$cols = array();
$cols[] = array("column"=>"installation_problem");
$cols[] = array("column"=>"installation_problem_count");

$tableOptions = array();
$tableOptions["tableClass"]= "table-hover table-bordered table-condensed table-striped";
$tableOptions["key"]=array("id"=>"transformer_id");

$sum_problem_count = 0;
for($i=0; $i<count($InstallationProblem); $i++){
    $sum_problem_count += $InstallationProblem[$i]["installation_problem_count"];
}

$InstallationProblem[$i]["installation_problem"] = '<b>'.$dictionary->GetValue("total").'</b>';
$InstallationProblem[$i]["installation_problem_count"] = '<b>'.$sum_problem_count.'</b>';

$html->OpenDiv("row");
{
    $html->OpenSpan(12);
    {
        $html->OpenWidget ("installation_problem_count", NULL, array('collapse' => true, 'fullscreen'=>true,'table-actions'=>"example1"));
        {
            echo '<br/><h3><b>'.$dictionary->GetValue("installation_problem_count").'</b></h3>';
            $html->Table($InstallationProblem, $cols, $tableOptions);

            /*
            $html->OpenDiv("row");
            {
                $html->OpenSpan(12);
                {
                    $condition["count"] = true;
                    $DifferenceCalculatedAndInstalledCount = $Installation->GetDifferenceCalculatedAndInstalled($condition);
                    $data = array();
                    if ($DifferenceCalculatedAndInstalledCount) {
                        $data[] = array("item"=>$dictionary->getValue("problem_point"), "value"=>"<b>".$DifferenceCalculatedAndInstalledCount[0]["count_point_id"]."</b>");
                        $data[] = array("item"=>$dictionary->getValue("installed_point"), "value"=>"<b>".$DifferenceCalculatedAndInstalledCount[1]["count_point_id"]."</b>");
                        $data[] = array("item"=>$dictionary->getValue("percentage"), "value"=>"<b>".number_format((($DifferenceCalculatedAndInstalledCount[0]["count_point_id"]*100)/$DifferenceCalculatedAndInstalledCount[1]["count_point_id"]), 2)." %</b>");
                    }

                    $cols2 = array();
                    $cols2[] = array("column"=>"item", "style"=>"font-weight:bold;");
                    $cols2[] = array("column"=>"value");

                    echo '<br/><h3><b>'.$dictionary->GetValue("difference_percentage_calculated_and_installed").'</b></h3>';
                    $html->Table($data, $cols2, array("header"=>false, "tableClass"=>"table-hover table-bordered table-condensed table-striped"));


                    $cols1 = array();
                    $cols1[] = array("column"=>"point_id");
                    $cols1[] = array("column"=>"calculated_type");
                    $cols1[] = array("column"=>"calculated_enclosures");
                    $cols1[] = array("column"=>"installed_type");
                    $cols1[] = array("column"=>"installed_enclosures");

                    $tableOptions1 = array();
                    $tableOptions1["tableClass"]= "table-hover table-bordered table-condensed table-striped";
                    $tableOptions1["key"]=array("id"=>"point_id");

                    echo '<br/><h3><b>'.$dictionary->GetValue("difference_list_calculated_and_installed").'</b></h3>';
                    $html->Datatable("example1", "api/list.difference.calculated.and.installed.php", $cols1, $tableOptions1);
                }
                $html->CloseSpan();
            }
            $html->CloseDiv();
            */
        }
        $html->CloseWidget();
    }
    $html->CloseSpan();
}
$html->CloseDiv();

include '../include/footer.php';
?>
<script src="../assets/global/scripts/datatable.js" type="text/javascript"></script>
<script src="../assets/global/plugins/datatables/datatables.min.js" type="text/javascript"></script>
<script src="../assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js" type="text/javascript"></script>
<script src="js/installation.js"></script>