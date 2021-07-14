<?php
include_once realpath(__DIR__ . '/../..').'/include/checksession.php';
include_once realpath(__DIR__ . '/../..').'/include/checkpermission.php';
require_once realpath(__DIR__ . '/../..').'/class/HtmlHelper.php';
require_once realpath(__DIR__ . '/../..').'/class/Assembly.class.php';

$html = new HTML ( $LANGUAGE );
$dictionary = new Dictionary ( $LANGUAGE );
$dictionary->GetAllDictionary ();
$Assembly = new Assembly( );

$order_id = $_GET['id'];
?>
<!-- <div class="portlet box green"> -->
    <!-- <div class="portlet-title">
        <div class="caption">
            <? php // echo $dictionary->GetValue("change_gateway_status");?>
        </div>
    </div> -->
    <div class="portlet-body" >
        <div class="row">
            <div class="col-lg-12">
                <?php
                    $html->OpenDiv("row");
                    {
                        $html->OpenSpan(6);
                        {
                            $html->DrawFormField ( "text", "activate_date", date("Y-m-d"), NULL, array("class"=>"form-control date-picker", "data-date-format"=>"yyyy-mm-dd", "readonly"=>"readonly") );
                            $html->HiddenField("order_id", $order_id);
                        }
                        $html->CloseSpan();
                    }
                    $html->CloseDiv();
                ?>
                <!-- <textarea class="col-lg-12" id="iccids" rows="10" style="margin-bottom: 20px" placeholder='ICCID1, ICCID2, ........... , ICCIDn'></textarea> -->
                <br/>
            </div>
            <div class="form-group col-lg-6">
                <button class="btn green col-xs-12 iccid_status" data-id="1" type="button"><?php echo $dictionary->GetValue("activate");?></button>
            </div>
            <div class="form-group col-lg-6">
                <button class="btn red col-xs-12 iccid_status" data-id="0" type="button"><?php echo $dictionary->GetValue("deactivate");?></button>
            </div>
        </div>
    </div>
<!-- </div> -->

<!-- <div class="col-lg-12 wizard">
    <div class="tab-content"> -->
<!--
        <div class="tools">
            <a href="javascript:;" class="expand" data-original-title="" title=""> </a>
        </div> -->
    <!-- </div>
</div> -->


<style>
    ::placeholder {
    font-size: 20px;
    font-weight: bold;
    }
</style>
<script>
$( document ).ready(function() {
    $( "#activate_date" ).datepicker();
});
</script>