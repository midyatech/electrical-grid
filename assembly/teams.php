<?php
include '../include/header.php';
require_once realpath(__DIR__ . '/..').'/include/settings.php';
include_once realpath(__DIR__ . '/..').'/include/checksession.php';
include_once realpath(__DIR__ . '/..').'/include/checkpermission.php';
require_once realpath(__DIR__ . '/..').'/class/Enclosure.class.php';
require_once realpath(__DIR__ . '/..').'/class/Dictionary.php';
require_once realpath(__DIR__ . '/..').'/class/Assembly.class.php';
require_once realpath(__DIR__ . '/..').'/class/AssemblyTeam.class.php';
$dictionary = new Dictionary ( $LANGUAGE );
$dictionary->GetAllDictionary ();
$Enclosure = new Enclosure( );
$Assembly = new Assembly();
$AssemblyTeam = new AssemblyTeam();

$TransformerArr = $AssemblyTeam->GetActiveAssemblyOrderTransformerArr();

$priority = $AssemblyTeam->GetActiveAssemblyOrderTransformerArr(true);
$transformer_id = $priority[0][0];

function DrawTeamTable($i, $team)
{
    echo '<div class="col-xs-6">';
    echo '<div class="teamtable">';
    echo "<h3>Table $i</h3>";
    ?>
    <table class="table table-bordered table-striped">
        <?php
            echo '<tr>';
            DrawTeam($i, 1, $team);
            DrawTeam($i, 2, $team);
            echo '</tr><tr>';
            DrawTeam($i, 3, $team);
            DrawTeam($i, 4, $team);
            echo '</tr><tr>';
            DrawTeam($i, 5, $team);
            DrawTeam($i, 6, $team);
            echo '</tr>';
        ?>
    </table>
    <?php
    echo '</div>';
    echo '</div>';
}

function DrawTeam($table, $position, $team)
{
    global $AssemblyTeam, $dictionary;
    echo '<td><span class="text-muted"> ('.$position.') </span>';
    $team_found = false;
    for ($i=0; $i<count($team); $i++) {
        if ($position == $team[$i]["position_number"]) {
            $team_found = true;
            print '<h4 class="text-primary">'.$team[$i]["team_name"].' ('.$team[$i]["NAME"].')'.'</h4>';
            echo '<button type="button" class="btn green pull-right team_configuration" data-team_id="'.$team[$i]["team_id"].'"><i class="fa fa-cogs"></i> '.$dictionary->GetValue("Configure").' </button>';
            $config = $AssemblyTeam->GetTeamEnclosureConfig($team[$i]["team_id"]);
            if ($config) {
                echo '<ul>';
                for ($j=0; $j<count($config); $j++) {
                    echo '<li>'.$config[$j]["enclosure_type"]. ' ['.$config[$j]["configuration_name"].']</li>';
                }
                echo '</ul>';
            }
        }
    }
    if (!$team_found) {
        echo '<button type="button" class="btn red add_team" data-table="'.$table.'" data-position="'.$position.'"><i class="fa fa-user"></i> '.$dictionary->GetValue("Add_Team").' </button>';
    }
    echo '</td>';
}
?>
<style>
.teamtable {
    /*height: 300px;*/
    border: solid 1px #ddd;
    margin: 20px auto;
}
.teamtable table td{
    min-height: 50px;
}
.teamtable h3{
    text-align:center;
}
.teamtable h4 {
    display: inline-block;
}
</style>



<?php

$options = array("class"=>"form-control", "flow"=>"horizental", "optional"=>"true");

$html->OpenDiv("row");
{
    $html->OpenSpan(12);
    {
        $html->OpenWidget ("Teams", NULL, array('collapse' => true, 'fullscreen'=>true));
        {
            $html->OpenForm ( "code/team.assembly.order.transformer.priority.upadte.code.php", "form3" );
            {
                $html->OpenDiv("row");
                {
                    $html->OpenSpan(6);
                    {
                        $html->DrawFormField ( "select", "transformer_id", $transformer_id, $TransformerArr, $options );
                    }
                    $html->CloseSpan();

                    $html->OpenSpan(2);
                    {
                        ?>
                        <div class="form-group">
                            <div class="input-group">
                                <span class="input-group-btn">
                                    <button class="btn green" type="submit"><?php echo $dictionary->GetValue("change");?></button>
                                </span>
                            </div>
                        </div>
                        <?php
                    }
                    $html->CloseSpan();
                }
                $html->CloseDiv();
            }
            $html->CloseForm();
        }
        $html->CloseWidget();
    }
    $html->CloseSpan();
}
$html->CloseDiv();

?>


<div class="row">
    <?php
    for ($i=1; $i<=12; $i++) {
        $team = $AssemblyTeam->GetTableTeams($i);
        DrawTeamTable($i, $team);
    }
    ?>
</div>
<script>

    $(document).ready(function() {

        var maxHeight = Math.max.apply(null, $("div.teamtable").map(function ()
        {
            return $(this).height();
        }).get());
        $("div.teamtable").height(maxHeight);

        $('#myModal').on('hidden.bs.modal', function () {
            window.location.reload();
        })

    });
</script>
<script src="js/assembly_team.js"></script>
<script src="js/enclosure.js?v=1"></script>
<?php
include '../include/footer.php';
?>