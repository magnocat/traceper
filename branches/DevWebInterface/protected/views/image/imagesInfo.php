<?php 
	if (isset($dataProvider)) {
		$this->renderPartial('getList', array('dataProvider'=>$dataProvider), false, true);
	}
	
	echo '<div id="imageSearchResults"></div>';
?>

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
												'complete'=> 'function() { $("#imageSearchResults").dialog("open"); return false;}',
												'update'=> '#imageSearchResults',
												 ),
											null);?>
		<?php echo $form->error($model,'keyword'); 	?>
	</div>

	<div class="row buttons">
		<?php  ?>
												
	</div>

<?php $this->endWidget(); ?>
</div>