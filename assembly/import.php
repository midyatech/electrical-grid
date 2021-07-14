<?php
include '../include/header.php';
require_once realpath(__DIR__ . '/..').'/include/settings.php';
include_once realpath(__DIR__ . '/..').'/include/checksession.php';
include_once realpath(__DIR__ . '/..').'/include/checkpermission.php';
require_once realpath(__DIR__ . '/..').'/class/Enclosure.class.php';
require_once realpath(__DIR__ . '/..').'/class/Dictionary.php';
require_once realpath(__DIR__ . '/..').'/class/Assembly.class.php';
$dictionary = new Dictionary ( $LANGUAGE );
$dictionary->GetAllDictionary ();
$Enclosure = new Enclosure( );
$Assembly = new Assembly();

$options = array("class"=>"form-control", "flow"=>"horizental", "label-align"=>"opposite");
$types = array(
    ["0"=>"meter", "1"=>"Meter"],
    ["0"=>"gateway", "1"=>"Gateway"]
);

$html->OpenDiv("row");
{
    $html->OpenSpan(12);
    {
        $html->OpenWidget("import_data", null, array('collapse' => true, 'fullscreen'=>true, "content"=>"form"));
        {
            $html->OpenForm( "code/serial.insert.php", "form2", "horizental");
            {
                $html->OpenDiv("row");
                {
                    $html->OpenSpan(4);
                    {
                        $html->DrawFormField("file", "files", null, null, $options);
                        $html->DrawFormField("select", "data_type", null, $types, $options);
                    }
                    $html->CloseSpan();
                    $html->OpenSpan(4);
                    {
                        $html->DrawFormField("text", "first_row", 1, null, $options);
                        //$html->DrawFormField("text", "columns", 1, null, $options);
                        $html->DrawFormField("text", "import_number", null, null, $options);
                    }
                    $html->CloseSpan();
                    $html->OpenSpan(4);
                    {
                        $html->HiddenField("data", "");
                        $html->DrawFormField("button", "", "upload", null, array("class"=>"btn btn-primary form-control readfile", "flow"=>"horizental", "label-align"=>"opposite"));
                    }
                    $html->CloseSpan();

                }
                $html->CloseDiv();

                $html->OpenDiv("row");
                {
                    $html->OpenSpan(12);
                    {
                        echo '<div id="upload_result">';
                        echo '<section id="result_message"></section>';
                        $html->DrawFormInput("button", "", "import", null, array("class"=>"btn btn-danger form-control upload_data", "style"=>"display:none;"));
                        echo '</div>';
                    }
                    $html->CloseSpan();
                }
                $html->CloseDiv();
            }
            $html->CloseForm();
        }
        $html->CloseWidget();
    }
    $html->CloseSpan();

}
$html->CloseDiv();
?>
<link href="../assets/layouts/layout/css/enclosure.css" rel="stylesheet" type="text/css" />
<style>

</style>

<script>
    var inputType = "string";
    var stepped = 0, rowCount = 0, errorCount = 0, firstError;
    var start, end;
    var firstRun = true;
    var maxUnparseLength = 10000;
    var output = null;

    var cols = [
        ['Model'],
        ['Serial No.']
    ];



    $(document).ready(function(){

        $(".readfile").click(function(){
            $("#result_message").html('');
            $(".upload_data").hide();

            input = $('#files').val();
            var config = buildConfig();

            if (!$('#files')[0].files.length)
            {
                alert("Please choose at least one file to parse.");
                return false;
            }

            $('#files').parse({
                config: config,
                before: function(file, inputElem)
                {
                    //console.log("Parsing file...", file);
                },
                error: function(err, file)
                {
                    console.log("ERROR:", err, file);
                    firstError = firstError || err;
                    errorCount++;

                },
                complete: function()
                {
                    //printStats("Done with all files");
                }
            });
        })

    });

    function completeFn(results)
    {
        if (results && results.errors)
        {
            if (results.errors)
            {
                errorCount = results.errors.length;
                firstError = results.errors[0];
            }
            if (results.data && results.data.length > 0)
                rowCount = results.data.length;
        }

        DisplayResult("Upload complete", true, results);
        //printStats("Parse complete");
        //console.log("    Results:", results);
    }

    function errorFn(err, file)
    {
        //console.log("ERROR:", err, file);
        DisplayResult(err, false);
    }

    function printStats(msg)
    {
        /*if (msg)
            console.log(msg);
        //console.log("       Time:", (end-start || "(Unknown; your browser does not support the Performance API)"), "ms");
        console.log("  Row count:", rowCount);
        if (stepped)
            console.log("    Stepped:", stepped);
        console.log("     Errors:", errorCount);
        if (errorCount)
            console.log("First error:", firstError);
        */
    }

    function buildConfig()
    {
        return {
            //delimiter: $('#delimiter').val(),
            //header: $('#header').prop('checked'),
            //dynamicTyping: $('#dynamicTyping').prop('checked'),
            //skipEmptyLines: $('#skipEmptyLines').prop('checked'),
            //preview: parseInt($('#preview').val() || 0),
            //step: $('#stream').prop('checked') ? stepFn : undefined,
            //encoding: $('#encoding').val(),
            //worker: $('#worker').prop('checked'),
            //comments: $('#comments').val(),
            complete: completeFn,
            error: errorFn,
            //download: inputType == "remote"
        };
    }

    function DisplayResult(msg, success, result)
    {
        if (success) {
            css = "success";
            output = ConvertResult(result);
            $("#data").val(JSON.stringify(output));
            $("#files").val('');
            msg += "<br>Records found: "+ output.length;
        } else {
            css = "danger";
        }
        $("#result_message").html('<div class="alert alert-'+css +'">'+msg+'</div>');

        if (success) {
            $(".upload_data").show();
        }
    }

    function ConvertResult(result)
    {
        first_row = $("#first_row").val();
        if (first_row < 1) {
            first_row = 1;
        }
        first_row--;


        var header_row = result.data[0];
        for (i=0; i<header_row.length; i++) {
            for (j=0; j<cols.length; j++) {
                if (cols[j][0] == header_row[i]) {
                    cols[j][1] = i;
                }
            }
        }

        //columns = ($("#columns").val()).split(",");

        output = [];
        for (i=first_row; i<result.data.length; i++) {
            var lineArray = {};
            for (j=0; j<cols.length; j++) {
                csv_name = cols[j][0];
                csv_index = cols[j][1];

                if (result.data[i][csv_index] != "" && result.data[i][csv_index] != undefined) {
                    lineArray[csv_name] = result.data[i][csv_index];
                }
            }
            output.push(lineArray);
        }
        console.log(output)
        return output;
    }
</script>
<script src="../assets/lib/papaparse/papaparse.min.js"></script>
<script src="js/enclosure.js"></script>
<?php
include '../include/footer.php';
?>
