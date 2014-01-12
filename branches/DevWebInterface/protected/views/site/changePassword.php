<?php 
$this->beginWidget('zii.widgets.jui.CJuiDialog', array(
	    'id'=>'changePasswordWindow',
	    // additional javascript options for the dialog plugin
	    'options'=>array(
	        'title'=>Yii::t('layout', 'Change Password'),
	        'autoOpen'=>false,
	        'modal'=>true, 
			'resizable'=>false,
			'width'=> '270px'      
	    ),
	));
?>
	<div class="form">
	<?php $form=$this->beginWidget('CActiveForm', array(
		'id'=>'changePassword-form',
		'enableClientValidation'=>true,
		'clientOptions'=> array(
							'validateOnSubmit'=> true,
							'validateOnChange'=>false,
						 ),
	
	)); ?>
	
		<div id="ajaxChangePasswordResponse">
			<div class="row">
				<?php echo $form->labelEx($model,'currentPassword'); ?>
				<?php echo $form->passwordField($model,'currentPassword'); ?>
				<?php $errorMessage = $form->error($model,'currentPassword'); 
					  if (strip_tags($errorMessage) == '') { echo '<div class="errorMessage">&nbsp;</div>'; }
					  else { echo $errorMessage; }
				?>
			</div>
	
			<div class="row">
				<?php echo $form->labelEx($model,'newPassword'); ?>
				<?php echo $form->passwordField($model,'newPassword'); ?>
				<?php $errorMessage = $form->error($model,'newPassword');  
					  if (strip_tags($errorMessage) == '') { echo '<div class="errorMessage">&nbsp;</div>'; }
					  else { echo $errorMessage; }
				?>	  			
			</div>
	
			<div class="row">
				<?php echo $form->labelEx($model,'newPasswordAgain'); ?>
				<?php echo $form->passwordField($model,'newPasswordAgain'); ?>
				<?php $errorMessage = $form->error($model,'newPasswordAgain'); 
					  if (strip_tags($errorMessage) == '') { echo '<div class="errorMessage">&nbsp;</div>'; }
					  else { echo $errorMessage; }	
				?>	  		
			</div>
		</div>	
	
		<div class="row buttons">
			<?php 
// 				echo CHtml::ajaxSubmitButton('Submit', $this->createUrl('site/changePassword'), 
// 													array(
// 														'success'=> 'function(result){ 
// 																		try {
// 																			var obj = jQuery.parseJSON(result);
// 																			if (obj.result && obj.result == "1") 
// 																			{
// 																				$("#changePasswordWindow").dialog("close");
// 																				TRACKER.showMessageDialog("'.Yii::t('site', 'Password has been changed...').'");
// 																			}
// 																		}
// 																		catch (error){
// 																			$("#changePasswordWindow").html(result);
// 																		}
// 																	 }',
// 														 ),
// 													null);

// 				$this->widget('zii.widgets.jui.CJuiButton', array(
// 						'name'=>'ajaxChangePassword',
// 						'caption'=>Yii::t('site', 'Change'),
// 						'id'=>'changePasswordAjaxButton-'.uniqid(), //Unique ID oluşturmayınca her ajaxta bir önceki sorgular da tekrarlanıyor
// 						'htmlOptions'=>array('type'=>'submit','ajax'=>array('type'=>'POST','url'=>array('site/changePassword'),
// 										'success'=> 'function(result)
// 													{
// 														try 
// 														{
// 															var obj = jQuery.parseJSON(result);
								
// 															if (obj.result && obj.result == "1")
// 															{
// 																$("#changePasswordWindow").dialog("close");
// 																TRACKER.showMessageDialog("'.Yii::t('site', 'Password has been changed...').'");
// 															}
// 														}
// 														catch(error)
// 														{
// 															$("#changePasswordWindow").html(result);
// 														}
// 													}',
// 						))
// 				));

			
			$app = Yii::app();
				
			echo CHtml::ajaxLink('<button class="btn btn-sliding-green btn-sliding-green-a icon-checkmark" style="'.(($app->language == 'en')?'padding-left:28px;padding-right:28px;':'padding-left:28px;padding-right:28px;').'">'.'<span style="font-family:Helvetica">'.Yii::t('site', 'Change').'</span>'.'</button>', $this->createUrl('site/changePassword'),
					array(
							'type'=>'POST',
							'success'=> 'function(result)
										 {
											try
											{
												var obj = jQuery.parseJSON(result);
											
												if (obj.result && obj.result == "1")
												{
													$("#changePasswordWindow").dialog("close");
													TRACKER.showMessageDialog("'.Yii::t('site', 'Password has been changed...').'");
												}
												}
											catch(error)
											{
												//$("#changePasswordWindow").html(result);
							
												$("#hiddenAjaxResponseForChangePassword").html(result);
												$("#ajaxChangePasswordResponse").html($("#hiddenAjaxResponseForChangePassword #ajaxChangePasswordResponse").html());
												$("#hiddenAjaxResponseForChangePassword").html("");							
											}
										 }',
					),
					array('id'=>'changePasswordAjaxButton-'.uniqid(), 'style'=>'padding-right:4px;'));			
			?>
												
			<?php 
// 				echo CHtml::htmlButton('Cancel',  
// 													array(
// 														'onclick'=> '$("#changePasswordWindow").dialog("close"); return false;',
// 														 ),
// 													null);
	
// 				$this->widget('zii.widgets.jui.CJuiButton', array(
// 						'name'=>'changePasswordCancel',
// 						'caption'=>Yii::t('common', 'Cancel'),
// 						'id'=>'changePasswordCancelButton',
// 						'onclick'=> 'js:function(){$("#changePasswordWindow").dialog("close"); return false;}'
// 				));

				echo CHtml::ajaxLink('<button class="btn btn-sliding-red btn-sliding-red-a icon-close" style="'.(($app->language == 'en')?'padding-left:25px;padding-right:25px;':'padding-left:28px;padding-right:28px;').'">'.'<span style="font-family:Helvetica">'.Yii::t('common', 'Cancel').'</span>'.'</button>', '#',
						array(),
						array('id'=>'changePasswordCancelButton', 'onclick'=>'$("#changePasswordWindow").dialog("close"); return false;'));				
			?>												
		</div>
	
	<?php $this->endWidget(); ?>
</div>
<?php 
	$this->endWidget('zii.widgets.jui.CJuiDialog');
?>

<!-- Diyaloglarda main layout'taki hiddenAjaxResponseToParse kullanilamadigindan (diyaloglar dinamik olarak sonradan eklendiginden yukarida -->
<!-- kaliyor) ve ayni isimle olunca da calismadigindan diyaloglarin view dosyalarinin sonuna gizli bir div tanimlaniyor -->
<div id="hiddenAjaxResponseForChangePassword" style="display:none;"></div>