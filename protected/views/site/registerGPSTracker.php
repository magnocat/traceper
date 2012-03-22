<?php 
$this->beginWidget('zii.widgets.jui.CJuiDialog', array(
	    'id'=>'registerGPSTrackerWindow',
	    // additional javascript options for the dialog plugin
	    'options'=>array(
	        'title'=>Yii::t('general', 'Register GPS Tracker'),
	        'autoOpen'=>false,
	        'modal'=>true, 
			'resizable'=>false,
			'width'=> '280px'      
	    ),
	));
?>
	<div class="form">
	<?php $form=$this->beginWidget('CActiveForm', array(
		'id'=>'registerGPSTracker-form',
		'enableClientValidation'=>true,
		'clientOptions'=> array(
							'validateOnSubmit'=> true,
							'validateOnChange'=>false,
						 ),
	
	)); ?>
		
		<div class="row">
			<?php echo $form->labelEx($model,'name'); ?>
			<?php echo $form->textField($model,'name'); ?>
			<?php $errorMessage = $form->error($model,'name');  
				  if (strip_tags($errorMessage) == '') { echo '<div class="errorMessage">&nbsp;</div>'; }
				  else { echo $errorMessage; }
			?>	  			
		</div>
		
		<div class="row">
			<?php echo $form->labelEx($model,'deviceId'); ?>
			<?php echo $form->textField($model,'deviceId'); ?>
			<?php $errorMessage = $form->error($model,'deviceId');  
				  if (strip_tags($errorMessage) == '') { echo '<div class="errorMessage">&nbsp;</div>'; }
				  else { echo $errorMessage; }
			?>	  			
		</div>
	
		<div class="row buttons">
			<?php echo CHtml::ajaxSubmitButton('Register', $this->createUrl('site/registerGPSTracker'), 
												array(
													'success'=> 'function(result){ 
																	try {
																		var obj = jQuery.parseJSON(result);
																		if (obj.result && obj.result == "1") 
																		{
																			$("#registerGPSTrackerWindow").dialog("close");
																			
																			$("#registerMessage").dialog("open");
																		}
																		else if(obj.result && obj.result == "Duplicate Entry")
																		{
																			$("#registerGPSTrackerWindow").html(result);

																			$("#registerGPSTrackerWindow").dialog("close");
																			$("#messageDialogText").html("Add only one GPS Tracker with same id");
																			$("#messageDialog").dialog("open");																			
																		}
																	}
																	catch (error){
																		$("#registerGPSTrackerWindow").html(result);
																		var confirmMessage = document.getElementById("registerMessage");
																		if(confirmMessage.style.display != "block") {																		
																			confirmMessage.style.display = "none";
																		}
																	}
																 }',
													 ),
												null); ?>
												
			<?php echo CHtml::htmlButton('Cancel',  
												array(
													'onclick'=> '$("#registerGPSTrackerWindow").dialog("close"); return false;',
													 ),
												null); ?>												
		</div>
	
	<?php $this->endWidget(); ?>
</div>
<?php 
	$this->endWidget('zii.widgets.jui.CJuiDialog');
?>

<?php
$this->beginWidget('zii.widgets.jui.CJuiDialog', array(
	    'id'=>'registerMessage',
	    // additional javascript options for the dialog plugin
	    'options'=>array(
	        'title'=>Yii::t('general', 'Info Message'),
	        'autoOpen'=>false,
	        'modal'=>true, 
			'resizable'=>false,
			'width'=>'520px',
			'rows'=>1,
			'htmlOptions'=>array('style'=>'text-align: center'),			
	    ),
	));
?>
	<div align="center" class="row"> <?php echo '<br/> GPS Tracker is added to your list <br/><br/>'; ?> </div>
	<div align="center" class="row buttons"> <?php echo CHtml::htmlButton(Yii::t('general', 'Ok'), array('onclick'=>'$("#registerMessage").dialog("close"); return false;','width'=>'200px'), null); ?> </div>
<?php	
	$this->endWidget('zii.widgets.jui.CJuiDialog');
?>