var showPhotosOnMapCookieId = "showPhotosOnMap";

function bindElements(langOperator, trackerOp) 
{	 		
	/**
	 * binding operation to search user
	 */
	$('#friendsList .search #searchButton').click(function(){
		trackerOp.searchUser($('#friendsList .search #searchBox').attr('value'), 1);					
	});
	$('#friendsList .search #searchBox').keydown(function(ev){
		if (ev.keyCode == 13) {
			trackerOp.searchUser($('#friendsList .search #searchBox').attr('value'), 1);
		}
	}).focus(function(){
		if ($(this).attr("value") == langOperator.usersSearchBox){
			$(this).attr("value", "");
		}
	});
	$("#signout").click(function(){
		trackerOp.signout();
	});
	
	$(".userOperations").mouseover(function(){
		
		$(".userOperations div").hide();
		
		$("div", $(this)).css('display','inline');
		

	});
	
	/**
	 * binding operations to search image
	 */
	$('#photosList .search #searchButton').click(function(){
		trackerOp.searchImage($('#photosList .search #searchBox').attr('value'), false, 1);					
	});
	$('#photosList .search #searchBox').keydown(function(ev){
		if (ev.keyCode == 13) {
			trackerOp.searchImage($('#photosList .search #searchBox').attr('value'), false, 1);
		}
	}).focus(function(){
		if ($(this).attr("value") == langOperator.photosSearchBox){
			$(this).attr("value", "");
		}
		
	});
	
	$('#showPhotosOnMap').change(function(){
		var checked = $(this).attr('checked');
		$.cookie(showPhotosOnMapCookieId, checked, {expires:15});

		if (checked == true){
			$(TRACKER.imageIds).each(function(){
				TRACKER.images[this].gmarker.show();
			});
		}
		else {
			$(TRACKER.imageIds).each(function(){
				TRACKER.images[this].gmarker.hide();
			});
		}
	
		
	});
	
	$('#photo_title').click(function(){

		if ($('#photos').children().size() == 0){
			trackerOp.getImageList(1, function(){
				$('#friendsList').slideUp('fast',function(){
					$('#photosList').slideDown();
				});				
			});
		}
		if (TRACKER.allImagesFetched == false){
			TRACKER.getImageListInBg();
		}
		
		$('#friendsList').slideUp('fast',function(){
			$('#photosList').slideDown('fast');
		});
		$(this).addClass('active_title');
		
		$('#user_title').animate({top:'0px'}, 500, function(){});
		$('#photo_title').animate({top:'0px'}, 500, function(){});
				
		$('#user_title div').addClass('arrowImageRight');
		$('#photo_title div').removeClass('arrowImageRight');		
		$('#user_title').removeClass('active_title');
		
	});
	
	$('#user_title').click(function(){
		$('#photosList').slideUp('fast',function(){
			$('#friendsList').slideDown('fast');
			
		});
		$(this).addClass('active_title');
		
		$('#user_title').animate({top:'26px'}, 500, function(){});
		$('#photo_title').animate({top:'-26px'}, 500, function(){});
				
		$('#photo_title div').addClass('arrowImageRight');
		$('#user_title div').removeClass('arrowImageRight');
		$('#photo_title').removeClass('active_title');
	});
	
	$('#friendRequest_title').click(function(){
		TRACKER.getFriendRequests(1);
		$('#photosList').slideUp('fast',function(){
			$('#friendsList').slideDown('fast');
			
		});
	});
	
	$('ul.sf-menu').superfish();  
	$('li#username').click(function(){
		$(this).find('ul').slideToggle('fast');
		
		$(this).hover(function(){}, function(){
			$(this).find('ul').slideUp('fast').hide();
		});
		
		
	});

	$("a[href=#auLink], #logo, .logo_inFullMap").click(function(){
		$('#aboutus').mb_open();
		$('#aboutus').mb_centerOnWindow(true);
	});
	
	//this callback opens the change password window
	$('#changePassword').click(function(){
		$('#changePasswordForm').mb_open();
		$('#changePasswordForm').mb_centerOnWindow(true);
	});
	
	$("#changePasswordButton").click(function(){
		
		if ($('#currentPassword').val() != '' && 
			$('#newPassword').val() != '' &&
			$('#newPasswordAgain').val() != '')
		{
			
			if	($('#newPassword').val() ==  $('#newPasswordAgain').val())
			{
				trackerOp.changePassword($('#newPassword').val(), $('#currentPassword').val());	
				$('#changePasswordForm').mb_close();
				
				$('#currentPassword').val('');
				$('#newPassword').val('');
				$('#newPasswordAgain').val('');
			}
			else {
				TRACKER.showMessage(langOperator.enterSamePassword, "warning");
			}
		}
		else {
			TRACKER.showMessage(langOperator.warningMissingParameter, "warning");
		}
	});
	
	$("#changePasswordCancel").click(function()
	{
		$('#changePasswordForm').mb_close();
	});
	
	$("#inviteUser").click(function(){
		$('#InviteUserForm').mb_open();
		$('#InviteUserForm').mb_centerOnWindow(true);
	});
	
	$('#inviteUserButton').click(function(){
		var useremail = $('#useremail').val();
		var invitationMessage = $('#invitationMessage').val();
		if (useremail != ""){
			TRACKER.inviteUser(useremail, invitationMessage);
		}
		else {
			TRACKER.showMessage(langOperator.warningMissingParameter, "warning");
		}
	});
	
	$("#inviteUserCancel").click(function()
	{
		$('#InviteUserForm').mb_close();
	});
	
	$("a[href=#returnToUserList]").click(function(){
		$('#friendsList > .searchResults').slideUp(function(){ $('#friendsList > #friends').slideDown(); });
	});
	
	$("a[href=#returnToPhotoList]").click(function(){
		$('#photosList > .searchResults').slideUp(function(){ $('#photosList > #photos').slideDown(); });
	});
	
	
	$("#bar").click(function ()	{	
				if ($('#sideBar > #content').css('display') == "none")
				{
					$('.logo_inFullMap').fadeOut().animate({left:'10px'});
					$('#sideBar > #content').fadeIn('slow');
					$('#sideBar').animate({width:'25%'}, function(){  $('#bar').css('background-image','url("images/left.png")') });
					$('#map').animate({width:'75%'});
					$('#bar').animate({right:'75%'});
					
				}	
				else 
				{
					$('.logo_inFullMap').fadeIn().animate({left:'80px'});
					$('#sideBar > #content').fadeOut('slow');
					$('#sideBar').animate({width:'0%'}, 
									function(){ $('#sideBar > #content').hide();
											    $('#bar').css('background-image','url("images/right.png")');
									});
					$('#map').animate({width:'99%'});
					$('#bar').animate({right:'99%'});
					
				}
	});	
	
	$('#sendStatusMessageButton').click(function(){
		var statusMessage = $('#statusMessage').val();
		if (statusMessage != "") {
			var params = "action=WebClientSaveStatusMessage&statusMessage=" + statusMessage;
			TRACKER.ajaxReq(params, function(result){
				alert(result + "mesaj kaydedildi.");
			});
		}
	});
	
	

};	

