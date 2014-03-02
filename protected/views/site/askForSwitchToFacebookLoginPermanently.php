<?php 
$this->beginWidget('zii.widgets.jui.CJuiDialog', array(
	    'id'=>'askForSwitchToFacebookLoginPermanentlyWindow',
	    // additional javascript options for the dialog plugin
	    'options'=>array(
	        'title'=>Yii::t('site', 'Do you want to switch to Facebook login permanently?'),
	        'autoOpen'=>false,
	        'modal'=>true, 
			'resizable'=>false,
			'width'=> '600px',
	    	//'close' => 'js:function(){ showFormErrorsIfExist(); }'
	    ),
	));
?>
	<div class="row" style="padding-top:2em;padding-bottom:1em;">
	<?php echo Yii::t('site', 'You have already signed up for our service as a Traceper user with this e-mail address. If you continue to log in with your Facebook account, we will update your Traceper account as Facebook user and henceforth you will have to log in with Facebook for your next logins. If you do not want to change your account as Facebook login, please close this dialog by "Cancel" button and use the regular login form.'); ?>
	</div>	
	
	</br>	

	<div class="row buttons" style="padding-bottom:1em;">
		<?php
		$app = Yii::app();
			
		echo CHtml::ajaxLink('<button class="btn btn-sliding-green btn-sliding-green-a icon-login1" style="'.(($app->language == 'en')?'padding-left:28px;padding-right:28px;':'padding-left:28px;padding-right:28px;').'">'.'<span style="font-family:Helvetica">'.Yii::t('site', 'I agree, continue').'</span>'.'</button>', $this->createUrl('site/switchToFacebookLoginPermanently', array()),
				array(
						'type'=>'POST',
						'success'=>'function(msg){
										try
										{
											var obj = jQuery.parseJSON(msg);
										
											if (obj.result)
											{
												if (obj.result == "1")
												{
													$("#askForSwitchToFacebookLoginPermanentlyWindow").dialog("close");
						
													$("#tabViewList").html(obj.renderedTabView);
													$("#userarea").html(obj.renderedUserAreaView);
													$("#FriendRequestsIconLink").html(obj.renderedFriendshipRequestsView);
													$("#loginBlock").html(obj.loginSuccessfulActions);
						
													TRACKER.showLongMessageDialog("'.Yii::t('site', 'Your account has been updated for Facebook login permanently. From now on, you could use this e-mail address only with Facebook login to log in to your Traceper account...').'");
												}
												else if (obj.result == "0")
												{
													$("#askForSwitchToFacebookLoginPermanentlyWindow").dialog("close");
													TRACKER.showMessageDialog("'.Yii::t('site', 'An error occured during login. Please retry the process and if the error persists please contact us.').'");
												}
												else if (obj.result == "-1")
												{
													$("#askForSwitchToFacebookLoginPermanentlyWindow").dialog("close");
													TRACKER.showMessageDialog("'.Yii::t('site', 'An error occured during register process. Please retry the process and if the error persists please contact us.').'");
												}
												else
												{
													$("#askForSwitchToFacebookLoginPermanentlyWindow").dialog("close");
													TRACKER.showMessageDialog("'.Yii::t('site', 'An error occured during login. Please retry the process and if the error persists please contact us.').'");
												}																		
											}
										}
										catch (error)
										{
											alertMsg("askForSwitchToFacebookLoginPermanently - error occured");
										}
									}',
				),
				array('id'=>'continueSwitchToFacebookLoginAjaxButton-'.uniqid(), 'style'=>'padding-right:4px;'));		
		?>
											
		<?php
			echo CHtml::ajaxLink('<button class="btn btn-sliding-red btn-sliding-red-a icon-close" style="'.(($app->language == 'en')?'padding-left:25px;padding-right:25px;':'padding-left:28px;padding-right:28px;').'">'.'<span style="font-family:Helvetica">'.Yii::t('common', 'Cancel').'</span>'.'</button>', '#',
					array(),
					array('id'=>'cancelSwitchToFacebookLoginButton', 'onclick'=>'$("#askForSwitchToFacebookLoginPermanentlyWindow").dialog("close"); return false;'));			
		?>												
	</div>
<?php 
	$this->endWidget('zii.widgets.jui.CJuiDialog');
?>
