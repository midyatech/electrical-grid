<?php
include '../include/header.php';
include_once realpath(__DIR__ . '/..').'/include/checksession.php';
include_once realpath(__DIR__ . '/..').'/include/checkpermission.php';
require_once realpath(__DIR__ . '/..').'/class/HtmlHelper.php';
require_once realpath(__DIR__ . '/..').'/class/SupplyChain.class.php';
require_once realpath(__DIR__ . '/..').'/class/Dictionary.php';

$dictionary= new Dictionary("ENGLISH");
$html = new HTML('ENGLISH');

$sc = new SupplyChain();

if ($_GET["id"]) {
    $id = $_GET["id"];

    $enclosure = $sc->GetEnclosureDetails($id);
    $e_trace = $sc->GetEnclosureTrace($id);


    $ml = $sc->GetEnclosureLinking($id);
}



$html->OpenDiv("row");
{
    $html->OpenSpan(12);
    {
        // $actions = array (
        //     array("type"=>"literal", "value"=>'<a href="./ui/enclosure.installation.list.export.php?'.$filter.'" class="btn green" target="blank"><i class="fa fa-file-excel-o"></i></a>')
        // );

        $html->OpenWidget ("meter_details", $actions, array('collapse' => false, 'fullscreen'=>false));
        {
            $html->OpenForm ( "", "form2", "horizental");
            {
                $html->OpenDiv("row");
                {
                    $html->OpenSpan(6);
                    {
                        $html->DrawFormField("label", "enclosure_sn", $enclosure[0]["enclosure_sn"], NULL, array("class"=>"form-control", "flow"=>"horizental") );
                    }
                    $html->CloseSpan();
                    $html->OpenSpan(6);
                    {
                        $html->DrawFormField("label", "enclosure_type", $enclosure[0]["enclosure_type"]. '['.$enclosure[0]["configuration_name"].']', NULL, array("class"=>"form-control", "flow"=>"horizental") );
                    }
                    $html->CloseSpan();
                }
                $html->CloseDiv();
            }
            $html->CloseForm();
        }
        $html->CloseWidget();

        $html->OpenWidget ("enclosure_linking", null, array('collapse' => false, 'fullscreen'=>false));
        {
            $html->OpenForm ( "", "form3", "vertical");
            {
                $html->OpenDiv("row");
                {
                    $html->OpenSpan(2);
                    {
                        $html->DrawFormField("label", "station", $ml[0]["station"], NULL, array("class"=>"form-control", "flow"=>"vertical") );
                    }
                    $html->CloseSpan();
                    $html->OpenSpan(2);
                    {
                        $html->DrawFormField("label", "feeder", $ml[0]["feeder"], NULL, array("class"=>"form-control", "flow"=>"vertical") );
                    }
                    $html->CloseSpan();
                    $html->OpenSpan(2);
                    {
                        $html->DrawFormField("label", "transformer_number", $ml[0]["transformer_number"], NULL, array("class"=>"form-control", "flow"=>"vertical") );
                    }
                    $html->CloseSpan();
                    $html->OpenSpan(2);
                    {
                        $html->DrawFormField("label", "point_id", $ml[0]["point_id"], NULL, array("class"=>"form-control", "flow"=>"vertical") );
                    }
                    $html->CloseSpan();
                    $html->OpenSpan(2);
                    {
                        $html->DrawFormField("label", "enclosure_sn", $ml[0]["enclosure_sn"], NULL, array("class"=>"form-control", "flow"=>"vertical") );
                    }
                    $html->CloseSpan();
                }
                $html->CloseDiv();
            }
            $html->CloseForm();
        }
        $html->CloseWidget();

        $html->OpenWidget ("enclosure_trace", null, array('collapse' => false, 'fullscreen'=>false));
        {
            print '<div class="timeline">';
            for ($i=0; $i < count($e_trace); $i++) {
                switch($e_trace[$i]["enclosure_trace_status_id"])
                {
                    case 1:
                        $icon = 'fa fa-plus';
                        $color = 'bg-green-jungle';
                        $description = "Enclosure";
                        break;
                    case 2:
                        $icon = 'fa fa-remove';
                        $color = 'bg-red';
                        $description = "Point";
                        break;
                    case 3:
                        $icon = 'fa fa-wrench';
                        $color = 'bg-blue-chambray';
                        $description = "";
                        break;
                    case 4:
                        $icon = 'fa fa-wrench';
                        $color = 'bg-blue-chambray';
                        $description = "";
                        break;
                    default:
                        $icon = '';
                        $color = 'bg-blue-steel';
                        break;
                }
            ?>
                <!-- TIMELINE ITEM -->
                <div class="timeline-item">
                    <div class="timeline-badge mt-timeline-icon <?php print $e_trace[$i]["color"];?> bg-font-red border-grey-steel">
                        <i class="<?php print $e_trace[$i]["icon"];?>"></i>
                    </div>
                    <div class="timeline-body">
                        <div class="timeline-body-arrow"> </div>
                        <div class="timeline-body-head">
                            <div class="timeline-body-head-caption">
                                <span class="timeline-body-alerttitle font-red-intense"><?php print $e_trace[$i]["enclosure_trace_status"];?></span>
                                <span class="timeline-body-time font-grey-cascade"><?php print $e_trace[$i]["timestamp"];?></span>
                            </div>
                            <div class="timeline-body-head-actions"> </div>
                        </div>
                        <div class="timeline-body-content">
                            <a href="javascript:;" class="timeline-body-title font-blue-madison"><?php echo $description;?>: <?php print $e_trace[$i]["description"];?></a>
                            <span class="font-grey-cascade"> (<?php print $e_trace[$i]["NAME"];?>)</span>
                        </div>
                    </div>
                </div>
                <!-- END TIMELINE ITEM -->
            <?php
            }
            print '</div>';
        }
        $html->CloseWidget();
    }
    $html->CloseSpan();
}
$html->CloseDiv();

include '../include/footer.php';
?>

<style>
    .timeline-badge {
        width: 70px;
        height: 70px;
        background-color: #ccc;
        border-radius: 50%!important;
        z-index: 5;
        border: 0;
        overflow: hidden;
        position: relative;
    }
    .bg-font-red {
        color: #fff!important;
    }
    .border-after-grey-steel:after, .border-before-grey-steel:before, .border-grey-steel {
        border-color: #e9edef!important;
    }
    .mt-timeline-icon>i {
        top: 50%;
        left: 80%;
        transform: translateY(-50%) translateX(-50%);
        font-size: 24px;
    }
    .timeline-badge i, .timeline-badge i {
        /* top: 1px; */
        position: relative;
    }
    .bg-blue {
        background: #3598dc!important;
    }
    .bg-green-turquoise {
        background: #36D7B7!important;
    }
    .bg-purple-medium {
        background: #BF55EC!important;
    }
    .bg-blue-steel {
        background: #4B77BE!important;
    }
    .bg-green-jungle {
        background: #26C281!important;
    }
    .bg-blue-chambray {
        background: #2C3E50!important;
    }
</style>