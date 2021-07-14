<?php
include_once realpath(__DIR__ . '/../..').'/include/checksession.php';
include_once realpath(__DIR__ . '/../..').'/include/checkpermission.php';
include_once realpath(__DIR__ . '/../..').'/include/settings.php';
include_once realpath(__DIR__ . '/../..').'/class/Dictionary.php';
include_once realpath(__DIR__ . '/../..').'/class/HtmlHelper.php';
include_once realpath(__DIR__ . '/../..').'/class/Installation.class.php';
include_once realpath(__DIR__ . '/../..').'/class/Survey.class.php';


$html = new HTML($LANGUAGE);
$Installation = new Installation();
$dictionary = new Dictionary($LANGUAGE);
$dictionary->GetAllDictionary();
$survey = new Survey();


$readonly = false;

if(isset($_POST["readonly"]) && $_POST["readonly"] == "1") {
    $readonly = true;
}

$point_id = $_POST["point_id"];
$filter["service_point.point_id"] = $point_id;
$point = $Installation->GetServicePoint($filter);

$installation_status_id = $point[0]["installation_status_id"];
//$installation_problem_id = $point[0]["installation_problem_id"];

$tab_add = "tab_add";
$tab_complete = "tab_complete";
$tab_deactivate = "tab_deactivate";
$tab_problem = "tab_problem";
$tab_comments = "tab_comments";
//$active_add = "active";

$install_enclosure = $dictionary->GetValue("install_enclosure");
$complete_installation = $dictionary->GetValue("complete_installation");
$deactivate_point = $dictionary->GetValue("deactivate_point");
$report_problem = $dictionary->GetValue("report_problem");
$comment = $dictionary->GetValue("comment");
$active_add = $active_complete = $active_delete = $active_problem = $previously_status = "";

if( $installation_status_id == 2 ) {
    $complete_installation = $dictionary->GetValue("not_complete_installation");
    $previously_status = "<b>".$dictionary->GetValue("the_point_previously_completed")."</b><br/>";
    $tab_add = "tab_add_desabled";
    $tab_complete = "tab_not_complete";
    $tab_deactivate = "tab_add_desabled";
    //$active_add = "";
    //$active_complete = "active";
}

