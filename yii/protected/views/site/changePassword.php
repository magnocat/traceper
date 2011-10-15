<?php 
$this->beginWidget('zii.widgets.jui.CJuiDialog', array(
	    'id'=>'changePasswordWindow',
	    // additional javascript options for the dialog plugin
	    'options'=>array(
	        'title'=>Yii::t('general', 'Change Password'),
	        'autoOpen'=>false,
	        'modal'=>true, 
			'resizable'=>false      
	    ),
	));
?>
	<div class="form">
	<?php $form=$this->beginWidget('CActiveForm', array(
		'id'=>'changePassword-form',
		'enableClientValidation'=>true,
	
	)); ?>
		<div class="row">
			<?php echo $form->labelEx($model,'currentPassword'); ?><br/>
			<?php echo $form->passwordField($model,'currentPassword'); ?>
			<?php echo $form->error($model,'currentPassword'); ?>
		</div>
	
		<div class="row">
			<?php echo $form->labelEx($model,'newPassword'); ?><br/>
			<?php echo $form->passwordField($model,'newPassword'); ?>
			<?php echo $form->error($model,'newPassword'); ?>
		</div>
	
		<div class="row">
			<?php echo $form->labelEx($model,'newPasswordAgain'); ?>
			<?php echo $form->passwordField($model,'newPasswordAgain'); ?>
			<?php echo $form->error($model,'newPasswordAgain'); ?>
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
																		}
																	}
																	catch (error){
																		$("#changePasswordWindow").html(result);
																	}
																 }',
													 ),
												null); ?>
												
			<?php echo CHtml::ajaxSubmitButton('Cancel', $this->createUrl('site/changePassword'), 
												array(
													'onclick'=> 'function(result){ 
																	$("#changePasswordWindow").dialog("close") return false;
																 }',
													 ),
												null); ?>												
		</div>
	
	<?php $this->endWidget(); ?>
</div>
<?php 
	$this->endWidget('zii.widgets.jui.CJuiDialog');
?>