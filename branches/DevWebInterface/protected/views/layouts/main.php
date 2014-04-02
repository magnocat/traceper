<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<title><?php echo Yii::app()->name; ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="keywords" content="" />
<meta name="description" content="Location-based social network and GPS tracking system" />

<link rel="stylesheet" type="text/css"
	href="<?php echo Yii::app()->request->baseUrl; ?>/css/cssreset-min.css" />
	
<?php
if (YII_DEBUG)
{		
	?>	
	<link rel="stylesheet" type="text/css"
		href="<?php echo Yii::app()->request->baseUrl; ?>/css/normal/style.css"
		media="screen, projection" />
	<link rel="stylesheet" type="text/css"
		href="<?php echo Yii::app()->request->baseUrl; ?>/css/normal/main.css" />
	<link rel="stylesheet" type="text/css"
		href="<?php echo Yii::app()->request->baseUrl; ?>/css/normal/form.css" />	
	<link rel="stylesheet" type="text/css"
		href="<?php echo Yii::app()->request->baseUrl; ?>/css/normal/iconfonts.css" />
	<link rel="stylesheet" type="text/css"
		href="<?php echo Yii::app()->request->baseUrl; ?>/css/normal/tooltipMenu.css" />
	<?php
}
else //DEPLOYMENT
{
	?>	
	<link rel="stylesheet" type="text/css"
		href="<?php echo Yii::app()->request->baseUrl; ?>/css/min/style-min.css"
		media="screen, projection" />
	<link rel="stylesheet" type="text/css"
		href="<?php echo Yii::app()->request->baseUrl; ?>/css/min/main-min.css" />
	<link rel="stylesheet" type="text/css"
		href="<?php echo Yii::app()->request->baseUrl; ?>/css/min/form-min.css" />	
	<link rel="stylesheet" type="text/css"
		href="<?php echo Yii::app()->request->baseUrl; ?>/css/min/iconfonts-min.css" />
	<link rel="stylesheet" type="text/css"
		href="<?php echo Yii::app()->request->baseUrl; ?>/css/min/tooltipMenu-min.css" />
	<?php		
}
?>				
					
<link rel="shortcut icon"
	href="<?php echo Yii::app()->request->baseUrl; ?>/images/icon.png" type="image/x-icon" />

<?php
if (YII_DEBUG)
{		
	?>
	<script type="text/javascript"
		src="<?php echo Yii::app()->request->baseUrl; ?>/js/normal/DataOperations.js"></script>
	<script type="text/javascript"
		src="<?php echo Yii::app()->request->baseUrl; ?>/js/maps/normal/MapStructs.js"></script>
	<script type="text/javascript"
		src="<?php echo Yii::app()->request->baseUrl; ?>/js/maps/normal/GMapOperator.js"></script>	
	<script type="text/javascript"
		src="<?php echo Yii::app()->request->baseUrl; ?>/js/normal/TrackerOperator.js"></script>
	<script type="text/javascript"
		src="<?php echo Yii::app()->request->baseUrl; ?>/js/normal/LanguageOperator.js"></script>
	<script type="text/javascript"
		src="<?php echo Yii::app()->request->baseUrl; ?>/js/normal/svgcheckbx.js"></script>
	<script type="text/javascript"
		src="<?php echo Yii::app()->request->baseUrl; ?>/js/normal/cbpTooltipMenu.js"></script>	
	<script type="text/javascript"
		src="<?php echo Yii::app()->request->baseUrl; ?>/js/normal/jquery.placeholder.js"></script>		
	<script type="text/javascript"
		src="<?php echo Yii::app()->request->baseUrl; ?>/js/normal/jquery.tooltipster.js"></script>		
	<?php //2.2.3 calisiyor, 2.3.0 calismiyor
}
else //DEPLOYMENT
{
	?>
	<script type="text/javascript"
		src="<?php echo Yii::app()->request->baseUrl; ?>/js/min/DataOperations.min.js"></script>
	<script type="text/javascript"
		src="<?php echo Yii::app()->request->baseUrl; ?>/js/maps/min/MapStructs.min.js"></script>
	<script type="text/javascript"
		src="<?php echo Yii::app()->request->baseUrl; ?>/js/maps/min/GMapOperator.min.js"></script>
	<script type="text/javascript"
		src="<?php echo Yii::app()->request->baseUrl; ?>/js/min/TrackerOperator.min.js"></script>
	<script type="text/javascript"
		src="<?php echo Yii::app()->request->baseUrl; ?>/js/min/LanguageOperator.min.js"></script>
	<script type="text/javascript"
		src="<?php echo Yii::app()->request->baseUrl; ?>/js/min/svgcheckbx.min.js"></script>
	<script type="text/javascript"
		src="<?php echo Yii::app()->request->baseUrl; ?>/js/min/cbpTooltipMenu.min.js"></script>		
	<script type="text/javascript"
		src="<?php echo Yii::app()->request->baseUrl; ?>/js/min/jquery.placeholder.min.js"></script>
	<script type="text/javascript"
		src="<?php echo Yii::app()->request->baseUrl; ?>/js/min/jquery.tooltipster.min.js"></script>		
	<?php		
}
?>	
	
<script type="text/javascript"
	src="<?php echo Yii::app()->request->baseUrl; ?>/js/modernizr.custom.js"></script>	

<?php

$token = null;
$tokenExists = false;

if (isset($_GET['tok'])  && ($_GET['tok'] != null))
{
	//Fb::warn("in main", "main");

	$token = $_GET['tok'];

	if(ResetPassword::model()->tokenExists($token))
	{
		$tokenExists = true;
		
		if(ResetPassword::model()->isRequestTimeValid($token))
		{
			$passwordResetRequestStatus = PasswordResetStatus::RequestValid;
		}
		else
		{
			$passwordResetRequestStatus = PasswordResetStatus::RequestInvalid;
		}
	}
	else
	{
		$passwordResetRequestStatus = PasswordResetStatus::NoRequest;
	}
}
else
{
	$passwordResetRequestStatus = PasswordResetStatus::NoRequest;
}

// Yii::app()->clientScript->registerScript(
// 		"test",
// 		"jQuery.ajax({
// 		type: 'POST',
// 		url: '".Yii::app()->createUrl('users/update', array('id'=>Yii::app()->user->id))."',
// 		success: function(html){
// 			jQuery('#userarea').html(html);
// 		}
// });
// 		",
// 		CClientScript::POS_READY
// 		);

if (Yii::app()->user->isGuest == false)
{
	$profilePhotoSource = null;
	$profilePhotoStatus = Users::model()->getProfilePhotoStatus(Yii::app()->user->id);
	$profilePhotoStatusTooltipMessage = null;
	$bothPhotoExists = null;
	$useAjaxLink = null;
	
	switch($profilePhotoStatus)
	{
		case Users::NO_TRACEPER_PROFILE_PHOTO_EXISTS:
			{
				if(Yii::app()->user->fb_id == 0)
				{
					$profilePhotoSource = null;
					$profilePhotoStatusTooltipMessage = Yii::t('site', 'Click here to upload your profile photo');
				}
				else
				{
					$profilePhotoSource = 'https://graph.facebook.com/'.Yii::app()->user->fb_id.'/picture?type=square';
					$profilePhotoStatusTooltipMessage = Yii::t('site', 'Click here to upload and set your profile photo. You will be able to set your profile photo as your Facebook profile photo again.');
				}
			}
			break;
	
		case Users::TRACEPER_PROFILE_PHOTO_EXISTS:
			{
				$profilePhotoSource = 'profilePhotos/'.Yii::app()->user->id.'.png?random='.time();
				$profilePhotoStatusTooltipMessage = Yii::t('site', 'Click here to change your profile photo');
	
				//Fb::warn($profilePhotoStatusTooltipMessage, "TRACEPER_PROFILE_PHOTO_EXISTS");
			}
			break;
	
		case Users::BOTH_PROFILE_PHOTOS_EXISTS_USE_FACEBOOK:
			{
				//$profilePhotoSource = 'https://graph.facebook.com/'.Yii::app()->user->fb_id.'/picture?type=square';
				$profilePhotoSource =  'https://graph.facebook.com/'.Yii::app()->user->fb_id.'/picture?type=square';
				//$profilePhotoStatusTooltipMessage = '4';
				$bothPhotoExists = 'useFacebook';
				
				$useAjaxLink = CHtml::ajaxLink(Yii::t('layout', 'Use my Traceper profile photo'), $this->createUrl('users/useTraceperProfilePhoto'),
						array(
// 								'complete'=> 'function() {}',
// 								'update'=> '#userarea',
								
								'success'=> 'function(msg) {
												if(msg == "Login Required")
												{
													location.reload();
												}
												else
												{
													$("#userarea").html(msg);
	
													$("#uploadProfilePhotoSuccessfulTooltip").css("bottom", "22px");
													$("#uploadProfilePhotoSuccessfulTooltip").tooltipster("update", "'.Yii::t('site', 'You have changed your profile photo successfully.').'");
											 		$("#uploadProfilePhotoSuccessfulTooltip").tooltipster("show");
						
													var timeStamp = new Date().getTime();													
													var imageSrc = "profilePhotos/'.Yii::app()->user->id.'.png?random=" + timeStamp;
						
													MAP_OPERATOR.updateMarkerImage(currentUserMarker, imageSrc, true);
													TRACKER.users['.Yii::app()->user->id.'].mapMarker[0].infoWindow.setContent(getContentFor('.Yii::app()->user->id.', imageSrc));				
												}															
											}'							
						),
						array(
								'id'=>'useTraceperProfilePhotoAjaxLink-'.uniqid(),
						));				
			}
			break;
	
		case Users::BOTH_PROFILE_PHOTOS_EXISTS_USE_TRACEPER:
			{
				$profilePhotoSource = 'profilePhotos/'.Yii::app()->user->id.'.png?random='.time();
				//$profilePhotoStatusTooltipMessage = '5';
				$bothPhotoExists = 'useTraceper';
				
				$useAjaxLink = CHtml::ajaxLink(Yii::t('layout', 'Use my Facebook profile photo'), $this->createUrl('users/useFacebookProfilePhoto'),
						array(
// 								'complete'=> 'function() {}',
// 								'update'=> '#userarea',
								
								'success'=> 'function(msg) {
												if(msg == "Login Required")
												{
													location.reload();
												}
												else
												{
													$("#userarea").html(msg);
													
													$("#uploadProfilePhotoSuccessfulTooltip").css("bottom", "22px");
													$("#uploadProfilePhotoSuccessfulTooltip").tooltipster("update", "'.Yii::t('site', 'You have changed your profile photo successfully.').'");
													$("#uploadProfilePhotoSuccessfulTooltip").tooltipster("show");
									
													var imageSrc = "https://graph.facebook.com/'.Yii::app()->user->fb_id.'/picture?type=square";
									
													MAP_OPERATOR.updateMarkerImage(currentUserMarker, imageSrc, true);
													TRACKER.users['.Yii::app()->user->id.'].mapMarker[0].infoWindow.setContent(getContentFor('.Yii::app()->user->id.', imageSrc));
												}																	
											}'								
						),
						array(
								'id'=>'useFacebookProfilePhotoAjaxLink-'.uniqid(),
						));				
			}
			break;

		default:
			Fb::warn($profilePhotoSource, "default - profilePhotoSource");
	}	
}

if (YII_DEBUG)
{
	Yii::app()->clientScript->registerCssFile(Yii::app()->baseUrl.'/css/normal/tooltipster.css');
}
else //DEPLOYMENT
{
	Yii::app()->clientScript->registerCssFile(Yii::app()->baseUrl.'/css/min/tooltipster-min.css');	
}

//Yii::app()->clientScript->registerScriptFile(Yii::app()->baseUrl.'/js/jquery.tooltipster.min.js', CClientScript::POS_END);

Yii::app()->clientScript->registerScript('formTooltips',
		"
		//Bunu silme, e-mail field'inin tooltip'inin cikmasini sagliyor
		$(\"#RegisterForm_email\").tooltipster({
        	 theme: \".tooltipster-info\",
        	 position: \"right\",
        	 trigger: \"custom\",
        	 maxWidth: 500,
        	 onlyOne: false,
			 interactive: true,
        	 });

		$(\"#registerFormTermsText\").tooltipster({
        	 theme: \".tooltipster-warning\",
        	 position: \"right\",
        	 trigger: \"custom\",
        	 maxWidth: 500,
        	 offsetX: 1,
        	 offsetY: 172,
        	 onlyOne: false,
			 interactive: true,
        	 });        	 

		$(\"#showCreateGroupWindow\").tooltipster({
	       	 theme: \".tooltipster-info\",
	       	 content: \"".Yii::t('layout', '<b>Create New Group</b> </br></br> Traceper lets you group your friends. You could create new groups by this link. Moreover, you could enroll your friends into the related group and adjust the privacy settings of your groups at the tab \"Groups\".')."\",
	       	 position: \"bottom\",
	       	 trigger: \"hover\",
	       	 maxWidth: 300,
			 offsetY: 10,
         	 onlyOne: false,       	 
       	 });

		$(\"#showPublicPhotosLink\").tooltipster({
	       	 theme: \".tooltipster-info\",
	       	 content: \"".Yii::t('layout', 'Click here to view the list of photos shared publicly')."\",
	       	 position: \"right\",
	       	 trigger: \"hover\",
	       	 maxWidth: 260,
	       	 offsetX: 10,
	       	 onlyOne: false,       	 
      	 }); 

		$(\"#showCachedPublicPhotosLink\").tooltipster({
	       	 theme: \".tooltipster-info\",
	       	 content: \"".Yii::t('layout', 'Click here to view the list of photos shared publicly')."\",
	       	 position: \"right\",
	       	 trigger: \"hover\",
	       	 maxWidth: 260,
	       	 offsetX: 10,
	       	 onlyOne: false,       	 
     	 });

		$(\"#showRegisterFormLink\").tooltipster({
	       	 theme: \".tooltipster-info\",
	       	 content: \"".Yii::t('layout', ($tokenExists === true)?'Click here to view the password reset form again':'Click here to view the registration form again')."\",
	       	 position: \"right\",
	       	 trigger: \"hover\",
	       	 maxWidth: 220,
	       	 offsetX: 10,
	       	 onlyOne: false,       	 
     	 });	
				
		$(\"#appQRCodeLink\").tooltipster({
        	 theme: \".tooltipster-info\",
        	 position: \"top-left\",
        	 trigger: \"custom\",
        	 maxWidth: 365,
			 offsetX: 5,
        	 onlyOne: false,
			 interactive: true,
        	 });		

        bindTooltipActions();

		$('input, textarea').placeholder(); //Placeholder desteklemeyen browser'lar icin
		",
		CClientScript::POS_READY);

Yii::app()->clientScript->registerScript('deploymentModeVariableDeclaration',
		"var bDeploymentModeOn = ".((YII_DEBUG)?"false":"true").";",
		CClientScript::POS_HEAD);

