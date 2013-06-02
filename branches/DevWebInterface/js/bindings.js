function bindElements(langOperator, trackerOp) 
{	 		
	/**
	 * binding operation to search user
	 */	
	$("#bar").click(function ()	{	
				if ($('#sideBar > #content').css('display') == "none")
				{					
					//Login olunmuþ ve topBar height'ý düþmüþse
					if(document.getElementById('topBar').style.height == "7%")
					{
						//$('.logo_inFullMap').fadeOut().animate({left:'10px'});
						$('#sideBar > #content').fadeIn('slow');
						$('#sideBar').animate({width:'26%'}, function(){  $('#bar').css('background-image','url("images/left.png")') });
						$('#map').animate({width:'74%'});
						$('#bar').animate({right:'74%'});						
					}
					else
					{
						//$('.logo_inFullMap').fadeOut().animate({left:'10px'});
						$('#sideBar > #content').fadeIn('slow');
						$('#sideBar').animate({width:'22%'}, function(){  $('#bar').css('background-image','url("images/left.png")') });
						$('#map').animate({width:'78%'});
						$('#bar').animate({right:'78%'});						
					}
				}	
				else 
				{
					//$('.logo_inFullMap').fadeIn().animate({left:'80px'});
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

