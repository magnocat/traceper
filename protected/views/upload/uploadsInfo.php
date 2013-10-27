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
	
		Yii::app()->clientScript->registerScript('uploadSearchTooltip',
			'$("#uploadSearchField").tooltipster({
			theme: ".tooltipster-infoWithIcon",
			trigger: "custom",
			maxWidth: 450,
			onlyOne: false,
			position: "right",
			interactive: true,
			offsetX: 100,
			});
	
			$("#uploadSearchField").focus(function ()	{
			$("#uploadSearchField").tooltipster("update", TRACKER.langOperator.uploadSearchNotificationMessage);
			$("#uploadSearchField").tooltipster("show");
			});
	
			$("#uploadSearchField").blur(function ()	{
			$("#uploadSearchField").tooltipster("hide");
			});
				
			', CClientScript::POS_HEAD);	
		 ?>
		<div class="row">
			<?php	echo $form->textField($model,'keyword', array('id'=>'uploadSearchField','class'=>'searchBox','placeholder'=>Yii::t('upload', 'Type a keyword'))); ?>
	
			<?php  
// 					echo CHtml::ajaxSubmitButton(Yii::t('common', 'Search'), $this->createUrl('upload/search', array('fileType'=>$fileType)), 
// 												array(
// 													'complete'=> 'function() { $("#uploadSearchResults").dialog("open"); return false;}',
// 													'update'=> '#uploadSearchResults',
// 													 ),
// 												array(
// 													'id'=>'searchUploadButton'
// 							 					));
					
					$this->widget('zii.widgets.jui.CJuiButton', array(
							'name'=>'ajaxUploadSearch',
							'caption'=>Yii::t('common', 'Search'),
							'id'=>'uploadSearchAjaxButton',
							'htmlOptions'=>array('type'=>'submit','style'=>'width:6em;margin-left:0.2em;','ajax'=>array('type'=>'POST','url'=>$this->createUrl('upload/search', array('fileType'=>$fileType)),
									'complete'=> 'function() { $("#uploadSearchResults").dialog("open"); return false;}',
									'update'=> '#uploadSearchResults',
							))
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
?>