if (Yii::app()->user->isGuest == false)
{
	Yii::app()->clientScript->registerScript('currentUserIdDeclaration',
			"currentUserId = ".Yii::app()->user->id.";",
			CClientScript::POS_HEAD);	
	
	if($profilePhotoStatus <= 1)
	{
		//Bu degiskenin diger upload profile photo error tooltip javascript kodlarindan once tanimlanmasi gerekiyor
		Yii::app()->clientScript->registerScript('uploadProfilePhotoVariableDeclarations', 
				"var bUploadProfilePhotoErrorExists = false;".
				(($profilePhotoSource == null)?"var eProfilePhotoExists = 'NONE';":"var eProfilePhotoExists = 'ONE';"), 
				CClientScript::POS_HEAD);		
		
		Yii::app()->clientScript->registerScript('uploadProfilePhotoTooltipsForNoOrSinglePhoto',
				"
				$(\"#uploadProfilePhoto\").tooltipster({
					theme: \".tooltipster-info\",
					content: \"".$profilePhotoStatusTooltipMessage."\",
					position: \"right\",
					trigger: \"hover\",
					maxWidth: 300,
					offsetX: 15,
					offsetY: -9,
					onlyOne: false,
				});			
					
				$(\"#uploadProfilePhoto\").click(function (){
					$(\"#uploadProfilePhoto\").tooltipster('hide');
				});
				",
				CClientScript::POS_READY);
	}
	else
	{
		Yii::app()->clientScript->registerScript('bothPhotoExistActions',
				"var eProfilePhotoExists = 'BOTH';
				var bUploadProfilePhotoErrorExists = false;
				var tooltipMenu = null;
				
				function closeTooltipMenu() 
				{
					var elem = document.getElementById('profilePhotoSettingsMenu');			
					var liElem = elem.querySelector( 'li' );																			
	
					tooltipMenu._closeMenu(liElem);	
				}
				",
				CClientScript::POS_HEAD);		
	}

	Yii::app()->clientScript->registerScript('uploadProfilePhotoFailureAndSuccessTooltips',
		"			
		$(\"#uploadProfilePhotoErrorTooltip\").tooltipster({
			theme: \".tooltipster-error\",
			content: \" \",
			position: \"right\",
			trigger: \"custom\",
			maxWidth: 320,
			offsetX: 45,
			offsetY: 1,
			onlyOne: false,
		});

		$(\"html\").click(function() {
			if(bUploadProfilePhotoErrorExists == true)
			{
				$(\"#uploadProfilePhotoErrorTooltip\").tooltipster('hide');

				if(eProfilePhotoExists == 'NONE')
				{
					$('#profilePhoto').css('opacity', 1);
					$('#profilePhotoUploadButton').removeClass('qq-upload-button-error-with-icon');
					$('#profileUserIcon').removeClass('profileUserIcon-error');
					$('#uploadProfilePhoto').removeClass('uploadProfilePhotoErrorForIcon');
					$('#uploadProfilePhotoErrorTooltip').css('bottom', '4px');
				}
				else if(eProfilePhotoExists == 'ONE')
				{
					$('#profilePhoto').css('opacity', 1);
					$('#profilePhotoUploadButton').removeClass('qq-upload-button-error');
					$('#profilePhoto').removeClass('profilePhoto-error');
				}
				else if(eProfilePhotoExists == 'BOTH')
				{
					$('#profilePhoto').css('opacity', 1);
					//$('#profilePhotoSettingsMenu').removeClass('profilePhotoSettingsMenu-error');
					$('#profilePhotoSettingsMenu').css('border', '');
					$('#profilePhotoSettingsMenu').css('left', '4px');
					$('#profilePhotoSettingsMenu').css('bottom', '0px');
					$('#profilePhotoSettingsMenu').css('background', '');			
				}			
				else
				{
					alert('Undefined eProfilePhotoExists:' + eProfilePhotoExists);
				}

				bUploadProfilePhotoErrorExists = false;
			}
			else
			{

			}
		});

		$(\"#uploadProfilePhoto\").hover(function (){
			if(bUploadProfilePhotoErrorExists)
			{
				$(\"#uploadProfilePhotoErrorTooltip\").tooltipster('hide');

				if(eProfilePhotoExists == 'NONE')
				{
					$('#profilePhoto').css('opacity', 1);
					$('#profilePhotoUploadButton').removeClass('qq-upload-button-error-with-icon');
					$('#profileUserIcon').removeClass('profileUserIcon-error');
					$('#uploadProfilePhoto').removeClass('uploadProfilePhotoErrorForIcon');
					$('#uploadProfilePhotoErrorTooltip').css('bottom', '4px');
				}
				else if(eProfilePhotoExists == 'ONE')
				{
					$('#profilePhoto').css('opacity', 1);
					$('#profilePhotoUploadButton').removeClass('qq-upload-button-error');
					$('#profilePhoto').removeClass('profilePhoto-error');
				}
				else if(eProfilePhotoExists == 'BOTH')
				{
					$('#profilePhoto').css('opacity', 1);
					//$('#profilePhotoSettingsMenu').removeClass('profilePhotoSettingsMenu-error');
					$('#profilePhotoSettingsMenu').css('border', '');
					$('#profilePhotoSettingsMenu').css('left', '4px');
					$('#profilePhotoSettingsMenu').css('bottom', '0px');
					$('#profilePhotoSettingsMenu').css('background', '');			
				}			
				else
				{
					alert('Undefined eProfilePhotoExists:' + eProfilePhotoExists);
				}

				bUploadProfilePhotoErrorExists = false;
			}
		});

		$(\"#uploadProfilePhotoSuccessfulTooltip\").tooltipster({
			theme: \".tooltipster-success\",
			content: \" \",
			position: \"right\",
			trigger: \"custom\",
			maxWidth: 300,
			offsetX: 45,
			offsetY: 1,
			onlyOne: false,
			timer: 1000,
			animation: \"grow\",
			speed: 500
		});
		",
		CClientScript::POS_READY);	
}

Yii::app()->clientScript->registerScript('browserDetection',
		'
			var BrowserDetect = {
			    init: function () {
			        this.browser = this.searchString(this.dataBrowser) || "An unknown browser";
			        this.version = this.searchVersion(navigator.userAgent)
			            || this.searchVersion(navigator.appVersion)
			            || "an unknown version";
			        this.OS = this.searchString(this.dataOS) || "an unknown OS";
			    },
			    searchString: function (data) {
			        for (var i=0;i<data.length;i++)    {
			            var dataString = data[i].string;
			            var dataProp = data[i].prop;
			            this.versionSearchString = data[i].versionSearch || data[i].identity;
			            if (dataString) {
			                if (dataString.indexOf(data[i].subString) != -1)
			                    return data[i].identity;
			            }
			            else if (dataProp)
			                return data[i].identity;
			        }
			    },
			    searchVersion: function (dataString) {
			        var index = dataString.indexOf(this.versionSearchString);
			        if (index == -1) return;
			        return parseFloat(dataString.substring(index+this.versionSearchString.length+1));
			    },
			    dataBrowser: [
			        {
			            string: navigator.userAgent,
			            subString: "YaBrowser",
			            identity: "Yandex Browser", //Icinde Chrome gectigi icin Chrome dan once bakilmali
						versionSearch: "YaBrowser"
			        },
			        {
			            string: navigator.userAgent,
			            subString: "OPR",
			            identity: "Opera",
						versionSearch: "OPR"
			        },				
			        {
			            string: navigator.userAgent,
			            subString: "Chrome",
			            identity: "Chrome"
			        },
			        {
			            string: navigator.userAgent,
			            subString: "Firefox",
			            identity: "Firefox"
			        },		
			        {
			            string: navigator.userAgent,
			            subString: "MSIE",
			            identity: "Internet Explorer",
			            versionSearch: "MSIE"
			        },
			        {
			            string: navigator.userAgent,
			            subString: "Trident",
			            identity: "Internet Explorer",
			            versionSearch: "rv"
			        },						
			        {
			            string: navigator.userAgent,
			            subString: "OmniWeb",
			            versionSearch: "OmniWeb/",
			            identity: "OmniWeb"
			        },
			        {
			            string: navigator.vendor,
			            subString: "Apple",
			            identity: "Safari",
			            versionSearch: "Version"
			        },
			        {
			            prop: window.opera,
			            identity: "Opera"
			        },
			        {
			            string: navigator.vendor,
			            subString: "iCab",
			            identity: "iCab"
			        },
			        {
			            string: navigator.vendor,
			            subString: "KDE",
			            identity: "Konqueror"
			        },
			        {
			            string: navigator.vendor,
			            subString: "Camino",
			            identity: "Camino"
			        },
			        {        // for newer Netscapes (6+)
			            string: navigator.userAgent,
			            subString: "Netscape",
			            identity: "Netscape"
			        },				
			        {
			            string: navigator.userAgent,
			            subString: "Gecko",
			            identity: "Mozilla",
			            versionSearch: "rv"
			        },
			        {         // for older Netscapes (4-)
			            string: navigator.userAgent,
			            subString: "Mozilla",
			            identity: "Netscape",
			            versionSearch: "Mozilla"
			        }
			    ],
			    dataOS : [
			        {
			            string: navigator.platform,
			            subString: "Win",
			            identity: "Windows"
			        },
			        {
			            string: navigator.platform,
			            subString: "Mac",
			            identity: "Mac"
			        },
			        {
			            string: navigator.platform,
			            subString: "Linux",
			            identity: "Linux"
			        }
			    ]			 
			};
			 
			BrowserDetect.init();

			//alert("navigator.userAgent: " + navigator.userAgent);
			//alert("Browser is : " + BrowserDetect.browser + " " + BrowserDetect.version + " / OS: " + BrowserDetect.OS);

		if((BrowserDetect.browser == "Internet Explorer") && (BrowserDetect.version <= 8))
		{
			alert("'.Yii::t('layout', 'Your Internet Explorer version is ').'" + BrowserDetect.version + ". " + "'.Yii::t('layout', 'Our website supports Internet Explorer versions higher than 8. Please use an Internet Explorer version of 9, 10 or higher or another modern browser (Chrome, Firefox, Safari...)').'");
		}		
		',
		CClientScript::POS_HEAD);

if (YII_DEBUG)
{
	Yii::app()->clientScript->registerScriptFile(Yii::app()->baseUrl.'/js/normal/bindings.js', CClientScript::POS_END);
}
else //DEPLOYMENT
{
	Yii::app()->clientScript->registerScriptFile(Yii::app()->baseUrl.'/js/min/bindings.min.js', CClientScript::POS_END);
}

if (Yii::app()->user->isGuest == false)
{
	Yii::app()->clientScript->registerScript('loggedInStyle',
			"
			var h = $(window).height(), offsetTop = 70; // Calculate the top offset
			var w = $(window).width(), offsetLeft = 396; // Calculate the left offset

			$('#topBar').css('height', '70px');
			$('#sideBar').css('top', '70px');
			//$('#sideBar').css('width', '380px');
			$('#sideBar').css('height', (h - offsetTop));
			$('#sideBar').css('min-height', (485 + 100 - 70));
			$('#bar').css('top', offsetTop);
			$('#bar').css('height', (h - offsetTop));
			//$('#bar').css('left', '380px');
			$('#bar').css('min-height', (485 + 100 - 70));
			$('#map').css('height', (h - offsetTop)); //$('#map').css('height', '94%');
			$('#map').css('width', (w - offsetLeft));
			$('#map').css('min-width', (735 + 260 - 380));
			$('#map').css('min-height', (485 + 100 - 70));
			",
			CClientScript::POS_READY);
	
 	//Burayı kaldırma, kaldırınca ilk önce birşey değişmiyor sanıyorsun, fakat "assets" dizinini kaldirinca hata veriyor 
	$assetsScriptUrl = Yii::app()->getAssetManager()->publish(Yii::getPathOfAlias('zii.widgets.assets'));
 	Yii::app()->getClientScript()->registerScriptFile($assetsScriptUrl .'/gridview/jquery.yiigridview.js');	
}
?>
    
<!-- <link href="http://vjs.zencdn.net/c/video-js.css" rel="stylesheet"> -->
<!-- <script src="http://vjs.zencdn.net/c/video.js"></script> -->

<!-- VIDEO WORK	 -->
	
<!-- <link href="http://localhost/traceper/branches/DevWebInterface/js/video-js/video-js.css" rel="stylesheet"> -->
<!-- <script src="http://localhost/traceper/branches/DevWebInterface/js/video-js/video.js"></script>		 -->

<?php

$userId = 0;

if(Yii::app()->user->isGuest == false)
{
	$userId = Yii::app()->user->id;
}

$app = Yii::app();
$language = 'tr';

if (isset($app->session['_lang']))
{
	$language = $app->session['_lang'];

	//echo 'Session VAR';
}
else
{
	$language = substr(Yii::app()->getRequest()->getPreferredLanguage(), 0, 2);
	//$app->session['_lang'] = substr(Yii::app()->getRequest()->getPreferredLanguage(), 0, 2);

	//echo 'Session YOK - pref. lang: '.substr(Yii::app()->getRequest()->getPreferredLanguage(), 0, 2);
}

$app->language = $language;

Yii::app()->clientScript->registerCoreScript('yiiactiveform');

//Fb::warn(Yii::app()->session['countryName'], "main");

//unset(Yii::app()->session['countryName']);

$countryName = null;

if(isset(Yii::app()->session['countryName']) == false)
{
	$countryName = "null";
}
else
{
	$countryName = "'".Yii::app()->session['countryName']."'";
}

//Fb::warn(Yii::app()->session['countryName'], "session['countryName']");

Yii::app()->clientScript->registerScript('appStart',"var checked = false;
	try
	{
		var mapStruct = new MapStruct();
		var initialLoc = new MapStruct.Location({latitude:39.504041,
		longitude:35.024414});
		mapOperator.initialize(initialLoc, ".$countryName.");
		//TODO: ../index.php should be changed
		//TODO: updateUserListInterval
		//TODO: queryIntervalForChangedUsers
		var trackerOp = new TrackerOperator('index.php', mapOperator, fetchPhotosDefaultValue, 10000 /*Users query period*/ /*5000*/, 30000 /*Uploads query period*/ /*30000*/);
		trackerOp.setLangOperator(langOp);
		bindElements(langOp, trackerOp);
		trackerOp.userId = ".$userId.";
	}
	catch (e) {

	}			
	",
	CClientScript::POS_READY);

if (Yii::app()->user->isGuest == true)
{
	Yii::app()->clientScript->registerScript('geolocationTooltipDeclaration',
			"
			if (navigator.geolocation)
			{
			//alert('Browser supports geolocation');
				
			$(\"#LoginForm_email\").tooltipster({
			theme: \".tooltipster-info\",
			content:TRACKER.langOperator.geolocationNotificationMessage,
			position: \"bottom\",
			trigger: \"custom\",
			maxWidth: 500,
			onlyOne: false,
			interactive: true,
			offsetX: 100,
			offsetY: 10,
			arrow:false
});
}
			else
			{
			//alert('Browser does NOT support geolocation!');
}
			", CClientScript::POS_READY);
}

if(isset(Yii::app()->session['countryName']) == false)
{
	Yii::app()->clientScript->registerScript('getGeolocationByIp',
			"
			jQuery.getJSON('http://freegeoip.net/json/', function(location) {
				//alert('freegeoip: location.country_name: ' + location.country_name + ' lat: ' + location.latitude + ' lon: ' + location.longitude);			
				MAP_OPERATOR.focusOnCountry(location.country_name, false);			
				$.post('index.php?r=users/getLocationByWebIP', { countryName:location.country_name, latitude:location.latitude, longitude:location.longitude });
			}) .done(function() {

				})
				.fail(function() { //freegeoip fail ederse telize den sorgula
					$.getJSON('http://www.telize.com/geoip?callback=?', function(json) {
						MAP_OPERATOR.focusOnCountry(json.country, false);			
						$.post('index.php?r=users/getLocationByWebIP', { countryName:json.country, latitude:json.latitude, longitude:json.longitude });
					}) .done(function() {
							//console.log('second success');
						})
						.fail(function() {
							//console.log('error');			
							jQuery.getJSON('http://ip-api.com/json', function(location) { //telize de fail ederse ip-api den sorgula
								MAP_OPERATOR.focusOnCountry(location.country, false);						
								$.post('index.php?r=users/getLocationByWebIP', { countryName:location.country, latitude:location.lat, longitude:location.lon });							
								//alert('ip-api: location.country: ' + location.country + ' location.city: ' + location.city  + ' lat: ' + location.lat + ' lon: ' + location.lon);
							}) .done(function() {
									//console.log('success');
								})
								.fail(function() { //freegeoip fail ederse telize den sorgula
									//console.log('error');
								})
								.always(function() {
									//console.log('complete');
								});				
						})
						.always(function() {
							//console.log('complete');
						});
					})
				.always(function() {
					//console.log('complete');
				});
			",
			CClientScript::POS_READY);

	// 	$pageContent = file_get_contents('http://freegeoip.net/json/'.$_SERVER['REMOTE_ADDR']);
	// 	$parsedJson  = json_decode($pageContent);

	// 	if((htmlspecialchars($parsedJson->country_name) == "Reserved") || ($parsedJson->country_name == null))
	// 	{
	// 		Yii::app()->session['countryName'] = "null";
	// 		Yii::app()->session['latitude'] = $parsedJson->latitude;
	// 		Yii::app()->session['longitude'] = $parsedJson->longitude;
	// 	}
	// 	else
	// 	{
	// 		Yii::app()->session['countryName'] = "'".htmlspecialchars($parsedJson->country_name)."'";
	// 		Yii::app()->session['latitude'] = $parsedJson->latitude;
	// 		Yii::app()->session['longitude'] = $parsedJson->longitude;
	// 	}
}
else
{
	Yii::app()->clientScript->registerScript('focusOnCountry',
			"
			MAP_OPERATOR.focusOnCountry('".Yii::app()->session['countryName']."', false);
			",
			CClientScript::POS_READY);
}

