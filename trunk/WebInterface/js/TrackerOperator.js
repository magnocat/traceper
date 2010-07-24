
function TrackerOperator(url, map, fetchPhotosInInitial, interval, qUpdatedUserInterval, langOp){
	
	TRACKER = this;	
	MAP = map;
	this.langOperator = langOp;
	this.language = "en";
	this.ajaxUrl = url;
	this.fetchPhotosInInitialization = Number(fetchPhotosInInitial);
	this.actionAuthenticateUser = "WebClientAuthenticateUser";
	this.actionGetUserList = "WebClientGetUserList";	
	this.actionSearchUser = "WebClientSearchUser";
	this.actionUpdateUserList = "WebClientUpdateUserList";
	this.actionGetUpdatedUserList = "WebClientGetUpdatedUserList";
	this.actionGetUserPastPoints = "WebClientGetUserPastPoints";
	this.actionGetImageList = "WebClientGetImageList";
	this.actionSearchImage = "WebClientSearchImage";
	this.actionSignout = "WebClientSignout";
	this.actionSendNewPassword = "WebClientSendNewPassword";
	this.actionInviteUser = "WebClientInviteUser";
	this.userListPageNo = 1;	
	this.userListPageCount = 0;
	this.updateUserListPageNo = 1;
	this.updateUserListPageCount = 0;
	this.searchPageNo = 1;
	this.searchPageCount = 0;
	this.pastPointsPageNo = 0;
	this.imageListPageNo = 1;
	this.imageListPageCount = 0;
	this.imageListSearchPageNo = 1;
	this.imageListSearchPageCount = 0;
	//page no initial value is important
	this.bgImageListPageNo = 1;
	this.bgImageListPageCount = 0;
	this.pastPointsPageCount = 0;
	this.updateInterval = interval;
	this.timer;
	this.maxZoomlevel = [[]];
	this.traceLineDrawedUserId = null;
	/*
	 * After all users info is got, only users whose location changed is queried every
	 * queryUpdatedUserInterval seconds
	 */
	this.queryUpdatedUserInterval = qUpdatedUserInterval;
	/*
	 * All users are gotten in initialization page by page, getUserListInterval 
	 * is the interval that ajax request sent
	 */
	this.getUserListInterval = interval;
	this.started = false;
	/**
	 * if all users are getted from the server, then this variable is set to true
	 */
    this.userPageResetCount = Number(0);	
	this.users = [];
	/**
	 * this is just a flag to know whether images are fetched
	 * it is used when photos tab is clicked...
	 */
	this.allImagesFetched = false;
	this.images = [];
	this.imageIds = [];
	this.imageThumbSuffix;
	this.imageOrigSuffix;
	
	this.User = function(){
//		var username;
		var realname;
		var latitude;
		var longitude;
		var time;
		var deviceId;
		var message;
		var gmarker;
		var pastPointsGMarker;
		var infoWindowIsOpened = false;
		var polyline = null;		
		var maxZoomLevel = null;
		
		for (var n in arguments[0]) { 
			this[n] = arguments[0][n]; 
		}		
	}
	this.Img = function(){
		var imageId;
		var imageURL;
		var userId;
		var username;
		var latitude;
		var longitude;
		var time;
		var gmarker;
		
		for (var n in arguments[0]) { 
			this[n] = arguments[0][n]; 
		}
	}
	
	this.authenticateUser = function(username, password, rememberMe)
	{
		var params = "action=" + TRACKER.actionAuthenticateUser + "&username=" + username + "&password=" + password + "&keepUserLoggedIn=" + rememberMe;
		if (username != "" && password != "" ) 
		{
			TRACKER.ajaxReq(params, function (result){
				if (result == "1") {					
					location.href = 'index.php';
				}
				else if (result == "-4"){
					alert(TRACKER.langOperator.incorrectPassOrUsername);
				}
			});
		}
		else {
			alert(TRACKER.langOperator.warningMissingParameter);
		}
	};
	
	this.inviteUser = function(email) {
		var params = "action=" + TRACKER.actionInviteUser + "&email=" + email;
		
		TRACKER.ajaxReq(params, function (result){
			if (result == "1") {					
				alert("operation is succesfull");
			}
			else{
				alert("Error in operation");
			}
		});
		
		
		
	};
	
	this.sendNewPassword = function(email){
		var params = "action=" + TRACKER.actionSendNewPassword + "&email=" + email;
		if (email != "" ) 
		{
			TRACKER.ajaxReq(params, function (result){
				alert(result);
				if (result == "1") {					
					alert(TRACKER.langOperator.newPasswordSent);
				}
				else if (result == "-6"){
					alert(TRACKER.langOperator.emailNotFound);
				}
			});
		}
		else {
			alert(TRACKER.langOperator.warningMissingParameter);
		}
	}
	
	this.signout = function(){
		var params = "action=" + TRACKER.actionSignout;
		TRACKER.ajaxReq(params, function (result){
			
			if (result == "1") {
				location.href = 'index.php';				
			}
			else if (result == "-4"){
				alert(TRACKER.langOperator.checkPassOrUsername);
			}
		});
	}
	
	// getting user list with latitude longittude info
	this.getUserList = function(pageNo)
	{		
		var params = "action=" + TRACKER.actionGetUserList + "&pageNo=" + pageNo;
		TRACKER.ajaxReq(params, function(result){			
			TRACKER.userListPageNo = TRACKER.getPageNo(result);
			TRACKER.userListPageCount = TRACKER.getPageCount(result);
			
			var str = TRACKER.processXML(result);
			
			if (str != null) {
				str += TRACKER.writePageNumbers('javascript:TRACKER.getUserList(%d)', TRACKER.userListPageCount, TRACKER.userListPageNo, 3);
			}
			else {
				str = TRACKER.langOperator.noMatchFound;				
			}
			$('#users').slideUp('fast',function(){
									$('#users').html(str);
									$('#users').slideDown();
								});
			
			if (TRACKER.started == false) {
				TRACKER.started = true;
				setTimeout(TRACKER.updateUserList, TRACKER.updateInterval);
			}			
			
		});	
	};
	/**
	 * 
	 */
	this.updateUserList = function(){
		
		var params;
		if (TRACKER.userPageResetCount > 0) 
		{
			var getImages = "&";
			if ($('#showPhotosOnMap').attr('checked') == true)
			{ 	getImages = "&include=image"; }
			
			params = "action=" + TRACKER.actionGetUpdatedUserList + "&pageNo=" + TRACKER.updateUserListPageNo
					+ getImages;
		}
		else {
			params = "action=" + TRACKER.actionUpdateUserList + "&pageNo=" + TRACKER.updateUserListPageNo; 
			
		}

		// set time out again
		TRACKER.timer = setTimeout(TRACKER.updateUserList, TRACKER.updateInterval);
				
		TRACKER.ajaxReq(params, function(result){
			
			TRACKER.updateUserListPageNo = TRACKER.getPageNo(result);
			TRACKER.updateUserListPageCount = TRACKER.getPageCount(result);
			TRACKER.processXML(result);
			// to fetched all data reguarly updateUserListPageNo must be resetted.
			var updateInt = TRACKER.updateInterval;
			if (TRACKER.updateUserListPageNo >= TRACKER.updateUserListPageCount){
				TRACKER.updateUserListPageNo = 1;
				TRACKER.updateInterval = TRACKER.queryUpdatedUserInterval;
				TRACKER.userPageResetCount = Number(TRACKER.userPageResetCount) + 1;
				
				var showPhotosOnMap = $('#showPhotosOnMap').attr('checked');
				if (TRACKER.userPageResetCount >= 1 &&
					showPhotosOnMap == true)
				{
					TRACKER.processImageXML(result);
				}
				// this is about initialization, it fetches photos data from server
				// after fetching users data
				if (TRACKER.userPageResetCount == 1 &&
					showPhotosOnMap == true) 
				{
					TRACKER.getImageListInBg();
				}
			}
			else{
				TRACKER.updateUserListPageNo++;
				TRACKER.updateInterval = TRACKER.getUserListInterval;
			}
			
			if (updateInt != TRACKER.updateInterval) {
				clearTimeout(TRACKER.timer);
				TRACKER.timer = setTimeout(TRACKER.updateUserList, TRACKER.updateInterval);
			}
			
		}, true);
	};

	this.searchUser = function(string, pageNo)
	{
		if (string.length >= 2) {
			var params = "action=" + TRACKER.actionSearchUser + "&search=" + string + "&pageNo=" + pageNo;
			
			TRACKER.ajaxReq(params, function(result){
				TRACKER.searchPageNo = TRACKER.getPageNo(result);
				TRACKER.searchPageCount = TRACKER.getPageCount(result);
				
				var str = TRACKER.processXML(result);
				if (str != null) {
					str += TRACKER.writePageNumbers('javascript:TRACKER.searchUser("' + string + '", %d)', TRACKER.searchPageCount, TRACKER.searchPageNo, 3);
				}
				else {
					str = TRACKER.langOperator.noMatchFound;
				}
				
				$('#usersList #users').slideUp();

				$('#usersList .searchResults').slideUp('fast',function(){
						$('#usersList .searchResults #results').html(str);
						$('#usersList .searchResults').slideDown();
				});
			});
		}
		else {
			alert(TRACKER.langOperator.searchStringIsTooShort);
		}	
	};	
	
	this.getImageList = function(pageNo, callback){
		var params = "action=" + TRACKER.actionGetImageList + "&pageNo=" + pageNo;
				
		TRACKER.ajaxReq(params, function(result){			
			TRACKER.imageListPageNo = TRACKER.getPageNo(result);
			TRACKER.imageListPageCount = TRACKER.getPageCount(result);
			
			var str = TRACKER.processImageXML(result);
			
			if (str != null) {
				str += TRACKER.writePageNumbers('javascript:TRACKER.getImageList(%d)', TRACKER.imageListPageCount, TRACKER.imageListPageNo, 3);
			}
			else {
				str = TRACKER.langOperator.noMatchFound;				
			}
			$('#photos').slideUp('fast',function(){
									$('#photos').html(str);
									$('#photos').slideDown();
								});
			
			if (typeof callback == 'function'){
				callback();
			}
			
		});	
	};
	
	var fetchingImagesInBgStart = false;
	
	this.getImageListInBg = function(){
		if (fetchingImagesInBgStart == true){
			return;
		}
		
		fetchingImagesInBgStart = true;
		var params = "action=" + TRACKER.actionGetImageList + "&pageNo=" + TRACKER.bgImageListPageNo
					+ "&list=long";
		
		TRACKER.ajaxReq(params, function(result){			
			TRACKER.bgImageListPageNo = TRACKER.getPageNo(result);
			TRACKER.bgImageListPageCount = TRACKER.getPageCount(result);
			
			TRACKER.processImageXML(result);
			
			if (TRACKER.bgImageListPageNo < TRACKER.bgImageListPageCount){
				TRACKER.bgImageListPageNo = Number(TRACKER.bgImageListPageNo) + 1;
				setTimeout(TRACKER.getImageListInBg, TRACKER.getUserListInterval);
			}	
			else if (TRACKER.bgImageListPageNo == TRACKER.bgImageListPageCount){
				TRACKER.allImagesFetched = true;
			}
		}, true);	
	}
	
	this.searchImage = function(username,userId, pageNo){
		var params = "";
		if (userId != false){
			params = "userId=" + userId;
		}
		else if (username != false){
			params = "username=" + username;
		}
		
		if (params == ""){
			
		}
		else {
			params += "&action="+ TRACKER.actionSearchImage +"&pageNo=" + pageNo;
		
			TRACKER.ajaxReq(params, function(result){			
				TRACKER.imageListSearchPageNo = TRACKER.getPageNo(result);
				TRACKER.imageListSearchPageCount = TRACKER.getPageCount(result);

				var str = TRACKER.processImageXML(result);

				if (str != null) {
					str += TRACKER.writePageNumbers('javascript:TRACKER.searchImage("'+ username +'","'+ userId +'" %d)', TRACKER.imageListSearchPageCount, TRACKER.imageListSearchPageNo, 3);
				}
				else {
					str = "<div class='generalStyle'>" + TRACKER.langOperator.noMatchFound + "</div>";				
				}
				$('#usersList').slideUp('fast',function(){
					$('#photosList').slideDown('fast', function(){
						$('#photosList #photos').slideUp();
						$('#photosList .searchResults').slideUp('fast',function(){
							$('#photosList .searchResults #results').html(str);
							$('#photosList .searchResults').slideDown();
						});
					});					
				});					
			});	
		}
		
	}
	
	this.trackUser = function(userId){
		MAP.panTo(new GLatLng(TRACKER.users[userId].latitude, TRACKER.users[userId].longitude));
		TRACKER.openMarkerInfoWindow(userId);		
	};
	
	this.drawTraceLine = function(userId, pageNo, callback) 
	{
		// hide any polyline if it is drawed
		if (TRACKER.traceLineDrawedUserId != null &&
			userId != TRACKER.traceLineDrawedUserId &&
			typeof TRACKER.users[TRACKER.traceLineDrawedUserId].polyline != "undefined")
		{			
			TRACKER.clearTraceLines(TRACKER.traceLineDrawedUserId);
		}		
		if (typeof TRACKER.users[userId].polyline == "undefined" ||
			pageNo > TRACKER.pastPointsPageNo ) 
		{
			var params = "action=" + TRACKER.actionGetUserPastPoints
						+"&userId=" + userId
						+"&pageNo=" + pageNo;
		    
			TRACKER.ajaxReq(params, function(result){
					TRACKER.pastPointsPageNo =  Number(TRACKER.getPageNo(result));
					TRACKER.pastPointsPageCount =  Number(TRACKER.getPageCount(result));
			
					var str = TRACKER.processUserPastLocationsXML(result);
	
					if (typeof callback == "function") {
						callback();
					}
			
			});
		}
		else {			
			TRACKER.users[userId].polyline.show();
			
			for (var i in TRACKER.users[userId].pastPointsGMarker) { 
				if (TRACKER.users[userId].pastPointsGMarker[i] != null){
					TRACKER.users[userId].pastPointsGMarker[i].show();
				}
			}
		}		
		TRACKER.traceLineDrawedUserId = userId;
	};
	
	this.clearTraceLines = function (userId)
	{
		if (typeof TRACKER.users[userId].polyline != 'undefined')
		{
			TRACKER.users[userId].polyline.hide();
			var len = TRACKER.users[userId].pastPointsGMarker.length;

			for (var i = 1; i < len; i++) { 
				if (TRACKER.users[userId].pastPointsGMarker[i] != null) {
					TRACKER.users[userId].pastPointsGMarker[i].hide();
					TRACKER.users[userId].pastPointsGMarker[i].closeInfoWindow();
				}
			}
		}
	};
	
	this.openMarkerInfoWindow = function(userId){
		TRACKER.users[userId].gmarker.openInfoWindowHtml( '<div>'
														//   + '<b>' + TRACKER.users[userId].username + '</b>'
														   + '<br/>' + TRACKER.langOperator.realname + ": "+TRACKER.users[userId].realname  
														   + '<br/>' + TRACKER.langOperator.time + ": " + TRACKER.users[userId].time
														   + '<br/>' + TRACKER.langOperator.deviceId + ": " + TRACKER.users[userId].deviceId
														   + '<br/>' + TRACKER.langOperator.latitude + ": " + TRACKER.users[userId].latitude  
														   + '<br/>' + TRACKER.langOperator.longitude + ": " + TRACKER.users[userId].longitude
														   +'</div>'
														   + '<ul class="sf-menu"> '
														   		+ '<li>'+'<a class="infoWinOperations" href="javascript:TRACKER.showPointGMarkerInfoWin(1,'+ userId +')">'
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
														   );
	};
	
	this.showImageWindow = function(imageId){
		var image = new Image();
		
		image.src= TRACKER.images[imageId].imageURL + TRACKER.imageOrigSuffix;
		$("#loading").show();
		$(image).load(function(){
			$("#loading").hide();
			
			TRACKER.images[imageId].gmarker.openInfoWindowHtml("<div class='origImageContainer'>"
					+ "<div>"
						+ "<img src='"+ image.src +"' height='"+ image.height +"' width='"+ image.width +"' class='origImage' />"
					+ "</div>"
					+ "<div>"
						+ TRACKER.langOperator.uploader + ": " + "<a href='javascript:TRACKER.trackUser("+ TRACKER.images[imageId].userId +")' class='uploader'>" + TRACKER.images[imageId].realname + "</a>"
						+ "<br/>"
						+ TRACKER.langOperator.time + ": " + TRACKER.images[imageId].time + "<br/>"
						+ TRACKER.langOperator.latitude + ": " + TRACKER.images[imageId].latitude + "<br/>"
						+ TRACKER.langOperator.longitude + ": " + TRACKER.images[imageId].longitude
					+ "</div>"
					+ '<ul class="sf-menu"> '
						+ '<li>'+'<a class="infoWinOperations" href="javascript:TRACKER.zoomPoint('+ TRACKER.images[imageId].latitude +','+ TRACKER.images[imageId].longitude +')">'
				   					    + TRACKER.langOperator.zoom
				   					    +'</a>'+ '</li>'
				   			    + '<li>'+'<a class="infoWinOperations" href="javascript:TRACKER.zoomMaxPoint('+ TRACKER.images[imageId].latitude +','+ TRACKER.images[imageId].longitude +')">'
	   							   	    + TRACKER.langOperator.zoomMax
	   							        +'</a>'+'</li>'
   					+'</li>'
			   + '</ul>'
				+ "</div>");
			
		});		
	};
	this.closeMarkerInfoWindow = function (userId) {
		TRACKER.users[userId].gmarker.closeInfoWindow();
	};
	
	this.zoomPoint = function (latitude, longitude) {
				
		var zoomlevel = MAP.getZoom();
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
		var ltlng = new GLatLng(latitude, longitude);
		
		MAP.setCenter(ltlng, zoomlevel);		
	}
	
	this.zoomMaxPoint = function(latitude, longitude)
	{
		var ltlng = new GLatLng(latitude, longitude);

		if (typeof TRACKER.maxZoomlevel[latitude] == "undefined" ||
			typeof TRACKER.maxZoomlevel[latitude][longitude] == "undefined") 
		{
			TRACKER.maxZoomlevel[latitude] = [];
			G_SATELLITE_MAP.getMaxZoomAtLatLng(ltlng, function(response) {
				if (response && response['status'] == G_GEO_SUCCESS) {
					TRACKER.maxZoomlevel[latitude][longitude] = response['zoom'];					
				}
				MAP.setCenter(ltlng, TRACKER.maxZoomlevel[latitude][longitude]);
				
			});
		}
		else {
			MAP.setCenter(ltlng, TRACKER.maxZoomlevel[latitude][longitude]);
		}
	};
	
	
	/**
	 * this function process the user past locations xml,
	 * it is responsible for creating and updating markers and polylines,
	 * server doesn't return the last available point of user in past locations xml
	 */
	this.processUserPastLocationsXML = function(xml) {
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
	 * this function is used to open info windows of markers when next point or
	 * previous point are clicked.
	 */
	this.showPointGMarkerInfoWin = function(gMarkerIndex, userId){
		if (typeof TRACKER.users[userId].pastPointsGMarker == "undefined" ||	
			typeof TRACKER.users[userId].pastPointsGMarker[gMarkerIndex] == "undefined") 
		{ 
			if (gMarkerIndex == "1") {
				TRACKER.pastPointsPageNo = 0;
			}
			var reqPageNo = TRACKER.pastPointsPageNo + 1;
			TRACKER.drawTraceLine(userId, reqPageNo, function(){
				// if it goes into this if statement it means that there is no available
				// past point in database
				if (typeof TRACKER.users[userId].pastPointsGMarker[gMarkerIndex] == "undefined") {
					// the statement below add a new element to array with null value
					// it is useful when understanding no previous point exists
					// but it is required to check value if it is null when hiding or
					// showing markers...
					TRACKER.users[userId].pastPointsGMarker[gMarkerIndex] = null;
					TRACKER.showInfoBar(TRACKER.langOperator.noMorePastDataAvailable);
				}
				else {
					GEvent.trigger(TRACKER.users[userId].pastPointsGMarker[gMarkerIndex], "click");
				}			
				
			});
		}
		else if (TRACKER.users[userId].pastPointsGMarker[gMarkerIndex] == null){
			TRACKER.showInfoBar(TRACKER.langOperator.noMorePastDataAvailable);
		}
		else {
			if (userId != TRACKER.traceLineDrawedUserId ||
				TRACKER.users[userId].polyline.isHidden() == true) 
			{				
				TRACKER.drawTraceLine(userId);
			}
			GEvent.trigger(TRACKER.users[userId].pastPointsGMarker[gMarkerIndex], "click");			
		}
	}
	/**
	 * 
	 */
	this.processImageXML = function(xml){
		var list = "";
		TRACKER.imageThumbSuffix = decodeURIComponent($(xml).find("page").attr("thumbSuffix"));
		TRACKER.imageOrigSuffix = decodeURIComponent($(xml).find("page").attr("origSuffix"));
		var hideMarker = !($('#showPhotosOnMap').attr('checked'));

		$(xml).find("page").find("image").each(function(){
			var image = $(this);
			var imageId = $(image).attr('id');
			var imageURL =  decodeURIComponent($(image).attr('url'));
			var username = $(image).attr("byUserName");
			var userId = $(image).attr("byUserId");
			var latitude = $(image).attr('latitude');
			var longitude = $(image).attr('longitude');
			var time = $(image).attr('time');
			var point = new GLatLng(latitude, longitude);
			
			list += "<li><a href='javascript:TRACKER.showImageWindow("+ imageId +")' id='image"+ imageId +"'>"
								+ "<div>"
									+ "<img src='"+ imageURL + TRACKER.imageThumbSuffix +"' class='thumbImage' />" 
								+ "</div>"
								+ "<div>"
									+ TRACKER.langOperator.uploader + ": " + username 
									+ "<br/>"
									+ TRACKER.langOperator.time + ": " + time
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
													username:username,
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
	/**
	 * this function process XML returned when actions are search user, get user list, update list,
	 * updated list...
	 */	
	this.processXML = function(xml)
	{
		var list = "";
		$(xml).find("page").find("user").each(function(){
			
			var user = $(this);			
			var userId = $(user).find("Id").text();
//			var username = $(user).find("username").text();
			var realname = $(user).find("realname").text();
			var latitude = $(user).find("location").attr('latitude');
			var longitude = $(user).find("location").attr('longitude');
			var point = new GLatLng(latitude, longitude);
			
			list += "<li><a href='javascript:TRACKER.trackUser("+ userId +")' id='user"+ userId +"'>"+ realname +"</a></li>";
		
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
	 * this a general ajax request function, it is used whenever any ajax request is made 
	 */
	this.ajaxReq = function(params, callback, notShowLoadingInfo)
	{	
			
		$.ajax({
			type: 'POST',
			url: TRACKER.ajaxUrl,
			data: params,
		//	dataType: 'xml',
			timeout:100000,
			beforeSend: function()
						{ 	if (!notShowLoadingInfo) {
								$("#loading").show();
							} 
						},
			success: function(result){ 
							$("#loading").hide(); 						
							if (result == "-4"){
								alert(TRACKER.langOperator.incorrectPassOrUsername);
							}
							else if(result == "-2") {
								alert(TRACKER.langOperator.warningMissingParameter);
							}
							else {
								callback(result);
							}

					}, 
			failure: function(result) {								
					$("#loading").hide();
					alert("Failure in ajax.");						
			},
			error: function(par1, par2, par3){
				//alert(par1.responseText);		
					$("#loading").hide();
					alert("Error in ajax..")
			}
		});
	};	
	
	this.getPageNo = function(xml){		
		return $(xml).find("page").attr("pageNo");
	};
	
	this.getPageCount = function(xml){
		return $(xml).find("page").attr("pageCount");
	};

	/**
	 * this function shows info and then hides it with slide effects
	 */
	this.showInfoBar = function(info) {
		$('#infoBottomBar').text(info).slideDown('slow', function(){
			setTimeout(function(){
				$('#infoBottomBar').slideUp('slow');
			}, 1000);
			
		});
	 }
	this.writePageNumbers = function(pageName, pageCount, currentPage, len)
	{
		var length = 3;
		if (length) {
			length = len;
		}
		var numsStr = "";
		var numsEnd = "";
		var nums = "";
		var start;
		var end;
		if (currentPage - length <= 3) {
			start = 1;
			end = 2 * length + 3;
		}
		else {
			start = currentPage - length;
		}
		
		if ( pageCount - currentPage <= (length + 2) ){
			start = pageCount - (2 * length + 2); 
			end = pageCount; 
		}
		else if ( start != 1 ){
			end = currentPage + length; 
		}
		
		if ( start > 1 )	{
			numsStr += "<a href='" + pageName.replace("%d", "1") + "'>1</a>";
			if (start > 3) {
				numsStr += "<a>...</a>";					
			}
			else
				numsStr += "<a href='" + pageName.replace("%d", "2") + "'>2</a>";	
				
		}
		else if ( start <= 0 ) {
			start = 1;
		}
		
		if ( end < pageCount ) {
			if ( end+2 == pageCount ){
				tmp = end+1;
				numsEnd += "<a href='" + pageName.replace("%d", tmp) + "'>" + tmp + "</a>";
			}
			else
				numsEnd = "<a>...</a>" + numsEnd;	
				
			numsEnd += "<a href='" + pageName.replace("%d", pageCount) + "' >" + pageCount + "</a>";
		}	
		for (var i = start; i <= end; i++)
		{
			if (currentPage == i)
			{
				nums += "<a href='" + pageName.replace("%d", i) + "' id='activePageNo'><b>" + i + "</b></a>";
			}
			else
			{
				nums += "<a href='" + pageName.replace("%d", i) + "'>" + i + "</a>";	
			}
		}
		var result = numsStr + nums + numsEnd;
		var preNext="";
		if (currentPage > 1)
		{
			var pre = Number(currentPage) - 1; 
			preNext = "<a href='" + pageName.replace("%d", pre) + "' id='previousPage'>"+ TRACKER.langOperator.previous +"</a>";
		}
		
		if (currentPage < pageCount)
		{			
			next = Number(currentPage) + 1; 
			preNext +=  " <a href='" + pageName.replace("%d", next) + "' id='nextPage'>" + TRACKER.langOperator.next +"</a>";
		}
		result = preNext + "<br/>" + result;
		
		return "<div class='pageNumbers'>" +  result + "</div>";
	};
}