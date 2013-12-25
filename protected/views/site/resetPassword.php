
<div class="form">
	<?php 
// 	$form=$this->beginWidget('CActiveForm', array(
// 			'id'=>'resetPassword-form',
// 			'enableClientValidation'=>true,
// 	));
	?>
	
	<?php $form=$this->beginWidget('CActiveForm', array(
			'id'=>'resetPassword-form',
			'enableClientValidation'=>true,
			'clientOptions'=> array(
					'validateOnSubmit'=> true,
					'validateOnChange'=>false,
			),
	)); 
	
	Yii::app()->clientScript->registerScript('resetPasswordTooltips',
			'$("#ResetPasswordForm_newPassword").tooltipster({
         	 theme: ".tooltipster-error",
			 position: "right",
         	 trigger: "custom",
         	 maxWidth: 540,
         	 onlyOne: false,
			 interactive: true,
         	 });	

			$("#ResetPasswordForm_newPasswordAgain").tooltipster({
         	 theme: ".tooltipster-error",
         	 position: "right",
         	 trigger: "custom",
         	 maxWidth: 540,
         	 onlyOne: false,
			 interactive: true,
         	 });
			',			
		 CClientScript::POS_HEAD);	
	?>	

	<div style="padding:9%;font-size:3em;">
		<?php echo $form->labelEx($model, 'resetPassword', array('style'=>'cursor:text;')); ?>
	</div>

	<div class="sideMenu" style="margin-left:2em;">
		<?php echo $form->labelEx($model,'newPassword'); ?>
		<?php echo $form->passwordField($model,'newPassword', array('size'=>'30%','maxlength'=>128,'tabindex'=>7)); ?>
		<?php $errorMessage = $form->error($model,'newPassword'); 
// 		if (strip_tags($errorMessage) == '') {
// 			echo '<div class="errorMessage">&nbsp;</div>';
// 		}
// 		else { echo '<div class="errorMessage" style="font-size: 1.1em;width:1000%;">'.$errorMessage.'</div>';
// 		}
		
		if (strip_tags($errorMessage) == '') {
			//echo '<div class="errorMessage">&nbsp;</div>';
		
			?>
			<script type="text/javascript">
				bResetPasswordFormNewPasswordErrorExists = false;
				$("#ResetPasswordForm_newPassword").tooltipster('hide');
			</script>
			<?php				
		}
		else { //echo '<div class="errorMessage" style="font-size: 1.1em;">'.$errorMessage.'</div>';
		
		?>
		<script type="text/javascript">
			bResetPasswordFormNewPasswordErrorExists = true;
            $("#ResetPasswordForm_newPassword").tooltipster('update', "<?php echo strip_tags($errorMessage); ?>");
            $("#ResetPasswordForm_newPassword").tooltipster('show');					
		</script>				
		<?php			
		}		
		?>
	</div>

	<div class="sideMenu" style="margin-left:2em;">
		<?php echo $form->labelEx($model,'newPasswordAgain'); ?>
		<?php echo $form->passwordField($model,'newPasswordAgain', array('size'=>'30%','maxlength'=>128,'tabindex'=>8)); ?>
		<?php $errorMessage = $form->error($model,'newPasswordAgain'); 
// 		if (strip_tags($errorMessage) == '') {
// 			echo '<div class="errorMessage">&nbsp;</div>';
// 		}
// 		else { echo '<div class="errorMessage" style="font-size: 1.1em;width:1000%;">'.$errorMessage.'</div>';
// 		}
		
		if (strip_tags($errorMessage) == '') {
			//echo '<div class="errorMessage">&nbsp;</div>';
		
			?>
			<script type="text/javascript">
				bResetPasswordFormNewPasswordAgainErrorExists = false;
				$("#ResetPasswordForm_newPasswordAgain").tooltipster('hide');
			</script>
			<?php				
		}
		else { //echo '<div class="errorMessage" style="font-size: 1.1em;">'.$errorMessage.'</div>';
		
		?>
		<script type="text/javascript">
			bResetPasswordFormNewPasswordAgainErrorExists = true;
            $("#ResetPasswordForm_newPasswordAgain").tooltipster('update', "<?php echo strip_tags($errorMessage); ?>");
            $("#ResetPasswordForm_newPasswordAgain").tooltipster('show');					
		</script>				
		<?php			
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
				'id'=>'resetPasswordAjaxButton-'.uniqid(), //Unique ID oluşturmayınca her ajaxta bir önceki sorgular da tekrarlanıyor
				'htmlOptions'=>array('type'=>'submit','tabindex'=>9,'ajax'=>array('type'=>'POST','url'=>$this->createUrl('site/resetPassword', array('token'=>$token)),
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
																												$("#showRegisterFormLink").tooltipster("update", "'.Yii::t('layout', 'Click here to view the registration form again').'");
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
