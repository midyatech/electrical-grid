function Map(container, latitude, longitude, showCurrentMarker, listenerCallback){
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

    this.map = new google.maps.Map(document.getElementById(container), {
        zoom: 18,
        center: {lat: this.lat, lng: this.lng}
    });

    //current marker can be used to store current position in movement, or store clicked point if map click listener is set
    this.currentMarker = new google.maps.Marker({
        map: this.map,
        position: { lat: this.lat, lng: this.lng}
    });

    //hide current marker if not set as visible
    if (!this.showCurrentMarker) {
        //this.hideMarker(this.currentMarker);
        this.currentMarker.setMap(null);
    }

    this.zoom = function(_zoom){
        this.zoom = _zoom;
        this.map.setZoom(this.zoom);
    }

    this.getZoom = function () {
        return this.map.zoom;
    }

    this.center = function(_lat, _lng){
        this.lat = _lat;
        this.lng = _lng;
        latlang = new google.maps.LatLng(this.lat, this.lng)
        this.map.setCenter(latlang)
        this.currentMarker.setPosition(latlang);
    }

    this.addMarker = function (_lat, _lng, _status, markerString, _area) {
        //console.log(_lat + "," + _lng)
        _area = _area || "";
        var marker = new google.maps.Marker({
            map: this.map,
            position: { lat: parseFloat(_lat), lng: parseFloat(_lng)},
            title: "",
            area: _area,
            status: _status
        });
        if (_status) {
            img = "../img/" + _status + ".png";
            marker.setIcon(img);
        }

        markerString = markerString || "";
        //if marker has listener, attach listenter function
        if (markerString != "") {
            //set info text
            marker.contentString = '<div class="infowindow">'+markerString+'</div>';

            //set listener
            google.maps.event.addListener(marker, 'click', function() {
                //Setting content of InfoWindow
                getInfoWindow().setContent( marker.contentString );
                //Opening
                getInfoWindow().open(map,marker);
            });
        }

        //add to markers heap
        this.markers.push(marker);
        return marker;
    }

    this.addPolygon = function(json_points, color, polygon_id){
        color = color || this.polygonColor;
        polygon_id = polygon_id || (new Date()).getTime();
        var polygon = new google.maps.Polygon({
            paths: jsonToCoordinates(json_points),
            strokeColor: color,
            strokeOpacity: 0.8,
            strokeWeight: 2,
            fillColor: color,
            fillOpacity: 0.25,
            id: polygon_id
        });
        polygon.setMap(this.map);
        this.polygons.push(polygon);
        return polygon;
    }

    this.getMarkersByattr = function(attr, val){
        var result = this.markers.filter(function(item) {
            return item[attr] === val;
        });
        return result;
    }
    this.hideMarker = function(marker){
        marker.setMap(null);
    }
    this.showMarker = function(marker){
        marker.setMap(this.map);
    }
    this.hideMarkerAt = function(index){
        //this.markers.splice(index, 1);
        this.markers[index].setMap(null);
    }
    this.hideMarkers = function () {
        for (i = 0; i < this.markers.length; i++) {
            //this.markers.splice(i, 1);
            this.markers[i].setMap(null);
        }
        //this.markers.length = 0;
    }

    this.hidePolygon = function(polygon){
        polygon.setMap(null);
    }
    this.hidePolygonAt = function(index){
        //selectedShape.setOptions({ visible: false });
        this.polygons[index].setMap(null);
    }
    this.showPolygonAt = function(index){
        this.polygons[index].setMap(this.map);
    }
    this.getPolygonsByattr = function(attr, val){
        var result = this.polygons.filter(function(item) {
            return item[attr] === val;
        });
        return result;
    }

    this.getCurrentLat = function () {
        return this.currentLat;
    }
    this.getCurrentLng = function () {
        return this.currentLng;
    }

    //if click listener is set
    if (this.mapListener != false) {
        //using arrow function in callback allows the access of parent (this) object
        google.maps.event.addListener(this.map, 'click', (event) => {
            this.center(event.latLng.lat(), event.latLng.lng()) // set center to clicked point
            this.showMarker(this.currentMarker); //show marker in case it was set as hidden
            saveCurrentData(event, this.mapListener);// capture event to store the coordinates
        });
    }
    //old code without arrow function
    //     google.maps.event.addListener(this.map, 'click', function (event) {
    //         _this.center(event.latLng.lat(), event.latLng.lng()) // set center to clicked point
    //         _this.showMarker(_this.currentMarker); //show marker in case it was set as hidden
    //         saveCurrentData(event, _this.mapListener);// capture event to store the coordinates
    //     });
    // }

    var getInfoWindow = (function(){
        var _instance = null;
        return function(){
            if(_instance == null){
                _instance = new google.maps.InfoWindow({
                                maxWidth: 250,
                                maxHeight: 500
                            });
            }
            return _instance;
        };
    })();

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
