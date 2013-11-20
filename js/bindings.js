
var bRegisterFormNameErrorExists = false;
var bRegisterFormLastNameErrorExists = false;
var bRegisterFormEmailErrorExists = false;
var bRegisterFormEmailAgainErrorExists = false;
var bRegisterFormPasswordErrorExists = false;
var bRegisterFormPasswordAgainErrorExists = false;
var bLoginFormEmailErrorExists = false;
var bLoginFormPasswordErrorExists = false;

var bShowPublicPhotosLinkActive = true;
var uploadsGridViewId = 'publicUploadListView';

function resetAllFormErrors()
{	
	$("#RegisterForm_name").tooltipster('update', "");
	$("#RegisterForm_name").tooltipster("hide");
	
	$("#RegisterForm_lastName").tooltipster('update', "");
	$("#RegisterForm_lastName").tooltipster("hide");
	
	$("#RegisterForm_email").tooltipster('update', "");
	$("#RegisterForm_email").tooltipster("hide");

	$("#RegisterForm_emailAgain").tooltipster('update', "");
	$("#RegisterForm_emailAgain").tooltipster("hide");
	
	$("#RegisterForm_password").tooltipster('update', "");
	$("#RegisterForm_password").tooltipster("hide");
	
	$("#RegisterForm_passwordAgain").tooltipster('update', "");
	$("#RegisterForm_passwordAgain").tooltipster("hide");

	$("#LoginForm_email").tooltipster('update', "");
	$("#LoginForm_email").tooltipster("hide");
	
	$("#LoginForm_password").tooltipster('update', "");
	$("#LoginForm_password").tooltipster("hide");	
}

function hideRegisterFormErrorsIfExist()
{
  	if(bRegisterFormNameErrorExists)
	{
		$("#RegisterForm_name").tooltipster("hide");
	}

  	if(bRegisterFormLastNameErrorExists)
	{
		$("#RegisterForm_lastName").tooltipster("hide");
	}

  	if(bRegisterFormEmailErrorExists)
	{
		$("#RegisterForm_email").tooltipster("hide");
	}

  	if(bRegisterFormEmailAgainErrorExists)
	{
		$("#RegisterForm_emailAgain").tooltipster("hide");
	}

  	if(bRegisterFormPasswordErrorExists)
	{
		$("#RegisterForm_password").tooltipster("hide");
	}

  	if(bRegisterFormPasswordAgainErrorExists)
	{
		$("#RegisterForm_passwordAgain").tooltipster("hide");
	}	
}

function hideLoginFormErrorsIfExist()
{
  	if(bLoginFormEmailErrorExists)
	{
		$("#LoginForm_email").tooltipster("hide");
	}
  	
  	if(bLoginFormPasswordErrorExists)
	{
		$("#LoginForm_password").tooltipster("hide");
	}	
}

function hideFormErrorsIfExist() 
{
	hideRegisterFormErrorsIfExist();
	hideLoginFormErrorsIfExist();
}

function showRegisterFormErrorsIfExist()
{
	if(bRegisterFormNameErrorExists)
	{
		$("#RegisterForm_name").tooltipster("show");
	}

	if(bRegisterFormLastNameErrorExists)
	{
		$("#RegisterForm_lastName").tooltipster("show");
	}

	if(bRegisterFormEmailErrorExists)
	{
		$("#RegisterForm_email").tooltipster("show");
	}

	if(bRegisterFormEmailAgainErrorExists)
	{
		$("#RegisterForm_emailAgain").tooltipster("show");
	}

	if(bRegisterFormPasswordErrorExists)
	{
		$("#RegisterForm_password").tooltipster("show");
	}

	if(bRegisterFormPasswordAgainErrorExists)
	{
		$("#RegisterForm_passwordAgain").tooltipster("show");
	}	
}

function showLoginFormErrorsIfExist()
{
  	if(bLoginFormEmailErrorExists)
	{
		$("#LoginForm_email").tooltipster("show");
	}
  	
  	if(bLoginFormPasswordErrorExists)
	{
		$("#LoginForm_password").tooltipster("show");
	}	
}

function showFormErrorsIfExist() 
{
	showRegisterFormErrorsIfExist();
	showLoginFormErrorsIfExist();
}

