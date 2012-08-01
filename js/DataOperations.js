/**
 * this function process the user past locations xml,
 * it is responsible for creating and updating markers and polylines,
 * server doesn't return the last available point of user in past locations xml
 */
function processUserPastLocationsXML (MAP, xml) {
	var userId = $(xml).find("page").attr('userId');
	var pastPoints = []; 
	var mapMarker = [];
//	var index = TRACKER.users[userId].mapMarker.length;

	$(xml).find("page").find("location").each(function(){
		var location = $(this);
		var latitude = $(location).attr('latitude');
		var longitude = $(location).attr('longitude');
		var time = $(location).find('time').text();
		var deviceId = $(location).find('deviceId').text();
		var fb_id = $(location).find('fb_id').text();

		var point = new MapStruct.Location({latitude:latitude, longitude:longitude});
		pastPoints.push(point);

		var gmarker = MAP.putMarker(point);
		var iWindow = MAP.initializeInfoWindow();
		var markerInfoWindow = new MapStruct.MapMarker({marker:gmarker, infoWindow:iWindow});


		MAP.setMarkerClickListener(gmarker,function (){

			var tr = TRACKER.users[userId].mapMarker.indexOf(markerInfoWindow);
			var previousGMarkerIndex = tr + 1; // it is reverse because 
			var nextGMarkerIndex = tr - 1;    // as index decreases, the current point gets closer
			// attention similar function is used in 
			// processXML function				
			var content =
				"<div>" 
				+ "<b>" + TRACKER.users[userId].realname + "</b> " 
				+ TRACKER.langOperator.wasHere 
				+ '<br/>' + TRACKER.langOperator.time + ": " + time
				+ '<br/>' + TRACKER.langOperator.deviceId + ": " + deviceId
				+ "</div>"
				+ '<ul class="sf-menu"> '
				+ "<li>"
				+'<a class="infoWinOperations" href="javascript:TRACKER.showPointGMarkerInfoWin('+ tr +','+ previousGMarkerIndex +','+ userId +')">'
				+ TRACKER.langOperator.previousPoint 
				+'</a>'
				+ "</li>"
				+ "<li>"
				+"<a href='#' class='infoWinOperations'>" 
				+ TRACKER.langOperator.operations
				+"</a>"
				+"<ul>"
				+"<li>"
				+'<a class="infoWinOperations" href="javascript:TRACKER.zoomPoint('+ latitude +','+ longitude +')">'
				+ TRACKER.langOperator.zoom 
				+'</a>' 		
				+"</li>"
				+"<li>"
				+'<a class="infoWinOperations" href="javascript:TRACKER.zoomMaxPoint('+ latitude +','+ longitude +')">'
				+ TRACKER.langOperator.zoomMax
				+'</a>'
				+"</li>"
				+"<li>"
				+'<a class="infoWinOperations" href="javascript:TRACKER.clearTraceLines('+ userId +')">'
				+ TRACKER.langOperator.clearTraceLines
				+'</a>'
				+"</li>"
				+"</ul>"
				+ "</li>"
				+ "<li>"
				+'<a class="infoWinOperations" href="javascript:TRACKER.showPointGMarkerInfoWin('+ tr +',' + nextGMarkerIndex +','+ userId +')">'
				+ TRACKER.langOperator.nextPoint 
				+'</a>'
				+ "</li>"
				+"</ul>";
			MAP.setContentOfInfoWindow(TRACKER.users[userId].mapMarker[tr].infoWindow,content);			
			MAP.openInfoWindow(TRACKER.users[userId].mapMarker[tr].infoWindow, TRACKER.users[userId].mapMarker[tr].marker);
		});

//		index++;
		mapMarker.push(markerInfoWindow)
	});

	if (typeof TRACKER.users[userId].polyline == "undefined") 
	{
		var firstPoint = new MapStruct.Location({latitude:TRACKER.users[userId].latitude, longitude:TRACKER.users[userId].longitude});
		TRACKER.users[userId].polyline = MAP.initializePolyline();
		MAP.addPointToPolyline(TRACKER.users[userId].polyline,firstPoint);
	}
	
	var len = pastPoints.length;
	var i;
	for (i = 0; i < len; i++){
		MAP.addPointToPolyline(TRACKER.users[userId].polyline,pastPoints[i]);
	}
	
	var tmp = TRACKER.users[userId].mapMarker;		
	TRACKER.users[userId].mapMarker = tmp.concat(mapMarker);
}

