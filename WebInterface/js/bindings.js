
function bindElements(langOperator, trackerOp) 
{	 		
	
	$('#searchButton').click(function(){
		trackerOp.searchUser($('#searchBox').attr('value'), 1);					
	});
	$('#searchBox').keydown(function(ev){
		if (ev.keyCode == 13) {
			trackerOp.searchUser($('#searchBox').attr('value'), 1);
		}
	}).focus(function(){
		$(this).select();
	});
				
	$("a[href=#auLink], #logo, .logo_inFullMap").colorbox({width:"60%", inline:true, href:"#aboutus", opacity:0.5, scrolling:true});

	$("a[href=#returnToUserList]").click(function(){
		$('#lists .title').html(langOperator.usersTitle);
		$('#search').slideUp(function(){ $('#users').slideDown(); });
	});
	
	$("#bar").click(function ()	{	
				if ($('#sideBar > #content').css('display') == "none")
				{
					$('.logo_inFullMap').fadeOut().animate({left:'10px'});
					$('#sideBar > #content').fadeIn('slow');
					$('#sideBar').animate({width:'25%'}, function(){  $('#bar').css('background','#EEEEFF url("images/left.png") no-repeat center') });
					$('#map').animate({width:'75%'});
					
				}	
				else 
				{
					$('.logo_inFullMap').fadeIn().animate({left:'80px'});
					$('#sideBar > #content').fadeOut('slow');
					$('#sideBar').animate({width:'20px'}, 
									function(){ $('#sideBar > #content').hide();
											    $('#bar').css('background','#EEEEFF url("images/right.png") no-repeat center');
									});
					$('#map').animate({width:'99%'});
					
					
				}
	});	
	

};	

function setLanguage(langOperator){
	
	$('title').text(langOperator.mark);	
	$('a[href=#auLink]').html(langOperator.aboutTitle);
	$("#lists .title").html(langOperator.usersTitle);
	$("a[href=#returnToUserList]").html(langOperator.returnToUserListLink);
	$("#aboutus").html(langOperator.aboutus);
//	$("#loading p").html(langOperator.loading);
	
}