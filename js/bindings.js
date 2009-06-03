
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
				

	$("a[href=#touLink]").click(function(){
			$('#termsofuse').modal();	
	});
	$("a[href=#auLink], #logo").click(function(){
			$('#aboutus').modal();
	});

	$("a[href=#returnToUserList]").click(function(){
		$('#lists .title').html(langOperator.usersTitle);
		$('#search').slideUp(function(){ $('#users').slideDown(); });
	});
	
	$("#bar").click(function ()	{	
				if ($('#sideBar').css('display') == "none")
				{
					$('#sideBar').animate({width:'25%'});
					$('#map').css('width','72%');
				}	
				else 
				{
					$('#sideBar').animate({width:'0%'}, function(){ $('#sideBar').hide(); });
					$('#map').css('width','98%');
				}
	});	
	

	if (langOperator.lang != "en")
	{
		changeLanguage(langOperator);
	}
	
};	

function changeLanguage(langOperator){

	$('title').text(langOperator.mark);
	$("a[href=#auLink]").html(langOperator.aboutTitle);
	$("a[href=#touLink]").html(langOperator.termsOfUseTitle);
	$("#lists .title").html(langOperator.usersTitle);
	$("a[href=#returnToUserList]").html(langOperator.returnToUserListLink);
	$("#aboutus").html(langOperator.about);
	$("#termsofuse").html(langOperator.termsofuse);
	$("#loading").html(langOperator.loading);
	
}