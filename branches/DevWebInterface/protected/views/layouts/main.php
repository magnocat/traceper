<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<title><?php echo Yii::app()->name; ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="keywords" content="" />
<meta name="description" content="Location-based social network and GPS tracking system" />
<link rel="stylesheet" type="text/css"
	href="<?php echo Yii::app()->request->baseUrl; ?>/css/cssreset-min.css" />
<link rel="stylesheet" type="text/css"
	href="<?php echo Yii::app()->request->baseUrl; ?>/css/style.css"
	media="screen, projection" />
<link rel="stylesheet" type="text/css"
	href="<?php echo Yii::app()->request->baseUrl; ?>/css/main.css" />
<link rel="stylesheet" type="text/css"
	href="<?php echo Yii::app()->request->baseUrl; ?>/css/form.css" />
<link rel="stylesheet" type="text/css"
	href="<?php echo Yii::app()->request->baseUrl; ?>/css/tooltipster.css" />				
<link rel="shortcut icon"
	href="<?php echo Yii::app()->request->baseUrl; ?>/images/icon.png"
	type="image/x-icon" />
<script type="text/javascript"
	src="<?php echo Yii::app()->request->baseUrl; ?>/js/DataOperations.js"></script>
<script type="text/javascript"
	src="<?php echo Yii::app()->request->baseUrl; ?>/js/maps/MapStructs.js"></script>	

<script type="text/javascript"
	src="<?php echo Yii::app()->request->baseUrl; ?>/js/maps/GMapOperator.js"></script>
	
<script type="text/javascript"
	src="<?php echo Yii::app()->request->baseUrl; ?>/js/TrackerOperator.js"></script>
<script type="text/javascript"
	src="<?php echo Yii::app()->request->baseUrl; ?>/js/LanguageOperator.js"></script>
<script type="text/javascript"
	src="<?php echo Yii::app()->request->baseUrl; ?>/js/jquery.tooltipster.min.js"></script>	
	
<script>
   $(document).ready(function() {
       	 //$('#register-form-main input[type="text"]').tooltipster({
         //	 theme: '.tooltipster-noir',
         //	 position: 'right',
         //	 trigger: 'custom',
         //	 maxWidth: 540,
         //	 onlyOne: false,
         //	 interactive: true,
         //});

         //$('#login-form-main input[type="text"]').tooltipster({
         // 	 theme: '.tooltipster-noir',
         //  	 position: 'right',
         //  	 trigger: 'custom',
         //  	 maxWidth: 540,
         //  	 onlyOne: false,
         //  	 interactive: true,
         //  });
         
		//Bunu silme, e-mail field'inin tooltip'inin cikmasini sagliyor
		$("#RegisterForm_email").tooltipster({
        	 theme: ".tooltipster-info",
        	 position: "right",
        	 trigger: "custom",
        	 maxWidth: 500,
        	 onlyOne: false,
			 interactive: true,
        	 });        

		$("#createGroup").tooltipster({
	       	 theme: ".tooltipster-info",
	       	 content: "<?php echo Yii::t('layout', '<b>Create New Group</b> </br></br> Traceper lets you group your friends. You could create new groups by this link. Moreover, you could enroll your friends into the related group and adjust the privacy settings of your groups at the tab \"Groups\".'); ?>",
	       	 position: "bottom",
	       	 trigger: "hover",
	       	 maxWidth: 300,
         	 onlyOne: false,       	 
       	 });

		$("#showPublicPhotosLink").tooltipster({
	       	 theme: ".tooltipster-info",
	       	 content: "<?php echo Yii::t('layout', 'Click here to view the list of photos shared publicly'); ?>",
	       	 position: "bottom",
	       	 trigger: "hover",
	       	 maxWidth: 260,
	       	 offsetX: -25,
	       	 onlyOne: false,       	 
      	 }); 

		$("#showCachedPublicPhotosLink").tooltipster({
	       	 theme: ".tooltipster-info",
	       	 content: "<?php echo Yii::t('layout', 'Click here to view the list of photos shared publicly'); ?>",
	       	 position: "bottom",
	       	 trigger: "hover",
	       	 maxWidth: 260,
	       	 offsetX: -25,
	       	 onlyOne: false,       	 
     	 });

		$("#showRegisterFormLink").tooltipster({
	       	 theme: ".tooltipster-info",
	       	 content: "<?php echo Yii::t('layout', 'Click here to view the registration form again'); ?>",
	       	 position: "bottom",
	       	 trigger: "hover",
	       	 maxWidth: 220,
	       	 offsetX: -25,
	       	 onlyOne: false,       	 
     	 });    	    	 

        bindTooltipActions();                    	         
   });
</script>
    
<script type="text/javascript"
	src="<?php echo Yii::app()->request->baseUrl; ?>/js/bindings.js"></script>       	

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

Yii::app()->clientScript->registerCoreScript('yiiactiveform');

