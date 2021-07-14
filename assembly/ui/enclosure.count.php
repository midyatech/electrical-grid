<?php
include_once realpath(__DIR__ . '/../..').'/include/checksession.php';
include_once realpath(__DIR__ . '/../..').'/include/checkpermission.php';
require_once realpath(__DIR__ . '/../..').'/class/HtmlHelper.php';
require_once realpath(__DIR__ . '/../..').'/class/Enclosure.class.php';
require_once realpath(__DIR__ . '/../..') . '/class/Tree.php';
require_once realpath(__DIR__ . '/../..') . '/class/User.php';
$html = new HTML ( $LANGUAGE );
$User = new User ();
$dictionary = new Dictionary ( $LANGUAGE );
$dictionary->GetAllDictionary ();
$options = array("class"=>"form-control");
$listoptions = array("class"=>"form-control","optional"=>"true");
$Enclosure = new Enclosure( );
$description_key = "description_Enclosure";
$area_tree = new Tree("AREA_TREE");
$area_id=$user_id=null;
$UsersArray=$User->GetUser();
$filter = array();
$condition = null;
$count = $sum = 0;

if( isset($_POST["to_date"] ) && $_POST["to_date"] != NULL ) {
    $to_date = $_POST["to_date"];
}else{
    $to_date = date("Y-m-d");
}
if( isset($_POST["from_date"] ) && $_POST["from_date"] != NULL ) {
    $from_date = $_POST["from_date"];
}else{
    $from_date = date('Y-m-d', strtotime($to_date . "-30 days"));
}
$condition .= "&from_date=$from_date&to_date=$to_date";

if(isset($_REQUEST["user_id"])&&$_REQUEST["user_id"]!=NULL){
    $user_id = $_REQUEST["user_id"];
    $condition .= "&user_id=". $user_id;
}
$cols = array();
$cols[] = array("column"=>"number_of_consumers");
$cols[] = array("column"=>"single_phase");
$cols[] = array("column"=>"three_phase");
$cols[] = array("column"=>"enclosure_count");

$tableOptions = array();
$tableOptions["tableClass"]= "table-hover table-bordered table-striped";
$tableOptions["ordering"]= "false";
//$tableOptions["paging"]="false";
//$tableOptions["footer"]="true";
//$tableOptions["totals"]=array("1"=>$count, "2"=>$sum);
$html->OpenDiv("row");
{
    $html->OpenSpan(12);
    {
        $html->OpenWidget ("Enclosure_summary", null, array('collapse' => true, 'fullscreen'=>true,'table-actions'=>"example1"));
        {
            $html->OpenForm ( null, "form3");
            {
                $html->OpenDiv("row");
                {
                    $html->OpenSpan(3);
                    {
                        $html->DrawFormField ( "text", "from_date", $from_date, NULL, array("class"=>"form-control date-picker", "data-date-format"=>"yyyy-mm-dd", "readonly"=>"readonly") );
                    }
                    $html->CloseSpan();

                    $html->OpenSpan(3);
                    {
                        $html->DrawFormField ( "text", "to_date", $to_date, NULL, array("class"=>"form-control date-picker", "data-date-format"=>"yyyy-mm-dd", "readonly"=>"readonly") );
                    }
                    $html->CloseSpan();
                    $html->OpenSpan(4);
                    {
                        $html->DrawFormField ( "select", "user_id",$user_id, $UsersArray, $listoptions);
                    }
                    $html->CloseSpan();

                    $html->OpenSpan(2);
                    {
                        ?>
                        <div class="form-group">
                            <label>&nbsp;</label>
                            <div class="input-group">
                                <span class="input-group-btn">
                                    <button class="btn green filter_enclosure_count" type="submit"><?php echo $dictionary->GetValue("filter");?></button>
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
            
            $html->Datatable("example", "api/list.enclosure.count.php?".$condition, $cols, $tableOptions);
        }
        $html->CloseWidget();
    }
    $html->CloseSpan();
}
$html->CloseDiv();
?>
