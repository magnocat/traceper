<?php if (Yii::app()->user->isGuest == false) { ?>
	<div class="form">
	<?php $form=$this->beginWidget('CActiveForm', array(
		'id'=>'searchUpload-form',
		'enableClientValidation'=>true,
		'clientOptions'=> array(
							'validateOnSubmit'=> true,
							'validateOnChange'=>false,
						 ),
	
		));
		 ?>
		<div class="row">
			<?php	echo $form->textField($model,'keyword', array('class'=>'searchBox')); ?>
	
			<?php  echo CHtml::ajaxSubmitButton(Yii::t('common', 'Search'), $this->createUrl('upload/search', array('fileType'=>$fileType)), 
												array(
													'complete'=> 'function() { $("#uploadSearchResults").dialog("open"); return false;}',
													'update'=> '#uploadSearchResults',
													 ),
												array(
													'id'=>'searchUploadButton'
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
		if (isset($uploadList) && $uploadList == true) {
			$params = array_merge($params, array('uploadList'=>true));
		}
		$this->renderPartial('getList', $params, false, true);
	}
	else
	{
		echo 'No files to show... <br/> <br/>';			
	}
	
	echo '<div id="uploadSearchResults"></div>';
?>