if( $installation_status_id == 3 ) {
    $deactivate_point = $dictionary->GetValue("activate_point");
    $previously_status = "<b>".$dictionary->GetValue("the_point_previously_deactivate")."</b><br/>";
    $tab_add = "tab_add_desabled";
    $tab_complete = "tab_add_desabled";
    $tab_deactivate = "tab_activate";
    $active_add = "";
    //$active_delete = "active";
}
/*
if( $installation_problem_id == 1 ) {
    $deactivate_point = $dictionary->GetValue("activate_point");
    $previously_status = "<b>".$dictionary->GetValue("the_point_previously_deactivate")."</b><br/>";
    $tab_add = "tab_add_desabled";
    $tab_complete = "tab_add_desabled";
    $tab_deactivate = "tab_activate";
    $active_add = "";
    //$active_delete = "active";
}
*/
$html->OpenDiv("row");
{
    $html->OpenDiv("col-xs-12");
    {
        $html->HiddenField("point_coordinates", $point[0]["latitude"].",".$point[0]["longitude"]);
        echo '<section id="message"></section>';
    }
    $html->CloseDiv();

    $html->OpenDiv("col-xs-6");
    {
        echo "<div class='col-xs-12' style='padding-left: 20px; line-height: 2em'>";
            echo "<b>".$dictionary->GetValue("point_id").":</b> ". $point[0]["point_id"];
            if ($point[0]["transformer_generated_number"] != "") {
                echo ' ['.$point[0]["transformer_generated_number"].']';
            }
            echo "<br>";
            echo "<b>".$dictionary->GetValue("point_type").":</b> ". $point[0]["point_type"]."<br>";
            if( isset($point[0]["needs_gateway"]) && $point[0]["needs_gateway"] == 1){
                echo "<b>".$dictionary->GetValue("Has_Gateway")."</b><br>";
            }
            echo "<b>".$dictionary->GetValue("accuracy_id").":</b> ". $point[0]["accuracy_id"]."<br>";
            echo "<b>".$dictionary->GetValue("single_phase_consumers").":</b> ". $point[0]["single_phase_consumers"]."<br>";
            echo "<b>".$dictionary->GetValue("three_phase_consumers").":</b> ". $point[0]["three_phase_consumers"]."<br>";
            echo "<b><a href='#' class='btn btn-info point_full_details' data-full_details=".$point[0]["point_id"]." style='margin: 10px 0px 20px 0px;'>".$dictionary->GetValue("point_full_detail")."</a></b><br/>";
        echo "</div>";
    }
    $html->CloseDiv();

    $html->OpenDiv("col-xs-6");
    {
        if (!$readonly) {
        ?>
            <ul class="nav nav-pills installation-actions">
                <li class="btn-block <?php print $active_add; ?>">
                    <a href="#<?php print $tab_add; ?>" class="btn btn-default btn-block" data-toggle="tab" aria-expanded="false"> <?php echo $install_enclosure ?> </a>
                </li>
                <li class="btn-block <?php print $active_complete; ?>">
                    <a href="#<?php print $tab_complete; ?>" class="btn btn-default btn-block" data-toggle="tab" aria-expanded="false"> <?php echo $complete_installation; ?> </a>
                </li>
                <li class="btn-block <?php print $active_delete; ?>">
                    <a href="#<?php print $tab_deactivate; ?>" class="btn btn-default btn-block" data-toggle="tab" aria-expanded="false"> <?php echo $deactivate_point ?> </a>
                </li>
                <li class="btn-block <?php print $active_problem; ?>">
                    <a href="#<?php print $tab_problem; ?>" class="btn btn-default btn-block" data-toggle="tab" aria-expanded="false"> <?php echo $report_problem ?> </a>
                </li>
        <?php
        }
        ?>
            <li class="btn-block">
                    <a href="#<?php print $tab_comments; ?>" class="btn btn-default btn-block" data-toggle="tab" aria-expanded="false"> <?php echo $comment ?> </a>
                </li>
            </ul>
        <?php
    }
    $html->CloseDiv();

}
$html->CloseDiv();
?>
<div class="tab-content">
    <div class="tab-pane fade in" id="tab_add">
        <div class="form-group ">
            <label class="control-label"><?php echo $dictionary->GetValue("enclosure_sn");?>:</label>
            <div class="input-group">
                <span class="input-group-btn">
                    <button class="btn default scan_barcode" type="button">&nbsp;<i class="fa fa-barcode"></i>&nbsp;</button>
                </span>
                <input type="text" id="enclosure_sn" name="enclosure_sn" class="form-control" placeholder="<?php echo $dictionary->GetValue("enclosure_sn");?>">
                <span class="input-group-btn">
                    <button class="btn green get_enclosure" data-point_id="<?php print $point_id; ?>" type="button">&nbsp;<i class="fa fa-check"></i>&nbsp;</button>
                </span>
            </div>
            <section id="add_enclosure"></section>
        </div>
    </div>

    <div class="tab-pane fade" id="tab_add_desabled">
        <div class='col-xs-12 well' style='padding: 20px; line-height: 2em; text-align: center'>
            <?php
            print $previously_status;
            ?>
        </div>
    </div>

    <div class="tab-pane fade" id="tab_complete">
        <div class='col-xs-12 well' style='padding: 20px; line-height: 2em; text-align: center'>
            <?php


            $enclosure_filter = array();
            $enclosure_filter["point_id"] = $point_id;
            $installed_enclosures = $Installation->GetInstalledEnclosures($enclosure_filter);


            print $previously_status;
            if( count($installed_enclosures) > 0 ) {
                print "<b>".$dictionary->GetValue("are_you_sure_installation_completed")."</b>";
                print "<br/><br/>";
                $html->OpenDiv("col-xs-12");
                {
                    $html->DrawFormInput("button", "installation_completed", "installation_completed", NULL, array("class"=>"btn btn-success col-xs-12 installation_status", "data-point_id"=>$point_id, "data-status_type"=>2));
                }
                $html->CloseDiv();
            } else {
                print "<b>".$dictionary->GetValue("please_install_the_enclosure_then_complete_the_point")."</b>";
            }
            ?>
        </div>
    </div>

    <div class="tab-pane fade" id="tab_not_complete">
        <div class='col-xs-12 well' style='padding: 20px; line-height: 2em; text-align: center'>
            <?php
            print $previously_status;
            print "<b>".$dictionary->GetValue("are_you_sure_to_resume_installation")."</b>";
            print "<br/><br/>";
            $html->OpenDiv("col-xs-12");
            {
                $html->DrawFormInput("button", "installation_not_complete", "installation_not_complete", NULL, array("class"=>"btn btn-success col-xs-12 installation_status", "data-point_id"=>$point_id, "data-status_type"=>1));
            }
            $html->CloseDiv();
            ?>
        </div>
    </div>

    <div class="tab-pane fade" id="tab_deactivate">
        <div class='col-xs-12 well' style='padding: 20px; line-height: 2em; text-align: center'>
            <?php
            print $previously_status;

            $filter2 = array();
            $filter2["point_id"] = $point_id;
            $installed_enclosures_2 = $Installation->GetInstalledEnclosures($filter2);
            if(is_array($installed_enclosures_2) && count($installed_enclosures_2) > 0){
                print '<section id="already_installed_section">';
                print "<b>".$dictionary->GetValue("the_point_already_installed_enclosure")."</b>";
                print "<br/>";
                print '</section>';
                print '<section id="deactivate_section" style="display: none">';
                print "<b>".$dictionary->GetValue("are_you_sure_deactivate_point")."</b>";
                print "<br/><br/>";
                $html->OpenDiv("col-xs-12");
                {
                    $html->DrawFormInput("button", "deactivate_point", "deactivate_point", NULL, array("class"=>"btn btn-success col-xs-12 installation_status", "data-point_id"=>$point_id, "data-status_type"=>3));
                }
                $html->CloseDiv();
                print '</section>';
            } else {
                print '<section id="already_installed_section" style="display: none">';
                print "<b>".$dictionary->GetValue("the_point_already_installed_enclosure")."</b>";
                print "<br/>";
                print '</section>';
                print '<section id="deactivate_section">';
                print "<b>".$dictionary->GetValue("are_you_sure_deactivate_point")."</b>";
                print "<br/><br/>";
                $html->OpenDiv("col-xs-12");
                {
                    $html->DrawFormInput("button", "deactivate_point", "deactivate_point", NULL, array("class"=>"btn btn-success col-xs-12 installation_status", "data-point_id"=>$point_id, "data-status_type"=>3));
                }
                $html->CloseDiv();
                print '</section>';
            }
            ?>
        </div>
    </div>

    <div class="tab-pane fade" id="tab_activate">
        <div class='col-xs-12 well' style='padding: 20px; line-height: 2em; text-align: center'>
            <?php
            print $previously_status;
            print "<b>".$dictionary->GetValue("are_you_sure_activate_point")."</b>";
            print "<br/><br/>";
            $html->OpenDiv("col-xs-12");
            {
                $html->DrawFormInput("button", "activate_point", "activate_point", NULL, array("class"=>"btn btn-success col-xs-12 installation_status", "data-point_id"=>$point_id, "data-status_type"=>1));
            }
            $html->CloseDiv();
            ?>
        </div>
    </div>

    <div class="tab-pane fade" id="tab_problem">
        <div class='col-xs-12 well' style='padding: 20px; line-height: 2em'>
            <div class="form-group ">
                <label class="control-label"><?php echo $dictionary->GetValue("installation_problem");?>:</label>
                <?php
                    print $previously_status;
                    $InstallationProblemArr = $Installation->GetInstallationProblemArr();
                    $html->DrawFormInput("select", "installation_problem_id", "installation_problem_id", $InstallationProblemArr, array("class"=>"form-control", "data-point_id"=>$point_id));
                    print '<hr styel="margin: 10px 0" /><label class="control-label">'.$dictionary->GetValue("create_notes").':</label>';
                    $html->DrawFormInput("text", "create_notes", NULL, NULL, array("class"=>"form-control"));
                    print '<br/>';
                    $html->DrawFormInput("button", "add_problem", "add_problem", NULL, array("class"=>"btn btn-success col-xs-12 add_problem", "data-point_id"=>$point_id, ));
                ?>
            </div>
            <br/>

        </div>
    </div>

    <div class="tab-pane fade" id="tab_comments">
        <div class='col-xs-12 well' style='padding: 20px; line-height: 2em'>
            <div class="form-group ">
                <?php
                    print $previously_status;
                    print '<label class="control-label">'.$dictionary->GetValue("comment").':</label>';
                    $html->DrawFormInput("textarea", "comment", NULL, NULL, array("class"=>"form-control"));
                    print '<br/>';
                    $html->DrawFormInput("button", "add_comment", "add_comment", NULL, array("class"=>"btn btn-success col-xs-12 add_comment", "data-point_id"=>$point_id, ));
                ?>
            </div>
            <br/>

        </div>
    </div>

    <section id="installation_problem">
        <?php
        $filter = array();
        $filter["point_id"] = $point_id;
        $filter["state"] = 1;
        $activeProblems = $Installation->GetInstallationProblem($filter);
        if ($activeProblems) {
            for($i = 0; $i < count($activeProblems); $i++){
                if($activeProblems[$i]["state"] == 1){
                    $activeProblems[$i]["state_label"] = '<span class="label label-success"> '.$dictionary->GetValue("closed").' </span>';
                    $activeProblems[$i]["change_state"] = '<a href="javascript:;" class="btn inactive_followup_state" style="color:#36c6d3" data-id="'.$activeProblems[$i]["problem_report_id"].'" data-point_id="'.$activeProblems[$i]["point_id"].'" data-state="0" title=""><i class="fa fa-toggle-on"></i></a>';
                }else{
                    $activeProblems[$i]["state_label"] = '<span class="label label-danger"> '.$dictionary->GetValue("open").' </span>';
                    $activeProblems[$i]["change_state"] = '<a href="javascript:;" class="btn inactive_followup_state" style="color:#ed6b75" data-id="'.$activeProblems[$i]["problem_report_id"].'" data-point_id="'.$activeProblems[$i]["point_id"].'" data-state="1" title=""><i class="fa fa-toggle-on"></i></a>';
                }
            }

            $cols = array();
            $cols[] = array("column"=>"installation_problem");
            $cols[] = array("column"=>"create_time_stamp");
            $cols[] = array("column"=>"change_state", "title"=>"");

            $tableOptions = array();
            $tableOptions["tableClass"]= "table-hover table-bordered table-condensed table-striped";
            $tableOptions["key"]=array("id"=>"problem_report_id");
            $html->Table($activeProblems, $cols, $tableOptions);
        }
        ?>
    </section>