function bindTooltipActions() 
{
 	$("#RegisterForm_email").blur(function ()	{
 		var enteredEmail = document.getElementById("RegisterForm_email").value;
 		var enteredDomain = enteredEmail.replace(/.*@/, "");
 		var correctedEmail = "";
 		var domainPartsArray = enteredDomain.split(".");
 		var tooltipMessage = "";
 		var bCorrectionRequired = false;
 		
 		if(domainPartsArray.length >= 2)
 		{
 	 		if((domainPartsArray[0].toLowerCase() === "gmial") || (domainPartsArray[0].toLowerCase() === "gmil") || (domainPartsArray[0].toLowerCase() === "gmal") || (domainPartsArray[0].toLowerCase() === "glail") || (domainPartsArray[0].toLowerCase() === "gamil"))
 	 		{
 	 			bCorrectionRequired = true;
 	 			correctedEmail = enteredEmail.replace(domainPartsArray[0],"gmail");			
 	 		}
 	 		else if((domainPartsArray[0].toLowerCase() === "yaho") || (domainPartsArray[0].toLowerCase() === "yhao") || (domainPartsArray[0].toLowerCase() === "yhaoo") || (domainPartsArray[0].toLowerCase() === "yhoo"))
 	 		{
 	 			bCorrectionRequired = true;
 	 			correctedEmail = enteredEmail.replace(domainPartsArray[0],"yahoo");
 	 		}
 	 		else if((domainPartsArray[0].toLowerCase() === "hotmial") || (domainPartsArray[0].toLowerCase() === "hotmal") || (domainPartsArray[0].toLowerCase() === "hotmil") || (domainPartsArray[0].toLowerCase() === "htmail") || (domainPartsArray[0].toLowerCase() === "hotma"))
 	 		{
 	 			bCorrectionRequired = true;
 	 			correctedEmail = enteredEmail.replace(domainPartsArray[0],"hotmail");
 	 		}
 	 		else if((domainPartsArray[0].toLowerCase() === "oulook") || (domainPartsArray[0].toLowerCase() === "outlok") || (domainPartsArray[0].toLowerCase() === "outloo") || (domainPartsArray[0].toLowerCase() === "otlook"))
 	 		{
 	 			bCorrectionRequired = true;
 	 			correctedEmail = enteredEmail.replace(domainPartsArray[0],"outlook");
 	 		} 	 		
 	 		else if((domainPartsArray[0].toLowerCase() === "myet") || (domainPartsArray[0].toLowerCase() === "mynt") || (domainPartsArray[0].toLowerCase() === "mymet"))
 	 		{
 	 			bCorrectionRequired = true;
 	 			correctedEmail = enteredEmail.replace(domainPartsArray[0],"mynet");
 	 		}
 	 		else if((domainPartsArray[1].toLowerCase() === "con") || (domainPartsArray[1].toLowerCase() === "co"))
 	 		{
 	 			correctedEmail = enteredEmail.replace(domainPartsArray[1],"com");
 	 			bCorrectionRequired = true;
 	 		}
 	 		else if(((domainPartsArray[0].toLowerCase() === "gmail") && (domainPartsArray[1].toLowerCase() !== "com")) || ((domainPartsArray[0].toLowerCase() === "yahoo") && (domainPartsArray[1].toLowerCase() !== "com")) || ((domainPartsArray[0].toLowerCase() === "hotmail") && (domainPartsArray[1].toLowerCase() !== "com")) || ((domainPartsArray[0].toLowerCase() === "mynet") && (domainPartsArray[1].toLowerCase() !== "com")) || ((domainPartsArray[0].toLowerCase() === "outlook") && (domainPartsArray[1].toLowerCase() !== "com")))
 	 		{
 	 			correctedEmail = enteredEmail.replace(domainPartsArray[1],"com");
 	 			bCorrectionRequired = true;	 			
 	 		}
 	 				 			 		
 	 		if(bCorrectionRequired)
 	 		{
 	 	 		domainPartsArray = correctedEmail.split(".");
 	 	 		
 	 	 		if(domainPartsArray[1].toLowerCase() === "com") //These domains all have "com" extension
 	 	 		{
 	 	 			//Nothig to do
 	 	 		}
 	 	 		else
 	 	 		{
 	 	 			correctedEmail = correctedEmail.replace(domainPartsArray[1],"com");
 	 	 		}
 	 	 		
 				if(LAN_OPERATOR.lang === "en")
 				{
 					tooltipMessage = TRACKER.langOperator.didYouMean + " <a style='cursor:pointer;' onclick='document.getElementById(\"RegisterForm_email\").value = \"" + correctedEmail + "\";$(\"#RegisterForm_email\").tooltipster(\"hide\");'>" + correctedEmail + "</a> ? " + TRACKER.langOperator.ifSoClickOnSuggestedEmail;
 				}
 				else
 				{
 					tooltipMessage = "<a style='cursor:pointer;' onclick='document.getElementById(\"RegisterForm_email\").value = \"" + correctedEmail + "\";$(\"#RegisterForm_email\").tooltipster(\"hide\");'>" + correctedEmail + "</a> " + TRACKER.langOperator.didYouMean + "? " + TRACKER.langOperator.ifSoClickOnSuggestedEmail;	
 				}
 	 		}			
 		}
 		
 		//var correctedEmail = enteredEmail.replace(domainPartsArray[0],"gmail");
 				
 		if(bCorrectionRequired)
 		{
 	 		$("#RegisterForm_email").tooltipster('update', tooltipMessage);
 	 		$("#RegisterForm_email").tooltipster('show'); 			
 		}
 		else
 		{
 			if(bRegisterFormEmailErrorExists === false)
 			{
 				$("#RegisterForm_email").tooltipster('hide');
 			} 						 			 
 		}

//		$("#RegisterForm_email").tooltipster({
//   	 theme: ".tooltipster-noir",
//   	 }); 				
	
	//$("#RegisterForm_email").tooltipster('hide');
	//$("#RegisterForm_email").tooltipster('destroy');
//	$("#RegisterForm_email").tooltipster({
//   	 theme: ".tooltipster-default",
//   	 position: "right",
//   	 trigger: "custom",
//   	 maxWidth: 540,
//   	 onlyOne: false,
//		 interactive: true,
//   	 });	
	
	//$("#RegisterForm_email").tooltipster('disable');
 		
	});
}

