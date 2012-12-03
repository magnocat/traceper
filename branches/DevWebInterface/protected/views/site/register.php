
<div class="form">
	<?php $form=$this->beginWidget('CActiveForm', array(
			'id'=>'register-form',
			'enableClientValidation'=>true,

	)); ?>

	
	<div class="sideMenu">
		<?php echo $form->labelEx($model,'email'); ?>
		<?php echo $form->textField($model,'email', array('size'=>20,'maxlength'=>128,'tabindex'=>2)); ?>
		<?php $errorMessage = $form->error($model,'email'); 
		if (strip_tags($errorMessage) == '') {
			echo '<div class="errorMessage">&nbsp;</div>';
		}
		else { echo $errorMessage;
		}
		?>
	</div>

	<div class="sideMenu">
		<?php echo $form->labelEx($model,'name'); ?>
		<?php echo $form->textField($model,'name', array('size'=>20,'maxlength'=>128,'tabindex'=>2)); ?>
		<?php $errorMessage = $form->error($model,'name');  
		if (strip_tags($errorMessage) == '') {
			echo '<div class="errorMessage">&nbsp;</div>';
		}
		else { echo $errorMessage;
		}
		?>
	</div>

	<div class="sideMenu">
		<?php echo $form->labelEx($model,'password'); ?>
		<?php echo $form->passwordField($model,'password', array('size'=>20,'maxlength'=>128,'tabindex'=>2)); ?>
		<?php $errorMessage = $form->error($model,'password'); 
		if (strip_tags($errorMessage) == '') {
			echo '<div class="errorMessage">&nbsp;</div>';
		}
		else { echo $errorMessage;
		}
		?>
	</div>

	<div class="sideMenu">
		<?php echo $form->labelEx($model,'passwordAgain'); ?>
		<?php echo $form->passwordField($model,'passwordAgain', array('size'=>20,'maxlength'=>128,'tabindex'=>2)); ?>
		<?php $errorMessage = $form->error($model,'passwordAgain'); 
		if (strip_tags($errorMessage) == '') {
			echo '<div class="errorMessage">&nbsp;</div>';
		}
		else { echo $errorMessage;
		}
		?>
	</div>
	
	<div class="sideMenu">
		<?php echo CHtml::ajaxSubmitButton(Yii::t('site','Register'), Yii::app()->createUrl('site/register'), 
				array(
						'success'=> 'function(result){
						try {
							var obj = jQuery.parseJSON(result);
							if (obj.result && obj.result == "1")
							{
								TRACKER.showMessageDialog("'.Yii::t('site', 'An activation mail is sent to your e-mail address...').'");
							}
						}
						catch (error){
						/*
						$("#registerWindow").html(result);
						var confirmMessage = document.getElementById("messageWindow");
						if(confirmMessage.style.display != "block") {
						confirmMessage.style.display = "none";
						*/
}
}
}',
				),
												array('size'=>20,'maxlength'=>128,'tabindex'=>4)); ?>

	</div>

	<?php $this->endWidget(); ?>
</div>