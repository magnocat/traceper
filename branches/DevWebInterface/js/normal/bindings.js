
var bRegisterFormNameErrorExists = false;
var bRegisterFormLastNameErrorExists = false;
var bRegisterFormEmailErrorExists = false;
var bRegisterFormEmailWarningExists = false;
var bRegisterFormEmailAgainErrorExists = false;
var bRegisterFormPasswordErrorExists = false;
var bRegisterFormPasswordAgainErrorExists = false;
var bLoginFormEmailErrorExists = false;
var bLoginFormPasswordErrorExists = false;
var bResetPasswordFormNewPasswordErrorExists = false;
var bResetPasswordFormNewPasswordAgainErrorExists = false;
var bAppQRCodeTooltipOpened = false; 

var bShowPublicPhotosLinkActive = true;
var uploadsGridViewId = 'publicUploadListView';
var bCountryInfoExists = false;
 
var currentUserId;
var currentUserMarker;
var locationlessUserIdArray = new Array();
var loginWidthCheckConstant = 1255;
var loginWidthMiddleCheckConstant = 1175;
var windowWidthSize = 2; //En buyuk pencere boyutu
var preWindowWidth = loginWidthCheckConstant;

function alertMsg(par_message) {
	if(bDeploymentModeOn === false)
	{
		alert(par_message);
	}
}

function changecss(myclass,element,value) {
	var CSSRules
	if (document.all) {
		CSSRules = 'rules'
	}
	else if (document.getElementById) {
		CSSRules = 'cssRules'
	}
	for (var i = 0; i < document.styleSheets[0][CSSRules].length; i++) {
		if (document.styleSheets[0][CSSRules][i].selectorText == myclass) {
			document.styleSheets[0][CSSRules][i].style[element] = value
		}
	}	
}

function resetAllFormErrors()
{	
	if(bRegisterFormNameErrorExists)
	{
		$("#RegisterForm_name").tooltipster('update', "");
		$("#RegisterForm_name").tooltipster("hide");		
	}
	
	if(bRegisterFormLastNameErrorExists)
	{
		$("#RegisterForm_lastName").tooltipster('update', "");
		$("#RegisterForm_lastName").tooltipster("hide");		
	}
	
	if(bRegisterFormEmailErrorExists)
	{
		$("#RegisterForm_email").tooltipster('update', "");
		$("#RegisterForm_email").tooltipster("hide");		
	}	
	
	if(bRegisterFormEmailAgainErrorExists)
	{
		$("#RegisterForm_emailAgain").tooltipster('update', "");
		$("#RegisterForm_emailAgain").tooltipster("hide");		
	}

	if(bRegisterFormPasswordErrorExists)
	{
		$("#registerPasswordField").tooltipster('update', "");
		$("#registerPasswordField").tooltipster("hide");		
	}
	
	if(bRegisterFormPasswordAgainErrorExists)
	{
		$("#registerPasswordAgainField").tooltipster('update', "");
		$("#registerPasswordAgainField").tooltipster("hide");		
	}	
	
	if(bRegisterFormEmailWarningExists)
	{
		$("#registerFormTermsText").tooltipster('update', "");
		$("#registerFormTermsText").tooltipster("hide");		
	}
	
	if(bLoginFormEmailErrorExists)
	{
		$("#LoginForm_email").tooltipster('update', "");
		$("#LoginForm_email").tooltipster("hide");		
	}
	
	if(bLoginFormPasswordErrorExists)
	{
		$("#LoginForm_password").tooltipster('update', "");
		$("#LoginForm_password").tooltipster("hide");		
	}	
		
	if(bResetPasswordFormNewPasswordErrorExists)
	{
		$("#ResetPasswordForm_newPassword").tooltipster('update', "");
		$("#ResetPasswordForm_newPassword").tooltipster("hide");		
	}
	
	if(bResetPasswordFormNewPasswordAgainErrorExists)
	{
		$("#ResetPasswordForm_newPasswordAgain").tooltipster('update', "");
		$("#ResetPasswordForm_newPasswordAgain").tooltipster("hide");			
	}
}

