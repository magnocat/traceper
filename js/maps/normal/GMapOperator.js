//Map class

function MapOperator(lang) {

	MAP_OPERATOR = this;
	var newGeofence = null;	
	/*
	 * GMap Object
	 */
	map = null;
		
	/*
	 * this loads the gmap js file and css file
	 */
	document.write("<script type='text/javascript' src='http://maps.google.com/maps/api/js?sensor=false&language=" + lang + "'></script>");
	document.write("<link href='http://code.google.com/apis/maps/documentation/javascript/examples/default.css' rel='stylesheet' type='text/css' />");
	
	/*
	 * initalizes map, location is, the type in MapStructs.js file, the center of the map
	 */
	MAP_OPERATOR.initialize = function(location, par_country) {
		var myOptions = {
				zoom: 5,
				mapTypeId: google.maps.MapTypeId.ROADMAP
		};
		MAP_OPERATOR.map = new google.maps.Map(document.getElementById("map"), myOptions);
		
		if(par_country == null)
		{
			MAP_OPERATOR.focusOnDefaultLocation(location);
		}
		else
		{
			MAP_OPERATOR.focusOnCountry(par_country, false, location.latitude, location.longitude);
		}

//		  // Try HTML5 geolocation
//		  if(navigator.geolocation) {
//		    navigator.geolocation.getCurrentPosition(function(position) {
//		      var pos = new google.maps.LatLng(position.coords.latitude,
//		                                       position.coords.longitude);
//
//		      var infowindow = new google.maps.InfoWindow({
//		        map: MAP_OPERATOR.map,
//		        position: pos,
//		        content: 'Location found using HTML5.'
//		      });
//
//		      MAP_OPERATOR.map.setCenter(pos);
//		    }, function() {
//		      handleNoGeolocation(true);
//		    });
//		  } else {
//		    // Browser doesn't support Geolocation
//		    handleNoGeolocation(false);
//		  }		
	}
		
	MAP_OPERATOR.focusOnCountryByCoordinates = function(latitude, longitude, defaultLocation) {			
		var geocoder = new google.maps.Geocoder();			 
		var latlng = new google.maps.LatLng(latitude, longitude);
		var country = null;

		//Web uzerinden kayit olmus ve mobil uzerinden hic konum gondermemis kisilerin degeleri 0 olacagindan bu konumu dikkate alma
		if((latitude != 0) || (longitude != 0))
		{
			geocoder.geocode({'latLng':latlng},function(data,status){
				 
				if(status == google.maps.GeocoderStatus.OK){
//					alert("data[0]: " + data[0].formatted_address);
//					alert("data[1]: " + data[1].formatted_address);
//					alert("data[2]: " + data[2].formatted_address);
//					alert("data[3]: " + data[3].formatted_address);					
//					alert("data[0].address_components: " + data[0].address_components[6].long_name);
				 
					//country = data[2].formatted_address;
					country = data[0].address_components[6].long_name;
					
					MAP_OPERATOR.focusOnCountry(country, true, defaultLocation.latitude, defaultLocation.longitude);
				}
				else
				{
					alertMsg("getCountryOfCoordinates(), GeocoderStatus not OK!");
					
					MAP_OPERATOR.focusOnDefaultLocation(defaultLocation);
				}
			});			
		}
		else
		{
			alertMsg("getCountryOfCoordinates(), latitude & longitude is 0");
		}	
	}	

	MAP_OPERATOR.focusOnDefaultLocation = function(location) {
		var initialLocation = new google.maps.LatLng(location.latitude, location.longitude);
		MAP_OPERATOR.map.setCenter(initialLocation);
	}
	
	MAP_OPERATOR.focusOnCountry = function(par_country, par_bSessionToBeUpdated, par_latitude, par_longitude) {			
		//alert("Country: " + par_country);
		
		var address = par_country;
		var geocoder = new google.maps.Geocoder();
		geocoder.geocode( { 'address': address}, function(results, status) {
		    if (status == google.maps.GeocoderStatus.OK) {
		    	MAP_OPERATOR.map.setCenter(results[0].geometry.location);
		    	MAP_OPERATOR.map.fitBounds(results[0].geometry.bounds);
		    	
			    	if(par_bSessionToBeUpdated == true)
			    	{
			    		$.post('index.php?r=site/updateCountryNameSessionVar', { country:par_country });
			    		
			    		//alert("$.post - updateCountryNameSessionVar");
			    	}
		    	
		    	bCountryInfoExists = true;
		    } else {
		        //alert("Geocode was not successful for the following reason: " + status);
				var initialLocation = new google.maps.LatLng(par_latitude, par_longitude);
				MAP_OPERATOR.map.setCenter(initialLocation);
				
				//Session variable null degilse fakat ayni zamanda da alinmis bilgi gecersiz bir konum bilgisiyse
				//her seferinde hata alip default konuma yonlenmek yerine session variable null'a cekilsin ki direk default konuma gidilsin
				//$.post('index.php?r=site/nullifyCountryNameSession');
				
				$.post('index.php?r=site/nullifyCountryNameSession');
				
				bCountryInfoExists = false;
		    }
		});
	}		

	/*
	 * puts a marker at that coordinate,
	 * location is mandatory and defined in MapStructs.js,
	 * other params are optional.
	 */
//	MAP_OPERATOR.putMarker = function(location, image, visible) {
//
//		var markerImage = null;
//		if (typeof(image) != "undefined") {
//			markerImage = new google.maps.MarkerImage(image);
//		}  
//		if (typeof(visible) == "undefined") {
//			visible = true;
//		}
//		var location = new google.maps.LatLng(location.latitude, location.longitude);
//		var marker = new google.maps.Marker({
//			position: location, 
//			map: MAP_OPERATOR.map,
//			visible:visible
//		});
//		if (markerImage != null) {
//			marker.setIcon(markerImage);
//		}
//		return marker;
//	}
	
	MAP_OPERATOR.putMarker = function(location, image, visible, toBeScaled, offsetX, offsetY) {
 
	    var pinImage = null;
	    
	    if(toBeScaled == true)
	    {
		    pinImage = new google.maps.MarkerImage(
		    		image,
		    	    null, /* size is determined at runtime */
		    	    null, /* origin is 0,0 */
		    	    new google.maps.Point(offsetX, offsetY),
		    	    new google.maps.Size(33, 36) //scaledSize
		    	);	    	
	    }
	    else
	    {
//	    	pinImage = {
//	    		url: image
//	    	};
	    	
		    pinImage = new google.maps.MarkerImage(
		    		image,
	                null, /* size is determined at runtime */
	                null, /* origin is 0,0 */
	                new google.maps.Point(offsetX, offsetY));		    
	    }

		if (typeof(visible) == "undefined") {
			visible = true;
		}
		
		var location = new google.maps.LatLng(location.latitude, location.longitude);
		var marker = new google.maps.Marker({
			position: location, 
			map: MAP_OPERATOR.map,
	        icon: pinImage,
			visible:visible
		});

		return marker;
	}
	
	MAP_OPERATOR.updateMarkerImage = function(marker, image, toBeScaled) {
		 
	    var pinImage = null;
	    
	    if(toBeScaled == true)
	    {
		    pinImage = new google.maps.MarkerImage(
		    		image,
		    	    null, /* size is determined at runtime */
		    	    null, /* origin is 0,0 */
		    	    null, /* anchor is bottom center of the scaled image */
		    	    new google.maps.Size(33, 36) //scaledSize
		    	);	    	
	    }
	    else
	    {
	    	pinImage = {
	    		url: image
	    	};	    	
	    }

	    marker.setIcon(pinImage);
	}	

	/*
	 * marker is the type that returns from putMarker,
	 * visible boolean value
	 */
	MAP_OPERATOR.setMarkerVisible = function(marker, visible) {
		marker.setVisible(visible);
	}


	/*
	 * marker is the type that returns from putMarker,
	 * location is defined in MapStructs.js,
	 */
	MAP_OPERATOR.setMarkerPosition = function(marker,location) {
		var position = new google.maps.LatLng(location.latitude, location.longitude);
		marker.setPosition(position);
	}

	/*
	 * contentString is the string that is shown in infowindow
	 */
	MAP_OPERATOR.initializeInfoWindow = function(contentString) {
		if (typeof(contentString) == "undefined") {
			contentString = "<div>" 				
				+ "</div>"
				;
		} 
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

		infowindow.open(MAP_OPERATOR.map,marker);		
	}


	/*
	 *
	 */
	MAP_OPERATOR.closeInfoWindow = function(infowindow) {

		infowindow.close();		
	}

	/*
	 * infoWindow is the type that returns from initializeInfoWindow
	 * functionName is the function that is called when closed infoWindow
	 */
	MAP_OPERATOR.setInfoWindowCloseListener = function(infoWindow,functionName) {
		google.maps.event.addListener(infoWindow, 'closeclick', function(){
			functionName();
		});

	}

	/*
	 * marker is the type that returns from putMarker
	 * functionName is the function that is called when clicked marker
	 */
	MAP_OPERATOR.setMarkerClickListener = function(marker,functionName) {
		google.maps.event.addListener(marker, 'click', function(){
			functionName();
		});

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
	 * adding point to the polyline end
	 * poly is type that used
	 * loc is type that is added to the polyline
	 */
	MAP_OPERATOR.addPointToPolyline = function(poly,loc) {
		var path = poly.getPath();

		// Because path is an MVCArray, we can simply append a new coordinate
		// and it will automatically appear

		var location = new google.maps.LatLng(loc.latitude, loc.longitude);

		path.push(location);
		
		//alert("addPointToPolyline() called");
	}
	
	MAP_OPERATOR.removePolyline = function(poly) {
		poly.setMap(null);
	}	

	/*
	 * insert point to the polyline at specified index 
	 * poly is type that used
	 * loc is type that is added to the polyline
	 * index  is the specified index.
	 */
	MAP_OPERATOR.insertPointToPolyline = function(poly,loc,index) {
		var path = poly.getPath();

		// Because path is an MVCArray, we can simply append a new coordinate
		// and it will automatically appear

		var location = new google.maps.LatLng(loc.latitude, loc.longitude);

		path.insertAt(index,location);
	}

	/*
	 * setting the polyline visibility
	 * poly is type that used
	 * visible is type that is setting visibility
	 */
	MAP_OPERATOR.setPolylineVisibility = function (poly, visible){
		var opacity = 0;
		if (visible == true) {
			opacity = 1;
		}
		poly.setOptions({strokeOpacity:opacity});
	}

	/*
	 * initialize polygon
	 */
	MAP_OPERATOR.initializePolygon = function() {
		var polyOptions = {
				strokeColor: '#FF0000',
				strokeOpacity: 5.0,
				strokeWeight: 3
		}
		polygon = new google.maps.Polygon(polyOptions);
		polygon.setMap(MAP_OPERATOR.map);
		return polygon;

	}

	

	/*
	 * adding point to the geofence end
	 * geoFence is type that defined in MapStructs.js
	 * loc is type that is added to the polygon
	 */
	MAP_OPERATOR.addPointToGeoFence = function(geoFence,loc) {
		var path = geoFence.polygon.getPath();

		// Because path is an MVCArray, we can simply append a new coordinate
		// and it will automatically appear

		var location = new google.maps.LatLng(loc.latitude, loc.longitude);

		path.push(location);
	}

	/*
	 * insert point to the geofence at specified index 
	 * geoFence is type that defined in MapStructs.js
	 * loc is type that is added to the polygon
	 * index  is the specified index.
	 */
	MAP_OPERATOR.insertPointToGeoFence = function(geoFence,loc,index) {
		var path = geoFence.polygon.getPath();

		// Because path is an MVCArray, we can simply append a new coordinate
		// and it will automatically appear

		var location = new google.maps.LatLng(loc.latitude, loc.longitude);

		path.insertAt(index,location);
	}
	
	/*
	 * remove point from the geofence at specified index 
	 * geoFence is type that defined in MapStructs.js
	 * loc is type that is added to the polygon
	 * index  is the specified index.
	 */
	MAP_OPERATOR.removePointFromGeoFence = function(geoFence,index) {
		var path = geoFence.polygon.getPath();

		// Because path is an MVCArray, we can simply append a new coordinate
		// and it will automatically appear

		path.removeAt(index);
	}
	
	/*
	 * remove all points from the geofence 
	 * geoFence is type that defined in MapStructs.js
	 * loc is type that is added to the polygon
	 * index  is the specified index.
	 */
	MAP_OPERATOR.removeAllPointsFromGeoFence = function(geoFence) {
		var path = geoFence.polygon.getPath();

		// Because path is an MVCArray, we can simply append a new coordinate
		// and it will automatically appear

		path.clear();
	}
	
	/*
	 * get the point number of the geofence path 
	 * geoFence is type that defined in MapStructs.js
	 * returns the point number of geoFence path
	 */
	MAP_OPERATOR.getPointNumberOfGeoFencePath = function(geoFence) {
		// Since this Polygon only has one path, we can call getPath()
		// to return the MVCArray of LatLngs
		var vertices = geoFence.polygon.getPath();
		var pointNumber = vertices.length;
		return pointNumber;
	}


	/*
	 * get the location of the geofence path in specified index
	 * geoFence is type that defined in MapStructs.js 
	 * index  is the specified index.
	 * returns the location of geoFence path in specified index
	 */
	MAP_OPERATOR.getPointOfGeoFencePath = function(geoFence,index) {

		// Since this Polygon only has one path, we can call getPath()
		// to return the MVCArray of LatLngs
		var vertices = geoFence.polygon.getPath();
		var xy = vertices.getAt(index);
		var location = new MapStruct.Location({latitude:xy.lat(),
			longitude:xy.lng()});
		return location;		
	}

	/*
	 * set the location of the geofence path in specified index
	 * loc is type that is added to the polygon 
	 * index  is the specified index.
	 * geoFence is type that defined in MapStructs.js
	 */
	MAP_OPERATOR.setPointOfGeoFencePath = function(geoFence,loc,index) {

		// Since this Polygon only has one path, we can call getPath()
		// to return the MVCArray of LatLngs
		var vertices = geoFence.polygon.getPath();
		var location = new google.maps.LatLng(loc.latitude, loc.longitude);
		vertices.setAt(index,location);		
	}

	/*
	 * setting the geofence visibility
	 * geoFence is type that defined in MapStructs.js
	 * loc is type that is added to the polygon
	 */
	MAP_OPERATOR.setGeoFenceVisibility = function(geoFence,visible) {
		var opacity = 0.0;

		if (visible == true)
		{
			opacity=0.6;
		}

		var options = {
				fillColor: '#FF0000',
				fillOpacity: opacity,
				strokeOpacity: opacity,
		}
		geoFence.visibility=visible;
		geoFence.polygon.setOptions(options);
	}
	
	/**
	 * Extend for polygon specific implementation
	 * @return container that holds the control
	 */ 
	MAP_OPERATOR.initializeGeoFenceControl = function(geoFence,callback){		
		var returnValue = false;
		var clickPointNumber = 0;
		var removeGeoFence = false;
		MAP_OPERATOR.removeAllPointsFromGeoFence(geoFence);
		MAP_OPERATOR.newGeofence = geoFence;
		
		if (geoFence.listener == null)
		{
			geoFence.listener = google.maps.event.addListener(MAP_OPERATOR.map, 'click', function(event) {
				if (removeGeoFence)
				{
					MAP_OPERATOR.removeAllPointsFromGeoFence(geoFence);
					removeGeoFence = false;
				}
				var location = new MapStruct.Location({latitude:event.latLng.lat(),
					longitude:event.latLng.lng()}); 
				clickPointNumber++;
				//MAP_OPERATOR.setGeoFenceVisibility(geoFence,false);
				MAP_OPERATOR.addPointToGeoFence(geoFence,location);
				if (clickPointNumber==3)
				{
					MAP_OPERATOR.setGeoFenceVisibility(geoFence,true);
					MAP_OPERATOR.map.setCenter(event.latLng);
					removeGeoFence = true;
					returnValue = true;
					clickPointNumber = 0;
					callback(geoFence);
				}		   
			});
		}
		else
		{
			google.maps.event.removeListener(geoFence.listener);
			geoFence.listener = null;
		}
		return returnValue;
	};

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
	 *location is defined in MapStructs.js,
	 */
	MAP_OPERATOR.panMapTo = function(location) {
		var position = new google.maps.LatLng(location.latitude, location.longitude);
		MAP_OPERATOR.map.panTo(position);		
	}

	/*
	 * This function trigs an event specified by eventName(String) on the object parameter
	 */
	MAP_OPERATOR.trigger = function (object, eventName) {
		google.maps.event.trigger(object, eventName);
	}

	/*
	 * This function provides maximum zoom at given point
	 */
	MAP_OPERATOR.zoomMaxPoint = function(point) {

		var zoomService = new google.maps.MaxZoomService();
		var position = new google.maps.LatLng(point.latitude, point.longitude);

		zoomService.getMaxZoomAtLatLng(position,function(maxResult){

			MAP_OPERATOR.map.setCenter(position);
			MAP_OPERATOR.map.setZoom(maxResult.zoom);
		});
	}

	/*
	 * This function provides zooming at given point
	 */
	MAP_OPERATOR.zoomPoint = function(point) {

		var zoomlevel = MAP_OPERATOR.map.getZoom();
		var incZoomlevel;
		
		//alert("Zoom:"+zoomlevel);
				
//		if (zoomlevel < 6) {
//			incZoomlevel = 5;
//		}
//		else if (zoomlevel < 10) {
//			incZoomlevel = 4;
//		}
//		else if (zoomlevel < 13) {
//			incZoomlevel = 3;
//		}
//		else if (zoomlevel < 15) {
//			incZoomlevel = 2;
//		}
//		else {
//			incZoomlevel = 1;
//		}
//
//		zoomlevel += incZoomlevel;
		
		zoomlevel += 1;

		var position = new google.maps.LatLng(point.latitude, point.longitude);
		MAP_OPERATOR.map.setCenter(position);
		MAP_OPERATOR.map.setZoom(zoomlevel);
	}
	
	/*
	 * This function provides zooming out at given point
	 */
	MAP_OPERATOR.zoomOutPoint = function(point) {

		var zoomlevel = MAP_OPERATOR.map.getZoom();
		var incZoomlevel;
		
		zoomlevel -= 1;

		var position = new google.maps.LatLng(point.latitude, point.longitude);
		MAP_OPERATOR.map.setCenter(position);
		MAP_OPERATOR.map.setZoom(zoomlevel);
	}	
}