if (Yii::app()->user->isGuest == false)
{
	Yii::app()->clientScript->registerScript('getDataInBackground',
			'TRACKER.showImagesOnTheMap = false; TRACKER.showUsersOnTheMap = true;
			trackerOp.getFriendList(1, 0/*UserType::RealUser*/);
			trackerOp.getImageList();',
			CClientScript::POS_READY);
}
else
{
	Yii::app()->clientScript->registerScript('getDataInBackground',
			'TRACKER.showImagesOnTheMap = true; TRACKER.showUsersOnTheMap = false;
			trackerOp.getImageList(true, false);',
			CClientScript::POS_READY);	
}

// $createGeofenceFormJSFunction = "function createGeofenceForm(geoFence){"
// .CHtml::ajax(
// 		array(
// 				'url'=>Yii::app()->createUrl('geofence/createGeofence'),
// 				'complete'=> 'function(result) {
// 				$("#createGeofenceWindow").dialog("open"); return false;
// }',
// 				'update'=> '#createGeofenceWindow',
// 		)).
// 		"}";

// Yii::app()->clientScript->registerScript('getGeofenceInBackground',
// 		$createGeofenceFormJSFunction,
// 		CClientScript::POS_BEGIN);
?>
<script type="text/javascript">		
	var langOp = new LanguageOperator();
	var fetchPhotosDefaultValue =  1;  //TODO: $fetchPhotosInInitialization;
	
	langOp.load("<?php echo $language;?>", "<?php echo "http://".Yii::app()->request->getServerName().Yii::app()->request->getBaseUrl(); ?>");  //TODO: itshould be parametric
	
	var mapOperator = new MapOperator("<?php echo $language;?>");

	var h = $(window).height();
	var w = $(window).width();
	var offsetTop = 70; //User list login olmusken ciktigi icin

	var userListHeight = ((h - offsetTop - 82) > 445)?(h - offsetTop - 82):445;

	//alert("userListHeight: " + userListHeight);
	
	//$.post('saveToSession.php', { width:w, height:userListHeight }, function(json) {
	$.post('index.php?r=site/getWinDimensions', { width:w, height:userListHeight }, function(json) {	
        if(json.outcome == 'success') {
        	//alert('OKKKKK');
            // do something with the knowledge possibly?
        } else {
            alert('Unable to let PHP know what the screen resolution is!');
        }
    },'json');		
</script>
</head>
<body>

<?php
	//Yii::app()->session['_lang'] = 'en';

	//echo 'Yii version:'.Yii::getVersion();
	//echo 'YII_DEBUG:'.YII_DEBUG;
	//echo 'http://'.Yii::app()->request->getServerName().Yii::app()->request->getBaseUrl();
	
// 	$url = 'http://maps.googleapis.com/maps/api/geocode/json?latlng='.trim('36.852160').','.trim('30.757322').'&sensor=false&language=tr';
// 	$json = @file_get_contents($url);
// 	$data=json_decode($json);
// 	$status = $data->status;
// 	if($status=="OK")
// 		echo $data->results[0]->formatted_address;
// 	else
// 		echo "No address info";

	///////////////////////////// About Us Window///////////////////////////
	echo '<div id="aboutUsWindow" style="display:none;font-family:Helvetica;"></div>';	
	///////////////////////////// Terms Window ///////////////////////////
	echo '<div id="termsWindow" style="display:none;font-family:Helvetica;"></div>';
	///////////////////////////// Contact Window ///////////////////////////
	echo '<div id="contactWindow" style="display:none;font-family:Helvetica;"></div>';			
	///////////////////////////// Accept Terms For Login Window ///////////////////////////
	echo '<div id="acceptTermsForLoginWindow" style="display:none;font-family:Helvetica;"></div>';
	///////////////////////////// Accept Terms For Facebook Login Window ///////////////////////////
	echo '<div id="acceptTermsForFacebookLoginWindow" style="display:none;font-family:Helvetica;"></div>';
	///////////////////////////// Enter Password For Old Facebook User to Log In ///////////////////////////
	echo '<div id="enterPasswordForOldFacebookUserToLoginWindow" style="display:none;font-family:Helvetica;"></div>';
	///////////////////////////// Ask For Switch To Facebook Login Permanently ///////////////////////////
	echo '<div id="askForSwitchToFacebookLoginPermanentlyWindow" style="display:none;font-family:Helvetica;"></div>';			
	///////////////////////////// Register Window ///////////////////////////
	echo '<div id="registerWindow" style="display:none;font-family:Helvetica;"></div>';
	///////////////////////////// Register GPS Tracker Window ///////////////////////////
	echo '<div id="registerGPSTrackerWindow" style="display:none;font-family:Helvetica;"></div>';
	///////////////////////////// Register GPS Tracker Window ///////////////////////////
	echo '<div id="registerNewStaffWindow" style="display:none;font-family:Helvetica;"></div>';
	///////////////////////////// GeoFence Window ///////////////////////////
	echo '<div id="geoFenceWindow" style="display:none;font-family:Helvetica;"></div>';
	///////////////////////////// Change Password Window ///////////////////////////
	echo '<div id="changePasswordWindow" style="display:none;font-family:Helvetica;"></div>';
	///////////////////////////// Forget Password Window ///////////////////////////
	echo '<div id="forgotPasswordWindow" style="display:none;font-family:Helvetica;"></div>';
	///////////////////////////// Reset Password Window ///////////////////////////
	echo '<div id="resetPasswordWindow" style="display:none;font-family:Helvetica;"></div>';
	///////////////////////////// Activation Not Received Window ///////////////////////////
	echo '<div id="activationNotReceivedWindow" style="display:none;font-family:Helvetica;"></div>';	
	///////////////////////////// Invite User Window ///////////////////////////
	echo '<div id="inviteUsersWindow" style="display:none;font-family:Helvetica;"></div>';
	///////////////////////////// Friend Request Window ///////////////////////////
	echo '<div id="friendRequestsWindow" style="display:none;font-family:Helvetica;"></div>';
	///////////////////////////// Create Group Window ///////////////////////////
	echo '<div id="createGroupWindow" style="display:none;font-family:Helvetica;"></div>';
	///////////////////////////// Group Settings Window ///////////////////////////
	echo '<div id="groupSettingsWindow" style="display:none;font-family:Helvetica;"></div>';
	///////////////////////////// Group Privacy Settings Window ///////////////////////////
	echo '<div id="groupPrivacySettingsWindow" style="display:none;font-family:Helvetica;"></div>';
	///////////////////////////// Group Members Window ///////////////////////////
	echo '<div id="groupMembersWindow" style="display:none;font-family:Helvetica;"></div>';
	///////////////////////////// Geofence Settings Window ///////////////////////////
	echo '<div id="geofenceSettingsWindow" style="display:none;font-family:Helvetica;"></div>';
	////////// Create Geofence Window ///////////////////////////
	echo '<div id="createGeofenceWindow" style="display:none;font-family:Helvetica;"></div>';
	////////// User Search Results Window ///////////////////////////
	echo '<div id="userSearchResults" style="display:none;font-family:Helvetica;"></div>';
	////////// Upload Search Results Window ///////////////////////////
	echo '<div id="uploadSearchResults" style="display:none;font-family:Helvetica;"></div>';
	///////////////////////////// Database Operations Window ///////////////////////////
	echo '<div id="databaseOperationsWindow" style="display:none;font-family:Helvetica;"></div>';
	///////////////////////////// Your Email Seems To Be Invalid Window ///////////////////////////
	echo '<div id="yourEmailSeemsToBeInvalidWindow" style="display:none;font-family:Helvetica;"></div>';	
	
	//$this->renderPartial('//site/dialog');
	
	//Bir link ile bir view render() fonksiyonu ile render edildiginde once tum layout aciliyor sonra da $content degsikeninde tutulan
	//view render ediliyor
	?>
	
	<div id="mainLayoutContent">
		<?php echo $content; ?>
	</div>	
	
	<?php
	
	///////////////////////////// Photo Comment Window ///////////////////////////
// 	$this->beginWidget('zii.widgets.jui.CJuiDialog', array(
// 			'id'=>'photoCommentWindow',
// 			// additional javascript options for the dialog plugin
// 			'options'=>array(
// 					'title'=>Yii::t('layout', 'Comment Window'),
// 					'autoOpen'=>false,
// 					'modal'=>true,
// 					'resizable'=>false,
// 					'width'=> '400px',
// 					'height'=> '300'
// 			),
// 	));

// 	echo '	<div id="photoCommentForm" class="">
// 	<div id="photoCommentLabel">Comment:</div>
// 	<textarea id="photoCommentTextBox" cols="40" rows="7" style="resize:none">'.Yii::t('layout', 'Enter your comments here...').'</textarea><br/>
// 	<input type="button" id="sendCommentButton" value="Upload Comment" /><br/>
// 	<input type="button" id="deleteCommentButton" value="Delete Comment" />
// 	</div>';

// 	$this->endWidget('zii.widgets.jui.CJuiDialog');
	/////////////////////////////////////////////////////////////////////////////////////////////////			

	// this is a generic message dialog
	$this->beginWidget('zii.widgets.jui.CJuiDialog', array(
			'id'=>'messageDialog',
			// additional javascript options for the dialog plugin
			'options'=>array(
					'title'=>Yii::t('layout', 'Message'),
					'autoOpen'=>false,
					'modal'=>true,
					'resizable'=>false,
					'width'=>'400px',
					'height'=>'auto',
					//'buttons'=>array(Yii::t('common', 'OK')=>'js:function(){$(this).dialog("close");}'),
					//'open' => 'js:function(){ hideFormErrorsIfExist(); }',
					//'close' => 'js:function(){ showFormErrorsIfExist(); }'
			),
	));
		echo '<div align="center" id="messageDialogText" style="font-family:Helvetica;"></div>';
	?>

	<div id="messageDialogOK" style="display:none;padding-top:2em;text-align:center">
	<?php
		echo CHtml::ajaxLink('<button class="btn btn-sliding-green btn-sliding-green-a icon-checkmark" style="'.(($app->language == 'en')?'padding-left:25px;padding-right:25px;':'padding-left:28px;padding-right:28px;').'">'.'<span style="font-family:Helvetica">'.Yii::t('common', 'OK').'</span>'.'</button>', '#',
				array(),
				array('onclick'=>'$("#messageDialog").dialog("close"); return false;'));
	?>
	</div>					
	
	<?php
	$this->endWidget('zii.widgets.jui.CJuiDialog');

	// this is a long generic message dialog
	$this->beginWidget('zii.widgets.jui.CJuiDialog', array(
			'id'=>'longMessageDialog',
			// additional javascript options for the dialog plugin
			'options'=>array(
					'title'=>Yii::t('layout', 'Message'),
					'autoOpen'=>false,
					'modal'=>true,
					'resizable'=>false,
					'width'=>'600px',
					'height'=>'auto',
// 					'buttons'=>array(
// 							Yii::t('common', 'OK')=>"js:function(){
// 								$(this).dialog('close');
// 							}"
// 					),
					//'open' => 'js:function(){ hideFormErrorsIfExist(); }',
					//'close' => 'js:function(){ showFormErrorsIfExist(); }'
			),
	));
	//echo '</br>';
	echo '<div align="justified" id="longMessageDialogText" style="font-family:Helvetica;"></div>';
	
	?>
	
	<div id="longMessageDialogOK" style="display:none;padding-top:2em;text-align:center">
	<?php
		echo CHtml::ajaxLink('<button class="btn btn-sliding-green btn-sliding-green-a icon-checkmark" style="'.(($app->language == 'en')?'padding-left:25px;padding-right:25px;':'padding-left:28px;padding-right:28px;').'">'.'<span style="font-family:Helvetica">'.Yii::t('common', 'OK').'</span>'.'</button>', '#',
				array(),
				array('id'=>'longMessageDialogOKButton', 'onclick'=>'$("#longMessageDialog").dialog("close"); return false;'));
	?>
	</div>					
	
	<?php
			
	$this->endWidget('zii.widgets.jui.CJuiDialog');
	
	/*
	* this is a generic confirmation dialog
	*/
	$this->beginWidget('zii.widgets.jui.CJuiDialog', array(
			'id'=>'confirmationDialog',
			// additional javascript options for the dialog plugin
			'options'=>array(
					'title'=>Yii::t('layout', 'Confirmation'),
					'autoOpen'=>false,
					'modal'=>true,
					'resizable'=>false,
					'width'=>'500px',
// 					'buttons' =>array (
// 							Yii::t('common', 'OK')=>'js:function(){}',
// 							Yii::t('common', 'Cancel')=>"js:function() {
// 								$(this).dialog( 'close' );
// 							}"
// 					)
					),
	));
	echo '<div id="question" style="font-family:Helvetica;"></div>';
	
	?>
		
	<div id="confirmationDialogButtons" class="row buttons" style="display:none;padding-top:2em;text-align:center">
	<?php
		echo CHtml::label('<button class="btn btn-sliding-green btn-sliding-green-a icon-checkmark" style="'.(($app->language == 'en')?'padding-left:25px;padding-right:25px;':'padding-left:28px;padding-right:28px;').'">'.'<span style="font-family:Helvetica">'.Yii::t('common', 'OK').'</span>'.'</button>', '#',
				array('id'=>'confirmationDialogOK', 'style'=>'padding-right:10px;'));
		
		echo CHtml::ajaxLink('<button class="btn btn-sliding-red btn-sliding-red-a icon-close" style="'.(($app->language == 'en')?'padding-left:25px;padding-right:25px;':'padding-left:28px;padding-right:28px;').'">'.'<span style="font-family:Helvetica">'.Yii::t('common', 'Cancel').'</span>'.'</button>', '#',
				array(),
				array('onclick'=>'$("#confirmationDialog").dialog("close"); return false;'));		
	?>
	</div>					
	
	<?php
			
	$this->endWidget('zii.widgets.jui.CJuiDialog');

	?>
<script type="text/javascript">
//document.write(screen.width+'x'+screen.height);

	//document.getElementById('wrap').style.width=screen.width+'px';
	//document.getElementById('wrap').style.height=screen.height+'px';
</script>	
	<div id='wrap'>
<!-- 	<div id='wrap'> -->
		<div class='logo_inFullMap'></div>
		<div id='bar' style='z-index:50;'></div> <!-- Firefox'ta muhtemelen upload photo'daki iframe'den dolayı cache temizleme calismiyor, bunun icin css yerine burada z-index set et -->

		<div id='topBar'>
			<div id='topContent'>
				<?php
				if (Yii::app()->user->isGuest == false) {
					echo CHtml::link('<div id="logo" style="display:none"></div>', '#', array(
							'onclick'=>'location.reload();', 'class'=>'vtip', 'title'=>Yii::t('layout', 'Click here to reload the main page or scroll down to bottom of the page for contact and other info.'),
					));
						
					echo CHtml::link('<div id="logoMini"></div>', '#', array(
							'onclick'=>'location.reload();', 'class'=>'vtip', 'title'=>Yii::t('layout', 'Click here to reload the main page or scroll down to bottom of the page for contact and other info.'),
					));
				}
				else
				{
				?>
				<div>
					<div style="position:absolute;display:inline-block;font-size:3em;vertical-align:middle;width:70%;">
					<?php					
					echo CHtml::link('<div id="logo" style="width:246px;"></div>', '#', array(
							'onclick'=>'location.reload();', 'class'=>'vtip', 'title'=>Yii::t('layout', 'Click here to reload the main page or scroll down to bottom of the page for contact and other info.'),
					));
						
					echo CHtml::link('<div id="logoMini" style="display:none"></div>', '#', array(
							'onclick'=>'location.reload();', 'class'=>'vtip', 'title'=>Yii::t('layout', 'Click here to reload the main page or scroll down to bottom of the page for contact and other info.'),
					)); 
					?>
					</div>										
				</div>					
				<?php	
				}
				?>
				
				<div id="loginBlock"
				<?php
				if (Yii::app()->user->isGuest == false) {
					echo "style='display:none'";
				}
				else
				{
					echo "style='margin-left:245px;'";
				}
				?>>
					<?php
// 					if (Yii::app()->user->isGuest == true) {
// 						echo '<div class="upperMenu" style="margin-top:1em;width:4%;">
// 								<script src="http://static.qrspider.com/getqr/13/25837" language="javascript"></script>
// 							</div>';
// 					}					
					?>														

					<div class="form">
						<?php 							
						$form=$this->beginWidget('CActiveForm', array(
								'id'=>'login-form-main',
								'enableClientValidation'=>true,
								'clientOptions'=> array(
										'validateOnSubmit'=> true,
										'validateOnChange'=>false,
								),																		
						));														

						$model = new LoginForm;
						//$model->validate();
						?>

						<div id="forAjaxRefresh">
							<div id="loginEmail" class="upperMenu">
								<div style="height:3em;top:0%;padding:0px;">
									<?php echo $form->labelEx($model,'email'); ?>
									<?php echo $form->textField($model,'email', array('size'=>'30%','maxlength'=>'30%','tabindex'=>1)); ?>
									<?php 
	// 										  $errorMessage = $form->error($model,'email'); 
	// 										  if (strip_tags($errorMessage) == '') { echo '<div class="errorMessage">&nbsp;</div>'; }
	// 										  else { echo $errorMessage; }
										?>									
								</div>
								
								<div id="rememberMeCheckbox" class="ac-custom ac-checkbox ac-checkmark" style="margin-top:0px;padding-top:6px;">
									<?php echo $form->checkBox($model,'rememberMe',array('size'=>5,'maxlength'=>128,'tabindex'=>4)); ?>
									<?php echo $form->label($model,'rememberMe',array('style'=>'font-weight:normal;')); ?>
								</div>
								
								<script type="text/javascript">		
								checkSVGElements("rememberMeCheckbox", false/*par_isForm*/);	
								</script>									
							</div>

							<div id="loginPassword" class="upperMenu" style="width:180px;">
								<div style="height:3em;top:0%;padding:0px;">
									<?php echo $form->labelEx($model,'password'); ?>
									<?php echo $form->passwordField($model,'password', array('size'=>'30%','maxlength'=>'30%','tabindex'=>2)); ?>
									<?php 
											  //$errorMessage = $form->error($model,'password'); 
	// 										  if (strip_tags($errorMessage) == '') { echo '<div class="errorMessage">&nbsp;</div>'; }
	// 										  else { echo $errorMessage; }
										?>									
								</div>
								
			 					<div style="width:120px;margin-top:0px;padding-top:6px;">
									<?php
										echo CHtml::ajaxLink('<div id="forgotPassword">'.Yii::t('site', 'Forgot Password?').
															'</div>', $this->createUrl('site/forgotPassword'),
												array(
	// 													'complete'=> 'function() 
	// 																  { 
	// 																	hideFormErrorsIfExist();																														
	// 																	$("#forgotPasswordWindow").dialog("open"); 
	// 																	return false;
	// 																  }',
	// 													'update'=> '#forgotPasswordWindow',
										
														'success'=> 'function(result){
																		try
																		{
																			var obj = jQuery.parseJSON(result);
																			
																			if (obj.result)
																			{
																				if (obj.result == "1")
																				{
																					$("#forgotPasswordWindow").dialog("close");
																					TRACKER.showLongMessageDialog("'.Yii::t('site', 'We have sent the password reset link to your mail address \"<b>').'" + obj.email + "'.Yii::t('site', '</b>\". </br></br>Please make sure you check the spam/junk folder as well. The links in a spam/junk folder may not work sometimes; so if you face such a case, mark our e-mail as \"Not Spam\" and reclick the link.').'");
																				}
																				else if (obj.result == "0")
																				{
																					$("#forgotPasswordWindow").dialog("close");
																					TRACKER.showMessageDialog("'.Yii::t('site', 'An error occured while sending the e-mail. Please retry the process and if the error persists please contact us.').'");
																				}
																			}
																		}
																		catch (error)
																		{
																			var opt = {
																		        autoOpen: false,
																		        modal: true,
																				resizable: false,
																		        width: 600,
																		        title: "'.Yii::t('site', 'Forgot Password?').'"
																			};													
																		
																			$("#forgotPasswordWindow").dialog(opt).dialog("open");
																			$("#forgotPasswordWindow").html(result);
																		}
																	}',													
												),
												array(
														'id'=>'showForgotPasswordWindow','tabindex'=>5));									
										?>	 					
			 					</div>								
							</div>
						</div>							
													
						<div id="loginButton" class="upperMenu" style="margin-top:14px;width:140px;padding-left:158px;padding-right:0px;">								
						<?php
						//Yii butonları <buton> tag'i ile uretmedigi icin boyle yapildi, bu css'ler Yii'nin urettigi <input> ile calismiyor
						echo CHtml::ajaxLink('<button class="btn btn-3 btn-3a icon-login1" style="'.(($app->language == 'en')?'padding-left:65px;padding-right:25px;':'padding-left:50px').'">'.Yii::t('site', 'Log in').'</button>', $this->createUrl('site/login'),
								array(
										'type'=>'POST',
										'success'=> 'function(msg){
														try
														{
															var obj = jQuery.parseJSON(msg);
														
															if (obj.result)
															{
																if (obj.result == "1")
																{
																	//alert("result:1");
										
																	$("#tabViewList").html(obj.renderedTabView);
																	$("#userarea").html(obj.renderedUserAreaView);
																	$("#FriendRequestsIconLink").html(obj.renderedFriendshipRequestsView);										
																	$("#loginBlock").html(obj.loginSuccessfulActions);																											
																}
																else if (obj.result == "-3")
																{
																	//alert("result:-3");

																	$("#hiddenAjaxResponseToParse").html(obj.loginView);
																	$("#forAjaxRefresh").html(document.getElementById("ajaxLoginResponse").innerHTML);
																	$("#hiddenAjaxResponseToParse").html("");									
															
																	var opt = {
																		autoOpen: false,
																		modal: true,
																		resizable: false,
																		width: 600,
																		title: "'.Yii::t('site', 'Accept Terms to continue').'"
																	};
															
																	$("#acceptTermsForLoginWindow").dialog(opt).dialog("open");
																	$("#acceptTermsForLoginWindow").html(obj.renderedView);
																}
																else
																{
																	//alert("else");
																}
															}
														}
														catch (error)
														{
															//alert("error");
										
															$("#hiddenAjaxResponseToParse").html(msg);
															//alert(document.getElementById("ajaxLoginResponse").innerHTML);
															$("#forAjaxRefresh").html(document.getElementById("ajaxLoginResponse").innerHTML);
															$("#hiddenAjaxResponseToParse").html("");
															
															//Dondukten sonra gostermezsen ilk once geolocation tooltip i gosterilen ID de aynı oldugundan 
															//kisa sureli gozukuyordu
															if(bLoginFormEmailErrorExists)
															{
																$("#LoginForm_email").tooltipster("show");
															}
															
															if(bLoginFormPasswordErrorExists)
															{
																$("#LoginForm_password").tooltipster("show");	
															}
										
															//var form = document.getElementById("loginFormForAjax");
															//form.parentNode.removeChild(form);

															//setTimeout(function(){document.getElementById("loginButton").className = "btn btn-3 btn-3a icon-login1";}, 10);
														}
													}',
										),
										array('id'=>'loginAjaxButton','type'=>'submit','tabindex'=>3));									
						?>																					
						</div>

						<div id="loginOr" class="upperMenu" style="width:auto;margin-top:12px;padding-right:0px;padding-left:10px;font-size:1.6em;">
							<?php echo CHtml::label(Yii::t('layout', 'or'), false, array('style'=>'cursor:text;')); ?>
						</div>
						
						<?php
						$successCallback = 'try
											{
												var obj = jQuery.parseJSON(msg);
												
												if (obj.result)
												{
													if (obj.result == "1")
													{
														$("#tabViewList").html(obj.renderedTabView);
														$("#userarea").html(obj.renderedUserAreaView);
														$("#FriendRequestsIconLink").html(obj.renderedFriendshipRequestsView);
														$("#loginBlock").html(obj.loginSuccessfulActions);
													}
													else if (obj.result == "0")
													{
														TRACKER.showMessageDialog("'.Yii::t('site', 'An error occured during login. Please retry the process and if the error persists please contact us.').'");
													}
													else if (obj.result == "-1")
													{
														var opt = {
															autoOpen: false,
															modal: true,
															resizable: false,
															width: 600,
															title: "'.Yii::t('site', 'Accept Terms to continue').'"
														};
												
														$("#acceptTermsForFacebookLoginWindow").dialog(opt).dialog("open");
														$("#acceptTermsForFacebookLoginWindow").html(obj.renderedView);
													}
													else if (obj.result == "-2")
													{
														var opt = {
															autoOpen: false,
															modal: true,
															resizable: false,
															width: 600,
															title: "'.Yii::t('site', 'Enter Your Traceper Password to Log In').'"
														};
												
														$("#enterPasswordForOldFacebookUserToLoginWindow").dialog(opt).dialog("open");
														$("#enterPasswordForOldFacebookUserToLoginWindow").html(obj.renderedView);
													}
													else if (obj.result == "-4")
													{
														var opt = {
															autoOpen: false,
															modal: true,
															resizable: false,
															width: 600,
															title: "'.Yii::t('site', 'Do you want to switch to Facebook login permanently?').'"
														};
															
														$("#askForSwitchToFacebookLoginPermanentlyWindow").dialog(opt).dialog("open");
														$("#askForSwitchToFacebookLoginPermanentlyWindow").html(obj.renderedView);
													}
													else
													{
													
													}
												}
											}
											catch (error)
											{
											
											}';
							
						//Facebook'a JS fonksiyonunu verirken tek satirda vermeni istiyor, bu nedenle callback string'i tek satira donusturuluyor
						//Bu fonksiyonun için de // ile yorum yazinca str_replace() tek satira cevirince kodlar yorumlandigi icin calismiyor,
						//o nedenle comment koyma
						$successCallback = str_replace(array("\r", "\n"), '', $successCallback);
						?>
						
						<div id="loginLongFacebookButton" class="upperMenu" style="margin-top:20px;padding-right:0px;padding-left:15px;">
							<?php
							$this->widget('ext.yii-facebook-opengraph.plugins.LoginButton', array(
							'size'=>'large',
							'text'=>Yii::t('layout', 'Log in with facebook'),
							'scope'=>'basic_info,email', //permissions		
							'on_login'=>'(function(){ $.post("index.php?r=site/facebookLogin", function(msg){'.$successCallback.'});})()'
							//'show_faces'=>true,
							//'registration_url'=>'http://mysite/index.php/users/facebookregister',
							));						
							?>
						</div>
						
						<div id="loginShortFacebookButton" class="upperMenu" style="display:none;margin-top:20px;padding-right:0px;width:80px;padding-left:8px;">
							<?php
							$this->widget('ext.yii-facebook-opengraph.plugins.LoginButton', array(
							'size'=>'large',
							'text'=>Yii::t('layout', 'Log in'),
							'scope'=>'basic_info,email', //permissions		
							'on_login'=>'(function(){ $.post("index.php?r=site/facebookLogin", function(msg){'.$successCallback.'});})()'
							//'show_faces'=>true,
							//'registration_url'=>'http://mysite/index.php/users/facebookregister',
							));						
							?>
						</div>							

						<?php $this->endWidget(); ?>
					</div>																		

					<!-- BURADA -->
									
				</div>

				<div id="userId" style="display: none;"></div>

				<div id="userBlock"
				<?php
				$userId = "$('#userId').html()";
				if (Yii::app()->user->isGuest == true) {
					echo "style='display:none;margin-right:2%;margin-top:1%;'";
				}
				else
				{
					echo "style='margin-right:2%;margin-top:1%;'";
					$userId = Yii::app()->user->id;
					

				}  ?>>

					<div id='userarea'>
					<?php if (Yii::app()->user->isGuest == false){ ?>
						<div style="position:absolute;display:inline-block;">
						<?php

							$this->widget('ext.EAjaxUpload.EAjaxUpload',
									array(
											'id'=>'uploadProfilePhoto',
											'config'=>array(
													'action'=>Yii::app()->createUrl('users/upload'),
													'allowedExtensions'=>array("jpg", "jpeg", "png", "gif"),//array("jpg","jpeg","gif","exe","mov" and etc...
													'sizeLimit'=>1*1024*1024,// maximum file size in bytes
													'photoSrc'=>$profilePhotoSource,
													'bothPhotoExists'=>$bothPhotoExists,
													'useAjaxLink'=>$useAjaxLink,
													'uploadMenuLabel'=>Yii::t('layout', 'Upload a new Traceper profile photo and use it'),
													//'minSizeLimit'=>10*1024*1024,// minimum file size in bytes
													'onSubmit'=>"js:function(file, extension) {
																	//$('div.preview').addClass('loading');
													
															 		$('#profilePhoto').css('opacity', 0.5);
															 		$('#profilePhotoLoading').show();
																	bUploadProfilePhotoErrorExists = false;
																}",																	
													'onComplete'=>"js:function(id, fileName, responseJSON){
																		$('#profilePhotoUploadButton').removeClass('qq-upload-button-hover');
													
																		if(eProfilePhotoExists == 'NONE') //Ne Traceper ne de FB fotosu yok
																		{
																			if(typeof responseJSON['result'] != 'undefined')
																			{
																				if(responseJSON['result'] == '-1')
																				{
																					$('#uploadProfilePhotoErrorTooltip').tooltipster('update', '".Yii::t('site', 'The file you try to select appears to be damaged, corrupted, or unreadable. Please, select a proper file.')."');
																				}
																				else
																				{
																					$('#uploadProfilePhotoErrorTooltip').tooltipster('update', '".Yii::t('site', 'There occured an unknown error during upload process. Make sure that you select a proper image file. If the error persists, please contact us.')."');
																				}		
	
																				$('#profilePhotoLoading').hide();
																				$('#profilePhotoUploadButton').addClass('qq-upload-button-error-with-icon');
																				$('#profileUserIcon').addClass('profileUserIcon-error');
																				$('#uploadProfilePhoto').addClass('uploadProfilePhotoErrorForIcon');
																				$('#uploadProfilePhotoErrorTooltip').css('bottom', '28px');																 			
																				$('#uploadProfilePhotoErrorTooltip').tooltipster('show');															
																				bUploadProfilePhotoErrorExists = true;																				
																			}
																			else if(bUploadProfilePhotoErrorExists == false) //showMessage'a bir hata gelmemisse
																			{
																				var timeStamp = new Date().getTime();
																				var imageSrc = 'profilePhotos/'+responseJSON['filename']+'.png'+'?random=' + timeStamp;
													
																				$('#profilePhotoLoading').hide();
																				$('#profilePhoto').css('opacity', 1);
																				$('#profileUserIcon').hide();
																				$('#profilePhoto').attr('src', imageSrc);
																				
																				$('#profilePhoto').show();
																				$('#uploadProfilePhoto').tooltipster('update', '".Yii::t('site', 'Click here to change your profile photo')."'); 
																																																					
																				$('#uploadProfilePhotoSuccessfulTooltip').tooltipster('update', '".Yii::t('site', 'You have uploaded your profile photo successfully.')."');
																		 		$('#uploadProfilePhotoSuccessfulTooltip').tooltipster('show');

																				MAP_OPERATOR.updateMarkerImage(currentUserMarker, imageSrc, true);
																				TRACKER.users[".Yii::app()->user->id."].mapMarker[0].infoWindow.setContent(getContentFor(".Yii::app()->user->id.", imageSrc));													

																				eProfilePhotoExists = 'ONE';
																			}
																			else
																			{
																				//Show message'a hata gelmis
																			}						
																		}
																		else if(eProfilePhotoExists == 'ONE') //Ya sadece Traceper ya da sadece FB fotosu var
																		{
																			if(typeof responseJSON['result'] != 'undefined')
																			{
																				if(responseJSON['result'] == '-1')
																				{
																					$('#uploadProfilePhotoErrorTooltip').tooltipster('update', '".Yii::t('site', 'The file you try to select appears to be damaged, corrupted, or unreadable. Please, select a proper file.')."');
																				}
																				else
																				{
																					$('#uploadProfilePhotoErrorTooltip').tooltipster('update', '".Yii::t('site', 'There occured an unknown error during upload process. Make sure that you select a proper image file. If the error persists, please contact us.')."');
																				}		
	
																				$('#profilePhotoLoading').hide();																				
																				$('#profilePhotoUploadButton').addClass('qq-upload-button-error');
																				$('#profilePhoto').css('opacity', '0.7');																 			
																				$('#uploadProfilePhotoErrorTooltip').tooltipster('show');																				
																				bUploadProfilePhotoErrorExists = true;																					
																			}
																			else if(bUploadProfilePhotoErrorExists == false) //showMessage'a bir hata gelmemisse
																			{
																				//Bu case'de sadece FB fotosu varken Traceper fotosu yuklenebildigi icin ajax yeniden alinmali ki tooltip menu cikmaya baslasin sayfayi refresh etmeden
																				jQuery.ajax({
																					type: 'POST',
																					url: '".Yii::app()->createUrl('users/viewProfilePhoto', array('variablesNotDefined'=>true))."',
																					success: function(html){
																						jQuery('#userarea').html(html);".
													
																						((Yii::app()->user->fb_id == 0)?"$('#uploadProfilePhoto').tooltipster('hide'); eProfilePhotoExists = 'ONE';":"eProfilePhotoExists = 'BOTH'; $('#uploadProfilePhotoSuccessfulTooltip').css('bottom', '22px');").
																						"$('#uploadProfilePhotoSuccessfulTooltip').tooltipster('update', '".Yii::t('site', 'You have changed your profile photo successfully.')."');
																						$('#uploadProfilePhotoSuccessfulTooltip').tooltipster('show');

																						var timeStamp = new Date().getTime();													
																						var imageSrc = 'profilePhotos/'+responseJSON['filename']+'.png'+'?random=' + timeStamp;
															
																						MAP_OPERATOR.updateMarkerImage(currentUserMarker, imageSrc, true);
																						TRACKER.users[".Yii::app()->user->id."].mapMarker[0].infoWindow.setContent(getContentFor(".Yii::app()->user->id.", imageSrc));													
																					}
																				});													
																			}
																			else
																			{
																				//Show message'a hata gelmis
																			}						
																		}
																		else if(eProfilePhotoExists == 'BOTH') //Hem Traceper hem de FB fotosu var
																		{
																			if(typeof responseJSON['result'] != 'undefined')
																			{
																				closeTooltipMenu();
														
																				//$('#profilePhotoSettingsMenu').addClass('profilePhotoSettingsMenu-error');
																				$('#profilePhotoLoading').hide();
																				$('#profilePhoto').css('opacity', 0.7);
																				$('#profilePhotoSettingsMenu').css('border', '3px solid #C00');													
																				$('#profilePhotoSettingsMenu').css('left', '1px');
																				$('#profilePhotoSettingsMenu').css('bottom', '3px');
																				$('#profilePhotoSettingsMenu').css('background', '#C00');
																				$('#uploadProfilePhotoErrorTooltip').css('bottom', '28px');
	
																				if(responseJSON['result'] == '-1')
																				{
																					$('#uploadProfilePhotoErrorTooltip').tooltipster('update', '".Yii::t('site', 'The file you try to select appears to be damaged, corrupted, or unreadable. Please, select a proper file.')."');
																				}
																				else
																				{
																					$('#uploadProfilePhotoErrorTooltip').tooltipster('update', '".Yii::t('site', 'There occured an unknown error during upload process. Make sure that you select a proper image file. If the error persists, please contact us.')."');
																				}
													
																		 		$('#uploadProfilePhotoErrorTooltip').tooltipster('show');
																				bUploadProfilePhotoErrorExists = true;													
																			}
																			else if(bUploadProfilePhotoErrorExists == false) //showMessage'a bir hata gelmemisse
																			{
																				//Sorun olustugunda toolip menu acik kaldigindan ve kapatilamadigindan ayni photo yeniden ajaxla yuklenerek bu sorun giderilmeye calisiliyor
																				jQuery.ajax({
																					type: 'POST',
																					url: '".Yii::app()->createUrl('users/viewProfilePhoto')."',
																					success: function(html){
																						jQuery('#userarea').html(html);".
													
																						((Yii::app()->user->fb_id == 0)?"$('#uploadProfilePhoto').tooltipster('hide');":"").
																						"$('#uploadProfilePhotoSuccessfulTooltip').css('bottom', '22px');
																						$('#uploadProfilePhotoSuccessfulTooltip').tooltipster('update', '".Yii::t('site', 'You have changed your profile photo successfully.')."');
																						$('#uploadProfilePhotoSuccessfulTooltip').tooltipster('show');

																						var timeStamp = new Date().getTime();													
																						var imageSrc = 'profilePhotos/'+responseJSON['filename']+'.png'+'?random=' + timeStamp;
															
																						MAP_OPERATOR.updateMarkerImage(currentUserMarker, imageSrc, true);
																						TRACKER.users[".Yii::app()->user->id."].mapMarker[0].infoWindow.setContent(getContentFor(".Yii::app()->user->id.", imageSrc));													
																					}
																				});													
																			}
																			else
																			{
																				//Show message'a hata gelmis
																			}				
																		}
																		else
																		{
																			alert('Undefined eProfilePhotoExists:' + eProfilePhotoExists);
																		}
																   }",
													'messages'=>array(
													                 'typeError'=>Yii::t('site', 'The file you try to select is invalid. Please, select a file of types {extensions}.'),
													                 'sizeError'=>Yii::t('site', 'The file you try to select is too large. Please, select a file smaller than 1 MB.'),
													                 //'minSizeError'=>"{file} is too small, minimum file size is {minSizeLimit}.",
													                 'emptyError'=>Yii::t('site', 'The file you try to select is empty. Please, select a proper file.'),
													                 //'onLeave'=>"The files are being uploaded, if you leave now the upload will be cancelled."
													                ),
													'showMessage'=>"js:function(message){ 
																		//alert(message);																															
																																				
																		if(eProfilePhotoExists == 'NONE')
																		{
																			$('#profilePhotoUploadButton').removeClass('qq-upload-button-hover');
																			$('#profilePhotoUploadButton').addClass('qq-upload-button-error-with-icon');
																			$('#profileUserIcon').addClass('profileUserIcon-error');
																			$('#uploadProfilePhoto').addClass('uploadProfilePhotoErrorForIcon');
																			$('#uploadProfilePhotoErrorTooltip').css('bottom', '28px');

																			$('#uploadProfilePhotoErrorTooltip').tooltipster('update', message);
																	 		$('#uploadProfilePhotoErrorTooltip').tooltipster('show');
																			bUploadProfilePhotoErrorExists = true;													
																		}
																		else if(eProfilePhotoExists == 'ONE')
																		{
																			$('#profilePhotoUploadButton').removeClass('qq-upload-button-hover');
																			$('#profilePhotoUploadButton').addClass('qq-upload-button-error');
																			$('#profilePhoto').css('opacity', '0.7');

																			$('#uploadProfilePhotoErrorTooltip').tooltipster('update', message);
																	 		$('#uploadProfilePhotoErrorTooltip').tooltipster('show');
																			bUploadProfilePhotoErrorExists = true;
																		}
																		else if(eProfilePhotoExists == 'BOTH')
																		{
																			closeTooltipMenu();												
													
																			//$('#profilePhotoSettingsMenu').addClass('profilePhotoSettingsMenu-error');
																			$('#profilePhoto').css('opacity', 0.7);
																			$('#profilePhotoSettingsMenu').css('border', '3px solid #C00');
																			$('#profilePhotoSettingsMenu').css('left', '1px');
																			$('#profilePhotoSettingsMenu').css('bottom', '3px');
																			$('#profilePhotoSettingsMenu').css('background', '#C00');
																			$('#uploadProfilePhotoErrorTooltip').css('bottom', '28px');

																			$('#uploadProfilePhotoErrorTooltip').tooltipster('update', message);
																	 		$('#uploadProfilePhotoErrorTooltip').tooltipster('show');
																			bUploadProfilePhotoErrorExists = true;																			
																		}
																		else
																		{
																			alert('Undefined eProfilePhotoExists: ' + eProfilePhotoExists);
																		}													
																	}"
											),
									));

							echo CHtml::label('', '#',
									array(
											'id'=>'uploadProfilePhotoErrorTooltip',
											'style'=>'pointer-events:none; position:absolute; left:3px; bottom:4px;'
									));

							echo CHtml::label('', '#',
									array(
											'id'=>'uploadProfilePhotoSuccessfulTooltip',
											'style'=>'pointer-events:none; position: absolute; bottom:4px;'
									));
						?>
						</div>

						<div id="userNameLink" style="display:inline-block;">
						<?php						
							echo CHtml::link(Yii::app()->user->name, "#", array('class'=>'vtip', 'onclick'=>'TRACKER.trackUser('.$userId.')', 'title'=>Yii::t('layout', 'See your position on the map'),
																				'style'=>'position:relative; left:54px; top:35px;'));
						?>
						</div>

						<!-- Yusuf'un FB fotosu: https://graph.facebook.com/100000325092362/picture?type=square -->
					<?php								
					}
					?>									
					</div>

					<div class="hi-icon-effect-1 hi-icon-effect-1a userOperations">	
					<?php
					echo CHtml::link('Signout', $this->createUrl('site/logout'), 
							array('class'=>'vtip', 'title'=>Yii::t('layout', 'Sign Out'),
								  'class'=>'hi-icon icon-exit'
								 ));
					?>
					</div>
					
					<div class="hi-icon-effect-1 hi-icon-effect-1a userOperations">						
					<?php					
