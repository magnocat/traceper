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
	 * location is mandatory and defined in MapStructs.js,
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
	 * check the location point on geofence or not according to Ray Casting algorithm.
	 * geoFence is type that defined in MapStructs.js
	 * loc is type that is added to the polygon
	 */
	MAP_OPERATOR.isGeoFenceContainsLocation = function(geoFence,location) {

		// Raycast point in polygon method
		var numPoints = MAP_OPERATOR.getPointNumberOfGeoFencePath(geoFence);
		var inPoly = false;
		var i;
		var j = numPoints-1;
		
		for(var i=0; i < numPoints; i++) { 
			
			var vertex1 = MAP_OPERATOR.getPointOfGeoFencePath(geoFence,i);
			var vertex2 = MAP_OPERATOR.getPointOfGeoFencePath(geoFence,j);
			if (vertex1.longitude < location.longitude && vertex2.longitude >= location.longitude || vertex2.longitude < location.longitude && vertex1.longitude >= location.longitude)	 {
				if (vertex1.latitude + (location.longitude - vertex1.longitude) / (vertex2.longitude - vertex1.longitude) * (vertex2.latitude - vertex1.latitude) < location.latitude) {
					inPoly = !inPoly;
				}
			}
			j = i;
		}
		return inPoly;
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
		geoFence.polygon.setOptions(options);
	}

	/**
	 * Extend for polygon specific implementation
	 * @return container that holds the control
	 */ 
	MAP_OPERATOR.initializeGeoFenceControl = function(geoFence){		
		var container = document.createElement('DIV');
		container.id = "mymaps-control-polygon";

		var opts = {
				button_opts:{
			img_up_url:'http://www.google.com/intl/en_us/mapfiles/ms/t/Bpu.png',
			img_down_url:'http://www.google.com/intl/en_us/mapfiles/ms/t/Bpd.png',
			name:'polygon',
			tooltip:'Draw a shape'
		},
		position:{
			controlPosition:[245,3]
		},
		tooltip:{
			anchor:[-30,-8],
			cursor_on:"", //only for overriding default digitizing cursor
			cursor_off:"",
			titles:{
			start:"Click to start drawing a shape",
			middle:"Click to continue drawing a shape",
			end:"Click a vertex once, or double click on the map to end this shape"
		},
		callback:null      
		},
		newGeometryOptions: { 
			strokeColor:"#000000",
			strokeWeight:3,
			strokeOpacity:0.25,
			fillColor:"#0000FF",
			fillOpacity:0.45,
			opts:{
			clickable:true
		}
		},
		geometryListenerOpts:{
			mouseoverEditingEnabled:true,
			infoWindowHtmlEnabled:true,
			mouseoverHighlightingEnabled:true,
			infoWindowTabsEnabled:false,
			/**
			 * Optional function to load up additional information from html template for tabs
			 * If the original infoWindowHtml content is desired, add it as the first tab in the array.
			 */
			/*
			assembleInfoWindowTabs:function(){
			me.infoWindowTabs.push(new GInfoWindowTab("Geometry Controls", me.infoWindowHtml));
			me.infoWindowTabs.push(new GInfoWindowTab("Example Tab", me.zuper.infoWindowHtmlTemplates["infoWindowTabContent1"]));

		}  */    
		},
		multiEdit:false, //allows for digitzing multiple geometries, useful for points, should polys support it too?
		htmlTemplateParams:{},
		cssId:"emmc-polygon",
		optionalGeometryListeners:null,
		autoSave:false     
		};

		var button = {};
		button.opts = opts.button_opts;
		var button_img = document.createElement('img');

		button_img.style.cursor = button.opts.buttonCursor || 'pointer';
		button_img.width = button.opts.buttonWidth || '33';
		button_img.height = button.opts.buttonHeight || '33';
		button_img.border = button.opts.buttonBorder || '0';
		button_img.src = button.opts.img_up_url;
		button_img.title = button.opts.tooltip;
		button_img.style.zIndex = 1009999900;

		button.img = button_img;

		var clickPointNumber = 0;
		var removeGeoFence = false;

		//Button toggle. First click turns it on (and other buttons off), triggers bound events. Second click turns it off
		google.maps.event.addDomListener(button.img, "click", function() {
			if(button.img.getAttribute("src") === button.opts.img_up_url){

				google.maps.event.addListener(MAP_OPERATOR.map, 'click', function(event) {
					if (removeGeoFence)
					{
						MAP_OPERATOR.removeAllPointsFromGeoFence(geoFence);
						removeGeoFence = false;
					}
					var location = new MapStruct.Location({latitude:event.latLng.lat(),
						longitude:event.latLng.lng()}); 
					clickPointNumber++;
					MAP_OPERATOR.setGeoFenceVisibility(geoFence,false);
					MAP_OPERATOR.addPointToGeoFence(geoFence,location);
					if (clickPointNumber==3)
					{
						MAP_OPERATOR.setGeoFenceVisibility(geoFence,true);
						MAP_OPERATOR.map.setCenter(event.latLng);
						removeGeoFence = true;
						clickPointNumber = 0;
						
						/*
						var initialLoc = new MapStruct.Location({latitude:39.504041,
							  longitude:35.024414}); 
						var sonuc=MAP_OPERATOR.isGeoFenceContainsLocation(geoFence,initialLoc);
					    alert(sonuc);
					    */
					}
				});				
			} else {
				MAP_OPERATOR.setGeoFenceVisibility(geoFence,false);
				clickPointNumber = 0;
				geoFence.polygon.getPath().clear();
			}    
		});  

		/*
		buttons_[opts.controlName] = button;
		stopDigitizingFuncs_[opts.controlName] = opts.stopDigitizing;
		 */

		container.appendChild(button.img);
		//MAP_OPERATOR.map.getDiv().appendChild(container);
		$('#friendsList').append(container);

		/*
		me.runInitFunctions();
		 */
		return container;

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
		if (zoomlevel < 6) {
			incZoomlevel = 5;
		}
		else if (zoomlevel < 10) {
			incZoomlevel = 4;
		}
		else if (zoomlevel < 13) {
			incZoomlevel = 3;
		}
		else if (zoomlevel < 15) {
			incZoomlevel = 2;
		}
		else {
			incZoomlevel = 1;
		}

		zoomlevel += incZoomlevel;

		var position = new google.maps.LatLng(point.latitude, point.longitude);
		MAP_OPERATOR.map.setCenter(position);
		MAP_OPERATOR.map.setZoom(zoomlevel);
	}
}

