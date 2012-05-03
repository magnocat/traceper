<?php 
$this->beginWidget('zii.widgets.jui.CJuiDialog', array(
	    'id'=>'createGeofenceWindow',
	    // additional javascript options for the dialog plugin
	    'options'=>array(
	        'title'=>Yii::t('geofence', 'Create New Geofence'),
	        'autoOpen'=>false,
	        'modal'=>true, 
			'resizable'=>false,
			'width'=> '340px',
	    	'close' => 'js:function(event, ui) { mapOperator.removeAllPointsFromGeoFence(mapOperator.newGeofence);return false; }'
	    ),
	));
?>
	<div class="form">
	<?php $form=$this->beginWidget('CActiveForm', array(
		'id'=>'createGeofence-form',
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
			<?php echo $form->labelEx($model,'description'); ?>
			<?php echo $form->textArea($model,'description', array('rows'=>5, 'cols'=>32,'resizable'=>false)); ?>	
			<?php $errorMessage = $form->error($model,'description'); 
				  if (strip_tags($errorMessage) == '') { echo '<div class="errorMessage">&nbsp;</div>'; }
				  else { echo $errorMessage; }
			?>
		</div>
	
		<div class="row buttons">
			<?php echo CHtml::ajaxSubmitButton('Create', $this->createUrl('geofence/createGeofence'), 
												array(													
													'success'=> 'function(result){ 
																	try {
																		
																		var obj = jQuery.parseJSON(result);
																		if (obj.result && obj.result == "1") 
																		{
																			var loc1=mapOperator.getPointOfGeoFencePath(mapOperator.newGeofence,0);
																			var loc2=mapOperator.getPointOfGeoFencePath(mapOperator.newGeofence,1);
																			var loc3=mapOperator.getPointOfGeoFencePath(mapOperator.newGeofence,2);
																										
																			TRACKER.sendGeoFencePoints(obj.name,obj.description,loc1.latitude,loc1.longitude,loc2.latitude,loc2.longitude,loc3.latitude,loc3.longitude);																			
																			$("#createGeofenceWindow").dialog("close");
																		}
																		else if(obj.result && obj.result == "Duplicate Entry")
																		{
																			$("#createGeofenceWindow").html(result);

																			$("#createGeofenceWindow").dialog("close");
																			TRACKER.showMessageDialog("'.Yii::t('geofence', 'A geofence with this name already exists!').'");																	
																		}
																	}
																	catch (error){
																		$("#createGeofenceWindow").html(result);
																	}
																 }',
													 ),
												null); ?>
												
			<?php echo CHtml::htmlButton('Cancel',  
												array(
													'onclick'=> '$("#createGeofenceWindow").dialog("close"); 
																mapOperator.removeAllPointsFromGeoFence(mapOperator.newGeofence);
																return false;',
													 ),
												null); ?>												
		</div>
	
	<?php $this->endWidget(); ?>
</div>
<?php 
	$this->endWidget('zii.widgets.jui.CJuiDialog');
?>