// 					echo CHtml::ajaxLink('Change Password', $this->createUrl('site/changePassword'),
// 							array(
// 									'complete'=> 'function() { $("#changePasswordWindow").dialog("open"); return false;}',
// 									'update'=> '#changePasswordWindow',
// 							),
// 							array(
// 									'id'=>'showChangePasswordWindow','class'=>'vtip', 'title'=>Yii::t('layout', 'Change Password'),
// 									'class'=>'hi-icon icon-key'
// 									));
									
					echo CHtml::ajaxLink('Change Password', $this->createUrl('site/changePassword'),
							array(
									'success'=> 'function(msg) 
												 {
													if(msg == "Login Required")
													{
														location.reload();
													}
													else
													{
														$("#changePasswordWindow").html(msg);
														$("#changePasswordWindow").dialog("open");
													}
												 }'
							),
							array(
									'id'=>'showChangePasswordWindow','class'=>'vtip', 'title'=>Yii::t('layout', 'Change Password'),
									'class'=>'hi-icon icon-key'
							));					
					?>
					</div>
					
					<?php
					if(Yii::app()->params->featureFriendManagementEnabled)
					{						
						?>																	
						<div class="hi-icon-effect-1 hi-icon-effect-1a userOperations">						
						<?php						
						
						echo CHtml::ajaxLink('Invite Users', $this->createUrl('site/inviteUsers'),
								array(
										//'complete'=> 'function() { $("#inviteUsersWindow").dialog("open"); return false;}',
										//'update'=> '#inviteUsersWindow',
										
										'success'=> 'function(msg)
													 {
														if(msg == "Login Required")
														{
															location.reload();
														}
														else
														{
															$("#inviteUsersWindow").html(msg);
															$("#inviteUsersWindow").dialog("open");
														}
													 }'										
								),
								array(
										'id'=>'showInviteUsersWindow','class'=>'vtip', 'title'=>Yii::t('layout', 'Invite Your Friends to Traceper'),
										'class'=>'hi-icon icon-inviteUsers'
									 ));
						?>
						</div>
						
						<?php

						$newRequestsCount = null;
						$totalRequestsCount = null;
						
						if (Yii::app()->user->isGuest == false)
						{
							Friends::model()->getFriendRequestsInfo(Yii::app()->user->id, $newRequestsCount, $totalRequestsCount);
						}
						else
						{
							//Bir kullanıcı ID'si olmadığından sorgu yapma
						}
						
						//Fb::warn("newRequestsCount:$newRequestsCount - totalRequestsCount:$totalRequestsCount", "main");

						if($newRequestsCount > 0)
						{	
							if($newRequestsCount <= 5)
							{
								$friendReqTooltip = Yii::t('users', 'Friendship Requests').' ('.$newRequestsCount.' '.Yii::t('users', 'new').(($totalRequestsCount > $newRequestsCount)?Yii::t('users', ' totally ').$totalRequestsCount:'').Yii::t('users', ' friendship request(s) you have').')';
							}
							else
							{
								$friendReqTooltip = Yii::t('users', 'Friendship Requests').' ('.$newRequestsCount.' '.Yii::t('users', 'new').(($totalRequestsCount > $newRequestsCount)?Yii::t('users', ' totally ').$totalRequestsCount:'').Yii::t('users', ' friendship request(s) you have').')';
							}
						}
						else
						{
							$friendReqTooltip = Yii::t('users', 'Friendship Requests');
						}						
						?>
											
						<div id="FriendRequestsIconLink" class="hi-icon-effect-1 hi-icon-effect-1a userOperations" style="position: relative;"> <!-- Friend req sayisini buna gore konumlandırmak icin parent'in da position ozelligi set edilmeli  -->						
							<?php
							if($newRequestsCount > 0)
							{
								echo CHtml::ajaxLink($newRequestsCount, $this->createUrl('users/GetFriendRequestList'),
										array(
												//'complete'=> 'function() { $("#friendReqCount").hide(); $("#friendRequestsWindow").dialog("open"); return false;}',
												//'update'=> '#friendRequestsWindow',
												
												'success'=> 'function(msg)
															 {
																if(msg == "Login Required")
																{
																	location.reload();
																}
																else
																{
																	$("#friendRequestsWindow").html(msg);
																	$("#friendReqCount").hide();
																	$("#friendRequestsWindow").dialog("open");
																}
															 }'												
										),
										array(
												'id'=>'friendReqCount','class'=>'vtip', 'title'=>$friendReqTooltip,
												'class'=>'friendRequestCount',
												'onMouseOver' => "$('#friendReqCount').css('background-color', '#F75D59');",
												'onMouseOut' => "$('#friendReqCount').css('background-color', '#F62217');",
										));
							}							
							
							echo CHtml::ajaxLink('Friend Requests', $this->createUrl('users/GetFriendRequestList'),
									array(
											//'complete'=> 'function() { $("#friendReqCount").hide(); $("#friendRequestsWindow").dialog("open"); return false;}',
											//'update'=> '#friendRequestsWindow',
											
											'success'=> 'function(msg)
														 {
															if(msg == "Login Required")
															{
																location.reload();
															}
															else
															{
																$("#friendRequestsWindow").html(msg);
																$("#friendReqCount").hide();
																$("#friendRequestsWindow").dialog("open");
															}
														 }'											
									),
									array(
											'id'=>'showFriendRequestsWindow','class'=>'vtip', 'title'=>$friendReqTooltip,
											'class'=>'hi-icon icon-mail',
											'onMouseOver' => "$('#friendReqCount').css('background-color', '#F75D59');",
											'onMouseOut' => "$('#friendReqCount').css('background-color', '#F62217');",											
									));							
							?>
						</div>	
					<?php
					}

