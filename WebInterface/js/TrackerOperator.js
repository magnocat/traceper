
function TrackerOperator(url, map, interval, langOp){
	
	TRACKER = this;	
	MAP = map;
	this.langOperator = langOp;
	this.language = "en";
	this.ajaxUrl = url;
	this.actionAuthenticateUser = "WebClientAuthenticateUser";
	this.actionGetUserList = "WebClientGetUserList";	
	this.actionSearchUser = "WebClientSearchUser";
	this.actionUpdateUserList = "WebClientUpdateUserList";
	this.userListPageNo = 0;	
	this.userListPageCount = 0;
	this.updateUserListPageNo = 0;
	this.updateUserListPageCount = 0;
	this.searchPageNo = 0;
	this.searchPageCount = 0;
	this.updateInterval = interval;
	this.started = false;
	this.trackedUserId = 0;
	
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
		var infoWindowIsOpened = false;
		
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
			$('#users').slideUp(function(){
									$('#users').html(str);
									$('#users').slideDown();
								});
			
			if (TRACKER.started == false) {
				TRACKER.started == true;
				setTimeout(TRACKER.updateUserList, TRACKER.updateInterval);
			}
			
			
		});	
	};
	
	this.updateUserList = function(){
		var params = "action=" + TRACKER.actionUpdateUserList + "&pageNo=" + TRACKER.updateUserListPageNo; 
		//alert("in update user data");
		
		if (TRACKER.trackedUserId != 0)
		{
			params+= "&trackedUser=" + TRACKER.trackedUserId;
		}
				
		
		TRACKER.ajaxReq(params, function(result){
			TRACKER.updateUserListPageNo = TRACKER.getPageNo(result);
			TRACKER.updateUserListPageCount = TRACKER.getPageNo(result);
			TRACKER.processXML(result);
			
			// to fetched all data reguarly updateUserListPageNo must be resetted.
			if (TRACKER.updateUserListPageNo >= TRACKER.userListPageCount){
				TRACKER.updateUserListPageNo = 1;
			}
			else{
				TRACKER.updateUserListPageNo++;
			}			
		}, true);
		// set time out again
		setTimeout(TRACKER.updateUserList, TRACKER.updateInterval);
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
				$('#search, #users').slideUp(function(){
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
	
	this.openMarkerInfoWindow = function(userId){
		TRACKER.users[userId].gmarker.openInfoWindowHtml('<b>' + TRACKER.users[userId].username + '</b>'
														+ '<br/>' + TRACKER.langOperator.realname + ": "+TRACKER.users[userId].realname  
														+ '<br/>' + TRACKER.langOperator.time + ": " + TRACKER.users[userId].time
														+ '<br/>' + TRACKER.langOperator.deviceId + ": " + TRACKER.users[userId].deviceId
														+ '<br/>' + TRACKER.langOperator.latitude + ": " + TRACKER.users[userId].latitude  
														+ '<br/>' + TRACKER.langOperator.longitude + ": " + TRACKER.users[userId].longitude);
	}
	
	this.closeMarkerInfoWindow = function (userId) {
		TRACKER.users[userId].gmarker.closeInfoWindow();
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