Yii::app()->clientScript->registerScript('appStart',"var checked = false;
	try
	{
		var mapStruct = new MapStruct();
		var initialLoc = new MapStruct.Location({latitude:39.504041,
		longitude:35.024414});
		mapOperator.initialize(initialLoc);
		//TODO: ../index.php should be changed
		//TODO: updateUserListInterval
		//TODO: queryIntervalForChangedUsers
		var trackerOp = new TrackerOperator('index.php', mapOperator, fetchPhotosDefaultValue, 10000 /*Users query period*/ /*5000*/, 5000 /*Uploads query period*/ /*30000*/);
		trackerOp.setLangOperator(langOp);
		bindElements(langOp, trackerOp);
		trackerOp.userId = ".$userId.";
	}
	catch (e) {

	}
		",
		CClientScript::POS_READY);

if (Yii::app()->user->isGuest == false)
{
	Yii::app()->clientScript->registerScript('getDataInBackground',
			'//trackerOp.getFriendList(1, 0/*UserType::RealUser*/);
			//trackerOp.getImageList(); ',
			CClientScript::POS_READY);
}
else
{
	Yii::app()->clientScript->registerScript('getDataInBackground',
			'trackerOp.getImageList(true, false); ',
			CClientScript::POS_READY);	
}

$createGeofenceFormJSFunction = "function createGeofenceForm(geoFence){"
.CHtml::ajax(
		array(
				'url'=>Yii::app()->createUrl('geofence/createGeofence'),
				'complete'=> 'function(result) {
				$("#createGeofenceWindow").dialog("open"); return false;
}',
				'update'=> '#createGeofenceWindow',
		)).
		"}";

Yii::app()->clientScript->registerScript('getGeofenceInBackground',
		$createGeofenceFormJSFunction,
		CClientScript::POS_BEGIN);

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

?>
<script type="text/javascript">		
	var langOp = new LanguageOperator();
	var fetchPhotosDefaultValue =  1;  //TODO: $fetchPhotosInInitialization;
	
	langOp.load("<?php echo $language;?>");  //TODO: itshould be parametric
	
	var mapOperator = new MapOperator("<?php echo $language;?>");	
</script>
</head>
<body>

<script type="text/javascript">
	//TRACKER.userId = <?php //if (Yii::app()->user->isGuest == false){echo Yii::app()->user->id;}else{echo '0';} ?>;

	//alert("main TRACKER.userId:" + TRACKER.userId);	
</script>

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
	///////////////////////////// User Login Window ///////////////////////////
	echo '<div id="acceptTermsForLoginWindow" style="display:none;font-family:Helvetica;"></div>';
	

	
	
	
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
		
	//Bir link ile bir view render() fonksiyonu ile render edildiginde once tum layout aciliyor sonra da $content degsikeninde tutulan
	//view render ediliyor
	echo $content;
	
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
		
	$token = null;
	
	if (isset($_GET['tok'])  && ($_GET['tok'] != null))
	{
		//Fb::warn("in main", "main");
		
		$token = $_GET['tok'];
		
		if(ResetPassword::model()->tokenExists($token))
		{
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

	// this is a generic message dialog
	$this->beginWidget('zii.widgets.jui.CJuiDialog', array(
			'id'=>'messageDialog',
			// additional javascript options for the dialog plugin
			'options'=>array(
					'title'=>Yii::t('layout', 'Message'),
					'autoOpen'=>false,
					'modal'=>true,
					'resizable'=>false,
					'width'=>'auto',
					'height'=>'auto',
					'buttons'=>array(
							Yii::t('common', 'OK')=>"js:function(){
							$(this).dialog('close');
}"
					),
			),
	));
	//echo '</br>';
	echo '<div align="center" id="messageDialogText" style="font-family:Helvetica;"></div>';
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
					'buttons'=>array(
							Yii::t('common', 'OK')=>"js:function(){
							$(this).dialog('close');
	}"
					),	
			),
	));
	//echo '</br>';
	echo '<div align="justified" id="longMessageDialogText" style="font-family:Helvetica;"></div>';
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
					'buttons' =>array (
							Yii::t('common', 'OK')=>'js:function(){}',
							Yii::t('common', 'Cancel')=>"js:function() {
							$(this).dialog( 'close' );
}"
					)),
	));
	echo '<div id="question" style="font-family:Helvetica;"></div>';
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
		<div id='bar'></div>

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

					<div id="showPublicPhotosLink" style="position:absolute;cursor:pointer;left:19em;top:1em;display:inline-block;vertical-align:middle;width:100px;">
					<?php 								
						//echo CHtml::image('http://'.Yii::app()->request->getServerName().Yii::app()->request->getBaseUrl().'/images/signup_button_default_'.Yii::app()->language.'.png');

						echo CHtml::ajaxLink('<div id="showPublicPhotos">
								<img src="images/PublicPhotos.png"/><div></div>
								</div>', $this->createUrl('upload/getPublicList', array('fileType'=>0)),
								array(
										'update'=> '#publicUploads',
										'complete'=> 'function() 
													  {													  	
														//$("#formContent").fadeToggle( "slow", "linear");
														uploadsGridViewId = \'publicUploadListView\';
														$("#formContent").fadeToggle( "slow", function(){ hideRegisterFormErrorsIfExist(); $("#showPublicPhotosLink").hide(); bShowPublicPhotosLinkActive = false; $("#showRegisterFormLink").show(); $("#publicUploadsContent").show();});
														//$("#formContent").animate({height:"0px", marginTop:$("#sideBar").height()}, function(){ $("#formContent").hide(); });
														//$("#showPublicPhotosLink").hide();
														//$("#showRegisterFormLink").show();	
														//$("#publicUploads").show();
													  }',										
						
// 										'success'=> 'function(msg)
// 													 {
// 														try
// 														{
// 															var obj = jQuery.parseJSON(msg);
															
// 															if (obj.result)
// 															{
// 																if (obj.result == "1")
// 																{
// 																	TRACKER.showLongMessageDialog("'.Yii::t('site', 'Your account created successfully. ').Yii::t('site', 'We have sent an account activation link to your mail address \"<b>').'" + obj.email + "'.Yii::t('site', '</b>\". </br></br>Please make sure you check the spam/junk folder as well. The links in a spam/junk folder may not work sometimes; so if you face such a case, mark our e-mail as \"Not Spam\" and reclick the link.').'");
// 																}
// 																else if (obj.result == "2")
// 																{
// 																	TRACKER.showLongMessageDialog("'.Yii::t('site', 'Your account created successfully, but an error occured while sending your account activation e-mail. You could request your activation e-mail by clicking the link \"Not Received Our Activation E-Mail?\" just below the register form. If the error persists, please contact us about the problem.').'");
// 																}
// 																else if (obj.result == "0")
// 																{
// 																	TRACKER.showMessageDialog("'.Yii::t('common', 'Sorry, an error occured in operation').'");
// 																}
// 															}
// 														}
// 														catch (error)
// 														{
// 															$("#publicUploads").html(msg);
														
// 															//alert("Deneme");
// 														}
// 													}'										
								),
								array(
										'id'=>'publicUploadsAjaxLink-'.uniqid() //Unique ID oluşturmayınca her ajaxta bir önceki sorgular da tekrarlanıyor
										/*,'class'=>'vtip', 'title'=>Yii::t('layout', 'Create New Group')*/));									
					?>							  								
					</div>
					
					<div id="showCachedPublicPhotosLink" style="position:absolute;cursor:pointer;left:19em;top:1em;display:none;vertical-align:middle;width:100px;">
						<?php 	
						echo CHtml::link('<img src="images/PublicPhotos.png" />');
						?>		
					</div>					
					
					<div id="showRegisterFormLink" style="position:absolute;cursor:pointer;left:19em;top:1em;display:none;vertical-align:middle;width:100px;">
						<?php 	
						echo CHtml::link('<img src="images/RegisterForm.png" />');
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

					<div id="forAjaxRefresh">
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

							<div class="upperMenu">
								<div style="height:3em;top:0%;padding:0px;">
									<?php echo $form->labelEx($model,'email'); ?>
									<?php echo $form->textField($model,'email', array('size'=>'30%','maxlength'=>'30%','tabindex'=>1)); ?>
									<?php 
