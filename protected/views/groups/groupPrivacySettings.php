<?php 
$this->beginWidget('zii.widgets.jui.CJuiDialog', array(
	    'id'=>'groupPrivacySettingsWindow',
	    // additional javascript options for the dialog plugin
	    'options'=>array(
	        'title'=>Yii::t('groups', 'Group Privacy Settings'),
	        'autoOpen'=>false,
	        'modal'=>true, 
			'resizable'=>false,
			'width'=> '600px'      
	    ),
	));
?>

<div>
	<?php $form=$this->beginWidget('CActiveForm', array(
		'id'=>'groupPrivacySettings-form',
		'enableClientValidation'=>true,
		'clientOptions'=> array(
							'validateOnSubmit'=> true,
							'validateOnChange'=>false,
						 ),
			'htmlOptions'=>array(
					'class'=>'ac-custom ac-checkbox ac-cross',
			),	
	)); ?>
	
		<div class="row" style="padding-top:1em">
			<?php echo Yii::t('groups', 'Give permissions to group members by checking/unchecking the fields below:'); ?>
		</div>		
	
		<div class="row" style="padding-top:2em">
			<?php echo $form->checkBox($model,'allowToSeeMyPosition'); ?>
			<?php echo $form->label($model,'allowToSeeMyPosition'); ?>
			<?php echo $form->error($model,'allowToSeeMyPosition'); ?>
		</div>

		<div class="row buttons" style="padding-top:2em;text-align:center">
			<?php 
				
// 				$this->widget('zii.widgets.jui.CJuiButton', array(
// 						'name'=>'ajaxSetPrivacyRights',
// 						'caption'=>Yii::t('common', 'Save'),
// 						'id'=>'setPrivacyRightsAjaxButton-'.uniqid(), //Unique ID oluşturmayınca her ajaxta bir önceki sorgular da tekrarlanıyor
// 						'htmlOptions'=>array('type'=>'submit','ajax'=>array('type'=>'POST','url'=>$this->createUrl('groups/setPrivacyRights', array('groupId'=>$groupId)),
// 														'success'=> 'function(result){ 
// 																		try {
// 																			var obj = jQuery.parseJSON(result);
// 																			if (obj.result && obj.result == "1") 
// 																			{
// 																				$("#groupPrivacySettingsWindow").dialog("close");
// 																				TRACKER.showMessageDialog("'.Yii::t('common', 'Your settings have been saved.').'");
// 																			}
// 																		}
// 																		catch (error){
// 																			$("#groupPrivacySettingsWindow").html(result);
// 																		}
// 																	 }',
// 						))
// 				));
				
				$app = Yii::app();
				
				echo CHtml::ajaxLink('<button class="btn btn-sliding-green btn-sliding-green-a icon-checkmark" style="'.(($app->language == 'en')?'padding-left:28px;padding-right:28px;':'padding-left:28px;padding-right:28px;').'">'.'<span style="font-family:Helvetica">'.Yii::t('common', 'Save').'</span>'.'</button>', $this->createUrl('groups/setPrivacyRights', array('groupId'=>$groupId)),
						array(
								'type'=>'POST',
								'success'=> 'function(result){ 
												try {
													var obj = jQuery.parseJSON(result);
													if (obj.result && obj.result == "1") 
													{
														$("#groupPrivacySettingsWindow").dialog("close");
														TRACKER.showMessageDialog("'.Yii::t('common', 'Your settings have been saved.').'");
													}
												}
												catch (error){
													$("#groupPrivacySettingsWindow").html(result);
												}
											 }',
						),
						array('id'=>'setPrivacyRightsAjaxButton-'.uniqid(), 'style'=>'padding-right:4px;'));				
				
// 				echo CHtml::htmlButton(Yii::t('common', 'Cancel'),  
// 													array(
// 														'onclick'=> '$("#groupPrivacySettingsWindow").dialog("close"); return false;',
// 														 ),
// 													null);

// 				$this->widget('zii.widgets.jui.CJuiButton', array(
// 						'name'=>'setPrivacyRightsCancel',
// 						'caption'=>Yii::t('common', 'Cancel'),
// 						'id'=>'setPrivacyRightsCancelButton',
// 						'onclick'=> 'js:function(){$("#groupPrivacySettingsWindow").dialog("close"); return false;}'
// 				));

				echo CHtml::ajaxLink('<button class="btn btn-sliding-red btn-sliding-red-a icon-close" style="'.(($app->language == 'en')?'padding-left:25px;padding-right:25px;':'padding-left:28px;padding-right:28px;').'">'.'<span style="font-family:Helvetica">'.Yii::t('common', 'Cancel').'</span>'.'</button>', '#',
						array(),
						array('id'=>'setPrivacyRightsCancelButton', 'onclick'=>'$("#groupPrivacySettingsWindow").dialog("close"); return false;'));				
			?>												
		</div>	
		
	<?php $this->endWidget(); ?>
</div>

<script type="text/javascript">		
checkSVGElements('groupPrivacySettings-form', true/*par_isForm*/);	
</script>

<?php 
	$this->endWidget('zii.widgets.jui.CJuiDialog');
?>