/**
 * this function process the user past locations xml,
 * it is responsible for creating and updating markers and polylines,
 * server doesn't return the last available point of user in past locations xml
 */

//date: in seconds
function timeAgo(date) 
{	
	var now = (new Date().getTime())/1000; //in milliseconds so convert to seconds 	
    var seconds = Math.floor(now - date);
    var returnString = "";
    
    //alert("timeAgo(), date:" + date + " now:" + now + " - seconds:" + seconds + " - interval:" + interval);
    var temp = seconds / 31536000;
    var interval = Math.floor(temp);
    
    if (interval > 0) {
    	//interval = Math.round(temp);
    	
    	if(interval == 1)
    	{
    		returnString = interval + " " + TRACKER.langOperator.year;
    	}
    	else // > 1
    	{
    		returnString = interval + " " + TRACKER.langOperator.year + TRACKER.langOperator.pluralSuffix;
    	}
    }
    else
    {
    	temp = seconds / 2592000;
    	interval = Math.floor(temp);
    	
        if (interval > 0) {
        	//interval = Math.round(temp);
        	
        	if(interval == 1)
        	{
        		returnString = interval + " " + TRACKER.langOperator.month;
        	}
        	else
        	{
        		returnString = interval + " " + TRACKER.langOperator.month + TRACKER.langOperator.pluralSuffix;
        	}
        }
        else
        {
        	temp = seconds / 86400;
        	interval = Math.floor(temp);
        	
            if (interval > 0) {
            	//interval = Math.round(temp);
            	
            	if(interval == 1)
            	{
            		returnString = interval + " " + TRACKER.langOperator.day;
            	}
            	else
            	{
            		returnString = interval + " " + TRACKER.langOperator.day + TRACKER.langOperator.pluralSuffix;
            	}
            }
            else
            {
            	temp = seconds / 3600;
            	interval = Math.floor(temp);
            	
                if (interval > 0) {
                	//interval = Math.round(temp);
                	
                	if(interval == 1)
                	{
                		returnString = interval + " " + TRACKER.langOperator.hour;
                	}
                	else
                	{
                		returnString = interval + " " + TRACKER.langOperator.hour + TRACKER.langOperator.pluralSuffix;
                	}
                }
                else
                {
                	temp = seconds / 60;
                	interval = Math.floor(temp);
                	
                    if (interval > 0) {
                    	//interval = Math.round(temp);
                    	
                    	if(interval == 1)
                    	{
                    		returnString = interval + " " + TRACKER.langOperator.minute;
                    	}
                    	else
                    	{
                    		returnString = interval + " " + TRACKER.langOperator.minute + TRACKER.langOperator.pluralSuffix;
                    	}
                    }
                    else
                    {
                    	interval = Math.floor(seconds);
                    	
                    	if(interval == 1)
                    	{
                    		returnString = interval + " " + TRACKER.langOperator.second;
                    	}
                    	else
                    	{
                    		returnString = interval + " " + TRACKER.langOperator.second + TRACKER.langOperator.pluralSuffix;
                    	}                   	
                    }
                }                
            }
        }   	
    }
    
    //alert(returnString + " " + TRACKER.langOperator.ago);

    return returnString + " " + TRACKER.langOperator.ago;
}

function getLocalDateTime(unixTimeStamp)
{
	var date = new Date(unixTimeStamp*1000);
	
	return date.getDate() + ' ' + TRACKER.monthNames[date.getMonth()] + ' ' + date.getFullYear() + ' ' + ('0' + date.getHours()).slice(-2) + ':' + ('0' + date.getMinutes()).slice(-2) + ':' + ('0' + date.getSeconds()).slice(-2);
}

