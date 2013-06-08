<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<title><?php echo Yii::app()->name; ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="keywords" content="" />
<meta name="description" content="open source GPS tracking system" />
<link rel="stylesheet" type="text/css"
	href="<?php echo Yii::app()->request->baseUrl; ?>/css/style.css"
	media="screen, projection" />
<link rel="stylesheet" type="text/css"
	href="<?php echo Yii::app()->request->baseUrl; ?>/css/main.css" />
<link rel="stylesheet" type="text/css"
	href="<?php echo Yii::app()->request->baseUrl; ?>/css/form.css" />
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
	src="<?php echo Yii::app()->request->baseUrl; ?>/js/bindings.js"></script>
	

<!-- <link href="http://vjs.zencdn.net/c/video-js.css" rel="stylesheet"> -->
<!-- <script src="http://vjs.zencdn.net/c/video.js"></script> -->

<!-- VIDEO WORK	 -->
	
<!-- <link href="http://localhost/traceper/branches/DevWebInterface/js/video-js/video-js.css" rel="stylesheet"> -->
<!-- <script src="http://localhost/traceper/branches/DevWebInterface/js/video-js/video.js"></script>		 -->

<?php 

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
		var trackerOp = new TrackerOperator('index.php', mapOperator, fetchPhotosDefaultValue, 5000, 30000)
		trackerOp.setLangOperator(langOp);
		bindElements(langOp, trackerOp);
}
		catch (e) {

}

		",
		CClientScript::POS_READY);

