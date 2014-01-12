
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
	?>
	
		<div id="ajaxPasswordResetResponse">
			<!-- Ajax cevabinda tooltiplerin calismasi icin bu script'in Yii::app()->clientScript->registerScript() ie degil, -->
			<!-- direk <div id="ajaxLoginResponse"> tag'inib icinde gonderilmesi gerekiyor, buton ajaxla yeniden yuklenmeyince  -->
			<!-- multiple ajax problemi de olmadigindan uniqid() kullanmaya da gerek kalmiyor.  -->
			<script type="text/javascript">
				$("#ResetPasswordForm_newPassword").tooltipster({
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
			</script>		

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

			<div class="sideMenu" style="margin-left:2em;padding-top:20px;">
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
		</div>
	<?php $this->endWidget(); ?>
</div>
