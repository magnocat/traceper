<?php 
$this->beginWidget('zii.widgets.jui.CJuiDialog', array(
	    'id'=>'groupSettingsWindow',
	    // additional javascript options for the dialog plugin
	    'options'=>array(
	        'title'=>Yii::t('general', 'Group Settings'),
	        'autoOpen'=>false,
	        'modal'=>true, 
			'resizable'=>false,
			'width'=> '350px'      
	    ),
	));
?>

<div>
	<?php $form=$this->beginWidget('CActiveForm', array(
		'id'=>'groupSettings-form',
		'enableClientValidation'=>true,
		'clientOptions'=> array(
							'validateOnSubmit'=> true,
							'validateOnChange'=>false,
						 ),
	
	)); ?>
	
		<div class="row" style="padding-top:1em">
			<?php echo 'Check the groups that you want to enroll the selected user:'; ?>
		</div>		
		
		<div class="row" style="padding-top:2em;padding-left:10px">
			<?php			
				if(empty($groupsOfUser))
				{
					echo '</br></br> There is no group to show... </br></br>';
					echo 'First create some group(s) please';
				}
				else
				{
					echo CHtml::activeCheckboxList(
					  $model, 'groupStatusArray', 
					  CHtml::listData($groupsOfUser, 'id', 'name'),
					  array()
					);	
					
//					$form->dropDownList($model,'groupStatusArray', CHtml::listData($groupsOfUser, 'id', 'name'), array('empty'=>'Select Group'));
//					
//					CHtml::dropDownList('listname', $select, array('M' => 'Male', 'F' => 'Female'), array('empty' => '(Select a gender)'));					
					
//					echo CHtml::checkboxList(
//					  'Groups', CHtml::listData($relationRowsSelectedFriendBelongsTo, 'groupId', 'groupId'), 
//					  CHtml::listData($groupsOfUser, 'id', 'name'),
//					  array()
//					);					
				}				
				
				
			?>				
		</div>
		
		<div class="row buttons" style="padding-top:2em;text-align:center">
			<?php 
				if(!empty($groupsOfUser))
				{
					echo CHtml::ajaxSubmitButton('Save', $this->createUrl('groups/updateGroup', array('friendId'=>$friendId)), 
														array(
															'success'=> 'function(result){ 
																			try {
																				var obj = jQuery.parseJSON(result);
																				if (obj.result && obj.result == "1") 
																				{
																					$("#groupSettingsWindow").dialog("close");
																					$("#messageDialogText").html("Your settings have been saved");
																					$("#messageDialog").dialog("open");																					
																				}
																				else if(obj.result && obj.result == "Duplicate Entry")
																				{
																					$("#groupSettingsWindow").html(result);
		
																					$("#groupSettingsWindow").dialog("close");
																					$("#messageDialogText").html("Select only one privacy group!");
																					$("#messageDialog").dialog("open");																			
																				}																				
																			}
																			catch (error){
																				$("#groupSettingsWindow").html(result);
																			}
																		 }',														
															 ),
														null);					
				}
				else
				{
					
					echo CHtml::htmlButton('OK',  
														array(
															'onclick'=> '$("#groupSettingsWindow").dialog("close"); return false;',
															'style'=>'text-align:center'
															 ),
														null); 					
				} 
			?>
												
			<?php 
				if(!empty($groupsOfUser))
				{
					echo CHtml::htmlButton('Cancel',  
														array(
															'onclick'=> '$("#groupSettingsWindow").dialog("close"); return false;',
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