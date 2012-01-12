<?php 
$this->beginWidget('zii.widgets.jui.CJuiDialog', array(
	    'id'=>'groupSettingsWindow',
	    // additional javascript options for the dialog plugin
	    'options'=>array(
	        'title'=>Yii::t('general', 'Group Settings'),
	        'autoOpen'=>false,
	        'modal'=>true, 
			'resizable'=>false,
			'width'=> '320px'      
	    ),
	));
?>
	<div class="form">
	<?php $form=$this->beginWidget('CActiveForm', array(
		'id'=>'groupSettings-form',
		'enableClientValidation'=>true,
		'clientOptions'=> array(
							'validateOnSubmit'=> true,
							'validateOnChange'=>false,
						 ),
	
	)); ?>
	

		<div class="row">
			<?php			
				echo CHtml::activeCheckboxList(
				  $model, 'groupStatusArray', 
				  CHtml::listData(Groups::model()->findAll(), 'id', 'name'),
				  array()
				);
			?>				
		</div>
		
		<br/>
		
		<div class="row buttons">
			<?php echo CHtml::ajaxSubmitButton('Save', $this->createUrl('groups/updateGroup'), 
												array(
													'success'=> 'function(result){ 
																	try {
																		var obj = jQuery.parseJSON(result);
																		if (obj.result && obj.result == "1") 
																		{
																			$("#groupSettingsWindow").dialog("close");
																		}
																	}
																	catch (error){
																		$("#groupSettingsWindow").html(result);
																	}
																 }',
													 ),
												null); ?>
												
			<?php echo CHtml::htmlButton('Cancel',  
												array(
													'onclick'=> '$("#groupSettingsWindow").dialog("close"); return false;',
													 ),
												null); ?>												
		</div>		
	
	<?php $this->endWidget(); ?>
</div>
<?php 
	$this->endWidget('zii.widgets.jui.CJuiDialog');
?>