function processUserPastLocations(MAP, locations, userId){
	//var pastPoints = []; 
	var mapMarker = [];
	
	if ((typeof TRACKER.users[userId].polyline == "undefined") || (TRACKER.users[userId].polyline == null)) 
	{
		//alert("polyline is UNdefined");
		
		var firstPoint = new MapStruct.Location({latitude:TRACKER.users[userId].latitude, longitude:TRACKER.users[userId].longitude});
		TRACKER.users[userId].polyline = MAP.initializePolyline();
		MAP.addPointToPolyline(TRACKER.users[userId].polyline, firstPoint);
	}
	else
	{
		//alert("polyline is defined");
	}

	$.each(locations, function(key, value){
		//var location = $(this);
		var latitude = value.latitude;
		var longitude = value.longitude;
		var altitude = value.altitude;
		var time = value.time;
		var timestamp = value.timestamp;
		//var timeAgo = value.timeAgo;
		var deviceId = value.deviceId;
		//var userType = value.userType;
		var address = value.address;
		var locationSource = value.locationSource;
		
		//alert("key:" + key + " - time:" + value.time);

		var myPoint = new MapStruct.Location({latitude:latitude, longitude:longitude});
		//pastPoints.push(myPoint);

		var gmarker = MAP.putMarker(myPoint, "images/marker.png", false, false, 10, 10); //var gmarker = MAP.putMarker(point);
		var iWindow = MAP.initializeInfoWindow();
		var markerInfoWindow = new MapStruct.MapMarker({marker:gmarker, infoWindow:iWindow, infoWindowIsOpened:false, point:myPoint, pointAdded:false});

		MAP.setMarkerClickListener(gmarker,function (){

			var tr = TRACKER.users[userId].mapMarker.indexOf(markerInfoWindow);
			
			//alert("tr:" + tr);
			
			var previousGMarkerIndex = tr + 1; // it is reverse because 
			var nextGMarkerIndex = tr - 1;    // as index decreases, the current point gets closer
			
			var deviceIdInfo = "";
			
//			if(userType == 1/*GPS Device*/)
//			{
//				deviceIdInfo = TRACKER.langOperator.deviceId + ": " + deviceId;
//			}
			
			var userWasHereString = null;
			
			if(LAN_OPERATOR.lang == 'en')
			{
				userWasHereString = TRACKER.langOperator.wasHere + ' ' + timeAgo(timestamp) + ' (' + getLocalDateTime(timestamp) + ') ';
			}
			else
			{
				userWasHereString = timeAgo(timestamp) + ' (' + getLocalDateTime(timestamp) + ') ' + TRACKER.langOperator.wasHere;
			}
			
			var iconLocationSource;
			var iconTitle;
			
			if(-1 == locationSource)
			{
				iconLocationSource = 'icon-warning';
				iconTitle = TRACKER.langOperator.locationInfoSourceUnknown;
			}
			else if((0 == locationSource) || (2 == locationSource))
			{
				iconLocationSource = 'icon-monitor2';
				iconTitle = TRACKER.langOperator.thisLocationInfoSentFromWebSite;		
			}
			else if(1 == locationSource)
			{
				iconLocationSource = 'icon-mobile';
				iconTitle = TRACKER.langOperator.thisLocationInfoSentFromMobileDevice;		
			}
			else
			{
				iconLocationSource = 'icon-warning';
				iconTitle = TRACKER.langOperator.locationInfoSourceUnknown;		
			}			

			var content = 
				  '<div style="width:280px; height:180px;">'
				+ 	'<div style="display:inline-block;vertical-align:middle;cursor:text;max-width:230px;word-wrap:break-word;line-height:20px;"><b><font size="5">' + TRACKER.users[userId].realname + '</font></b>' +  ' ' + '<font size="3">' +  userWasHereString  + ':' + '</font> <div class="hi-icon-in-list ' + iconLocationSource + '" style="color:#FF7F00;display:inline-block;position:absolute;left:230px;top:11px;cursor:default;" class="vtip" title="' + iconTitle + '"></div> </div>'  
				+ 	'</br></br>'
				//+ 	'<div style="cursor:text;">' + time + ' - (' + latitude + ", " + longitude + ')' + '</div>'
				+ 	'<div style="cursor:text;">' + address + '</div>'				
				+ 	'</br>'				
				+ 	'<div style="position:absolute;bottom:10px;">'
				+ 		'<a class="infoWinOperations med-icon-bordered-effect med-icon-effect-a" href="javascript:TRACKER.showPointGMarkerInfoWin('+ tr +','+ previousGMarkerIndex +','+ userId +')">'+ '<div class="med-icon-bordered icon-arrow-left vtip" title="' + TRACKER.langOperator.previousPoint + '"></div>' + '</a>'
				+		'<a class="infoWinOperations med-icon-bordered-effect med-icon-effect-a" style="margin-left:5px;" href="javascript:TRACKER.showPointGMarkerInfoWin('+ tr +',' + nextGMarkerIndex +','+ userId +')">'+ '<div class="med-icon-bordered icon-arrow-right vtip" title="' + TRACKER.langOperator.nextPoint + '"></div>' + '</a>'
				+ 		'<a class="infoWinOperations med-icon-effect med-icon-effect-a" style="margin-left:76px;" href="javascript:TRACKER.clearTraceLines('+ userId +')">'+ '<div class="med-icon icon-eraser vtip" title="' + TRACKER.langOperator.clearTraceLines + '"></div>' + '</a>'
				+ 		'<a class="infoWinOperations med-icon-effect med-icon-effect-a" href="javascript:TRACKER.zoomPoint('+ latitude +','+ longitude +')">'+ '<div class="med-icon icon-zoomIn1 vtip" title="' + TRACKER.langOperator.zoom + '"></div>' + '</a>'				
				+ 		'<a class="infoWinOperations med-icon-effect med-icon-effect-a" href="javascript:TRACKER.zoomOutPoint('+ latitude +','+ longitude +')">'+ '<div class="med-icon icon-zoomOut1 vtip" title="' + TRACKER.langOperator.zoomOut + '"></div>' + '</a>'
				+ 		'<a class="infoWinOperations med-icon-effect med-icon-effect-a" href="javascript:TRACKER.zoomMaxPoint('+ latitude +','+ longitude +')">'+ '<div class="med-icon icon-zoomMax5 vtip" title="' + TRACKER.langOperator.zoomMax + '"></div>' + '</a>'		
				+ 	'</div>';
				+ '</div>';				
			
			//MAP.addPointToPolyline(TRACKER.users[userId].polyline, point);
			
			MAP.setContentOfInfoWindow(TRACKER.users[userId].mapMarker[tr].infoWindow,content);			
			MAP.openInfoWindow(TRACKER.users[userId].mapMarker[tr]);
		});

		mapMarker.push(markerInfoWindow);
	});

	var tmp = TRACKER.users[userId].mapMarker;		
	TRACKER.users[userId].mapMarker = tmp.concat(mapMarker);	
}

