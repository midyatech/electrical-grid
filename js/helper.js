var ignore_progress = false;
$(document).ready(function() {

    $(".calendar_control, .date-picker").datepicker({ autoclose: true });

});


function ShowLoader($parent) {
    //$("#loader").clone().show().appendTo($parent.css("position", "relative"));
    document.getElementById('load').style.visibility = "visible";
}

function HideLoader() {
    document.getElementById('load').style.visibility = "hidden";
    //$("#loader").hide();
}


function HilightListItem(list_selector, $listitem) {
    $(list_selector).css("background-color", "");
    $listitem.css("background-color", "#d9eef2");
    /* "# edf7f9"*/
}


function getParameterByName(name) {
    name = name.replace(/[\[]/, "\\\[").replace(/[\]]/, "\\\]");
    var regex = new RegExp("[\\?&]" + name + "=([^&#]*)"),
        results = regex.exec(location.search);
    return results == null ? "" : decodeURIComponent(results[1].replace(/\+/g, " "));
}

function FormatNumber(x) {
    return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
}

function Translate(keyword, callback, params) {
    translation = keyword;
    //optional parameter sent to callback method
    if (typeof params === 'undefined') {
        optionalArg = null;
    }

    if (translation.charAt(0) == "{") {
        //special case, we don't translate keyword starting with "{"
        if (callback) {
            if (params == null) {
                callback(translation);
            } else {
                callback(translation, params);
            }
        }
        return translation;
    } else {
        $.ajax({
            type: "POST",
            url: '../api/translate.php',
            data: ({ "keyword": keyword }),
            dataType: "html"
        }).done(function(msg) {

            if (msg != "") {
                translation = msg;
            }
            if (callback) {
                if (params == null) {
                    callback(translation);
                } else {
                    callback(translation, params);
                }
            }
        }).fail(function() {
            if (callback) {
                if (params == null) {
                    callback(translation);
                } else {
                    callback(translation, params);
                }
            }
        });
    }
    return translation;

}

function OpenConfirmModal(msg, callback) {
    $('#confirmModal').unbind('hide.bs.modal');
    $('#confirmModal .modal-body').html('');
    msgStr = "";
    translatedMsg = [];
    var counter = 0;
    if (msg != null && msg.length > 0) {
        for (i = 0; i < msg.length; i++) {
            var index = i;

            Translate(msg[i], function(translated, _index) {
                counter += 1
                if (translated.charAt(0) == "{") {
                    translated = translated.slice(1, -1);
                }
                translatedMsg[_index] = translated;
                //when we make sure we covered all message, show translation
                if (counter == msg.length) {
                    msgStr = translatedMsg.join(" ");
                    $('#confirmModal .modal-body').html(msgStr)
                }
            }, index);
        }
    }

    $('#confirmModal').on('hide.bs.modal', function() {
        if ($('#confirmModal').data("result") == 1) {
            //proceed with callback only if confirm is true
            callback();
        }
    });
    $("#confirmModal").modal();
}

$("#confirmModal").on("click", "#ok", function() {
    $('#confirmModal').data("result", 1);
    $('#confirmModal').modal('hide')
});
$("#confirmModal").on("click", "#cancel", function() {
    $('#confirmModal').data("result", 0);
    $('#confirmModal').modal('hide')
});


function FillListOptions(url, select, optional) {
    $("#" + select).find('option').remove();
    $.getJSON(url, function(result) {
        optional = optional || false;
        if (optional) {
            $("#" + select).append('<option value="">All</option>');
        }
        $.each(result, function(i, field) {
            $("#" + select).append('<option value="' + field[0] + '">' + field[1] + '</option>');
        });
    });
}

function SetModalTitle($modal, headerHtml) {
    if (headerHtml == "" || headerHtml == undefined) {
        $("#" + $modal + " .modal-dialog .modal-title").html('');
    } else {
        $("#" + $modal + " .modal-dialog .modal-title").html(headerHtml);
    }
}

function UpdateClientNotificationCounter() {
    $(".top-menu").load("../client_include/notification_bar.php");
}

function UpdateUserApplicationNotificationCounter() {
    $("#header_notification_bar").load("../include/application.notification.php");
}

function UpdateUserTraceNotificationCounter() {
    $("#header_inbox_bar").load("../include/doc.in.notification.php");
}



function ShowNotification(msg) {
    Translate(msg, function(message) {
        null
        $.bootstrapGrowl(message, {
            ele: "body",
            type: "info",
            offset: {
                from: "top",
                amount: parseInt(100)
            },
            align: "left",
            width: parseInt(300),
            delay: 10000,
            allow_dismiss: true,
            stackup_spacing: 10
        })
    });
}

function SetLocalStatus(result) {
    localStorage.setItem("message", JSON.stringify(result)); //this will convert json to string;
}

function GetLocalStatus(show_all, message) {
    //default value is false
    show_all = show_all || false;

    var state = ""; //localStorage.getItem("state");

    //in case message was not provided, look it up in localstorage
    var json_string = JSON.stringify(message) || localStorage.getItem("message"); // json array []

    //console.log(">> "+json_string)
    if (json_string != "" && json_string != null & json_string != '""') {
        json_array = JSON.parse(json_string); //convert json strng to array
        //console.log(json_string);
        all_good = true;
        for (var i = 0; i < json_array.length; i++) {
            var object = json_array[i];
            //console.log(object["status"]);
            toast_message = object["message"];
            toast_state = "";
            if (object["status"] == "0") {
                all_good = false;
                toast_state = "error";
            } else if (object["status"] == "1") {
                all_good = false;
                toast_state = "warning";
            } else if (object["status"] == "2") {
                toast_state = "success";
            }
            if (toast_state == "error" || toast_state == "warning" || show_all) {
                ShowToastr(toast_state, toast_message, "");
            }
            if (!message) {
                localStorage.removeItem("message");
            }
        }
        if (all_good) {
            return true;
        } else {
            return false;
        }
    } else {
        //error meesage is empty, nothing is wrong
        return true;
    }
}

function ShowToastr(state, message, title) {
    //check if state is valid
    if (state == "error" || state == "warning" || state == "success") {
        //message = Translate(message);
        Translate(message, function(message) {
            //var toastIndex = toastCount++;
            toastr.options = {
                "closeButton": true,
                "debug": false,
                "newestOnTop": false,
                "progressBar": false,
                "positionClass": "toast-top-full-width",
                "preventDuplicates": false,
                "onclick": null,
                "showDuration": "300",
                "hideDuration": "1000",
                "timeOut": "10000",
                "extendedTimeOut": "10000",
                "showEasing": "swing",
                "hideEasing": "linear",
                "showMethod": "fadeIn",
                "hideMethod": "fadeOut",
                "tapToDismiss": false
            }
            var $toast = toastr[state](message, title); // Wire up an event handler to a button in the toast, if it exists
            if (typeof $toast === 'undefined') {
                console.log("toaster undefined")
                return;
            }
        });
    }
}