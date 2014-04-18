
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
	this.pastPointsPageNo = []; //this.pastPointsPageNo = 0;
	//page no initial value is important
	this.bgImageListPageNo = 1;
	this.bgImageListPageCount = 0;
	this.pastPointsPageCount = []; //this.pastPointsPageCount = null;
	this.bAllLinesCleared = [];
	this.updateInterval = interval;
	this.timer;
	this.imageTimer;
	this.traceLineDrawedUserId = null;
	this.showImagesOnTheMap = true;
	this.showUsersOnTheMap = false;
	
	this.monthNames = [];
	
	this.showImagesOnTheMapJustToggled = false;
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
		var address;
		var friendshipStatus;
		var time;
		var locationTimeStamp;
		var deviceId;
		var message;
		var mapMarker;
		var infoWindowIsOpened = false;
		var polyline = null;		
		var maxZoomLevel = null;
		var statusMessage = null;
		var locationCalculatedTime = null;
		var locationSource = null;

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
		
		this.monthNames = [this.langOperator.jan, this.langOperator.feb, this.langOperator.mar, this.langOperator.apr, 
		                   this.langOperator.may, this.langOperator.jun, this.langOperator.jul, this.langOperator.aug, 
		                   this.langOperator.sep, this.langOperator.oct, this.langOperator.nov, this.langOperator.dec];
	}
	
	this.updateFriendListWithDeletion = function(deletedFriendId){
		
//		var allKeys = "";
//		
//		for (var key in TRACKER.users) {
//			allKeys += key + " ";
//		}
//		
//		alertMsg("allKeys BEFORE deletion: " + allKeys);		
		
		MAP.setMarkerVisible(TRACKER.users[deletedFriendId].mapMarker[0].marker, false);
		//alertMsg("setMarkerVisible(false) for deletedFriendId:" + deletedFriendId);
		
		if(TRACKER.users[deletedFriendId].infoWindowIsOpened)
		{
			MAP.closeInfoWindow(TRACKER.users[deletedFriendId].mapMarker[0].infoWindow)			
		}
		
		if(TRACKER.preUserId == deletedFriendId)
		{
			TRACKER.preUserId = -1;
		}
		
		delete TRACKER.users[deletedFriendId];
		
//		allKeys = "";
//		
//		for (var key in TRACKER.users) {
//			allKeys += key + " ";
//		}
//		
//		alertMsg("allKeys AFTER deletion: " + allKeys);		
	}
	
	this.updateImageListWithDeletion = function(deletedImageId){
		
//		var allKeys = "";
//		
//		for (var key in TRACKER.images) {
//			allKeys += key + " ";
//		}
//		
//		alertMsg("allKeys BEFORE deletion: " + allKeys);		
		
		MAP.setMarkerVisible(TRACKER.images[deletedImageId].mapMarker.marker, false);
		//alertMsg("setMarkerVisible(false) for deletedImageId:" + deletedImageId);
		
		if(TRACKER.images[deletedImageId].infoWindowIsOpened)
		{
			MAP.closeInfoWindow(TRACKER.images[deletedImageId].mapMarker.infoWindow)			
		}
		
		delete TRACKER.images[deletedImageId];
		
//		allKeys = "";
//		
//		for (var key in TRACKER.images) {
//			allKeys += key + " ";
//		}
//		
//		alertMsg("allKeys AFTER deletion: " + allKeys);		
	}
	
	var exceptionForParamsMap = {'getUserListJson':0, 'getPublicUploadListJson':0, 'getUploadListJson':0, 'getUserPastPointsJSON':0, 'sendGeofenceData':0};

	/**
	 * 
	 */
	this.getFriendList = function(pageNo, userType, newFriendId, deletedFriendId){

		//alertMsg('getFriendList() called');
		
		//Default value implementation in JS
		//newFriendId = typeof newFriendId !== 'undefined' ? newFriendId : null;
		//deletedFriendId = typeof deletedFriendId !== 'undefined' ? deletedFriendId : null;
		
		var jsonparams;
		
		//Normalde userType hep gonderiliyor, fakat undefined gelebildigi goruldugu icin boyle bir onlem koyuldu
		if(typeof userType !== 'undefined')
		{
			jsonparams = "r=users/getUserListJson&pageNo=" + TRACKER.updateFriendListPageNo + "&userType=" + userType; 
		}
		else
		{
			jsonparams = "r=users/getUserListJson&pageNo=" + TRACKER.updateFriendListPageNo + "&userType=0";
		}
		
		//jsonparams = "r=users/getUserListJson&pageNo=" + TRACKER.updateFriendListPageNo + "&userType=" + userType + "&"; 
		
		if (TRACKER.friendPageResetCount > 0) 
		{
			jsonparams += "&list=onlyUpdated";
		}
		
		//if(newFriendId != null)
		if(typeof newFriendId !== 'undefined')
		{
			jsonparams += "&newFriendId=" + newFriendId;
		}
		
		TRACKER.ajaxReq(jsonparams, function(obj){			
			try
			{
				//var obj = $.parseJSON(result);

				if(typeof deletedFriendId !== 'undefined')
				{
					processUsers(MAP, obj.userlist, obj.currentUser, obj.updateType, deletedFriendId);
				}
				else
				{
					processUsers(MAP, obj.userlist, obj.currentUser, obj.updateType);
				}
				
				TRACKER.updateFriendListPageNo = obj.pageNo; //TRACKER.getPageNo(result);
				TRACKER.updateFriendListPageCount = obj.pageCount; //TRACKER.getPageCount(result);
				// to fetched all data reguarly updateFriendListPageNo must be resetted.
				var updateInt = TRACKER.updateInterval;
				
				//alertMsg("PageNo:" + TRACKER.updateFriendListPageNo + " / PageCount:" + TRACKER.updateFriendListPageCount);
				
				if (TRACKER.updateFriendListPageNo >= TRACKER.updateFriendListPageCount){
					TRACKER.updateFriendListPageNo = 1;
					//TRACKER.updateInterval = TRACKER.queryUpdatedUserInterval;
					TRACKER.friendPageResetCount = Number(TRACKER.friendPageResetCount) + 1;
				}
				else{
					TRACKER.updateFriendListPageNo++;
					TRACKER.updateInterval = TRACKER.getUserListInterval;
				}
				
				clearTimeout(TRACKER.timer);
				//TRACKER.timer = setTimeout(TRACKER.getFriendList, TRACKER.updateInterval);
				//TRACKER.timer = setTimeout(function() {TRACKER.getFriendList(pageNo, userType, newFriendId, deletedFriendId);}, TRACKER.updateInterval);
				
				//newFriendId ve deletedFriendId parametreleri sadece bu islemler yapildiginda userList.php tarafindan verilmeli diger durumlarda verilmemeli
				TRACKER.timer = setTimeout(function() {TRACKER.getFriendList(pageNo, userType);}, TRACKER.updateInterval);
				
				if(exceptionForParamsMap['getUserListJson'] > 5)
				{
					  var data = "r=site/ajaxEmailNotification&title=Javascript Exception Recovered&message=" +
					  
					  "User Info: <br/><br/>" + 
					  "User Browser: " + BrowserDetect.browser + " " + BrowserDetect.version + "<br/>" +
					  "User OS: " + BrowserDetect.OS + "<br/><br/>" +
					  "'getUserListJson' javascript exception is recovered at " + exceptionForParamsMap['getUserListJson'] + ". trial." + "<br/>" +
					  "&params=" + 'getUserListJsonRecovery'; 

					  //If the javascript exception is recovered, report this situation via e-mail
					  TRACKER.ajaxReq(data, null, true);					
				}				
				
				exceptionForParamsMap['getUserListJson'] = 0;				
			}
			catch(error)
			{
				alertMsg('Exception in jsonparams: ' + jsonparams + '\n' + 
					  'Error: ' + error.message + '\n' + 
					  'JSON obj: ' + JSON.stringify(obj));
				
				exceptionForParamsMap['getUserListJson'] = exceptionForParamsMap['getUserListJson'] + 1;
				
				if(exceptionForParamsMap['getUserListJson'] < 5)
				{
					//Try the function call again after 1 second
					clearTimeout(TRACKER.timer);  
					TRACKER.timer = setTimeout(function() {TRACKER.getFriendList(pageNo, userType, newFriendId, deletedFriendId);}, 1000);					
				}
				else if(exceptionForParamsMap['getUserListJson'] == 5)
				{
					  var data = "r=site/ajaxEmailNotification&title=Javascript Exception Occured!&message=" +
					  
					  "Exception Info: <br/><br/>" + 
					  "User Browser: " + BrowserDetect.browser + " " + BrowserDetect.version + "<br/>" +
					  "User OS: " + BrowserDetect.OS + "<br/><br/>" +
					  "Exception in jsonparams: " + jsonparams + "<br/>" +
					  "Error: " + error.message + "<br/>" +
					  "JSON obj: " + JSON.stringify(obj) + "&params=" + "getUserListJson"; 

					  //If 5 consecutive ajax queries are erroneous, report this situation via e-mail
					  TRACKER.ajaxReq(data, null, true);					
				}
				else //exceptionForParamsMap['getUserListJson'] > 5
				{
					//For the error case check connection for every 10 seconds
					clearTimeout(TRACKER.timer);  
					TRACKER.timer = setTimeout(function() {TRACKER.getFriendList(pageNo, userType, newFriendId, deletedFriendId);}, 10000);					
				}
			}			
		}, true);		
	};
	
	this.getImageList = function(isPublic, updateAll, callback){
		var jsonparams;
		var exceptionForParamsMapKey;
		
		//alertMsg("getImageList() called");
		
		//alertMsg("TRACKER.bgImageListPageNo:" + TRACKER.bgImageListPageNo);

		if((typeof isPublic !== 'undefined') && (isPublic == true))
		{
			jsonparams = "r=upload/getPublicUploadListJson&pageNo="+ TRACKER.bgImageListPageNo +"&fileType=0";
			exceptionForParamsMapKey = 'getPublicUploadListJson';
		}
		else
		{
			jsonparams = "r=upload/getUploadListJson&pageNo="+ TRACKER.bgImageListPageNo +"&fileType=0";
			exceptionForParamsMapKey = 'getUploadListJson';
		}
		
		if((typeof updateAll !== 'undefined') && (updateAll == true))
		{
			//Do not add "list=onlyUpdated"
			TRACKER.bgImageListPageNo = 1;
			TRACKER.allImagesFetched = false;
		}
		else if (TRACKER.allImagesFetched == true) {
			jsonparams += "&list=onlyUpdated";
			
			//alertMsg("onlyUpdated");
		}
		else
		{
			//alertMsg("All");
		}		

		TRACKER.showImagesOnTheMapJustToggled = false;
		
		//alertMsg("getImageList(), jsonparams: " + jsonparams);

		TRACKER.ajaxReq(jsonparams, function(obj){			
			try
			{
				//var obj = $.parseJSON(result);
				//alertMsg("After parseJSON()");
				
				TRACKER.bgImageListPageNo = obj.pageNo; //TRACKER.getPageNo(result);
				TRACKER.bgImageListPageCount = obj.pageCount; //TRACKER.getPageCount(result);
				
				//alertMsg("pageNo:" + TRACKER.bgImageListPageNo + " - pageCount:" + TRACKER.bgImageListPageCount);

				processUploads(MAP, obj.deletedlist, obj.uploadlist, obj.updateType, obj.thumbSuffix, isPublic);
				
				//alertMsg("processImageXML() called");
				
				if (TRACKER.bgImageListPageNo < TRACKER.bgImageListPageCount){
					TRACKER.bgImageListPageNo = Number(TRACKER.bgImageListPageNo) + 1;
					clearTimeout(TRACKER.imageTimer);
					TRACKER.imageTimer = setTimeout(function(){TRACKER.getImageList(isPublic, false)}, TRACKER.getUserListInterval);
				}	
				else //if (TRACKER.bgImageListPageNo == TRACKER.bgImageListPageCount)
				{
					TRACKER.bgImageListPageNo = 1;
					TRACKER.allImagesFetched = true;
					//alertMsg('allImagesFetched');
					clearTimeout(TRACKER.imageTimer);
					TRACKER.imageTimer = setTimeout(function(){TRACKER.getImageList(isPublic, false)}, TRACKER.queryUpdatedUserInterval);
					
					//alertMsg("allImagesFetched: " + TRACKER.allImagesFetched);
				}
				
				if (typeof callback == 'function'){
					callback();					
				}			
				
//				if (result != "") {
//					TRACKER.bgImageListPageNo = TRACKER.getPageNo(result);
//					TRACKER.bgImageListPageCount = TRACKER.getPageCount(result);
//					
//					//alertMsg("pageNo:" + TRACKER.bgImageListPageNo + " - pageCount:" + TRACKER.bgImageListPageCount);
	//	
//					processImageXML(MAP, result);
//					
//					//alertMsg("processImageXML() called");
//					
//					if (TRACKER.bgImageListPageNo < TRACKER.bgImageListPageCount){
//						TRACKER.bgImageListPageNo = Number(TRACKER.bgImageListPageNo) + 1;
//						clearTimeout(TRACKER.imageTimer);
//						TRACKER.imageTimer = setTimeout(function(){TRACKER.getImageList(isPublic)}, TRACKER.getUserListInterval);
//					}	
//					else //if (TRACKER.bgImageListPageNo == TRACKER.bgImageListPageCount)
//					{
//						TRACKER.bgImageListPageNo = 1;
//						TRACKER.allImagesFetched = true;
//						clearTimeout(TRACKER.imageTimer);
//						TRACKER.imageTimer = setTimeout(function(){TRACKER.getImageList(isPublic)}, TRACKER.queryUpdatedUserInterval);
//					}
//					if (typeof callback == 'function'){
//						callback();					
//					}
//				}
				
				if(exceptionForParamsMap[exceptionForParamsMapKey] > 5)
				{
					  var data = "r=site/ajaxEmailNotification&title=Javascript Exception Recovered&message=" +
					  
					  "User Info: <br/><br/>" + 
					  "User Browser: " + BrowserDetect.browser + " " + BrowserDetect.version + "<br/>" +
					  "User OS: " + BrowserDetect.OS + "<br/><br/>" +
					  exceptionForParamsMapKey + " javascript exception is recovered at " + exceptionForParamsMap[exceptionForParamsMapKey] + ". trial." + "<br/>" +
					  "&params=" + exceptionForParamsMapKey + "Recovery"; 

					  //If the javascript exception is recovered, report this situation via e-mail
					  TRACKER.ajaxReq(data, null, true);					
				}				
				
				exceptionForParamsMap[exceptionForParamsMapKey] = 0;				
			}
			catch(error)
			{
				alertMsg('Exception in jsonparams: ' + jsonparams + '\n' + 
						  'Error: ' + error.message + '\n' + 
						  'JSON obj: ' + JSON.stringify(obj));
					
				exceptionForParamsMap[exceptionForParamsMapKey] = exceptionForParamsMap[exceptionForParamsMapKey] + 1;
				
				if(exceptionForParamsMap[exceptionForParamsMapKey] < 5)
				{
					//Try the function call again after 1 second
					clearTimeout(TRACKER.imageTimer);  
					TRACKER.imageTimer = setTimeout(function() {TRACKER.getImageList(isPublic, updateAll, callback);}, 1000);					
				}
				else if(exceptionForParamsMap[exceptionForParamsMapKey] == 5)
				{
					  var data = "r=site/ajaxEmailNotification&title=Javascript Exception Occured!&message=" +
					  
					  "Exception Info: <br/><br/>" + 
					  "User Browser: " + BrowserDetect.browser + " " + BrowserDetect.version + "<br/>" +
					  "User OS: " + BrowserDetect.OS + "<br/><br/>" +
					  "Exception in jsonparams: " + jsonparams + "<br/>" +
					  "Error: " + error.message + "<br/>" +
					  "JSON obj: " + JSON.stringify(obj) + "&params=" + exceptionForParamsMapKey; 

					  //If 5 consecutive ajax queries are erroneous, report this situation via e-mail
					  TRACKER.ajaxReq(data, null, true);					
				}
				else //exceptionForParamsMap[exceptionForParamsMapKey] > 5
				{
					//For the error case check connection for every 10 seconds
					clearTimeout(TRACKER.imageTimer);  
					TRACKER.imageTimer = setTimeout(function() {TRACKER.getImageList(isPublic, updateAll, callback);}, 10000);					
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

//	this.getImageList = function(isPublic, updateAll, callback){
//		var params; 
//		
//		if((typeof isPublic !== 'undefined') && (isPublic == true))
//		{
//			params = "r=upload/getPublicUploadListXML&pageNo="+ TRACKER.bgImageListPageNo +"&fileType=0&";
//		}
//		else
//		{
//			params = "r=upload/getUploadListXML&pageNo="+ TRACKER.bgImageListPageNo +"&fileType=0&"; 
//		}
//
//		if((typeof updateAll !== 'undefined') && (updateAll == true))
//		{
//			//Do not add "list=onlyUpdated"
//		}
//		else if (TRACKER.allImagesFetched == true) {
//			params += "list=onlyUpdated";
//			
//			//alertMsg("onlyUpdated");
//		}
//		else
//		{
//			//alertMsg("All");
//		}
//		
//		TRACKER.showImagesOnTheMapJustToggled = false;
//		
//		//alertMsg("getImageList() called");
//
//		TRACKER.ajaxReq(params, function(result){	
//			if (result != "") {
//				TRACKER.bgImageListPageNo = TRACKER.getPageNo(result);
//				TRACKER.bgImageListPageCount = TRACKER.getPageCount(result);
//				
//				//alertMsg("pageNo:" + TRACKER.bgImageListPageNo + " - pageCount:" + TRACKER.bgImageListPageCount);
//	
//				processImageXML(MAP, result);
//				
//				//alertMsg("processImageXML() called");
//				
//				if (TRACKER.bgImageListPageNo < TRACKER.bgImageListPageCount){
//					TRACKER.bgImageListPageNo = Number(TRACKER.bgImageListPageNo) + 1;
//					clearTimeout(TRACKER.imageTimer);
//					TRACKER.imageTimer = setTimeout(function(){TRACKER.getImageList(isPublic)}, TRACKER.getUserListInterval);
//				}	
//				else //if (TRACKER.bgImageListPageNo == TRACKER.bgImageListPageCount)
//				{
//					TRACKER.bgImageListPageNo = 1;
//					TRACKER.allImagesFetched = true;
//					clearTimeout(TRACKER.imageTimer);
//					TRACKER.imageTimer = setTimeout(function(){TRACKER.getImageList(isPublic)}, TRACKER.queryUpdatedUserInterval);
//				}
//				if (typeof callback == 'function'){
//					callback();					
//				}
//			}		
//		}, 
//		
////		_V_("my_video_2", {}, function(){
////			  // Player (this) is initialized and ready.
////			
////				//var player = this;
////				//player.play();
////			});	
//		
//		true);	
//	}	

	this.trackUser = function(userId){
		//String olarak vermezse bulmuyor
		if(locationlessUserIdArray.indexOf(userId.toString()) != -1)
		{
			if(currentUserId == userId)
			{
				this.showLongMessageDialog(this.langOperator.youHaveNoValidLocationInfo);
			}
			else
			{
				this.showLongMessageDialog(this.langOperator.yourFriendHasNoValidLocationInfo);
			}			
		}
		else
		{
			if (typeof this.users[userId] === "undefined") {
				alertMsg("Id:" + userId + " is undefined");
				
				var params = "r=users/getUserInfoJSON&userId="+ userId +"&"; 
				this.ajaxReq(params, function(result){
					processUsers(MAP, result);
					if (typeof this.users[userId]  !== "undefined") {
						this.trackUser(userId);
					}
					else {
						this.users[userId] = null;
					}
				}, true);
			}
			else if (this.users[userId] !== null &&
					this.users[userId].friendshipStatus == "1") 
			{
				//alertMsg("else if");

				//if((TRACKER.users[userId].locationCalculatedTime.indexOf(" 1970 ") != -1) || (TRACKER.users[userId].locationCalculatedTime == ""))
				if(this.users[userId].locationSource == "0")	
				{
					if(currentUserId == userId)
					{
						this.showLongMessageDialog(this.langOperator.yourLocationInfoNotReliable);
					}
					else
					{
						this.showLongMessageDialog(this.langOperator.yourFriendsLocationInfoNotReliable);
					}
				}
							
				var location = new MapStruct.Location({latitude:this.users[userId].latitude, longitude:this.users[userId].longitude});
				MAP.panMapTo(location);
				
				//Kisi kendisi sildiyse preUserId -1'e cekiliyor ve bu kontrol edilmeli
				if(this.preUserId != -1)//Check for the first click in order not to take "undefined" error
				{
					//alertMsg("TRACKER.users[TRACKER.preUserId]: " + TRACKER.users[TRACKER.preUserId]);
					
					//Kullanici bilgi disinda arkadasliktan ciktiysa veya konumunu kapattiysa diye "undefined" kontrolu de yapilmali
					if((typeof this.users[TRACKER.preUserId] == "undefined") || (this.users[TRACKER.preUserId] == "undefined"))
					{
						//Do not take action
					}
					else
					{
						MAP.closeInfoWindow(this.users[this.preUserId].mapMarker[0].infoWindow);
						this.users[this.preUserId].infoWindowIsOpened = false;					
					}
				}
				MAP.openInfoWindow(this.users[userId].mapMarker[0].infoWindow, this.users[userId].mapMarker[0].marker);
				this.users[userId].infoWindowIsOpened = true;
				this.preUserId = userId;
			}
			else
			{
				alertMsg("else, TRACKER.users[userId]:" + TRACKER.users[userId]);
			}			
		}
	};

	this.drawTraceLine = function(userId, pageNo, callback) 
	{
		//alert("drawTraceLine() called with pageNo:" + pageNo);
		
		// hide any polyline if it is drawed
		if (TRACKER.traceLineDrawedUserId != null &&
				//userId != TRACKER.traceLineDrawedUserId &&
				typeof TRACKER.users[TRACKER.traceLineDrawedUserId].polyline != "undefined")
		{			
			//TRACKER.clearTraceLines(TRACKER.traceLineDrawedUserId);
			
			//alert("drawTraceLine - 1");
		}
		
		if(TRACKER.pastPointsPageNo[userId] == undefined)
		{
			TRACKER.pastPointsPageNo[userId] = 0;
		}		

		if (typeof TRACKER.users[userId].polyline == "undefined" ||
				pageNo > TRACKER.pastPointsPageNo[userId] ) 
		{
			//alert("drawTraceLine - 2 if(undefined) - pastPointsPageNo[userId]:" + TRACKER.pastPointsPageNo[userId]);
			
//			if (pageNo > TRACKER.pastPointsPageCount[userId]) {
//				
//			}
			
			var jsonparams = "r=users/getUserPastPointsJSON&userId=" + userId + "&pageNo=" + pageNo;
			
			TRACKER.ajaxReq(jsonparams, function(obj){
				try
				{
					//var obj = $.parseJSON(result);

					TRACKER.pastPointsPageNo[userId] =  obj.pageNo;
					TRACKER.pastPointsPageCount[userId] =  obj.pageCount;
					
					if(obj.userwashere.length > 0)
					{
						processUserPastLocations(MAP, obj.userwashere, userId);
						TRACKER.bAllLinesCleared[userId] = false;

						if (typeof callback == "function") {
							callback();
						}						
					}
					else if(1 == pageNo) //Daha ilk sayfada bile hic past point yoksa
					{
						TRACKER.showMessageDialog(TRACKER.langOperator.noPastDataAvailable);
					}
					else
					{
						TRACKER.showMessageDialog(TRACKER.langOperator.noMorePastDataAvailable);
					}

					if(exceptionForParamsMap['getUserPastPointsJSON'] > 5)
					{
						  var data = "r=site/ajaxEmailNotification&title=Javascript Exception Recovered&message=" +
						  
						  "User Info: <br/><br/>" + 
						  "User Browser: " + BrowserDetect.browser + " " + BrowserDetect.version + "<br/>" +
						  "User OS: " + BrowserDetect.OS + "<br/><br/>" +
						  "'getUserPastPointsJSON' javascript exception is recovered at " + exceptionForParamsMap['getUserPastPointsJSON'] + ". trial." + "<br/>" +
						  "&params=" + 'getUserPastPointsJSONRecovery'; 

						  //If the javascript exception is recovered, report this situation via e-mail
						  TRACKER.ajaxReq(data, null, true);					
					}				
					
					exceptionForParamsMap['getUserPastPointsJSON'] = 0;					
				}
				catch(error)
				{
					alertMsg('Exception in jsonparams: ' + jsonparams + '\n' + 
						  'Error: ' + error.message + '\n' + 
						  'JSON obj: ' + JSON.stringify(obj));
						
					exceptionForParamsMap['getUserPastPointsJSON'] = exceptionForParamsMap['getUserPastPointsJSON'] + 1;
					
					if(exceptionForParamsMap['getUserPastPointsJSON'] < 5)
					{
						//Try the function call again after 1 second 
						setTimeout(function() {TRACKER.drawTraceLine(userId, pageNo, callback);}, 1000);					
					}
					else if(exceptionForParamsMap['getUserPastPointsJSON'] == 5)
					{
						  var data = "r=site/ajaxEmailNotification&title=Javascript Exception Occured!&message=" +
						  
						  "Exception Info: <br/><br/>" + 
						  "User Browser: " + BrowserDetect.browser + " " + BrowserDetect.version + "<br/>" +
						  "User OS: " + BrowserDetect.OS + "<br/><br/>" +
						  "Exception in jsonparams: " + jsonparams + "<br/>" +
						  "Error: " + error.message + "<br/>" +
						  "JSON obj: " + JSON.stringify(obj) + "&params=" + "getUserPastPointsJSON"; 

						  //If 5 consecutive ajax queries are erroneous, report this situation via e-mail
						  TRACKER.ajaxReq(data, null, true);					
					}
					else //exceptionForParamsMap['getUserListJson'] > 5
					{
						//For the error case check connection for every 10 seconds 
						setTimeout(function() {TRACKER.drawTraceLine(userId, pageNo, callback);}, 10000);					
					}					
				}				
			}, true);			
		}
		else {			
			//alert("drawTraceLine - 3 else(undefined)");
			
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
					TRACKER.users[userId].mapMarker[i].pointAdded = false;
					TRACKER.users[userId].infoWindowIsOpened = false;
					
				}
			}
			
			MAP.removePolyline(TRACKER.users[userId].polyline);
			delete TRACKER.users[userId].polyline;
			TRACKER.users[userId].polyline = null;
			TRACKER.bAllLinesCleared[userId] = true;
		}
	};
	
	/*
	 * sending GeoFence points to the server
	 */
	this.sendGeoFencePoints = function(name,desc,lat1,long1,lat2,long2,lat3,long3) {
		
		var processOutput = true;
		var params = "r=geofence/sendGeofenceData&name="+name+"&description="+desc+"&point1Latitude="+ lat1.toFixed(6) +"&point1Longitude="+ long1.toFixed(6)+"&point2Latitude="+ lat2.toFixed(6) +"&point2Longitude="+ long2.toFixed(6)+"&point3Latitude="+ lat3.toFixed(6) +"&point3Longitude="+ long3.toFixed(6);
		
		TRACKER.ajaxReq(params, function(obj){
			try
			{
				//var obj = jQuery.parseJSON(response);
				if (obj.result && obj.result == "1") 
				{
				}
				else
				{
					processOutput = false;
					TRACKER.showMessageDialog("A geofence with this name already exists!");
				}				
			}
			catch(error)
			{
				alertMsg('Exception in jsonparams: ' + jsonparams + '\n' + 
					  'Error: ' + error.message + '\n' + 
					  'JSON obj: ' + JSON.stringify(obj));
			}
		}, true);
		
		return processOutput;	
	}

	this.showMediaWindow = function(uploadId, isPublic){
		if (typeof TRACKER.images[uploadId] == "undefined") {
			TRACKER.getImageList(isPublic, true, function(){
				MAP.trigger(TRACKER.images[uploadId].mapMarker.marker, 'click');			
			});
			
			//alertMsg("showMediaWindow 111");
		}
		else {		
			MAP.trigger(TRACKER.images[uploadId].mapMarker.marker, 'click');
			
			//alertMsg("showMediaWindow 222");
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
	
	this.zoomOutPoint = function (latitude, longitude) {
		var point = new MapStruct.Location({latitude:latitude, longitude:longitude});
		MAP.zoomOutPoint(point);
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
	this.showPointGMarkerInfoWin = function(currentMarkerIndex, nextMarkerIndex, userId){

		if(TRACKER.bAllLinesCleared[userId] == undefined)
		{
			TRACKER.bAllLinesCleared[userId] = true;
		}
		
		//alert("showPointGMarkerInfoWin - TRACKER.bAllLinesCleared[userId]:" + TRACKER.bAllLinesCleared[userId]);
		
		//if (typeof TRACKER.users[userId].mapMarker == "undefined" || typeof TRACKER.users[userId].mapMarker[nextMarkerIndex] == "undefined")
		if((true == TRACKER.bAllLinesCleared[userId]) || (typeof TRACKER.users[userId].mapMarker[nextMarkerIndex] == "undefined"))	
		{ 
			//alert("showPointGMarkerInfoWin - if");
			
			if (nextMarkerIndex == "0") {
				TRACKER.pastPointsPageNo[userId] = 0;
			}
			else if(TRACKER.pastPointsPageNo[userId] == undefined)
			{
				TRACKER.pastPointsPageNo[userId] = 0;
			}
			
			var reqPageNo = Number(TRACKER.pastPointsPageNo[userId]) + 1;
			
			if(TRACKER.pastPointsPageCount[userId] == undefined)
			{
				TRACKER.pastPointsPageCount[userId] = null;
			}

			//alert("pastPointsPageCount:" + TRACKER.pastPointsPageCount[userId] + " - reqPageNo:" + reqPageNo);
			
			if (TRACKER.pastPointsPageCount[userId] == null || reqPageNo <= TRACKER.pastPointsPageCount[userId]) {
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
						MAP.setMarkerVisible(TRACKER.users[userId].mapMarker[nextMarkerIndex].marker, true);
						
						if(false == TRACKER.users[userId].mapMarker[nextMarkerIndex].pointAdded)
						{
							MAP.addPointToPolyline(TRACKER.users[userId].polyline, TRACKER.users[userId].mapMarker[nextMarkerIndex].point);
							TRACKER.users[userId].mapMarker[nextMarkerIndex].pointAdded = true;
						}
						
						MAP.trigger(TRACKER.users[userId].mapMarker[nextMarkerIndex].marker, 'click');
					}
				});
			}
			else {
				//TRACKER.showInfoBar(TRACKER.langOperator.noMorePastDataAvailable);
				//alert("noMorePastDataAvailable - 1");
				TRACKER.showMessageDialog(TRACKER.langOperator.noMorePastDataAvailable);				
			}
		}
		else if (TRACKER.users[userId].mapMarker[nextMarkerIndex] == null){
			//TRACKER.showInfoBar(TRACKER.langOperator.noMorePastDataAvailable);
			//alert("noMorePastDataAvailable - 2");
			TRACKER.showMessageDialog(TRACKER.langOperator.noMorePastDataAvailable);
		}
		else {
			//alert("showPointGMarkerInfoWin - else");
			
//			if (nextMarkerIndex == "1") {
//				nextMarkerIndex = 0;
//			}			
			
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
			MAP.setMarkerVisible(TRACKER.users[userId].mapMarker[nextMarkerIndex].marker, true);
			
			if(false == TRACKER.users[userId].mapMarker[nextMarkerIndex].pointAdded)
			{
				MAP.addPointToPolyline(TRACKER.users[userId].polyline, TRACKER.users[userId].mapMarker[nextMarkerIndex].point);
				TRACKER.users[userId].mapMarker[nextMarkerIndex].pointAdded = true;
			}

			MAP.trigger(TRACKER.users[userId].mapMarker[nextMarkerIndex].marker, "click");			
		}
	}
	
	function postRequest (url, params, success, error) {  
		  var xhr = XMLHttpRequest ? new XMLHttpRequest() : 
		                             new ActiveXObject("Microsoft.XMLHTTP");

		  xhr.open("POST", url, true/*async*/);
		  
		  xhr.onreadystatechange = function(){ 
		    if(xhr.readyState == 4) 
		    { 
		      if(xhr.status == 200) 
		      { 
		    	  success(xhr.responseText); 
		      } 
		      else 
		      { 
		    	  error(xhr, xhr.status); 
		      } 
		    } 
		  };
		  
		  xhr.onerror = function () { 
		    error(xhr, xhr.status); 
		  };
		  
		  xhr.send(params); 
		}
	
	var ajaxErrorForParamsMap = {};

	/**
	 * this a general ajax request function, it is used whenever any ajax request is made 
	 */
	this.ajaxReq = function(params, callback, notShowLoadingInfo)
	{	
//		if(BrowserDetect.browser == "Internet Explorer")
//		{
//			//alertMsg("ajax in Internet Explorer");
//			
////		    var xhReq = new XMLHttpRequest();
////		    xhReq.open("POST", 'index.php?' + params, false);
////		    xhReq.send(null);
////
////		    var result = JSON.parse(xhReq.responseText);
////		    callback(result);
//		    
//		    postRequest('index.php?' + params, null, 
//		    	function (response) { // success callback
//		    		var result = JSON.parse(response);
//		    		callback(result);
//		    	}, 
//			    function (xhr, status) { // error callback
//			        switch(status) { 
//			          case 404: 
//			            alertMsg('File not found'); 
//			            break; 
//			          case 500: 
//			            alertMsg('Server error'); 
//			            break; 
//			          case 0: 
//			            alertMsg('Request aborted'); 
//			            break; 
//			          default: 
//			            alertMsg('Unknown error ' + status); 
//			        } 
//		    	});		    
//		}
//		else
//		{
			$.ajax({
				url: TRACKER.ajaxUrl,
				type: 'POST',
				data: params,
				//	dataType: 'xml',
	            //contentType: "application/json; charset=utf-8", //Bunu acinca hata olusuyor?
	            dataType: "json",			
				timeout:100000,
				beforeSend: function()
				{ 	
//					if (!notShowLoadingInfo) {
//						$("#loading").show();
//					} 
				},
				success: function(result){ 
					//$("#loading").hide(); 	
					callback(result);
					
					ajaxErrorForParamsMap[params] = 0;
				}, 			
				statusCode: {
					  400: function() {
						    //alertMsg('400 Bad Request: Server understood the request but request content was invalid.');
						  },				
					  401: function() {
						    //alertMsg('401 Unauthorized: Unauthorized Access!');
						  },				
					  403: function() {
						    //alertMsg('403 Forbidden: Forbidden, authorization required!');
						    //location.reload(); //Kullanici log out olmus, sayfayi yenile ki login sayfasi gelsin
						  },				
					  404: function() {
						  	//alertMsg('404 Not Found: Could not contact server!');
					  	  },
					  406: function() {
						    //alertMsg('406 Not Acceptable');
						  },
					  408: function() {
						    //alertMsg('408 Request Timeout');
						  },					  
					  500: function() {
						  	//alertMsg('500: A server-side error has occurred!');
					  	  },
					  503: function() {
						    //alertMsg('503: Service Unavailable!');
						  }				  
					},			
//				failure: function(result) {								
//					$("#loading").hide();
//					alertMsg("Failure in ajax.");						
//				},
	/*			error: function(par1, par2, par3){
					//alertMsg(par1.responseText);		
					$("#loading").hide();
					alertMsg("Error in ajax.." + " ajaxUrl:" + TRACKER.ajaxUrl + " - params:" + params);
				}*/
				error: function(xhr, status, error) {
//					  var err = eval("(" + xhr.responseText + ")");
//					  alertMsg("err.Message: " + err.Message + "\n\n" +
//							"status: " + status + "\n\n" +
//							"error: " + error + "\n\n" +						  
//							"Error in ajax -" + " ajaxUrl:" + TRACKER.ajaxUrl + " - params:" + params);
					
					  if(xhr.status != 200) //status 200 de olsa error diye gelebiliyor, 200 olmayanlari dikkate al
					  {
//						  var errorData = "r=site/ajaxErrorOccured&errorMessage=" +
//						  
//						  "User Browser: " + BrowserDetect.browser + " " + BrowserDetect.version + "<br/>" +
//						  "User OS: " + BrowserDetect.OS + "<br/><br/>" +					  
//		  				  "xhr.responseText: " + xhr.responseText + "<br/>" +
//						  "xhr.status: " + xhr.status + "<br/>" + 
//						  "error: " + error + "<br/>" +						  
//						  "Error in ajax -" + " ajaxUrl:" + TRACKER.ajaxUrl + " - params:" + params + 
//						  
//						  "&params=" + params;	 
//
//						  TRACKER.ajaxReq(errorData, null, true);				
//						  
//						  alertMsg("xhr.responseText: " + xhr.responseText + "\n" +
//								"xhr.status: " + xhr.status + "\n" + 
//								"error: " + error + "\n" +						  
//								"Error in ajax -" + " ajaxUrl:" + TRACKER.ajaxUrl + " - params:" + params + "\n\n" +
//								"User Browser: " + BrowserDetect.browser + " " + BrowserDetect.version + "\n" +
//								"User OS: " + BrowserDetect.OS);					  
					  }

					  if(xhr.status == 403)
					  {
						  //alertMsg('403 Forbidden: Forbidden, authorization required!');
						  
						  if(bDeploymentModeOn === true)
						  {
							  var data = "r=site/ajaxEmailNotification&title=Ajax Error Occured!&message=" +
							  
							  "Error Info: <br/><br/>" + 
							  "User Browser: " + BrowserDetect.browser + " " + BrowserDetect.version + "<br/>" +
							  "User OS: " + BrowserDetect.OS + "<br/><br/>" +					  
			  				  "xhr.responseText: " + xhr.responseText + "<br/>" +
							  "xhr.status: " + xhr.status + "<br/>" + 
							  "error: " + error + "<br/>" +						  
							  "Error in ajax -" + " ajaxUrl:" + TRACKER.ajaxUrl + " - params:" + params + "&params=" + params; 

							  //If authorization error occurs, report this situation via e-mail
							  TRACKER.ajaxReq(data, null, true);							  
						  }
						  						  
						  location.reload(); //Kullanici log out olmus, sayfayi yenile ki login sayfasi gelsin 
					  }
					  else
					  {
						  if(!ajaxErrorForParamsMap[params]) 
						  {
							  ajaxErrorForParamsMap[params] = 0;
						  }					  
						  
						  ajaxErrorForParamsMap[params] = ajaxErrorForParamsMap[params] + 1;
						  
						  if(ajaxErrorForParamsMap[params] > 5)
						  {
							  var data = "r=site/ajaxEmailNotification&title=Ajax Error Occured!&message=" +
							  
							  "Error Info: <br/><br/>" + 
							  "User Browser: " + BrowserDetect.browser + " " + BrowserDetect.version + "<br/>" +
							  "User OS: " + BrowserDetect.OS + "<br/><br/>" +					  
			  				  "xhr.responseText: " + xhr.responseText + "<br/>" +
							  "xhr.status: " + xhr.status + "<br/>" + 
							  "error: " + error + "<br/>" +						  
							  "Error in ajax -" + " ajaxUrl:" + TRACKER.ajaxUrl + " - params:" + params + "&params=" + params; 

							  //If more than 5 consecutive ajax queries are erroneous, report this situation via e-mail
							  TRACKER.ajaxReq(data, null, true);

							  //If more than 5 consecutive ajax queries are erroneous, then reload the page
							  location.reload();
						  }
						  else
						  {
							  //Try the ajax query again after 1 second
							  //TRACKER.ajaxReq(params, callback, notShowLoadingInfo);
							  setTimeout(function() {TRACKER.ajaxReq(params, callback, notShowLoadingInfo);}, 1000);
						  }						  
					  }
					}			
			});			
		//}
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
		$("#messageDialogOK").show(); 
		$("#messageDialogText").html('<br/>' + message); 
		$("#messageDialog").dialog("open"); 
	}
	
	this.showLongMessageDialog = function(message, homePage) {
		$("#longMessageDialogOK").show();
		$("#longMessageDialogText").html('<br/>' + message);
		$("#longMessageDialogOKButton").unbind("click");
		
		if(typeof homePage !== 'undefined')
		{
			$('#longMessageDialogOKButton').click(function() { location.href = homePage; });
		}
		
		$("#longMessageDialog").dialog("open"); 
	}	
	
//	this.showConfirmationDialog = function(question, callback){
//		$("#confirmationDialog #question").html('<br/>' + question); 
//		var buttons = $("#confirmationDialog").dialog( "option", "buttons" );
//		// dont forget first button is positivie button so below loop works
//		for(var property in buttons) {
//			buttons[property] = callback;
//			break;
//		}
//		//buttons.OK = callback;
//		$("#confirmationDialog").dialog("option","buttons",buttons);
//		$("#confirmationDialog").dialog("open");	
//	}
	
	this.showConfirmationDialog = function(question, callback){
		$("#confirmationDialogButtons").show();
		$("#confirmationDialog #question").html('<br/>' + question);
		$("#confirmationDialogOK").unbind("click"); //Onceden baglanmisları unbind etmezse surekli birikiyor
		$('#confirmationDialogOK').click(function() { callback(); });
		$("#confirmationDialog").dialog("open");	
	}	
		
	this.closeConfirmationDialog = function(){
		$("#confirmationDialog").dialog("close");
	}
}
