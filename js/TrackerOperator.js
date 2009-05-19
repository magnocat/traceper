
function TrackerOperator(url, map, period){
	
	TRACKER = this;	
	MAP = map;
	this.language = "en";
	this.periodGetLocations = period;
	this.ajaxUrl = url;
	this.actionAuthenticateUser = "WebClientAuthenticateUser";
	this.actionGetUserList = "WebClientGetUserList";	
	this.actionSearchUser = "WebClientSearchUser";
	this.userListPageNo = 0;
	// updateUserListPageNo's first value is 2 because first page is already fetched.
	this.updateUserListPageNo = 2;
	this.userListPageCount = 0;
	this.searchPageNo = 0;
	this.searchPageCount = 0;
	this.updateInterval = 3000;
	this.started = false;
	
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
			var str = "<ul>";	
				
			$(result).find("page").find("user").each(function(){
				
				var user = $(this);
				
				var userId = $(user).find("Id").text();
				var username = $(user).find("username").text();
				var latitude = $(user).find("location").attr('latitude');
				var longitude = $(user).find("location").attr('longitude');
				var point = new GLatLng(latitude, longitude);
				
				str += "<li><a href='javascript:TRACKER.trackUser("+ userId +")'>"+ username +"</a></li>";
			
				if (typeof TRACKER.users[userId] == "undefined") 
				{					
					TRACKER.users[userId] = new TRACKER.User( {username:username,
															   realname:$(user).find("realname").text(),
															   latitude:latitude,
															   longitude:longitude,
															   time:$(user).find("time").text(),
															   message:$(user).find("message").text(),
															   deviceId:$(user).find("deviceId").text(),
															   gmarker:new GMarker(point),
															});
					
					
					
					MAP.addOverlay(TRACKER.users[userId].gmarker);
				}
				else
				{
					var point = new GLatLng($(user).find("location").attr('latitude'), $(user).find("location").attr('longitude'));
					
					TRACKER.users[userId].gmarker.setLatLng(point);					
				}
				
			});
			
			str += "</ul>";
			str += TRACKER.writePageNumbers('javascript:TRACKER.getUserList(%d)', TRACKER.userListPageCount, TRACKER.userListPageNo, 3);
			$('#users').html(str);
			
			if (TRACKER.started == false) {
				TRACKER.started == true;
				setTimeout(TRACKER.updateUserList, TRACKER.updateInterval);
			}
			
			
		});	
	};
	
	this.updateUserList = function(){
		// this function is called after the first page is fetched by TRACKER.getUserList function
		// so pageNo's first value is 2
		var params = "action=" + TRACKER.actionGetUserList + "&pageNo=" + TRACKER.updateUserListPageNo; 
		//alert("in update user data");
		TRACKER.ajaxReq(params, function(result){
			TRACKER.updateUserListPageNo = TRACKER.getPageNo(result);
			
			$(result).find("page").find("user").each(function(){
				
				var user = $(this);
				
				var userId = $(user).find("Id").text();
				var username = $(user).find("username").text();
				var latitude = $(user).find("location").attr('latitude');
				var longitude = $(user).find("location").attr('longitude');
				var point = new GLatLng(latitude, longitude);
						
				if (typeof TRACKER.users[userId] == "undefined") 
				{					
					TRACKER.users[userId] = new TRACKER.User( {username:username,
															   realname:$(user).find("realname").text(),
															   latitude:latitude,
															   longitude:longitude,
															   time:$(user).find("time").text(),
															   message:$(user).find("message").text(),
															   deviceId:$(user).find("deviceId").text(),
															   gmarker:new GMarker(point),
															});					
					
					MAP.addOverlay(TRACKER.users[userId].gmarker);
				}
				else
				{
					var point = new GLatLng($(user).find("location").attr('latitude'), $(user).find("location").attr('longitude'));
					
					TRACKER.users[userId].gmarker.setLatLng(point);					
				}				
			});
			// to fetched all data reguarly updateUserListPageNo must be resetted.
			if (TRACKER.updateUserListPageNo >= TRACKER.userListPageCount){
				TRACKER.updateUserListPageNo = 1;
			}
			else{
				TRACKER.updateUserListPageNo++;
			}
			
			
		});
		// set time out again
		setTimeout(TRACKER.updateUserList, TRACKER.updateInterval);
	}

	this.searchUser = function(string, pageNo)
	{
		var params = "action=" + TRACKER.actionSearchUser + "&search=" + string + "&pageNo=" + pageNo;
		
		TRACKER.ajaxReq(params, function(result){
			TRACKER.searchPageNo = TRACKER.getPageNo(result);
			TRACKER.searchPageCount = TRACKER.getPageCount(result);
			
			//alert(TRACKER.searchPageNo);
			var str = "<ul>";	
			
			$(result).find("page").find("user").each(function(){
				var user = $(this);
				
				var userId = $(user).find("Id").text();
				var username = $(user).find("username").text();
				var latitude = $(user).find("location").attr('latitude');
				var longitude = $(user).find("location").attr('longitude');
				var point = new GLatLng(latitude, longitude);
				
				str += "<li><a href='javascript:TRACKER.trackUser("+ userId +")'>"+ username +"</a></li>";
			
				if (typeof TRACKER.users[userId] == "undefined") 
				{					
					TRACKER.users[userId] = new TRACKER.User( {username:username,
															   realname:$(user).find("realname").text(),
															   latitude:latitude,
															   longitude:longitude,
															   time:$(user).find("time").text(),
															   message:$(user).find("message").text(),
															   deviceId:$(user).find("deviceId").text(),
															   gmarker:new GMarker(point),
															});
					
					
					
					MAP.addOverlay(TRACKER.users[userId].gmarker);
				}
				else
				{
					var point = new GLatLng($(user).find("location").attr('latitude'), $(user).find("location").attr('longitude'));
					
					TRACKER.users[userId].gmarker.setLatLng(point);					
				}				
						
			});
			str += "</ul>";
			str += TRACKER.writePageNumbers('javascript:TRACKER.searchUser("'+ string +'", %d)',TRACKER.searchPageCount, TRACKER.searchPageNo, 3);
			$('#searchResults').html(str);	
		
		});	
	};	
	
	this.trackUser = function(userId){
		MAP.panTo(new GLatLng(TRACKER.users[userId].latitude, TRACKER.users[userId].longitude));
		TRACKER.users[userId].gmarker.openInfoWindowHtml(TRACKER.users[userId].username + '<br/>'+ TRACKER.users[userId].realname);
		
	}
	
	
	this.ajaxReq = function(params, callback)
	{		
		$.ajax({
			type: 'POST',
			url: TRACKER.ajaxUrl,
			data: params,
			dataType: 'xml',
			timeout:100000,
			beforeSend: function(){  },
			success: callback,
			failure: function(result) {								
						alert("Failure in ajax.");						
			},
			error: function(par1, par2, par3){			
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