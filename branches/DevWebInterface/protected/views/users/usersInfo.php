
<?php if (Yii::app()->user->isGuest == false) { ?>
	<div class="form">
	<?php $form=$this->beginWidget('CActiveForm', array(
		'id'=>'searchUser-form',
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
				
				echo CHtml::ajaxSubmitButton(Yii::t('layout', 'Search'), $this->createUrl('users/search'), 
												array(
													'complete'=> 'function() { $("#userSearchResults").dialog("open"); return false;}',
													'update'=> '#userSearchResults',
													 ),
												array(
													'id'=>'searchUserButton',
												));
			
			
			?>
			<?php echo $form->error($model,'keyword'); 	?>
		</div>
	<?php $this->endWidget(); ?>
	</div>
<?php }?>
<?php 
	if (isset($dataProvider)) {
		$this->renderPartial('userList', array('dataProvider'=>$dataProvider, 'friendList'=>true, 'userType'=>$userType), false, true);
	}
	else
	{
		echo Yii::t('users', 'No users to show...').'<br/> <br/>';				
	}
	
	echo '<div id="userSearchResults"></div>';
?>