// 					echo CHtml::ajaxLink('<div class="userOperations" id="createGroup">
// 							<img id="createGroupImg" src="images/createGroup.png"  /><div></div>
// 							</div>', $this->createUrl('groups/createGroup'),
// 							array(
// 									'complete'=> 'function() { $("#createGroupWindow").dialog("open"); return false;}',
// 									'update'=> '#createGroupWindow',
// 							),
// 							array(
// 									'id'=>'showCreateGroupWindow'/*,'class'=>'vtip', 'title'=>Yii::t('layout', 'Create New Group')*/,
// 									'onMouseOver' => "document.createGroupImg.src = 'images/createGroup_hover.png';",
// 									'onMouseOut' => "document.createGroupImg.src = 'images/createGroup.png';",
// 									));
					
					?>

					<div class="hi-icon-effect-1 hi-icon-effect-1a userOperations">
					<?php
					echo CHtml::ajaxLink('Create Group', $this->createUrl('groups/createGroup'),
							array(
									//'complete'=> 'function() { $("#createGroupWindow").dialog("open"); return false;}',
									//'update'=> '#createGroupWindow',
									
									'success'=> 'function(msg)
												 {
													if(msg == "Login Required")
													{
														location.reload();
													}
													else
													{
														$("#createGroupWindow").html(msg);
														$("#createGroupWindow").dialog("open");
													}
												 }'									
							),
							array(
									'id'=>'showCreateGroupWindow'/*,'class'=>'vtip', 'title'=>Yii::t('layout', 'Create New Group')*/,
									'class'=>'hi-icon icon-users'
							));					
					?>
					</div>
								
					<?php
					if(Yii::app()->user->id == 160)
					{
					?>
						<div class="hi-icon-effect-1 hi-icon-effect-1a userOperations">
						<?php
						echo CHtml::ajaxLink('Database Operations', $this->createUrl('site/runDatabaseQueries'),
								array(
										//'complete'=> 'function() { $("#createGroupWindow").dialog("open"); return false;}',
										//'update'=> '#createGroupWindow',
										
										'success'=> 'function(msg)
													 {
														if(msg == "Login Required")
														{
															location.reload();
														}
														else
														{
															$("#databaseOperationsWindow").html(msg);
															$("#databaseOperationsWindow").dialog("open");
														}
													 }'									
								),
								array(
										'id'=>'showDatabaseOperationsWindow', 'class'=>'vtip', 'title'=>Yii::t('layout', 'Database Operations'),
										'class'=>'hi-icon icon-plus'
								));					
						?>
						</div>
					<?php
					}
					?>											

					<?php					
											
					if(Yii::app()->params->featureGPSDeviceEnabled)
					{
						echo CHtml::ajaxLink('<div class="userOperations" id="registerGPSTracker">
								<img src="images/registerGPSTracker.png"  /><div></div>
								</div>', $this->createUrl('site/registerGPSTracker'),
								array(
										'complete'=> 'function() { $("#registerGPSTrackerWindow").dialog("open"); return false;}',
										'update'=> '#registerGPSTrackerWindow',
								),
								array(
										'id'=>'showRegisterGPSTrackerWindow','class'=>'vtip', 'title'=>Yii::t('layout', 'Register GPS Tracker')));						
					}

					if(Yii::app()->params->featureStaffManagementEnabled)
					{
						echo CHtml::ajaxLink('<div class="userOperations" id="registerNewStaff">
								<img src="images/add_as_friend.png"  /><div></div>
								</div>', $this->createUrl('site/registerNewStaff'),
								array(
										'complete'=> 'function() { $("#registerNewStaffWindow").dialog("open"); return false;}',
										'update'=> '#registerNewStaffWindow',
								),
								array(
										'id'=>'showRegisterNewStaffWindow','class'=>'vtip', 'title'=>Yii::t('layout', 'Register New Staff')));

					}
					
					/*
					echo CHtml::ajaxLink('<div class="userOperations" id="showGeofence">
							<img src="images/userGeofences.png"  /><div></div>
							</div>', $this->createUrl('geofence/getGeofences'),
							array(
									'success'=> 'function(result){
									try {
									var obj = jQuery.parseJSON(result);

									if (obj.count > 0)
									{
									for(var i=0; i<obj.count; i++) {
									if (typeof TRACKER.geofences[obj.dataProvider[i].id] == "undefined")
									{
									var poly = mapOperator.initializePolygon();
									var mapStruct = new MapStruct();
									geoFence = new MapStruct.GeoFence({geoFenceId:obj.dataProvider[i].id,listener:null,polygon:poly});

									var Loc1 = new MapStruct.Location({latitude:obj.dataProvider[i].Point1Latitude,
									longitude:obj.dataProvider[i].Point1Longitude});
									mapOperator.addPointToGeoFence(geoFence,Loc1);

									var Loc2 = new MapStruct.Location({latitude:obj.dataProvider[i].Point2Latitude,
									longitude:obj.dataProvider[i].Point2Longitude});
									mapOperator.addPointToGeoFence(geoFence,Loc2);

									var Loc3 = new MapStruct.Location({latitude:obj.dataProvider[i].Point3Latitude,
									longitude:obj.dataProvider[i].Point3Longitude});
									mapOperator.addPointToGeoFence(geoFence,Loc3);

									TRACKER.geofences[obj.dataProvider[i].id] = geoFence;
}

									mapOperator.setGeoFenceVisibility(TRACKER.geofences[obj.dataProvider[i].id],true);
}
}
									else
									{
									TRACKER.showMessageDialog("There is no geofence");
}
}
									catch (error){
}
}',
							),
							array(
									'id'=>'showGeofenceWindow','class'=>'vtip', 'title'=>Yii::t('layout', 'Show Geofences')));

					echo CHtml::ajaxLink('<div class="userOperations" id="hideGeofence">
							<img src="images/hideGeofences.png"  /><div></div>
							</div>', $this->createUrl('geofence/getGeofences'),
							array(
									'success'=> 'function(result){
									try {
									var obj = jQuery.parseJSON(result);

									if (obj.count > 0)
									{
									for(var i=0; i<obj.count; i++) {
									if (typeof TRACKER.geofences[obj.dataProvider[i].id] == "undefined")
									{
}

									mapOperator.setGeoFenceVisibility(TRACKER.geofences[obj.dataProvider[i].id],false);
}
}
									else
									{
}
}
									catch (error){
}
}',
							),
							array(
									'id'=>'hideGeofenceWindow','class'=>'vtip', 'title'=>Yii::t('layout', 'Hide Geofences')));

					echo CHtml::link('<div  class="userOperations" id="geoFence">
							<img src="images/geoFence.png"  /><div></div>
							</div>', '#', array(
									'onclick'=>'var mapStruct = new MapStruct();
									var geoFence_ = mapOperator.newGeofence;
									if (geoFence_==null)
									{
									var polygon = mapOperator.initializePolygon();
									geoFence_ = new MapStruct.GeoFence({geoFenceId:1,listener:null,polygon:polygon});
}
									if (geoFence_.listener == null)
									{
									TRACKER.showInfoBar("Select 3 points to generate a Geofence");
}
									else
									{
									TRACKER.showInfoBar("Geofence points selection disabled");
}
									var openDialog = mapOperator.initializeGeoFenceControl(geoFence_,createGeofenceForm);
									return false;', 'class'=>'vtip', 'title'=>Yii::t('layout', 'Create Geo-Fence'),
							));
					
					*/
					?>
				</div>
			</div>
		</div>
		
		<div id='sideBar'>
			<div id='content'>
				<div id='formContent'
				<?php
				if (Yii::app()->user->isGuest == false) {
					echo "style='display:none'";
				}
				else {
					echo "style='height:100%;'";
				}				
				?>>					 							
					<div id="registerBlock"
					<?php
					if (($passwordResetRequestStatus == PasswordResetStatus::RequestInvalid) || ($passwordResetRequestStatus == PasswordResetStatus::RequestValid)) {
						echo "style='display:none'";
					}
					else {
						echo "style='height:85%;min-height:420px;padding-top:10px;'";
					}				
					?>>										
							<div class="form" style='height:100%;'>
								<?php
								$form=$this->beginWidget('CActiveForm', array(
										'id'=>'register-form-main',
										'enableClientValidation'=>true,
										'clientOptions'=> array(
												'validateOnSubmit'=> true,
												'validateOnChange'=>false,
										),									
										'htmlOptions'=>array('style'=>'height:100%;'),
								));
		
								$model = new RegisterForm;
								?>		
														
								<div class="sideMenu">
									<div style="position:relative;display:inline-block;bottom:6px;">									
										<?php echo CHtml::label("", "#", array("class"=>"hi-icon-in-list icon-profile", "style"=>"color:#555555; cursor:default;")); ?>
									</div>
									
									<div style="position:relative;left:0em;bottom:6px;display:inline-block;font-size:2.5em;">									
										<?php echo $form->labelEx($model, 'register', array('style'=>'cursor:text;')); ?>
									</div>
									
									<div id="showPublicPhotosLink" class="hi-icon-effect-5 hi-icon-effect-5b" style="position:absolute;left:305px;bottom:6px;display:inline-block;z-index:200;">						
									<?php					
									echo CHtml::ajaxLink("<div id=\"publicPhotosCamera\" class=\"secondIcon icon-camera\"></div><div class=\"sliding-icon icon-group\"></div>", $this->createUrl('upload/getPublicList', array('fileType'=>0)),
											array(
													'update'=> '#publicUploads',
													'complete'=> 'function()
													{
														uploadsGridViewId = \'publicUploadListView\';
														$("#formContent").fadeToggle( "slow", function(){ hideRegisterFormErrorsIfExist(); hideResetPasswordFormErrorsIfExist(); $("#showPublicPhotosLink").hide(); $("#showCachedPublicPhotosLink").css("display", "inline-block"); $("#publicUploadsContent").show();});
													}',
											),
											array(
													'id'=>'publicUploadsAjaxLink-'.uniqid(),
													'onMouseOver' => '$("#publicPhotosCamera").animate({color: "#D1D0CE"}, 200);',
													'onMouseOut' => '$("#publicPhotosCamera").animate({color: "#848482"}, 200);',
											));									
									?>
									</div>

									<div id="showCachedPublicPhotosLink" class="hi-icon-effect-5 hi-icon-effect-5b" style="position:absolute;left:305px;bottom:6px;display:none;z-index:200;">
									<?php 	
									echo CHtml::label("<div id=\"publicPhotosCameraCached\" class=\"secondIcon icon-camera\"></div><div class=\"sliding-icon icon-group\"></div>", "#",
											array(
													'id'=>'publicUploadsAjaxLink-'.uniqid(),
													'onclick'=>'hideRegisterFormErrorsIfExist(); hideResetPasswordFormErrorsIfExist(); $("#publicUploadsContent").show();',
													'onMouseOver' => '$("#publicPhotosCameraCached").animate({color: "#D1D0CE"}, 200);',
													'onMouseOut' => '$("#publicPhotosCameraCached").animate({color: "#848482"}, 200);',
											));	
									?>		
									</div>																		
								</div>
								
								<div id="forRegisterRefresh">
								<div class="sideMenu">
									<div style="position:absolute;display:inline-block;vertical-align:top;width:49%;">
									<?php //echo $form->labelEx($model,'name'); ?>
									<?php echo $form->textField($model,'name', array('size'=>'22%','maxlength'=>128,'tabindex'=>7,'placeholder'=>Yii::t('site', 'First Name'),'class'=>'registerFormField','style'=>'width:145px;')); ?>																				 											 
									<?php 
		// 								    $errorMessage = $form->error($model,'name');  
		// 									if (strip_tags($errorMessage) == '') {
		// 										echo '<div class="errorMessage">&nbsp;</div>';
		// 									}
		// 									else { echo '<div class="errorMessage" style="font-size: 1.1em;">'.$errorMessage.'</div>';
		// 									}
										?>
									</div>
									
									<div style="position:absolute;left:13.6em;display:inline-block;vertical-align:top;width:49%;">
									<?php //echo $form->labelEx($model,'lastName'); ?>
									<?php echo $form->textField($model,'lastName', array('size'=>'22%','maxlength'=>128,'tabindex'=>8,'placeholder'=>Yii::t('site', 'Last Name'),'class'=>'registerFormField','style'=>'width:145px;')); ?>
									<?php 
		// 									$errorMessage = $form->error($model,'lastName');  
		// 									if (strip_tags($errorMessage) == '') {
		// 										echo '<div class="errorMessage">&nbsp;</div>';
		// 									}
		// 									else { 
		// 										echo '<div class="errorMessage" style="font-size: 1.1em;">'.$errorMessage.'</div>';										
		// 									}
										?>
									</div>																
								</div>							
								
								<div class="sideMenu">
									<?php //echo $form->labelEx($model,'email'); ?>
									<?php echo $form->textField($model,'email', array('size'=>'50%','maxlength'=>128,'tabindex'=>9,'placeholder'=>Yii::t('site', 'Your E-mail Address'),'class'=>'registerFormField','style'=>'width:324px;')); ?>
									<?php 
		// 									$errorMessage = $form->error($model,'email'); 
		// 									if (strip_tags($errorMessage) == '') {
		// 										echo '<div class="errorMessage">&nbsp;</div>';
		// 									}
		// 									else { echo '<div class="errorMessage" style="font-size: 1.1em;">'.$errorMessage.'</div>';
		// 									}
										?>								
								</div>
		
								<div class="sideMenu">
									<?php //echo $form->labelEx($model,'emailAgain'); ?>
									<?php echo $form->textField($model,'emailAgain', array('size'=>'50%','maxlength'=>128,'tabindex'=>10,'placeholder'=>Yii::t('site', 'Your E-mail Address (Again)'),'class'=>'registerFormField','style'=>'width:324px;')); ?>
									<?php 
		// 									$errorMessage = $form->error($model,'emailAgain'); 
		// 									if (strip_tags($errorMessage) == '') {
		// 										echo '<div class="errorMessage">&nbsp;</div>';
		// 									}
		// 									else { echo '<div class="errorMessage" style="font-size: 1.1em;">'.$errorMessage.'</div>';
		// 									}
										?>								
								</div>							
		
								<div class="sideMenu">
									<div style="position:absolute;display:inline-block;vertical-align:top;width:49%;">
									<?php //echo $form->labelEx($model,'password'); ?>
									<?php echo $form->passwordField($model,'password', array('size'=>'22%','maxlength'=>128,'tabindex'=>11,'placeholder'=>Yii::t('site', 'Password'),'class'=>'registerFormField','style'=>'width:145px;')); ?>
									<?php 
		// 									$errorMessage = $form->error($model,'password');
		// 									if (strip_tags($errorMessage) == '') {
		// 										echo '<div class="errorMessage">&nbsp;</div>';
		// 									}
		// 									else { echo '<div class="errorMessage" style="font-size: 1.1em;">'.$errorMessage.'</div>';
		// 									}
										?>
									</div>
									
									<div style="position:absolute;left:13.6em;display:inline-block;vertical-align:top;width:49%;">
									<?php //echo $form->labelEx($model,'passwordAgain'); ?>
									<?php echo $form->passwordField($model,'passwordAgain', array('size'=>'22%','maxlength'=>128,'tabindex'=>12,'placeholder'=>Yii::t('site', 'Password (Again)'),'class'=>'registerFormField','style'=>'width:145px;')); ?>
									<?php 
		// 									$errorMessage = $form->error($model,'passwordAgain');
		// 									if (strip_tags($errorMessage) == '') 
		// 									{
		// 										echo '<div class="errorMessage">&nbsp;</div>';
		// 									}
		// 									else { echo '<div class="errorMessage" style="font-size: 1.1em;">'.$errorMessage.'</div>';
		// 									}
										?>
									</div>																
								</div>
								</div>
								
								<div id="registerFormTermsText" class="sideMenu" style="height:25px;font-size:12px;margin-left:0.3em;cursor:text;">
								<?php	
									echo Yii::t('layout', 'By sending the Sign Up form, you agree to our {terms of use}', array('{terms of use}'=>
											CHtml::ajaxLink(Yii::t('layout', 'Terms of Use'), $this->createUrl('site/terms'),
													array(
															'complete'=> 'function() { $("#termsWindow").dialog("open"); return false;}',
															'update'=> '#termsWindow',
													),
													array(
															'id'=>'showTermsWindowAtRegisterForm','tabindex'=>15))
									));
								?>
								</div>							
								
								<div class="sideMenu">
									<div style="position:absolute;display:inline-block;vertical-align:top;width:50%;">
									<?php																
		// 								$this->widget('zii.widgets.jui.CJuiButton', array(
		// 										'name'=>'ajaxRegister',
		// 										'caption'=>Yii::t('site', 'Sign Up'),
		// 										'id'=>'registerAjaxButton',
		// 										'htmlOptions'=>array('type'=>'submit','onmouseover'=>'this.style.cursor="none";','onmouseout'=>'this.style.cursor="default";','ajax'=>array('type'=>'POST','url'=>array('site/register'),
		// 																'success'=> 'function(msg){
		// 																			try
		// 																			{																								
		// 																				var obj = jQuery.parseJSON(msg);
																							
		// 																				if (obj.result)
		// 																				{
		// 																					if (obj.result == "1") 
		// 																					{
		// 																						TRACKER.showLongMessageDialog("'.Yii::t('site', 'Your account created successfully. ').Yii::t('site', 'We have sent an account activation link to your mail address \"<b>').'" + obj.email + "'.Yii::t('site', '</b>\". </br></br>Please make sure you check the spam/junk folder as well. The links in a spam/junk folder may not work sometimes; so if you face such a case, mark our e-mail as \"Not Spam\" and reclick the link.').'");
		// 																					}
		// 																					else if (obj.result == "2")
		// 																					{
		// 																						TRACKER.showLongMessageDialog("'.Yii::t('site', 'Your account created successfully, but an error occured while sending your account activation e-mail. You could request your activation e-mail by clicking the link \"Not Received Our Activation E-Mail?\" just below the register form. If the error persists, please contact us about the problem.').'");
		// 																					}
		// 																					else if (obj.result == "0")
		// 																					{
		// 																						TRACKER.showMessageDialog("'.Yii::t('common', 'Sorry, an error occured in operation').'");																									
		// 																					}																													
		// 																				}
		// 																			}
		// 																			catch (error)
		// 																			{																																											
		// 																				$("#forRegisterRefresh").html(msg);
		
		// 																				//alert(msg);
		// 																			}																															
		// 																}',
		// 										))																		
		// 								));
										
