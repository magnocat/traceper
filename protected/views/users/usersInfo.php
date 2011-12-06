<?php 
	if (isset($dataProvider)) {
		$this->renderPartial('userList', array('dataProvider'=>$dataProvider), false, true);
	}
	
	echo '<div id="userSearchResults"></div>';
?>

<div class="form">
<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'searchUser-form',
	'enableClientValidation'=>true,
	'clientOptions'=> array(
						'validateOnSubmit'=> true,
						'validateOnChange'=>true,
					 ),

	));
	 ?>

	<div class="row">
		<?php echo $form->labelEx($model,'keyword'); ?>
		<?php echo $form->textField($model,'keyword', array('size'=>15)); ?>
		<?php echo CHtml::ajaxSubmitButton('Submit', $this->createUrl('users/search'), 
											array(
												'complete'=> 'function() { $("#userSearchResults").dialog("open"); return false;}',
												'update'=> '#userSearchResults',
												 ),
											null);?>
		<?php echo $form->error($model,'keyword'); 	?>
	</div>

	<div class="row buttons">
		<?php  ?>
												
	</div>

<?php $this->endWidget(); ?>
</div>