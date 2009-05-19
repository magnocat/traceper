
function Tracker(url, period){
	
	TRACKER = this;	
	this.language = "en";
	this.periodGetLocations = period;
	this.ajaxUrl = url;
	this.actionAuthenticateUser = "WebClientAuthenticateUser";
	this.actionGetUserList = "WebClientGetUserList";	
	this.actionGetLocations = "WebClientGetLocations";
	this.actionSearchUser = "WebClientSearchUser";
	this.userListPageNo = 0;
	this.userListPageCount = 0;
	this.searchPageNo = 0;
	this.searchPageCount = 0;
	
	this.authenticateUser = function(username, password)
	{
		var params = "action=" + TRACKER.actionAuthenticateUser + "&username=" + username + "&password=" + password;
		TRACKER.ajaxReq(params, function (result){
			if (result == "1") {
				//TODO: doing simultaneous ajax requests 
				TRACKER.getLocations();
				TRACKER.getUserList();
			}
		});
	};
	
	this.getUserList = function(pageNo)
	{
		var params = "action=" + TRACKER.actionGetUserList + "&pageNo=" + pageNo;
		TRACKER.ajaxReq(params, function(result){
			TRACKER.userListPageNo = TRACKER.getPageNo(result);
			TRACKER.userListPageCount = TRACKER.getPageCount(result);
			
			$(result).find("page").find("users").each(function(){
				
			});
			
		});	
	};
	
	this.getLocations = function(){
		setTimeout(TRACKER.periodGetLocations, function(){
				var params = "action=" + TRACKER.actionGetLocations;
				TRACKER.ajaxReq(params, function(result){
						//TODO: map update operations
				});
		});
	};
	
	this.searchUser = function(pageNo)
	{
		var params = "action=" + TRACKER.actionSearchUser + "&pageNo=" + pageNo;
		TRACKER.ajaxReq(params, function(result){
			TRACKER.searchPageNo = TRACKER.getPageNo(result);
			TRACKER.searchPageCount = TRACKER.getPageCount(result);
		
		});	
	};	
	
	this.ajaxReq = function(params, callback)
	{		
		$.ajax({
			type: 'POST',
			url: TRACKER.ajaxUrl,
			data: params,
			dataType: 'script',
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

/*	
	this.writePageNumbers = function(pageName, pageCount, currentPage, len){
		var length = 3;
		if (length) {
			length = len;
		}
		var numsStr="";
		var numsEnd="";
		var nums="";
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
			numsStr += "<a href='" + sprintf(pageName,1) + "'>1</a>";
			if (start > 3) {
				numsStr += "<a>...</a>";					
			}
			else
				numsStr += "<a href='" + sprintf(pageName,2) + "'>2</a>";	
				
		}
		else if ( start <= 0 ) {
			start = 1;
		}
		
		if ( end < pageCount ) {
			if ( end+2 == pageCount ){
				tmp = end+1;
				numsEnd += "<a href='" + sprintf(pageName, tmp) + "'>" + tmp + "</a>";
			}
			else
				numsEnd = "<a>...</a>" + numsEnd;	
				
			numsEnd += "<a href='" + sprintf($pageName, $pageCount) + "' >" + $pageCount + "</a>";
		}	
		for (var i = start; i <= end; i++)
		{
			if (currentPage == i)
			{
				nums += "<a href='" + sprintf(pageName, i) + "' id='activePageNo'>" + i + "</a>";
			}
			else
			{
				nums += "<a href='" + sprintf(pageName, i) + "'>" + i + "</a>";	
			}
		}
		var result = numsStr + nums + numsEnd;
		if (currentPage > 1)
		{
			var pre = currentPage - 1; 
			result = "<a href='" + sprintf(pageName, pre) + "' id='previousPage'></a>" + result;
		}
		
		if (currentPage < pageCount)
		{			
			next = currentPage + 1; 
			result +=  " <a href='" + sprintf(pageName, next) + "' id='nextPage'></a>";
		}
		
		return "<div class='pageNumbers'>" +  result + "</div>";
	}
*/	
}