</div>



<ul class="nav nav-tabs">
    <li class="active">
        <a href="#tab_installed" class="active" style="padding: 10px 5px" data-toggle="tab" aria-expanded="false"> <?php echo $dictionary->GetValue("installed");?> </a>
    </li>
    <li>
        <a href="#tab_calculated" class="" style="padding: 10px 5px" data-toggle="tab" aria-expanded="false"> <?php echo $dictionary->GetValue("calcualted");?> </a>
    </li>
    <li>
        <a href="#tab_ReportProblem" class="" style="padding: 10px 5px" data-toggle="tab" aria-expanded="false"> <?php echo $dictionary->GetValue("report");?> </a>
    </li>
    <li>
        <a href="#tab_all_comments" class="" style="padding: 10px 5px" data-toggle="tab" aria-expanded="false"> <?php echo $dictionary->GetValue("comments");?> </a>
    </li>
</ul>
<div class="tab-content">
    <div class="tab-pane fade active in" id="tab_installed">
        <section id="installed_enclosures"></section>
    </div>
    <div class="tab-pane fade" id="tab_calculated">
        <?php
        $calculated = $Installation->GetCalculatedEnclosures(array("point_id"=>$point_id));
        $cols = array();
        $cols[] = array("column"=>"enclosure_type");
        $cols[] = array("column"=>"enclosure_count");
        $tableOptions = array();
        $tableOptions["tableClass"]= "table-bordered table-striped";

        $html->Table($calculated, $cols, $tableOptions);
        ?>
    </div>
    <div class="tab-pane fade" id="tab_ReportProblem">
        <?php
        $condition2 = "&point_id=".$point_id;

        $cols2 = array();
        $cols2[] = array("column"=>"installation_problem");
        $cols2[] = array("column"=>"create_notes");
        $cols2[] = array("column"=>"create_time_stamp");
        $cols2[] = array("column"=>"update_time_stamp");
        $cols2[] = array("column"=>"state");
        $tableOptions2 = array();
        $tableOptions2["tableClass"]= "table-hover table-bordered table-condensed table-striped";
        $tableOptions2["paging"]= "false";
        $tableOptions2["key"]=array("id"=>"problem_report_id");
        $html->Datatable("example2", "api/list.report.problem.php?".$condition2, $cols2, $tableOptions2);
        ?>
    </div>

    <div class="tab-pane fade" id="tab_all_comments">
        <?php
        $condition2 = "&point_id=".$point_id;
        $filter = array();
        $filter["point_id"] = $point_id;
        $EnclosureInstallationcomments = $Installation->GetInstallationComments($filter);
        $cols2 = array();
        $cols2[] = array("column"=>"NAME", "title"=>"Name");
        $cols2[] = array("column"=>"comments", "title"=>"Comments");
        $cols2[] = array("column"=>"comment_time", "title"=>"Comment Time");
        $tableOptions = array();
        $tableOptions["tableClass"]= "table-bordered table-striped";
        $html->Table($EnclosureInstallationcomments, $cols2, $tableOptions);
        // $html->Datatable("example2", "api/list.comments.php?".$condition2, $cols2, $tableOptions2);
        ?>
    </div>
</div>

<style>
    #myModal{
        padding-left: 0px !important;
    }
</style>
<script>
$(function() {
    EnclosurePointList(<?php print $point_id; ?>);

    setTimeout(function () {
        getCoordinates()
        , 1000
    });
});
</script>