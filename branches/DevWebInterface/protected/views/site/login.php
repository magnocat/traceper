	<div class="form">
	<?php $form=$this->beginWidget('CActiveForm', array(
		'id'=>'login-form',
		'enableClientValidation'=>true,
	
	)); ?>
		
		<div class="upperMenu" style="margin-top:0.8em;width:11%;">
			<?php 
				//echo CHtml::submitButton('Login');
				echo CHtml::ajaxSubmitButton(Yii::t('site','Login'), Yii::app()->createUrl('site/login'), 
												array(),
												array('class'=>'ui-button ui-widget ui-state-default ui-corner-all','role'=>'button','tabindex'=>4)); 
				?>
		</div>
		
		<div class="upperMenu" style="margin-top:1.5em;width:10%;">
			<?php echo $form->checkBox($model,'rememberMe',array('size'=>5,'maxlength'=>128,'tabindex'=>3)); ?>
			<?php echo $form->label($model,'rememberMe'); ?>
			<?php echo $form->error($model,'rememberMe'); ?>
		</div>	
		
		<div class="upperMenu">
			<?php echo $form->labelEx($model,'password'); ?>
			<?php echo $form->passwordField($model,'password', array('size'=>25,'maxlength'=>128,'tabindex'=>2)); ?>
			<?php $errorMessage = $form->error($model,'password'); 
				  if (strip_tags($errorMessage) == '') { echo '<div class="errorMessage">&nbsp;</div>'; }
				  else { echo $errorMessage; }
			?>
		</div>	
		
		<div class="upperMenu">
			<?php echo $form->labelEx($model,'email'); ?>
			<?php echo $form->textField($model,'email', array('size'=>25,'maxlength'=>128,'tabindex'=>1)); ?>
			<?php $errorMessage = $form->error($model,'email'); 
				  if (strip_tags($errorMessage) == '') { echo '<div class="errorMessage">&nbsp;</div>'; }
				  else { echo $errorMessage; }
			?>			
		</div>				
	
	<?php $this->endWidget(); ?>
</div>
