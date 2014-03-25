<?php 
$this->beginWidget('zii.widgets.jui.CJuiDialog', array(
	    'id'=>'yourEmailSeemsToBeInvalidWindow',
	    // additional javascript options for the dialog plugin
	    'options'=>array(
	        'title'=>Yii::t('site', 'Your E-mail Seems To Be Invalid'),
	        'autoOpen'=>false,
	        'modal'=>true, 
			'resizable'=>false,
			'width'=> '600px',
	    	//'close' => 'js:function(){ showFormErrorsIfExist(); }'
	    ),
	));
?>

<div>
	<?php $this->beginWidget('CActiveForm', array(
		'id'=>'yourEmailSeemsToBeInvalid-form',
		'enableClientValidation'=>true,
		'clientOptions'=> array(
							'validateOnSubmit'=> true,
							'validateOnChange'=>false,
						 ),
		'htmlOptions'=>array(),					
	)); ?>
	
	<div class="row" style="padding-top:2em;padding-bottom:1em;">
		<?php
		echo Yii::t('site', 'We think that you might have misentered the domain part of your e-mail address. If so, you could select the first option to correct it as shown and click the "OK" button continue with the corrected address. If not, you could select the second option to continue with the current address. Lastly, you could just close this dialog window and return back to the sign up form by clicking the "Cancel" button in case none of these options suit you:');
		?>
	</div>
	
	<div class="row" style="padding-top:2em;padding-bottom:1em;">
		<?php
		echo CHtml::radioButtonList('registerEmail','answer',
									array($correctedEmail=>Yii::t('site', 'Correct as <b>{email}</b> and continue', array('{email}'=>$correctedEmail)),
										  $currentEmail=>Yii::t('site', 'Continue with <b>{email}</b>', array('{email}'=>$currentEmail))), 
									array('separator' => "<br/><br/>"));
		?>
	</div>	

	</br>
	
	<?php
	$app = Yii::app();
	?>

	<div class="row buttons" style="padding-bottom:1em;">
		<?php	
		echo CHtml::ajaxLink('<button class="btn btn-sliding-green btn-sliding-green-a icon-arrow-right" style="'.(($app->language == 'en')?'padding-left:28px;padding-right:28px;':'padding-left:28px;padding-right:28px;').'">'.'<span style="font-family:Helvetica">'.Yii::t('common', 'OK').'</span>'.'</button>', $this->createUrl('site/continueRegister', array('RegisterForm'=>$form, 'preferredLanguage'=>$preferredLanguage, 'mobileLang'=>$mobileLang, 'isMobile'=>$isMobile)),
				array(
						'type'=>'POST',
						'success'=>'function(msg){
										try
										{
											var obj = jQuery.parseJSON(msg);
										
											if (obj.result)
											{
												$("#forRegisterRefresh").html(obj.registerView);
						
												if (obj.result == "1")
												{
													$("#yourEmailSeemsToBeInvalidWindow").dialog("close");						
													TRACKER.showLongMessageDialog("'.Yii::t('site', 'Your account created successfully. ').Yii::t('site', 'We have sent an account activation link to your mail address \"<b>').'" + obj.email + "'.Yii::t('site', '</b>\". </br></br>Please make sure you check the spam/junk folder as well. The links in a spam/junk folder may not work sometimes; so if you face such a case, mark our e-mail as \"Not Spam\" and reclick the link.').'", "'.Yii::app()->homeUrl.'");
												}
												else if (obj.result == "2")
												{
													$("#yourEmailSeemsToBeInvalidWindow").dialog("close");
													TRACKER.showLongMessageDialog("'.Yii::t('site', 'Your account created successfully, but an error occured while sending your account activation e-mail. You could request your activation e-mail by clicking the link \"Not Received Our Activation E-Mail?\" just below the register form. If the error persists, please contact us about the problem.').'", "'.Yii::app()->homeUrl.'");
												}
												else if (obj.result == "0")
												{
													$("#yourEmailSeemsToBeInvalidWindow").dialog("close");
													TRACKER.showMessageDialog("'.Yii::t('site', 'An error occured during sign up. Please retry the process and if the error persists please contact us.').'");
												}
												else if (obj.result == "-1")
												{
													TRACKER.showMessageDialog("'.Yii::t('site', 'Please select an option before clicking \"OK\" button').'");
												}												
												else
												{
													$("#yourEmailSeemsToBeInvalidWindow").dialog("close");
													TRACKER.showMessageDialog("'.Yii::t('site', 'An error occured during sign up. Please retry the process and if the error persists please contact us.').'");
												}													
											}
										}
										catch (error)
										{
											alert("Exception catched in yourEmailSeemsToBeInvalidWindow");
										}
									}',
				),
				array('id'=>'continueRegisterAjaxButtonWithSelectedEmail-'.uniqid(), 'style'=>'padding-right:4px;'));		
		?>									

		<?php
		echo CHtml::ajaxLink('<button class="btn btn-sliding-red btn-sliding-red-a icon-close" style="'.(($app->language == 'en')?'padding-left:25px;padding-right:25px;':'padding-left:28px;padding-right:28px;').'">'.'<span style="font-family:Helvetica">'.Yii::t('common', 'Cancel').'</span>'.'</button>', '#',
				array(),
				array('id'=>'cancelContinueRegisterButton', 'onclick'=>'$("#yourEmailSeemsToBeInvalidWindow").dialog("close"); return false;'));			
		?>	
	</div>
	<?php $this->endWidget(); ?>
</div>	
<?php 
	$this->endWidget('zii.widgets.jui.CJuiDialog');
?>