function getContentFor(userId, imageSrc) {
	var iconLocationSource;
	var iconTitle;
	
	if(-1 == TRACKER.users[userId].locationSource)
	{
		iconLocationSource = 'icon-warning';
		iconTitle = TRACKER.langOperator.locationInfoSourceUnknown;
	}
	else if((0 == TRACKER.users[userId].locationSource) || (2 == TRACKER.users[userId].locationSource))
	{
		iconLocationSource = 'icon-monitor2';
		iconTitle = TRACKER.langOperator.thisLocationInfoSentFromWebSite;		
	}
	else if(1 == TRACKER.users[userId].locationSource)
	{
		iconLocationSource = 'icon-mobile';
		iconTitle = TRACKER.langOperator.thisLocationInfoSentFromMobileDevice;		
	}
	else
	{
		iconLocationSource = 'icon-warning';
		iconTitle = TRACKER.langOperator.locationInfoSourceUnknown;		
	}	
	
	var content = 
		  '<div style="width:280px; height:180px;">'		
		+ 	'<div><div style="display:inline-block;vertical-align:middle;">' + '<img src="' + imageSrc + '" width="44px" height="48px"/>' + '</div><div style="display:inline-block;vertical-align:middle;padding-left:5px;cursor:text;max-width:188px;word-wrap:break-word;line-height:24px;"><b><font size="5">' + TRACKER.users[userId].realname + '</font></b></div><div class="hi-icon-in-list ' + iconLocationSource + '" style="color:#FF7F00;display:inline-block;position:absolute;left:230px;top:11px;" class="vtip" title="' + iconTitle + '"></div></div>'  
		+ 	'</br>'
		+ 	'<div style="cursor:text;">' + getLocalDateTime(TRACKER.users[userId].locationTimeStamp) + ' (' + timeAgo(TRACKER.users[userId].locationTimeStamp) + ')' + '</div>'				
		+ 	'<div style="cursor:text;">' + TRACKER.users[userId].address + '</div>'				
		+ 	'</br>'				
		+ 	'<div style="position:absolute;bottom:10px;">'
		+ 		'<a class="infoWinOperations med-icon-bordered-effect med-icon-effect-a" href="javascript:TRACKER.showPointGMarkerInfoWin('+0+','+1+','+ userId +')">'+ '<div class="med-icon-bordered icon-arrow-left vtip" title="' + TRACKER.langOperator.previousPoint + '"></div>' + '</a>'
		+ 		'<a class="infoWinOperations med-icon-effect med-icon-effect-a" style="margin-left:145px;" href="javascript:TRACKER.zoomPoint('+ TRACKER.users[userId].latitude +','+ TRACKER.users[userId].longitude +')">'+ '<div class="med-icon icon-zoomIn1 vtip" title="' + TRACKER.langOperator.zoom + '"></div>' + '</a>'				
		+ 		'<a class="infoWinOperations med-icon-effect med-icon-effect-a" href="javascript:TRACKER.zoomOutPoint('+ TRACKER.users[userId].latitude +','+ TRACKER.users[userId].longitude +')">'+ '<div class="med-icon icon-zoomOut1 vtip" title="' + TRACKER.langOperator.zoomOut + '"></div>' + '</a>'
		+ 		'<a class="infoWinOperations med-icon-effect med-icon-effect-a" href="javascript:TRACKER.zoomMaxPoint('+ TRACKER.users[userId].latitude +','+ TRACKER.users[userId].longitude +')">'+ '<div class="med-icon icon-zoomMax5 vtip" title="' + TRACKER.langOperator.zoomMax + '"></div>' + '</a>'		
		+ 	'</div>';
		+ '</div>';		
		
	return content;	
}