// 										  $errorMessage = $form->error($model,'email'); 
// 										  if (strip_tags($errorMessage) == '') { echo '<div class="errorMessage">&nbsp;</div>'; }
// 										  else { echo $errorMessage; }
									?>									
								</div>
								
								<div style="margin-top:0px;padding:0px;">
									<?php echo $form->checkBox($model,'rememberMe',array('size'=>5,'maxlength'=>128,'tabindex'=>4)); ?>
									<?php echo $form->label($model,'rememberMe',array('style'=>'font-weight:normal;')); ?>
								</div>									
							</div>

							<div class="upperMenu">
								<div style="height:3em;top:0%;padding:0px;">
									<?php echo $form->labelEx($model,'password'); ?>
									<?php echo $form->passwordField($model,'password', array('size'=>'30%','maxlength'=>'30%','tabindex'=>2)); ?>
									<?php 
										  //$errorMessage = $form->error($model,'password'); 
// 										  if (strip_tags($errorMessage) == '') { echo '<div class="errorMessage">&nbsp;</div>'; }
// 										  else { echo $errorMessage; }
									?>									
								</div>
								
			 					<div style="margin-top:2px;padding:0px;">
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
														
							<div class="upperMenu" style="margin-top:0.7em;width:50px;">
								<div style="height:3.3em;top:0%;padding:0px;">								
									<?php																											
