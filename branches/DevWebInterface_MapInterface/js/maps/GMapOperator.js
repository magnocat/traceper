// Map class

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
	
	// open info window, (marker)
	// close info window
	// initialize info window, (content string), return info window 
	
	// click function
	
	//polygon cizilecek, parametre location array alacak
	

	
}





