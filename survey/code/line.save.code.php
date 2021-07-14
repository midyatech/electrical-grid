<?php
//include_once realpath(__DIR__ . '/../..').'/include/checkpermission.php';
require_once realpath(__DIR__ . '/../..') . '/class/SystemMessage.php';
require_once realpath(__DIR__ . '/../..') . '/class/Survey.class.php';
require_once realpath(__DIR__ . '/../..') . '/class/Helper.php';
include_once realpath(__DIR__ . '/../..') . '/include/checksession.php';
require_once realpath(__DIR__ . '/../..') . '/include/settings.php';

$survey = new Survey();
$message = New SysetemMessage($LANGUAGE);

// $service_point_data =array();
// $service_point_data['point_id']= Helper::Post("point_id", true);
// $service_point_data['longitude']= Helper::Post("longitude", true);
// $service_point_data['latitude']= Helper::Post("latitude", true);
//$transformer_id = $_POST["transformer_id"];
$transformer_id = null;
$line = $_POST["line"];

$lineArray = json_decode($line, TRUE);
$validLine = true;
if (count($lineArray) > 1) {
	$points = array();
	//foreach ($lineArray as $point) {
	for ($i=0; $i<count($lineArray); $i++) {
		//echo "-".$lineArray[$i]."<br>";
		if ($lineArray[$i] != "") {
			$line_point_position_id = 2; //middle
			if ($i==0) {
				$line_point_position_id = 1; //start
				//get transformer
				$transformer_id = $survey->GetPointTransformer($lineArray[$i]);
			} else if ($i == count($lineArray) -1) {
				$line_point_position_id = 3; //end
			}
			$points[$i+1] = array($lineArray[$i], $line_point_position_id);
		} else {
			$validLine = false;
			break;
		}
	}

	if ($validLine && $transformer_id) {
		$result = $survey->InsertLine($transformer_id, $points);
		if ($result != false) {
			//print $result;
		} else {
			//error
			$message -> AddMessage($survey->State, $survey->Message);
		}
	} else {
		$message -> AddMessage(1, "invalid_line_points");
	}

} else {
	$message -> AddMessage(1, "too_few_points");
}
$message -> PrintJsonMessage();
?>
