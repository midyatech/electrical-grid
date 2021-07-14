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

$table_number = $position_number = NULL;

if(isset($_REQUEST['table']) && $_REQUEST['table'] != "" ){
    $table_number = $_REQUEST['table'];
}

if(isset($_REQUEST['position']) && $_REQUEST['position'] != "" ){
    $position_number = $_REQUEST['position'];
}

$Option = array("class"=>"form-control", "optional"=>"true");
$UserArr = $AssemblyTeam->GetUsersArr($condition);
$TableArr = array("0"=>1, "1"=>2, "2"=>3, "3"=>4, "4"=>5, "5"=>6, "6"=>7, "7"=>8, "8"=>9, "9"=>10, "10"=>11, "11"=>12);
$PositionArr = array("0"=>1, "1"=>2, "2"=>3, "3"=>4, "4"=>5, "5"=>6);

$html->OpenForm ("code/assembly.team.insert.php", "add_assembly_team", "vertical" , "post");
{
    $html->OpenDiv("row");
    {
        $html->OpenSpan(12);
        {
            $html->DrawFormField("text", "team_name", NULL, NULL, $Option );
            $html->DrawFormField("select", "user_id", NULL, $UserArr, $Option);
            if($table_number){
                $html->HiddenField("table_number", $table_number);
            } else {
                $html->DrawFormField("select", "table_number", NULL, $TableArr, $Option);
            }

            if( $position_number ) {
                $html->HiddenField("position_number", $position_number);
            } else {
                $html->DrawFormField("select", "position_number", NULL, $PositionArr, $Option);
            }

            $button = array ();
            $button[]=array ( "type"=>"button", "name"=>"add", "value"=>"Add", "list"=>NULL, "options"=>array("class"=>"form-control btn btn-success insert_team") );
            $html->DrawGenericFormField ( "&nbsp;", $button, "vertical", array("suffix"=>""));
        }
        $html->CloseSpan();
    }
    $html->CloseDiv();
}
$html->CloseForm();
?>