<?php
include_once realpath(__DIR__ . '/../..').'/include/checksession.php';
include_once realpath(__DIR__ . '/../..').'/include/checkpermission.php';
require_once realpath(__DIR__ . '/../..').'/class/HtmlHelper.php';
require_once realpath(__DIR__ . '/../..').'/class/Survey.class.php';


$html = new HTML ( $LANGUAGE );
$dictionary = new Dictionary ( $LANGUAGE );
$dictionary->GetAllDictionary ();
$options = array("class"=>"form-control");
$listoptions = array("class"=>"form-control","optional"=>"true");
$Survey = new Survey( );
$description_key = "description_Survey";


$filter = array();
$condition = NULL;
$count = $sum = 0;

$month = date('Y-m');
if(isset($_REQUEST["month_id"]) && $_REQUEST["month_id"] != ""){
    $month = $_REQUEST["month_id"];
}
$condition="&month_id=".$month;

$cols = array();
$cols[] = array("column"=>"NAME");
$cols[] = array("column"=>"month");
$cols[] = array("column"=>"points");
$cols[] = array("column"=>"salary");

$tableOptions = array();
$tableOptions["tableClass"]= "table-hover table-bordered table-striped";
//$tableOptions["paging"]="false";
//$tableOptions["footer"]="true";
//$tableOptions["totals"]=array("3"=>$sum);
$html->OpenDiv("row");
{
    $html->OpenSpan(12);
    {
        $html->OpenWidget ("salary_report",'', array('collapse' => true, 'fullscreen'=>true,'table-actions'=>"example1"));
        {
            $html->OpenForm ( null, "form3", "horizental");
            {
                $html->OpenDiv("row");
                {
                    $html->OpenSpan(4);
                    {
                        $html->DrawFormField ( "text", "month_id", $month, NULL, array("class"=>"form-control date-picker", "data-date-format"=>"yyyy-mm", "readonly"=>"readonly", "flow"=>"horizental") );
                    }
                    $html->CloseSpan();

                    $html->OpenSpan(2);
                    {
                        ?>
                        <div class="form-group">
                            <!--<label>&nbsp;</label>-->
                            <div class="input-group">
                                <span class="input-group-btn">
                                    <button class="btn green filter_salary_list" type="submit"><?php echo $dictionary->GetValue("search");?></button>
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

            $html->Datatable("example", "api/list.user.salary.php?".$condition, $cols, $tableOptions);
        }
        $html->CloseWidget();
    }
    $html->CloseSpan();
}
$html->CloseDiv();
?>
<script>
    $(function(){
        $('#month_id').datepicker({
            dateFormat: 'MM yy',
            viewMode: "months",
            minViewMode: "months",
            autoclose : true
        });
    });
</script>