<?php
include_once realpath(__DIR__ . '/../..').'/include/settings.php';
include_once realpath(__DIR__ . '/../..').'/include/checksession.php';
require_once realpath(__DIR__ . '/../..').'/class/Survey.class.php';
require_once realpath(__DIR__ . '/../..').'/class/HtmlHelper.php';

$html = new HTML ( $LANGUAGE );
$dictionary = new Dictionary ( $LANGUAGE );
$dictionary->GetAllDictionary ();

$Survey = new Survey();


$allSurvicePoints = $Survey->GetServicePoint($filter, NULL, $startingRecord, $pageSize, $totalRecords);

if ($allSurvicePoints) {
    for ($counter=0; $counter < count( $allSurvicePoints ); $counter++) {
        $allSurvicePoints[$counter]['point_type'] = $dictionary->GetValue($allSurvicePoints[$counter]['point_type']);
    }
}
?>