function setLanguage(langOperator){
	
	$('title').text(langOperator.mark);	
	$('a[href=#auLink]').html(langOperator.aboutTitle);
	$("#lists #user_title").append(langOperator.usersTitle);
	$("#lists #photo_title").append(langOperator.photosTitle);
	$("#friendsList .searchResults a[href=#returnToUserList]").html(langOperator.returnToUserListLink);
	$("#photosList .searchResults a[href=#returnToPhotoList]").html(langOperator.returnToPhotoListLink)
	$("#photosList .search #searchBox").attr('value', langOperator.photosSearchBox);
	$("#friendsList .search #searchBox").attr('value', langOperator.usersSearchBox);
	$("#aboutus").html(langOperator.aboutus);
//	$("#userarea").prepend(langOperator.hi);
	
	$("#currentPasswordLabel").text(langOperator.currentPasswordLabel + " :");
	$("#newPasswordLabel").text(langOperator.newPasswordLabel + " :");
	$("#newPasswordAgainLabel").text(langOperator.newPasswordAgainLabel + " :");
	$("#changePasswordButton").val(langOperator.submitFormButtonLabel);
	$("#changePasswordCancel, #inviteUserCancel").val(langOperator.cancelFormButtonLabel);
	$("#inviteUserEmailLabel").text(langOperator.emailLabel);
	$("#inviteUserButton").val(langOperator.inviteUserLabel);
	$("#signout div").append(langOperator.signout);
	$("#changePassword div").text(langOperator.changePassword);
	$("#inviteUser div").append(langOperator.inviteUserLabel);
	$("#friendRequests div").append(langOperator.friendRequests);
	
	$("#inviteUserInvitationMessage").text(langOperator.invitationMessage);
	$("#mailEntraceAlert").text(langOperator.mailEntranceRule);
	
//	$("#loading p").html(langOperator.loading);
	
}