<?php
require_once realpath(__DIR__ . '/../..').'/include/settings.php';
include_once realpath(__DIR__ . '/../..').'/include/checksession.php';
include_once realpath(__DIR__ . '/../..').'/include/checkpermission.php';
require_once realpath(__DIR__ . '/../..').'/class/Dictionary.php';
require_once realpath(__DIR__ . '/../..').'/class/Assembly.class.php';
$dictionary = new Dictionary ( $LANGUAGE );
$dictionary->GetAllDictionary ();
$Assembly = new Assembly();

$aoid = $feeder_id = $transformer_id = null;
if (isset($_GET["aoid"]) && $_GET["aoid"] != null) {
    $aoid = $_GET["aoid"];
    $order = $Assembly->GetAssemblyOrder($aoid);
    $feeder_id = $order[0]["feeder_id"];
}

if (isset($_GET["transformer_id"]) && $_GET["transformer_id"] != null) {
    $transformer_id = $_GET["transformer_id"];
}

$project_items = $Assembly->getAssemblyOrderItems($aoid, NULL, $transformer_id);

//$feeder_items = $Assembly->getAssemblyOrderItems($aoid, $feeder_id, $transformer_id);


//$assemblyOrders = $Assembly->getActiveAssemblyOrders($aoid);

$filter = array();
$filter["assembly_order_id"]= $aoid;
$filter["user_id"] = $USERID;
$work_item = $Assembly->GetTeamStack($filter, 1);


?>
<?php
function drawKnob($data)
{
    $name = $data["enclosure_type"];
    $id = $data["enclosure_type_id"];
    $knobMax = $max = $data["enclosures_count"];
    $knobVal = $val = $data["manufactured_count"] != null ? $data["manufactured_count"] : 0;
    if ($val > $max) {
        $knobMax = $knobVal;
    }

    $percentage = $val*100/$max;
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
    echo '<div class="col-lg-2 col-md-2 col-sm-3 col-xs-4 knob-wrapper">
            <div class="e_name">'.$name.'</div>
            <input type="text" value="'.$knobVal.'" class="knob" id="'.$id.'" data-max="'.$knobMax.'" data-fgColor="'.$color.'"
                    data-angleOffset="-125" data-angleArc="250" data-rotation="clockwise" data-width="98%" readonly>
            <div class="knob-text">'.$val .'/'.$max.'</div>
        </div>';
}


// for ($i=0; $i<count(//); $i++) {

//     if (//[$i]["required_count"] > 0) {
//         $perc = floor(//[$i]["manufactured_count"]*100 / //[$i]["required_count"]);
//         if ($perc < 50) {
//             $color = "danger";
//         } else if ($perc < 75) {
//             $color = "warning";
//         } else {
//             $color = "success";
//         }

//         $progres = '<div class="progress">
//                         <div class="progress-bar  progress-bar-'.$color.'" role="progressbar"
//                                 aria-valuenow="'.//[$i]["manufactured_count"].'" aria-valuemin="0"
//                                 aria-valuemax="'.//[$i]["required_count"].'"
//                                 style="width: '.$perc.'%;">'.$perc.'
//                         </div>
//                     </div>';


//         print '<li '. (((//[$i]["assembly_order_id"] == $aoid) || ($aoid== null && $i==0)) ? 'class="active"' : '')  .'>
//                     <a href="#tab_'.//[$i]["assembly_order_id"].'" data-toggle="tab"> '.
//                     ((//[$i]["assembly_order_code"] == null) ? "#".//[$i]["assembly_order_id"] : //[$i]["assembly_order_code"]).'<br>'.
//                     '<div class=""><div class=""></div></div>'.
//                     $progres.
//                     ' </a>
//                 </li>';

//     }
// }

?>
<div class="row">
    <div class="col-lg-12">
        <!-- <div class="well">
            <div class="row">
                <div class="col-lg-4">
                    <?php //$html->DrawFormField("label", "assembly_order_code", $order[0]["assembly_order_code"], null, $options);?>
                </div>
                <div class="col-lg-4">
                    <?php //$html->DrawFormField ("label", "start_date", $order[0]["start_date"], null, array() );?>
                </div>
            </div>
        </div> -->
    </div>
</div>
<div class="row">
    <div class="col-lg-12">
        <div class="row">
            <div class="col-lg-12">
                <ul class="nav nav-tabs">
                    <li class="active">
                        <a href="#tab_1_1" data-toggle="tab"> Order Summary </a>
                    </li>
                    <?php /*
                    <li>
                        <a href="#tab_1_2" data-toggle="tab"> Feeder Summary </a>
                    </li>
                    */ ?>
                </ul>
                <div class="tab-content">
                    <div class="tab-pane fade active in" id="tab_1_1">
                        <div class="col-lg-12">
                            <div class="row">
                                <?php
                                for ($i=0; $i<count($project_items); $i++) {
                                    drawKnob($project_items[$i]);
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                    <?php /*
                    <div class="tab-pane fade active in" id="tab_1_2">
                        <div class="col-lg-12">
                            <div class="row">
                                <?php
                                for ($i=0; $i<count($feeder_items); $i++) {
                                    drawKnob($feeder_items[$i]);
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                    */ ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(function() {
        $(".knob").knob();
        $("#tab_1_2").removeClass("active");
        $("#tab_1_2").removeClass("in");
    });
</script>