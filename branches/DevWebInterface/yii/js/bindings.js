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
/*	
	$("#signout").click(function(){
		trackerOp.signout();
	});
*/	
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

		var MAP = TRACKER.getMap();
		if (checked == true){
			$(TRACKER.imageIds).each(function(){
				MAP.setMarkerVisible(TRACKER.images[this].mapMarker.marker, true);
			});
		}
		else {
			$(TRACKER.imageIds).each(function(){
				MAP.setMarkerVisible(TRACKER.images[this].mapMarker.marker, false);
			});
		}		
	});
/*	
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
*/	
	/*$('#friendRequest_title').click(function(){
		TRACKER.getFriendRequests(1);
		$('#photosList').slideUp('fast',function(){
			$('#friendsList').slideDown('fast');
			
		});
	});*/
	
	$('ul.sf-menu').superfish();  
	$('li#username').click(function(){
		$(this).find('ul').slideToggle('fast');
		
		$(this).hover(function(){}, function(){
			$(this).find('ul').slideUp('fast').hide();
		});
		
		
	});
/*
	$("#changePasswordButton").click(function(){
		
		if ($('#currentPassword').val() != '' && 
			$('#newPassword').val() != '' &&
			$('#newPasswordAgain').val() != '')
		{
			
			if	($('#newPassword').val() ==  $('#newPasswordAgain').val())
			{
				trackerOp.changePassword($('#newPassword').val(), $('#currentPassword').val());	
				$('#changePasswordWindow').dialog('close');
				
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
		$('#changePasswordForm input:text').attr('value','');
		$('#changePasswordWindow').dialog('close');
	});
*/	
	$('#inviteUserButton').click(function(){
		var useremail = $('#useremail').val();
		var invitationMessage = $('#invitationMessage').val();
		if (useremail != ""){
			TRACKER.inviteUser(useremail, invitationMessage);
			$('#inviteUserWindow').dialog('close');
		}
		else {
			TRACKER.showMessage(langOperator.warningMissingParameter, "warning");
		}
	});
	
	$("#inviteUserCancel").click(function()
	{
		$('#InviteUserForm textarea').attr('value','');
		$('#inviteUserWindow').dialog('close');
	});
	
	/*
	//this callback opens the friend requests window
	$('#friendRequests').click(function(){
		TRACKER.getFriendRequests(1,function(str){
			$('#friendRequestsList').find(".mbcontainercontent:first").html("<div id='lists'><div id='friendsList'><div id='friends'></div></div></div>");
			$('#friendRequestsList').find(".mbcontainercontent:first #friends").html(str);			
			$('#friendRequestsList').mb_open();
			$('#friendRequestsList').mb_centerOnWindow(true);
		});		
	});
	*/
	
	
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
	
	$('#registerButton').click(function(){
		trackerOp.registerUser($('#registerEmail').val(), $('#registerName').val(), $('#registerPassword').val(), $('#registerConfirmPassword').val(),null, 
			function(result){
                $('#registerForm input[type!=button]').attr('value', '');
				$("#registerWindow").dialog("close");
			});						
	});	
/*	
	$('#submitLoginFormButton').click(function(){
		trackerOp.authenticateUser($('#emailLogin').val(), $('#password').val(), $('#rememberMe').attr('checked'), function(){ $('#password').val(""); });			
	});
*/	
	$('#forgotPasswordLink').click(function(){
		$('#forgotPasswordForm').mb_open();
		$('#forgotPasswordForm').mb_centerOnWindow(true);

		$('#sendNewPassword').click(function(){
		    TRACKER.sendNewPassword($('#email').val(),
		   		function(result){
                    $('#forgotPasswordForm input[type!=button]').attr('value', '');
					$('#forgotPasswordForm').mb_close();
				});
		});
	});
/*	
	$('#email').keypress(function(event){
		if (event.keyCode == '13'){
			sendNewPassword();	
		}
	});
	
	$('#username , #password').keypress(function(event){
		if (event.keyCode == '13'){
			authenticateUser();
		}						
	});
*/	
	$('#sendCommentButton').click(function(){			
		var photoId=1;
		var userId=1;
		var comment=$('#photoCommentTextBox').val();
		TRACKER.sendNewComment(userId, photoId, comment);
	});
	
	

};	

function setLanguage(langOperator){
	
	$('title').text(langOperator.mark);	
	$('a[href=#auLink]').html(langOperator.aboutTitle);
//	$("#lists #user_title").append(langOperator.usersTitle);
//	$("#lists #photo_title").append(langOperator.photosTitle);
	$("#friendsList .searchResults a[href=#returnToUserList]").html(langOperator.returnToUserListLink);
	$("#photosList .searchResults a[href=#returnToPhotoList]").html(langOperator.returnToPhotoListLink)
	$("#photosList .search #searchBox").attr('value', langOperator.photosSearchBox);
	$("#friendsList .search #searchBox").attr('value', langOperator.usersSearchBox);
//	$("#aboutus").append(langOperator.aboutus);
	
//	$("#currentPasswordLabel").text(langOperator.currentPasswordLabel + " :");
//	$("#newPasswordLabel").text(langOperator.newPasswordLabel + " :");
//	$("#newPasswordAgainLabel").text(langOperator.newPasswordAgainLabel + " :");
//	$("#changePasswordButton").val(langOperator.submitFormButtonLabel);
//	$("#changePasswordCancel, #inviteUserCancel").val(langOperator.cancelFormButtonLabel);
	$("#inviteUserEmailLabel").text(langOperator.emailLabel);
	$("#inviteUserButton").val(langOperator.inviteUserLabel);
//	$("#signout div").append(langOperator.signout);
//	$("#changePassword div").text(langOperator.changePassword);
//	$("#inviteUser div").append(langOperator.inviteUserLabel);
//	$("#friendRequests div").append(langOperator.friendRequests);
	
	$("#inviteUserInvitationMessage").text(langOperator.invitationMessage);
	$("#mailEntraceAlert").text(langOperator.mailEntranceRule);
	$("#inviteUserFormTitle").text(langOperator.sendInvitations);
	$("#friendRequestsListTitle").text(langOperator.friendRequests);
	$("#changePasswordFormTitle").text(langOperator.changePassword);
	
	
//	$('#usernameLabel').text(langOp.emailLabel+":");	
//	$('#passwordLabel').text(langOp.passwordLabel+":");
//	$('#rememberMeLabel').text(langOp.rememberMeLabel).click(function(){
//		$('#rememberMe').attr('checked', !($('#rememberMe').attr('checked')));
			
//	});
	$('#forgotPasswordLink').text(langOp.forgotPassword);
	$('#sendNewPassword').attr('value', langOp.sendNewPassword);	
//	$('#registerLink').text(langOp.registerLabel);	
//	$('#loginLink').text(langOp.login);
//	$('#emailLabel').text(langOp.emailLabel + ":");	
//	$("#submitLoginFormButton").val(langOp.submitFormButtonLabel);	
//	$('#aboutusLink').text(langOp.aboutTitle);
	
}
