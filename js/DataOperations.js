/**
 * this function process the user past locations xml,
 * it is responsible for creating and updating markers and polylines,
 * server doesn't return the last available point of user in past locations xml
 */

function processUserPastLocations(MAP, locations, userId){
	var pastPoints = []; 
	var mapMarker = [];
//	var index = TRACKER.users[userId].mapMarker.length;

	$.each(locations, function(key, value){
		//var location = $(this);
		var latitude = value.latitude;
		var longitude = value.longitude;
		var altitude = value.altitude;
		var time = value.time;
		var deviceId = value.deviceId;
		var userType = value.userType;

		var point = new MapStruct.Location({latitude:latitude, longitude:longitude});
		pastPoints.push(point);

		var gmarker = MAP.putMarker(point);
		var iWindow = MAP.initializeInfoWindow();
		var markerInfoWindow = new MapStruct.MapMarker({marker:gmarker, infoWindow:iWindow});


		MAP.setMarkerClickListener(gmarker,function (){

			var tr = TRACKER.users[userId].mapMarker.indexOf(markerInfoWindow);
			var previousGMarkerIndex = tr + 1; // it is reverse because 
			var nextGMarkerIndex = tr - 1;    // as index decreases, the current point gets closer
			
			var deviceIdInfo = "";
			
			if(userType == 1/*GPS Device*/)
			{
				deviceIdInfo = TRACKER.langOperator.deviceId + ": " + deviceId;
			}
			
			// attention similar function is used in 
			// processXML function				
			var content =
				"<div>" 
				+ "<b>" + TRACKER.users[userId].realname + "</b> " 
				+ TRACKER.langOperator.wasHere 
				+ '<br/>' + TRACKER.langOperator.time + ": " + time
				+ '<br/>' + latitude + ", " + longitude
				//+ (userType == 1/*GPS Device*/)?'<br/>' + TRACKER.langOperator.deviceId + ": " + deviceId:""
				+ deviceIdInfo
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
			TRACKER.users[userId].infoWindowIsOpened = true;
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
 * this function process users array returned when actions are search user, get user list, update list,
 * updated list...
 */	
function processUsers(MAP, users, deletedFriendId) {
	
	//alert("processUsers(), start - TRACKER.users.length:" + TRACKER.users.length);
	//alert('processUsers() called');
	
	//Default value implementation in JS
	//deletedFriendId = typeof deletedFriendId !== 'undefined' ? deletedFriendId : null;
		
	//if(deletedFriendId != null)
	if(typeof deletedFriendId !== 'undefined')
	{
		MAP.setMarkerVisible(TRACKER.users[deletedFriendId].mapMarker[0].marker, false);
		//alert("setMarkerVisible(false) for deletedFriendId:" + deletedFriendId);
		
		TRACKER.users.splice(deletedFriendId, 1);			
	}	

	$.each(users, function(index, value)
	{
		var userId = value.user;
		var isFriend =  1;
		var realname = value.realname;
		var latitude = value.latitude;
		var longitude = value.longitude;
		var address = value.address;
		var locationCalculatedTime = value.calculatedTime
		var status_message = value.status_message;
		var fb_id = value.fb_id;
		var dataArrivedTime = value.time;
		var message = value.message;
		var deviceId = value.deviceId;
		var userType = value.userType;
		var location = new MapStruct.Location({latitude:latitude, longitude:longitude});
		var visible = false;

		if (isFriend == "1") {
			visible = true;
		}

		if (typeof TRACKER.users[userId] == "undefined") 
		{		
			var personPhoto;
			if(fb_id != 0){
				personPhoto = "https://graph.facebook.com/"+ fb_id + "/picture?type=square";
				var userMarker = MAP.putMarker(location, "https://graph.facebook.com/"+ fb_id + "/picture?type=square", visible);
			}else{
				personPhoto = "images/Friend.png";
				var userMarker = MAP.putMarker(location, "images/person.png", visible);
			}
	
			var markerInfo= new MapStruct.MapMarker({marker:userMarker});
			
			TRACKER.users[userId] = new TRACKER.User( {//username:username,
				realname:realname,
				latitude:latitude,
				longitude:longitude,
				address:address,
				friendshipStatus:isFriend,
				//time:time,
				time:dataArrivedTime,
				message:message,
				status_message:status_message,
				deviceId:deviceId,
				userType:userType,
				mapMarker:new Array(markerInfo),
				locationCalculatedTime:locationCalculatedTime
			});
						
			var content = '<div style="width:200px; height:180px;">'
				+ '<div><div style="display:inline-block;vertical-align:middle;"><img src="'+ personPhoto +'"/></div><div style="display:inline-block;vertical-align:middle;padding-left:5px;"><b><font size="5">' + TRACKER.users[userId].realname + '</font></b></div></div>'  
				+ '</br>'
				+ '<div>' + TRACKER.users[userId].time + '</div>'
				+ '<div>' + TRACKER.users[userId].latitude + ", " + TRACKER.users[userId].longitude + '</div>'
				+ '<div>' + TRACKER.users[userId].address + '</div>'
				+ '<div>' + '<a class="infoWinOperations" href="javascript:TRACKER.showPointGMarkerInfoWin('+1+','+2+','+ userId +')">'+ TRACKER.langOperator.previousPoint +'</a>'  + '</div>'
				+ '</br>'				
				+ '<div style="position:absolute;bottom:5px;">'				
				+ '<div style="display:inline-block;vertical-align:middle;">' + '<a class="infoWinOperations" href="javascript:TRACKER.zoomPoint('+ TRACKER.users[userId].latitude +','+ TRACKER.users[userId].longitude +')">'+ '<img class="vtip" title="' + TRACKER.langOperator.zoom + '" src="images/Zoom-In.png"/>' + '</a>' + '</div>'				
				+ '<div style="display:inline-block;vertical-align:middle;">' + '<a class="infoWinOperations" href="javascript:TRACKER.zoomOutPoint('+ TRACKER.users[userId].latitude +','+ TRACKER.users[userId].longitude +')">'+ '<img class="vtip" title="' + TRACKER.langOperator.zoomOut + '" src="images/Zoom-Out.png"/>' + '</a>' + '</div>'
				+ '<div style="display:inline-block;vertical-align:middle;">' + '<a class="infoWinOperations" href="javascript:TRACKER.zoomMaxPoint('+ TRACKER.users[userId].latitude +','+ TRACKER.users[userId].longitude +')">'+ TRACKER.langOperator.zoomMax +'</a>' + '</div>'
				+ '</div>';
				+ '</div>';			
					
//			var content = '<div style="height:200px;">'
//				+ '<img src="images/Friend.png"/>'
//				+ '<br/>' + TRACKER.users[userId].realname  
//				+ '<br/>' + TRACKER.users[userId].time
//				+ '<br/>' + TRACKER.users[userId].latitude + ", " + TRACKER.users[userId].longitude
//
//				+'</div>'
//				+'<div>'
//				+ '<ul class="sf-menu"> '
//				//+ '<li>'+'<a class="infoWinOperations" href="javascript:TRACKER.showPointGMarkerInfoWin('+0+','+1+','+ userId +')">'
//				+ '<li>'+'<a class="infoWinOperations" href="javascript:TRACKER.showPointGMarkerInfoWin('+1+','+2+','+ userId +')">'
//				+ TRACKER.langOperator.previousPoint 
//				+'</a>'+ '</li>'
//				+ '<li>'
//				+ TRACKER.langOperator.operations
//				+'<ul>' + '<li>'+'<a class="infoWinOperations" href="javascript:TRACKER.zoomPoint('+ TRACKER.users[userId].latitude +','+ TRACKER.users[userId].longitude +')">'
//				+ TRACKER.langOperator.zoom
//				+'</a>'+ '</li>'
//				+ '<li>'+'<a class="infoWinOperations" href="javascript:TRACKER.zoomMaxPoint('+ TRACKER.users[userId].latitude +','+ TRACKER.users[userId].longitude +')">'
//				+ TRACKER.langOperator.zoomMax
//				+'</a>'+'</li>'
//				
//				+ '<li>'+'<a class="infoWinOperations" href="javascript:TRACKER.zoomOutPoint('+ TRACKER.users[userId].latitude +','+ TRACKER.users[userId].longitude +')">'
//				+ TRACKER.langOperator.zoomOut
//				+'</a>'+'</li>'				
//				
//				+'</ul>'
//				+'</li>'
//				+ '</ul>'
//				+ '</div>';
				
			TRACKER.users[userId].mapMarker[0].infoWindow = MAP.initializeInfoWindow(content);
			
			MAP.setMarkerClickListener(TRACKER.users[userId].mapMarker[0].marker,function (){
				MAP.openInfoWindow(TRACKER.users[userId].mapMarker[0].infoWindow, TRACKER.users[userId].mapMarker[0].marker);
				TRACKER.users[userId].infoWindowIsOpened = true;
			});

			//MAP.setMarkerVisible(TRACKER.users[userId].mapMarker[0].marker, TRACKER.showUsersOnTheMap);
			MAP.setMarkerVisible(TRACKER.users[userId].mapMarker[0].marker, (userId==TRACKER.userId)?true:TRACKER.showUsersOnTheMap);						
			
			//TODO: kullanıcının pencresi açıkken konum bilgisi güncellediğinde
			//pencerenin yeni konumda da açık olmasının sağlanması

		}
		else
		{						
			var time = dataArrivedTime;
			var deviceId = deviceId;
			var userType = userType;
			MAP.setMarkerPosition(TRACKER.users[userId].mapMarker[0].marker,location);

			if (isFriend == "1" && TRACKER.users[userId].latitude == "" && TRACKER.users[userId].longitude == "")
			{
				// if they have just become friend, there are no latitude and longitude data 
				// so this statement will run and we update latitude and longitude
				//MAP.setMarkerVisible(TRACKER.users[userId].mapMarker[0].marker, TRACKER.showUsersOnTheMap);
				MAP.setMarkerVisible(TRACKER.users[userId].mapMarker[0].marker, (userId==TRACKER.userId)?true:TRACKER.showUsersOnTheMap);						
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
							getPastPointInfoContent(userId, time, deviceId, userType, previousGMarkerIndex, oldlatitude, oldlongitude, nextGMarkerIndex));
					MAP.openInfoWindow(infoWindow, userMarker);
					TRACKER.users[userId].infoWindowIsOpened = true;
				});

				TRACKER.users[userId].mapMarker.splice(1,0, markerInfoWindow);					

				if (TRACKER.traceLineDrawedUserId != userId) {
					// if traceline is not visible, hide the marker
					MAP.setMarkerVisible(userMarker, false)						
				}
			}
			
			if ((TRACKER.users[userId].mapMarker[0].infoWindow != null) && ((TRACKER.users[userId].latitude != latitude) ||
					(TRACKER.users[userId].longitude != longitude) || (TRACKER.users[userId].time != time))){
				var isWindowOpen = TRACKER.users[userId].infoWindowIsOpened;

//				var content =  '<div>'														   
//				+ '<br/>' + TRACKER.users[userId].realname  
//				+ '<br/>' + time //TRACKER.users[userId].time
//				+ '<br/>' + latitude + ", " + longitude
//
//				+'</div>'
//				+'<div>'
//				+ '<ul class="sf-menu"> '
//				//+ '<li>'+'<a class="infoWinOperations" href="javascript:TRACKER.showPointGMarkerInfoWin('+0+','+1+','+ userId +')">'
//				+ '<li>'+'<a class="infoWinOperations" href="javascript:TRACKER.showPointGMarkerInfoWin('+1+','+2+','+ userId +')">'
//				+ TRACKER.langOperator.previousPoint 
//				+'</a>'+ '</li>'
//				+ '<li>'
//				+ TRACKER.langOperator.operations
//				+'<ul>' + '<li>'+'<a class="infoWinOperations" href="javascript:TRACKER.zoomPoint('+ latitude +','+ longitude +')">'
//				+ TRACKER.langOperator.zoom
//				+'</a>'+ '</li>'
//				+ '<li>'+'<a class="infoWinOperations" href="javascript:TRACKER.zoomMaxPoint('+ latitude +','+ longitude +')">'
//				+ TRACKER.langOperator.zoomMax
//				+'</a>'+'</li>'
//				
//				+ '<li>'+'<a class="infoWinOperations" href="javascript:TRACKER.zoomOutPoint('+ latitude +','+ longitude +')">'
//				+ TRACKER.langOperator.zoomOut
//				+'</a>'+'</li>'				
//				
//				+'</ul>'
//				+'</li>'
//				+ '</ul>'
//				+ '</div>';
								
				var content = '<div style="width:200px; height:180px;">'
					+ '<div><div style="display:inline-block;vertical-align:middle;"><img src="images/Friend.png"/></div><div style="display:inline-block;vertical-align:middle;padding-left:5px;"><b><font size="5">' + TRACKER.users[userId].realname + '</font></b></div></div>'  
					+ '</br>'
					+ '<div>' + time + '</div>'
					+ '<div>' + latitude + ", " + longitude + '</div>'
					+ '<div>' + address + '</div>'
					+ '<div>' + '<a class="infoWinOperations" href="javascript:TRACKER.showPointGMarkerInfoWin('+1+','+2+','+ userId +')">'+ TRACKER.langOperator.previousPoint +'</a>'  + '</div>'
					+ '</br>'				
					+ '<div style="position:absolute;bottom:5px;">'				
					+ '<div style="display:inline-block;vertical-align:middle;">' + '<a class="infoWinOperations" href="javascript:TRACKER.zoomPoint('+ latitude +','+ longitude +')">'+ '<img class="vtip" title="' + TRACKER.langOperator.zoom + '" src="images/Zoom-In.png"/>' + '</a>' + '</div>'				
					+ '<div style="display:inline-block;vertical-align:middle;">' + '<a class="infoWinOperations" href="javascript:TRACKER.zoomOutPoint('+ latitude +','+ longitude +')">'+ '<img class="vtip" title="' + TRACKER.langOperator.zoomOut + '" src="images/Zoom-Out.png"/>' + '</a>' + '</div>'
					+ '<div style="display:inline-block;vertical-align:middle;">' + '<a class="infoWinOperations" href="javascript:TRACKER.zoomMaxPoint('+ latitude +','+ longitude +')">'+ TRACKER.langOperator.zoomMax +'</a>' + '</div>'
					+ '</div>';
					+ '</div>';				
								
				if (isWindowOpen == true) {
					MAP.closeInfoWindow(TRACKER.users[userId].mapMarker[0].infoWindow)
					MAP.setContentOfInfoWindow(TRACKER.users[userId].mapMarker[0].infoWindow,content);			
					MAP.openInfoWindow(TRACKER.users[userId].mapMarker[0].infoWindow, TRACKER.users[userId].mapMarker[0].marker);
				}
			}
						
			TRACKER.users[userId].latitude = latitude;
			TRACKER.users[userId].longitude = longitude;
			TRACKER.users[userId].time = time;
			TRACKER.users[userId].locationCalculatedTime = locationCalculatedTime;
			TRACKER.users[userId].deviceId = deviceId;
			TRACKER.users[userId].userType = userType;
			TRACKER.users[userId].friendshipStatus = isFriend;	
		}
	});
	
	//alert("processUsers(), stop - TRACKER.users.length:" + TRACKER.users.length);
	
	for (key in TRACKER.users) {
	    if (TRACKER.users.hasOwnProperty(key)  &&        // These are explained
	        /^0$|^[1-9]\d*$/.test(key) &&    // and then hidden
	        key <= 4294967294                // away below
	        ) {
			
	    	//alert("processUsers(), TRACKER.users[" + key + "]: false");
			
	    	//MAP.setMarkerVisible(TRACKER.users[key].mapMarker[0].marker, TRACKER.showUsersOnTheMap);
	    	MAP.setMarkerVisible(TRACKER.users[key].mapMarker[0].marker, (key==TRACKER.userId)?true:TRACKER.showUsersOnTheMap);
			
	    	//if(TRACKER.users[key].infoWindowIsOpened && (TRACKER.showUsersOnTheMap == false))
	    	if(TRACKER.users[key].infoWindowIsOpened && (TRACKER.showUsersOnTheMap == false) && (key != TRACKER.userId))
			{
				MAP.closeInfoWindow(TRACKER.users[key].mapMarker[0].infoWindow)
			}
	    }
	}	
}

/**
 * 
 */
function processImageXML(MAP, xml){
	var list = "";
	TRACKER.imageThumbSuffix = decodeURIComponent($(xml).find("page").attr("thumbSuffix"));
//	TRACKER.imageOrigSuffix = decodeURIComponent($(xml).find("page").attr("origSuffix"));
	
	//alert("processImageXML(), start - TRACKER.images.length:" + TRACKER.images.length);
	
	$(xml).find("page").find("upload").each(function(){
		
		//alert("processImageXML(), find-each");
		
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
			
			//alert("images["+ imageId +"] is undefined!");
				
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
			
			//alert("MAP.setMarkerVisible(true)");
						
			MAP.setMarkerVisible(TRACKER.images[imageId].mapMarker.marker, TRACKER.showImagesOnTheMap); //ADNAN					
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
					
					
					//var content = "<video id='my_video_2' class='video-js vjs-default-skin' controls preload='auto' width='320' height='264'><source src='http://localhost/traceper/branches/DevWebInterface/upload/oceans-clip.mp4' type='video/mp4'></video>";
					
					//var content = "<div> Deneme </div>";
					
					//var content = '<video id="my_video_2" class="video-js vjs-default-skin" controls preload="auto" width="320" height="264" data-setup="{}"><source src="http://localhost/traceper/branches/DevWebInterface/upload/oceans-clip.mp4" type="video/mp4"></video>'; 

					MAP.setContentOfInfoWindow(TRACKER.images[imageId].mapMarker.infoWindow,content);									
					
					MAP.openInfoWindow(TRACKER.images[imageId].mapMarker.infoWindow, TRACKER.images[imageId].mapMarker.marker);					
					TRACKER.images[imageId].infoWindowIsOpened = true;	
					
					MAP.setInfoWindowCloseListener(TRACKER.images[imageId].mapMarker.infoWindow, function (){
						if ($('#showPhotosOnMap').attr('checked') == false){
							MAP.setMarkerVisible(TRACKER.images[imageId].mapMarker.marker,false);
						}				
					});
				});				
			});		
		}
		else
		{
			//alert("images["+ imageId +"] is already defined");
			
			//alert("TRACKER.showImagesOnTheMap: " + TRACKER.showImagesOnTheMap);
			
			MAP.setMarkerVisible(TRACKER.images[imageId].mapMarker.marker, TRACKER.showImagesOnTheMap); //ADNAN		
		}

	});
	