/**
 * this function process users arrayy returned when actions are search user, get user list, update list,
 * updated list...
 */	
function processUsers(MAP, users) {
	
	$.each(users, function(index, value)
	{
		var userId = value.user;
		var isFriend =  1;
		var realname = value.realname;
		var latitude = value.latitude;
		var longitude = value.longitude;
		var locationCalculatedTime = value.calculatedTime
		var status_message = value.status_message;
		var fb_id = value.fb_id;
		var dataArrivedTime = value.time;
		var message = value.message;
		var deviceId = value.deviceId;
		var location = new MapStruct.Location({latitude:latitude, longitude:longitude});
		var visible = false;
		if (isFriend == "1") {
			visible = true;
		}
		
		if (typeof TRACKER.users[userId] == "undefined") 
		{		
			if(fb_id != 0){
				var userMarker = MAP.putMarker(location, "https://graph.facebook.com/"+ fb_id + "/picture?type=square", visible);
			}else{
				var userMarker = MAP.putMarker(location, "images/person.png", visible);
			}
				
			var markerInfo= new MapStruct.MapMarker({marker:userMarker});
			
			TRACKER.users[userId] = new TRACKER.User( {//username:username,
				realname:realname,
				latitude:latitude,
				longitude:longitude,
				friendshipStatus:isFriend,
				time:time,
				message:message,
				status_message:status_message,
				deviceId:deviceId,
				mapMarker:new Array(markerInfo),
				locationCalculatedTime:locationCalculatedTime
			});
			var content =  '<div>'														   
				+ '<br/>' + TRACKER.users[userId].realname  
				+ '<br/>' + TRACKER.users[userId].time
				+ '<br/>' + TRACKER.users[userId].latitude + ", " + TRACKER.users[userId].longitude

				+'</div>'
				+'<div>'
				+ '<ul class="sf-menu"> '
				+ '<li>'+'<a class="infoWinOperations" href="javascript:TRACKER.showPointGMarkerInfoWin('+0+','+1+','+ userId +')">'
				+ TRACKER.langOperator.previousPoint 
				+'</a>'+ '</li>'
				+ '<li>'+ '<a class="infoWinOperations" href="#">'
				+ TRACKER.langOperator.operations
				+'</a>'
				+'<ul>' + '<li>'+'<a class="infoWinOperations" href="javascript:TRACKER.zoomPoint('+ TRACKER.users[userId].latitude +','+ TRACKER.users[userId].longitude +')">'
				+ TRACKER.langOperator.zoom
				+'</a>'+ '</li>'
				+ '<li>'+'<a class="infoWinOperations" href="javascript:TRACKER.zoomMaxPoint('+ TRACKER.users[userId].latitude +','+ TRACKER.users[userId].longitude +')">'
				+ TRACKER.langOperator.zoomMax
				+'</a>'+'</li>'
				+'</ul>'
				+'</li>'
				+ '</ul>'
				+ '</div>';

			TRACKER.users[userId].mapMarker[0].infoWindow = MAP.initializeInfoWindow(content);
			
			MAP.setMarkerClickListener(TRACKER.users[userId].mapMarker[0].marker,function (){
				MAP.openInfoWindow(TRACKER.users[userId].mapMarker[0].infoWindow, TRACKER.users[userId].mapMarker[0].marker);	
			});
			MAP.setMarkerVisible(TRACKER.users[userId].mapMarker[0].marker,true);						
			
			//TODO: kullanıcının pencresi açıkken konum bilgisi güncellediğinde
			//pencerenin yeni konumda da açık olmasının sağlanması

		}
		else
		{
			var time = dataArrivedTime;
			var deviceId = deviceId;
			MAP.setMarkerPosition(TRACKER.users[userId].mapMarker[0].marker,location);

			if (isFriend == "1" && TRACKER.users[userId].latitude == "" && TRACKER.users[userId].longitude == "")
			{
				// if they have just become friend, there are no latitude and longitude data 
				// so this statement will run and we update latitude and longitude
				MAP.setMarkerVisible(TRACKER.users[userId].mapMarker[0].marker,true);						
			}

			if ((TRACKER.users[userId].latitude != latitude ||
					TRACKER.users[userId].longitude != longitude) &&
					typeof TRACKER.users[userId].polyline != "undefined")
			{
				//these "if" is for creating new gmarker when user polyline is already drawed
				var userMarker = MAP.putMarker(location, "images/person.png", true);					
				var iWindow = MAP.initializeInfoWindow();
				var markerInfoWindow = new TRACKER.mapMarker({marker:userMarker, infoWindow:iWindow});
				
				MAP.insertPointToPolyline(TRACKER.users[userId].polyline,location,0);
				
				var oldlatitude = TRACKER.users[userId].latitude;
				var oldlongitude = TRACKER.users[userId].longitude;

				MAP.setMarkerClickListener(userMarker,function (){
					// attention similar function is used in 
					// processUserPastLocationsXML function
					var tr = TRACKER.users[userId].mapMarker.indexOf(markerInfoWindow);
					var previousGMarkerIndex = tr + 1; // it is reverse because 
					var nextGMarkerIndex = tr - 1;    // as index decreases, the current point gets closer

					var infoWindow = MAP.initializeInfoWindow(
							getPastPointInfoContent(userId, time, deviceId, previousGMarkerIndex, oldlatitude, oldlongitude, nextGMarkerIndex));
					MAP.openInfoWindow(infoWindow, userMarker);	
				});

				TRACKER.users[userId].mapMarker.splice(1,0, markerInfoWindow);					

				if (TRACKER.traceLineDrawedUserId != userId) {
					// if traceline is not visible, hide the marker
					MAP.setMarkerVisible(userMarker,false)						
				}


			}
			
			TRACKER.users[userId].latitude = latitude;
			TRACKER.users[userId].longitude = longitude;
			TRACKER.users[userId].time = time;
			TRACKER.users[userId].locationCalculatedTime = locationCalculatedTime;
			TRACKER.users[userId].deviceId = deviceId;
			TRACKER.users[userId].friendshipStatus = isFriend;
			//TODO: kullanıcının pencresi açıkken konum bilgisi güncellediğinde
			// pencerenin yeni konumda da açık olmasının sağlanması
			/*
			var isWindowOpen = TRACKER.users[userId].infoWindowIsOpened;
			MAP.closeInfoWindow(TRACKER.closeMarkerInfoWindow(userId))

			if (isWindowOpen == true) {
				TRACKER.openMarkerInfoWindow(userId);
			}
			 */

		}

	});
}


