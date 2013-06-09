
<?php 
$this->beginWidget('zii.widgets.jui.CJuiDialog', array(
	    'id'=>'forgotPasswordWindow',
	    // additional javascript options for the dialog plugin
	    'options'=>array(
	        'title'=>Yii::t('site', 'Forgot Password?'),
	        'autoOpen'=>false,
	        'modal'=>true, 
			'resizable'=>false,
			'width'=> '40%'      
	    ),
	));
?>
	<div class="form">
	<?php $form=$this->beginWidget('CActiveForm', array(
		'id'=>'forgotPassword-form',
		'enableClientValidation'=>true,
		'clientOptions'=> array(
							'validateOnSubmit'=> true,
							'validateOnChange'=>false,
						 ),
	
	)); ?>
	
		<div class="row" style="padding-top:1em">
			<?php echo Yii::t('site', 'Enter your registered e-mail address into the below field and we will send you a link to change your password:'); ?>
		</div>	
		
		</br>	

		<div class="row">
			<?php echo $form->labelEx($model,'email'); ?>
			<?php echo $form->textField($model,'email', array('size'=>'50%','maxlength'=>128,'tabindex'=>1));?>
			<?php $errorMessage = $form->error($model,'email'); 
				  if (strip_tags($errorMessage) == '') { echo '<div class="errorMessage">&nbsp;</div>'; }
				  else { echo $errorMessage; }
			?>
		</div>
	
		<div class="row buttons">
			<?php 
// 			echo CHtml::ajaxSubmitButton('Submit', $this->createUrl('site/forgotPassword'), 
// 												array(
// 													'success'=> 'function(result){ 
// 																	try {
// 																		var obj = jQuery.parseJSON(result);
																		
// 																		if (obj.result)
// 																		{
// 																			if (obj.result == "1") 
// 																			{
// 																				$("#forgotPasswordWindow").dialog("close");
// 																				TRACKER.showMessageDialog("'.Yii::t('site', 'We have sent the password reset link to your mailbox. Please make sure you check the spam folder as well.').'");
// 																			}
// 																			else if (obj.result == "0")
// 																			{
// 																				$("#forgotPasswordWindow").dialog("close");
// 																				TRACKER.showMessageDialog("'.Yii::t('site', 'This e-mail is not registered!').'");																									
// 																			}
// 																		}													
// 																	}
// 																	catch (error){
// 																		$("#forgotPasswordWindow").html(result);
// 																	}
// 																 }',
// 													 ),
// 												null);

			$this->widget('zii.widgets.jui.CJuiButton', array(
					'name'=>'ajaxForgotPassword',
					'caption'=>Yii::t('site', 'Submit'),
					'id'=>'forgotPasswordAjaxButton',
					'htmlOptions'=>array('type'=>'submit','ajax'=>array('type'=>'POST','url'=>array('site/forgotPassword'),
							'success'=> 'function(result){
								try
								{
									var obj = jQuery.parseJSON(result);
										
									if (obj.result)
									{
										if (obj.result == "1") 
										{
											$("#forgotPasswordWindow").dialog("close");
											TRACKER.showMessageDialog("'.Yii::t('site', 'We have sent the password reset link to your mailbox. </br> Please make sure you check the spam folder as well.').'");
										}
										else if (obj.result == "0")
										{
											$("#forgotPasswordWindow").dialog("close");
											TRACKER.showMessageDialog("'.Yii::t('site', 'This e-mail is not registered!').'");																									
										}
									}	
								}
								catch (error)
								{
									$("#forgotPasswordWindow").html(result);
								}
							}',
					))
			));			
			
			
			?>
												
			<?php 
// 				echo CHtml::htmlButton('Cancel',  
// 													array(
// 														'onclick'=> '$("#forgotPasswordWindow").dialog("close"); return false;',
// 														 ),
// 													null);
				 
				$this->widget('zii.widgets.jui.CJuiButton', array(
						'name'=>'forgotPasswordCancel',
						'caption'=>Yii::t('common', 'Cancel'),
						'id'=>'forgotPasswordCancelButton',
						'onclick'=> 'js:function(){$("#forgotPasswordWindow").dialog("close"); return false;}'
				));			
			?>												
		</div>
	
	<?php $this->endWidget(); ?>
</div>
<?php 
	$this->endWidget('zii.widgets.jui.CJuiDialog');
?>
