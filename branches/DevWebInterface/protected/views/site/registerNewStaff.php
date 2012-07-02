<?php 
$this->beginWidget('zii.widgets.jui.CJuiDialog', array(
	    'id'=>'registerNewStaffWindow',
	    // additional javascript options for the dialog plugin
	    'options'=>array(
	        'title'=>Yii::t('site', 'Register New Staff'),
	        'autoOpen'=>false,
	        'modal'=>true, 
			'resizable'=>false,
			'width'=> '280px'      
	    ),
	));

	echo "<div id='gridViewId' style='display:none'></div>";
?>
	<div class="form">
	<?php $form=$this->beginWidget('CActiveForm', array(
		'id'=>'registerNewStaff-form',
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
			<?php echo $form->labelEx($model,'email'); ?>
			<?php echo $form->textField($model,'email'); ?>
			<?php $errorMessage = $form->error($model,'email');  
				  if (strip_tags($errorMessage) == '') { echo '<div class="errorMessage">&nbsp;</div>'; }
				  else { echo $errorMessage; }
			?>	  			
		</div>
		
		<div class="row">
			<?php echo $form->labelEx($model,'password'); ?>
			<?php echo $form->passwordField($model,'password'); ?>
			<?php $errorMessage = $form->error($model,'password'); 
				  if (strip_tags($errorMessage) == '') { echo '<div class="errorMessage">&nbsp;</div>'; }
				  else { echo $errorMessage; }	
			?>	  		
		</div>
		
		<div class="row">
			<?php echo $form->labelEx($model,'passwordAgain'); ?>
			<?php echo $form->passwordField($model,'passwordAgain'); ?>
			<?php $errorMessage = $form->error($model,'passwordAgain'); 
				  if (strip_tags($errorMessage) == '') { echo '<div class="errorMessage">&nbsp;</div>'; }
				  else { echo $errorMessage; }	
			?>	  		
		</div>		
	
		<div class="row buttons">
			<?php echo CHtml::ajaxSubmitButton(Yii::t('site','Register'), $this->createUrl('site/registerNewStaff'), 
												array(
													'success'=> 'function(result){ 
																	try {
																		var obj = jQuery.parseJSON(result);
																		if (obj.result && obj.result == "1") 
																		{
																			$("#registerNewStaffWindow").dialog("close");
																			$.fn.yiiGridView.update($("#gridViewId").text());	
																			TRACKER.showMessageDialog("'.Yii::t('site', 'The staff is registered successfully').'");
																		}
																		else if(obj.result && obj.result == "Duplicate Entry")
																		{
																			$("#registerNewStaffWindow").html(result);

																			$("#registerNewStaffWindow").dialog("close");
																			TRACKER.showMessageDialog("'.Yii::t('site', 'You can add only one staff with the e-mail!').'");
																		}																		
																	}
																	catch (error){
																		$("#registerNewStaffWindow").html(result);
																		var confirmMessage = document.getElementById("messageWindow");
																		if(confirmMessage.style.display != "block") {																		
																			confirmMessage.style.display = "none";
																		}
																	}
																 }',
													 ),
												null); ?>
												
			<?php echo CHtml::htmlButton(Yii::t('site','Cancel'),  
												array(
													'onclick'=> '$("#registerNewStaffWindow").dialog("close"); return false;',
													 ),
												null); ?>												
		</div>
	
	<?php $this->endWidget(); ?>
</div>
<?php 
	$this->endWidget('zii.widgets.jui.CJuiDialog');
?>
