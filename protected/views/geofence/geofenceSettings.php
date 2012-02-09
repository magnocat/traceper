<?php 
$this->beginWidget('zii.widgets.jui.CJuiDialog', array(
	    'id'=>'geofenceSettingsWindow',
	    // additional javascript options for the dialog plugin
	    'options'=>array(
	        'title'=>Yii::t('general', 'Geofence Settings'),
	        'autoOpen'=>false,
	        'modal'=>true, 
			'resizable'=>false,
			'width'=> '350px'      
	    ),
	));
?>

<div>
	<?php $form=$this->beginWidget('CActiveForm', array(
		'id'=>'geofenceSettings-form',
		'enableClientValidation'=>true,
		'clientOptions'=> array(
							'validateOnSubmit'=> true,
							'validateOnChange'=>false,
						 ),
	
	)); ?>
	
		<div class="row" style="padding-top:1em">
			<?php echo 'Check the geofences that you want to enroll the selected user:'; ?>
		</div>		
		
		<div class="row" style="padding-top:2em;padding-left:10px">
			<?php			
				if(empty($geofencesOfUser))
				{
					echo '</br></br> There is no geofence to show... </br></br>';
					echo 'First create some geofence(s) please';
				}
				else
				{
					echo CHtml::activeCheckboxList(
					  $model, 'geofenceStatusArray', 
					  CHtml::listData($geofencesOfUser, 'id', 'name'),
					  array()
					);	
				}				
				
				
			?>				
		</div>
		
		<div class="row buttons" style="padding-top:2em;text-align:center">
			<?php 
				if(!empty($geofencesOfUser))
				{
					echo CHtml::ajaxSubmitButton('Save', $this->createUrl('Geofence/UpdateGeofencePrivacy', array('friendId'=>$friendId)), 
														array(
															'success'=> 'function(result){ 
																			try {
																				var obj = jQuery.parseJSON(result);
																				if (obj.result && obj.result == "1") 
																				{
																					$("#geofenceSettingsWindow").dialog("close");
																					$("#messageDialogText").html("Your settings have been saved");
																					$("#messageDialog").dialog("open");																					
																				}
																				else if(obj.result && obj.result == "Duplicate Entry")
																				{
																					$("#geofenceSettingsWindow").html(result);
		
																					$("#geofenceSettingsWindow").dialog("close");
																					$("#messageDialogText").html("Select only one geofence!");
																					$("#messageDialog").dialog("open");																			
																				}																				
																			}
																			catch (error){
																				$("#geofenceSettingsWindow").html(result);
																			}
																		 }',														
															 ),
														null);					
				}
				else
				{
					
					echo CHtml::htmlButton('OK',  
														array(
															'onclick'=> '$("#geofenceSettingsWindow").dialog("close"); return false;',
															'style'=>'text-align:center'
															 ),
														null); 					
				} 
			?>
												
			<?php 
				if(!empty($geofencesOfUser))
				{
					echo CHtml::htmlButton('Cancel',  
														array(
															'onclick'=> '$("#geofenceSettingsWindow").dialog("close"); return false;',
															 ),
														null);					
				} 
			?>												
		</div>	
		
	<?php $this->endWidget(); ?>
</div>				

<?php 
	$this->endWidget('zii.widgets.jui.CJuiDialog');
?>