function resetResetPasswordFormErrors()
{
	bResetPasswordFormNewPasswordErrorExists = false;
	bResetPasswordFormNewPasswordAgainErrorExists = false;	
	
	$("#ResetPasswordForm_newPassword").tooltipster('update', "");
	$("#ResetPasswordForm_newPassword").tooltipster("hide");
	
	$("#ResetPasswordForm_newPasswordAgain").tooltipster('update', "");
	$("#ResetPasswordForm_newPasswordAgain").tooltipster("hide");	
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
		$("#registerPasswordField").tooltipster("hide");
	}

  	if(bRegisterFormPasswordAgainErrorExists)
	{
		$("#registerPasswordAgainField").tooltipster("hide");
	}
  	
  	if(bRegisterFormEmailWarningExists)
  	{
  		$("#registerFormTermsText").tooltipster('hide');
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

function hideResetPasswordFormErrorsIfExist()
{
  	if(bResetPasswordFormNewPasswordErrorExists)
	{
		$("#ResetPasswordForm_newPassword").tooltipster("hide");
	}
  	
  	if(bResetPasswordFormNewPasswordAgainErrorExists)
	{
		$("#ResetPasswordForm_newPasswordAgain").tooltipster("hide");
	}	
}

function hideFormErrorsIfExist() 
{
	hideRegisterFormErrorsIfExist();
	hideLoginFormErrorsIfExist();
	hideResetPasswordFormErrorsIfExist();
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
		$("#registerPasswordField").tooltipster("show");
	}

	if(bRegisterFormPasswordAgainErrorExists)
	{
		$("#registerPasswordAgainField").tooltipster("show");
	}
	
  	if(bRegisterFormEmailWarningExists)
  	{
  		$("#registerFormTermsText").tooltipster('show');
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

function showResetPasswordFormErrorsIfExist()
{
	if(bResetPasswordFormNewPasswordErrorExists)
	{
		$("#ResetPasswordForm_newPassword").tooltipster("show");
	}
  	
  	if(bResetPasswordFormNewPasswordAgainErrorExists)
	{
		$("#ResetPasswordForm_newPasswordAgain").tooltipster("show");
	}	
}

function showFormErrorsIfExist() 
{
	showRegisterFormErrorsIfExist();
	showLoginFormErrorsIfExist();
	showResetPasswordFormErrorsIfExist();
}

function bindTooltipActions() 
{
 	$("#RegisterForm_email").blur(function ()	{
 		var enteredEmail = document.getElementById("RegisterForm_email").value;
 		var userPart = enteredEmail.substring(0, enteredEmail.indexOf("@")); 		
 		var enteredDomain = enteredEmail.replace(/.*@/, "");
 		var correctedEmail = "";
 		var domainPartsArray = enteredDomain.split(".");
 		var tooltipMessage = "";
 		var bCorrectionRequired = false;
 		
 		//alert("userPart:" + userPart + " / " + "0:" + domainPartsArray[0] + " / " + "1:" + domainPartsArray[1]);
 		
 		if(domainPartsArray.length >= 2)
 		{
 	 		if((domainPartsArray[0].toLowerCase() === "gmial") || (domainPartsArray[0].toLowerCase() === "gmil") || (domainPartsArray[0].toLowerCase() === "gmal") || (domainPartsArray[0].toLowerCase() === "glail") || (domainPartsArray[0].toLowerCase() === "gamil"))
 	 		{
 	 			bCorrectionRequired = true;
 	 			//correctedEmail = enteredEmail.replace(domainPartsArray[0],"gmail");
 	 			correctedEmail = userPart + "@" + "gmail.com";
 	 		}
 	 		else if((domainPartsArray[0].toLowerCase() === "yaho") || (domainPartsArray[0].toLowerCase() === "yhao") || (domainPartsArray[0].toLowerCase() === "yhaoo") || (domainPartsArray[0].toLowerCase() === "yhoo"))
 	 		{
 	 			bCorrectionRequired = true;
 	 			//correctedEmail = enteredEmail.replace(domainPartsArray[0],"yahoo");
 	 			correctedEmail = userPart + "@" + "yahoo.com";
 	 		}
 	 		else if((domainPartsArray[0].toLowerCase() === "hotmial") || (domainPartsArray[0].toLowerCase() === "hotmal") || (domainPartsArray[0].toLowerCase() === "hotmil") || (domainPartsArray[0].toLowerCase() === "htmail") || (domainPartsArray[0].toLowerCase() === "hotma"))
 	 		{
 	 			bCorrectionRequired = true;
 	 			//correctedEmail = enteredEmail.replace(domainPartsArray[0],"hotmail");
 	 			correctedEmail = userPart + "@" + "hotmail.com";
 	 		}
 	 		else if((domainPartsArray[0].toLowerCase() === "oulook") || (domainPartsArray[0].toLowerCase() === "outlok") || (domainPartsArray[0].toLowerCase() === "outloo") || (domainPartsArray[0].toLowerCase() === "otlook"))
 	 		{
 	 			bCorrectionRequired = true;
 	 			//correctedEmail = enteredEmail.replace(domainPartsArray[0],"outlook");
 	 			correctedEmail = userPart + "@" + "outlook.com";
 	 		} 	 		
 	 		else if((domainPartsArray[0].toLowerCase() === "myet") || (domainPartsArray[0].toLowerCase() === "mynt") || (domainPartsArray[0].toLowerCase() === "mymet"))
 	 		{
 	 			bCorrectionRequired = true;
 	 			//correctedEmail = enteredEmail.replace(domainPartsArray[0],"mynet");
 	 			correctedEmail = userPart + "@" + "mynet.com";
 	 		}
 	 		else if((domainPartsArray[1].toLowerCase() === "con") || (domainPartsArray[1].toLowerCase() === "co"))
 	 		{
 	 			bCorrectionRequired = true;
 	 			correctedEmail = enteredEmail.replace(domainPartsArray[1],"com");
 	 			correctedEmail = userPart + "@" + domainPartsArray[0].toLowerCase() + ".com";
 	 		}
 	 		else if(((domainPartsArray[0].toLowerCase() === "gmail") && (domainPartsArray[1].toLowerCase() !== "com")) || ((domainPartsArray[0].toLowerCase() === "yahoo") && (domainPartsArray[1].toLowerCase() !== "com")) || ((domainPartsArray[0].toLowerCase() === "hotmail") && (domainPartsArray[1].toLowerCase() !== "com")) || ((domainPartsArray[0].toLowerCase() === "mynet") && (domainPartsArray[1].toLowerCase() !== "com")) || ((domainPartsArray[0].toLowerCase() === "outlook") && (domainPartsArray[1].toLowerCase() !== "com")))
 	 		{
 	 			bCorrectionRequired = true;
 	 			//correctedEmail = enteredEmail.replace(domainPartsArray[1],"com");
 	 			correctedEmail = userPart + "@" + domainPartsArray[0].toLowerCase() + ".com"; 	 				 			
 	 		}
	 			 		
 	 		if(bCorrectionRequired)
 	 		{
// 	 	 		domainPartsArray = correctedEmail.split(".");
// 	 	 		
// 	 	 		if(domainPartsArray[1].toLowerCase() === "com") //These domains all have "com" extension
// 	 	 		{
// 	 	 			//Nothig to do
// 	 	 		}
// 	 	 		else if(domainPartsArray[1] === "")
// 	 	 		{
// 	 	 			alert("domainPartsArray[1] is null! ");
// 	 	 		}
// 	 	 		else
// 	 	 		{
// 	 	 			correctedEmail = correctedEmail.replace(domainPartsArray[1],"com");
// 	 	 		}
// 	 	 		
// 	 	 		alert("correctedEmail: " + correctedEmail);
 	 			
// 	 			if(TRACKER == null)
// 	 			{
// 	 				alert("TRACKER is NULL");
// 	 			}
// 	 			else
// 	 			{
// 	 				alert("TRACKER is NOT null");
// 	 			}
 	 	 		
 				if(LAN_OPERATOR.lang === "en")
 				{
 					tooltipMessage = TRACKER.langOperator.didYouMean + " <a style='cursor:pointer;' onclick='document.getElementById(\"RegisterForm_email\").value = \"" + correctedEmail + "\";$(\"#registerFormTermsText\").tooltipster(\"hide\"); $(\"#RegisterForm_email\").removeClass(\"warning\"); $(\"#RegisterForm_email\").removeClass(\"error\"); bRegisterFormEmailWarningExists = false;'>" + correctedEmail + "</a> ? " + TRACKER.langOperator.ifSoClickOnSuggestedEmail;
 				}
 				else
 				{
 					tooltipMessage = "<a style='cursor:pointer;' onclick='document.getElementById(\"RegisterForm_email\").value = \"" + correctedEmail + "\";$(\"#registerFormTermsText\").tooltipster(\"hide\"); $(\"#RegisterForm_email\").removeClass(\"warning\"); $(\"#RegisterForm_email\").removeClass(\"error\"); bRegisterFormEmailWarningExists = false;'>" + correctedEmail + "</a> " + TRACKER.langOperator.didYouMean + "? " + TRACKER.langOperator.ifSoClickOnSuggestedEmail;	
 				}

 				//alert(tooltipMessage);
 	 		}			
 		}
 		
 		//var correctedEmail = enteredEmail.replace(domainPartsArray[0],"gmail");
 				
 		if(bCorrectionRequired)
 		{
 			//alert("bCorrectionRequired");

 			$("#RegisterForm_email").tooltipster('hide');
 			$("#registerFormTermsText").tooltipster('update', tooltipMessage);
 			//$("#registerFormTermsText").tooltipster('update', 'AAA');
 	 		$("#registerFormTermsText").tooltipster('show');
 	 		$("#RegisterForm_email").addClass("warning");
 	 		bRegisterFormEmailWarningExists = true;
 		}
 		else
 		{
 			//alert("NOOO bCorrectionRequired");
 			
 			if(true == bRegisterFormEmailErrorExists)
 			{
 				$("#RegisterForm_email").tooltipster('show'); 				
 			} 
 			
 			$("#registerFormTermsText").tooltipster('hide');
 			bRegisterFormEmailWarningExists = false;
 			$("#RegisterForm_email").removeClass("warning");
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

//function updateRegisterEmailTooltip(par_type, par_message, par_bShow) 
//{
//	switch(par_type)
//	{
//		case "info":
//			$("#RegisterForm_email").tooltipster('update', TRACKER.langOperator.registerEmailNotificationMessage);
//	 		$("#RegisterForm_email").tooltipster('show');
//	 		break;
//		  
//		case "warning":
//			execute code block 2
//			break;
//		  
//		case "error":
//			execute code block 2
//			break;
//			  
//		default:
//			break;
//	}
//}

function bindElements(langOperator, trackerOp) 
{	 		
	/**
	 * binding operation to search user
	 */	

 	$("#RegisterForm_email").focus(function ()	{ 		
 		//$("#RegisterForm_email").tooltipster('update', '<div id="registerEmailNotificationMessageId">' + TRACKER.langOperator.registerEmailNotificationMessage + '</div>');
 		
 		if((false == bRegisterFormEmailWarningExists) && (false == bRegisterFormEmailErrorExists))
		{
 	 		$("#RegisterForm_email").tooltipster('update', TRACKER.langOperator.registerEmailNotificationMessage);
 	 		$("#RegisterForm_email").tooltipster('show');		
		}
	});
 	
 	$("#LoginForm_email").focus(function ()	{
 		//$("#LoginForm_email").tooltipster('update', TRACKER.langOperator.geolocationNotificationMessage);
 		$("#LoginForm_email").tooltipster('show'); 		
	});

 	$("#LoginForm_email").blur(function () {
 		$("#LoginForm_email").tooltipster('hide');
 	});
 	
 	$("#LoginForm_password").focus(function ()	{
 		//$("#LoginForm_email").tooltipster('update', TRACKER.langOperator.geolocationNotificationMessage);
 		$("#LoginForm_email").tooltipster('show'); 		
	}); 	
 	
 	$("#LoginForm_password").blur(function () {
 		$("#LoginForm_email").tooltipster('hide');
 	}); 	
 	
 	
 	
//  Label'lar webkit browser'larda focus almadigindan bu cozum calismiyor, linkler de tiklaninca sayfanin yukarisina gittiginden click cozumu uygulandi
// 	$("#appQRCodeLink").focus(function (event)	{
// 		$("#appQRCodeLink").tooltipster('update', '<table><tr><td><img src="images/QR_code.png" width="130" height="130" style="cursor:none;"></td><td>' + TRACKER.langOperator.QRCodeNotificationMessage + '</td></tr></table>');
// 		$("#appQRCodeLink").tooltipster('show');
//	});
// 	
// 	$("#appQRCodeLink").blur(function ()	{
// 		$("#appQRCodeLink").tooltipster('hide');
// 	}); 	
 		
 	$("#appQRCodeLink").click(function (event)	{
 		event.stopPropagation();
 		$("#appQRCodeLink").tooltipster('update', '<table><tr><td><img src="images/QR_code.png" width="130" height="130" style="cursor:none;"></td><td>' + TRACKER.langOperator.QRCodeNotificationMessage + '</td></tr></table>');
 		$("#appQRCodeLink").tooltipster('show');
 		bAppQRCodeTooltipOpened = true;
	});
 	
 	$('html').click(function() {
 		if(true == bAppQRCodeTooltipOpened)
 		{
 			$("#appQRCodeLink").tooltipster('hide');
 		}
 		
// 		alertMsg("Clicked");
// 		$('#profilePhoto').css('opacity', 0.5);
// 		$('#profilePhotoLoading').show();
 	}); 	

	$("#bar").click(function ()	{
		
		//alertMsg("Deneme");
		
	    var w = $(window).width();
	    
	    if(w < 1007)
	    {
	    	w = 1007;
	    }	    
		
		if ($('#sideBar > #content').css('display') == "none")
		{					
			var offsetLeft = 396;
			
			//If logged in and top bar height decreased
			if(document.getElementById('topBar').style.height == "70px")
			{

			}
			else //If not logged in
			{
//				showRegisterFormErrorsIfExist();
//				showResetPasswordFormErrorsIfExist();				
			}
			
			//$('.logo_inFullMap').fadeOut().animate({left:'10px'});
			$('#sideBar > #content').fadeIn('slow');
			$('#sideBar').animate({width:'380px'}, function(){  $('#bar').css('background-image','url("images/left.png")'); showRegisterFormErrorsIfExist(); showResetPasswordFormErrorsIfExist(); });
			$('#map').animate({width:(w - offsetLeft)});
			$('#bar').animate({left:'380px'});			
		}	
		else 
		{
			var offsetLeft = 16;
			
			//If logged in and top bar height decreased
			if(document.getElementById('topBar').style.height == "70px")
			{

			}
			else //If not logged in
			{
				hideRegisterFormErrorsIfExist();
				hideResetPasswordFormErrorsIfExist();				
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
		$("#showRegisterFormLink").hide();
		$("#formContent").fadeToggle( "slow", function(){showRegisterFormErrorsIfExist(); showResetPasswordFormErrorsIfExist(); $("#showCachedPublicPhotosLink").show(); $("#publicUploadsContent").hide();});
		//$('#formContent').animate({height:'100%', marginTop:'0'}, function(){ $('#formContent').show(); $("#showRegisterFormLink").hide(); $("#publicUploads").hide(); $("#showPublicPhotosLink").show();});		
	});
	
	$("#showCachedPublicPhotosLink").click(function (){
		$("#showCachedPublicPhotosLink").hide();
		$("#formContent").fadeToggle( "slow", function(){ hideRegisterFormErrorsIfExist(); hideResetPasswordFormErrorsIfExist(); $("#showRegisterFormLink").show(); $("#publicUploadsContent").show();});
	});

	function changeSrcBack(elementid, imgSrc)
	{
	  document.getElementById(elementid).src = imgSrc;
	}
	
	$( document ).ready(function() {
		var h = $(window).height();
		var w = $(window).width();
		var offsetTop = 0;
		
		if(document.getElementById('topBar').style.height == "70px")
		{
			offsetTop = 70;
		}
		else
		{
			offsetTop = 85;
		}
		
		$("#users_tab").css("min-height", (485 + 100 - 70 - 82)); $("#users_tab").css("height", (h - offsetTop - 82));
		$("#photos_tab").css("min-height", (485 + 100 - 70 - 82)); $("#photos_tab").css("height", (h - offsetTop - 82));
		$("#groups_tab").css("min-height", (485 + 100 - 70 - 68)); $("#groups_tab").css("height", (h - offsetTop - 72));

//		var myElem = document.getElementById('usersGridView');
//		if(myElem == null)
//		{
//			alertMsg('"#usersGridView" does not exist!');
//		}
//		else
//		{
//			alertMsg('"#usersGridView" exists');
//		}
		
		//alertMsg("height:" + (h - offsetTop - 82));
		    
//		var userListHeight = ((h - offsetTop - 82) > 445)?(h - offsetTop - 82):445;
//		
//		//$.post('saveToSession.php', { width:w, height:userListHeight }, function(json) {
//		$.post('index.php?r=site/getWinDimensions', { width:w, height:userListHeight }, function(json) {	
//	        if(json.outcome == 'success') {
//	        	//alertMsg('OKKKKK');
//	            // do something with the knowledge possibly?
//	        } else {
//	            alertMsg('Unable to let PHP know what the screen resolution is!');
//	        }
//	    },'json');
	});	

	$(window).resize(function () {
	    var h = $(window).height(), offsetTop = 0; // Calculate the top offset	    
	    var w = $(window).width(), offsetLeft = 0; // Calculate the left offset
	    
	    var usersCount = 5;

	    //alertMsg('Javascript');
	    
	    //alertMsg('Height:'+(h)+' Width:'+(w));

	    if(preWindowWidth != w)
	    {
	    	var tempWindowWidthSize = 0;
	    	
	    	if(w < loginWidthMiddleCheckConstant) //En kucuk pencere boyutu
	    	{
	    		tempWindowWidthSize = 0;
	    	}
	    	else if(w < loginWidthCheckConstant) //Ortanca pencere boyutu
	    	{
	    		tempWindowWidthSize = 1;
	    	}
	    	else //En buyuk pencere boyutu
	    	{
	    		tempWindowWidthSize = 2;
	    	}
	    	
	    	if(windowWidthSize != tempWindowWidthSize)
	    	{
	    		switch(tempWindowWidthSize)
	    		{
	    		case 0: //En kucuk
			    	$("#loginEmail").css("width", "134px");		    	
			    	document.getElementById('LoginForm_email').size = "20";
			    	
			    	$("#loginPassword").css("width", "134px");		    	
			    	document.getElementById('LoginForm_password').size = "20";	
			    	
			    	$("#loginButton").css("padding-left", "134px");
			    	$("#loginOr").css("padding-left", "4px");
			    	
			    	$("#loginLongFacebookButton").hide(); 
			    	$("#loginShortFacebookButton").show();	    			
	    			break;
	    			
	    		case 1: //Ortancı
			    	$("#loginEmail").css("width", "164px");		    	
			    	document.getElementById('LoginForm_email').size = "25";
			    	
			    	$("#loginPassword").css("width", "164px");		    	
			    	document.getElementById('LoginForm_password').size = "25";	
			    	
			    	$("#loginButton").css("padding-left", "134px");
			    	$("#loginOr").css("padding-left", "4px");
			    	
			    	$("#loginShortFacebookButton").hide(); 
			    	$("#loginLongFacebookButton").show();	    			
	    			break;
	    			
	    		case 2: //En buyuk
			    	$("#loginEmail").css("width", "200px");		    	
			    	document.getElementById('LoginForm_email').size = "30";	

			    	$("#loginPassword").css("width", "200px");		    	
			    	document.getElementById('LoginForm_password').size = "30";	
			    	
			    	$("#loginButton").css("padding-left", "138px");
			    	$("#loginOr").css("padding-left", "10px");
			    	
			    	$("#loginShortFacebookButton").hide(); 
			    	$("#loginLongFacebookButton").show();	    			
	    			break;
	    			
	    		default:
			    	$("#loginEmail").css("width", "200px");		    	
			    	document.getElementById('LoginForm_email').size = "30";	
	
			    	$("#loginPassword").css("width", "200px");		    	
			    	document.getElementById('LoginForm_password').size = "30";	
			    	
			    	$("#loginButton").css("padding-left", "138px");
			    	$("#loginOr").css("padding-left", "10px");
			    	
			    	$("#loginShortFacebookButton").hide(); 
			    	$("#loginLongFacebookButton").show();	    			
	    			break;	    			
	    		}
	    		
	    		if(bLoginFormEmailErrorExists)
	    		{
	    			$("#LoginForm_email").tooltipster('reposition');
	    		}
	    		
	    		if(bLoginFormPasswordErrorExists)
	    		{
	    			$("#LoginForm_password").tooltipster('reposition');
	    		}	    		
	    		
	    		windowWidthSize = tempWindowWidthSize;	    		
	    	}
	    	
	    	preWindowWidth = w;	 
	    }

	  //If logged in and top bar height decreased
		if(document.getElementById('topBar').style.height == "70px")
		{
			offsetTop = 70;
			offsetLeft = 396;
			
			if ($('#sideBar > #content').css('display') == "none")
			{
				//$('#map').css('width', '99%');
				$('#map').css('width', (w - 16));
				$('#map').css('min-width', (735 + 260));
				$('#bar').css('left', '0px');
				
				//alertMsg('Wide');
			}
			else
			{
				$('#map').css('width', (w - offsetLeft));
				$('#map').css('min-width', (735 + 260 - 380));
				$('#bar').css('left', '380px');
				
				//alertMsg('Narrow');
			}
			
			$("#users_tab").css("min-height", (485 + 100 - 70 - 82)); $("#users_tab").css("height", (h - offsetTop - 82));
			$("#photos_tab").css("min-height", (485 + 100 - 70 - 82)); $("#photos_tab").css("height", (h - offsetTop - 82));
			$("#groups_tab").css("min-height", (485 + 100 - 70 - 68)); $("#groups_tab").css("height", (h - offsetTop - 72));
			
	 		$("#usersGridView").css("min-height", 370);
			$("#usersGridView").css("height", (h - offsetTop - 82 - 20));
			
	 		$("#uploadsGridView").css("min-height", 370);
			$("#uploadsGridView").css("height", (h - offsetTop - 82 - 20));
			
	 		$("#groupsGridView").css("min-height", 440);
			$("#groupsGridView").css("height", (h - offsetTop - 82 + 10));				

		    //alertMsg("resize - height: " + (h - offsetTop - 82 - 100));			
			
			//$("#usersGridView").css("height", userListHeight - 50);
			//$("#uploadsGridView").css("height", userListHeight - 50);
			//$("#groupsGridView").css("height", userListHeight - 50);
			
//		    $.post('saveToSession.php', { width:w, height:userListHeight }, function(json) {
//		        if(json.outcome == 'success') {
//		        	//alertMsg('OKKKKK');
//		            // do something with the knowledge possibly?
//		        } else {
//		            alertMsg('Unable to let PHP know what the screen resolution is!');
//		        }
//		    },'json');	    			
		}
		else
		{			
			offsetTop = 85;
			offsetLeft = 396;
			
			var userListHeight = ((h - offsetTop - 82) > 445)?(h - offsetTop - 82):445;
			$("#uploadsGridView").css("height", userListHeight - 40);
			
			if ($('#sideBar > #content').css('display') == "none")
			{
				//$('#map').css('width', '99%');
				$('#map').css('width', (w - 16));
				$('#map').css('min-width', (735 + 380));
				$('#bar').css('left', '0px');
				
				//alertMsg('Wide:' + (w - 16));
			}
			else
			{
				$('#map').css('width', (w - offsetLeft));
				$('#map').css('min-width', 735);
				$('#bar').css('left', '380px');
				
				//alertMsg('Narrow');
			}
			
	 		//Public upload'lar icin
			$("#publicUploadsGridView").css("min-height", 450);
			$("#publicUploadsGridView").css("height", (h - offsetTop));
		}
		
		//alertMsg('binding.js: offsetTop:'+(offsetTop)+' offsetLeft:'+(offsetLeft));
		
	    $('#map').css('height', (h - offsetTop));
	    $('#bar').css('height', (h - offsetTop));
	    $('#sideBar').css('height', (h - offsetTop));  
	}).resize();
//     
//     $('#tab_view').bind('tabsselect', function(event, ui) {
//    	 switch (ui.index)
//    	 {
//	    	 case 0: //Friends
//	    	 {
//	    		 TRACKER.showImagesOnTheMap = false; TRACKER.showUsersOnTheMap = true; TRACKER.getImageList(); TRACKER.getFriendList(1, 0/*UserType::RealUser*/);
//	    	 }
//	    	 break;
//	    	   
//	    	 case 1: //Uploads
//	    	 {
//	    		 TRACKER.showImagesOnTheMap = true; TRACKER.showUsersOnTheMap = false; TRACKER.getImageList(); TRACKER.getFriendList(1, 0/*UserType::RealUser*/);
//	    	 }
//	    	 break;
//	    	   
//	    	 case 2: //Groups
//	    	 {
//	    		 TRACKER.showImagesOnTheMap = false; TRACKER.showUsersOnTheMap = true; TRACKER.getImageList(); TRACKER.getFriendList(1, 0/*UserType::RealUser*/);
//	    	 }
//	    	 break;
//    	 }     	 
//     });
     
//     $('#tab_view').bind('easytabs:after', function(event, ui) {
//    	 switch (ui.index)
//    	 {
//	    	 case 0: //Friends
//	    	 {
//	    		 TRACKER.showImagesOnTheMap = false; TRACKER.showUsersOnTheMap = true; TRACKER.getImageList(); TRACKER.getFriendList(1, 0/*UserType::RealUser*/);
//	    	 }
//	    	 break;
//	    	   
//	    	 case 1: //Uploads
//	    	 {
//	    		 TRACKER.showImagesOnTheMap = true; TRACKER.showUsersOnTheMap = false; TRACKER.getImageList(); TRACKER.getFriendList(1, 0/*UserType::RealUser*/);
//	    	 }
//	    	 break;
//	    	   
//	    	 case 2: //Groups
//	    	 {
//	    		 TRACKER.showImagesOnTheMap = false; TRACKER.showUsersOnTheMap = true; TRACKER.getImageList(); TRACKER.getFriendList(1, 0/*UserType::RealUser*/);
//	    	 }
//	    	 break;
//    	 }     	 
//     });
     
	$('#tab_view').bind('easytabs:before', function(e, $clicked, $targetPanel, settings){
        switch($targetPanel.get(0).id)
        {
		   	 case "users_tab": //Friends
		   	 {
		   		 TRACKER.showImagesOnTheMap = false; TRACKER.showUsersOnTheMap = true; TRACKER.getImageList(); TRACKER.getFriendList(1, 0/*UserType::RealUser*/);
		   	 }
		   	 break;
		   	   
		   	 case "photos_tab": //Uploads
		   	 {
		   		 TRACKER.showImagesOnTheMap = true; TRACKER.showUsersOnTheMap = false; TRACKER.getImageList(); TRACKER.getFriendList(1, 0/*UserType::RealUser*/);
		   	 }
		   	 break;
		   	   
		   	 case "groups_tab": //Groups
		   	 {
		   		 TRACKER.showImagesOnTheMap = false; TRACKER.showUsersOnTheMap = true; TRACKER.getImageList(); TRACKER.getFriendList(1, 0/*UserType::RealUser*/);
		   	 }
		   	 break;       
        }
	});
	
	$('#tab_view').bind('easytabs:ajax:complete', function(e, $clicked, $targetPanel, response, status, xhr){
		var h = $(window).height();
		var offsetTop = 70;

        switch($targetPanel.get(0).id)
        {
		   	 case "users_tab": //Friends
		   	 {
		 		$("#usersGridView").css("min-height", 370);
				$("#usersGridView").css("height", (h - offsetTop - 82 - 20));
		   	 }
		   	 break;
		   	   
		   	 case "photos_tab": //Uploads
		   	 {
		 		$("#uploadsGridView").css("min-height", 370);
				$("#uploadsGridView").css("height", (h - offsetTop - 82 - 20));
				uploadsGridViewId = 'uploadListView';
		   	 }
		   	 break;
		   	   
		   	 case "groups_tab": //Groups
		   	 {
		 		$("#groupsGridView").css("min-height", 440);
				$("#groupsGridView").css("height", (h - offsetTop - 82 + 10));	
		   	 }
		   	 break;       
        }		

		//alertMsg("ajax complete");
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

