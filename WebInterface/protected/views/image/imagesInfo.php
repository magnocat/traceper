<?php if (Yii::app()->user->isGuest == false) { ?>
	<div class="form">
	<?php $form=$this->beginWidget('CActiveForm', array(
		'id'=>'searchImage-form',
		'enableClientValidation'=>true,
		'clientOptions'=> array(
							'validateOnSubmit'=> true,
							'validateOnChange'=>false,
						 ),
	
		));
		 ?>
		<div class="row">
			<?php	echo $form->textField($model,'keyword', array('class'=>'searchBox')); ?>
	
			<?php  echo CHtml::ajaxSubmitButton('Search', $this->createUrl('image/search'), 
												array(
													'complete'=> 'function() { $("#imageSearchResults").dialog("open"); return false;}',
													'update'=> '#imageSearchResults',
													 ),
												array(
													'id'=>'searchImageButton'
							 					));
				?>
			<?php echo $form->error($model,'keyword'); 	?>
		</div>
	<?php $this->endWidget(); ?>
	</div>
<?php } ?>
<?php 
	if (isset($dataProvider)) {
		$params = array('dataProvider'=>$dataProvider);
		if (isset($imageList) && $imageList == true) {
			$params = array_merge($params, array('imageList'=>true));
		}
		$this->renderPartial('getList', $params, false, true);
	}
	else
	{
		echo 'No photos to show... <br/> <br/>';			
	}
	
	echo '<div id="imageSearchResults"></div>';
?>