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

function getContentFor(userId, imageSrc) {
	var content = 
		  '<div style="width:280px; height:180px;">'
		+ 	'<div><div style="display:inline-block;vertical-align:middle;">' + '<img src="' + imageSrc + '" width="44px" height="48px"/>' + '</div><div style="display:inline-block;vertical-align:middle;padding-left:5px;cursor:text;"><b><font size="5">' + TRACKER.users[userId].realname + '</font></b></div></div>'  
		+ 	'</br>'
		+ 	'<div style="cursor:text;">' + TRACKER.users[userId].time + ' - (' + TRACKER.users[userId].latitude + ", " + TRACKER.users[userId].longitude + ')' + '</div>'
		+ 	'<div style="cursor:text;">' + TRACKER.users[userId].address + '</div>'				
		+ 	'</br>'				
		+ 	'<div style="position:absolute;bottom:10px;">'
		+ 		'<a class="infoWinOperations med-icon-bordered-effect med-icon-effect-a" href="javascript:TRACKER.showPointGMarkerInfoWin('+1+','+2+','+ userId +')">'+ '<div class="med-icon-bordered icon-arrow-left vtip" title="' + TRACKER.langOperator.previousPoint + '"></div>' + '</a>'
		+ 		'<a class="infoWinOperations med-icon-effect med-icon-effect-a" style="margin-left:145px;" href="javascript:TRACKER.zoomPoint('+ TRACKER.users[userId].latitude +','+ TRACKER.users[userId].longitude +')">'+ '<div class="med-icon icon-zoomIn1 vtip" title="' + TRACKER.langOperator.zoom + '"></div>' + '</a>'				
		+ 		'<a class="infoWinOperations med-icon-effect med-icon-effect-a" href="javascript:TRACKER.zoomOutPoint('+ TRACKER.users[userId].latitude +','+ TRACKER.users[userId].longitude +')">'+ '<div class="med-icon icon-zoomOut1 vtip" title="' + TRACKER.langOperator.zoomOut + '"></div>' + '</a>'
		+ 		'<a class="infoWinOperations med-icon-effect med-icon-effect-a" href="javascript:TRACKER.zoomMaxPoint('+ TRACKER.users[userId].latitude +','+ TRACKER.users[userId].longitude +')">'+ '<div class="med-icon icon-zoomMax5 vtip" title="' + TRACKER.langOperator.zoomMax + '"></div>' + '</a>'
		+ 	'</div>';
		+ '</div>';
		
	return content;	
}

/**
 * this function process users array returned when actions are search user, get user list, update list,
 * updated list...
 */	
