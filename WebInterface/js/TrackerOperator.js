
function TrackerOperator(url, map, fetchPhotosInInitial, interval, qUpdatedUserInterval, langOp, userId){
	
	TRACKER = this;	
	MAP = map;
	this.langOperator = langOp;
	this.userId = userId;
	this.language = "en";
	this.ajaxUrl = url;
	this.facebookId = null;
	this.fetchPhotosInInitialization = Number(fetchPhotosInInitial);
	this.actionAuthenticateUser = "WebClientAuthenticateUser";
	this.actionGetUserList = "WebClientGetUserList";
	this.actionGetFriendList = "WebClientGetFriendList";
	this.actionSearchUser = "WebClientSearchUser";
	this.actionUpdateFriendList = "WebClientUpdateFriendList";
	this.actionGetUpdatedFriendList = "WebClientGetUpdatedFriendList";
	this.actionGetUserPastPoints = "WebClientGetUserPastPoints";
	this.actionGetImageList = "WebClientGetImageList";
	this.actionSearchImage = "WebClientSearchImage";
	this.actionSignout = "WebClientSignout";
	this.actionSendNewPassword = "WebClientSendNewPassword";
	this.actionInviteUser = "WebClientInviteUser";
	this.actionChangePassword = "WebClientChangePassword";
	this.actionDeleteImage = "WebClientDeleteImage";
	this.actionRegisterUser = "WebClientRegisterUser";
	this.actionActivateAccount= "WebClientActivateAccount";
	this.actionDeleteFriendship= "WebClientDeleteFriendship";
	this.actionAddFriendRequest = "WebClientAddFriendRequest";
	this.actionGetFriendRequests = "WebClientGetFriendRequests";	
	this.actionConfirmFriendship = "WebClientConfirmFriendship";
	this.userListPageNo = 1;	
	this.userListPageCount = 0;
	this.friendListPageNo = 1;
	this.friendListPageCount = 0;
	this.updateFriendListPageNo = 1;
	this.updateFriendListPageCount = 0;
	this.searchPageNo = 1;
	this.searchPageCount = 0;
	this.pastPointsPageNo = 0;
	this.imageListPageNo = 1;
	this.imageListPageCount = 0;
	this.imageListSearchPageNo = 1;
	this.imageListSearchPageCount = 0;
	this.friendRequestListPageNo = 1;
	this.friendRequestListPageCount = 0;
	//page no initial value is important
	this.bgImageListPageNo = 1;
	this.bgImageListPageCount = 0;
	this.pastPointsPageCount = 0;
	this.updateInterval = interval;
	this.timer;
	this.maxZoomlevel = [[]];
	this.traceLineDrawedUserId = null;
	this.deletedImageId = 0;
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
    this.friendPageResetCount = Number(0);	
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
		//var username;
		var realname;
		var latitude;
		var longitude;
		var friendshipStatus;
		var time;
		var deviceId;
		var message;
		var gmarker;
		var pastPointsGMarker;
		var infoWindowIsOpened = false;
		var polyline = null;		
		var maxZoomLevel = null;
		var statusMessage = null;
		
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
		var gmarker;
		
		for (var n in arguments[0]) { 
			this[n] = arguments[0][n]; 
		}
	}
	
	this.setFacebookId = function(facebookId){
		if (facebookId) {
			TRACKER.facebookId = facebookId;
			$("#changePassword").hide();
		}
	}
	
	this.authenticateUser = function(username, password, rememberMe, callback)
	{
		var params = "action=" + TRACKER.actionAuthenticateUser + "&username=" + username + "&password=" + password + "&keepUserLoggedIn=" + rememberMe;
		
		if (username != "" && password != "" ) 
		{
			TRACKER.ajaxReq(params, function (result){
				if (result == "1") {					
					location.href = 'index.php';
				}
				else {
					if (typeof(callback) == "function") {
						callback();
					}
				}
			});
		}
		else {
			TRACKER.showMessage(TRACKER.langOperator.warningMissingParameter, "warning");
		}
	};
	
	this.inviteUser = function(email, message) {
	
		var params = "action=" + TRACKER.actionInviteUser + "&email=" + email + "&message=" + message;
		
		TRACKER.ajaxReq(params, function (result){
			if (result == "1") {					
				alert("operation is succesfull");
			}
			else{
				alert("Error in operation");
			}
		});		
	};
	
	this.registerUser = function(email, name, password, confirmPassword, invitationKey, callback) {
		if (password == confirmPassword) {		
		
			var params = "action=" + TRACKER.actionRegisterUser + "&email=" + email + "&name=" + name + "&password=" + password + "&key="+invitationKey;
			
			TRACKER.ajaxReq(params, function (result){
				if (result == "1") {					
					TRACKER.showMessage(TRACKER.langOperator.dataRecordedCheckYourEmail, "info");
				}
				else if (result == "2"){
					TRACKER.showMessage(TRACKER.langOperator.activateAccountSuccesful, "info");
				}
				else if (result == "-5"){
					TRACKER.showMessage(TRACKER.langOperator.emailAlreadyExist, "warning");
				}
				else if (result == "-9"){
					TRACKER.showMessage(TRACKER.langOperator.emailNotValid, "warning");
				}
				
				if (typeof callback == 'function'){
					callback(result);
				}
			});
			
		}
		else{
			//TODO: password alanlarindaki degerler silinsin
			TRACKER.showMessage("Passwords don't match", "warning");
		}
	}
	
	this.activateAccount = function(key, email){
		var params = "action=" + TRACKER.actionActivateAccount + "&email=" + email + "&key=" + key;
		TRACKER.ajaxReq(params, function (result){
			
			if (result == "1") {					
				$("#activateAccountInfo").html(TRACKER.langOperator.activateAccountSuccesful);
			}
			else if (result == "-6"){
				$("#activateAccountInfo").html(TRACKER.langOperator.activatedAccountMailNotFound);
			}
			else if (result == "-8"){
				$("#activateAccountInfo").html(TRACKER.langOperator.activatedAccountKeysDontMatch);
			}
			else if (result == "-5") {
				$("#activateAccountInfo").html(TRACKER.langOperator.emailAlreadyExist);
			}
			
			$("#activateAccountInfo").append("<br/><br/> <a href='index.php'>"+TRACKER.langOperator.HomePage+"</a>");
			
		});
		
	}
	
	this.sendNewPassword = function(email, callback){
		var params = "action=" + TRACKER.actionSendNewPassword + "&email=" + email;		
		if (email != "" ) 
		{
			TRACKER.ajaxReq(params, function (result){
				if (result == "1") {					
					TRACKER.showMessage(TRACKER.langOperator.newPasswordSent, "info");
				}
				else if (result == "-6"){
					TRACKER.showMessage(TRACKER.langOperator.emailNotFound, "warning");
				}
				else if (result == "-7"){
					TRACKER.showMessage(TRACKER.langOperator.currentPasswordDoesntMatch, "warning");
				}
				
				if (typeof callback == 'function'){
					callback(result);
				}
			});
		}
		else {
			TRACKER.showMessage(TRACKER.langOperator.warningMissingParameter, "warning");
		}
	}
	
	this.changePassword = function(newPassword, currentPassword){
		var params = "action=" + TRACKER.actionChangePassword + "&newPassword=" + newPassword + "&currentPassword=" + currentPassword;
	
		if (newPassword != "" && currentPassword != "")
		{
			TRACKER.ajaxReq(params, function (result){
				if (result == "1") {	
					TRACKER.showMessage(TRACKER.langOperator.passwordChanged, "info");
				}
				else if (result == "-7"){
					TRACKER.showMessage(TRACKER.langOperator.currentPasswordDoesntMatch, "warning");
				}
			});
		}
		else {
			TRACKER.showMessage(TRACKER.langOperator.warningMissingParameter, "warning");
		}
	}
	
	this.signout = function(){
		var params = "action=" + TRACKER.actionSignout;
		TRACKER.ajaxReq(params, function (result){	
				if (TRACKER.facebookId != null) {
					FB.logout(function(response){
						location.href = 'index.php';
					});
				}
				else {
					location.href = 'index.php';
				}
		});
	}
	
	this.deleteFriendship = function (friendId) {
		var deleteFriendMessage = TRACKER.langOperator.deleteFriendshipConfirmation;
		if (typeof TRACKER.users[friendId] != "undefined") 
		{
			deleteFriendMessage = deleteFriendMessage.replace("%s", TRACKER.users[friendId].realname);
			if (confirm(deleteFriendMessage)) {
				var params = "action=" + TRACKER.actionDeleteFriendship + "&friendId="+friendId;
				TRACKER.ajaxReq(params, function (result){	
						if (result == "1") {
							TRACKER.getFriendList(TRACKER.friendListPageNo);
							TRACKER.users[friendId].friendshipStatus = 0;
						}
						else {
							TRACKER.showMessage(TRACKER.langOperator.errorInOperation, "warning");
						}
				});	
			}
		}	
	}
	
	this.confirmFriendship = function(friendId){
		var confirmationMessage = TRACKER.langOperator.acceptFriendRequestConfirmation;
		if (typeof TRACKER.users[friendId] != "undefined") 
		{
			confirmationMessage = confirmationMessage.replace("%s", TRACKER.users[friendId].realname);
			if (confirm(confirmationMessage)) {
				var params = "action=" + TRACKER.actionConfirmFriendship + "&friendId="+friendId;
				TRACKER.ajaxReq(params, function (result){	
						if (result == "1") {
							//TRACKER.getFriendRequests(TRACKER.friendRequestListPageNo,);
							TRACKER.getFriendRequests(TRACKER.friendRequestListPageNo,function(str){
								//$('#friendRequestsList').find(".mbcontainercontent:first").html(str);
								$('#friendRequestsList').find(".mbcontainercontent:first").html("<div id='lists'><div id='friendsList'><div id='friends'></div></div></div>");
								$('#friendRequestsList').find(".mbcontainercontent:first #friends").html(str);			
								//$('#friendRequestsList').mb_open();
								//$('#friendRequestsList').mb_centerOnWindow(true);
								//$('#friendRequestsList').mb_resizeTo(200,300);
			
			
								//$('#friendRequestsListContent').html(str);
							});
							TRACKER.getFriendList(TRACKER.friendListPageNo);

							TRACKER.users[friendId].friendshipStatus = 1; 

						}
						else {
							TRACKER.showMessage(TRACKER.langOperator.errorInOperation, "warning");
						}
				});	
			}
		}
	}
	
	this.addAsFriend = function(userId) {
		var friendRequestMessage = TRACKER.langOperator.addFriendRequestConfirmation;
		if (typeof TRACKER.users[userId] != "undefined") 
		{
			friendRequestMessage = friendRequestMessage.replace("%s", TRACKER.users[userId].realname);
			
			if (confirm(friendRequestMessage)) 
			{
				var params = "action=" + TRACKER.actionAddFriendRequest + "&friendId=" + userId;
				
				TRACKER.ajaxReq(params, function(result){
					if (result == "1") {
						TRACKER.showMessage(TRACKER.langOperator.friendRequestRecorded, "info");
						TRACKER.users[friendId].friendshipStatus = 2;
					}
					else {
						
						TRACKER.showMessage(TRACKER.langOperator.errorInOperation, "info");
					}
				});
			}
		}
	}
	
