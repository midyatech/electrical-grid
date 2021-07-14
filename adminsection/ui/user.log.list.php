<?php
include_once realpath(__DIR__ . '/../..').'/include/checkpermission.php';
require_once realpath(__DIR__ . '/../..').'/class/HtmlHelper.php';
require_once realpath(__DIR__ . '/../..').'/class/User.php';
require_once realpath(__DIR__ . '/../..').'/class/Helper.php';
require_once realpath(__DIR__ . '/../..').'/class/Tree.php';
require_once realpath(__DIR__ . '/../..').'/class/SystemMessage.php';
require_once realpath(__DIR__ . '/../..').'/class/UserLog.class.php';

$UserLog = new User_Log();
$tree = new Tree("DIR_TREE");
$message = New SysetemMessage($LANGUAGE);
$user = new User();
$html = new HTML($LANGUAGE);
$dictionary = new Dictionary($LANGUAGE);
$dictionary->GetAllDictionary();
$description_key = "description_users";

$ModuleOperaionsArr = $UserLog -> GetOperations();
$ModuleNamesArr = $UserLog -> GetModules();

/*filter parameters*/
$options = array("class"=>"form-control");
$filter = $USER = $TIMESTAMP = $MODULE_NAME = $MODULE_OPERATION = $KEY_DATA = $CRUD_OPERATION = null;

if(isset($_POST["USER"]) && $_POST["USER"] !=""){
	$USER = $_POST["USER"];
	$filter .= "&USER=".$USER;
}

if(isset($_POST["from_date"]) && $_POST["from_date"] != ""){
    $from_date = $_POST["from_date"];
    $filter .= "&from_date=". $from_date;
} else {
    $from_date = Date('Y-m-1');
    $filter .= "&from_date=". $from_date;
}

if(isset($_POST["to_date"]) && $_POST["to_date"] != ""){
    $to_date = $_POST["to_date"];
    $filter .= "&to_date=". $to_date;
} else {
    $to_date = date("Y-m-d");
    $filter .= "&to_date=". $to_date;
}

if(isset($_POST["MODULE_ID"]) && $_POST["MODULE_ID"] !=""){
	$MODULE_ID = $_POST["MODULE_ID"];
	$filter .= "&MODULE_ID=".$MODULE_ID;
}

if(isset($_POST["MODULE_OPERATION_ID"]) && $_POST["MODULE_OPERATION_ID"] !=""){
	$MODULE_OPERATION_ID = $_POST["MODULE_OPERATION_ID"];
	$filter .= "&MODULE_OPERATION_ID=".$MODULE_OPERATION_ID;
}

if(isset($_POST["KEY_DATA"]) && $_POST["KEY_DATA"] !=""){
	$KEY_DATA = $_POST["KEY_DATA"];
	$filter .= "&KEY_DATA=".$KEY_DATA;
}

// if(isset($_POST["CRUD_OPERATION"]) && $_POST["CRUD_OPERATION"] !=""){
// 	$CRUD_OPERATION = $_POST["CRUD_OPERATION"];
// 	$filter .= "&CRUD_OPERATION=".$CRUD_OPERATION;
// }
/*filter parameters*/

/**
 * Table Option Section
 */
$cols = array();
$cols[] = array("column"=>"NAME", "title"=>"USER");
$cols[] = array("column"=>"TIMESTAMP");
$cols[] = array("column"=>"MODULE_NAME");
$cols[] = array("column"=>"MODULE_OPERATION");
// $cols[] = array("column"=>"NOTES");
$cols[] = array("column"=>"KEY_DATA");
$cols[] = array("column"=>"NEW_DATA");
//$cols[] = array("column"=>"OLD_DATA");
$cols[] = array("column"=>"CRUD_OPERATION");
$cols[] = array("column"=>"TABLE_NAME");
$cols[] = array("column"=>"RECORD_ID");
// $cols[] = array("column"=>"RESULT");

$tableOptions = array();
//$tableOptions["key"]=array("user_id"=>"USER_ID");



$html->OpenWidget ("Users Log", NULL, array('collapse' => true));
{
	$html->OpenForm ( null, "form3" );
	{
		$html->OpenDiv("row");
		{
			$html->OpenSpan(2);
            {
                $html->DrawFormField ( "text", "from_date", $from_date, NULL, array("class"=>"form-control date-picker", "data-date-format"=>"yyyy-mm-dd", "readonly"=>"readonly") );
            }
            $html->CloseSpan();
            $html->OpenSpan(2);
            {
                $html->DrawFormField ( "text", "to_date", $to_date, NULL, array("class"=>"form-control date-picker", "data-date-format"=>"yyyy-mm-dd", "readonly"=>"readonly") );
            }
            $html->CloseSpan();
			$html->OpenSpan(2);
			{
				$html->DrawFormField ( "text", "USER", $USER, NULL, $options );
			}
			$html->CloseSpan();
			$html->OpenSpan(2);
			{
				$html->DrawFormField ( "select", "MODULE_ID", $MODULE_ID, $ModuleNamesArr, array("class"=>"form-control", "optional"=>true) );
			}
            $html->CloseSpan();
            $html->OpenSpan(2);
			{
				$html->DrawFormField ( "select", "MODULE_OPERATION_ID", $MODULE_OPERATION_ID, $ModuleOperaionsArr, array("class"=>"form-control", "optional"=>true));
			}
            $html->CloseSpan();
            $html->OpenSpan(2);
			{
				$html->DrawFormField ( "text", "KEY_DATA", $KEY_DATA, NULL, $options );
			}
            $html->CloseSpan();
			// $html->OpenSpan(2);
			// {
			// 	$html->DrawFormField ( "text", "CRUD_OPERATION", $CRUD_OPERATION, NULL, $options );
			// }
            // $html->CloseSpan();
			$html->OpenSpan(2);
			{
				?>
				<div class="form-group">
					<label>&nbsp;</label>
					<div class="input-group">
						<span class="input-group-btn">
							<button class="btn green" type="submit" style="width:95%"><?php echo $dictionary->GetValue("filter");?></button>
						</span>
						<span class="input-group-btn">
							<button class="btn default clear_filter_users" type="button" style="width:95%"><?php echo $dictionary->GetValue("clear");?></button>
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

	$html->Datatable("example", "api/list.user.log.php?".$filter, $cols, $tableOptions);
}
$html->CloseWidget();
?>
<div id="UserContent"></div>
<script>
$( document ).ready(function() {
	$('#MODULE_ID').on('change', function () {
		MODULE_ID = $(this).val();
		FillMODULEOPERATION(MODULE_ID);
	});
});

function FillMODULEOPERATION(MODULE_ID){
	if( MODULE_ID > 0){
		FillListOptions("ui/get.module.operation.php?MODULE_ID="+MODULE_ID, "MODULE_OPERATION_ID", true);
	} else {
		FillListOptions("ui/get.module.operation.php", "MODULE_OPERATION_ID", true);
	}
}
</script>