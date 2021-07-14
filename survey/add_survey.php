<?php
// error_reporting(E_ALL);
// ini_set('display_errors', 1);
include_once realpath(__DIR__ . '/..').'/include/header.php';
include_once realpath(__DIR__ . '/..').'/survey/add.survey.php';
require_once '../include/footer.php';
?>
<script src="../js/map_helper.js"></script>
<link rel="stylesheet" href="../osm/leaflet.css" />
<script src="../osm/leaflet.js" ></script>

<script src="../js/OpenMap.js?v=11"></script>
<script>

function SavePoint()
{
    $("form#service_point").submit()
//     transformer_id = $("#form3 #transformer_id").val();
//     var formData = new FormData($("form#service_point")[0]);
//     var url = "code/service.point.add.code.php";
//     var request = $.ajax({
//         url: url,
//         type: 'POST',
//         processData: false,
//         contentType: false,
//         data: formData
//     });
//     request.done(function(msg) {
//         window.location.assign("add_survey.php");
//         // GetTransformer(transformer_id)
//         // $('#myModal').modal('hide');
//     });
//     request.fail(function(jqXHR, textStatus) {
//         HideLoader();
//         ShowToastr("error", jqXHR.statusText);
//     });
}
</script>