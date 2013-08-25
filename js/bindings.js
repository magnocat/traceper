function bindElements(langOperator, trackerOp) 
{	 		
	/**
	 * binding operation to search user
	 */	
	
//	$(document).ready(function() {
//		   $('.tooltip').tooltipster();
//		});	
	
// 	$("#registerEmailField").click(function ()	{
// 		$("#registerEmailField").tooltipster('show');
// 		//$('.tooltip').tooltipster('show');
// 		
// 		//alert("Deneme");
//	});
 	
	$("#bar").click(function ()	{
		
		//alert("Deneme");
		
	    var w = $(window).width();
	    
	    if(w < 1007)
	    {
	    	w = 1007;
	    }	
		
		if ($('#sideBar > #content').css('display') == "none")
		{					
			//If logged in and top bar height decreased
			if(document.getElementById('topBar').style.height == "60px")
			{
				var offsetLeft = 396;
				
				//$('.logo_inFullMap').fadeOut().animate({left:'10px'});
				$('#sideBar > #content').fadeIn('slow');
				$('#sideBar').animate({width:'396px'}, function(){  $('#bar').css('background-image','url("images/left.png")') });
				$('#map').animate({width:(w - offsetLeft)});
				$('#bar').animate({left:'380px'});
			}
			else
			{
				var offsetLeft = 396;
				
				//$('.logo_inFullMap').fadeOut().animate({left:'10px'});
				$('#sideBar > #content').fadeIn('slow');
				$('#sideBar').animate({width:'396px'}, function(){  $('#bar').css('background-image','url("images/left.png")') });
				$('#map').animate({width:(w - offsetLeft)});
				$('#bar').animate({left:'380px'});
			}
		}	
		else 
		{
			var offsetLeft = 16;
			
			//$('.logo_inFullMap').fadeIn().animate({left:'80px'});
			$('#sideBar > #content').fadeOut('slow');
			$('#sideBar').animate({width:'0px'}, 
							function(){ $('#sideBar > #content').hide();
									    $('#bar').css('background-image','url("images/right.png")');
							});
			
			//$('#map').animate({width:'99%'});
			$('#map').animate({width:(w - offsetLeft)});
			$('#bar').animate({left:'0px'});
		}
	});
	
	function changeSrcBack(elementid, imgSrc)
	{
	  document.getElementById(elementid).src = imgSrc;
	}

	$(window).resize(function () {
	    var h = $(window).height(), offsetTop = 0; // Calculate the top offset	    
	    var w = $(window).width(), offsetLeft = 0; // Calculate the left offset	

	    //alert('Height:'+(h)+' Width:'+(w));
	    
	    if(h > 720)
	    {	   	
	    	document.getElementById('androidBubbleTr').src = "images/AndroidBubble_big_tr.png";
	    	document.getElementById('androidBubbleTr').onmouseout = function() {changeSrcBack('androidBubbleTr', 'images/AndroidBubble_big_tr.png')};
	    	
	    	document.getElementById('androidBubbleEn').src = "images/AndroidBubble_big_en.png";
	    	document.getElementById('androidBubbleEn').onmouseout =  function() {changeSrcBack('androidBubbleEn', 'images/AndroidBubble_big_en.png')};	    	
	    }
	    else
	    {
	    	document.getElementById('androidBubbleTr').src = "images/AndroidBubble_tr.png";
	    	document.getElementById('androidBubbleTr').onmouseout = function() {changeSrcBack('androidBubbleTr', 'images/AndroidBubble_tr.png')};
	    	
	    	document.getElementById('androidBubbleEn').src = "images/AndroidBubble_en.png";
	    	document.getElementById('androidBubbleEn').onmouseout =  function() {changeSrcBack('androidBubbleEn', 'images/AndroidBubble_en.png')};		    	
	    }

	  //If logged in and top bar height decreased
		if(document.getElementById('topBar').style.height == "60px")
		{
			offsetTop = 60;
			offsetLeft = 396;
			
			if ($('#sideBar > #content').css('display') == "none")
			{
				//$('#map').css('width', '99%');
				$('#map').css('width', (w - 16));
				$('#map').css('min-width', (735 + 260));
				$('#bar').css('left', '0px');
				
				//alert('Wide');
			}
			else
			{
				$('#map').css('width', (w - offsetLeft));
				$('#map').css('min-width', (735 + 260 - 380));
				$('#bar').css('left', '380px');
				
				//alert('Narrow');
			}
		}
		else
		{			
			offsetTop = 100;
			offsetLeft = 396;
			
			if ($('#sideBar > #content').css('display') == "none")
			{
				//$('#map').css('width', '99%');
				$('#map').css('width', (w - 16));
				$('#map').css('min-width', (735 + 380));
				$('#bar').css('left', '0px');
				
				//alert('Wide:' + (w - 16));
			}
			else
			{
				$('#map').css('width', (w - offsetLeft));
				$('#map').css('min-width', 735);
				$('#bar').css('left', '380px');
				
				//alert('Narrow');
			}			
		}
		
		//alert('binding.js: offsetTop:'+(offsetTop)+' offsetLeft:'+(offsetLeft));
		
	    $('#map').css('height', (h - offsetTop));
	    $('#bar').css('height', (h - offsetTop));
	    $('#sideBar').css('height', (h - offsetTop));		
	    
	}).resize();
     
     $('#tab_view').bind('tabsselect', function(event, ui) {
    	 switch (ui.index)
    	 {
	    	 case 0: //Friends
	    	 {
	    		 TRACKER.showImagesOnTheMap = false; TRACKER.showUsersOnTheMap = true; TRACKER.getImageList(); TRACKER.getFriendList(1, 0/*UserType::RealUser*/);
	    	 }
	    	 break;
	    	   
	    	 case 1: //Uploads
	    	 {
	    		 TRACKER.showImagesOnTheMap = true; TRACKER.showUsersOnTheMap = false; TRACKER.getImageList(); TRACKER.getFriendList(1, 0/*UserType::RealUser*/);
	    	 }
	    	 break;
	    	   
	    	 case 2: //Groups
	    	 {
	    		 TRACKER.showImagesOnTheMap = false; TRACKER.showUsersOnTheMap = true; TRACKER.getImageList(); TRACKER.getFriendList(1, 0/*UserType::RealUser*/);
	    	 }
	    	 break;
    	 }     	 
     });
     
     
		
//	$("#bar").click(function ()	{	
//				if ($('#sideBar > #content').css('display') == "none")
//				{					
//					//If logged in and top bar height decreased
//					if(document.getElementById('topBar').style.height == "7%")
//					{
//						//$('.logo_inFullMap').fadeOut().animate({left:'10px'});
//						$('#sideBar > #content').fadeIn('slow');
//						$('#sideBar').animate({width:'26%'}, function(){  $('#bar').css('background-image','url("images/left.png")') });
//						$('#map').animate({width:'74%'});
//						$('#bar').animate({right:'74%'});						
//					}
//					else
//					{
//						//$('.logo_inFullMap').fadeOut().animate({left:'10px'});
//						$('#sideBar > #content').fadeIn('slow');
//						$('#sideBar').animate({width:'22%'}, function(){  $('#bar').css('background-image','url("images/left.png")') });
//						$('#map').animate({width:'78%'});
//						$('#bar').animate({right:'78%'});						
//					}
//				}	
//				else 
//				{
//					//$('.logo_inFullMap').fadeIn().animate({left:'80px'});
//					$('#sideBar > #content').fadeOut('slow');
//					$('#sideBar').animate({width:'0%'}, 
//									function(){ $('#sideBar > #content').hide();
//											    $('#bar').css('background-image','url("images/right.png")');
//									});
//					$('#map').animate({width:'99%'});
//					$('#bar').animate({right:'99%'});					
//				}
//	});	
};	

