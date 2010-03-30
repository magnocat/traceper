
function TrackerOperator(url, map, interval, qUpdatedUserInterval, langOp){
	
	TRACKER = this;	
	MAP = map;
	this.langOperator = langOp;
	this.language = "en";
	this.ajaxUrl = url;
	this.actionAuthenticateUser = "WebClientAuthenticateUser";
	this.actionGetUserList = "WebClientGetUserList";	
	this.actionSearchUser = "WebClientSearchUser";
	this.actionUpdateUserList = "WebClientUpdateUserList";
	this.actionGetUpdatedUserList = "WebClientGetUpdatedUserList";
	this.actionGetUserPastPoints = "WebClientGetUserPastPoints";
	this.userListPageNo = 1;	
	this.userListPageCount = 0;
	this.updateUserListPageNo = 1;
	this.updateUserListPageCount = 0;
	this.searchPageNo = 1;
	this.searchPageCount = 0;
	this.pastPointsPageNo = 0;
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
	this.trackedUserId = 0;
	/**
	 * if all users are getted from the server, then this variable is set to true
	 */
    this.initializationCompleted = false;	
	this.users = [];
	
	this.User = function(){
		var username;
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
	
	this.authenticateUser = function(username, password)
	{
		var params = "action=" + TRACKER.actionAuthenticateUser + "&username=" + username + "&password=" + password;
		TRACKER.ajaxReq(params, function (result){
			if (result == "1") {
				//TODO: doing simultaneous ajax requests 
				//TRACKER.getLocations();
				//TRACKER.getUserList(1);
			}
		});
	};
	
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
		if (TRACKER.initializationCompleted == true) {
			params = "action=" + TRACKER.actionGetUpdatedUserList + "&pageNo=" + TRACKER.updateUserListPageNo;
		}
		else {
			params = "action=" + TRACKER.actionUpdateUserList + "&pageNo=" + TRACKER.updateUserListPageNo; 
			
		}
		
		if (TRACKER.trackedUserId != 0)
		{
			params+= "&trackedUser=" + TRACKER.trackedUserId;
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
				TRACKER.initializationCompleted = true;
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
				
				$('#lists .title').html(TRACKER.langOperator.searchResultsTitle);
				$('#users').slideUp();

				$('#search').slideUp('fast',function(){
						$('#search #results').html(str);
						$('#search').slideDown();
				});
			});
		}
		else {
			alert(TRACKER.langOperator.searchStringIsTooShort);
		}	
	};	
	
	this.trackUser = function(userId){
		MAP.panTo(new GLatLng(TRACKER.users[userId].latitude, TRACKER.users[userId].longitude));
		TRACKER.openMarkerInfoWindow(userId);
		
		$('#user' + TRACKER.trackedUserId).removeClass('trackedUser');
		if (TRACKER.trackedUserId == userId) {
			TRACKER.trackedUserId = 0;			
		}
		else {
			TRACKER.trackedUserId = userId;
		
		}
		$('#user'+ TRACKER.trackedUserId ).addClass('trackedUser');
		
	};
	
	this.drawTraceLine = function(userId, pageNo, callback) 
	{
		// hide any polyline if it is drawed
		if (TRACKER.traceLineDrawedUserId != null &&
			userId != TRACKER.traceLineDrawedUserId &&
			typeof TRACKER.users[TRACKER.traceLineDrawedUserId].polyline != "undefined")
		{			
			TRACKER.users[TRACKER.traceLineDrawedUserId].polyline.hide();
			var len = TRACKER.users[TRACKER.traceLineDrawedUserId].pastPointsGMarker.length;
			
			for (var i = 1; i < len; i++) { 
				if (TRACKER.users[TRACKER.traceLineDrawedUserId].pastPointsGMarker[i] != null) {
					TRACKER.users[TRACKER.traceLineDrawedUserId].pastPointsGMarker[i].hide();
				}
			}
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
	}
	
	this.openMarkerInfoWindow = function(userId){
		TRACKER.users[userId].gmarker.openInfoWindowHtml( '<div>'
														   + '<b>' + TRACKER.users[userId].username + '</b>'
														   + '<br/>' + TRACKER.langOperator.realname + ": "+TRACKER.users[userId].realname  
														   + '<br/>' + TRACKER.langOperator.time + ": " + TRACKER.users[userId].time
														   + '<br/>' + TRACKER.langOperator.deviceId + ": " + TRACKER.users[userId].deviceId
														   + '<br/>' + TRACKER.langOperator.latitude + ": " + TRACKER.users[userId].latitude  
														   + '<br/>' + TRACKER.langOperator.longitude + ": " + TRACKER.users[userId].longitude
														   +'</div>'
														   + '<div style="float:right">'
																+'<a class="infoWinOperations" href="javascript:TRACKER.showPointGMarkerInfoWin(1,'+ userId +')">'
										   							+ TRACKER.langOperator.previousPoint 
										   						+'</a>'
										   						+'|'	
														   		+'<a class="infoWinOperations" href="javascript:TRACKER.zoomPoint('+ TRACKER.users[userId].latitude +','+ TRACKER.users[userId].longitude +')">'
														   			+ TRACKER.langOperator.zoom 
														   		+'</a>'														   		
														   		+'<a class="infoWinOperations" href="javascript:TRACKER.zoomMaxPoint('+ TRACKER.users[userId].latitude +','+ TRACKER.users[userId].longitude +')">'
														   			+'(' + TRACKER.langOperator.zoomMax
														   			+')'
														   		+'</a>'
													   			
														   +'</div>'
														   );
	}
	
	this.closeMarkerInfoWindow = function (userId) {
		TRACKER.users[userId].gmarker.closeInfoWindow();
	}
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
	}
	
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
			var previousGMarkerIndex = index + 1; // it is reverse because 
			var nextGMarkerIndex = index - 1;    // as index decreases, the current point gets closer
			GEvent.addListener(gmarker, "click", function() {
					gmarker.openInfoWindowHtml("<div>" 
												  + "<b>" + TRACKER.users[userId].username + "</b> " 
												  + TRACKER.langOperator.wasHere 
												  + '<br/>' + TRACKER.langOperator.time + ": " + time
												  + '<br/>' + TRACKER.langOperator.deviceId + ": " + deviceId
												+ "</div>"
												+ "<div style='float:right'>"
													+'<a class="infoWinOperations" href="javascript:TRACKER.showPointGMarkerInfoWin('+ previousGMarkerIndex +','+ userId +')">'
									   					+ TRACKER.langOperator.previousPoint 
									   				+'</a>'
									   				+'|'
									   				+'<a class="infoWinOperations" href="javascript:TRACKER.zoomPoint('+ latitude +','+ longitude +')">'
										   				+ TRACKER.langOperator.zoom 
										   			+'</a>'														   		
										   			+'<a class="infoWinOperations" href="javascript:TRACKER.zoomMaxPoint('+ latitude +','+ longitude +')">'
										   				+'(' + TRACKER.langOperator.zoomMax
										   				+')'
										   			+'</a>'
										   			+'|'
									   				+'<a class="infoWinOperations" href="javascript:TRACKER.showPointGMarkerInfoWin('+ nextGMarkerIndex +','+ userId +')">'
								   						+ TRACKER.langOperator.nextPoint 
								   					+'</a>'
									   
												+"</div>");	
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
			typeof  TRACKER.users[userId].pastPointsGMarker[gMarkerIndex] == "undefined") 
		{ 
			var reqPageNo = TRACKER.pastPointsPageNo + 1;
			TRACKER.drawTraceLine(userId, reqPageNo, function(){
				// if it goes into this if statement it means that there is no available
				// past point in database
				if (typeof TRACKER.users[userId].pastPointsGMarker[gMarkerIndex] == "undefined") {
					// the statement below add a new element to array with null value
					// it is useful when understand there is no previous point 
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
			if (userId != TRACKER.traceLineDrawedUserId) {
				TRACKER.drawTraceLine(userId);
			}
			GEvent.trigger(TRACKER.users[userId].pastPointsGMarker[gMarkerIndex], "click");
			
		}
	}
	/*
	 * 
	 */
	this.showInfoBar = function(info) {
		$('#infoBottomBar').text(info).slideDown('slow', function(){
			setTimeout(function(){
				$('#infoBottomBar').slideUp('slow');
			}, 1000);
			
		});
	}
	
	this.processXML = function(xml)
	{
		var list = "";
		$(xml).find("page").find("user").each(function(){
			
			var user = $(this);			
			var userId = $(user).find("Id").text();
			var username = $(user).find("username").text();
			var latitude = $(user).find("location").attr('latitude');
			var longitude = $(user).find("location").attr('longitude');
			var point = new GLatLng(latitude, longitude);
			
			list += "<li><a href='javascript:TRACKER.trackUser("+ userId +")' id='user"+ userId +"'>"+ username +"</a></li>";
		
			if (typeof TRACKER.users[userId] == "undefined") 
			{		
				var personIcon = new GIcon(G_DEFAULT_ICON);
				personIcon.image = "images/person.png";
				personIcon.iconSize = new GSize(32,32);
				personIcon.shadow = null;
				markerOptions = { icon:personIcon };
				

			
				TRACKER.users[userId] = new TRACKER.User( {username:username,
														   realname:$(user).find("realname").text(),
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
				var latitude = $(user).find("location").attr('latitude');
				var longitude = $(user).find("location").attr('longitude');
				var time = $(user).find("time").text();
				var deviceId = $(user).find("deviceId").text();
				var point = new GLatLng(latitude, longitude);
				
				TRACKER.users[userId].gmarker.setLatLng(point);					
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
	
	
	
	
	this.ajaxReq = function(params, callback, notShowLoadingInfo)
	{	
			
		$.ajax({
			type: 'POST',
			url: TRACKER.ajaxUrl,
			data: params,
			dataType: 'xml',
			timeout:100000,
			beforeSend: function()
						{ 	if (!notShowLoadingInfo) {
								$("#loading").show();
							} 
						},
			success: function(result){ 
							$("#loading").hide(); 							
							callback(result); 
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
		if (currentPage > 1)
		{
			var pre = currentPage - 1; 
			result = "<a href='" + pageName.replace("%d", pre) + "' id='previousPage'></a>" + result;
		}
		
		if (currentPage < pageCount)
		{			
			next = currentPage + 1; 
			result +=  " <a href='" + pageName.replace("%d", next) + "' id='nextPage'></a>";
		}
		
		return "<div class='pageNumbers'>" +  result + "</div>";
	};
}