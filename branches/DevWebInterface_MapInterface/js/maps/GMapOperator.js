//Map class

function MapOperator() {

	MAP_OPERATOR = this;
	/*
	 * GMap Object
	 */
	map = null;
	/*
	 * this loads the gmap js file and css file
	 */
	document.write("<script type='text/javascript' src='http://maps.google.com/maps/api/js?sensor=false'></script>");
	document.write("<link href='http://code.google.com/apis/maps/documentation/javascript/examples/default.css' rel='stylesheet' type='text/css' />");

	/*
	 * initalizes map, location is, the type in MapStructs.js file, the center of the map
	 */
	MAP_OPERATOR.initialize = function(location) {
		var myOptions = {
				zoom: 5,
				mapTypeId: google.maps.MapTypeId.ROADMAP
		};
		MAP_OPERATOR.map = new google.maps.Map(document.getElementById("map"), myOptions);
		var initialLocation = new google.maps.LatLng(location.latitude, location.longitude);
		MAP_OPERATOR.map.setCenter(initialLocation);
	};


	/*
	 * puts a marker at that coordinate,
	 * latitude and longitude is mandatory,
	 * other params are optional.
	 */
	MAP_OPERATOR.putMarker = function(location, image, visible) {

		var markerImage = null;
		if (typeof(image) != "undefined") {
			markerImage = new google.maps.MarkerImage(image);
		}  
		if (typeof(visible) == "undefined") {
			visible = true;
		}
		var location = new google.maps.LatLng(location.latitude, location.longitude);
		var marker = new google.maps.Marker({
			position: location, 
			map: MAP_OPERATOR.map,
			visible:visible
		});
		if (markerImage != null) {
			marker.setIcon(markerImage);
		}
		return marker;
	}

	/*
	 * marker is the type that returns from putMarker,
	 * visible boolean value
	 */
	MAP_OPERATOR.setMarkerVisible = function(marker, visible) {
		marker.setVisible(visible);
	}

	/*
	 * contentString is the string that is shown in infowindow
	 */
	MAP_OPERATOR.initializeInfoWindow = function(contentString) {
		var infowindow = new google.maps.InfoWindow({
			content: contentString
		});
		return infowindow;
	}

	/*
	 * infowindow is the type that returns from initializeInfoWindow
	 * marker is the type where open the infowindow
	 * there is no close function for infowindow
	 */
	MAP_OPERATOR.openInfoWindow = function(infowindow,marker) {
		infowindow.open(map,marker);		
	}

	/*
	 * infowindow is the type that returns from initializeInfoWindow
	 * contentString is the string that updates the content of infowindow
	 */
	MAP_OPERATOR.setContentOfInfoWindow = function(infowindow,contentString) {
		infowindow.setContent(contentString);		
	}

	/*
	 * initialize polyline
	 */
	MAP_OPERATOR.initializePolyline = function() {
		var polyOptions = {
				strokeColor: '#FF0000',
				strokeOpacity: 5.0,
				strokeWeight: 3
		}
		poly = new google.maps.Polyline(polyOptions);
		poly.setMap(MAP_OPERATOR.map);
		return poly;

	}

	/*
	 * updates polyline
	 * poly is type that used
	 * loc is type that is added to the polyline
	 */
	MAP_OPERATOR.updatePolyline = function(poly,loc) {
		var path = poly.getPath();

		// Because path is an MVCArray, we can simply append a new coordinate
		// and it will automatically appear

		var locations = new google.maps.LatLng(loc.latitude, loc.longitude);

		path.push(locations);
	}

	/*
	 * clickFunction
	 * functionName is the type that is called when clicked
	 */
	MAP_OPERATOR.clickFunction = function(functionName) {
		google.maps.event.addListener(MAP_OPERATOR.map, 'click', function(event) {			
			functionName(event);			
		});

	}

	/*
	MAP_OPERATOR.drawPolygon = function(locationArray) {
		var triangleCoords = new google.maps.LatLng(locationArray.latitude, locationArray.longitude);

		// Construct the polygon
	    bermudaTriangle = new google.maps.Polygon({
	      paths: triangleCoords,
	      strokeColor: "#FF0000",
	      strokeOpacity: 0.8,
	      strokeWeight: 2,
	      fillColor: "#FF0000",
	      fillOpacity: 0.35
	    });

	   bermudaTriangle.setMap(map);		
	}
	 */

	// open info window, (marker)
	// close info window
	// initialize info window, (content string), return info window 

	// click function

	//polygon cizilecek, parametre location array alacak



}





