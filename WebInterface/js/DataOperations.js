/**
 * this function process the user past locations xml,
 * it is responsible for creating and updating markers and polylines,
 * server doesn't return the last available point of user in past locations xml
*/
function processUserPastLocationsXML (MAP, xml) {
		var userId = $(xml).find("page").attr('userId');
		var pastPoints = []; 
		var pastPointsGMarker = [];
		var index = TRACKER.users[userId].pastPointsGMarker.length;

		$(xml).find("page").find("location").each(function(){
			var location = $(this);
			var latitude = $(location).attr('latitude');
			var longitude = $(location).attr('longitude');
			var time = $(location).find('time').text();
			var deviceId = $(location).find('deviceId').text();
			var point = new GLatLng(latitude, longitude);
			pastPoints.push(point);
			var gmarker = new GMarker(point);
			
			GEvent.addListener(gmarker, "click", function(){
				
				var tr = TRACKER.users[userId].pastPointsGMarker.indexOf(gmarker);
				var previousGMarkerIndex = tr + 1; // it is reverse because 
				var nextGMarkerIndex = tr - 1;    // as index decreases, the current point gets closer
				// attention similar function is used in 
				// processXML function				
				gmarker.openInfoWindowHtml("<div>" 
						  					+ "<b>" + TRACKER.users[userId].realname + "</b> " 
						  						+ TRACKER.langOperator.wasHere 
						  						+ '<br/>' + TRACKER.langOperator.time + ": " + time
						  						+ '<br/>' + TRACKER.langOperator.deviceId + ": " + deviceId
						  					+ "</div>"
						  					+ '<ul class="sf-menu"> '
						  					+ "<li>"
						  						+'<a class="infoWinOperations" href="javascript:TRACKER.showPointGMarkerInfoWin('+ previousGMarkerIndex +','+ userId +')">'
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
							   					+'<a class="infoWinOperations" href="javascript:TRACKER.showPointGMarkerInfoWin('+ nextGMarkerIndex +','+ userId +')">'
							   						+ TRACKER.langOperator.nextPoint 
							   					+'</a>'
							   				+ "</li>"
										+"</ul>");	
			});
			index++;
			MAP.addOverlay(gmarker);			
			pastPointsGMarker.push(gmarker)
		});
		
		if (typeof TRACKER.users[userId].polyline == "undefined") 
		{
			var firstPoint= [new GLatLng(TRACKER.users[userId].latitude, TRACKER.users[userId].longitude)];
			TRACKER.users[userId].polyline = new GPolyline(firstPoint.concat(pastPoints), "#ff0000", 10);;
			MAP.addOverlay(TRACKER.users[userId].polyline);
		}
		else {
			var len = pastPoints.length;
			var i;
			var vertexIndex = TRACKER.users[userId].polyline.getVertexCount();
			for (i = 0; i < len; i++){
				TRACKER.users[userId].polyline.insertVertex(vertexIndex, pastPoints[i]);
				vertexIndex++;
			}
		}
		var tmp = TRACKER.users[userId].pastPointsGMarker;	
		TRACKER.users[userId].pastPointsGMarker = tmp.concat(pastPointsGMarker);
	}

	/**
	 * this function process XML returned when actions are search user, get user list, update list,
	 * updated list...
	 */	
	function processXML(MAP, xml, isFriendList)
	{
		//alert("in processXML");
		var list = "";
		$(xml).find("page").find("user").each(function(){
			
			var user = $(this);			
			var userId = $(user).find("Id").text();
			var isFriend =  $(user).find("Id").attr('isFriend');
//			var username = $(user).find("username").text();
			var realname = $(user).find("realname").text();
			var latitude = $(user).find("location").attr('latitude');
			var longitude = $(user).find("location").attr('longitude');
			var status_message = $(user).find("status_message").text();
			var point = new GLatLng(latitude, longitude);
			
			if (userId  != TRACKER.userId ){
				
				if (isFriend == "1" || isFriend == "2" || isFriendList == true) {
					list += "<img class='deleteImageButton' onclick='TRACKER.deleteFriendship("+userId+")' src='images/delete.png' />";
					if (isFriend == "2")
					{
						list += "<img class='deleteImageButton' src='images/question_mark.png' />";					
						
					}				
				}
				else {
					// add as friend button
					list += "<img class='deleteImageButton' onclick='TRACKER.addAsFriend("+userId+")' src='images/user_add_friend.png' />";
				}
				
				
			}
			list += "<li><a href='javascript:TRACKER.trackUser("+ userId +")' id='user"+ userId +"'>"+ realname + " " + status_message +"</a></li>";
			
			if (isFriend == "1")
			{
				if (typeof TRACKER.users[userId] == "undefined") 
				{		
					var personIcon = new GIcon(G_DEFAULT_ICON);
					personIcon.image = "images/person.png";
					personIcon.iconSize = new GSize(24,24);
					personIcon.shadow = null;
					markerOptions = { icon:personIcon };
				
					TRACKER.users[userId] = new TRACKER.User( {//username:username,
															   realname:realname,
															   latitude:latitude,
															   longitude:longitude,
															   time:$(user).find("time").text(),
															   message:$(user).find("message").text(),
															   status_message:status_message,
															   deviceId:$(user).find("deviceId").text(),
															   gmarker:new GMarker(point, markerOptions),														   
															});
					GEvent.addListener(TRACKER.users[userId].gmarker, "click", function() {
	  						TRACKER.openMarkerInfoWindow(userId);	
	  				});
	  				
					GEvent.addListener(TRACKER.users[userId].gmarker,"infowindowopen",function(){
						TRACKER.users[userId].infoWindowIsOpened = true;
	  				});
					
	  				GEvent.addListener(TRACKER.users[userId].gmarker,"infowindowclose",function(){
	  					TRACKER.users[userId].infoWindowIsOpened = false;
	  				});
	  				if (typeof TRACKER.users[userId].pastPointsGMarker == "undefined") {
	  					TRACKER.users[userId].pastPointsGMarker = new Array(TRACKER.users[userId].gmarker);
	  				}
					MAP.addOverlay(TRACKER.users[userId].gmarker);
				}
				else
				{
					var time = $(user).find("time").text();
					var deviceId = $(user).find("deviceId").text();
					var point = new GLatLng(latitude, longitude);				
					TRACKER.users[userId].gmarker.setLatLng(point);
					
					if ((TRACKER.users[userId].latitude != latitude ||
						 TRACKER.users[userId].longitude != longitude) &&
						 typeof TRACKER.users[userId].polyline != "undefined")
					{
						//these "if" is for creating new gmarker when user polyline is already drawed  
						var gmarker = new GMarker(new GLatLng(TRACKER.users[userId].latitude, 
															  TRACKER.users[userId].longitude));
						TRACKER.users[userId].polyline.insertVertex(0, point);
						var oldlatitude = TRACKER.users[userId].latitude;
						var oldlongitude = TRACKER.users[userId].longitude;
						
						GEvent.addListener(gmarker, "click", function(){
							// attention similar function is used in 
							// processUserPastLocationsXML function
							var tr = TRACKER.users[userId].pastPointsGMarker.indexOf(gmarker);
							var previousGMarkerIndex = tr + 1; // it is reverse because 
							var nextGMarkerIndex = tr - 1;    // as index decreases, the current point gets closer
							
							gmarker.openInfoWindowHtml("<div>" 
														  + "<b>" + TRACKER.users[userId].realname + "</b> " 
														  + TRACKER.langOperator.wasHere 
														  + '<br/>' + TRACKER.langOperator.time + ": " + TRACKER.users[userId].time
														  + '<br/>' + TRACKER.langOperator.deviceId + ": " + TRACKER.users[userId].deviceId
														+ "</div>"
														+ '<ul class="sf-menu"> '
									  					+ "<li>"
									  						+'<a class="infoWinOperations" href="javascript:TRACKER.showPointGMarkerInfoWin('+ previousGMarkerIndex +','+ userId +')">'
										   						+ TRACKER.langOperator.previousPoint 
										   					+'</a>'
										   				+ "</li>"
										   				+ "<li>"
										   					+"<a href='#' class='infoWinOperations'>"
										   						+ TRACKER.langOperator.operations
										   					+"</a>"
										   					+"<ul>"
												   				+"<li>"
										   							+'<a class="infoWinOperations" href="javascript:TRACKER.zoomPoint('+ oldlatitude +','+ oldlongitude +')">'
										   								+ TRACKER.langOperator.zoom 
										   							+'</a>' 		
										   						+"</li>"
										   						+"<li>"
										   							+'<a class="infoWinOperations" href="javascript:TRACKER.zoomMaxPoint('+ oldlatitude +','+ oldlongitude +')">'
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
										   					+'<a class="infoWinOperations" href="javascript:TRACKER.showPointGMarkerInfoWin('+ nextGMarkerIndex +','+ userId +')">'
										   						+ TRACKER.langOperator.nextPoint 
										   					+'</a>'
										   				+ "</li>"
													+"</ul>");	
						});
						
						TRACKER.users[userId].pastPointsGMarker.splice(1,0, gmarker);					
						MAP.addOverlay(gmarker);
						
						if (TRACKER.traceLineDrawedUserId != userId) {
							// if traceline is not visible, hide the marker
							gmarker.hide();
						}
						
					}
					TRACKER.users[userId].latitude = latitude;
					TRACKER.users[userId].longitude = longitude;
					TRACKER.users[userId].time = time;
					TRACKER.users[userId].deviceId = deviceId;
					
					var isWindowOpen = TRACKER.users[userId].infoWindowIsOpened;
					TRACKER.closeMarkerInfoWindow(userId);
					
					if (isWindowOpen == true) {
						TRACKER.openMarkerInfoWindow(userId);
					}
					
				}			
			} // end of if (isFriend == 1)
					
		});
		
		if (list != "") {
			list = "<ul>" + list + "</ul>"; 
		}
		else {
			list = null;
		}
		return list;		
	};	

	/**
	 * 
	 */
