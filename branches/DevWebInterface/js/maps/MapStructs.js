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
