//get user area
function GetAreaPolygon(area_id, callback) {
    polygon = [];
    $.ajax({
        type: "POST",
        url: '../api/get.area.php',
        data: ({ "area_id": area_id }),
        dataType: "json"
    }).done(function(msg) {
        if (msg != "") {
            polygon = msg;
        }
        if (callback) {
            callback(polygon);
        }
        return polygon;
    });
}

function GetAreaUnits(area_id, callback) {
    polygon = [];
    $.ajax({
        type: "POST",
        url: '../api/get.area.units.php',
        data: ({ "area_id": area_id }),
        dataType: "json"
    }).done(function(msg) {
        if (msg != "") {
            polygon = msg;
        }
        if (callback) {
            callback(polygon);
        }
        return polygon;
    });
}

function PlotPins(data) {
    for (var i in data) {
        var unit = data[i];
        var lat = unit["latitude"];
        var long = unit["longitude"];
        var status = unit["status"];

        map.addMarker(lat, long, color)
    }
}

function GetStatusColor(status) {
    color = "";
    switch (status) {
        case 1:
            color = "blue";
            break;
        case 2:
            color = "red";
            break;
        case 3:
            color = "yellow";
            break;
        case -1:
            color = "green";
            break;
        default:
            color = "green";
    }
    return color;
}

function GetMessages(callback) {
    polygon = [];
    $.ajax({
        type: "POST",
        url: '../api/get.messages.php',
        //data: ({ "area_id": area_id }),
        dataType: "json"
    }).done(function(msg) {
        if (msg != "") {
            polygon = msg;
        }
        if (callback) {
            callback(polygon);
        }
        return polygon;
    });
}


//OSM
function SwitchCoordinates(str) {
    arr = JSON.parse(str)
    for (i = 0; i < arr.length; i++) {
        lat = arr[i][0];
        lng = arr[i][1];
        arr[i] = [lng, lat];
    }
    return JSON.stringify(arr);
}

function GeneratePopUpString(id, name){
    return '<div class="mapPopup">' + name + '<br/><a href="map_area.php?id=' + id + '">Edit <i class="fa fa-pencil"></i></a></div>';
}

// function SetIcon(map, point_id, imageUrl)
// {
//     map.selectMarkersByattr("point_id", point_id);

//     var icon = L.icon({
//             iconUrl: imageUrl,
//             iconSize: [12,12]
//         })
//     marker.setIcon(icon)
// }