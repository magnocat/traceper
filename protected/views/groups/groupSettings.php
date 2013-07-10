<?php 
$this->beginWidget('zii.widgets.jui.CJuiDialog', array(
	    'id'=>'groupSettingsWindow',
	    // additional javascript options for the dialog plugin
	    'options'=>array(
	        'title'=>Yii::t('groups', 'Group Settings'),
	        'autoOpen'=>false,
	        'modal'=>true, 
			'resizable'=>false,
			'width'=> '500px'      
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

		<div class="row" style="padding-top:2em;padding-left:10px">
			<?php			
				if(empty($groupsOfUser))
				{
					echo Yii::t('groups', 'Unfortunately, you have no groups to show yet. You can create new group(s) using the top menu, and then you can enroll your friend(s) into the groups you want.');
				}
				else
				{
					echo Yii::t('groups', 'You can enroll your friend to the selected group(s) just by ticking the corresponding checkboxes and clicking the "Save" button. Conversely, in order to remove your friend from the membership of a group, just remove the tick on the corresponding checkbox and save the operation again:').'</br></br>';
					
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
					$this->widget('zii.widgets.jui.CJuiButton', array(
							'name'=>'ajaxUpdateGroup',
							'caption'=>Yii::t('common', 'Save'),
							'id'=>'updateGroupAjaxButton',
							'htmlOptions'=>array('type'=>'submit','ajax'=>array('type'=>'POST','url'=>$this->createUrl('groups/updateGroup', array('friendId'=>$friendId, 'groupType'=>$groupType)),
									'success'=> 'function(result)
												{
													try 
													{
														var obj = jQuery.parseJSON(result);
									
														if (obj.result && obj.result == "1")
														{
															$("#groupSettingsWindow").dialog("close");
															TRACKER.showMessageDialog("'.Yii::t('groups', 'Your settings have been saved').'")
														}
														else if(obj.result && obj.result == "Duplicate Entry")
														{
															$("#groupSettingsWindow").html(result);													
															$("#groupSettingsWindow").dialog("close");
															TRACKER.showMessageDialog("'.Yii::t('groups', 'Select only one privacy group!').'")
														}
													}
													catch(error)
													{
														$("#groupSettingsWindow").html(result);
													}
												}',
							))
					));					
				}
				else
				{					
					$this->widget('zii.widgets.jui.CJuiButton', array(
							'name'=>'groupSettingsOK',
							'caption'=>Yii::t('common', 'OK'),
							'id'=>'groupSettingsOKButton',
							'onclick'=> 'js:function(){$("#groupSettingsWindow").dialog("close"); return false;}'
					));					
				} 
			?>
												
			<?php 
				if(!empty($groupsOfUser))
				{
					$this->widget('zii.widgets.jui.CJuiButton', array(
							'name'=>'updateGroupCancel',
							'caption'=>Yii::t('common', 'Cancel'),
							'id'=>'updateGroupCancelButton',
							'onclick'=> 'js:function(){$("#groupSettingsWindow").dialog("close"); return false;}'
					));					
				} 
			?>												
		</div>	
		
	<?php $this->endWidget(); ?>
</div>				

<?php 
	$this->endWidget('zii.widgets.jui.CJuiDialog');
?>