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
	
		<div class="row">
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
					
//					echo CHtml::checkboxList(
//					  'Groups', CHtml::listData($relationRowsSelectedFriendBelongsTo, 'groupId', 'groupId'), 
//					  CHtml::listData($groupsOfUser, 'id', 'name'),
//					  array()
//					);					
				}				
				
				
			?>				
		</div>
		
		<br/>
		
		<div class="row buttons" style="text-align:center">
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