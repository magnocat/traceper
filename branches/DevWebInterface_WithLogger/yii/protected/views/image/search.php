<div class="form">
<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'searchImage-form',
	'enableClientValidation'=>true,
	'clientOptions'=> array(
						'validateOnSubmit'=> true,
						'validateOnChange'=>true,
					 ),

	));
	 ?>


	<div class="row">
		<?php echo $form->labelEx($model,'keyword'); ?>
		<?php echo $form->textField($model,'keyword'); ?>
		<?php echo CHtml::ajaxSubmitButton('Submit', $this->createUrl('image/search'), 
											array(
												'update'=> '#imageSearch',
												 ),
											null);?>
		<?php echo $form->error($model,'keyword'); 	?>
	</div>

<?php $this->endWidget(); ?>
</div>

<?php 
	if (isset($dataProvider)) {
		$this->renderPartial('getList', array('dataProvider'=>$dataProvider), false, true);
	}
?>