/**
 * 
 */
function processImageXML(MAP, xml){
	var list = "";
	TRACKER.imageThumbSuffix = decodeURIComponent($(xml).find("page").attr("thumbSuffix"));
//	TRACKER.imageOrigSuffix = decodeURIComponent($(xml).find("page").attr("origSuffix"));
	$(xml).find("page").find("upload").each(function(){
		var image = $(this);
		var imageId = $(image).attr('id');
		var imageURL =  decodeURIComponent($(image).attr('url'));
		var realname = $(image).attr("byRealName");
		var userId = $(image).attr("byUserId");
		var latitude = $(image).attr('latitude');
		var longitude = $(image).attr('longitude');
		var time = $(image).attr('time');
		var rating = $(image).attr('rating');
		var description = ""; //$(image).attr('description');
		
		var location = new MapStruct.Location({latitude:latitude, longitude:longitude});
		
		if ($.inArray(imageId, TRACKER.imageIds) == -1)
		{
			TRACKER.imageIds.push(imageId);
		}
		

		if (typeof TRACKER.images[imageId] == "undefined") {
				
			image = imageURL + "&fileType=0&"+ TRACKER.imageThumbSuffix;
			var userMarker = MAP.putMarker(location, image, false);
			var iWindow = MAP.initializeInfoWindow();
			var markerInfoWindow = new MapStruct.MapMarker({marker:userMarker, infoWindow:iWindow});
			
			TRACKER.images[imageId] = new TRACKER.Img({imageId:imageId,
				imageURL:imageURL,
				userId:userId,
				realname:realname,
				latitude:latitude,
				longitude:longitude,
				time:time,
				rating:rating,
				mapMarker:markerInfoWindow,
				description:description,
			});
						
			MAP.setMarkerVisible(TRACKER.images[imageId].mapMarker.marker, true);						
			MAP.setMarkerClickListener(TRACKER.images[imageId].mapMarker.marker,function (){
				var image = new Image();

				image.src= TRACKER.images[imageId].imageURL + "&fileType=0"; // + TRACKER.imageOrigSuffix;
				$("#loading").show();
				$(image).load(function(){
					$("#loading").hide();
					
					var content = "<div class='origImageContainer'>"
						+ "<div>"
						+ "<img src='"+ image.src +"' height='"+ image.height +"' width='"+ image.width +"' class='origImage' />"
						+ "</div>"
						+ "<div>"
						+ TRACKER.images[imageId].description + "<br/>"
						+ "<a href='javascript:TRACKER.trackUser("+ TRACKER.images[imageId].userId +")' class='uploader'>" + TRACKER.images[imageId].realname + "</a>"
						+ "<br/>"
						+ TRACKER.images[imageId].time + "<br/>"
						+ TRACKER.images[imageId].latitude + ", " + TRACKER.images[imageId].longitude
						+ "</div>"
						+ '<ul class="sf-menu"> '
						+ '<li>'+'<a class="infoWinOperations" href="javascript:TRACKER.zoomPoint('+ TRACKER.images[imageId].latitude +','+ TRACKER.images[imageId].longitude +')">'
						+ TRACKER.langOperator.zoom
						+'</a>'+ '</li>'
						+ '<li>'+'<a class="infoWinOperations" href="javascript:TRACKER.zoomMaxPoint('+ TRACKER.images[imageId].latitude +','+ TRACKER.images[imageId].longitude +')">'
						+ TRACKER.langOperator.zoomMax
						+'</a>'+'</li>'
						+'<li>'+'<a href="javascript:TRACKER.showCommentWindow(1,1,null)" id="commentsWindow"> Display Comments</a>'
						+'</a>'+'</li>'
						+'</li>'
						+ '</ul>'
						+ "</div>";
					MAP.setContentOfInfoWindow(TRACKER.images[imageId].mapMarker.infoWindow,content);
					MAP.openInfoWindow(TRACKER.images[imageId].mapMarker.infoWindow, TRACKER.images[imageId].mapMarker.marker);

					MAP.setInfoWindowCloseListener(TRACKER.images[imageId].mapMarker.infoWindow, function (){
						if ($('#showPhotosOnMap').attr('checked') == false){
							MAP.setMarkerVisible(TRACKER.images[imageId].mapMarker.marker,false);
						}
					});

				});		

			});			
		}

	});
	return list;
}
//TODO: latitude longitude -> location a cevrilsin
function getPastPointInfoContent(userId, time, deviceId, previousGMarkerIndex, latitude, longitude, nextGMarkerIndex) {

	var content = "<div>" 
		+ "<b>" + TRACKER.users[userId].realname + "</b> " 
		+ TRACKER.langOperator.wasHere 
		+ '<br/>' + TRACKER.langOperator.time + ": " + time
		+ '<br/>' + TRACKER.langOperator.deviceId + ": " + deviceId
		+ "</div>"
		+ '<ul class="sf-menu"> '
		+ "<li>"
		+'<a class="infoWinOperations" href="javascript:TRACKER.showPointGMarkerInfoWin('+ tr +',' + previousGMarkerIndex +','+ userId +')">'
		+ TRACKER.langOperator.previousPoint 
		+'</a>'
		+ "</li>"
		+ "<li>"
		+"<a href='#' class='infoWinOperations'>" 
		+ TRACKER.langOperator.operations
		+"</a>"
		+"<ul>"
		+"<li>"
		+'<a class="infoWinOperations" href="javascript:TRACKER.zoomPoint('+ latitude +','+ longitude +')">'
		+ TRACKER.langOperator.zoom 
		+'</a>' 		
		+"</li>"
		+"<li>"
		+'<a class="infoWinOperations" href="javascript:TRACKER.zoomMaxPoint('+ latitude +','+ longitude +')">'
		+ TRACKER.langOperator.zoomMax
		+'</a>'
		+"</li>"
		+"<li>"
		+'<a class="infoWinOperations" href="javascript:TRACKER.clearTraceLines('+ userId +')">'
		+ TRACKER.langOperator.clearTraceLines
		+'</a>'
		+"</li>"
		+"</ul>"
		+ "</li>"
		+ "<li>"
		+'<a class="infoWinOperations" href="javascript:TRACKER.showPointGMarkerInfoWin('+ tr +',' + nextGMarkerIndex +','+ userId +')">'
		+ TRACKER.langOperator.nextPoint 
		+'</a>'
		+ "</li>"
		+"</ul>";

	return content;
}