//	for (var i = 0; i < TRACKER.images.length; i++) {
//		MAP.setMarkerVisible(TRACKER.images[imageId].mapMarker.marker, TRACKER.showImagesOnTheMap); //ADNAN	
//	}
	
	for (key in TRACKER.images) {
	    if (TRACKER.images.hasOwnProperty(key)  &&        // These are explained
	        /^0$|^[1-9]\d*$/.test(key) &&    // and then hidden
	        key <= 4294967294                // away below
	        ) {
			//alert("processUsers(), TRACKER.images[" + key + "]: false");

			MAP.setMarkerVisible(TRACKER.images[key].mapMarker.marker, TRACKER.showImagesOnTheMap); //ADNAN	
			
			if(TRACKER.images[key].infoWindowIsOpened && (TRACKER.showImagesOnTheMap == false))
			{
				MAP.closeInfoWindow(TRACKER.images[key].mapMarker.infoWindow)
			}			
	    }
	}	
	
	//alert("processImageXML(), stop - TRACKER.images.length:" + TRACKER.images.length);
	
	return list;
}
//TODO: latitude longitude -> location a cevrilsin
function getPastPointInfoContent(userId, time, deviceId, userType, previousGMarkerIndex, latitude, longitude, nextGMarkerIndex) {

	var deviceIdInfo = "";
	
	if(userType == 1/*GPS Device*/)
	{
		deviceIdInfo = TRACKER.langOperator.deviceId + ": " + deviceId;
	}
	
	var content = "<div>" 
		+ "<b>" + TRACKER.users[userId].realname + "</b> " 
		+ TRACKER.langOperator.wasHere 
		+ '<br/>' + TRACKER.langOperator.time + ": " + time
		+ '<br/>' + latitude + ", " + longitude
		//+ (userType == 1/*GPS Device*/)?'<br/>' + TRACKER.langOperator.deviceId + ": " + deviceId:""
		+ deviceIdInfo
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
