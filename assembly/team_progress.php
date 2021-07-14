<?php
include '../include/header.php';
require_once realpath(__DIR__ . '/..').'/include/settings.php';
include_once realpath(__DIR__ . '/..').'/include/checksession.php';
include_once realpath(__DIR__ . '/..').'/include/checkpermission.php';
require_once realpath(__DIR__ . '/..').'/class/Enclosure.class.php';
require_once realpath(__DIR__ . '/..').'/class/Dictionary.php';
require_once realpath(__DIR__ . '/..').'/class/AssemblyTeam.class.php';
require_once realpath(__DIR__ . '/..').'/class/Assembly.class.php';
$dictionary = new Dictionary ( $LANGUAGE );
$dictionary->GetAllDictionary ();
$Enclosure = new Enclosure( );
$AssemblyTeam = new AssemblyTeam();
$Assembly = new Assembly();

$aoid = null;
if (isset($_GET["id"]) && $_GET["id"] != ""){
    $aoid = $_GET["id"];
}

$team_prgoress = $Assembly->GetTeamProgress($aoid);
$AssemblyOrders = $Assembly->getActiveAssemblyOrdersArr();



function drawKnob($data)
{
    $name = $data["team_name"];
    $id = $data["team_id"];
    $knobMax = $max = $data["enclosures_count"];
    $knobVal = $val = $data["manufactured_count"] != null ? $data["manufactured_count"] : 0;
    if ($val > $max) {
        $knobMax = $knobVal;
    }

    if ($max>0) {
        $percentage = $val*100/$max;
    } else {
        $percentage = 0;
    }
    if ($percentage > 100) {
        $color = "#ff0000";
    } else {
        if ($percentage >= 75) {
            $color = "#00F729";
        } else if ($percentage >= 50) {
            $color = "#F7C000";
        } else {
            $color = "#F75800"; //";
        }
    }
    echo '<div class="col-lg-1 col-md-2 col-sm-3 col-xs-4 knob-wrapper">
            <div class="e_name">'.$name.'</div>
            <input type="text" value="'.$knobVal.'" class="knob" id="'.$id.'" data-max="'.$knobMax.'" data-fgColor="'.$color.'"
                    data-angleOffset="-125" data-angleArc="250" data-rotation="clockwise" data-width="98%" readonly>
            <div class="knob-text">'.$val .'/'.$max.'</div>
        </div>';
}
?>
<style>
.progress {
    margin-bottom: 10px
}
.knob-wrapper {
    /*height: 110px;*/
    margin-bottom: 20px;

}
.knob-wrapper div{
    text-align: center;
}
.knob-text {
    text-align: center;
    margin-bottom: 20px;
    margin-top: -20px;
}
</style>
<div class="row form">
    <div class="col-lg-12">
        <div class="well" style="background-color:#fff; border:solid 1px #999; padding: 20px !important; border: solid 1px silver">
            <div class="row">
                <div class="col-lg-12">
                    <div class="row">
                        <?php
                            $html->DrawFormField("select", "assembly_order", $aoid, $AssemblyOrders, array("class"=>"form-control", "optional"=>"All", "flow"=>"horizental"));
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row form">
    <div class="col-lg-12">
        <div class="well" style="background-color:#fff; border:solid 1px #999; padding: 20px !important; border: solid 1px silver">
            <div class="row">
                <div class="col-lg-12">
                    <div class="row">
                        <?php
                        for ($i=0; $i<count($team_prgoress); $i++) {
                            drawKnob($team_prgoress[$i]);
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(function() {
        $(".knob").knob();
        $('#assembly_order').on('change', function () {
            id = $(this).val();
            if (id!="") {
                location.href = "team_progress.php?id="+id;
            } else {
                location.href = "team_progress.php";
            }
        });
        $(".knob-wrapper").height($(".knob-wrapper").first().height()+2);
    });

</script>
<script src="../jquery.knob.min.js"></script>
<script src="js/enclosure.js?v=1"></script>
<?php
include '../include/footer.php';
?>