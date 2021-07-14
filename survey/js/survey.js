$(document).ready(function() {
    //hide map by default
    //MapHeight();

    //Wizard
    $(".next1").click(function() {
        type_id = $(this).data("type");
        type_label = $(this).data("label");
        $("#type_id").val(type_id);
        $("#type_label").val(type_label);
        $("#confirm_type").text(type_label);
        if (type_id == 1 || type_id == 2 || type_id == 3) {
            //pole or twitested cable
            MoveNext();
            setTimeout(function () {
                $("#single_phase").select();
            }, 500);

        }else if(type_id == 4){
            //transformer
            GoTo("step4");
        }
    });

    $(".next3").click(function() {
        accuracy = $(this).data("accuracy");
        accuracy_label = $(this).data("accuracy")+"%";
        $("#confirm_single_phase").text($("#single_phase").val());
        $("#confirm_three_phase").text($("#three_phase").val());
        lat = $("#latitude").val();
        long = $("#longitude").val();
        $("#confrim_latitude").text(lat);
        $("#confrim_longitude").text(long);
        $("#accuracy").val(accuracy);
        $("#confirm_accuracy").text(accuracy_label);
        map.center(lat, long);
        GoTo("step5");
        //MapHeight(true);//show map in pole
    });

    $(".next4").click(function() {
        station_id = $("#step4 #station_id").val();
        feeder_id = $("step4 #feeder_id").val();
        capacity_id = $("step4 #capacity_id").val();
        transformer_number_val = $("step4 #transformer_number").val();

        transformer_text = station_id + "/" + feeder_id + "/" + capacity_id + "/" + transformer_number_val;
        //console.log("transformer_text" + transformer_text)
        lat = $("#latitude").val();
        long = $("#longitude").val();
        gps_accuracy = $("#gps_accuracy").val();

        $("#confirm_transformer").text(transformer_text);
        $("#confirm_latitude_2").text(lat);
        $("#confirm_longitude_2").text(long);
        $("#confirm_gps_accuracy_2").text(gps_accuracy);

        map.center(lat, long);

        if ( station_id != "" && feeder_id != "" && capacity_id != "" && transformer_number_val != "") {
            GoTo("step5");
            //MapHeight(true);//show map in transformer
        } else {

            if( station_id == "" ) {
                $("#station_id").closest('.form-group').addClass('has-error');
            } else {
                $("#station_id").closest('.form-group').removeClass('has-error');
            }

            if( feeder_id == "" ) {
                $("#feeder_id").closest('.form-group').addClass('has-error');
            } else {
                $("#feeder_id").closest('.form-group').removeClass('has-error');
            }

            if( capacity_id == "" ) {
                $("#capacity_id").closest('.form-group').addClass('has-error');
            } else {
                $("#capacity_id").closest('.form-group').removeClass('has-error');
            }

            if( transformer_number_val == "" ) {
                $("#transformer_number").closest('.form-group').addClass('has-error');
            } else {
                $("#transformer_number").closest('.form-group').removeClass('has-error');
            }

        }

    });


    $(".next").click(function() {
        single_phase = $("#single_phase").val();
        three_phase = $("#three_phase").val();
        if (type_id == 1 || type_id == 2 || type_id == 3) {
            //pole or twitested cable
            if (single_phase != "" && three_phase != "" && single_phase >=0 && three_phase >=0) {
                MoveNext();
            } else {
                if( single_phase == "" ||single_phase<0) {
                    $("#single_phase").closest('.form-group').addClass('has-error');
                }else {
                    $("#single_phase").closest('.form-group').removeClass('has-error');
                }
                if( three_phase == "" ||three_phase <0) {
                    $("#three_phase").closest('.form-group').addClass('has-error');
                }else {
                    $("#three_phase").closest('.form-group').removeClass('has-error');
                }
            }
        }else{
            MoveNext();
        }
    });

    $(".next5").click(function() {
        type_id = $("#type_id").val();
        if(type_id == 1 || type_id == 2 || type_id == 3){
            GoTo("step6");
        } else if(type_id == 4){
            GoTo("step7");
        }
    });

    $(".prev").click(function() {
        $(".nav-tabs > .active")
            .prev("li")
            .find("a")
            .trigger("click");
    });

    $(".prev4").click(function() {
        GoTo("step1");
    });
    $(".prev6").click(function() {
        GoTo("step5");
    });

    $(".prev5").click(function() {
        type_id = $("#type_id").val();
        if(type_id == 1 || type_id == 2 || type_id == 3){
            GoTo("step3");
        } else if(type_id == 4){
            GoTo("step4");
        }
    });

    $(".prev7").click(function() {
        GoTo("step5");
    });

    $("body").on("click", ".quick_edit", function() {
        point_id = $(this).data("id");
        OpenModal("ui/quick.edit.php", {"point_id": point_id});
    });

    $("body").on("click", ".point_full_details", function() {
        point_id = $(this).data("full_details");
        OpenModal("ui/point.full.detail.php", {"point_id": point_id}, "modal-full");
    });

    $("body").on("click", ".add_gateway", function() {
        //$("#GatewaySummary").html("");
        point_id = $(this).data("point_id");
        gateway = $(this).data("gateway");
        transformer_id = $(this).data("transformer_id");
        $element = $(this);
        /*
        if( gateway == 1 ){
            color = "red";
            confirm_message = "<i style='padding: 10px 120px; color:green' class='fa fa-plus-circle fa-3x'></i><br/>Are you sure add gateway in this point";
            new_gateway = 0;
        } else {
            color = "blue";
            confirm_message = "<i style='padding: 10px 120px; color:red' class='fa fa-times fa-3x'></i><br/>Are you sure delete gateway in this point";
            new_gateway = 1;
        }
        */
        //OpenConfirmModal([confirm_message], function() {
            //console.log(gateway);
            var request = $.ajax({
                url: "code/service.point.edit.gateway.code.php?point_id=" + point_id + "&gateway=" + gateway,
                type: "GET",
                processData: false,
                contentType: false
            });
            request.done(function(msg) {
                //console.log("done")
                if (GetLocalStatus(true, msg)) {
                    //success
                    // $element.data("gateway", new_gateway);
                    // ChangePointIcon(point_id, color);
                    // $("#GatewaySummary").load("ui/service.point.gateway.summary.php", {"transformer_id":transformer_id});
                    /*
                    url = (window.location.href).replace("#","")+"&center="+point_id;
                    console.log(url);
                    window.location.href  = url;
                    */
                    // url = (window.location.href).replace("#","");
                    // url = url.replace( new RegExp( "\\b" + "center" + "=[^&;]+[&;]?", "gi" ), "" );
                    // // remove any leftover crud
                    // url = url.replace( /[&;]$/, "" );

                    url = (window.location.href).replace("#","")
                    if (url.indexOf("&center") > 0) {
                        url = url.substring(0, url.indexOf("&center"));
                    }
                    url = url + "&center="+point_id
                    window.location.href  = url;
                } else {
                    //something wrong happened
                }
                HideLoader();
            });
            request.fail(function(jqXHR, textStatus) {
                //error
                ShowToastr("error", jqXHR.statusText);
                HideLoader();
            });
        //});
    });

    /*
    $("body").on("click", ".save_service_point", function() {
        var form = $(this).closest('service_point').get(0);
        var url = "code/service.point.add.code.php";
        msgArray ="";

        var formData = new FormData($("#service_point")[0]);
        var action = url;
        console.log(url);
        var request = $.ajax({
            url: action,
            type: 'POST',
            processData: false,
            contentType: false,
            data: formData
        });
        request.done(function(msg) {
            //show message anyway
            if (GetLocalStatus(true, msg)) {
                //reload
                window.location.assign("../survey/add_survey.php");
            } else {
                //error, stay here
            }
            //HideLoader();
        });

        request.fail(function(jqXHR, textStatus) {
            HideLoader();
            ShowToastr("error", jqXHR.statusText);
        });
    });
    */

    $("body").on("click", ".edit_service_point_location", function() {
        var form = $(this).closest('service_point').get(0);
        var url = "code/service.point.edit.location.code.php";
        msgArray ="";

        var formData = new FormData($("#service_point")[0]);
        var action = url;
        console.log(url);
        var request = $.ajax({
            url: action,
            type: 'POST',
            processData: false,
            contentType: false,
            data: formData
        });
        request.done(function(msg) {
            //show message anyway
            if (GetLocalStatus(true, msg)) {
                //reload
                window.location.assign("../survey/add_survey.php");
            } else {
                //error, stay here
            }
            //HideLoader();
        });

        request.fail(function(jqXHR, textStatus) {
            HideLoader();
            ShowToastr("error", jqXHR.statusText);
        });
    });

    /*
    $("body").on("click", ".edit_service_point", function() {
        var form = $(this).closest('service_point').get(0);
        var url = "code/service.point.add.code.php";
        msgArray ="";

        var formData = new FormData($("#service_point")[0]);
        var action = url;
        console.log(url);
        var request = $.ajax({
            url: action,
            type: 'POST',
            processData: false,
            contentType: false,
            data: formData
        });
        request.done(function(msg) {
            //show message anyway
            if (GetLocalStatus(true, msg)) {
                //reload
                window.location.assign("../survey/add_survey.php");
            } else {
                //error, stay here
            }
            //HideLoader();
        });

        request.fail(function(jqXHR, textStatus) {
            HideLoader();
            ShowToastr("error", jqXHR.statusText);
        });
    });
    */

    $("body").on("click", ".survey_delete", function() {
        var id = $(this).data("id");
        OpenConfirmModal(null, function() {
            var request = $.ajax({
                url: "code/service.point.delete.code.php?point_id=" + id,
                type: "GET",
                processData: false,
                contentType: false
            });
            request.done(function(msg) {
                //console.log("done")
                if (GetLocalStatus(true, msg)) {
                    //success
                    window.location.assign("survey_list.php");
                } else {
                    //something wrong happened
                    SetLocalStatus(msg);
                    GetLocalStatus();
                }
                HideLoader();
            });
            request.fail(function(jqXHR, textStatus) {
                //error
                ShowToastr("error", jqXHR.statusText);
                HideLoader();
            });
        });
    });

    $("body").on("click", ".open_area_tree", function() {
        OpenTreeModal("area");
    });

    $("body").on("click", ".open_area_tree1", function() {
        OpenTreeModal("area.1");
    });

    $("body").on("click", ".open_area_tree2", function() {
        OpenTreeModal("area.2");
    });

    $("body").on("click", ".open_area_tree3", function() {
        OpenTreeModal("area.3");
    });

    $('#myModal').on('click', '.tree.AREA_TREE li > span', function(e) {
        $currentSpan = $(this);
        OpenNode($currentSpan, "area");
    });

    $('#myModal').on('click', ' .select_node_button', function() {
        $node_id = $(this).data("id");
        $node_text = $(this).data("text");
        SelectNode($node_id, "#area", $node_text, "#area_text", "myModal");
    });

    $('#myModal').on('click', '.tree_1 .select_node_button', function() {
        $node_id = $(this).data("id");
        $node_text = $(this).data("text");
        SelectNode($node_id, "#area1", $node_text, "#area_text1", "myModal");
    });

    $('#myModal').on('click', '.tree_2 .select_node_button', function() {
        $node_id = $(this).data("id");
        $node_text = $(this).data("text");
        SelectNode($node_id, "#area2", $node_text, "#area_text2", "myModal");
    });

    $('#myModal').on('click', '.tree_3 .select_node_button', function() {
        $node_id = $(this).data("id");
        $node_text = $(this).data("text");
        SelectNode($node_id, "#area3", $node_text, "#area_text3", "myModal");
    });

    //Listen to fill area tree node CHECKBOX
    $('#treeCheck').on('click', '.AREA_TREE li > span', function (e) {
        $currentSpan = $(this);
        OpenNodeCheck($currentSpan, "area");
    });

});