/*	we will use it to get whole user list page by page
 
	// getting user list with latitude longittude info
	this.getUserList = function(pageNo)
	{		
		var params = "action=" + TRACKER.actionGetUserList + "&pageNo=" + pageNo;
		TRACKER.ajaxReq(params, function(result){			
			TRACKER.friendListPageNo = TRACKER.getPageNo(result);
			TRACKER.friendListPageCount = TRACKER.getPageCount(result);
			
			var str = processXML(MAP, result);
			
			if (str != null) {
				str += TRACKER.writePageNumbers('javascript:TRACKER.getUserList(%d)', TRACKER.friendListPageCount, TRACKER.friendListPageNo, 3);
			}
			else {
				str = TRACKER.langOperator.noMatchFound;				
			}
			$('#friends').slideUp('fast',function(){
									$('#friends').html(str);
									$('#friends').slideDown();
								});
			
			if (TRACKER.started == false) {
				TRACKER.started = true;
				setTimeout(TRACKER.updateFriendList, TRACKER.updateInterval);
			}			
		});	
	};
*/	
	this.getFriendList = function(pageNo)
	{		
		var params = "action=" + TRACKER.actionGetFriendList + "&pageNo=" + pageNo;
		TRACKER.ajaxReq(params, function(result){			
			TRACKER.friendListPageNo = TRACKER.getPageNo(result);
			TRACKER.friendListPageCount = TRACKER.getPageCount(result);
			
			var str = processXML(MAP, result, true);
			
			if (str != null) {
				str += TRACKER.writePageNumbers('javascript:TRACKER.getFriendList(%d)', TRACKER.friendListPageCount, TRACKER.friendListPageNo, 3);
			}
			else {
				str = TRACKER.langOperator.noMatchFound;				
			}
			$('#friends').slideUp('fast',function(){
									$('#friends').html(str);
									$('#friends').slideDown();
								});
			
			if (TRACKER.started == false) {
				TRACKER.started = true;
				setTimeout(TRACKER.updateFriendList, TRACKER.updateInterval);
			}			
		});	
	};
	

	this.getFriendRequests = function(pageNo,callback) {
	
		var params = "action=" + TRACKER.actionGetFriendRequests + "&pageNo=" + pageNo;
		
		TRACKER.ajaxReq(params, function (result){
			TRACKER.friendRequestListPageNo = TRACKER.getPageNo(result);
			TRACKER.friendRequestListPageCount = TRACKER.getPageCount(result);
			
			var str = processXML(MAP, result, true);
			
			if (str != null) {
				str += TRACKER.writePageNumbers('javascript:TRACKER.getFriendRequests(%d)', TRACKER.friendRequestListPageNo, TRACKER.friendRequestListPageCount, 3);
			}
			else {
				str = TRACKER.langOperator.noMatchFound;				
			}
			if (typeof(callback)=="function")
			{
				callback(str);
			}				
		});		
	};
	
	/**
	 * 
	 */
	this.updateFriendList = function(){
		
		var params;
		if (TRACKER.friendPageResetCount > 0) 
		{
			var getImages = "&";
			if ($('#showPhotosOnMap').attr('checked') == true)
			{ 	getImages = "&include=image"; }
			
			params = "action=" + TRACKER.actionGetUpdatedFriendList + "&pageNo=" + TRACKER.updateFriendListPageNo
					+ getImages;
		}
		else {
			params = "action=" + TRACKER.actionUpdateFriendList + "&pageNo=" + TRACKER.updateFriendListPageNo; 
			
		}

		// set time out again
		TRACKER.timer = setTimeout(TRACKER.updateFriendList, TRACKER.updateInterval);
				
		TRACKER.ajaxReq(params, function(result){
			
			TRACKER.updateFriendListPageNo = TRACKER.getPageNo(result);
			TRACKER.updateFriendListPageCount = TRACKER.getPageCount(result);
			processXML(MAP, result);
			// to fetched all data reguarly updateFriendListPageNo must be resetted.
			var updateInt = TRACKER.updateInterval;
			if (TRACKER.updateFriendListPageNo >= TRACKER.updateFriendListPageCount){
				TRACKER.updateFriendListPageNo = 1;
				TRACKER.updateInterval = TRACKER.queryUpdatedUserInterval;
				TRACKER.friendPageResetCount = Number(TRACKER.friendPageResetCount) + 1;
				
				var showPhotosOnMap = $('#showPhotosOnMap').attr('checked');
				if (TRACKER.friendPageResetCount >= 1 &&
					showPhotosOnMap == true)
				{
					processImageXML(MAP, result);
				}
				// this is about initialization, it fetches photos data from server
				// after fetching users data
				if (TRACKER.friendPageResetCount == 1 &&
					showPhotosOnMap == true) 
				{
					TRACKER.getImageListInBg();
				}
			}
			else{
				TRACKER.updateFriendListPageNo++;
				TRACKER.updateInterval = TRACKER.getUserListInterval;
			}
			
			if (updateInt != TRACKER.updateInterval) {
				clearTimeout(TRACKER.timer);
				TRACKER.timer = setTimeout(TRACKER.updateFriendList, TRACKER.updateInterval);
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
				
				var str = processXML(MAP, result);
				if (str != null) {
					str += TRACKER.writePageNumbers('javascript:TRACKER.searchUser("' + string + '", %d)', TRACKER.searchPageCount, TRACKER.searchPageNo, 3);
				}
				else {
					str = TRACKER.langOperator.noMatchFound;
				}
				
				$('#friendsList #friends').slideUp();

				$('#friendsList .searchResults').slideUp('fast',function(){
						$('#friendsList .searchResults #results').html(str);
						$('#friendsList .searchResults').slideDown();
				});
			});
		}
		else {
			TRACKER.showMessage(TRACKER.langOperator.searchStringIsTooShort, "warning");
		}	
	};	
	
	this.getImageList = function(pageNo, callback){
		var params = "action=" + TRACKER.actionGetImageList + "&pageNo=" + pageNo;
				
		TRACKER.ajaxReq(params, function(result){			
			TRACKER.imageListPageNo = TRACKER.getPageNo(result);
			TRACKER.imageListPageCount = TRACKER.getPageCount(result);
			
			var str = processImageXML(MAP, result);
			
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
			
			processImageXML(MAP, result);
			
			if (TRACKER.bgImageListPageNo < TRACKER.bgImageListPageCount){
				TRACKER.bgImageListPageNo = Number(TRACKER.bgImageListPageNo) + 1;
				setTimeout(TRACKER.getImageListInBg, TRACKER.getUserListInterval);
			}	
			else if (TRACKER.bgImageListPageNo == TRACKER.bgImageListPageCount){
				TRACKER.allImagesFetched = true;
			}
		}, true);	
	}
	
	this.searchImage = function(realname,userId, pageNo){
		var params = "";
		if (userId != false){
			params = "userId=" + userId;
		}
		else if (username != false){
			params = "realname=" + realname;
		}
		
		if (params == ""){
			
		}
		else {
			params += "&action="+ TRACKER.actionSearchImage +"&pageNo=" + pageNo;
		
			TRACKER.ajaxReq(params, function(result){			
				TRACKER.imageListSearchPageNo = TRACKER.getPageNo(result);
				TRACKER.imageListSearchPageCount = TRACKER.getPageCount(result);

				var str = processImageXML(MAP, result);
				
				if (str != null) {
					str += TRACKER.writePageNumbers('javascript:TRACKER.searchImage("'+ realname +'","'+ userId +'" %d)', TRACKER.imageListSearchPageCount, TRACKER.imageListSearchPageNo, 3);
				}
				else {
					str = "<div class='generalStyle'>" + TRACKER.langOperator.noMatchFound + "</div>";				
				}
				$('#friendsList').slideUp('fast',function(){
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
	
	this.deleteImage = function(imageId){
		if (confirm(TRACKER.langOperator.confirmationMessage)) 
		{
			TRACKER.deletedImageId = imageId;
			var params = "action=" + TRACKER.actionDeleteImage + "&imageId=" + imageId
			TRACKER.ajaxReq(params, function(result){
				
				if (result == "1") {
					TRACKER.getImageList(TRACKER.imageListPageNo);
					MAP.removeOverlay(TRACKER.images[TRACKER.deletedImageId].gmarker);
					TRACKER.images.splice(TRACKER.deletedImageId, 1);
				}
				else {
					TRACKER.showMessage(TRACKER.langOperator.errorInOperation,"warning");
				}
			});
		}
		
	};
	
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
			
					var str = processUserPastLocationsXML(MAP, result);
	
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
														   + '<br/>' + TRACKER.users[userId].realname  
														   + '<br/>' + TRACKER.users[userId].time
														   + '<br/>' + TRACKER.users[userId].latitude + ", " + TRACKER.users[userId].longitude
														   //+ '<br/>' + TRACKER.users[userId].deviceId + " (" + TRACKER.langOperator.deviceId +") "
															  
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
						+ TRACKER.langOperator.upLoadtime + ": " + TRACKER.images[imageId].time + "<br/>"
						+ TRACKER.images[imageId].latitude + ", " + TRACKER.images[imageId].longitude
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
	this.showMessage = function(message, type, callback)
	{
		//message = '<div style="padding:5px;text-align:center;font-family:verdana;color:#FF6600">' + message + '</div>';

		//$("#message").html(message);
		
		var object = "#message_info";
		if (type == "warning") {
			object = "#message_warning";
		}
		
		$(object).mb_resizeTo(100, 600);
		$(object + ' .mbcontainercontent:first').html(message);
		$(object).mb_open();
		$(object).mb_centerOnWindow(true);
		$(object).mb_switchAlwaisOnTop(); 
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
							if (result == "-4"){								
								TRACKER.showMessage(TRACKER.langOperator.incorrectPassOrUsername, "warning", function(){ location.href = "index.php"; });
								
							}
							else if(result == "-2") {
								TRACKER.showMessage(TRACKER.langOperator.warningMissingParameter, "warning");
							}
							else if (result == "-1"){
								TRACKER.showMessage(TRACKER.langOperator.errorInOperation, "warning");
							}
							
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
