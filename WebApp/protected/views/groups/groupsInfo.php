
<?php if (Yii::app()->user->isGuest == false) { ?>
	<div class="form">
	<?php $form=$this->beginWidget('CActiveForm', array(
		'id'=>'searchGroup-form',
		'enableClientValidation'=>true,
		'clientOptions'=> array(
							'validateOnSubmit'=> true,
							'validateOnChange'=>false,
						 ),
	
		));
		 ?>
		<div class="row">
			<?php // echo $form->labelEx($model,'keyword'); ?>
			<?php echo $form->textField($model,'keyword', array('class'=>'searchBox')); ?>
			<?php 
				
				echo CHtml::ajaxSubmitButton('Search', $this->createUrl('groups/search'), 
												array(
													'complete'=> 'function() { $("#groupSearchResults").dialog("open"); return false;}',
													'update'=> '#groupSearchResults',
													 ),
												array(
													'id'=>'searchGroupButton',
												));
			
			
			?>
			<?php echo $form->error($model,'keyword'); 	?>
		</div>
	<?php $this->endWidget(); ?>
	</div>
<?php }?>
<?php 
	if (isset($dataProvider)) {
		$this->renderPartial('groupList', array('dataProvider'=>$dataProvider), false, true);
	}
	else
	{
		echo 'No groups to show... <br/> <br/>';				
	}
	
	echo '<div id="groupSearchResults"></div>';
?>