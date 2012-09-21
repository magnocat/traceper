<?php 
$this->beginWidget('zii.widgets.jui.CJuiDialog', array(
	    'id'=>'changePasswordWindow',
	    // additional javascript options for the dialog plugin
	    'options'=>array(
	        'title'=>Yii::t('general', 'Change Password'),
	        'autoOpen'=>false,
	        'modal'=>true, 
			'resizable'=>false,
			'width'=> '280px'      
	    ),
	));
?>
	<div class="form">
	<?php $form=$this->beginWidget('CActiveForm', array(
		'id'=>'changePassword-form',
		'enableClientValidation'=>true,
		'clientOptions'=> array(
							'validateOnSubmit'=> true,
							'validateOnChange'=>false,
						 ),
	
	)); ?>
	

		<div class="row">
			<?php echo $form->labelEx($model,'currentPassword'); ?>
			<?php echo $form->passwordField($model,'currentPassword'); ?>
			<?php $errorMessage = $form->error($model,'currentPassword'); 
				  if (strip_tags($errorMessage) == '') { echo '<div class="errorMessage">&nbsp;</div>'; }
				  else { echo $errorMessage; }
			?>
		</div>
	
		<div class="row">
			<?php echo $form->labelEx($model,'newPassword'); ?>
			<?php echo $form->passwordField($model,'newPassword'); ?>
			<?php $errorMessage = $form->error($model,'newPassword');  
				  if (strip_tags($errorMessage) == '') { echo '<div class="errorMessage">&nbsp;</div>'; }
				  else { echo $errorMessage; }
			?>	  			
		</div>
	
		<div class="row">
			<?php echo $form->labelEx($model,'newPasswordAgain'); ?>
			<?php echo $form->passwordField($model,'newPasswordAgain'); ?>
			<?php $errorMessage = $form->error($model,'newPasswordAgain'); 
				  if (strip_tags($errorMessage) == '') { echo '<div class="errorMessage">&nbsp;</div>'; }
				  else { echo $errorMessage; }	
			?>	  		
		</div>
	
		<div class="row buttons">
			<?php echo CHtml::ajaxSubmitButton('Submit', $this->createUrl('site/changePassword'), 
												array(
													'success'=> 'function(result){ 
																	try {
																		var obj = jQuery.parseJSON(result);
																		if (obj.result && obj.result == "1") 
																		{
																			$("#changePasswordWindow").dialog("close");
																			TRACKER.showMessageDialog("'.Yii::t('site', 'Password has been changed...').'");
																		}
																	}
																	catch (error){
																		$("#changePasswordWindow").html(result);
																	}
																 }',
													 ),
												null); ?>
												
			<?php echo CHtml::htmlButton('Cancel',  
												array(
													'onclick'=> '$("#changePasswordWindow").dialog("close"); return false;',
													 ),
												null); ?>												
		</div>
	
	<?php $this->endWidget(); ?>
</div>
<?php 
	$this->endWidget('zii.widgets.jui.CJuiDialog');
?>