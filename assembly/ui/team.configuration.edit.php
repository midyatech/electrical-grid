<?php
include_once realpath(__DIR__ . '/../..').'/include/checksession.php';
include_once realpath(__DIR__ . '/../..').'/include/checkpermission.php';
include_once realpath(__DIR__ . '/../..').'/include/settings.php';
include_once realpath(__DIR__ . '/../..').'/class/Dictionary.php';
include_once realpath(__DIR__ . '/../..').'/class/AssemblyTeam.class.php';

$AssemblyTeam = new AssemblyTeam();
$User = new User();
$dictionary = new Dictionary($LANGUAGE);
$dictionary->GetAllDictionary();

$condition = array();
$condition["WEB_BROWSE"] = 3;

$team_default_config_id = NULL;

if(isset($_REQUEST['team_default_config_id']) && $_REQUEST['team_default_config_id'] != "" ){
    $team_default_config_id = $_REQUEST['team_default_config_id'];
}

$TeamEnclosureConfig = $AssemblyTeam->GetTeamEnclosureConfigByID($team_default_config_id);

$html->OpenForm ("code/team.default.config.update.code.php", "add_assembly_team", "vertical" , "post");
{
    $html->OpenDiv("row");
    {
        $html->OpenSpan(12);
        {
            $html->DrawFormField ( "number", "priority", $TeamEnclosureConfig[0]["priority"], NULL, array("class"=>"form-control") );
            $html->HiddenField("team_default_config_id", $team_default_config_id);
            //$html->HiddenField("team_id", $team_id);

            $button = array ();
            $button[]=array ( "type"=>"button", "name"=>"add", "value"=>"change", "list"=>NULL, "options"=>array("class"=>"form-control btn btn-success update_config_priority") );
            $html->DrawGenericFormField ( "&nbsp;", $button, "vertical", array("suffix"=>""));
        }
        $html->CloseSpan();
    }
    $html->CloseDiv();
}
$html->CloseForm();
?>