function processUsers(MAP, users, currentUser, par_updateType, deletedFriendId) {

	//alertMsg("processUsers(), start - TRACKER.users.length:" + TRACKER.users.length);
	//alertMsg('processUsers() called');
	
	//Default value implementation in JS
	//deletedFriendId = typeof deletedFriendId !== 'undefined' ? deletedFriendId : null;
		
//	//if(deletedFriendId != null)
//	if(typeof deletedFriendId !== 'undefined')
//	{
//		MAP.setMarkerVisible(TRACKER.users[deletedFriendId].mapMarker[0].marker, false);
//		//alertMsg("setMarkerVisible(false) for deletedFriendId:" + deletedFriendId);
//		
//		if(TRACKER.users[deletedFriendId].infoWindowIsOpened)
//		{
//			MAP.closeInfoWindow(TRACKER.users[deletedFriendId].mapMarker[0].infoWindow)
//			
//		}
//		
//		if(TRACKER.preUserId == deletedFriendId)
//		{
//			TRACKER.preUserId = -1;
//		}
//		
//		delete TRACKER.users[deletedFriendId];
//	}
	
	var updateType = 'all';
	
	if(typeof par_updateType !== 'undefined')
	{
		updateType = par_updateType;
		
		//alertMsg("if - updateType: " + updateType);
	}
	else
	{
		//alertMsg("else - updateType: " + updateType);
	}
		
	//alertMsg("users.length:" + users.length + " / TRACKER.users.length:" + TRACKER.users.length);
	
	var userIdArray = new Array();	
	var newFriend = false;

	$.each(users, function(index, value)
	{
		var userId = value.user;

		var isFriend =  1;
		var realname = value.realname;
		var latitude = value.latitude;
		var longitude = value.longitude;
		var address = value.address;
		var locationCalculatedTime = value.calculatedTime
		var locationSource = value.locationSource
		var status_message = value.status_message;
		var fb_id = value.fb_id;
		var profilePhotoStatus = value.profilePhotoStatus;
		var dataArrivedTime = value.time;
		var message = value.message;
		var deviceId = value.deviceId;
		var userType = value.userType;
		var location = new MapStruct.Location({latitude:latitude, longitude:longitude});
		var visible = false;
		
		var locationlessUserIdIndexInArray = locationlessUserIdArray.indexOf(userId);
		
		//Hem mobil veri gelmemis hem de IP uzerinden de konum elde edilememise kisiyi haritada gosterme
		//if(((locationCalculatedTime.indexOf(" 1970 ") != -1) || (locationCalculatedTime == ""))  && (latitude == 0.000000) && (longitude == 0.000000))
		if(locationSource == "-1")	
		{
			if(locationlessUserIdIndexInArray == -1)
			{
				locationlessUserIdArray.push(userId);
			}

			return;
		}
		else
		{
			if(locationlessUserIdIndexInArray != -1)
			{
				locationlessUserIdArray.splice(locationlessUserIdIndexInArray, 1);
			}
			
			//userIdArray.push(userId);
			
			if ((value.isVisible == "1") || (currentUserId == userId)) {
				visible = true;
				userIdArray.push(userId);
			}			
		}	

//		if (isFriend == "1") {
//			visible = true;
//		}

		var personPhotoElement;
		var timeStamp = new Date().getTime();
		
		switch(profilePhotoStatus)
		{
			case "0": //Users::NO_TRACEPER_PROFILE_PHOTO_EXISTS
			{
				if((fb_id != 0) && (typeof fb_id != "undefined")){			
					personPhotoElement = '<img src="https://graph.facebook.com/'+ fb_id +'/picture?type=square" width="44px" height="48px" />';
				}else{
					personPhotoElement = '<div class="hi-icon-in-list icon-user" style="color:#FFDB58; cursor:default;"></div>';
				}
			}
			break;

			case "1": //Users::TRACEPER_PROFILE_PHOTO_EXISTS
			case "3": //Users::BOTH_PROFILE_PHOTOS_EXISTS_USE_TRACEPER
			{
				if(userId === currentUser) //Current user ise cache kullanma (foto degistirirse hemen gorebilsin diye) 
				{
					personPhotoElement = '<img src="profilePhotos/' + userId + '.png' + '?random=' + timeStamp + '" />';											
				}
				else //Diger kullanicilar icin cache kullan
				{
					personPhotoElement = '<img src="profilePhotos/' + userId + '.png" />';						
				}					
			}
			break;

			case "2": //Users::BOTH_PROFILE_PHOTOS_EXISTS_USE_FACEBOOK
			{
				personPhotoElement = '<img src="https://graph.facebook.com/'+ fb_id +'/picture?type=square" width="44px" height="48px"/>';
			}
			break;

			default:
				//alertMsg("processUsers(), undefined profilePhotoStatus:" + profilePhotoStatus);
				personPhotoElement = '<div class="hi-icon-in-list icon-user" style="color:#FFDB58; cursor:default;"></div>';				
		}		
		
		//alertMsg("userId:" + userId);		

		if (typeof TRACKER.users[userId] == "undefined") 
		{		
			var userMarker;

			switch(profilePhotoStatus)
			{
				case "0": //Users::NO_TRACEPER_PROFILE_PHOTO_EXISTS
				{
					if((fb_id != 0) && (typeof fb_id != "undefined")){					
						userMarker = MAP.putMarker(location, "https://graph.facebook.com/"+ fb_id + "/picture?type=square", visible, true);
					}else{
						userMarker = MAP.putMarker(location, "images/person.png", visible, false);
					}
				}
				break;

				case "1": //Users::TRACEPER_PROFILE_PHOTO_EXISTS
				case "3": //Users::BOTH_PROFILE_PHOTOS_EXISTS_USE_TRACEPER
				{
					if(userId === currentUser) //Current user ise cache kullanma (foto degistirirse hemen gorebilsin diye) 
					{					
						userMarker = MAP.putMarker(location, "profilePhotos/" + userId + ".png" + "?random=" + timeStamp, visible, true);						
					}
					else //Diger kullanicilar icin cache kullan
					{					
						userMarker = MAP.putMarker(location, "profilePhotos/" + userId + ".png", visible, true);						
					}					
				}
				break;

				case "2": //Users::BOTH_PROFILE_PHOTOS_EXISTS_USE_FACEBOOK
				{				
					userMarker = MAP.putMarker(location, "https://graph.facebook.com/"+ fb_id + "/picture?type=square", visible, true);
				}
				break;

				default:
					//alertMsg("processUsers(), undefined profilePhotoStatus:" + profilePhotoStatus);
					userMarker = MAP.putMarker(location, "images/person.png", visible, false);				
			}					

			if(userId === currentUser)
			{
				//main.php ve userAreaView.php dosyalarinda kullaniliyor
				currentUserMarker = userMarker;
			}			
			
			newFriend = true;
			//alertMsg('userId:' + userId + ' added');
	
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
				locationCalculatedTime:locationCalculatedTime,
				locationSource:locationSource
			});				

			var content = 
				  '<div style="width:280px; height:180px;">'
				+ 	'<div><div style="display:inline-block;vertical-align:middle;">' + personPhotoElement + '</div><div style="display:inline-block;vertical-align:middle;padding-left:5px;cursor:text;"><b><font size="5">' + TRACKER.users[userId].realname + '</font></b></div></div>'  
				+ 	'</br>'
				+ 	'<div style="cursor:text;">' + TRACKER.users[userId].time + ' - (' + TRACKER.users[userId].latitude + ", " + TRACKER.users[userId].longitude + ')' + '</div>'
				+ 	'<div style="cursor:text;">' + TRACKER.users[userId].address + '</div>'				
				+ 	'</br>'				
				+ 	'<div style="position:absolute;bottom:10px;">'
				+ 		'<a class="infoWinOperations med-icon-bordered-effect med-icon-effect-a" href="javascript:TRACKER.showPointGMarkerInfoWin('+1+','+2+','+ userId +')">'+ '<div class="med-icon-bordered icon-arrow-left vtip" title="' + TRACKER.langOperator.previousPoint + '"></div>' + '</a>'
				+ 		'<a class="infoWinOperations med-icon-effect med-icon-effect-a" style="margin-left:145px;" href="javascript:TRACKER.zoomPoint('+ TRACKER.users[userId].latitude +','+ TRACKER.users[userId].longitude +')">'+ '<div class="med-icon icon-zoomIn1 vtip" title="' + TRACKER.langOperator.zoom + '"></div>' + '</a>'				
				+ 		'<a class="infoWinOperations med-icon-effect med-icon-effect-a" href="javascript:TRACKER.zoomOutPoint('+ TRACKER.users[userId].latitude +','+ TRACKER.users[userId].longitude +')">'+ '<div class="med-icon icon-zoomOut1 vtip" title="' + TRACKER.langOperator.zoomOut + '"></div>' + '</a>'
				+ 		'<a class="infoWinOperations med-icon-effect med-icon-effect-a" href="javascript:TRACKER.zoomMaxPoint('+ TRACKER.users[userId].latitude +','+ TRACKER.users[userId].longitude +')">'+ '<div class="med-icon icon-zoomMax5 vtip" title="' + TRACKER.langOperator.zoomMax + '"></div>' + '</a>'		
				+ 	'</div>';
				+ '</div>';								
				
			TRACKER.users[userId].mapMarker[0].infoWindow = MAP.initializeInfoWindow(content);
			
			MAP.setMarkerClickListener(TRACKER.users[userId].mapMarker[0].marker,function (){
				//alertMsg(userId + ". marker clicked");
				if(TRACKER.users[userId].locationSource == "0")	
				{
					if(currentUserId == userId)
					{
						TRACKER.showLongMessageDialog(TRACKER.langOperator.yourLocationInfoNotReliable);
					}
					else
					{
						TRACKER.showLongMessageDialog(TRACKER.langOperator.yourFriendsLocationInfoNotReliable);
					}
				}				
				
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
			//alertMsg("else");
			
			var time = dataArrivedTime;
			var deviceId = deviceId;
			var userType = userType;
			//MAP.setMarkerPosition(TRACKER.users[userId].mapMarker[0].marker,location);

			if (isFriend == "1" && TRACKER.users[userId].latitude == "" && TRACKER.users[userId].longitude == "")
			{
				// if they have just become friend, there are no latitude and longitude data 
				// so this statement will run and we update latitude and longitude
				//MAP.setMarkerVisible(TRACKER.users[userId].mapMarker[0].marker, TRACKER.showUsersOnTheMap);
				MAP.setMarkerVisible(TRACKER.users[userId].mapMarker[0].marker, (userId==TRACKER.userId)?true:TRACKER.showUsersOnTheMap);						
			}

//			if ((TRACKER.users[userId].latitude != latitude ||
//					TRACKER.users[userId].longitude != longitude) &&
//					typeof TRACKER.users[userId].polyline != "undefined")
//			{
//				//these "if" is for creating new gmarker when user polyline is already drawed
//				//var userMarker = MAP.putMarker(location, "images/person.png", true, false);					
//				var iWindow = MAP.initializeInfoWindow();
//				var markerInfoWindow = new TRACKER.mapMarker({marker:userMarker, infoWindow:iWindow});
//				
//				MAP.insertPointToPolyline(TRACKER.users[userId].polyline,location,0);
//				
//				var oldlatitude = TRACKER.users[userId].latitude;
//				var oldlongitude = TRACKER.users[userId].longitude;
//
//				MAP.setMarkerClickListener(userMarker,function (){
//					// attention similar function is used in 
//					// processUserPastLocationsXML function
//					var tr = TRACKER.users[userId].mapMarker.indexOf(markerInfoWindow);
//					var previousGMarkerIndex = tr + 1; // it is reverse because 
//					var nextGMarkerIndex = tr - 1;    // as index decreases, the current point gets closer
//
//					var infoWindow = MAP.initializeInfoWindow(
//							getPastPointInfoContent(userId, time, deviceId, userType, previousGMarkerIndex, oldlatitude, oldlongitude, nextGMarkerIndex));
//					MAP.openInfoWindow(infoWindow, userMarker);
//					TRACKER.users[userId].infoWindowIsOpened = true;
//				});
//
//				TRACKER.users[userId].mapMarker.splice(1,0, markerInfoWindow);					
//
//				if (TRACKER.traceLineDrawedUserId != userId) {
//					// if traceline is not visible, hide the marker
//					MAP.setMarkerVisible(userMarker, false)						
//				}
//			}
			
			if ((TRACKER.users[userId].mapMarker[0].infoWindow != null) && ((TRACKER.users[userId].latitude != latitude) ||
					(TRACKER.users[userId].longitude != longitude) || (TRACKER.users[userId].time != time))){
				var isWindowOpen = TRACKER.users[userId].infoWindowIsOpened;
								
				var content = 
					  '<div style="width:280px; height:180px;">'
					+ 	'<div><div style="display:inline-block;vertical-align:middle;">' + personPhotoElement + '</div><div style="display:inline-block;vertical-align:middle;padding-left:5px;cursor:text;"><b><font size="5">' + realname + '</font></b></div></div>'  
					+ 	'</br>'
					+ 	'<div style="cursor:text;">' + time + ' - (' + latitude + ", " + longitude + ')' + '</div>'
					+ 	'<div style="cursor:text;">' + address + '</div>'
					+ 	'</br>'				
					+ 	'<div style="position:absolute;bottom:10px;">'
					+ 		'<a class="infoWinOperations med-icon-bordered-effect med-icon-effect-a" href="javascript:TRACKER.showPointGMarkerInfoWin('+1+','+2+','+ userId +')">'+ '<div class="med-icon-bordered icon-arrow-left vtip" title="' + TRACKER.langOperator.previousPoint + '"></div>' + '</a>'
					+ 		'<a class="infoWinOperations med-icon-effect med-icon-effect-a" style="margin-left:145px;" href="javascript:TRACKER.zoomPoint('+ latitude +','+ longitude +')">'+ '<div class="med-icon icon-zoomIn1 vtip" title="' + TRACKER.langOperator.zoom + '"></div>' + '</a>'				
					+ 		'<a class="infoWinOperations med-icon-effect med-icon-effect-a" href="javascript:TRACKER.zoomOutPoint('+ latitude +','+ longitude +')">'+ '<div class="med-icon icon-zoomOut1 vtip" title="' + TRACKER.langOperator.zoomOut + '"></div>' + '</a>'
					+ 		'<a class="infoWinOperations med-icon-effect med-icon-effect-a" href="javascript:TRACKER.zoomMaxPoint('+ latitude +','+ longitude +')">'+ '<div class="med-icon icon-zoomMax5 vtip" title="' + TRACKER.langOperator.zoomMax + '"></div>' + '</a>'					
					+ 	'</div>';
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
			TRACKER.users[userId].locationSource = locationSource;
			TRACKER.users[userId].deviceId = deviceId;
			TRACKER.users[userId].userType = userType;
			TRACKER.users[userId].friendshipStatus = isFriend;	
		}
	});
	
	//alertMsg("processUsers(), stop - TRACKER.users.length:" + TRACKER.users.length);
	//var size = TRACKER.users.filter(function(value) { return value !== undefined }).length;	
	//alertMsg('TRACKER.users.size:' + size);
	
//	var allKeys = "";
//	
//	for (var key in TRACKER.users) {
//		allKeys += key + " ";
//	}
//	
//	alertMsg("allKeys: " + allKeys);
	
	var anyDeletedFriend = false;
	
	for (var key in TRACKER.users) {	    			
		if((typeof TRACKER.users[key] !== "undefined") && (TRACKER.users[key] !== null))
		{
			//MAP.setMarkerVisible(TRACKER.users[key].mapMarker[0].marker, TRACKER.showUsersOnTheMap);
			//Kullanicinin kendisi her zaman haritada gosterilsin
	    	MAP.setMarkerVisible(TRACKER.users[key].mapMarker[0].marker, (key==TRACKER.userId)?true:TRACKER.showUsersOnTheMap);
			
	    	//if(TRACKER.users[key].infoWindowIsOpened && (TRACKER.showUsersOnTheMap == false))
	    	if(TRACKER.users[key].infoWindowIsOpened && (TRACKER.showUsersOnTheMap == false) && (key != TRACKER.userId))
			{
				MAP.closeInfoWindow(TRACKER.users[key].mapMarker[0].infoWindow)
			}	
	
	    	if((updateType === 'all') && (userIdArray.indexOf(key) === -1))
	    	{
	    		//alertMsg('userId:' + key + ' deleted');
	    		//Bir once tiklanan kisi bu yeni arkadasliktan cikmis kisi ise
	    		if(TRACKER.preUserId == key)
	    		{
	    			TRACKER.preUserId = -1;
	    		}
	    		
	    		MAP.setMarkerVisible(TRACKER.users[key].mapMarker[0].marker, false);
	    		
	    		if(TRACKER.users[key].infoWindowIsOpened)
	    		{
	    			MAP.closeInfoWindow(TRACKER.users[key].mapMarker[0].infoWindow);
	    		}
	
	    		delete TRACKER.users[key];		    		
	    		anyDeletedFriend = true;
	    	}
		}
	}	
	
//	for (key in TRACKER.users) {
//	    if (TRACKER.users.hasOwnProperty(key)  &&        // These are explained
//	        /^0$|^[1-9]\d*$/.test(key) &&    // and then hidden
//	        key <= 4294967294                // away below
//	        ) {
//			
//	    	//alertMsg("processUsers(), TRACKER.users[" + key + "]: false");
//	    			
//	    	if((typeof TRACKER.users[key] !== "undefined") && (TRACKER.users[key] !== null))
//	    	{
//	    		//MAP.setMarkerVisible(TRACKER.users[key].mapMarker[0].marker, TRACKER.showUsersOnTheMap);
//	    		//Kullanicinin kendisi her zaman haritada gosterilsin
//		    	MAP.setMarkerVisible(TRACKER.users[key].mapMarker[0].marker, (key==TRACKER.userId)?true:TRACKER.showUsersOnTheMap);
//				
//		    	//if(TRACKER.users[key].infoWindowIsOpened && (TRACKER.showUsersOnTheMap == false))
//		    	if(TRACKER.users[key].infoWindowIsOpened && (TRACKER.showUsersOnTheMap == false) && (key != TRACKER.userId))
//				{
//					MAP.closeInfoWindow(TRACKER.users[key].mapMarker[0].infoWindow)
//				}	
//
//		    	if((updateType === 'all') && (userIdArray.indexOf(key) === -1))
//		    	{
//		    		//alertMsg('userId:' + key + ' deleted');
//		    		MAP.setMarkerVisible(TRACKER.users[key].mapMarker[0].marker, false);
//		    		
//		    		if(TRACKER.users[key].infoWindowIsOpened)
//		    		{
//		    			MAP.closeInfoWindow(TRACKER.users[key].mapMarker[0].infoWindow);
//		    		}
//
////		    		if(typeof $.fn.yiiGridView != "undefined")
////		    		{
////			    		$.fn.yiiGridView.update("userListView");
////			    		//$.fn.yiiGridView.update('userListView',{ complete: function(){ alertMsg("userListView updated"); } });
////		    		}		    		
//		    		
////		            var myElem = document.getElementById('uploadListView');
////		            if(myElem == null)
////		            {
////		           	 alertMsg('userListView YOK!');
////		            }
////		            else
////		            {
////		           	 alertMsg('userListView VAR');
////		            }		    		
//		    		
//		    		delete TRACKER.users[key];
//		    		
//		    		anyDeletedFriend = true;
//		    	}
//	    	}	    		    	
//	    }
//	}
	
	//if(newFriend === true)
	//if((anyDeletedFriend === true) || ((updateType === 'onlyUpdated') && (newFriend === true)))
	//Sayfa bir kere yuklendikten yani tum arkadaslar alindiktan sonra yeni arkadas geldiyse
	if((anyDeletedFriend === true) || ((TRACKER.updateFriendListPageCount == 1) && (newFriend === true)))
	{
		//alertMsg('$.fn.yiiGridView.settings["userListView"]: ' + typeof $.fn.yiiGridView.settings["userListView"]);
		
		if((typeof $.fn.yiiGridView == "undefined") || (typeof $.fn.yiiGridView.settings["userListView"] == "undefined"))	
		{
			//Do not update
			
			//alertMsg('NOT UPDATED 1');
		}
		else
		{
			$.fn.yiiGridView.update("userListView");
			
			//alertMsg('$.fn.yiiGridView.update("userListView")');
		}
	}
	else
	{
		//alertMsg('NOT UPDATED 2');
	}
	
	delete userIdArray;
}

//var uploadIdArray = new Array();

function processUploads(MAP, deletedUploads, uploads, par_updateType, par_thumbSuffix){
	
	//alertMsg("processUploads() called");
	var updateType = 'all';
	
	if(typeof par_updateType !== 'undefined')
	{
		updateType = par_updateType;
		
		//alertMsg("processUploads - if updateType: " + updateType);
	}
	else
	{
		//alertMsg("processUploads - else updateType: " + updateType);
	}
	
	if(typeof par_thumbSuffix !== 'undefined')
	{
		TRACKER.imageThumbSuffix = par_thumbSuffix;
	}

	var newUpload = false;
	
	//alertMsg("uploads.length:" + uploads.length);
	
	//$(xml).find("page").find("upload").each(function(){
	$.each(uploads, function(index, value)
	{				
		//alertMsg("processImageXML(), find-each");

		var imageId = value.id;
		//alertMsg("imageId:" + imageId);
		
		//uploadIdArray.push(imageId);
		
		var imageURL = decodeURIComponent(value.url); //decodeURIComponent($(image).attr('url'));
		
		//alertMsg("value.url: " + value.url);
		//alertMsg("decodeURIComponent(value.url): " + decodeURIComponent(value.url));
		
		var realname = value.byRealName;
		var userId = value.byUserId;
		var latitude = value.latitude;
		var longitude = value.longitude;
		var time = value.time;
		var rating = value.rating;
		var description = ""; //value.description; //$(image).attr('description');
		
		//alertMsg(value.description);
		
		var location = new MapStruct.Location({latitude:latitude, longitude:longitude});
		
		if ($.inArray(imageId, TRACKER.imageIds) == -1)
		{
			TRACKER.imageIds.push(imageId);
		}
		
		if (typeof TRACKER.images[imageId] == "undefined") {
			
			newUpload = true;
			
			//alertMsg("images["+ imageId +"] is undefined!");
	
			image = imageURL + "&fileType=0&"+ TRACKER.imageThumbSuffix;
			//image = imageURL + "&fileType=0&thumb=ok";
			var userMarker = MAP.putMarker(location, image, false, false);
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
			
			//alertMsg("MAP.setMarkerVisible(true)");
						
			MAP.setMarkerVisible(TRACKER.images[imageId].mapMarker.marker, TRACKER.showImagesOnTheMap); //ADNAN					
			MAP.setMarkerClickListener(TRACKER.images[imageId].mapMarker.marker,function (){
				var image = new Image();

				image.src= TRACKER.images[imageId].imageURL + "&fileType=0"; // + TRACKER.imageOrigSuffix;
				//$("#loading").show();
				$(image).load(function(){
					//$("#loading").hide();
					
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
			//alertMsg("images["+ imageId +"] is already defined");
			
			//alertMsg("TRACKER.showImagesOnTheMap: " + TRACKER.showImagesOnTheMap);
			
			MAP.setMarkerVisible(TRACKER.images[imageId].mapMarker.marker, TRACKER.showImagesOnTheMap); //ADNAN		
		}

	});
	
//	for (var i = 0; i < TRACKER.images.length; i++) {
//		MAP.setMarkerVisible(TRACKER.images[imageId].mapMarker.marker, TRACKER.showImagesOnTheMap); //ADNAN	
//	}
	
//	var allKeys = "";
//	
//	for (var key in TRACKER.images) {
//		allKeys += key + " ";
//	}
//	
//	alertMsg("allKeys: " + allKeys);
	
	for (var key in TRACKER.images) {	    			
		if((typeof TRACKER.images[key] !== "undefined") && (TRACKER.images[key] !== null))
		{
	    	MAP.setMarkerVisible(TRACKER.images[key].mapMarker.marker, TRACKER.showImagesOnTheMap); //ADNAN	
			
			if(TRACKER.images[key].infoWindowIsOpened && (TRACKER.showImagesOnTheMap == false))
			{
				MAP.closeInfoWindow(TRACKER.images[key].mapMarker.infoWindow)
			}				
		}
	}	
	
//	for (key in TRACKER.images) {
//	    if (TRACKER.images.hasOwnProperty(key)  &&        // These are explained
//	        /^0$|^[1-9]\d*$/.test(key) &&    // and then hidden
//	        key <= 4294967294                // away below
//	        ) {
//			//alertMsg("processUsers(), TRACKER.images[" + key + "]: false");
//
//			if((typeof TRACKER.images[key] !== "undefined") && (TRACKER.images[key] !== null))
//			{
//		    	MAP.setMarkerVisible(TRACKER.images[key].mapMarker.marker, TRACKER.showImagesOnTheMap); //ADNAN	
//				
//				if(TRACKER.images[key].infoWindowIsOpened && (TRACKER.showImagesOnTheMap == false))
//				{
//					MAP.closeInfoWindow(TRACKER.images[key].mapMarker.infoWindow)
//				}
//				
////		    	if((updateType === 'all') && (uploadIdArray.indexOf(key) === -1))
////		    	{
////		    		//alertMsg('uploadId:' + key + 'deleted');
////		    		MAP.setMarkerVisible(TRACKER.images[key].mapMarker.marker, false);	
////		    		
////		    		if(TRACKER.images[key].infoWindowIsOpened)
////		    		{
////		    			MAP.closeInfoWindow(TRACKER.images[key].mapMarker.infoWindow);
////		    		}
////
////		    		if(typeof $.fn.yiiGridView != "undefined")
////		    		{
////			    		$.fn.yiiGridView.update(uploadsGridViewId);
////			    		//$.fn.yiiGridView.update('uploadListView',{ complete: function(){ alertMsg("uploadListView updated"); } });
////		    		}		    		
////
////		    		delete TRACKER.images[key];
////		    	}				
//			}	    				
//	    }
//	}
	
	var anyDeletedUpload = false;
	
	$.each(deletedUploads, function(index, value)
	{				
		var uploadId = value.uploadId;

    	//alertMsg('uploadId:' + uploadId + ' deleted');
    	
    	if((typeof TRACKER.images[uploadId] !== "undefined") && (TRACKER.images[uploadId] !== null))
    	{
    		anyDeletedUpload = true;
    		
    		MAP.setMarkerVisible(TRACKER.images[uploadId].mapMarker.marker, false);	
    		
    		if(TRACKER.images[uploadId].infoWindowIsOpened)
    		{
    			MAP.closeInfoWindow(TRACKER.images[uploadId].mapMarker.infoWindow);
    		}

//    		if(typeof $.fn.yiiGridView != "undefined")
//    		{
//	    		$.fn.yiiGridView.update(uploadsGridViewId);
//	    		//$.fn.yiiGridView.update('uploadListView',{ complete: function(){ alertMsg("uploadListView updated"); } });
//    		}		    		

    		delete TRACKER.images[uploadId];   		
    	}						
	});				
	
	//Tum fotolar alindiktan sonra yeni foto geldiyse
	if((anyDeletedUpload === true) || ((TRACKER.allImagesFetched === true) && (newUpload === true)))
	{
		if((typeof $.fn.yiiGridView == "undefined") || (typeof $.fn.yiiGridView.settings[uploadsGridViewId] == "undefined"))	
		{
			//Do not update
			
			//alertMsg('NOT UPDATED 1 - 1.cond:' + (typeof $.fn.yiiGridView == "undefined") + ' / 2. cond:' + (typeof $.fn.yiiGridView.settings[uploadsGridViewId] == "undefined") + ' / uploadsGridViewId:' + uploadsGridViewId);
		}
		else
		{
			$.fn.yiiGridView.update(uploadsGridViewId);
			
			//alertMsg('$.fn.yiiGridView.update(uploadsGridViewId)');
		}		
		
		//alertMsg("Deleted or New Upload");
	}
	else
	{
		//alertMsg('NOT UPDATED 2 - TRACKER.allImagesFetched: ' + TRACKER.allImagesFetched + ' / newUpload: ' + newUpload);
	}
	
	//delete uploadIdArray;
	
	//alertMsg("processImageXML(), stop - TRACKER.images.length:" + TRACKER.images.length);
}

/**
 * 
 */
function processImageXML(MAP, xml){
	var list = "";
	TRACKER.imageThumbSuffix = decodeURIComponent($(xml).find("page").attr("thumbSuffix"));
//	TRACKER.imageOrigSuffix = decodeURIComponent($(xml).find("page").attr("origSuffix"));
	
	//alertMsg("processImageXML(), start - TRACKER.images.length:" + TRACKER.images.length);

	var updateType = decodeURIComponent($(xml).find("page").attr("updateType"));
	
	//alertMsg("users.length:" + users.length + " / TRACKER.users.length:" + TRACKER.users.length);
	
	var uploadIdArray = new Array();
	var newUpload = false;	
	
	$(xml).find("page").find("upload").each(function(){
		
		//alertMsg("processImageXML(), find-each");
		
		var image = $(this);
		var imageId = $(image).attr('id');
		uploadIdArray.push(imageId);
		
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
			
			newUpload = true;
			
			//alertMsg("images["+ imageId +"] is undefined!");
	
			image = imageURL + "&fileType=0&"+ TRACKER.imageThumbSuffix;
			//image = imageURL + "&fileType=0&thumb=ok";
			var userMarker = MAP.putMarker(location, image, false, false);
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
			
			//alertMsg("MAP.setMarkerVisible(true)");
						
			MAP.setMarkerVisible(TRACKER.images[imageId].mapMarker.marker, TRACKER.showImagesOnTheMap); //ADNAN					
			MAP.setMarkerClickListener(TRACKER.images[imageId].mapMarker.marker,function (){
				var image = new Image();

				image.src= TRACKER.images[imageId].imageURL + "&fileType=0"; // + TRACKER.imageOrigSuffix;
				//$("#loading").show();
				$(image).load(function(){
					//$("#loading").hide();
					
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
			//alertMsg("images["+ imageId +"] is already defined");
			
			//alertMsg("TRACKER.showImagesOnTheMap: " + TRACKER.showImagesOnTheMap);
			
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
			//alertMsg("processUsers(), TRACKER.images[" + key + "]: false");

			if((typeof TRACKER.images[key] !== "undefined") && (TRACKER.images[key] !== null))
			{
		    	MAP.setMarkerVisible(TRACKER.images[key].mapMarker.marker, TRACKER.showImagesOnTheMap); //ADNAN	
				
				if(TRACKER.images[key].infoWindowIsOpened && (TRACKER.showImagesOnTheMap == false))
				{
					MAP.closeInfoWindow(TRACKER.images[key].mapMarker.infoWindow)
				}
				
		    	if((updateType === 'all') && (uploadIdArray.indexOf(key) === -1))
		    	{
		    		//alertMsg('uploadId:' + key + 'deleted');
		    		MAP.setMarkerVisible(TRACKER.images[key].mapMarker.marker, false);	
		    		
		    		if(TRACKER.images[key].infoWindowIsOpened)
		    		{
		    			MAP.closeInfoWindow(TRACKER.images[key].mapMarker.infoWindow);
		    		}

		    		if((typeof $.fn.yiiGridView == "undefined") || (typeof $.fn.yiiGridView.settings[uploadsGridViewId] == "undefined"))	
		    		{
		    			//Do not update
		    			
		    			//alertMsg('NOT UPDATED 1');
		    		}
		    		else
		    		{
		    			$.fn.yiiGridView.update(uploadsGridViewId);
		    		}		    		

		    		delete TRACKER.images[key];
		    	}				
			}	    				
	    }
	}
	
	if(newUpload === true)
	{
		if((typeof $.fn.yiiGridView == "undefined") || (typeof $.fn.yiiGridView.settings[uploadsGridViewId] == "undefined"))	
		{
			//Do not update
			
			//alertMsg('NOT UPDATED 1');
		}
		else
		{
			$.fn.yiiGridView.update(uploadsGridViewId);
		}		
	}	
	
	delete uploadIdArray;
	
	//alertMsg("processImageXML(), stop - TRACKER.images.length:" + TRACKER.images.length);
	
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
