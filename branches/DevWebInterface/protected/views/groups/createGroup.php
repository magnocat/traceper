<?php
$this->beginWidget('zii.widgets.jui.CJuiDialog', array(
	    'id'=>'createGroupWindow',
	    // additional javascript options for the dialog plugin
	    'options'=>array(
	        'title'=>Yii::t('groups', 'Create New Group'),
	        'autoOpen'=>false,
	        'modal'=>true, 
			'resizable'=>false,
			'width'=> '340px'      
	    ),
	));
?>
	<div class="form">
	<?php $form=$this->beginWidget('CActiveForm', array(
		'id'=>'createGroup-form',
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
		
		<?php 
		if(Yii::app()->params->featureFriendManagementEnabled && Yii::app()->params->featureStaffManagementEnabled)
		{
		?>
		<div class="row">			
			<?php echo $form->dropDownList($model,'groupType', array(GroupType::FriendGroup => Yii::t('groups', 'Friend Group'), GroupType::StaffGroup => Yii::t('groups', 'Staff Group')), array('empty'=>Yii::t('groups', 'Select Group Type'))); ?>
			<?php $errorMessage = $form->error($model,'groupType'); 
				  if (strip_tags($errorMessage) == '') { echo '<div class="errorMessage">&nbsp;</div>'; }
				  else { echo $errorMessage; }
			?>
		</div>
		<?php 
		}
		else if(Yii::app()->params->featureFriendManagementEnabled)
		{
			echo $form->hiddenField($model,'groupType',array('value'=>GroupType::FriendGroup));
		}
		else if(Yii::app()->params->featureStaffManagementEnabled)
		{
			echo $form->hiddenField($model,'groupType',array('value'=>GroupType::StaffGroup));
		}
		?>				
			
		<div class="row">
			<?php echo $form->labelEx($model,'description'); ?>
			<?php echo $form->textArea($model,'description', array('rows'=>5, 'cols'=>32,'resizable'=>false)); ?>	
			<?php $errorMessage = $form->error($model,'description'); 
				  if (strip_tags($errorMessage) == '') { echo '<div class="errorMessage">&nbsp;</div>'; }
				  else { echo $errorMessage; }
			?>
		</div>
	
		<div class="row buttons">
			<?php 
// 				echo CHtml::ajaxSubmitButton(Yii::t('common', 'Create'), $this->createUrl('groups/createGroup'), 
// 												array(
// 													'success'=> 'function(result){ 
// 																	try {
// 																		var obj = jQuery.parseJSON(result);
// 																		if (obj.result && obj.result == "1") 
// 																		{
// 																			$("#createGroupWindow").dialog("close");
																			
// 																			if(obj.groupType == '.GroupType::FriendGroup.')
// 																			{
// 																				$.fn.yiiGridView.update("friendGroupsListView");
// 																			}
// 																			else if(obj.groupType == '.GroupType::StaffGroup.')
// 																			{
// 																				$.fn.yiiGridView.update("staffGroupsListView");
// 																			}

// 																			TRACKER.showMessageDialog("'.Yii::t('groups', 'The group is created successfully').'");
// 																		}
// 																		else if(obj.result && obj.result == "Duplicate Entry")
// 																		{
// 																			$("#createGroupWindow").html(result);

// 																			$("#createGroupWindow").dialog("close");
// 																			TRACKER.showMessageDialog("'.Yii::t('groups', 'A group with this name already exists!').'");
// 																		}
// 																	}
// 																	catch (error){
// 																		$("#createGroupWindow").html(result);
// 																	}
// 																 }',
// 													 ),
// 												null);

				$this->widget('zii.widgets.jui.CJuiButton', array(
						'name'=>'ajaxCreateGroup',
						'caption'=>Yii::t('common', 'Create'),
						'id'=>'createGroupAjaxButton-'.uniqid(), //Unique ID oluşturmayınca her ajaxta bir önceki sorgular da tekrarlanıyor
						'htmlOptions'=>array('type'=>'submit','ajax'=>array('type'=>'POST','url'=>array('groups/createGroup'),
																			'success'=> 'function(result){ 
																											try {
																												var obj = jQuery.parseJSON(result);
																												if (obj.result && obj.result == "1") 
																												{
																													$("#createGroupWindow").dialog("close");
								
																													//var selectedTab = $("#tab_view").tabs("option", "selected");																													
																													//alert("Selected Tab Index: " + selectedTab);								

																													if(obj.groupType == '.GroupType::FriendGroup.')
																													{
																														//"#friendGroupsListView" eleman� yuklu degilse JS hata veriyor ve asagidaki mesaj g�z�km�yor
																														if($("#friendGroupsListView").length)
																														{
																															$.fn.yiiGridView.update("friendGroupsListView");
																														}																														
																													}
																													else if(obj.groupType == '.GroupType::StaffGroup.')
																													{
																														//"#staffGroupsListView" eleman� yuklu degilse JS hata veriyor ve asagidaki mesaj g�z�km�yor
																														if($("#staffGroupsListView").length)
																														{
																															$.fn.yiiGridView.update("staffGroupsListView");
																														}																														
																													}

																													TRACKER.showMessageDialog("'.Yii::t('groups', 'The group is created successfully').'");
																												}
																												else if(obj.result && obj.result == "Duplicate Entry")
																												{
																													$("#createGroupWindow").html(result);
										
																													$("#createGroupWindow").dialog("close");
																													TRACKER.showMessageDialog("'.Yii::t('groups', 'A group with this name already exists!').'");
																												}
																											}
																											catch (error){
																												$("#createGroupWindow").html(result);
																											}
																										}'
										))
				));				
			?>
												
			<?php 
// 				echo CHtml::htmlButton(Yii::t('common', 'Cancel'),  
// 												array(
// 													'onclick'=> '$("#createGroupWindow").dialog("close"); return false;',
// 													 ),
// 												null); 
			?>
												
			<?php 
				$this->widget('zii.widgets.jui.CJuiButton', array(
						'name'=>'createGroupCancel',
						'caption'=>Yii::t('common', 'Cancel'),
						'id'=>'createGroupCancelButton',
						'onclick'=> 'js:function(){$("#createGroupWindow").dialog("close"); return false;}'
				));				
 			?>																	
		</div>
	
	<?php $this->endWidget(); ?>
</div>
<?php 
	$this->endWidget('zii.widgets.jui.CJuiDialog');
?>