function getPersonPhotoElement(userId, currentUser)
{
	var personPhotoElement;
	var timeStamp = new Date().getTime();	
	
	switch(TRACKER.users[userId].profilePhotoStatus)
	{
		case "0": //Users::NO_TRACEPER_PROFILE_PHOTO_EXISTS
		{
			if((TRACKER.users[userId].fb_id != 0) && (typeof TRACKER.users[userId].fb_id != "undefined")){			
				personPhotoElement = '<img src="https://graph.facebook.com/'+ TRACKER.users[userId].fb_id +'/picture?type=square" width="44px" height="48px" />';
			}else{
				//personPhotoElement = '<div class="hi-icon-in-list icon-user" style="color:#FFDB58; cursor:default;"></div>';
				personPhotoElement = '<img src="images/UserIcon.png" width="44px" height="48px" />';
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
			personPhotoElement = '<img src="https://graph.facebook.com/'+ TRACKER.users[userId].fb_id +'/picture?type=square" width="44px" height="48px"/>';
		}
		break;

		default:
			//alertMsg("processUsers(), undefined profilePhotoStatus:" + profilePhotoStatus);
			//personPhotoElement = '<div class="hi-icon-in-list icon-user" style="color:#FFDB58; cursor:default;"></div>';
			personPhotoElement = '<img src="images/UserIcon.png" width="44px" height="48px" />';
	}
	
	return personPhotoElement;
}

function getUserContent(userId, personPhotoElement) 
{	
	var iconLocationSource;
	var iconTitle;
	
	if(-1 == TRACKER.users[userId].locationSource)
	{
		iconLocationSource = 'icon-warning';
		iconTitle = TRACKER.langOperator.locationInfoSourceUnknown;
	}
	else if((0 == TRACKER.users[userId].locationSource) || (2 == TRACKER.users[userId].locationSource))
	{
		iconLocationSource = 'icon-monitor2';
		iconTitle = TRACKER.langOperator.thisLocationInfoSentFromWebSite;		
	}
	else if(1 == TRACKER.users[userId].locationSource)
	{
		iconLocationSource = 'icon-mobile';
		iconTitle = TRACKER.langOperator.thisLocationInfoSentFromMobileDevice;		
	}
	else
	{
		iconLocationSource = 'icon-warning';
		iconTitle = TRACKER.langOperator.locationInfoSourceUnknown;		
	}	

	var content = 
		  '<div style="width:280px; height:180px;">'
		+ 	'<div><div style="display:inline-block;vertical-align:middle;">' + personPhotoElement + '</div><div style="display:inline-block;vertical-align:middle;padding-left:5px;cursor:text;max-width:188px;word-wrap:break-word;line-height:24px;"><b><font size="5">' + TRACKER.users[userId].realname + '</font></b></div><div class="hi-icon-in-list ' + iconLocationSource + '" style="color:#FF7F00;display:inline-block;position:absolute;left:230px;top:11px;" class="vtip" title="' + iconTitle + '"></div></div>'  
		+ 	'</br>'
		+ 	'<div style="cursor:text;">' + getLocalDateTime(TRACKER.users[userId].locationTimeStamp) + ' (' + timeAgo(TRACKER.users[userId].locationTimeStamp) + ')' + '</div>'				
		+ 	'<div style="cursor:text;">' + TRACKER.users[userId].address + '</div>'				
		+ 	'</br>'				
		+ 	'<div style="position:absolute;bottom:10px;">'
		+ 		'<a class="infoWinOperations med-icon-bordered-effect med-icon-effect-a" href="javascript:TRACKER.showPointGMarkerInfoWin('+0+','+1+','+ userId +')">'+ '<div class="med-icon-bordered icon-arrow-left vtip" title="' + TRACKER.langOperator.previousPoint + '"></div>' + '</a>'
		+ 		'<a class="infoWinOperations med-icon-effect med-icon-effect-a" style="margin-left:145px;" href="javascript:TRACKER.zoomPoint('+ TRACKER.users[userId].latitude +','+ TRACKER.users[userId].longitude +')">'+ '<div class="med-icon icon-zoomIn1 vtip" title="' + TRACKER.langOperator.zoom + '"></div>' + '</a>'				
		+ 		'<a class="infoWinOperations med-icon-effect med-icon-effect-a" href="javascript:TRACKER.zoomOutPoint('+ TRACKER.users[userId].latitude +','+ TRACKER.users[userId].longitude +')">'+ '<div class="med-icon icon-zoomOut1 vtip" title="' + TRACKER.langOperator.zoomOut + '"></div>' + '</a>'
		+ 		'<a class="infoWinOperations med-icon-effect med-icon-effect-a" href="javascript:TRACKER.zoomMaxPoint('+ TRACKER.users[userId].latitude +','+ TRACKER.users[userId].longitude +')">'+ '<div class="med-icon icon-zoomMax5 vtip" title="' + TRACKER.langOperator.zoomMax + '"></div>' + '</a>'		
		+ 	'</div>';
		+ '</div>';		
		
	return content;	
}

function checkAndUpdateUserQueryInterval(par_user, par_userId) 
{		
	var nowInSeconds = (new Date().getTime())/1000; //in milliseconds so convert to seconds 	
    var timeInSeconds = Math.floor(nowInSeconds - par_user.locationTimeStamp);
    var residualSeconds; //artık sn
    
    $("#userTimeAgo_" + par_userId).html(timeAgo($("#userTimestamp_" + par_userId).html()));
    
    if(timeInSeconds < 60)
    {
    	par_user.timeAgoTimerInterval = 1000;
    }
    else if(timeInSeconds < 60*60)
    {
    	if(par_user.timeAgoTimerInterval < 60*1000) //Saniyeden gecis yapiliyorsa veya kalan surelik timer kurulduysa, direk 1 dk timer kur
    	{
    		par_user.timeAgoTimerInterval = 60*1000;
    	}
    	else //Sayfa acildiginda dk ise, once bir sonraki dk ya kadar kalan sure kadar timer kur, sonra 1 dk lık timer kur
    	{
    		residualSeconds = timeInSeconds % 60;
    		par_user.timeAgoTimerInterval = (60-residualSeconds)*1000;
    	}
    }
    else if(timeInSeconds < 24*60*60)
    {
    	if(par_user.timeAgoTimerInterval < 60*60*1000) //Dakikadan gecis yapiliyorsa veya kalan surelik timer kurulduysa, direk 1 dk timer kur
    	{
    		par_user.timeAgoTimerInterval =  60*60*1000;
    	}
    	else //Sayfa acildiginda saat ise, once bir sonraki saate kadar kalan sure kadar timer kur, sonra 1 dk lık timer kur
    	{
    		residualSeconds = timeInSeconds % 60*60;
    		par_user.timeAgoTimerInterval = (60-residualSeconds)*1000;
    	}
    }
    else if(timeInSeconds < 30*24*60*60)
    {
    	if(par_user.timeAgoTimerInterval < 24*60*60*1000) //Saatten gecis yapiliyorsa veya kalan surelik timer kurulduysa, direk 1 dk timer kur
    	{
    		par_user.timeAgoTimerInterval =  24*60*60*1000;
    	}
    	else //Sayfa acildiginda gun ise, once bir sonraki gune kadar kalan sure kadar timer kur, sonra 1 dk lık timer kur
    	{
    		residualSeconds = timeInSeconds % 24*60*60;
    		par_user.timeAgoTimerInterval = (60-residualSeconds)*1000;
    	}
    }
    else if(timeInSeconds < 12*30*24*60*60)
    {
    	if(par_user.timeAgoTimerInterval < 30*24*60*60*1000) //Gunden gecis yapiliyorsa veya kalan surelik timer kurulduysa, direk 1 dk timer kur
    	{
    		par_user.timeAgoTimerInterval =  30*24*60*60*1000;
    	}
    	else //Sayfa acildiginda ay ise, once bir sonraki aya kadar kalan sure kadar timer kur, sonra 1 dk lık timer kur
    	{
    		residualSeconds = timeInSeconds % 30*24*60*60;
    		par_user.timeAgoTimerInterval = (60-residualSeconds)*1000;
    	}
    }
    else
    {
    	par_user.timeAgoTimerInterval =  12*30*24*60*60*1000;
    }
    
	clearTimeout(par_user.timeAgoTimer);
	par_user.timeAgoTimer = setTimeout(function() {checkAndUpdateUserQueryInterval(par_user, par_userId);}, par_user.timeAgoTimerInterval);
}

function checkAndUpdateUserInfoWindow(par_timestamp, par_mapMarker, par_userId, par_personPhotoElement) 
{		
	var nowInSeconds = (new Date().getTime())/1000; //in milliseconds so convert to seconds 	
    var timeInSeconds = Math.floor(nowInSeconds - par_timestamp);
    var residualSeconds; //artık sn
    
	var content = getUserContent(par_userId, par_personPhotoElement);		
	MAP.setContentOfInfoWindow(par_mapMarker.infoWindow, content);
    
    if(timeInSeconds < 60)
    {
    	par_mapMarker.timeAgoTimerInterval = 1000;
    }
    else if(timeInSeconds < 60*60)
    {
    	if(par_mapMarker.timeAgoTimerInterval < 60*1000) //Saniyeden gecis yapiliyorsa veya kalan surelik timer kurulduysa, direk 1 dk timer kur
    	{
    		par_mapMarker.timeAgoTimerInterval = 60*1000;
    	}
    	else //Sayfa acildiginda dk ise, once bir sonraki dk ya kadar kalan sure kadar timer kur, sonra 1 dk lık timer kur
    	{
    		residualSeconds = timeInSeconds % 60;
    		par_mapMarker.timeAgoTimerInterval = (60-residualSeconds)*1000;
    	}
    }
    else if(timeInSeconds < 24*60*60)
    {
    	if(par_mapMarker.timeAgoTimerInterval < 60*60*1000) //Dakikadan gecis yapiliyorsa veya kalan surelik timer kurulduysa, direk 1 dk timer kur
    	{
    		par_mapMarker.timeAgoTimerInterval = 60*60*1000;
    	}
    	else //Sayfa acildiginda saat ise, once bir sonraki saate kadar kalan sure kadar timer kur, sonra 1 dk lık timer kur
    	{
    		residualSeconds = timeInSeconds % 60*60;
    		par_mapMarker.timeAgoTimerInterval = (60-residualSeconds)*1000;
    	}
    }
    else if(timeInSeconds < 30*24*60*60)
    {
    	if(par_mapMarker.timeAgoTimerInterval < 24*60*60*1000) //Saatten gecis yapiliyorsa veya kalan surelik timer kurulduysa, direk 1 dk timer kur
    	{
    		par_mapMarker.timeAgoTimerInterval = 24*60*60*1000;
    	}
    	else //Sayfa acildiginda gun ise, once bir sonraki gune kadar kalan sure kadar timer kur, sonra 1 dk lık timer kur
    	{
    		residualSeconds = timeInSeconds % 24*60*60;
    		par_mapMarker.timeAgoTimerInterval = (60-residualSeconds)*1000;
    	}
    }
    else if(timeInSeconds < 12*30*24*60*60)
    {
    	if(par_mapMarker.timeAgoTimerInterval < 30*24*60*60*1000) //Gunden gecis yapiliyorsa veya kalan surelik timer kurulduysa, direk 1 dk timer kur
    	{
    		par_mapMarker.timeAgoTimerInterval = 30*24*60*60*1000;
    	}
    	else //Sayfa acildiginda ay ise, once bir sonraki aya kadar kalan sure kadar timer kur, sonra 1 dk lık timer kur
    	{
    		residualSeconds = timeInSeconds % 30*24*60*60;
    		par_mapMarker.timeAgoTimerInterval = (60-residualSeconds)*1000;
    	}
    }
    else
    {
    	par_mapMarker.timeAgoTimerInterval = 12*30*24*60*60*1000;
    }
    
	clearTimeout(par_mapMarker.timeAgoTimer);
	par_mapMarker.timeAgoTimer = setTimeout(function() {checkAndUpdateUserInfoWindow(par_timestamp, par_mapMarker, par_userId, par_personPhotoElement);}, par_mapMarker.timeAgoTimerInterval);
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
	var newestTimestamp = 0;
	var isAnyTimestampChanged = false;

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
		var locationTimeStamp = value.timestamp;
		var message = value.message;
		var deviceId = value.deviceId;
		var userType = value.userType;
		var location = new MapStruct.Location({latitude:latitude, longitude:longitude});
		var visible = false;
		
		if(userId != currentUser)
		{
			if(locationTimeStamp > newestTimestamp)
			{
				newestTimestamp = locationTimeStamp;
			}
		}

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
		
		//alertMsg("userId:" + userId);		

		if (typeof TRACKER.users[userId] == "undefined") 
		{		
			var userMarker;
			var timeStamp = new Date().getTime();
			
			switch(profilePhotoStatus)
			{
				case "0": //Users::NO_TRACEPER_PROFILE_PHOTO_EXISTS
				{
					if((fb_id != 0) && (typeof fb_id != "undefined")){					
						userMarker = MAP.putMarker(location, "https://graph.facebook.com/"+ fb_id + "/picture?type=square", visible, true, 16, 18);
					}else{
						userMarker = MAP.putMarker(location, "images/person.png", visible, false, 8, 8);
					}
				}
				break;

				case "1": //Users::TRACEPER_PROFILE_PHOTO_EXISTS
				case "3": //Users::BOTH_PROFILE_PHOTOS_EXISTS_USE_TRACEPER
				{
					if(userId === currentUser) //Current user ise cache kullanma (foto degistirirse hemen gorebilsin diye) 
					{					
						userMarker = MAP.putMarker(location, "profilePhotos/" + userId + ".png" + "?random=" + timeStamp, visible, true, 16, 18);						
					}
					else //Diger kullanicilar icin cache kullan
					{					
						userMarker = MAP.putMarker(location, "profilePhotos/" + userId + ".png", visible, true, 16, 18);						
					}					
				}
				break;

				case "2": //Users::BOTH_PROFILE_PHOTOS_EXISTS_USE_FACEBOOK
				{				
					userMarker = MAP.putMarker(location, "https://graph.facebook.com/"+ fb_id + "/picture?type=square", visible, true, 16, 18);
				}
				break;

				default:
					//alertMsg("processUsers(), undefined profilePhotoStatus:" + profilePhotoStatus);
					userMarker = MAP.putMarker(location, "images/person.png", visible, false, 8, 8);				
			}					

			if(userId === currentUser)
			{
				//main.php ve userAreaView.php dosyalarinda kullaniliyor
				currentUserMarker = userMarker;
			}			
			
			newFriend = true;
			//alertMsg('userId:' + userId + ' added');
	
			var markerInfo= new MapStruct.MapMarker({marker:userMarker, infoWindowIsOpened:false, timeAgoTimerInterval:0});
			
			TRACKER.users[userId] = new TRACKER.User( {//username:username,
				realname:realname,
				latitude:latitude,
				longitude:longitude,
				address:address,
				friendshipStatus:isFriend,
				//time:time,
				time:dataArrivedTime,
				locationTimeStamp:locationTimeStamp,
				message:message,
				statusMessage:status_message,
				deviceId:deviceId,
				userType:userType,
				mapMarker:new Array(markerInfo),
				locationCalculatedTime:locationCalculatedTime,
				locationSource:locationSource,
				fb_id:fb_id,
				profilePhotoStatus:profilePhotoStatus
			});
			
			checkAndUpdateUserQueryInterval(TRACKER.users[userId], userId);
	
			var personPhotoElement = getPersonPhotoElement(userId, currentUser);
			var content = getUserContent(userId, personPhotoElement);	
				
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
	
				var content = getUserContent(userId, personPhotoElement);	
								
				MAP.setContentOfInfoWindow(TRACKER.users[userId].mapMarker[0].infoWindow, content);				
				MAP.openInfoWindow(TRACKER.users[userId].mapMarker[0]);
				checkAndUpdateUserInfoWindow(TRACKER.users[userId].locationTimeStamp, TRACKER.users[userId].mapMarker[0], userId, personPhotoElement);
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

			if ((TRACKER.users[userId].latitude != latitude) || (TRACKER.users[userId].longitude != longitude) || 
				(TRACKER.users[userId].locationTimeStamp != locationTimeStamp)/* || true*/)
			{				
				if(TRACKER.users[userId].locationTimeStamp != locationTimeStamp)
				{
					if(false == isAnyTimestampChanged) //Sadece ilk degisimde view'i update et yai bir kere sunucuyla etkilesime gir
					{
						$.fn.yiiGridView.update("userListView");
						isAnyTimestampChanged = true;
					}					
					
					TRACKER.users[userId].locationTimeStamp = locationTimeStamp;
					checkAndUpdateUserQueryInterval(TRACKER.users[userId], userId);
				}				
				
				TRACKER.users[userId].latitude = latitude;
				TRACKER.users[userId].longitude = longitude;
				TRACKER.users[userId].time = time;
				TRACKER.users[userId].locationCalculatedTime = locationCalculatedTime;
				//TRACKER.users[userId].locationTimeStamp = locationTimeStamp;
				TRACKER.users[userId].locationSource = locationSource;
				TRACKER.users[userId].deviceId = deviceId;
				TRACKER.users[userId].userType = userType;
				TRACKER.users[userId].friendshipStatus = isFriend;
				TRACKER.users[userId].address = address;				
				
				//var isWindowOpen = TRACKER.users[userId].mapMarker[0].infoWindowIsOpened;
								
				var personPhotoElement = getPersonPhotoElement(userId, currentUser);
				var content = getUserContent(userId, personPhotoElement);

				//If user location changed, update marker position
				TRACKER.users[userId].mapMarker[0].marker.setPosition(new google.maps.LatLng(latitude, longitude));
			
//				if (isWindowOpen == true) {
//					MAP.closeInfoWindow(TRACKER.users[userId].mapMarker[0]);
//					MAP.setContentOfInfoWindow(TRACKER.users[userId].mapMarker[0].infoWindow, content);			
//					MAP.openInfoWindow(TRACKER.users[userId].mapMarker[0]);
//					checkAndUpdateUserInfoWindow(locationTimeStamp, TRACKER.users[userId].mapMarker[0], userId, personPhotoElement);
//				}
			}
						
			TRACKER.users[userId].latitude = latitude;
			TRACKER.users[userId].longitude = longitude;
			TRACKER.users[userId].time = time;
			TRACKER.users[userId].locationCalculatedTime = locationCalculatedTime;
			TRACKER.users[userId].locationTimeStamp = locationTimeStamp;
			TRACKER.users[userId].locationSource = locationSource;
			TRACKER.users[userId].deviceId = deviceId;
			TRACKER.users[userId].userType = userType;
			TRACKER.users[userId].friendshipStatus = isFriend;
			TRACKER.users[userId].address = address;
		}
	});
	
	//checkAndUpdateUserQueryInterval(newestTimestamp);
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
	    	if((TRACKER.showUsersOnTheMap == false) && (key != TRACKER.userId))
			{
				MAP.closeInfoWindow(TRACKER.users[key].mapMarker[0]);
				clearTimeout(TRACKER.users[key].mapMarker[0].timeAgoTimer);
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
	    		MAP.closeInfoWindow(TRACKER.users[key].mapMarker[0]);
	    		clearTimeout(TRACKER.users[key].mapMarker[0].timeAgoTimer);
	    		
	    		TRACKER.clearTraceLines(key);
	
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

function processUploads(MAP, deletedUploads, uploads, par_updateType, par_thumbSuffix, par_isPublic){
	
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
		var timestamp = value.timestamp;
		var rating = value.rating;
		var description = value.description; //value.description; //$(image).attr('description');
		var fileExists = value.fileExists;
		var isPublic = value.isPublic;
		
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
			var userMarker = MAP.putMarker(location, image, false, false, 0, 0);
			var iWindow = MAP.initializeInfoWindow();
			var markerInfoWindow = new MapStruct.MapMarker({marker:userMarker, infoWindow:iWindow, infoWindowIsOpened:false});
			
			TRACKER.images[imageId] = new TRACKER.Img({imageId:imageId,
				imageURL:imageURL,
				userId:userId,
				realname:realname,
				latitude:latitude,
				longitude:longitude,
				time:time,
				timestamp:timestamp,
				rating:rating,
				mapMarker:markerInfoWindow,
				description:description,
				isPublic:isPublic
			});
			
			//alertMsg("MAP.setMarkerVisible(true)");
						
			MAP.setMarkerVisible(TRACKER.images[imageId].mapMarker.marker, TRACKER.showImagesOnTheMap); //ADNAN					
			MAP.setMarkerClickListener(TRACKER.images[imageId].mapMarker.marker,function (){
				var image = new Image();

				image.src= TRACKER.images[imageId].imageURL + "&fileType=0"; // + TRACKER.imageOrigSuffix;

				if(fileExists === true)
				{
					//$("#loading").show();
					$(image).load(function(){
						//$("#loading").hide();
						
//						var content = "<div class='origImageContainer'>"
//							+ "<div>"
//							+ "<img src='"+ image.src +"' height='"+ image.height +"' width='"+ image.width +"' class='origImage' />"
//							+ "</div>"
//							+ "<div>"
//							+ TRACKER.images[imageId].description + "<br/>"
//							+ ((par_isPublic == false)?("<a href='javascript:TRACKER.trackUser("+ TRACKER.images[imageId].userId +")' class='uploader'>" + TRACKER.images[imageId].realname + "</a>"):TRACKER.images[imageId].realname)
//							+ "<br/>"
//							+ TRACKER.images[imageId].time + "<br/>"
//							+ TRACKER.images[imageId].latitude + ", " + TRACKER.images[imageId].longitude
//							+ "</div>"
//							+ '<ul class="sf-menu"> '
//							+ '<li>'+'<a class="infoWinOperations" href="javascript:TRACKER.zoomPoint('+ TRACKER.images[imageId].latitude +','+ TRACKER.images[imageId].longitude +')">'
//							+ TRACKER.langOperator.zoom
//							+'</a>'+ '</li>'
//							+ '<li>'+'<a class="infoWinOperations" href="javascript:TRACKER.zoomMaxPoint('+ TRACKER.images[imageId].latitude +','+ TRACKER.images[imageId].longitude +')">'
//							+ TRACKER.langOperator.zoomMax
//							+'</a>'+'</li>'
//							+'<li>'+'<a href="javascript:TRACKER.showCommentWindow(1,1,null)" id="commentsWindow"> Display Comments</a>'
//							+'</a>'+'</li>'
//							+'</li>'
//							+ '</ul>'
//							+ "</div>";
						
						var userSharedThisPhoto = null;

						if(LAN_OPERATOR.lang == 'en')
						{
							if(isPublic == "1")
							{
								userSharedThisPhoto = ' shared this photo as "Public" ' + timeAgo(timestamp) + ' (' + getLocalDateTime(timestamp) + ') with the comment "' + TRACKER.images[imageId].description + '".';
							}
							else
							{
								userSharedThisPhoto = ' shared this photo ' + timeAgo(timestamp) + ' (' + getLocalDateTime(timestamp) + ') with the comment "' + TRACKER.images[imageId].description + '".';
							}
						}
						else
						{
							if(isPublic == "1")
							{
								userSharedThisPhoto = ' bu fotoğrafı ' + timeAgo(timestamp) + ' (' + getLocalDateTime(timestamp) + ') ' + '"' + TRACKER.images[imageId].description + '" yorumu ile "Herkese Açık" olarak paylaştı.';
							}
							else
							{
								userSharedThisPhoto = ' bu fotoğrafı ' + timeAgo(timestamp) + ' (' + getLocalDateTime(timestamp) + ') ' + '"' + TRACKER.images[imageId].description + '" yorumu ile paylaştı.';
							}	
						}						
											
						var content = 
							  //'<div style="width:280px; height:180px;">'
							  '<div style="width:400px; height:440px;">'
							//+ 	'<div><div style="display:inline-block;vertical-align:middle;">' + personPhotoElement + '</div><div style="display:inline-block;vertical-align:middle;padding-left:5px;cursor:text;"><b><font size="5">' + TRACKER.users[userId].realname + '</font></b></div></div>'
							+ 	'<div><img src="' + image.src + '" width="400px" height="300px"/></div>'
							+ 	'</br>'				
							+ 	'<div style="cursor:text;word-wrap: break-word;">' + '<b>' + TRACKER.images[imageId].realname + '</b>' + userSharedThisPhoto + '</div>'	
							+ 	'</br>'				
							+ 	'<div style="position:absolute;bottom:10px;">'						
							+ 		'<a class="infoWinOperations med-icon-effect med-icon-effect-a" style="margin-left:142px;" href="javascript:TRACKER.zoomPoint('+ TRACKER.images[imageId].latitude +','+ TRACKER.images[imageId].longitude +')">'+ '<div class="med-icon icon-zoomIn1 vtip" title="' + TRACKER.langOperator.zoom + '"></div>' + '</a>'				
							+ 		'<a class="infoWinOperations med-icon-effect med-icon-effect-a" style="margin-left:20px;" href="javascript:TRACKER.zoomOutPoint('+ TRACKER.images[imageId].latitude +','+ TRACKER.images[imageId].longitude +')">'+ '<div class="med-icon icon-zoomOut1 vtip" title="' + TRACKER.langOperator.zoomOut + '"></div>' + '</a>'
							+ 		'<a class="infoWinOperations med-icon-effect med-icon-effect-a" style="margin-left:20px;" href="javascript:TRACKER.zoomMaxPoint('+ TRACKER.images[imageId].latitude +','+ TRACKER.images[imageId].longitude +')">'+ '<div class="med-icon icon-zoomMax5 vtip" title="' + TRACKER.langOperator.zoomMax + '"></div>' + '</a>'		
							+ 	'</div>';
							+ '</div>';						

						//var content = "<video id='my_video_2' class='video-js vjs-default-skin' controls preload='auto' width='320' height='264'><source src='http://localhost/traceper/branches/DevWebInterface/upload/oceans-clip.mp4' type='video/mp4'></video>";
						
						//var content = "<div> Deneme </div>";
						
						//var content = '<video id="my_video_2" class="video-js vjs-default-skin" controls preload="auto" width="320" height="264" data-setup="{}"><source src="http://localhost/traceper/branches/DevWebInterface/upload/oceans-clip.mp4" type="video/mp4"></video>'; 

						MAP.setContentOfInfoWindow(TRACKER.images[imageId].mapMarker.infoWindow,content);						
						MAP.openInfoWindow(TRACKER.images[imageId].mapMarker);	
						
						MAP.setInfoWindowCloseListener(TRACKER.images[imageId].mapMarker.infoWindow, function (){
							if ($('#showPhotosOnMap').attr('checked') == false){
								MAP.setMarkerVisible(TRACKER.images[imageId].mapMarker.marker,false);
							}				
						});
					});					
				}
				else
				{
					TRACKER.showMessageDialog(TRACKER.langOperator.thisPhotoIsUnavailable);
				}
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
			
			if(TRACKER.showImagesOnTheMap == false)
			{
				MAP.closeInfoWindow(TRACKER.images[key].mapMarker);
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
    		MAP.closeInfoWindow(TRACKER.images[uploadId].mapMarker);

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
			var userMarker = MAP.putMarker(location, image, false, false, 0, 0);
			var iWindow = MAP.initializeInfoWindow();
			var markerInfoWindow = new MapStruct.MapMarker({marker:userMarker, infoWindow:iWindow, infoWindowIsOpened:false});
			
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
					MAP.openInfoWindow(TRACKER.images[imageId].mapMarker);	
					
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
		    	MAP.closeInfoWindow(TRACKER.images[key].mapMarker);
				
		    	if((updateType === 'all') && (uploadIdArray.indexOf(key) === -1))
		    	{
		    		//alertMsg('uploadId:' + key + 'deleted');
		    		MAP.setMarkerVisible(TRACKER.images[key].mapMarker.marker, false);	
		    		MAP.closeInfoWindow(TRACKER.images[key].mapMarker);

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