function bindElements(langOperator, trackerOp) 
{	 		
	/**
	 * binding operation to search user
	 */	

 	$("#RegisterForm_email").focus(function ()	{
 		//$("#RegisterForm_email").tooltipster('update', '<div id="registerEmailNotificationMessageId">' + TRACKER.langOperator.registerEmailNotificationMessage + '</div>');
 		$("#RegisterForm_email").tooltipster('update', TRACKER.langOperator.registerEmailNotificationMessage);
 		$("#RegisterForm_email").tooltipster('show'); 		
	});

// 	$("#RegisterForm_email").blur(function ()	{
// 		$("#RegisterForm_email").tooltipster('hide');
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
			showRegisterFormErrorsIfExist();

			if(bShowPublicPhotosLinkActive === true)
			{
				$("#showPublicPhotosLink").fadeIn('slow');
			}
			else
			{
				$("#showRegisterFormLink").fadeIn('slow');
			}			
			
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
			hideRegisterFormErrorsIfExist();
			
			var offsetLeft = 16;

			if(bShowPublicPhotosLinkActive === true)
			{
				$("#showPublicPhotosLink").fadeOut('slow');
			}
			else
			{
				$("#showRegisterFormLink").fadeOut('slow');
			}	
			
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
	
	$("#showRegisterFormLink").click(function (){
		//$('#sideBar > #formContent').fadeIn('slow');
		$("#formContent").fadeToggle( "slow", function(){showRegisterFormErrorsIfExist(); $("#showRegisterFormLink").hide(); $("#publicUploadsContent").hide(); $("#showCachedPublicPhotosLink").show(); bShowPublicPhotosLinkActive = true;});
		//$('#formContent').animate({height:'100%', marginTop:'0'}, function(){ $('#formContent').show(); $("#showRegisterFormLink").hide(); $("#publicUploads").hide(); $("#showPublicPhotosLink").show();});		
	});
	
	$("#showCachedPublicPhotosLink").click(function (){
		$("#formContent").fadeToggle( "slow", function(){ hideRegisterFormErrorsIfExist(); $("#showCachedPublicPhotosLink").hide(); bShowPublicPhotosLinkActive = false; $("#showRegisterFormLink").show(); $("#publicUploadsContent").show();});
	});	
	
	function changeSrcBack(elementid, imgSrc)
	{
	  document.getElementById(elementid).src = imgSrc;
	}
	
	$( document ).ready(function() {		
		var h = $(window).height();
		var w = $(window).width();
		var offsetTop = 0;
		
		if(document.getElementById('topBar').style.height == "60px")
		{
			offsetTop = 60;
		}
		else
		{
			offsetTop = 85;
		}
		    
		var userListHeight = ((h - offsetTop - 80) > 445)?(h - offsetTop - 80):445;
		
		$.post('saveToSession.php', { width:w, height:userListHeight }, function(json) {
	        if(json.outcome == 'success') {
	        	//alert('OKKKKK');
	            // do something with the knowledge possibly?
	        } else {
	            alert('Unable to let PHP know what the screen resolution is!');
	        }
	    },'json');
	});	

	$(window).resize(function () {
	    var h = $(window).height(), offsetTop = 0; // Calculate the top offset	    
	    var w = $(window).width(), offsetLeft = 0; // Calculate the left offset
	    
	    var usersCount = 5;

	    //alert('Javascript');
	    
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
			
			$("#users_tab").css("min-height", (485 + 100 - 60 - 81)); $("#users_tab").css("height", (h - offsetTop - 81));
			$("#photos_tab").css("min-height", (485 + 100 - 60 - 81)); $("#photos_tab").css("height", (h - offsetTop - 81));
			$("#groups_tab").css("min-height", (485 + 100 - 60 - 81)); $("#groups_tab").css("height", (h - offsetTop - 81));
			
			var userListHeight = ((h - offsetTop - 80) > 445)?(h - offsetTop - 80):445;
			
			$("#usersGridView").css("height", userListHeight - 50);
			$("#uploadsGridView").css("height", userListHeight - 50);
			$("#groupsGridView").css("height", userListHeight - 50);
			
//		    $.post('saveToSession.php', { width:w, height:userListHeight }, function(json) {
//		        if(json.outcome == 'success') {
//		        	//alert('OKKKKK');
//		            // do something with the knowledge possibly?
//		        } else {
//		            alert('Unable to let PHP know what the screen resolution is!');
//		        }
//		    },'json');	    			
		}
		else
		{			
			offsetTop = 85;
			offsetLeft = 396;
			
			var userListHeight = ((h - offsetTop - 80) > 445)?(h - offsetTop - 80):445;
			$("#uploadsGridView").css("height", userListHeight - 40);
			
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

