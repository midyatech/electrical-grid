function Map(container, latitude, longitude, showCurrentMarker, editable,listenerCallback){
    this.zoom="";
    this.lat = latitude || 36.191254;
    this.lng = longitude || 44.008911;
    this.polygonColor = "#00FF00";
    this.markers = [];
    this.polygons = [];
    this.currentLat = "";
    this.currentLng = "";
    this.showCurrentMarker = showCurrentMarker || false;//this variable decides whether by default current position marker is visible
    this.mapListener = listenerCallback || false;//set this variable to listener on click event on the map itself, or set false if we don't have listener
    _this = this;//reference to object this
    editable = editable || false;
    this.drawnItems = null;
    this.draggableMarker = null;
    this.selectedMarker = null;

    //init
    var osmUrl = 'http://map.midyatech.net/osm_tiles/{z}/{x}/{y}.png';
    osmUrl = 'https://a.tile.openstreetmap.org/{z}/{x}/{y}.png';

    var osm = new L.TileLayer(osmUrl, {
        minZoom: 6,
        maxZoom: 18,
        crs: L.CRS.EPSG3857

    });
    var latlng = L.latLng(this.lat, this.lng);

    this.map = new L.map(container, {
        layers: [osm],
        center: latlng,
        zoom: 16,
        fullscreenControl: true
    });
    L.control.scale().addTo(this.map);

    // this.map.setView(latlng, 18);
    // this.map.addLayer(osm);
    if(editable){
        this.drawnItems = L.featureGroup().addTo(this.map);
        L.control.layers({
            "osm": osm.addTo(this.map),
            "google": L.tileLayer('http://www.google.cn/maps/vt?lyrs=s@189&gl=cn&x={x}&y={y}&z={z}', {
                attribution: 'google'
            })
        }, {
            'drawlayer': this.drawnItems
        }, {
            position: 'topright',
            collapsed: false
        }).addTo(this.map);
        this.map.addControl(new L.Control.Draw({
            edit: {
                featureGroup: this.drawnItems,
                poly: {
                    allowIntersection: false
                }
            },
            draw: {
                polygon: {
                    allowIntersection: false,
                    showArea: true
                }
            }
        }));
    }


    //current marker can be used to store current position in movement, or store clicked point if map click listener is set
    this.currentMarker = L.marker([this.lat, this.lng]).addTo(this.map);
    var current = new L.FeatureGroup();
    current.addLayer(this.currentMarker);
    this.map.addLayer(current);

    this.center = function (_lat, _lng) {
        this.lat = _lat;
        this.lng = _lng;
        var latlng = L.latLng(this.lat, this.lng);
        this.currentMarker.setLatLng(latlng);
        this.map.panTo(latlng);
    };

    this.showCurrent = function () {
        this.map.addLayer(current);
    };

    this.hideCurrent = function () {
        this.map.removeLayer(current);
    };

    //hide current marker if not set as visible
    if (!this.showCurrentMarker) {
        //this.currentMarker.remove();
        this.hideCurrent();
    }else{
        this.showCurrent();
    }


    this.addCustomControl = function(icon, tooltip, onClick) {
        var customControl =  L.Control.extend({
            options: {
                position: 'topleft'
            },
            onAdd: function (map) {
                var container = L.DomUtil.create('div', 'leaflet-bar leaflet-control leaflet-control-custom');
                container.style.backgroundColor = 'white';
                container.style.backgroundImage = "linear-gradient(transparent,transparent),url("+icon+")";
                container.style.backgroundRepeat = "no-repeat";
                container.style.backgroundSize = "30px 30px";
                container.style.width = '34px';
                container.style.height = '34px';
                container.border = "2px solid rgba(0,0,0,0.2)";
                container.title = tooltip;
                container.onclick = onClick;
                return container;
            }
        });
        this.map.addControl(new customControl());
    }

    this.zoom = function(_zoom){
        this.zoom = _zoom;
        this.map.setZoom(this.zoom);
    };

    this.getZoom = function () {
        return this.map.zoom;
    };

    this.addGroup = function (){
        var group = L.layerGroup().addTo(this.map);
        return group;
    };

    this.removeGroup = function (layrGroup) {
        layrGroup.clearLayers();
    };


    this.addMarker = function (_lat, _lng, placeHolder, _status, markerString, _area, point_id, divicon, onClick, extra) {
        placeHolder = placeHolder || this.map;
        _area = _area || "";
        onClick = onClick || false;
        divicon = divicon || false;

        markerTooltip = "";

        marker = L.marker([_lat, _lng]).addTo(placeHolder);
        marker.properties = {};
        marker.properties.area = _area;
        if (point_id != null) {
            marker.properties.point_id = point_id;
        }
        if (extra != null) {
            if (extra["position"] != undefined) {
                marker.properties.position = extra["position"];
            }
            if (extra["tooltip"] != undefined) {
                markerTooltip = extra["tooltip"];
            }
            if (extra["attr"] != undefined) {
                for (var attribute in extra["attr"]) {
                    //console.log(attribute)
                    marker.properties[attribute] = extra["attr"][attribute];
                }
            }
        }
        if (_status) {
            // var icon = L.icon({
            //     iconUrl: "../img/" + _status + ".png",
            //     iconSize: [12,12]
            // });
            size = 12;
            if(divicon){
                icon = L.divIcon({
                    className:'current-location-icon',
                    html:'<div class="divicon">'+point_id+'</div>',
                    iconAnchor:[0,0],
                    popupAnchor:[0,0],
                    iconUrl: "../img/" + _status + ".png",
                    iconSize: [size, size]
                });
                marker.setIcon(icon);
            } else {
                if (typeof _status != "string") {
                    size = _status["size"];
                    _status = _status["img"];
                }
                this.setMarkerIcon(marker, _status, size);
            }
        }
        if(markerString != ""){
            if (typeof markerString == "string") {
                marker.bindPopup(markerString);
            }
        }
        if (markerTooltip != "") {
            marker.bindTooltip(markerTooltip, {permanent: true, direction: 'right', className: 'tooltipClass'});
        }
        if (onClick) {
            if (onClick["event"] == "click") {
                marker.on('click', onClick["function"])
            }
        }
        this.markers.push(marker);
        return marker;
    };

    this.addMarkerD = function (_lat, _lng) {
        draggable_marker = L.marker([_lat, _lng],{draggable:'true'}).addTo(this.map);
        var icon = L.icon({
            iconUrl: "../img/dragmarker.png",
            iconSize: [128,256]
        });
        draggable_marker.setIcon(icon);
        this.markers.push(draggable_marker);
        return draggable_marker;
    };

    this.addPolygon = function (json_points, placeHolder, color, polygon_id, editable, popupStr) {
        if(json_points != ""){
            placeHolder = placeHolder || this.map;
            popupStr = (popupStr+" ") || "";
            //popupStr = (popupStr !== undefined) ? popupStr + " " : "";
            editable = editable || false;
            var polygon = L.polygon(JSON.parse(json_points), {
                color: color
            });
			polygon.properties = {};
			polygon.properties.id = polygon_id;
            if (popupStr != ""){
                polygon.bindPopup(popupStr);
            }
            polygon.addTo(placeHolder);
            this.polygons.push(polygon);
            if(editable){
                polygon.editing.enable();
            }
            return polygon;
        }else{
            return false;
        }
    };

    this.addLine = function (pointList, color, popupStr, extra) {
        onPopupOpen = false;
        popupVisibility = false;
        opacity = 0.5;
        weight = 5;
        if (extra != null) {
            if (extra["opacity"] != undefined) {
                opacity = extra["opacity"];
            }
            if (extra["weight"] != undefined) {
                weight = extra["weight"];
            }
            if (extra["popup"] != undefined) {
                popupVisibility = extra["popup"];
            }
            if (extra["popupopen"] != undefined) {
                onPopupOpen = true;
            }
        }
        polyline = new L.Polyline(pointList, {
            color: color,
            weight: weight,
            opacity: opacity
        });
        // if (popupStr != "") {
        //     polyline.bindTooltip(popupStr, { permanent: true, interactive: true }).addTo(this.map);
        // } else {
        //     polyline.addTo(this.map);
        // }

        if (popupStr != "") {
            if (popupVisibility) {
                polyline.bindTooltip(popupStr, { permanent: true, interactive: true }).addTo(this.map);
            } else {
                polyline.bindPopup(popupStr).addTo(this.map);
                //polyline.bindTooltip(popupStr, { permanent: false, interactive: true }).addTo(this.map);
            }
        } else {
            polyline.addTo(this.map);
        }

        if (onPopupOpen) {
            polyline.on('popupopen', extra["popupopen"]);
        }

        return polyline
    }

    this.addCircle = function (centerPoint, _radius, _color) {
        accuracyCircle = new L.circle(centerPoint, {radius: _radius, color: _color, opacity:0.4}).addTo(this.map);
        return accuracyCircle
    }

    this.remove = function (element, placeHolder) {
        placeHolder = placeHolder || this.map;
        placeHolder.removeLayer(element);
    };

    this.selectMarkersByattr = function(attr, val){
        val = val.toString();
        var result = this.markers.filter(function(item) {
            return item.properties[attr] === val;
        });
        if (result.length > 0) {
            this.selectedMarker = result[0];
        }
    };

    this.referenceMarkerByAttr = function (attr, val, callback) {
        for (i=0; i<this.markers.length; i++) {
            if (this.markers[i].properties[attr] === val) {
                callback(this.markers[i]);
                break;
            }
        }
    };

    this.setMarkerIcon = function (marker, color, size) {
        size = size || 12
        var icon = L.icon({
            iconUrl: "../img/" + color + ".png",
            iconSize: [size, size]
        });
        marker.setIcon(icon);
    };

    //set marker icon by attribute
    this.setMarkerIconByAttr = function (attr, val, color) {
        //first select the marker by attr
        this.selectMarkersByattr(attr, val);
        //then set icon to that marker
        this.setMarkerIcon(this.selectedMarker, color);
    };





    // this.hideMarkerAt = function(index){
    //     //this.markers.splice(index, 1);
    //     this.markers[index].setMap(null);
    // };

    // this.hideMarkers = function () {
    //     for (i = 0; i < this.markers.length; i++) {
    //         this.markers[i].setMap(null);
    //     }
    //     //this.markers.length = 0;
    // }

    // this.hidePolygon = function(polygon){
    //     polygon.setMap(null);
    // };

    // this.hidePolygonAt = function(index){
    //     //selectedShape.setOptions({ visible: false });
    //     this.polygons[index].setMap(null);
    // };

    // this.showPolygonAt = function(index){
    //     this.polygons[index].setMap(this.map);
    // };

    // this.getPolygonsByattr = function(attr, val){
    //     var result = this.polygons.filter(function(item) {
    //         return item[attr] === val;
    //     });
    //     return result;
    // };

    // this.getCurrentLat = function () {
    //     return this.currentLat;
    // };

    // this.getCurrentLng = function () {
    //     return this.currentLng;
    // };

    function jsonToCoordinates (jsonStr) {
        //converts saved json array string to google points
        var points;
        var coordinates = jsonStr;//JSON.parse(jsonStr);
        if (coordinates.constructor === Array) {
            points = coordinates.map(function (latlang) {
                return new google.maps.LatLng(latlang[0], latlang[1]);
            });
        }
        return points;
    }

    function saveCurrentData(event, callback) {
        var pos = (event.latLng).toString();
        this.map.currentLat = event.latLng.lat();
        this.map.currentLng = event.latLng.lng();
        //call the callback method that will take values up to the page level (not really a callback)
        window[callback]();
    }

}
