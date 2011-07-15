// Map class

function MapOperator() {
	
	MAP_OPERATOR = this;
	
	this.initializeMap = function() {
		alert("I1");
		var script = document.createElement("script");
		script.type = "text/javascript";
		script.src = "http://maps.google.com/maps/api/js?sensor=false";
		document.write("<script type='text/javascript' src='http://maps.google.com/maps/api/js?sensor=false'></script>");
		var initialLocation;
		alert("I01");
		var siberia = new google.maps.LatLng(60, 105);
		alert("I11");
		var newyork = new google.maps.LatLng(40.69847032728747, -73.9514422416687);
		var browserSupportFlag =  new Boolean();
		//var map;
		var infowindow = new google.maps.InfoWindow();
		
		// Try W3C Geolocation method (Preferred)
		if(navigator.geolocation) 
		{
			var myOptions = {
			   zoom: 12,
			   mapTypeId: google.maps.MapTypeId.ROADMAP
			};
			this.map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);
			browserSupportFlag = true;
		    navigator.geolocation.getCurrentPosition(function(position) {
		      initialLocation = new google.maps.LatLng(position.coords.latitude,position.coords.longitude);
		      contentString = "Your location found using W3C standard";
		      this.map.setCenter(initialLocation);
		      infowindow.setContent(contentString);
		      infowindow.setPosition(initialLocation);
		      infowindow.open(this.map);
		    }, function() {
		      handleNoGeolocation(browserSupportFlag);
		    });
		}
		else if (google.gears) 
		{
		    // Try Google Gears Geolocation
		    browserSupportFlag = true;
		    var geo = google.gears.factory.create('beta.geolocation');
		    geo.getCurrentPosition(function(position) {
		      initialLocation = new google.maps.LatLng(position.latitude,position.longitude);
		      contentString = "Location found using Google Gears";
		      this.map.setCenter(initialLocation);
		      infowindow.setContent(contentString);
		      infowindow.setPosition(initialLocation);
		      infowindow.open(this.map);
		    }, function() {
		      handleNoGeolocation(browserSupportFlag);
		    });
		} 
		else 
		{
		    // Browser doesn't support Geolocation
		    browserSupportFlag = false;
		    handleNoGeolocation(browserSupportFlag);
	  	}
		alert("I2");
	};
	
	this.loadMapScript = function() {
		alert("S1");
		var script = document.createElement("script");
		script.type = "text/javascript";
		//script.src = "http://maps.google.com/maps/api/js?sensor=false&callback=initializeMap";
		//script.src = "http://maps.google.com/maps/api/js?sensor=false&callback";
		this.initializeMap();
		document.body.appendChild(script);
		alert("S2");
	}
	
}

/*
function initializeMap() {	
	var initialLocation;
	var siberia = new google.maps.LatLng(60, 105);
	var newyork = new google.maps.LatLng(40.69847032728747, -73.9514422416687);
	var browserSupportFlag =  new Boolean();
	var map;
	var infowindow = new google.maps.InfoWindow();
	
	// Try W3C Geolocation method (Preferred)
	if(navigator.geolocation) 
	{
		var myOptions = {
		   zoom: 12,
		   mapTypeId: google.maps.MapTypeId.ROADMAP
		};
		map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);
		browserSupportFlag = true;
	    navigator.geolocation.getCurrentPosition(function(position) {
	      initialLocation = new google.maps.LatLng(position.coords.latitude,position.coords.longitude);
	      contentString = "Your location found using W3C standard";
	      map.setCenter(initialLocation);
	      infowindow.setContent(contentString);
	      infowindow.setPosition(initialLocation);
	      infowindow.open(map);
	    }, function() {
	      handleNoGeolocation(browserSupportFlag);
	    });
	}
	else if (google.gears) 
	{
	    // Try Google Gears Geolocation
	    browserSupportFlag = true;
	    var geo = google.gears.factory.create('beta.geolocation');
	    geo.getCurrentPosition(function(position) {
	      initialLocation = new google.maps.LatLng(position.latitude,position.longitude);
	      contentString = "Location found using Google Gears";
	      map.setCenter(initialLocation);
	      infowindow.setContent(contentString);
	      infowindow.setPosition(initialLocation);
	      infowindow.open(map);
	    }, function() {
	      handleNoGeolocation(browserSupportFlag);
	    });
	} 
	else 
	{
	    // Browser doesn't support Geolocation
	    browserSupportFlag = false;
	    handleNoGeolocation(browserSupportFlag);
  	}
		
}

function handleNoGeolocation(errorFlag) 
{
	if (errorFlag == true) 
	{
		initialLocation = newyork;
		contentString = "Error: The Geolocation service failed.";
		} 
		else 
		{
			initialLocation = siberia;
		contentString = "Error: Your browser doesn't support geolocation. Are you in Siberia?";
		}
		map.setCenter(initialLocation);
		infowindow.setContent(contentString);
		infowindow.setPosition(initialLocation);
		infowindow.open(map);
}
	  
function loadMapScript() {
  var script = document.createElement("script");
  
  /*
  alert("1");
  //window.open("http://code.google.com/apis/maps/documentation/javascript/examples/default.css");
  window.location.href="http://code.google.com/apis/maps/documentation/javascript/examples/default.css";
  alert("2");
  *//*
  
  
  script.type = "text/javascript";
  script.src = "http://maps.google.com/maps/api/js?sensor=false&callback=initializeMap";
  document.body.appendChild(script);  
}
*/

function placeMarker(location) {
	/*
	var personIcon = new GIcon(G_DEFAULT_ICON);
	personIcon.image = "images/person.png";
	personIcon.iconSize = new GSize(24,24);
	personIcon.shadow = null;
	markerOptions = { icon:personIcon };
	*/
	
  var marker = new google.maps.Marker({
      position: location, 
      map: map
  });

  map.setCenter(location);
}