if (Yii::app()->user->isGuest == false)
{
	Yii::app()->clientScript->registerScript('getDataInBackground',
			'trackerOp.getFriendList(1);
			trackerOp.getImageList(); ',
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
?>

<script type="text/javascript">		
		var langOp = new LanguageOperator();
		var fetchPhotosDefaultValue =  1;  //TODO: $fetchPhotosInInitialization;
		langOp.load("en");  //TODO: itshould be parametric
		
		var mapOperator = new MapOperator();
	</script>


</head>
<body>

	<?php

	///////////////////////////// About traceper Window ///////////////////////////
	$this->beginWidget('zii.widgets.jui.CJuiDialog', array(
			'id'=>'Logo',
			// additional javascript options for the dialog plugin
			'options'=>array(
					'title'=>Yii::t('layout', 'About'),
					'autoOpen'=>false,
					'modal'=>true,
					'resizable'=>false,
					'width'=> '600px'
			),
	));

	//echo '<div id="logo"></div>';
	//echo 'traceper is a GPS tracking system for mobile users, it is free, it is open source, it is simple. You can track and see your friends\' positions online.<br/><br/><div class=\"title\">Support</div>If you need support to modify and use this software, We can share all information we have, so feel free to contact us.<br/><br/><div class=\"title\">License</div>This software is free. It can be modified and distributed without notification.<br/><br/><div class=\"title\">Disclaimer</div>This software guarantees nothing, use it with your own risk. No responsilibity is taken for any situation.<br/><br/><div class=\"title\">Contact</div><a href=\"mailto:contact@mekya.com\">contact@mekya.com</a><br/><br/><div class=\"title\">Project Team</div><div id=\"projectteam\">Adnan Kalay - adnankalay@gmail.com <br/> Ahmet Oguz Mermerkaya - ahmetmermerkaya@gmail.com <br/> Murat Salman - salman.murat@gmail.com </div>';
	echo Yii::t('layout', 'Traceper Info');

	$this->endWidget('zii.widgets.jui.CJuiDialog');
	
	///////////////////////////// User Login Window ///////////////////////////
	echo '<div id="userLoginWindow"></div>';
	///////////////////////////// Register Window ///////////////////////////
	echo '<div id="registerWindow"></div>';
	///////////////////////////// Register GPS Tracker Window ///////////////////////////
	echo '<div id="registerGPSTrackerWindow"></div>';
	///////////////////////////// Register GPS Tracker Window ///////////////////////////
	echo '<div id="registerNewStaffWindow"></div>';
	///////////////////////////// GeoFence Window ///////////////////////////
	echo '<div id="geoFenceWindow"></div>';
	///////////////////////////// Change Password Window ///////////////////////////
	echo '<div id="changePasswordWindow"></div>';
	///////////////////////////// Forget Password Window ///////////////////////////
	echo '<div id="forgotPasswordWindow"></div>';
	///////////////////////////// Reset Password Window ///////////////////////////
	echo '<div id="resetPasswordWindow"></div>';
	///////////////////////////// Activation Not Received Window ///////////////////////////
	echo '<div id="activationNotReceivedWindow"></div>';	
	///////////////////////////// Invite User Window ///////////////////////////
	echo '<div id="inviteUsersWindow"></div>';
	///////////////////////////// Friend Request Window ///////////////////////////
	echo '<div id="friendRequestsWindow"></div>';
	///////////////////////////// Create Group Window ///////////////////////////
	echo '<div id="createGroupWindow"></div>';
	///////////////////////////// Group Settings Window ///////////////////////////
	echo '<div id="groupSettingsWindow"></div>';
	///////////////////////////// Group Privacy Settings Window ///////////////////////////
	echo '<div id="groupPrivacySettingsWindow"></div>';
	///////////////////////////// Group Members Window ///////////////////////////
	echo '<div id="groupMembersWindow"></div>';
	///////////////////////////// Geofence Settings Window ///////////////////////////
	echo '<div id="geofenceSettingsWindow"></div>';
	////////// Create Geofence Window ///////////////////////////
	echo '<div id="createGeofenceWindow"></div>';
	
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
	echo '</br>';
	echo '<div align="center" id="messageDialogText"></div>';
	$this->endWidget('zii.widgets.jui.CJuiDialog');
	/////////////////////////////////////////////////////////////////////////////////////////////
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
					'buttons' =>array (
							Yii::t('common', 'OK')=>'js:function(){}',
							Yii::t('common', 'Cancel')=>"js:function() {
							$(this).dialog( 'close' );
}"
					)),
	));
	echo '<div id="question"></div>';
	$this->endWidget('zii.widgets.jui.CJuiDialog');

	?>
	<div id='wrap'>
		<div class='logo_inFullMap'></div>
		<div id='bar'></div>

		<div id='topBar'>
			<div id='topContent'>

				<?php
				if (Yii::app()->user->isGuest == false) {
					echo CHtml::link('<div id="logo" style="display:none"></div>', '#', array(
							'onclick'=>'$("#Logo").dialog("open"); return false;', 'class'=>'vtip', 'title'=>Yii::t('layout', 'Click here to learn about traceper'),
					));
						
					echo CHtml::link('<div id="logoMini"></div>', '#', array(
							'onclick'=>'$("#Logo").dialog("open"); return false;', 'class'=>'vtip', 'title'=>Yii::t('layout', 'Click here to learn about traceper'),
					));					
				}
				else
				{
					echo CHtml::link('<div id="logo"></div>', '#', array(
							'onclick'=>'$("#Logo").dialog("open"); return false;', 'class'=>'vtip', 'title'=>Yii::t('layout', 'Click here to learn about traceper'),
					));
					
					echo CHtml::link('<div id="logoMini" style="display:none"></div>', '#', array(
							'onclick'=>'$("#Logo").dialog("open"); return false;', 'class'=>'vtip', 'title'=>Yii::t('layout', 'Click here to learn about traceper'),
					));					
				}
				?>

				<div id="loginBlock"
				<?php
				if (Yii::app()->user->isGuest == false) {
					echo "style='display:none'";
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
							));

							$model = new LoginForm;
							?>

							<div class="upperMenu">
								<?php echo $form->labelEx($model,'email'); ?>
								<?php echo $form->textField($model,'email', array('size'=>'30%','maxlength'=>'30%','tabindex'=>1)); ?>
							</div>
							
							<div class="upperMenu" style="width:15%;">
								<?php echo $form->labelEx($model,'password'); ?>
								<?php echo $form->passwordField($model,'password', array('size'=>'30%','maxlength'=>'30%','tabindex'=>2)); ?>
							</div>							
														
							<div class="upperMenu" style="margin-top:0.8%;width:10.5em;">
								<div class="sideMenu" style="top:0%;padding:0px;">								
									<?php																											
									$this->widget('zii.widgets.jui.CJuiButton', array(
											'name'=>'ajaxLogin',
											'caption'=>Yii::t('site', 'Login'),
											'id'=>'loginAjaxButton',
											'htmlOptions'=>array('type'=>'submit','ajax'=>array('type'=>'POST','url'=>array('site/login'),'update'=>'#forAjaxRefresh'))
									));															
									?>
								</div>
								
								<div class="sideMenu" style="top:20%;padding:0px;display:inline;">
									<?php echo $form->checkBox($model,'rememberMe',array('size'=>5,'maxlength'=>128,'tabindex'=>3)); ?>
									<?php echo $form->label($model,'rememberMe'); ?>
								</div>																									
							</div>														

							<?php $this->endWidget(); ?>
						</div>						
					</div>
					
					<div class="upperMenu" style="margin-top:1em;width:16%;margin-left:1em;">
						<div class="sideMenu" style="top:0%;padding:0px;">
						<?php 
							$this->widget('zii.widgets.jui.CJuiButton', array(
									'name'=>'facebookLogin',
									'caption'=>Yii::t('layout', 'Sign in with Facebook'),
									'id'=>'facebookLoginWindow',
									'onclick'=>'function(){ '.
									CHtml::ajax(
											array(
													'url'=>array('site/facebooklogin'),
											)).
									' }',
							));
	 					?>
	 					</div>

	 					<div style="margin-left:10%;">
							<?php
							echo CHtml::ajaxLink('<div style="float:left" id="forgotPassword">'.Yii::t('site', 'Forgot Password?').
												'</div>', $this->createUrl('site/forgotPassword'),
									array(
											'complete'=> 'function() { $("#forgotPasswordWindow").dialog("open"); return false;}',
											'update'=> '#forgotPasswordWindow',
									),
									array(
											'id'=>'showForgotPasswordWindow','class'=>'vtip'));							
							?>	 					
	 					</div>
					</div>
					
					<div class="upperMenu" style="width:1%;padding:0px;margin-top:1em;">
						<a href="https://play.google.com/store/apps/details?id=com.yudu&feature=search_result#?t=W251bGwsMSwxLDEsImNvbS55dWR1Il0."><img src="images/Android_QR.png" onmouseover="this.src='images/QR_code.png'" onmouseout="this.src='images/Android_QR.png'" /></a>	
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

						echo CHtml::ajaxLink('<div class="userOperations" id="friendRequests">
								<img src="images/friends.png"  /><div></div>
								</div>', $this->createUrl('users/GetFriendRequestList'),
								array(
										'complete'=> 'function() { $("#friendRequestsWindow").dialog("open"); return false;}',
										'update'=> '#friendRequestsWindow',
								),
								array(
										'id'=>'showFriendRequestsWindow','class'=>'vtip', 'title'=>Yii::t('users', 'Friendship Requests')));
					}

					echo CHtml::ajaxLink('<div class="userOperations" id="createGroup">
							<img src="images/createGroup.png"  /><div></div>
							</div>', $this->createUrl('groups/createGroup'),
							array(
									'complete'=> 'function() { $("#createGroupWindow").dialog("open"); return false;}',
									'update'=> '#createGroupWindow',
							),
							array(
									'id'=>'showCreateGroupWindow','class'=>'vtip', 'title'=>Yii::t('layout', 'Create New Group')));


					echo CHtml::ajaxLink('<div class="userOperations" id="registerGPSTracker">
							<img src="images/registerGPSTracker.png"  /><div></div>
							</div>', $this->createUrl('site/registerGPSTracker'),
							array(
									'complete'=> 'function() { $("#registerGPSTrackerWindow").dialog("open"); return false;}',
									'update'=> '#registerGPSTrackerWindow',
							),
							array(
									'id'=>'showRegisterGPSTrackerWindow','class'=>'vtip', 'title'=>Yii::t('layout', 'Register GPS Tracker')));

					if(Yii::app()->params->featureStaffManagementEnabled)
					{
						echo CHtml::ajaxLink('<div class="userOperations" id="registerNewStaff">
								<img src="images/user_add_friend.png"  /><div></div>
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
				<div id="registerBlock"
				<?php
				if ((Yii::app()->user->isGuest == false) || ($passwordResetRequestStatus == PasswordResetStatus::RequestInvalid) || ($passwordResetRequestStatus == PasswordResetStatus::RequestValid)) {
					echo "style='display:none'";
				}
				?>>						
					<div id="forRegisterRefresh">
						<div class="form">
							<?php
							$form=$this->beginWidget('CActiveForm', array(
									'id'=>'register-form-main',
									'enableClientValidation'=>true,
							));

							$model = new RegisterForm;
							?>

							<div class="sideMenu" style="font-size: 3em;">
								<?php echo $form->labelEx($model,'register'); ?>
							</div>
							
							<div class="sideMenu">
								<?php echo $form->labelEx($model,'email'); ?>
								<?php echo $form->textField($model,'email', array('size'=>30,'maxlength'=>128)); ?>
							</div>

							<div class="sideMenu">
								<?php echo $form->labelEx($model,'name'); ?>
								<?php echo $form->textField($model,'name', array('size'=>30,'maxlength'=>128)); ?>
							</div>

							<div class="sideMenu">
								<?php echo $form->labelEx($model,'password'); ?>
								<?php echo $form->passwordField($model,'password', array('size'=>30,'maxlength'=>128)); ?>
							</div>

							<div class="sideMenu">
								<?php echo $form->labelEx($model,'passwordAgain'); ?>
								<?php echo $form->passwordField($model,'passwordAgain', array('size'=>30,'maxlength'=>128)); ?>
							</div>

							<div class="sideMenu">
								<?php
								//echo CHtml::ajaxSubmitButton(Yii::t('site','Register'), array('site/register'), array('update'=>'#forRegisterRefresh'), array('id'=>'registerAjaxButton','class'=>'ui-button ui-widget ui-state-default ui-corner-all','tabindex'=>4));
								
// 								echo CHtml::ajaxSubmitButton(Yii::t('site','Register'), array('site/register'),
// 										array('success'=>'function(data){
// 												$("#forRegisterRefresh").html(data);
// 											}'),
// 										array('id'=>'registerAjaxButton','class'=>'ui-button ui-widget ui-state-default ui-corner-all','tabindex'=>4));

								$this->widget('zii.widgets.jui.CJuiButton', array(
										'name'=>'ajaxRegister',
										'caption'=>Yii::t('site', 'Register'),
										'id'=>'registerAjaxButton',
										'htmlOptions'=>array('type'=>'submit','ajax'=>array('type'=>'POST','url'=>array('site/register'),'update'=>'#forRegisterRefresh'))
								));								
								?>
							</div>
							
							<div style="padding:1.5em;">
								<?php
								echo CHtml::ajaxLink('<div style="float:left" id="activationNotReceived">'.Yii::t('site', 'Not Received Our Activation E-Mail?').
													'</div>', $this->createUrl('site/activationNotReceived'),
										array(
												'complete'=> 'function() { $("#activationNotReceivedWindow").dialog("open"); return false;}',
												'update'=> '#activationNotReceivedWindow',
										),
										array(
												'id'=>'showActivationNotReceivedWindow','class'=>'vtip'));							
								?>
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
				if ((Yii::app()->user->isGuest == false) || ($passwordResetRequestStatus == PasswordResetStatus::NoRequest) || ($passwordResetRequestStatus == PasswordResetStatus::RequestInvalid)) {
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
							
							<div style="font-size:3em;padding:20px;">
								<?php echo $form->labelEx($model,'resetPassword'); ?>
							</div>							
							
							<div class="sideMenu">
								<?php echo $form->labelEx($model,'newPassword'); ?>
								<?php echo $form->passwordField($model,'newPassword', array('size'=>30,'maxlength'=>128)); ?>
							</div>

							<div class="sideMenu">
								<?php echo $form->labelEx($model,'newPasswordAgain'); ?>
								<?php echo $form->passwordField($model,'newPasswordAgain', array('size'=>30,'maxlength'=>128)); ?>
							</div>

							<div class="sideMenu">
								<?php
								$this->widget('zii.widgets.jui.CJuiButton', array(      
										'name'=>'ajaxResetPassword',
										'caption'=>Yii::t('site', 'Update'),
										'id'=>'resetPasswordAjaxButton',
										'htmlOptions'=>array('type'=>'submit','ajax'=>array('type'=>'POST','url'=>$this->createUrl('site/resetPassword', array('token'=>$token)), 'update'=>'#forPasswordResetRefresh'))
								));								
								?>
							</div>

							<?php $this->endWidget(); ?>
						</div>
					</div>
				</div>
				
				
				
				<div id="passwordResetInvalidBlock"
				<?php
				if ((Yii::app()->user->isGuest == false) || ($passwordResetRequestStatus == PasswordResetStatus::NoRequest) || ($passwordResetRequestStatus == PasswordResetStatus::RequestValid)) {
					echo "style='display:none'";
				}                                     
				?>>						
					<div id="forPasswordResetInvalidRefresh">
						<div class="form">							
							<div style="font-size:3em;padding:20px;color:#E41B17">
								<?php echo CHtml::label(Yii::t('site', 'Reset Your Password'), false); ?>
							</div>
							
							<div style="font-size:1em;padding:20px;color:#E41B17">
								<?php echo CHtml::label(Yii::t('site', 'This link is not valid anymore. Did you forget your password, please try to reset your password again.'), false); ?>
							</div>							
						</div>
							
							
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
							$tabs[Yii::t('layout', 'Users')]  = array('ajax' => $this->createUrl('users/getFriendList', array('userType'=>array(UserType::RealUser, UserType::GPSDevice))), 'id'=>'users_tab');
							//$tabs[Yii::t('layout', 'Users')]  = array('ajax' => $this->createUrl('users/getFriendList', array('userType'=>(UserType::RealUser Or UserType::GPSDevice))), 'id'=>'users_tab');
						}

						if(Yii::app()->params->featureStaffManagementEnabled)
						{
							$tabs[Yii::t('layout', 'Staff')]  = array('ajax' => $this->createUrl('users/getFriendList', array('userType'=>array(UserType::RealStaff, UserType::GPSStaff))), 'id'=>'staff_tab');
						}
						
						$tabs[Yii::t('layout', 'Photos')] = array('ajax' => $this->createUrl('upload/getList', array('fileType'=>0)), 'id'=>'photos_tab'); //0:image 'id'=>'photos_tab');
						$tabs[Yii::t('layout', 'Friend Groups')] = array('ajax' => $this->createUrl('groups/getGroupList', array('groupType'=>GroupType::FriendGroup)), 'id'=>'groups_tab');
						$tabs[Yii::t('layout', 'Staff Groups')] = array('ajax' => $this->createUrl('groups/getGroupList', array('groupType'=>GroupType::StaffGroup)), 'id'=>'staff_groups_tab');

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
								),
						));
					?>
					</div>
				</div>
			</div>
		</div>

	</div>
	<div id="map"></div>
	<div id='infoBottomBar'></div>
	<div id='loading'></div>
	</div>
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
	document.getElementById('topBar').style.height='7%';
	document.getElementById('sideBar').style.height='93%';
	document.getElementById('sideBar').style.top='7%';
	document.getElementById('sideBar').style.width='26%';
	document.getElementById('bar').style.top='7%';
	document.getElementById('bar').style.right='74%';
	document.getElementById('map').style.height='93%'; //$("#map").css('height', '94%');
	document.getElementById('map').style.width='74%';
</script>	
<?php	
	}
?>