// 										echo CHtml::imageButton('http://'.Yii::app()->request->getServerName().Yii::app()->request->getBaseUrl().'/images/signup_button_default_'.Yii::app()->language.'.png',
// 												array('id'=>'registerButton', 'type'=>'submit', 'style'=>'margin-top:0px;cursor:pointer;', 'ajax'=>array('type'=>'POST','url'=>array('site/register'),
// 														'success'=> 'function(msg){
// 																		try
// 																		{
// 																			var obj = jQuery.parseJSON(msg);
																			
// 																			if (obj.result)
// 																			{
// 																				$("#forRegisterRefresh").html(obj.registerView);
														
// 																				if (obj.result == "1")
// 																				{
// 																					TRACKER.showLongMessageDialog("'.Yii::t('site', 'Your account created successfully. ').Yii::t('site', 'We have sent an account activation link to your mail address \"<b>').'" + obj.email + "'.Yii::t('site', '</b>\". </br></br>Please make sure you check the spam/junk folder as well. The links in a spam/junk folder may not work sometimes; so if you face such a case, mark our e-mail as \"Not Spam\" and reclick the link.').'");
// 																				}
// 																				else if (obj.result == "2")
// 																				{
// 																					TRACKER.showLongMessageDialog("'.Yii::t('site', 'Your account created successfully, but an error occured while sending your account activation e-mail. You could request your activation e-mail by clicking the link \"Not Received Our Activation E-Mail?\" just below the register form. If the error persists, please contact us about the problem.').'");
// 																				}
// 																				else if (obj.result == "0")
// 																				{
// 																					TRACKER.showMessageDialog("'.Yii::t('common', 'Sorry, an error occured in operation').'");
// 																				}
// 																			}
// 																		}
// 																		catch (error)
// 																		{
// 																			$("#forRegisterRefresh").html(msg);
														
