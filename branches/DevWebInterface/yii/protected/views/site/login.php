<?php 
$this->beginWidget('zii.widgets.jui.CJuiDialog', array(
	    'id'=>'userLoginWindow',
	    // additional javascript options for the dialog plugin
	    'options'=>array(
	        'title'=>Yii::t('general', 'Login'),
	        'autoOpen'=>false,
	        'modal'=>true, 
			'resizable'=>false      
	    ),
	));
?>
	<div class="form">
	<?php $form=$this->beginWidget('CActiveForm', array(
		'id'=>'login-form',
		'enableClientValidation'=>true,
	
	)); ?>
		<div class="row">
			<?php echo $form->labelEx($model,'email'); ?><br/>
			<?php echo $form->textField($model,'email'); ?>
			<?php echo $form->error($model,'email'); ?>
		</div>
	
		<div class="row">
			<?php echo $form->labelEx($model,'password'); ?><br/>
			<?php echo $form->passwordField($model,'password'); ?>
			<?php echo $form->error($model,'password'); ?>
		</div>
	
		<div class="row rememberMe">
			<?php echo $form->checkBox($model,'rememberMe'); ?>
			<?php echo $form->label($model,'rememberMe'); ?>
			<?php echo $form->error($model,'rememberMe'); ?>
		</div>
	
		<div class="row buttons">
			<?php echo CHtml::ajaxSubmitButton('Login', $this->createUrl('site/login'), 
												array(
													'success'=> 'function(result){ 
																	try {
																		var obj = jQuery.parseJSON(result);
																		if (obj.result && obj.result == "1") 
																		{
																			$("#username").html(obj.realname);
																			$("#loginBlock").hide();
																			$("#userBlock").show();
																			$("#userLoginWindow").dialog("close");
																		}
																	}
																	catch (error){
																		$("#userLoginWindow").html(result);
																	}
																 }',
													 ),
												null); ?>
		</div>
	
	<?php $this->endWidget(); ?>
</div>
<?php 
	$this->endWidget('zii.widgets.jui.CJuiDialog');
?>