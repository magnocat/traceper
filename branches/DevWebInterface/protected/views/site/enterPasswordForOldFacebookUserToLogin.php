<?php 
$this->beginWidget('zii.widgets.jui.CJuiDialog', array(
	    'id'=>'enterPasswordForOldFacebookUserToLoginWindow',
	    // additional javascript options for the dialog plugin
	    'options'=>array(
	        'title'=>Yii::t('site', 'Enter Your Traceper Password to Log In'),
	        'autoOpen'=>false,
	        'modal'=>true, 
			'resizable'=>false,
			'width'=> '600px',
	    	//'close' => 'js:function(){ showFormErrorsIfExist(); }'
	    ),
	));
?>
	<div class="form">
	<?php $form=$this->beginWidget('CActiveForm', array(
		'id'=>'enterPasswordForOldFacebookUserToLogin-form',
		'enableClientValidation'=>true,
		'clientOptions'=> array(
							'validateOnSubmit'=> true,
							'validateOnChange'=>false,
						 ),
	
	)); ?>
	
		<div id="ajaxEnterPasswordForOldFacebookUserToLoginResponse">
			<div class="row" style="padding-top:1em;">
				<?php echo Yii::t('site', 'We have updated our web site and mobile application. Henceforth, you could directly login with your Facebook account. But for this, your mobile app has to be up-to-date. Since you have not updated your mobile app yet, we require you to enter your Traceper password. After your app is updated, you will not have to enter any password for Facebook login.'); ?>
			</div>		

			<div class="row">
				<?php echo $form->labelEx($model,'password'); ?>
				<?php echo $form->passwordField($model,'password', array('size'=>'50%','maxlength'=>128,'tabindex'=>1));?>
				<?php $errorMessage = $form->error($model,'password'); 
					  if (strip_tags($errorMessage) == '') { echo '<div class="errorMessage">&nbsp;</div>'; }
					  else { echo $errorMessage; }
				?>
			</div>
		</div>	
	
		<div class="row buttons">
			<?php
			$app = Yii::app();
				
			echo CHtml::ajaxLink('<button class="btn btn-sliding-green btn-sliding-green-a icon-login1" style="'.(($app->language == 'en')?'padding-left:28px;padding-right:28px;':'padding-left:28px;padding-right:28px;').'">'.'<span style="font-family:Helvetica">'.Yii::t('site', 'Log in').'</span>'.'</button>', $this->createUrl('site/oldFacebookUserLogin'),
					array(
							'type'=>'POST',
							'success'=> 'function(msg){
											try
											{
												var obj = jQuery.parseJSON(msg);
											
												if (obj.result)
												{
													if (obj.result == "1") //Login successful
													{
														$("#enterPasswordForOldFacebookUserToLoginWindow").dialog("close");

														$("#tabViewList").html(obj.renderedTabView);
														$("#userarea").html(obj.renderedUserAreaView);
														$("#FriendRequestsIconLink").html(obj.renderedFriendshipRequestsView);
														$("#loginBlock").html(obj.loginSuccessfulActions);							
													}
													else if (obj.result == "0") //Login error
													{
														$("#enterPasswordForOldFacebookUserToLoginWindow").dialog("close");
														TRACKER.showMessageDialog("'.Yii::t('site', 'An error occured during login. Please retry the process and if the error persists please contact us.').'");
													}
													else
													{
														//alert("else");
													}
												}
											}
											catch (error)
											{
												//$("#enterPasswordForOldFacebookUserToLoginWindow").html(msg);
							
												$("#hiddenAjaxResponseForEnterPasswordForOldFacebookUserToLogin").html(msg);
												$("#ajaxEnterPasswordForOldFacebookUserToLoginResponse").html($("#hiddenAjaxResponseForEnterPasswordForOldFacebookUserToLogin #ajaxEnterPasswordForOldFacebookUserToLoginResponse").html());
												$("#hiddenAjaxResponseForEnterPasswordForOldFacebookUserToLogin").html("");
											}
										}',
					),
					array('id'=>'enterPasswordForOldFacebookUserToLoginAjaxButton-'.uniqid(), 'style'=>'padding-right:4px;'));			
			?>
												
			<?php 
				echo CHtml::ajaxLink('<button class="btn btn-sliding-red btn-sliding-red-a icon-close" style="'.(($app->language == 'en')?'padding-left:25px;padding-right:25px;':'padding-left:28px;padding-right:28px;').'">'.'<span style="font-family:Helvetica">'.Yii::t('common', 'Cancel').'</span>'.'</button>', '#',
						array(),
						array('id'=>'enterPasswordForOldFacebookUserToLoginCancelButton', 'onclick'=>'$("#enterPasswordForOldFacebookUserToLoginWindow").dialog("close"); return false;'));				
			?>												
		</div>
	
	<?php $this->endWidget(); ?>
</div>
<?php 
	$this->endWidget('zii.widgets.jui.CJuiDialog');
?>

<!-- Diyaloglarda main layout'taki hiddenAjaxResponseToParse kullanilamadigindan (diyaloglar dinamik olarak sonradan eklendiginden yukarida -->
<!-- kaliyor) ve ayni isimle olunca da calismadigindan diyaloglarin view dosyalarinin sonuna gizli bir div tanimlaniyor -->
<div id="hiddenAjaxResponseForEnterPasswordForOldFacebookUserToLogin" style="display:none;"></div>