function MoveNext(){
    //MapHeight();
    $(".nav-tabs > .active")
        .next("li")
        .find("a")
        .trigger("click");
}

function GoTo(tab_id) {
    //MapHeight();
    var marker_id;
    lat = $("#latitude").val();
    long = $("#longitude").val();
    if(tab_id=="step5"){
        //
        ShowDraggable();
    } else {
        HideDraggable();
    }
    $('#wizard_tabs a[href="#'+tab_id+'"]').tab("show");
}

function ShowDraggable() {
    //if(androidInterval != undefined){
    if (typeof androidInterval !== 'undefined') {

        window.clearInterval(androidInterval);
    }
    map.hideCurrent();
    map.draggableMarker = map.addMarkerD(lat, long);

    map.draggableMarker.on('dragend', function (event) {
        var marker2 = event.target;
        var position = marker2.getLatLng();
        //marker2.setLatLng(new L.LatLng(position.lat, position.lng), { draggable: 'true' });
        $("#latitude").val(position.lat);
        $("#longitude").val(position.lng);
        $("#confirm_latitude").text($("#latitude").val());
        $("#confirm_longitude").text($("#longitude").val());
        $("#confirm_accuracy").text($("#accuracy").val());
    });
}

function HideDraggable(){
    if ((map.draggableMarker != null && map.draggableMarker != undefined)) {
        map.remove(map.draggableMarker);

        // marker_id="vian";
        //map.addMarker(lat, long, '', '', '', '', marker_id);
    }
    map.center($("#latitude").val(), $("#longitude").val());
    map.showCurrent();
}

function MapHeight(showMap) {
    showMap = showMap || false;
    if (showMap) {
        $("#map").css("height", mapHeight);
    }else{
        $("#map").css("height", 0);
    }
}

/**/
//Open Tree Dialog
function OpenTreeModal($tree_name){
    $("#myModal .modal-dialog .modal-title").html('');
    $("#myModal .modal-body").html("");
    $("#myModal .modal-body").load("../tree/ui/tree.php", {"tree_name":$tree_name});
    SetModalTitle("myModal", "","icon-ok-sign");
    $("#myModal").modal();
    //fix relative position when closing
    $("#myModal").css( "position", "" );
}

//Open Dialog
function OpenModal($address, $data, parameter){
    parameter = parameter || "modal-lg";

    $("#myModal .modal-dialog .modal-title").html('');
    $("#myModal .modal-body").html("");
    $("#myModal .modal-body").load($address, $data);
    SetModalTitle("myModal", "","icon-ok-sign");
    $("#myModal").modal();

    $("#myModal .modal-dialog").removeClass("modal-full");
    $("#myModal .modal-dialog").addClass(parameter);
    $("#myModal").modal({ backdrop: 'static', keyboard: false });

    //fix relative position when closing
    $("#myModal").css( "position", "" );
}