// 																			//alert("Deneme");
// 																		}
// 																	}',
// 												),'onmouseover'=>'this.src="images/signup_button_mouseover_'.Yii::app()->language.'.png";',
// 														'onmouseout'=>'this.src="images/signup_button_default_'.Yii::app()->language.'.png";$("#registerButton").css("margin-top", "0px");',
// 														'onmousedown'=>'$("#registerButton").css("margin-top", "2px");',
// 														'onmouseup'=>'$("#registerButton").css("margin-top", "0px");',
// 												));

									//Yii butonları <buton> tag'i ile uretmedigi icin boyle yapildi, bu css'ler Yii'nin urettigi <input> ile calismiyor
									echo CHtml::ajaxLink('<button class="btn btn-green btn-green-a icon-signup" style="font-size:20px;'.(($app->language == 'en')?'padding-left:55px;padding-right:15px;':'').'">'.Yii::t('site', 'Sign Up').'</button>', $this->createUrl('site/register'),
											array(
													'type'=>'POST',
													'success'=> 'function(msg){
																	try
																	{
																		var obj = jQuery.parseJSON(msg);
																		
																		if (obj.result)
																		{
																			$("#forRegisterRefresh").html(obj.registerView);
																	
																			if (obj.result == "1")
																			{
																				TRACKER.showLongMessageDialog("'.Yii::t('site', 'Your account created successfully. ').Yii::t('site', 'We have sent an account activation link to your mail address \"<b>').'" + obj.email + "'.Yii::t('site', '</b>\". </br></br>Please make sure you check the spam/junk folder as well. The links in a spam/junk folder may not work sometimes; so if you face such a case, mark our e-mail as \"Not Spam\" and reclick the link.').'", "'.Yii::app()->homeUrl.'");
																			}
																			else if (obj.result == "2")
																			{
																				TRACKER.showLongMessageDialog("'.Yii::t('site', 'Your account created successfully, but an error occured while sending your account activation e-mail. You could request your activation e-mail by clicking the link \"Not Received Our Activation E-Mail?\" just below the register form. If the error persists, please contact us about the problem.').'", "'.Yii::app()->homeUrl.'");
																			}
																			else if (obj.result == "0")
																			{
																				TRACKER.showMessageDialog("'.Yii::t('site', 'An error occured during sign up. Please retry the process and if the error persists please contact us.').'");
																			}
																			else if (obj.result == "-1")
																			{
																				var opt = {
																					autoOpen: false,
																					modal: true,
																					resizable: false,
																					width: 600,
																					title: "'.Yii::t('site', 'Your E-mail Seems To Be Invalid').'"
																				};
																		
																				$("#yourEmailSeemsToBeInvalidWindow").dialog(opt).dialog("open");
																				$("#yourEmailSeemsToBeInvalidWindow").html(obj.renderedView);																				
																			}													
																			else
																			{
																				TRACKER.showMessageDialog("'.Yii::t('site', 'An error occured during sign up. Please retry the process and if the error persists please contact us.').'");
																			}																										
																		}
																	}
																	catch (error)
																	{
																		$("#hiddenAjaxResponseToParse").html(msg);																		
																		//alert(document.getElementById("ajaxRegisterResponse").innerHTML);
																		$("#forRegisterRefresh").html(document.getElementById("ajaxRegisterResponse").innerHTML);																		
																		$("#hiddenAjaxResponseToParse").html("");
																		$("input, textarea").placeholder(); //Placeholder desteklemeyen browserlar icin	

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
													
// 																		$("#forRegisterRefresh").html(msg);
// 																		setTimeout(function(){document.getElementById("registerButton").className = "btn btn-green btn-green-a icon-signup";}, 10);
																																																
																		//alert("Deneme");
																	}
																}',
											),
											array('id'=>'registerAjaxButton','type'=>'submit','tabindex'=>16));									
										?>
									</div>

									<div style="position:absolute;left:176px;;top:1.2em;display:inline-block;vertical-align:top;width:50%;">
									<?php
									echo CHtml::ajaxLink('<div id="activationNotReceived">'.Yii::t('site', 'Not Received Our Activation E-Mail?').
														'</div>', $this->createUrl('site/activationNotReceived'),
											array(
													'complete'=> 'function() { $("#activationNotReceivedWindow").dialog("open"); return false;}',
													'update'=> '#activationNotReceivedWindow',
											),
											array(
													'id'=>'activationNotReceivedLink', //Main'de henuz ajax sorgusu yapilmadigindan unique ID vermeye gerek yok
													'tabindex'=>14));							
									?>
									</div>
								</div>																
																						
								<?php $this->endWidget(); ?>
							</div>													
					</div>
					
					<script type="text/javascript">		
					checkSVGElements("mainCustomForm");	
					</script>					
					
				
				
<!-- VIDEO WORK	 -->
								
<!-- 				<video id="my_video_1" class="video-js vjs-default-skin" controls preload="auto" width="320" height="264" poster="my_video_poster.png" data-setup="{}"> -->
<!-- 				  <source src="http://localhost/traceper/branches/DevWebInterface/upload/oceans-clip.mp4" type='video/mp4'> -->
<!-- 				  <source src="http://localhost/traceper/branches/DevWebInterface/upload/14.flv" type='video/flv'> -->
<!-- 				</video>				 -->
				
				
					<div id="passwordResetBlock"
					<?php
					if (($passwordResetRequestStatus == PasswordResetStatus::NoRequest) || ($passwordResetRequestStatus == PasswordResetStatus::RequestInvalid)) {
						echo "style='display:none'";
					}                                     
					?>>						
						
						<div class="form">
							<?php
							$form=$this->beginWidget('CActiveForm', array(
									'id'=>'passwordReset-form-main',
									'enableClientValidation'=>true,
							));

							$model = new ResetPasswordForm;
							?>							
							<div id="forPasswordResetRefresh">
								<div style="padding:9%;font-size:3em;">
									<?php echo $form->labelEx($model, 'resetPassword', array('style'=>'cursor:text;')); ?>
								</div>							
							
								<div class="sideMenu" style="margin-left:2em;">
									<?php echo $form->labelEx($model,'newPassword'); ?>
									<?php echo $form->passwordField($model,'newPassword', array('size'=>'30%','maxlength'=>128,'tabindex'=>7)); ?>
								</div>

								<div class="sideMenu" style="margin-left:2em;padding-top:20px;">
									<?php echo $form->labelEx($model,'newPasswordAgain'); ?>
									<?php echo $form->passwordField($model,'newPasswordAgain', array('size'=>'30%','maxlength'=>128,'tabindex'=>8)); ?>
								</div>
							</div>	

							<div class="sideMenu" style="margin-left:2em;padding-top:30px;">
								<?php
								//Yii butonları <buton> tag'i ile uretmedigi icin boyle yapildi, bu css'ler Yii'nin urettigi <input> ile calismiyor
								echo CHtml::ajaxLink('<button class="btn btn-3 btn-3a icon-checkmark" style="'.(($app->language == 'en')?'padding-left:65px;padding-right:25px;':'padding-left:50px').'">'.Yii::t('site', 'Update').'</button>', $this->createUrl('site/resetPassword', array('token'=>$token)),
										array(
												'type'=>'POST',
												'success'=> 'function(msg){
																try
																{
																	var obj = jQuery.parseJSON(msg);
																	
																	if (obj.result)
																	{
																		//$("#forPasswordResetRefresh").html(obj.resetPaswordView);
																		//Yukaridaki gibi yapinca login error varken mesaj cikip geri kapandiginda errorlar tekrar gosterilmiyor. Muhtemelen yeniden dosya yukleme sonucu tooltipser bozuluyor
																		resetResetPasswordFormErrors();
																
																		if (obj.result == "1")
																		{
																			//TRACKER.showLongMessageDialog("'.Yii::t('site', 'Your account created successfully. ').Yii::t('site', 'We have sent an account activation link to your mail address \"<b>').'" + obj.email + "'.Yii::t('site', '</b>\". </br></br>Please make sure you check the spam/junk folder as well. The links in a spam/junk folder may not work sometimes; so if you face such a case, mark our e-mail as \"Not Spam\" and reclick the link.').'");
																	
																			TRACKER.showMessageDialog("'.Yii::t('site', 'Your password has been changed successfully, you can login now...').'");
																			$("#passwordResetBlock").hide();
																			$("#registerBlock").css("height", "85%");
																			$("#registerBlock").css("min-height", "420px");
																			$("#registerBlock").load();
																			$("#registerBlock").show();
																		}
																		else if (obj.result == "0")
																		{
																			TRACKER.showMessageDialog("'.Yii::t('site', 'An error occured while changing your password!').'");
																		}
																	}
																}
																catch (error)
																{
																	$("#forPasswordResetRefresh").html(msg);
												
																	$("#hiddenAjaxResponseToParse").html(msg);
																	//alert(document.getElementById("ajaxPasswordResetResponse").innerHTML);
																	$("#forPasswordResetRefresh").html(document.getElementById("ajaxPasswordResetResponse").innerHTML);
																	$("#hiddenAjaxResponseToParse").html("");												
																}
															}',
										),
										array('id'=>'resetPasswordAjaxButton','type'=>'submit','tabindex'=>9));								
								?>
							</div>

							<?php $this->endWidget(); ?>
						</div>						
					</div>
												
					<div id="passwordResetInvalidBlock"
					<?php
					if (($passwordResetRequestStatus == PasswordResetStatus::NoRequest) || ($passwordResetRequestStatus == PasswordResetStatus::RequestValid)) {
						echo "style='display:none'";
					}                                     
					?>>						
						<div id="forPasswordResetInvalidRefresh">
							<div class="form">							
								<div style="font-size:3em;padding:9%;color:#E41B17">
									<?php echo CHtml::label(Yii::t('site', 'Reset Your Password'), false, array('style'=>'cursor:text;')); ?>
								</div>
								
								<div style="font-size:1em;padding:9%;color:#E41B17">
									<?php echo CHtml::label(Yii::t('site', 'This link is not valid anymore. Did you forget your password, please try to reset your password again.'), false, array('style'=>'cursor:text;')); ?>
								</div>							
							</div>																
						</div>
					</div>											
				</div>							
				
				<!-- Guest olsa da olmasa da ilk basta gozukmuyor, sonra dan istenirse aciliyor. Bu nedenle ilk olarak display:none -->	
				<div id='publicUploadsContent' style='display:none' 
				<?php
