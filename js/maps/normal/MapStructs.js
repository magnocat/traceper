//TYPES FOR MAP Interface

function MapStruct() { 

	MAP_STRUCTS = this;

}

MapStruct.Location = function (){
	var latitude;
	var longitude;
	var altitude;

	for (var n in arguments[0]) { 
		this[n] = arguments[0][n]; 
	}	
}

MapStruct.MapMarker = function(){
	var marker;
	var infoWindow;
	
	for (var n in arguments[0]) { 
		this[n] = arguments[0][n]; 
	}
}

MapStruct.GeoFence = function(){
	var geoFenceId;
	var listener;
	var polygon;
	var visibility;
	
	for (var n in arguments[0]) { 
		this[n] = arguments[0][n]; 
	}
}
