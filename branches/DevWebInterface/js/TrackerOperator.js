
function TrackerOperator(url, map, fetchPhotosInInitial, interval, qUpdatedUserInterval){

	TRACKER = this;	
	MAP = map;
	this.langOperator;
	this.userId;
	this.preUserId = -1;
	this.language = "en";
	this.ajaxUrl = url;
	this.facebookId = null;
	this.fetchPhotosInInitialization = Number(fetchPhotosInInitial);
	this.updateFriendListPageNo = 1;
	this.updateFriendListPageCount = 0;
	this.pastPointsPageNo = 0;
	//page no initial value is important
	this.bgImageListPageNo = 1;
	this.bgImageListPageCount = 0;
	this.pastPointsPageCount = null;
	this.updateInterval = interval;
	this.timer;
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
	/**
	 * if all users are getted from the server, then this variable is set to true
	 */
	this.friendPageResetCount = Number(0);	
	this.users = [];
	
	this.geofences = [];
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
		var realname;
		var latitude;
		var longitude;
		var friendshipStatus;
		var time;
		var deviceId;
		var message;
		var mapMarker;
		var infoWindowIsOpened = false;
		var polyline = null;		
		var maxZoomLevel = null;
		var statusMessage = null;
		var locationCalculatedTime = null;

		for (var n in arguments[0]) { 
			this[n] = arguments[0][n]; 
		}		
	}
	this.Img = function(){
		var imageId;
		var imageURL;
		var userId;
		var realname; // realname of the user
		var latitude;
		var longitude;
		var time;
		var rating;
		var mapMarker;
		var infoWindowIsOpened = false;
		var description;

		for (var n in arguments[0]) { 
			this[n] = arguments[0][n]; 
		}
	}
	
	this.setLangOperator = function(langOperator) {
		TRACKER.langOperator = langOperator;		
	}

	/**
	 * 
	 */
	this.getFriendList = function(pageNo){

		var jsonparams = "r=users/getUserListJson&page="+ TRACKER.updateFriendListPageNo +"&"; 
		
		if (TRACKER.friendPageResetCount > 0) 
		{
			jsonparams += "list=onlyUpdated";
		}
		
		TRACKER.ajaxReq(jsonparams, function(result){
			
			var obj = $.parseJSON(result);
			processUsers(MAP, obj.userlist);
						
			TRACKER.updateFriendListPageNo = obj.pageNo; //TRACKER.getPageNo(result);
			TRACKER.updateFriendListPageCount = obj.pageCount; //TRACKER.getPageCount(result);
			// to fetched all data reguarly updateFriendListPageNo must be resetted.
			var updateInt = TRACKER.updateInterval;
			
			if (TRACKER.updateFriendListPageNo >= TRACKER.updateFriendListPageCount){
				TRACKER.updateFriendListPageNo = 1;
				TRACKER.updateInterval = TRACKER.queryUpdatedUserInterval;
				TRACKER.friendPageResetCount = Number(TRACKER.friendPageResetCount) + 1;
			}
			else{
				TRACKER.updateFriendListPageNo++;
				TRACKER.updateInterval = TRACKER.getUserListInterval;
			}
			
			TRACKER.timer = setTimeout(TRACKER.getFriendList, TRACKER.updateInterval);			
		}, true);
		
	};

	this.getImageList = function(callback){
		var params = "r=upload/getUploadListXML&pageNo="+ TRACKER.bgImageListPageNo +"&fileType=0&"; 

		if (TRACKER.allImagesFetched == true) {
			params += "list=onlyUpdated";
		}

		TRACKER.ajaxReq(params, function(result){	
			if (result != "") {
				TRACKER.bgImageListPageNo = TRACKER.getPageNo(result);
				TRACKER.bgImageListPageCount = TRACKER.getPageCount(result);
	
				processImageXML(MAP, result);				
				
				if (TRACKER.bgImageListPageNo < TRACKER.bgImageListPageCount){
					TRACKER.bgImageListPageNo = Number(TRACKER.bgImageListPageNo) + 1;
					setTimeout(TRACKER.getImageList, TRACKER.getUserListInterval);
				}	
				else //if (TRACKER.bgImageListPageNo == TRACKER.bgImageListPageCount)
				{
					TRACKER.bgImageListPageNo = 1;
					TRACKER.allImagesFetched = true;
					setTimeout(TRACKER.getImageList, TRACKER.queryUpdatedUserInterval);
				}
				if (typeof callback == 'function'){
					callback();					
				}
			}		
		}, 
		
//		_V_("my_video_2", {}, function(){
//			  // Player (this) is initialized and ready.
//			
//				//var player = this;
//				//player.play();
//			});	
		
		true);	
	}

	this.trackUser = function(userId){
		
		if (typeof TRACKER.users[userId] === "undefined") {
			var params = "r=users/getUserInfoJSON&userId="+ userId +"&"; 
			TRACKER.ajaxReq(params, function(result){
				processUsers(MAP, result);
				if (typeof TRACKER.users[userId]  !== "undefined") {
					TRACKER.trackUser(userId);
				}
				else {
					TRACKER.users[userId] = null;
				}
			});
		}
		else if (TRACKER.users[userId] !== null &&
				 TRACKER.users[userId].friendshipStatus == "1" &&  
				 TRACKER.users[userId].latitude != "" && TRACKER.users[userId].longitude != "") 
		{
			
			var location = new MapStruct.Location({latitude:TRACKER.users[userId].latitude, longitude:TRACKER.users[userId].longitude});
			MAP.panMapTo(location);
			if(TRACKER.preUserId != -1)//Check for the first click in order not to take "undefined" error
			{
				MAP.closeInfoWindow(TRACKER.users[TRACKER.preUserId].mapMarker[0].infoWindow);
				TRACKER.users[userId].infoWindowIsOpened = false;
			}
			MAP.openInfoWindow(TRACKER.users[userId].mapMarker[0].infoWindow, TRACKER.users[userId].mapMarker[0].marker);
			TRACKER.users[userId].infoWindowIsOpened = true;
			TRACKER.preUserId = userId;
		}
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
			if (pageNo > TRACKER.pastPointsPageCount) {
				
			}
			var jsonparams = "r=users/getUserPastPointsJSON&userId=" + userId + "&page=" + pageNo;
			
			TRACKER.ajaxReq(jsonparams, function(result){
				var obj = $.parseJSON(result);

				TRACKER.pastPointsPageNo =  obj.pageNo;
				TRACKER.pastPointsPageCount =  obj.pageCount;
				
				var str = processUserPastLocations(MAP, obj.userwashere, userId);

				if (typeof callback == "function") {
					callback();
				}
			});			
		}
		else {			
			MAP.setPolylineVisibility(TRACKER.users[userId].polyline, true);
			
			for (var i in TRACKER.users[userId].mapMarker) { 
				if (TRACKER.users[userId].mapMarker[i].marker != null){
					MAP.setMarkerVisible(TRACKER.users[userId].mapMarker[i].marker, true);
				}
			}
		}		
		TRACKER.traceLineDrawedUserId = userId;
	};

	this.clearTraceLines = function (userId)
	{
		if (typeof TRACKER.users[userId].polyline != 'undefined')
		{
			MAP.setPolylineVisibility(TRACKER.users[userId].polyline, false);
			var len = TRACKER.users[userId].mapMarker.length;

			for (var i = 1; i < len; i++) { 
				if (TRACKER.users[userId].mapMarker[i].marker != null) {
					MAP.setMarkerVisible(TRACKER.users[userId].mapMarker[i].marker, false);
					MAP.closeInfoWindow(TRACKER.users[userId].mapMarker[i].infoWindow);
					TRACKER.users[userId].infoWindowIsOpened = false;
					
				}
			}
		}
	};
	
	/*
	 * sending GeoFence points to the server
	 */
	this.sendGeoFencePoints = function(name,desc,lat1,long1,lat2,long2,lat3,long3) {
		
			var processOutput = true;
			var params = "r=geofence/sendGeofenceData&name="+name+"&description="+desc+"&point1Latitude="+ lat1.toFixed(6) +"&point1Longitude="+ long1.toFixed(6)+"&point2Latitude="+ lat2.toFixed(6) +"&point2Longitude="+ long2.toFixed(6)+"&point3Latitude="+ lat3.toFixed(6) +"&point3Longitude="+ long3.toFixed(6);
			TRACKER.ajaxReq(params, function(response){			
			var obj = jQuery.parseJSON(response);
			if (obj.result && obj.result == "1") 
			{
			}
			else
			{
				processOutput = false;
				TRACKER.showMessageDialog("A geofence with this name already exists!");
			}
		}, true);
		return processOutput;	
	}


	this.showMediaWindow = function(uploadId){
		if (typeof TRACKER.images[uploadId] == "undefined") {
			TRACKER.getImageList(function(){
				MAP.trigger(TRACKER.images[uploadId].mapMarker.marker, 'click');			
			});
		}
		else {		
			MAP.trigger(TRACKER.images[uploadId].mapMarker.marker, 'click');			
		}		
	};
	this.closeMarkerInfoWindow = function (userId) {
		TRACKER.users[userId].gmarker.closeInfoWindow();
		TRACKER.users[userId].infoWindowIsOpened = false;
	};

	this.zoomPoint = function (latitude, longitude) {
		var point = new MapStruct.Location({latitude:latitude, longitude:longitude});
		MAP.zoomPoint(point);
	}

	this.zoomMaxPoint = function(latitude, longitude)
	{
		var point = new MapStruct.Location({latitude:latitude, longitude:longitude});
		MAP.zoomMaxPoint(point);
	};


	/**
	 * this function is used to open info windows of markers when next point or
	 * previous point are clicked.
	 */
	this.showPointGMarkerInfoWin = function(currentMarkerIndex,nextMarkerIndex, userId){
		
		if (typeof TRACKER.users[userId].mapMarker == "undefined" ||	
				typeof TRACKER.users[userId].mapMarker[nextMarkerIndex] == "undefined") 
		{ 
			if (nextMarkerIndex == "1") {
				TRACKER.pastPointsPageNo = 0;
			}
			var reqPageNo = Number(TRACKER.pastPointsPageNo) + 1;
			
			if (TRACKER.pastPointsPageCount == null || reqPageNo <= TRACKER.pastPointsPageCount) {
				TRACKER.drawTraceLine(userId, reqPageNo, function(){
					// if it goes into this if statement it means that there is no available
					// past point in database
					if (typeof TRACKER.users[userId].mapMarker[nextMarkerIndex] == "undefined") {
						// the statement below add a new element to array with null value
						// it is useful when understanding no previous point exists
						// but it is required to check value if it is null when hiding or
						// showing markers...
						TRACKER.users[userId].mapMarker[nextMarkerIndex] = null;
					}
					else {
						MAP.closeInfoWindow(TRACKER.users[userId].mapMarker[currentMarkerIndex].infoWindow);
						TRACKER.users[userId].infoWindowIsOpened = false;
						MAP.trigger(TRACKER.users[userId].mapMarker[nextMarkerIndex].marker, 'click');
					}			

				});
			}
			else {
				TRACKER.showInfoBar(TRACKER.langOperator.noMorePastDataAvailable);
				
			}
		}
		else if (TRACKER.users[userId].mapMarker[nextMarkerIndex] == null){
			TRACKER.showInfoBar(TRACKER.langOperator.noMorePastDataAvailable);
		}
		else {
			if (nextMarkerIndex == "1") {
				nextMarkerIndex = 0;
			}			
			
			if (userId != TRACKER.traceLineDrawedUserId ) // ||
			{				
				TRACKER.drawTraceLine(userId);
			}
			if (typeof TRACKER.users[userId].polyline != 'undefined')
			{
				MAP.setPolylineVisibility(TRACKER.users[userId].polyline, true);
			}
			MAP.closeInfoWindow(TRACKER.users[userId].mapMarker[currentMarkerIndex].infoWindow);
			TRACKER.users[userId].infoWindowIsOpened = false;
			MAP.trigger(TRACKER.users[userId].mapMarker[nextMarkerIndex].marker, "click");			
		}
	}

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
	
	this.showMessageDialog = function(message) {
		$("#messageDialogText").html(message); 
		$("#messageDialog").dialog("open"); 
	}
	
	this.showConfirmationDialog = function(question, callback){
		$("#confirmationDialog #question").html(question); 
		var buttons = $("#confirmationDialog").dialog( "option", "buttons" );
		// dont forget first button is positivie button so below loop works
		for(var property in buttons) {
			buttons[property] = callback;
			break;
		}
		//buttons.OK = callback;
		$("#confirmationDialog").dialog("option","buttons",buttons);
		$("#confirmationDialog").dialog("open");	
	}
	this.closeConfirmationDialog = function(){
		$("#confirmationDialog").dialog("close");
	}
}