// 									$this->widget('zii.widgets.jui.CJuiButton', array(											
// 											'name'=>'ajaxLogin',
// 											'caption'=>Yii::t('site', 'Log in'),
// 											'id'=>'loginAjaxButton',
// 											'htmlOptions'=>array('type'=>'submit','style'=>'width:8.4em;','tabindex'=>3,'ajax'=>array('type'=>'POST','url'=>array('site/login'),'update'=>'#forAjaxRefresh'))
// 									));

									$this->widget('zii.widgets.jui.CJuiButton', array(
											'name'=>'ajaxLogin',
											'caption'=>Yii::t('site', 'Log in'),
											'id'=>'loginAjaxButton',
											'htmlOptions'=>array('type'=>'submit','style'=>'width:8.4em;','tabindex'=>3,'ajax'=>array('type'=>'POST','url'=>array('site/login'),
																	'success'=> 'function(msg){													
																					if(msg.search("acceptTermsForLoginWindow") !== -1)
																					{
																						var opt = {
																						        autoOpen: false,
																						        modal: true,
																								resizable: false,
																						        width: 600,
																						        title: "'.Yii::t('site', 'Accept Terms to continue').'"
																						};													
																						
																						$("#acceptTermsForLoginWindow").dialog(opt).dialog("open");
																						$("#acceptTermsForLoginWindow").html(msg);																																		
																					}
																					else
																					{
																						//Form error veya successful durumu icin
																						$("#forAjaxRefresh").html(msg);
																					}													
																				}',
													))
											));									
									?>
								</div>																					
							</div>													

							<?php $this->endWidget(); ?>
						</div>						
					</div>										
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
						<div id="username"
							onclick="TRACKER.trackUser(<?php echo $userId; ?>)" class="vtip"
							title="<?php echo Yii::t('layout', 'See your position on the map'); ?>">
							<?php if (Yii::app()->user->isGuest == false){ 
								echo Yii::app()->user->name;
							}
							?>
						</div>
					</div>
					<?php
					echo CHtml::link('<div  class="userOperations" id="signout">
							<img src="images/signout.png"  /><div></div>
							</div>', $this->createUrl('site/logout'), array('class'=>'vtip', 'title'=>Yii::t('layout', 'Sign Out')));					
					
					echo CHtml::ajaxLink('<div style="float:right" id="changePassword" class="userOperations">
							<img src="images/changePassword.png"  /><div></div>
							</div>', $this->createUrl('site/changePassword'),
							array(
									'complete'=> 'function() { $("#changePasswordWindow").dialog("open"); return false;}',
									'update'=> '#changePasswordWindow',
							),
							array(
									'id'=>'showChangePasswordWindow','class'=>'vtip', 'title'=>Yii::t('layout', 'Change Password')));										 

					if(Yii::app()->params->featureFriendManagementEnabled)
					{
						echo CHtml::ajaxLink('<div class="userOperations" id="inviteUser">
								<img src="images/invite.png"  /><div></div>
								</div>', $this->createUrl('site/inviteUsers'),
								array(
										'complete'=> 'function() { $("#inviteUsersWindow").dialog("open"); return false;}',
										'update'=> '#inviteUsersWindow',
								),
								array(
										'id'=>'showInviteUsersWindow','class'=>'vtip', 'title'=>Yii::t('layout', 'Invite Friends')));

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
																
						if($newRequestsCount > 0)
						{
							//Fb::warn("newRequestsCount > 0", "main");
							
							if($newRequestsCount <= 5)
							{
								echo CHtml::ajaxLink('<div class="userOperations" id="friendRequests">
										<img id="friendRequestsImage" src="images/friends_'.$newRequestsCount.'.png"/><div></div>
										</div>', $this->createUrl('users/GetFriendRequestList'),
										array(
												'complete'=> 'function() { $("#friendRequestsWindow").dialog("open"); document.getElementById("friendRequestsImage").src = "images/friends.png"; document.getElementById("friendRequestsImage").title = "'.Yii::t('users', 'Friendship Requests').'"; return false;}',
												'update'=> '#friendRequestsWindow',
										),
										array(
												'id'=>'showFriendRequestsWindow','class'=>'vtip', 'title'=>Yii::t('users', 'Friendship Requests').' ('.$newRequestsCount.' '.Yii::t('users', 'new').(($totalRequestsCount > $newRequestsCount)?Yii::t('users', ' totally ').$totalRequestsCount:'').Yii::t('users', ' friendship request(s) you have').')'));								
							}
							else
							{
								echo CHtml::ajaxLink('<div class="userOperations" id="friendRequests">
										<img id="friendRequestsImage" src="images/friends_many.png"/><div></div>
										</div>', $this->createUrl('users/GetFriendRequestList'),
										array(
												'complete'=> 'function() { $("#friendRequestsWindow").dialog("open"); document.getElementById("friendRequestsImage").src = "images/friends.png"; document.getElementById("friendRequestsImage").title = "'.Yii::t('users', 'Friendship Requests').'"; return false;}',
												'update'=> '#friendRequestsWindow',
										),
										array(
												'id'=>'showFriendRequestsWindow','class'=>'vtip', 'title'=>Yii::t('users', 'Friendship Requests').' ('.$newRequestsCount.' '.Yii::t('users', 'new').(($totalRequestsCount > $newRequestsCount)?Yii::t('users', ' totally ').$totalRequestsCount:'').Yii::t('users', ' friendship request(s) you have').')'));								
							}							
						}
						else
						{
							//Fb::warn("newRequestsCount == 0", "main");
							
							echo CHtml::ajaxLink('<div class="userOperations" id="friendRequests">
									<img id="friendRequestsImage" src="images/friends.png"  /><div></div>
									</div>', $this->createUrl('users/GetFriendRequestList'),
									array(
											'complete'=> 'function() { $("#friendRequestsWindow").dialog("open"); return false;}',
											'update'=> '#friendRequestsWindow',
									),
									array(
											'id'=>'showFriendRequestsWindow','class'=>'vtip', 'title'=>Yii::t('users', 'Friendship Requests')));							
						}						
					}

					echo CHtml::ajaxLink('<div class="userOperations" id="createGroup">
							<img src="images/createGroup.png"  /><div></div>
							</div>', $this->createUrl('groups/createGroup'),
							array(
									'complete'=> 'function() { $("#createGroupWindow").dialog("open"); return false;}',
									'update'=> '#createGroupWindow',
							),
							array(
									'id'=>'showCreateGroupWindow'/*,'class'=>'vtip', 'title'=>Yii::t('layout', 'Create New Group')*/));
			
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
						echo "style='height:85%;min-height:420px;'";
					}				
					?>>						
						<div id="forRegisterRefresh" style='height:100%;'>						
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
									<div style="font-size:3em;">
									<?php echo $form->labelEx($model, 'register', array('style'=>'cursor:text;')); ?>
									</div>
								</div>
								
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
								
								<div class="sideMenu" style="height:25px;font-size:12px;margin-left:0.3em;">
								<?php	
									echo Yii::t('layout', 'By sending the Sign Up form, you agree to our {terms of use}', array('{terms of use}'=>
											CHtml::ajaxLink(Yii::t('layout', 'Terms of Use'), $this->createUrl('site/terms'),
													array(
															'complete'=> 'function() { hideFormErrorsIfExist(); $("#termsWindow").dialog("open"); return false;}',
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
									
									echo CHtml::imageButton('http://'.Yii::app()->request->getServerName().Yii::app()->request->getBaseUrl().'/images/signup_button_default_'.Yii::app()->language.'.png',
											array('id'=>'registerButton', 'type'=>'submit', 'style'=>'margin-top:0px;cursor:pointer;', 'ajax'=>array('type'=>'POST','url'=>array('site/register'),
													'success'=> 'function(msg){
													try
													{
													var obj = jQuery.parseJSON(msg);
														
													if (obj.result)
													{
													if (obj.result == "1")
													{
													TRACKER.showLongMessageDialog("'.Yii::t('site', 'Your account created successfully. ').Yii::t('site', 'We have sent an account activation link to your mail address \"<b>').'" + obj.email + "'.Yii::t('site', '</b>\". </br></br>Please make sure you check the spam/junk folder as well. The links in a spam/junk folder may not work sometimes; so if you face such a case, mark our e-mail as \"Not Spam\" and reclick the link.').'");
									}
													else if (obj.result == "2")
													{
													TRACKER.showLongMessageDialog("'.Yii::t('site', 'Your account created successfully, but an error occured while sending your account activation e-mail. You could request your activation e-mail by clicking the link \"Not Received Our Activation E-Mail?\" just below the register form. If the error persists, please contact us about the problem.').'");
									}
													else if (obj.result == "0")
													{
													TRACKER.showMessageDialog("'.Yii::t('common', 'Sorry, an error occured in operation').'");
									}
									}
									}
													catch (error)
													{
													$("#forRegisterRefresh").html(msg);
									
													//alert("Deneme");
													
													
									}
									}',
											),'onmouseover'=>'this.src="images/signup_button_mouseover_'.Yii::app()->language.'.png";',
													'onmouseout'=>'this.src="images/signup_button_default_'.Yii::app()->language.'.png";$("#registerButton").css("margin-top", "0px");',
													'onmousedown'=>'$("#registerButton").css("margin-top", "2px");',
													'onmouseup'=>'$("#registerButton").css("margin-top", "0px");',
											));								
									?>
									</div>
									
									<div style="position:absolute;left:11em;top:1.2em;display:inline-block;vertical-align:top;width:50%;">
									<?php
									echo CHtml::ajaxLink('<div id="activationNotReceived">'.Yii::t('site', 'Not Received Our Activation E-Mail?').
														'</div>', $this->createUrl('site/activationNotReceived'),
											array(
													'complete'=> 'function() { hideFormErrorsIfExist(); $("#activationNotReceivedWindow").dialog("open"); return false;}',
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
					</div>
				
<!-- VIDEO WORK	 -->
								
<!-- 				<video id="my_video_1" class="video-js vjs-default-skin" controls preload="auto" width="320" height="264" poster="my_video_poster.png" data-setup="{}"> -->
<!-- 				  <source src="http://localhost/traceper/branches/DevWebInterface/upload/oceans-clip.mp4" type='video/mp4'> -->
<!-- <!-- 				  <source src="http://localhost/traceper/branches/DevWebInterface/upload/14.flv" type='video/flv'> -->
<!-- 				</video>				 -->
				
				
					<div id="passwordResetBlock"
					<?php
					if (($passwordResetRequestStatus == PasswordResetStatus::NoRequest) || ($passwordResetRequestStatus == PasswordResetStatus::RequestInvalid)) {
						echo "style='display:none'";
					}                                     
					?>>						
						<div id="forPasswordResetRefresh">
							<div class="form">
								<?php
								$form=$this->beginWidget('CActiveForm', array(
										'id'=>'passwordReset-form-main',
										'enableClientValidation'=>true,
								));
	
								$model = new ResetPasswordForm;
								?>							
								
								<div style="padding:9%;font-size:3em;">
									<?php echo $form->labelEx($model,'resetPassword'); ?>
								</div>							
								
								<div class="sideMenu" style="margin-left:2em;">
									<?php echo $form->labelEx($model,'newPassword'); ?>
									<?php echo $form->passwordField($model,'newPassword', array('size'=>'30%','maxlength'=>128,'tabindex'=>7)); ?>
								</div>
	
								<div class="sideMenu" style="margin-left:2em;">
									<?php echo $form->labelEx($model,'newPasswordAgain'); ?>
									<?php echo $form->passwordField($model,'newPasswordAgain', array('size'=>'30%','maxlength'=>128,'tabindex'=>8)); ?>
								</div>
	
								<div class="sideMenu" style="margin-left:2em;">
									<?php
	// 								$this->widget('zii.widgets.jui.CJuiButton', array(      
	// 										'name'=>'ajaxResetPassword',
	// 										'caption'=>Yii::t('site', 'Update'),
	// 										'id'=>'resetPasswordAjaxButton',
	// 										'htmlOptions'=>array('type'=>'submit','tabindex'=>9,'ajax'=>array('type'=>'POST','url'=>$this->createUrl('site/resetPassword', array('token'=>$token)), 'update'=>'#forPasswordResetRefresh'))
	// 								));
	
									
									$this->widget('zii.widgets.jui.CJuiButton', array(
																			'name'=>'ajaxResetPassword',
																			'caption'=>Yii::t('site', 'Update'),
																			'id'=>'resetPasswordAjaxButton',
																			'htmlOptions'=>array('type'=>'submit','tabindex'=>9,'ajax'=>array('type'=>'POST','url'=>$this->createUrl('site/resetPassword', array('token'=>$token)), 
																																			  'success'=> 'function(msg){
																																								try
																																								{																								
																																									var obj = jQuery.parseJSON(msg);
																																										
																																									if (obj.result)
																																									{
																																										if (obj.result == "1") 
																																										{
																																											//TRACKER.showLongMessageDialog("'.Yii::t('site', 'Your account created successfully. ').Yii::t('site', 'We have sent an account activation link to your mail address \"<b>').'" + obj.email + "'.Yii::t('site', '</b>\". </br></br>Please make sure you check the spam/junk folder as well. The links in a spam/junk folder may not work sometimes; so if you face such a case, mark our e-mail as \"Not Spam\" and reclick the link.').'");
																																											
																																											TRACKER.showMessageDialog("'.Yii::t('site', 'Your password has been changed successfully, you can login now...').'");
																																											$("#passwordResetBlock").hide();
																																											$("#registerBlock").css("height", "85%");
																																											$("#registerBlock").css("min-height", "420px");
																																											$("#registerBlock").load();
																																											$("#registerBlock").show();
																																											$("#appLinkBlock").load();
																																											$("#appLinkBlock").show();																																										
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
																																								}																															
																																							}',																		
																			))
									));								
									?>
								</div>
	
								<?php $this->endWidget(); ?>
							</div>
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
									<?php echo CHtml::label(Yii::t('site', 'Reset Your Password'), false); ?>
								</div>
								
								<div style="font-size:1em;padding:9%;color:#E41B17">
									<?php echo CHtml::label(Yii::t('site', 'This link is not valid anymore. Did you forget your password, please try to reset your password again.'), false); ?>
								</div>							
							</div>
								
								
						</div>
					</div>
											
					<div id="appLinkBlock"
					<?php
					if (($passwordResetRequestStatus == PasswordResetStatus::RequestValid) || ($passwordResetRequestStatus == PasswordResetStatus::RequestInvalid)) {
						echo "style='display:none'";
					}
					?>>
				        <div id="appLink" style="position:relative;top:3em;left:1em;" class="vtip" title="<?php echo Yii::t('layout', 'Download the application at Google Play'); ?>">
				            <a href="https://play.google.com/store/apps/details?id=com.yudu&feature=search_result#?t=W251bGwsMSwxLDEsImNvbS55dWR1Il0." tabindex="6"><img src="images/GooglePlay.png" style="position:absolute;bottom:0;"/></a>  			
				        </div>				
					
				        <div id="appQRCode" style="position:relative;top:3.6em;left:9.5em;">
				            <img id="androidBubbleTr" src="images/AndroidBubble_tr.png" style="<?php if(Yii::app()->language != 'tr') {echo "display:none;";} ?>position:absolute;bottom:0;" onmouseover="this.src='images/QR_code.png';this.style.cursor='none';" onmouseout="this.src='images/AndroidBubble_tr.png';this.style.cursor='default';"/>  			
				        </div>
				        
				        <div id="appQRCode" style="position:relative;top:3.6em;left:9em;">
				            <img id="androidBubbleEn" src="images/AndroidBubble_en.png" style="<?php if(Yii::app()->language != 'en') {echo "display:none;";} ?>position:absolute;bottom:0;" onmouseover="this.src='images/QR_code.png';this.style.cursor='none';" onmouseout="this.src='images/AndroidBubble_en.png';this.style.cursor='default';"/>  			
				        </div>			        									
					</div>
				</div>							
				
				<div id='publicUploadsContent' 
				<?php
				if (Yii::app()->user->isGuest == false) {
					echo "style='display:none'";
				}
				else {
					
				}				
				?>>				
					<div class="sideMenu">
						<div id='publicUploadsTitle' style="font-size:2.6em;font-weight:bold;cursor:text;">
							<?php echo Yii::t('layout', 'Public Photos'); ?>
						</div>
					</div>										
					
					<div id='publicUploads' style="width:340px; margin-left:10px;">
						<!-- To be updated by the ajax request -->
					</div>								
				</div>
				
				<div id="lists"
				<?php
				if (Yii::app()->user->isGuest == true) {
					echo "style='display:none'";
				}?>>				
					<div class='titles'>
					<?php
						$tabs = array();

						if(Yii::app()->params->featureFriendManagementEnabled)
						{
							$tabs[Yii::t('layout', 'Users')]  = array('ajax' => $this->createUrl('users/getFriendList', array('userType'=>array(UserType::RealUser/*, UserType::GPSDevice*/))), 
																	  //'id'=>'users_tab-'.uniqid(), //Unique ID oluşturmayınca her ajaxta bir önceki sorgular da tekrarlanıyor 
																	  'id'=>'users_tab', //Unique ID verince sonradan style degisimi zor oluyor
																	  'style'=>'width:8.4em;');
							//$tabs[Yii::t('layout', 'Users')]  = array('ajax' => $this->createUrl('users/getFriendList', array('userType'=>(UserType::RealUser Or UserType::GPSDevice))), 'id'=>'users_tab');
						}

						if(Yii::app()->params->featureStaffManagementEnabled)
						{
							$tabs[Yii::t('layout', 'Staff')]  = array('ajax' => $this->createUrl('users/getFriendList', array('userType'=>array(UserType::RealStaff, UserType::GPSStaff))), 
																	  //'id'=>'staff_tab-'.uniqid() //Unique ID oluşturmayınca her ajaxta bir önceki sorgular da tekrarlanıyor
																	  'id'=>'staff_tab' //Unique ID verince sonradan style degisimi zor oluyor
																	 );
						}
						
						$tabs[Yii::t('layout', 'Photos')] = array('ajax' => $this->createUrl('upload/getList', array('fileType'=>0)), 
																  //'id'=>'photos_tab-'.uniqid() //Unique ID oluşturmayınca her ajaxta bir önceki sorgular da tekrarlanıyor
																  'id'=>'photos_tab' //Unique ID verince sonradan style degisimi zor oluyor
																 ); //0:image 'id'=>'photos_tab');

						if(Yii::app()->params->featureFriendManagementEnabled)
						{
							$tabs[Yii::t('layout', 'Groups')] = array('ajax' => $this->createUrl('groups/getGroupList', array('groupType'=>GroupType::FriendGroup)), 
																	  //'id'=>'groups_tab-'.uniqid() //Unique ID oluşturmayınca her ajaxta bir önceki sorgular da tekrarlanıyor
																	  'id'=>'groups_tab' //Unique ID verince sonradan style degisimi zor oluyor
																	 );
						}
							
						if(Yii::app()->params->featureStaffManagementEnabled)
						{
							$tabs[Yii::t('layout', 'Staff Groups')] = array('ajax' => $this->createUrl('groups/getGroupList', array('groupType'=>GroupType::StaffGroup)), 
																			//'id'=>'staff_groups_tab-'.uniqid(), //Unique ID oluşturmayınca her ajaxta bir önceki sorgular da tekrarlanıyor
																		  	'id'=>'staff_groups_tab' //Unique ID verince sonradan style degisimi zor oluyor
																		   );
						}	

						$this->widget('zii.widgets.jui.CJuiTabs', array(
								// 											    'tabs' => array(
										// 													Yii::t('layout', 'Users') => array('ajax' => $this->createUrl('users/getFriendList'),
												// 																	 'id'=>'users_tab'),
										// 											        Yii::t('layout', 'Photos') => array('ajax' => $this->createUrl('upload/getList', array('fileType'=>0)), //0:image
												// 											        				  'id'=>'photos_tab'),
										// 											        Yii::t('layout', 'Groups') => array('ajax' => $this->createUrl('groups/getGroupList'),
												// 											        				  'id'=>'groups_tab'),
										// 											    ),
								'tabs' => $tabs,
								'id'=>"tab_view",
								// additional javascript options for the tabs plugin
								'options' => array(
										'collapsible' => false,
										'cache'=>true,
										'selected' => 0,
										'load' => 'js:function(){
														uploadsGridViewId = \'uploadListView\';
														var h = $(window).height(), offsetTop = 60;
														$("#users_tab").css("min-height", (485 + 100 - 60 - 81)); $("#users_tab").css("height", (h - offsetTop - 81));
														$("#photos_tab").css("min-height", (485 + 100 - 60 - 81)); $("#photos_tab").css("height", (h - offsetTop - 81));
														$("#groups_tab").css("min-height", (485 + 100 - 60 - 81)); $("#groups_tab").css("height", (h - offsetTop - 81));
														'.((Yii::app()->user->isGuest == false)?
														'switch ($("#tab_view").tabs("option", "selected"))
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
												    	 }':'').
														'}'									
								),
								'htmlOptions'=>array(										
										'style'=>'width:345px; overflow-y:hidden;',
								),								
						));
					?>
					</div>
				</div>									
			</div>			
		</div>


		<div id='bottomBar'>			
			<div id='bottomContent'>
				<div class="bottomMenu">
					<?php
					echo CHtml::ajaxLink('<div id="aboutUs">'.Yii::t('layout', 'About Us').
							'</div>', $this->createUrl('site/aboutUs'),
							array(
									'complete'=> 'function() { hideFormErrorsIfExist(); $("#aboutUsWindow").dialog("open"); return false;}',
									'update'=> '#aboutUsWindow',
							),
							array(
									'id'=>'showAboutUsWindow','tabindex'=>16));

					//echo 'AAA';
					?>
				</div>
				
 				<div class="bottomMenu">
					<?php
					echo CHtml::ajaxLink('<div id="terms">'.Yii::t('layout', 'Terms').
							'</div>', $this->createUrl('site/terms'),
							array(
									'complete'=> 'function() { hideFormErrorsIfExist(); $("#termsWindow").dialog("open"); return false;}',
									'update'=> '#termsWindow',
							),
							array(
									'id'=>'showTermsWindow','tabindex'=>15));

					//echo 'BBB';
 					?>
 				</div>
				
				<div class="bottomMenu">	
					<a href= "http://traceper.blogspot.com" tabindex="17">Blog</a>
				</div>
				
				<div class="bottomMenu">	
					<?php
					echo CHtml::ajaxLink('<div id="contact">'.Yii::t('layout', 'Contact').
							'</div>', $this->createUrl('site/contact'),
							array(
									'complete'=> 'function() { hideFormErrorsIfExist(); $("#contactWindow").dialog("open"); return false;}',
									'update'=> '#contactWindow',
							),
							array(
									'id'=>'showContactWindow','tabindex'=>18));

					//echo 'BBB';
					?>
				</div>
				
			    <div id="languageSelection">
			        <div id="langTr">	
			        	<?php
			        		if(Yii::app()->language == 'tr')
			        		{
			        			echo CHtml::image("images/Turkish.png", "#", array('class'=>'vtip', 'title'=>'Türkçe (şu an bu dil seçili)', 'style'=>'cursor:default;border:ridge;border-radius:10px;border-color:#98AFC7;border-width:5px;'));
			        		}
			        		else
			        		{
			        			echo CHtml::link('<img src="images/TurkishNotSelected.png" />', "#", array('type'=>'submit', 'tabindex'=>15, 'ajax'=>array('type'=>'POST','url'=>$this->createUrl('site/changeLanguage', array('lang'=>'tr')), 'complete'=> 'function() { location.reload();}'), 'class'=>'vtip', 'title'=>'Türkçe (Bu dili seçmek için tıklayın)'));
			        		}				        					        		
			        	?>
			        </div>					    
			    
			        <div id="langEn">
			        	<?php
			        	//echo CHtml::link('<img src="images/English.png" />', "#", array('type'=>'submit', 'ajax'=>array('type'=>'POST','url'=>$this->createUrl('site/changeLanguage', array('lang'=>'en')), 'complete'=> 'function() {'.CHtml::ajax(array('type'=>'POST','url'=>array('site/register'),'update'=>'#forRegisterRefresh')).';'.CHtml::ajax(array('type'=>'POST','url'=>array('site/login'),'update'=>'#forAjaxRefresh')).';$("#logo").load();}')));
			        		
				        	if(Yii::app()->language == 'en')
				        	{
				        		echo CHtml::image("images/English.png", "#", array('class'=>'vtip', 'title'=>'English (This is the current language)', 'style'=>'cursor: default;border:ridge;border-radius:10px;border-color:#98AFC7;border-width:5px;'));
				        	}
				        	else
				        	{					        		
				        		echo CHtml::link('<img src="images/EnglishNotSelected.png" />', "#", array('type'=>'submit', 'tabindex'=>15, 'ajax'=>array('type'=>'POST','url'=>$this->createUrl('site/changeLanguage', array('lang'=>'en')), 'complete'=> 'function() { location.reload();}'), 'class'=>'vtip', 'title'=>'English (Click to choose this language)'));
				        	}
			        	?>
			        </div>			        					        
			    </div>													
			</div>			
		</div>
		
		<div id="map"></div>			
	</div>
	


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

<?php	
	if (Yii::app()->user->isGuest == false)
	{
?>	
<script type="text/javascript">
	var h = $(window).height(), offsetTop = 60; // Calculate the top offset
	var w = $(window).width(), offsetLeft = 396; // Calculate the left offset	

	$('#topBar').css('height', '60px');
	$('#sideBar').css('top', '60px');
	$('#sideBar').css('width', '380px');
	$('#sideBar').css('height', (h - offsetTop));
	$('#sideBar').css('min-height', (485 + 100 - 60));
	$('#bar').css('top', offsetTop);
	$('#bar').css('height', (h - offsetTop));
	$('#bar').css('left', '380px');
	$('#bar').css('min-height', (485 + 100 - 60));		
	$('#map').css('height', (h - offsetTop)); //$("#map").css('height', '94%');
	$('#map').css('width', (w - offsetLeft));
	$('#map').css('min-width', (735 + 260 - 380));
	$('#map').css('min-height', (485 + 100 - 60));		
</script>	
<?php	
	}
?>