// 				if (Yii::app()->user->isGuest == false) {
// 					echo "style='display:none'";
// 				}
// 				else {
// 					echo "style='padding-top:10px;'";
// 				}				
				?>>
					<div class="form"> <!-- Bu aslina form degil, fakat yazi stili ayni olmasi icin -->				
						<div class="sideMenu">
							<div style="position:relative;display:inline-block;bottom:10px;">														
								<?php echo CHtml::label("<div class=\"secondIconPassive icon-camera\" style=\"color:#D0D0D0; cursor:default;\"></div><div class=\"hi-icon-in-list icon-group\" style=\"color:#555555; cursor:default;\"></div>", "#"); ?>
							</div>
							
							<div style="position:relative;left:3px;bottom:6px;display:inline-block;font-size:2.5em;">									
								<?php //echo Yii::t('layout', 'Public Photos'); ?>
								<?php echo CHtml::label(Yii::t('layout', 'Public Photos'), "#", array('style'=>'cursor:text;')); ?>
							</div>
							
							<div id="showRegisterFormLink" class="hi-icon-effect-5 hi-icon-effect-5a" style="position:absolute;left:305px;bottom:6px;display:inline-block;z-index:200;">						
							<?php						
							
							echo CHtml::label("Show Register Form", "#",
									array(
											//'id'=>'registerFormAjaxLink-'.uniqid(),												
											'class'=>'sliding-icon icon-profile'									
										 ));
							?>
							</div>							
						</div>
					</div>										
					
					<div id='publicUploads' style="width:340px; margin-left:10px;">
						<!-- To be updated by the ajax request -->
					</div>								
				</div>
				
				<div id="lists">				
					<div id="tabViewList" class='titles' style='width:355px; overflow-y:hidden;'>
					<?php
// 						$tabs = array();

// 						if(Yii::app()->params->featureFriendManagementEnabled)
// 						{
// 							$tabs[Yii::t('layout', 'Users')]  = array('ajax' => $this->createUrl('users/getFriendList', array('userType'=>array(UserType::RealUser/*, UserType::GPSDevice*/))), 
// 																	  //'id'=>'users_tab-'.uniqid(), //Unique ID oluşturmayınca her ajaxta bir önceki sorgular da tekrarlanıyor 
// 																	  'id'=>'users_tab',//Unique ID verince sonradan style degisimi zor oluyor
// 																	  'style'=>'width:8.4em;');
// 							//$tabs[Yii::t('layout', 'Users')]  = array('ajax' => $this->createUrl('users/getFriendList', array('userType'=>(UserType::RealUser Or UserType::GPSDevice))), 'id'=>'users_tab');
// 						}

// 						if(Yii::app()->params->featureStaffManagementEnabled)
// 						{
// 							$tabs[Yii::t('layout', 'Staff')]  = array('ajax' => $this->createUrl('users/getFriendList', array('userType'=>array(UserType::RealStaff, UserType::GPSStaff))), 
// 																	  //'id'=>'staff_tab-'.uniqid() //Unique ID oluşturmayınca her ajaxta bir önceki sorgular da tekrarlanıyor
// 																	  'id'=>'staff_tab' //Unique ID verince sonradan style degisimi zor oluyor
// 																	 );
// 						}
						
// 						$tabs[Yii::t('layout', 'Photos')] = array('ajax' => $this->createUrl('upload/getList', array('fileType'=>0)), 
// 																  //'id'=>'photos_tab-'.uniqid() //Unique ID oluşturmayınca her ajaxta bir önceki sorgular da tekrarlanıyor
// 																  'id'=>'photos_tab' //Unique ID verince sonradan style degisimi zor oluyor
// 																 ); //0:image 'id'=>'photos_tab');

// 						if(Yii::app()->params->featureFriendManagementEnabled)
// 						{
// 							$tabs[Yii::t('layout', 'Groups')] = array('ajax' => $this->createUrl('groups/getGroupList', array('groupType'=>GroupType::FriendGroup)), 
// 																	  //'id'=>'groups_tab-'.uniqid() //Unique ID oluşturmayınca her ajaxta bir önceki sorgular da tekrarlanıyor
// 																	  'id'=>'groups_tab' //Unique ID verince sonradan style degisimi zor oluyor
// 																	 );
// 						}
							
// 						if(Yii::app()->params->featureStaffManagementEnabled)
// 						{
// 							$tabs[Yii::t('layout', 'Staff Groups')] = array('ajax' => $this->createUrl('groups/getGroupList', array('groupType'=>GroupType::StaffGroup)), 
// 																			//'id'=>'staff_groups_tab-'.uniqid(), //Unique ID oluşturmayınca her ajaxta bir önceki sorgular da tekrarlanıyor
// 																		  	'id'=>'staff_groups_tab' //Unique ID verince sonradan style degisimi zor oluyor
// 																		   );
// 						}	

// 						$this->widget('zii.widgets.jui.CJuiTabs', array(
// 								// 											    'tabs' => array(
// 										// 													Yii::t('layout', 'Users') => array('ajax' => $this->createUrl('users/getFriendList'),
// 												// 																	 'id'=>'users_tab'),
// 										// 											        Yii::t('layout', 'Photos') => array('ajax' => $this->createUrl('upload/getList', array('fileType'=>0)), //0:image
// 												// 											        				  'id'=>'photos_tab'),
// 										// 											        Yii::t('layout', 'Groups') => array('ajax' => $this->createUrl('groups/getGroupList'),
// 												// 											        				  'id'=>'groups_tab'),
// 										// 											    ),
// 								'tabs' => $tabs,
// 								'id'=>"tab_view",
// 								// additional javascript options for the tabs plugin
// 								'options' => array(
// 										'collapsible' => false,
// 										'cache'=>true,
// 										'selected' => 0,
// 										'load' => 'js:function(){
// 														uploadsGridViewId = \'uploadListView\';
// 														var h = $(window).height(), offsetTop = 60;
// 														$("#users_tab").css("min-height", (485 + 100 - 60 - 81)); $("#users_tab").css("height", (h - offsetTop - 81));
// 														$("#photos_tab").css("min-height", (485 + 100 - 60 - 81)); $("#photos_tab").css("height", (h - offsetTop - 81));
// 														$("#groups_tab").css("min-height", (485 + 100 - 60 - 81)); $("#groups_tab").css("height", (h - offsetTop - 81));
// 														'.((Yii::app()->user->isGuest == false)?
// 														'switch ($("#tab_view").tabs("option", "selected"))
// 												    	 {
// 													    	 case 0: //Friends
// 													    	 {
// 													    		 TRACKER.showImagesOnTheMap = false; TRACKER.showUsersOnTheMap = true; TRACKER.getImageList(); TRACKER.getFriendList(1, 0/*UserType::RealUser*/);
// 													    	 }
// 													    	 break;
													    	   
// 													    	 case 1: //Uploads
// 													    	 {
// 													    		 TRACKER.showImagesOnTheMap = true; TRACKER.showUsersOnTheMap = false; TRACKER.getImageList(); TRACKER.getFriendList(1, 0/*UserType::RealUser*/);
// 													    	 }
// 													    	 break;
													    	   
// 													    	 case 2: //Groups
// 													    	 {
// 													    		 TRACKER.showImagesOnTheMap = false; TRACKER.showUsersOnTheMap = true; TRACKER.getImageList(); TRACKER.getFriendList(1, 0/*UserType::RealUser*/);
// 													    	 }
// 													    	 break;
// 												    	 }':'').
// 														'}'									
// 								),
// 								'htmlOptions'=>array(										
// 										'style'=>'width:345px; overflow-y:hidden;'
// 								),								
// 						));					

					if (Yii::app()->user->isGuest == false)
					{
						$this->widget('ext.EasyTabs.EasyTabs', array(
								'id'=>"tab_view",
								'tabs'=>array(
										array(
												'id'=>'users_tab',
												'title' => Yii::t('layout', 'Users'),
												'contentTitle' => '',
												'url' => $this->createUrl('users/getFriendList', array('userType'=>array(UserType::RealUser/*, UserType::GPSDevice*/))),
										),
										array(
												'id'=>'photos_tab',
												'title' => Yii::t('layout', 'Photos'),
												'contentTitle' => '',
												'url' => $this->createUrl('upload/getList', array('fileType'=>0)),
										),
										array(
												'id'=>'groups_tab',
												'title' => Yii::t('layout', 'Groups'),
												'contentTitle' => '',
												'url' => $this->createUrl('groups/getGroupList', array('groupType'=>GroupType::FriendGroup)),
										),
								),
								'options' => array(
										'updateHash' => false, //false olunca IE10'da Ctrl-F5 yapinca sonda # kalıyor ve tab view yuklenmiyor
										'cache' => true,											
								),
						));						
					}					
					?>
					</div>
				</div>									
			</div>			
		</div>

		<div id='bottomBar'>			
			<div id='bottomContent'>						
			    <div id="mobilApplicationInfo">			        
					<div id="appLink" class="hi-icon-effect-2 hi-icon-effect-2a appOperations">						
					<?php
					echo CHtml::link('Google Play Link', "https://play.google.com/store/apps/details?id=com.yudu&feature=search_result#?t=W251bGwsMSwxLDEsImNvbS55dWR1Il0.", 
									 array('id'=>'appGoogleplayLink','class'=>'vtip', 'title'=>Yii::t('layout', 'Download our mobile application at Google Play'), 
									 	   'class'=>'bottom-icon icon-download1')
							
							);
					?>
					</div>
					
					<div id="appQRCode" class="hi-icon-effect-2 hi-icon-effect-2a appOperations" style="margin-left:1em;" class="vtip" title="<?php echo Yii::t('layout', 'Click to view QR code of our mobile application'); ?>">						
					<?php					
					echo CHtml::label('QR Code','#',
									  array(
										  'id'=>'appQRCodeLink',
										  'class'=>'bottom-icon icon-qrcode','tabindex'=>14
										   ));
					?>
					</div>					
			    </div>    			
			
				<div class="cl-effect-bottomMenu">
					<div class="bottomMenu">
						<?php
						echo CHtml::ajaxLink(Yii::t('layout', 'About Us'), $this->createUrl('site/aboutUs'),
								array(
										'complete'=> 'function() { $("#aboutUsWindow").dialog("open"); return false;}',
										'update'=> '#aboutUsWindow',
								),
								array(
										'id'=>'showAboutUsWindow','tabindex'=>16));
	
						//echo 'AAA';
						?>
					</div>
					
	 				<div class="bottomMenu">
						<?php
						echo CHtml::ajaxLink(Yii::t('layout', 'Terms'), $this->createUrl('site/terms'),
								array(
										'complete'=> 'function() { $("#termsWindow").dialog("open"); return false;}',
										'update'=> '#termsWindow',
								),
								array(
										'id'=>'showTermsWindow','tabindex'=>15));
	
						//echo 'BBB';
	 					?>
	 				</div>
					
					<div class="bottomMenu">
						<?php
						echo CHtml::link('Blog', "http://traceper.blogspot.com", array('id'=>"blog", 'tabIndex'=>17));
						?>
					</div>
					
					<div class="bottomMenu">	
						<?php
						echo CHtml::ajaxLink(Yii::t('layout', 'Contact'), $this->createUrl('site/contact'),
								array(
										'complete'=> 'function() { $("#contactWindow").dialog("open"); return false;}',
										'update'=> '#contactWindow',
								),
								array(
										'id'=>'showContactWindow','tabindex'=>18));
	
						//echo 'BBB';
						?>
					</div>
				</div>	
				
			    <div id="languageSelection">
			        <div id="langTr"<?php echo ((Yii::app()->language !== 'tr')?' class="hi-icon-wrap hi-icon-effect-9 hi-icon-effect-9b" style="position:relative;bottom:5px;"':'');?>>	
			        	<?php
			        		if(Yii::app()->language == 'tr')
			        		{
			        			echo CHtml::image("images/Turkish.png", "#", array('class'=>'vtip', 'title'=>'Türkçe (şu an bu dil seçili)', 'style'=>'cursor:default;border:ridge;border-radius:10px;border-color:#98AFC7;border-width:5px;'));
			        		}
			        		else
			        		{
			        			echo CHtml::link('<img src="images/Turkish.png"/>', "#", array('type'=>'submit', 'tabindex'=>15, 'ajax'=>array('type'=>'POST','url'=>$this->createUrl('site/changeLanguage', array('lang'=>'tr')), 'complete'=> 'function() { location.reload();}'), 'class'=>'vtip selectLanguage', 'title'=>'Türkçe (Bu dili seçmek için tıklayın)'));
			        		}				        					        		
			        	?>
			        </div>

			        <div id="langEn"<?php echo ((Yii::app()->language !== 'en')?' class="hi-icon-wrap hi-icon-effect-9 hi-icon-effect-9b" style="position:relative;bottom:5px;"':'');?>>
			        	<?php
			        	//echo CHtml::link('<img src="images/English.png" />', "#", array('type'=>'submit', 'ajax'=>array('type'=>'POST','url'=>$this->createUrl('site/changeLanguage', array('lang'=>'en')), 'complete'=> 'function() {'.CHtml::ajax(array('type'=>'POST','url'=>array('site/register'),'update'=>'#forRegisterRefresh')).';'.CHtml::ajax(array('type'=>'POST','url'=>array('site/login'),'update'=>'#forAjaxRefresh')).';$("#logo").load();}')));
			        		
				        	if(Yii::app()->language == 'en')
				        	{
				        		echo CHtml::image("images/English.png", "#", array('class'=>'vtip', 'title'=>'English (This is the current language)', 'style'=>'cursor: default;border:ridge;border-radius:10px;border-color:#98AFC7;border-width:5px;'));
				        	}
				        	else
				        	{					        		
				        		echo CHtml::link('<img src="images/English.png" />', "#", array('type'=>'submit', 'tabindex'=>15, 'ajax'=>array('type'=>'POST','url'=>$this->createUrl('site/changeLanguage', array('lang'=>'en')), 'complete'=> 'function() { location.reload();}'), 'class'=>'vtip selectLanguage', 'title'=>'English (Click to choose this language)'));
				        	}
			        	?>
			        </div>			        					        
			    </div>													
			</div>			
		</div>
		
		<div id="map"></div>			
	</div>
	
	<!-- Bu gizli div gelen ajax cevabini document icine alip getElementById() ile gelen cevaptaki belli bir kismi alabilmek icin -->
	<!-- kullaniliyor. Bu div'in kullanan kisimdan sonra tanimlanmasi gerekiyor, bu nedenle document'in en sonuna tanimlandi -->
	<div id="hiddenAjaxResponseToParse" style="display:none;"></div>
	
	<script>
		//var menu = new cbpTooltipMenu( document.getElementById( 'profilePhotoSettingsMenu' ) );
	</script>	

<!-- 	<div id="forgotPasswordForm" -->
<!-- 		class="containerPlus draggable {buttons:'c', skin:'default', icon:'tick_ok.png',width:'300', height:'200', closed:'true' }"> -->
<!-- 		<div id="emailLabel"></div> -->
<!-- 		<div> -->
<!-- 			<input type="text" name="email" id="email" /><input type="button" -->
<!-- 				id="sendNewPassword" /> -->
<!-- 		</div> -->
<!-- 	</div> -->
					
</body>
</html>


