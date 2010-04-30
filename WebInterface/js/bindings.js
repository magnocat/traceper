var showPhotosOnMapCookieId = "showPhotosOnMap";

function bindElements(langOperator, trackerOp) 
{	 		
	/**
	 * binding operation to search user
	 */
	$('#usersList .search #searchButton').click(function(){
		trackerOp.searchUser($('#usersList .search #searchBox').attr('value'), 1);					
	});
	$('#usersList .search #searchBox').keydown(function(ev){
		if (ev.keyCode == 13) {
			trackerOp.searchUser($('#usersList .search #searchBox').attr('value'), 1);
		}
	}).focus(function(){
		if ($(this).attr("value") == langOperator.usersSearchBox){
			$(this).attr("value", "");
		}
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
				$('#usersList').slideUp('fast',function(){
					$('#photosList').slideDown();
				});				
			});
		}
		if (TRACKER.allImagesFetched == false){
			TRACKER.getImageListInBg();
		}
		$('#usersList').slideUp('fast',function(){
			$('#photosList').slideDown();
		});
		
		$(this).addClass('active_title');
		$('#user_title').removeClass('active_title');
		
	});
	
	$('#user_title').click(function(){
		$('#photosList').slideUp('fast',function(){
			$('#usersList').slideDown();
		});
		$(this).addClass('active_title');
		$('#photo_title').removeClass('active_title');
	});
	
	$('ul.sf-menu').superfish();  
				
	$("a[href=#auLink], #logo, .logo_inFullMap").colorbox({width:"60%", inline:true, href:"#aboutus", opacity:0.5, scrolling:true});

	$("a[href=#returnToUserList]").click(function(){
		$('#usersList .searchResults').slideUp(function(){ $('#usersList #users').slideDown(); });
	});
	
	$("a[href=#returnToPhotoList]").click(function(){
		$('#photosList .searchResults').slideUp(function(){ $('#photosList #photos').slideDown(); });
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
	

};	

function setLanguage(langOperator){
	
	$('title').text(langOperator.mark);	
	$('a[href=#auLink]').html(langOperator.aboutTitle);
	$("#lists #user_title").html(langOperator.usersTitle);
	$("#lists #photo_title").html(langOperator.photosTitle);
	$("#usersList .searchResults a[href=#returnToUserList]").html(langOperator.returnToUserListLink);
	$("#photosList .searchResults a[href=#returnToPhotoList]").html(langOperator.returnToPhotoListLink)
	$("#photosList .search #searchBox").attr('value', langOperator.photosSearchBox);
	$("#usersList .search #searchBox").attr('value', langOperator.usersSearchBox);
	$("#aboutus").html(langOperator.aboutus);
//	$("#loading p").html(langOperator.loading);
	
}