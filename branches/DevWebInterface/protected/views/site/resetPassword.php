
<div class="form">
	<?php $form=$this->beginWidget('CActiveForm', array(
			'id'=>'resetPassword-form',
			'enableClientValidation'=>true,
	)); ?>

	<div style="font-size:3em;padding:20px;">
		<?php echo $form->labelEx($model,'resetPassword'); ?>
	</div>

	<div class="sideMenu">
		<?php echo $form->labelEx($model,'newPassword'); ?>
		<?php echo $form->passwordField($model,'newPassword', array('size'=>'30%','maxlength'=>128,'tabindex'=>2)); ?>
		<?php $errorMessage = $form->error($model,'newPassword'); 
		if (strip_tags($errorMessage) == '') {
			echo '<div class="errorMessage">&nbsp;</div>';
		}
		else { echo '<div class="errorMessage" style="font-size: 1.1em;width:1000%;">'.$errorMessage.'</div>';
		}
		?>
	</div>

	<div class="sideMenu">
		<?php echo $form->labelEx($model,'newPasswordAgain'); ?>
		<?php echo $form->passwordField($model,'newPasswordAgain', array('size'=>'30%','maxlength'=>128,'tabindex'=>2)); ?>
		<?php $errorMessage = $form->error($model,'newPasswordAgain'); 
		if (strip_tags($errorMessage) == '') {
			echo '<div class="errorMessage">&nbsp;</div>';
		}
		else { echo '<div class="errorMessage" style="font-size: 1.1em;width:1000%;">'.$errorMessage.'</div>';
		}
		?>
	</div>

	<div class="sideMenu">
		<?php
		$this->widget('zii.widgets.jui.CJuiButton', array(
				'name'=>'ajaxResetPassword',
				'caption'=>Yii::t('site', 'Update'),
				'id'=>'resetPasswordAjaxButton',
				'htmlOptions'=>array('type'=>'submit','ajax'=>array('type'=>'POST','url'=>$this->createUrl('site/resetPassword', array('token'=>$token)), 'update'=>'#forPasswordResetRefresh'))
		));
		?>
	</div>
	<?php $this->endWidget(); ?>
</div>
