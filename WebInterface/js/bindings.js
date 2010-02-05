
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
				
	$("a[href=#auLink], #logo").colorbox({width:"60%", inline:true, href:"#aboutus", opacity:0.5, scrolling:true});

	$("a[href=#returnToUserList]").click(function(){
		$('#lists .title').html(langOperator.usersTitle);
		$('#search').slideUp(function(){ $('#users').slideDown(); });
	});
	
	$("#bar").click(function ()	{	
				if ($('#sideBar > #content').css('display') == "none")
				{
					$('#sideBar > #content').animate({width:'20%'});
					$('#map').animate({width:'80%'});
				}	
				else 
				{
					$('#sideBar > #content').animate({width:'0%'}, function(){ $('#sideBar > #content').hide(); });
					$('#map').animate({width:'90%'});
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