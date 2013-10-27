
<div class="form">
	<?php $form=$this->beginWidget('CActiveForm', array(
			'id'=>'resetPassword-form',
			'enableClientValidation'=>true,
	)); ?>

	<div style="padding:9%;font-size:3em;">
		<?php echo $form->labelEx($model,'resetPassword'); ?>
	</div>

	<div class="sideMenu" style="margin-left:2em;">
		<?php echo $form->labelEx($model,'newPassword'); ?>
		<?php echo $form->passwordField($model,'newPassword', array('size'=>'30%','maxlength'=>128,'tabindex'=>7)); ?>
		<?php $errorMessage = $form->error($model,'newPassword'); 
		if (strip_tags($errorMessage) == '') {
			echo '<div class="errorMessage">&nbsp;</div>';
		}
		else { echo '<div class="errorMessage" style="font-size: 1.1em;width:1000%;">'.$errorMessage.'</div>';
		}
		?>
	</div>

	<div class="sideMenu" style="margin-left:2em;">
		<?php echo $form->labelEx($model,'newPasswordAgain'); ?>
		<?php echo $form->passwordField($model,'newPasswordAgain', array('size'=>'30%','maxlength'=>128,'tabindex'=>8)); ?>
		<?php $errorMessage = $form->error($model,'newPasswordAgain'); 
		if (strip_tags($errorMessage) == '') {
			echo '<div class="errorMessage">&nbsp;</div>';
		}
		else { echo '<div class="errorMessage" style="font-size: 1.1em;width:1000%;">'.$errorMessage.'</div>';
		}
		?>
	</div>

	<div class="sideMenu" style="margin-left:2em;">
		<?php
// 		$this->widget('zii.widgets.jui.CJuiButton', array(
// 				'name'=>'ajaxResetPassword',
// 				'caption'=>Yii::t('site', 'Update'),
// 				'id'=>'resetPasswordAjaxButton',
// 				'htmlOptions'=>array('type'=>'submit','tabindex'=>9,'ajax'=>array('type'=>'POST','url'=>$this->createUrl('site/resetPassword', array('token'=>$token)), 'update'=>'#forPasswordResetRefresh'))
// 		));
		
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
