<?php 
$this->beginWidget('zii.widgets.jui.CJuiDialog', array(
	    'id'=>'registerGPSTrackerWindow',
	    // additional javascript options for the dialog plugin
	    'options'=>array(
	        'title'=>Yii::t('site', 'Register GPS Tracker'),
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
			<?php echo $form->textField($model,'name', array('size'=>'30%')); ?>
			<?php $errorMessage = $form->error($model,'name');  
				  if (strip_tags($errorMessage) == '') { echo '<div class="errorMessage">&nbsp;</div>'; }
				  else { echo $errorMessage; }
			?>	  			
		</div>
		
		<div class="row">
			<?php echo $form->labelEx($model,'deviceId'); ?>
			<?php echo $form->textField($model,'deviceId', array('size'=>'30%')); ?>
			<?php $errorMessage = $form->error($model,'deviceId');  
				  if (strip_tags($errorMessage) == '') { echo '<div class="errorMessage">&nbsp;</div>'; }
				  else { echo $errorMessage; }
			?>	  			
		</div>
	
		<div class="row buttons">
			<?php 
// 				echo CHtml::ajaxSubmitButton(Yii::t('site','Register'), $this->createUrl('site/registerGPSTracker'), 
// 													array(
// 														'success'=> 'function(result){ 
// 																		try {
// 																			var obj = jQuery.parseJSON(result);
// 																			if (obj.result && obj.result == "1") 
// 																			{
// 																				$("#registerGPSTrackerWindow").dialog("close");	
// 																				TRACKER.showMessageDialog("'.Yii::t('site', 'The device is registered successfully').'");
// 																			}
// 																			else if(obj.result && obj.result == "Duplicate Entry")
// 																			{
// 																				$("#registerGPSTrackerWindow").html(result);
	
// 																				$("#registerGPSTrackerWindow").dialog("close");
// 																				TRACKER.showMessageDialog("'.Yii::t('site', 'You can add only one GPS Tracker with the same id!').'");
// 																			}
// 																			else if(obj.result && obj.result == "Duplicate Name")
// 																			{
// 																				$("#registerGPSTrackerWindow").html(result);
	
// 																				$("#registerGPSTrackerWindow").dialog("close");
// 																				TRACKER.showMessageDialog("'.Yii::t('site', 'You can add only one GPS Tracker with the same name!').'");
// 																			}																		
// 																		}
// 																		catch (error){
// 																			$("#registerGPSTrackerWindow").html(result);
// 																			var confirmMessage = document.getElementById("messageWindow");
// 																			if(confirmMessage.style.display != "block") {																		
// 																				confirmMessage.style.display = "none";
// 																			}
// 																		}
// 																	 }',
// 														 ),
// 													null);

				$this->widget('zii.widgets.jui.CJuiButton', array(
						'name'=>'ajaxRegisterGPSTracker',
						'caption'=>Yii::t('site', 'Register'),
						'id'=>'registerGPSTrackerAjaxButton',
						'htmlOptions'=>array('type'=>'submit','ajax'=>array('type'=>'POST','url'=>array('site/registerGPSTracker'),
								'success'=> 'function(result)
											{
												try 
												{
													var obj = jQuery.parseJSON(result);
								
													if (obj.result && obj.result == "1")
													{
														$("#registerGPSTrackerWindow").dialog("close");
														TRACKER.showMessageDialog("'.Yii::t('site', 'The device is registered successfully').'");
													}
													else if(obj.result && obj.result == "Duplicate Entry")
													{
														$("#registerGPSTrackerWindow").html(result);												
														$("#registerGPSTrackerWindow").dialog("close");
														TRACKER.showMessageDialog("'.Yii::t('site', 'You can add only one GPS Tracker with the same id!').'");
													}
													else if(obj.result && obj.result == "Duplicate Name")
													{
														$("#registerGPSTrackerWindow").html(result);												
														$("#registerGPSTrackerWindow").dialog("close");
														TRACKER.showMessageDialog("'.Yii::t('site', 'You can add only one GPS Tracker with the same name!').'");
													}
												}
												catch(error)
												{
													$("#registerGPSTrackerWindow").html(result);
								
													var confirmMessage = document.getElementById("messageWindow");
								
													if(confirmMessage.style.display != "block") 
													{
														confirmMessage.style.display = "none";
													}
												}
											}',
						))
				));				
			?>
												
			<?php 
// 				echo CHtml::htmlButton(Yii::t('site','Cancel'),  
// 													array(
// 														'onclick'=> '$("#registerGPSTrackerWindow").dialog("close"); return false;',
// 														 ),
// 													null); 
				
				$this->widget('zii.widgets.jui.CJuiButton', array(
						'name'=>'registerGPSTrackerCancel',
						'caption'=>Yii::t('common', 'Cancel'),
						'id'=>'registerGPSTrackerCancelButton',
						'onclick'=> 'js:function(){$("#registerGPSTrackerWindow").dialog("close"); return false;}'
				));				
			?>												
		</div>
	
	<?php $this->endWidget(); ?>
</div>
<?php 
	$this->endWidget('zii.widgets.jui.CJuiDialog');
?>
