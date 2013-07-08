<?php 
$this->beginWidget('zii.widgets.jui.CJuiDialog', array(
	    'id'=>'contactWindow',
	    // additional javascript options for the dialog plugin
	    'options'=>array(
	        'title'=>Yii::t('layout', 'Contact'),
	        'autoOpen'=>false,
	        'modal'=>true, 
			'resizable'=>false,
			'width'=> '650px'      
	    ),
	));
?>
	<div class="form">
	<?php $form=$this->beginWidget('CActiveForm', array(
		'id'=>'contact-form',
		'enableClientValidation'=>true,
		'clientOptions'=> array(
							'validateOnSubmit'=> true,
							'validateOnChange'=>false,
						 ),
	
	)); ?>
	

		<div class="row">
			<?php echo $form->labelEx($model,'firstName'); ?>
			<?php echo $form->textField($model,'firstName', array('size'=>'30%','maxlength'=>'60%')); ?>
			<?php $errorMessage = $form->error($model,'firstName'); 
				  if (strip_tags($errorMessage) == '') { echo '<div class="errorMessage">&nbsp;</div>'; }
				  else { echo $errorMessage; }
			?>
		</div>
	
		<div class="row">
			<?php echo $form->labelEx($model,'lastName'); ?>
			<?php echo $form->textField($model,'lastName', array('size'=>'30%','maxlength'=>'60%')); ?>
			<?php $errorMessage = $form->error($model,'lastName'); 
				  if (strip_tags($errorMessage) == '') { echo '<div class="errorMessage">&nbsp;</div>'; }
				  else { echo $errorMessage; }
			?>
		</div>
	
		<div class="row">
			<?php echo $form->labelEx($model,'subject'); ?>
			<?php echo $form->textField($model,'subject', array('size'=>'30%','maxlength'=>'60%')); ?>
			<?php $errorMessage = $form->error($model,'subject'); 
				  if (strip_tags($errorMessage) == '') { echo '<div class="errorMessage">&nbsp;</div>'; }
				  else { echo $errorMessage; }
			?>
		</div>
		
		<div class="row">
			<?php echo $form->labelEx($model,'email'); ?>
			<?php echo $form->textField($model,'email', array('size'=>'30%','maxlength'=>'60%')); ?>
			<?php $errorMessage = $form->error($model,'email'); 
				  if (strip_tags($errorMessage) == '') { echo '<div class="errorMessage">&nbsp;</div>'; }
				  else { echo $errorMessage; }
			?>
		</div>
		
		<div class="row">
			<?php echo $form->labelEx($model,'detail'); ?>
			<?php echo $form->textArea($model,'detail',array('rows'=>'4%', 'cols'=>'65%')); ?>
			<?php $errorMessage = $form->error($model,'detail'); 
				  if (strip_tags($errorMessage) == '') { echo '<div class="errorMessage">&nbsp;</div>'; }
				  else { echo $errorMessage; }
			?>
		</div>
		
	<?php if(CCaptcha::checkRequirements()): ?>
		<div class="row">
			<?php echo $form->labelEx($model,'verifyCode'); ?>
			<div class="hint"><?php echo Yii::t('site', 'Please enter the letters as they are shown in the image below. Letters are not case-sensitive.'); ?></div>			
			<div>
			<?php $this->widget('CCaptcha'); ?>
			<?php echo $form->textField($model,'verifyCode'); ?>
			</div>
			<?php $errorMessage = $form->error($model,'verifyCode'); 
				  if (strip_tags($errorMessage) == '') { echo '<div class="errorMessage">&nbsp;</div>'; }
				  else { echo $errorMessage; }
			?>	
		</div>
	<?php endif; ?>						
	
		<div class="row buttons">
			<?php
				$this->widget('zii.widgets.jui.CJuiButton', array(
						'name'=>'ajaxContact',
						'caption'=>Yii::t('site', 'Submit'),
						'id'=>'contactAjaxButton',
						'htmlOptions'=>array('type'=>'submit','ajax'=>array('type'=>'POST','url'=>array('site/contact'),
										'success'=> 'function(result)
													{
														try 
														{
															var obj = jQuery.parseJSON(result);
								
															if(obj.result)
															{
																$("#contactWindow").dialog("close");
									
																if(obj.result == "1")
																{
																	TRACKER.showMessageDialog("'.Yii::t('site', 'Thank you for contacting us, we will respond to you as soon as possible.').'");
																}
																else
																{
																	TRACKER.showMessageDialog("'.Yii::t('site', 'An error occured while sending the form. If the problem persists, please send an e-mail to contact@traceper.com').'");								
																}	
															}
														}
														catch(error)
														{
															$("#contactWindow").html(result);
														}
													}',
						))
				));				
			?>
												
			<?php
				$this->widget('zii.widgets.jui.CJuiButton', array(
						'name'=>'contactCancel',
						'caption'=>Yii::t('common', 'Cancel'),
						'id'=>'contactCancelButton',
						'onclick'=> 'js:function(){$("#contactWindow").dialog("close"); return false;}'
				));			
			?>												
		</div>
	
	<?php $this->endWidget(); ?>
</div>
<?php 
	$this->endWidget('zii.widgets.jui.CJuiDialog');
?>