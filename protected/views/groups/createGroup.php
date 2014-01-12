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
	
		<div id="ajaxCreateGroupResponse">
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
		</div>

		<div class="row buttons" id="createGroupButtons">
			<?php 
// 				$this->widget('zii.widgets.jui.CJuiButton', array(
// 						'name'=>'ajaxCreateGroup',
// 						'caption'=>Yii::t('common', 'Create'),
// 						//Ajax'la gelen cevapta butonu guncellemesen bile diyalog yapisinda gelen tum cevapta buton ojesi yine geldiginden
// 						//ve ayni ID'li oldugundan eski butona bassan da 2 butona basilmis gibi multiple iste yapiyor unique ID vermezsen
// 						'id'=>'createGroupAjaxButton-'.uniqid(), //Unique ID oluşturmayınca her ajaxta bir önceki sorgular da tekrarlanıyor
// 						'htmlOptions'=>array('type'=>'submit','ajax'=>array('type'=>'POST','url'=>array('groups/createGroup'),
// 																			'success'=> 'function(result){ 
// 																							try {
// 																								var obj = jQuery.parseJSON(result);
// 																								if (obj.result && obj.result == "1") 
// 																								{
// 																									$("#createGroupWindow").dialog("close");
				
// 																									//var selectedTab = $("#tab_view").tabs("option", "selected");																													
// 																									//alert("Selected Tab Index: " + selectedTab);								

// 																									if(obj.groupType == '.GroupType::FriendGroup.')
// 																									{
// 																										//"#friendGroupsListView" eleman� yuklu degilse JS hata veriyor ve asagidaki mesaj g�z�km�yor
// 																										if($("#friendGroupsListView").length)
// 																										{
// 																											$.fn.yiiGridView.update("friendGroupsListView");
// 																										}																														
// 																									}
// 																									else if(obj.groupType == '.GroupType::StaffGroup.')
// 																									{
// 																										//"#staffGroupsListView" eleman� yuklu degilse JS hata veriyor ve asagidaki mesaj g�z�km�yor
// 																										if($("#staffGroupsListView").length)
// 																										{
// 																											$.fn.yiiGridView.update("staffGroupsListView");
// 																										}																														
// 																									}

// 																									TRACKER.showMessageDialog("'.Yii::t('groups', 'The group is created successfully').'");
// 																								}
// 																								else if(obj.result && obj.result == "Duplicate Entry")
// 																								{
// 																									//$("#createGroupWindow").html(result);
// 																									$("#hiddenAjaxResponseForCreateGroup").html(result);
// 																									$("#ajaxCreateGroupResponse").html($("#hiddenAjaxResponseForCreateGroup #ajaxCreateGroupResponse").html());
// 																									$("#hiddenAjaxResponseForCreateGroup").html("");								
						
// 																									$("#createGroupWindow").dialog("close");
// 																									TRACKER.showMessageDialog("'.Yii::t('groups', 'A group with this name already exists!').'");
// 																								}
// 																							}
// 																							catch (error){
// 																								//$("#createGroupWindow").html(result);
				
// 																								$("#hiddenAjaxResponseForCreateGroup").html(result);
// 																								$("#ajaxCreateGroupResponse").html($("#hiddenAjaxResponseForCreateGroup #ajaxCreateGroupResponse").html());
// 																								$("#hiddenAjaxResponseForCreateGroup").html("");
// 																							}
// 																						}'
// 										))
// 				));

				$app = Yii::app();
			
				echo CHtml::ajaxLink('<button class="btn btn-sliding-green btn-sliding-green-a icon-plus" style="'.(($app->language == 'en')?'padding-left:28px;padding-right:28px;':'padding-left:28px;padding-right:28px;').'">'.'<span style="font-family:Helvetica">'.Yii::t('common', 'Create').'</span>'.'</button>', $this->createUrl('groups/createGroup'),
						array(
								'type'=>'POST',
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
												
														TRACKER.showMessageDialog("'.Yii::t('groups', 'The group is created successfully.').'");
													}
													else if(obj.result && obj.result == "Duplicate Entry")
													{
														//$("#createGroupWindow").html(result);
														$("#hiddenAjaxResponseForCreateGroup").html(result);
														$("#ajaxCreateGroupResponse").html($("#hiddenAjaxResponseForCreateGroup #ajaxCreateGroupResponse").html());
														$("#hiddenAjaxResponseForCreateGroup").html("");
												
														$("#createGroupWindow").dialog("close");
														TRACKER.showMessageDialog("'.Yii::t('groups', 'A group with this name already exists!').'");
													}
												}
												catch (error){
													//$("#createGroupWindow").html(result);
												
													$("#hiddenAjaxResponseForCreateGroup").html(result);
													$("#ajaxCreateGroupResponse").html($("#hiddenAjaxResponseForCreateGroup #ajaxCreateGroupResponse").html());
													$("#hiddenAjaxResponseForCreateGroup").html("");
												}
											}'
						),
						array('id'=>'createGroupAjaxButton-'.uniqid(), 'style'=>'padding-right:4px;'));				
			?>
												
			<?php 
// 				echo CHtml::htmlButton(Yii::t('common', 'Cancel'),  
// 												array(
// 													'onclick'=> '$("#createGroupWindow").dialog("close"); return false;',
// 													 ),
// 												null); 
			?>
												
			<?php 
// 				$this->widget('zii.widgets.jui.CJuiButton', array(
// 						'name'=>'createGroupCancel',
// 						'caption'=>Yii::t('common', 'Cancel'),
// 						'id'=>'createGroupCancelButton',
// 						'onclick'=> 'js:function(){$("#createGroupWindow").dialog("close"); return false;}'
// 				));

				echo CHtml::ajaxLink('<button class="btn btn-sliding-red btn-sliding-red-a icon-close" style="'.(($app->language == 'en')?'padding-left:25px;padding-right:25px;':'padding-left:28px;padding-right:28px;').'">'.'<span style="font-family:Helvetica">'.Yii::t('common', 'Cancel').'</span>'.'</button>', '#',
						array(),
						array('id'=>'createGroupCancelButton', 'onclick'=>'$("#createGroupWindow").dialog("close"); return false;'));				
 			?>																	
		</div>					
	<?php $this->endWidget(); ?>
</div>
<?php 
	$this->endWidget('zii.widgets.jui.CJuiDialog');
?>

<!-- Diyaloglarda main layout'taki hiddenAjaxResponseToParse kullanilamadigindan (diyaloglar dinamik olarak sonradan eklendiginden yukarida -->
<!-- kaliyor) ve ayni isimle olunca da calismadigindan diyaloglarin view dosyalarinin sonuna gizli bir div tanimlaniyor -->
<div id="hiddenAjaxResponseForCreateGroup" style="display:none;"></div>
