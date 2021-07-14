<?php
include_once realpath(__DIR__ . '/../..').'/include/checksession.php';
include_once realpath(__DIR__ . '/../..').'/include/checkpermission.php';
include_once realpath(__DIR__ . '/../..').'/include/settings.php';
include_once realpath(__DIR__ . '/../..').'/class/Dictionary.php';
include_once realpath(__DIR__ . '/../..').'/class/AssemblyTeam.class.php';
require_once realpath(__DIR__ . '/../..').'/class/HtmlHelper.php';

$html = new HTML($LANGUAGE);
$AssemblyTeam = new AssemblyTeam();
$dictionary = new Dictionary($LANGUAGE);
$dictionary->GetAllDictionary();

$condition = array();
$condition["WEB_BROWSE"] = 3;

$team_id = NULL;

if(isset($_REQUEST['team_id']) && $_REQUEST['team_id'] != "" ){
    $team_id = $_REQUEST['team_id'];
}

$TeamEnclosureConfig = $AssemblyTeam->GetTeamEnclosureConfig($team_id);

$TeamConfigAlreadyInserted = array();
for($j=0; $j<count($TeamEnclosureConfig); $j++){
    $TeamConfigAlreadyInserted[] = $TeamEnclosureConfig[$j]["enclosure_config_id"];
}

$EnclosureConfig = $AssemblyTeam->getEnclosureConfig($team_id);

$html->OpenForm ("code/team.default.config.insert.code.php", "add_assembly_team", "vertical" , "post");
{
    $html->OpenDiv("row");
    {
        $html->OpenSpan(12);
        {
            //echo  '<div class="mt-checkbox-list" style="height: 200px; overflow: auto; margin-bottom: 20px">';
            echo '<div class="well" style="height: 200px; overflow: auto; margin-bottom: 20px; " >';
            $html->DrawFormField ( "checkbox", "add_configuration", NULL, $EnclosureConfig, array("flow"=>"horizental", "items-flow"=>"vertical", "label-align"=>"opposite", "checkBoxList"=>true, "class"=>"form-control add_configuration", "data-team_id"=>$team_id) );
            echo '</div>';
            //echo '</div>';

            $html->HiddenField("team_id", $team_id);

            if ($TeamEnclosureConfig) {
                $cols = array();
                //$cols[] = array("column"=>"team_name");
                $cols[] = array("column"=>"enclosure_type");
                $cols[] = array("column"=>"configuration_name");
                $cols[] = array("column"=>"priority");
                $cols[] = array("column"=>"ACTION_COL", "style"=>"width:100px","action-type"=>"ajax",
                                    "buttons"=> array(
                                        array("action-class"=>"configuration_priority_edit", "button-icon"=>"fa fa-pencil", "title"=>$dictionary->GetValue("Edit"), "type"=>"link", "url"=>"href=javascript:;"),
                                        array("action-class"=>"configuration_delete", "button-icon"=>"fa fa-times", "title"=>$dictionary->GetValue("Delete"), "type"=>"button", "url"=>"href=javascript:;")
                                    )
                                );

                $tableOptions = array();
                $tableOptions["tableClass"]= "table-hover table-bordered table-condensed table-striped";
                $tableOptions["key"]=array("id"=>"team_default_config_id", "team_id"=>"team_id");
                $html->Table($TeamEnclosureConfig, $cols, $tableOptions);
            }
        }
        $html->CloseSpan();
    }
    $html->CloseDiv();
}
$html->CloseForm();
?>