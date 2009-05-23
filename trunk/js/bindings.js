function bindElements(languageOperator) 
{	 		
	$('#searchButton').click(function(){
		trackerOp.searchUser($('#searchBox').attr('value'), 1);					
	});
				
	$("#aboutus, #termsofuse").dialog({							
				autoOpen: false,
				modal:true,
				buttons: {
					Ok: function() {
							$(this).dialog('close');
						}
				}				
	});
	$("a[href=#touLink]").click(function(){
			$('#termsofuse').dialog( 'open');	
	});
	$("a[href=#auLink], #logo").click(function(){
			$('#aboutus').dialog( 'open');
	});

	$("a[href=#returnToUserList]").click(function(){
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
	
	if (languageOperator.lang != "en"){
		changeLanguage(languageOperator);
	}
	
};	

function changeLanguage(languageOperator){
	
}