function processImageXML(MAP, xml){
	var list = "";
	TRACKER.imageThumbSuffix = decodeURIComponent($(xml).find("page").attr("thumbSuffix"));
	TRACKER.imageOrigSuffix = decodeURIComponent($(xml).find("page").attr("origSuffix"));
	var hideMarker = !($('#showPhotosOnMap').attr('checked'));

	$(xml).find("page").find("image").each(function(){
		var image = $(this);
		var imageId = $(image).attr('id');
		var imageURL =  decodeURIComponent($(image).attr('url'));
		var realname = $(image).attr("byRealName");
		var userId = $(image).attr("byUserId");
		var latitude = $(image).attr('latitude');
		var longitude = $(image).attr('longitude');
		var time = $(image).attr('time');
		var point = new GLatLng(latitude, longitude);
		
		list += "<li>";
				
		if (TRACKER.userId == userId) {
			// add delete image button if logged in user and image uploader are same
			list += "<img class='deleteImageButton' onclick='TRACKER.deleteImage("+imageId+")' src='images/delete.png' />";
		}				
		list += "<a href='javascript:TRACKER.showImageWindow("+ imageId +")' id='image"+ imageId +"'>"
							+ "<div>"
								+ "<img src='"+ imageURL + TRACKER.imageThumbSuffix +"' class='thumbImage' />" 
							+ "</div>"
							+ "<div>"
								+ TRACKER.langOperator.uploader + ": " + realname 
								+ "<br/>"
								/* + TRACKER.langOperator.time + ": " */ +  time									
							+ "</div>"
						+ "</a>"
					+"</li>";

		if ($.inArray(imageId, TRACKER.imageIds) == -1)
		{
			TRACKER.imageIds.push(imageId);
		}
		
		if (typeof TRACKER.images[imageId] == "undefined") {
			
			var personIcon = new GIcon(G_DEFAULT_ICON);
			personIcon.image = imageURL + TRACKER.imageThumbSuffix;
	//		personIcon.iconSize = new GSize(24,24);
			personIcon.shadow = null;
			markerOptions = { icon:personIcon, hide:hideMarker };
			TRACKER.images[imageId] = new TRACKER.Img({imageId:imageId,
												imageURL:imageURL,
												userId:userId,
												realname:realname,
												latitude:latitude,
												longitude:longitude,
												time:time,
												gmarker:new GMarker(point, markerOptions),
												});
			GEvent.addListener(TRACKER.images[imageId].gmarker, "click", function() {
				TRACKER.showImageWindow(imageId);	
			});
			GEvent.addListener(TRACKER.images[imageId].gmarker,"infowindowopen",function(){
				TRACKER.images[imageId].gmarker.show();
				});
			GEvent.addListener(TRACKER.images[imageId].gmarker,"infowindowclose",function(){
				if ($('#showPhotosOnMap').attr('checked') == false){
					TRACKER.images[imageId].gmarker.hide();
				}
				});
			MAP.addOverlay(TRACKER.images[imageId].gmarker);
		}
		
	});
	if (list != "") {
		list = "<ul>" + list + "</ul>"; 
	}
	else {
		list = null;
	}
	return list;
}