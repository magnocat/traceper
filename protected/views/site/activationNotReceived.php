
<?php 
$this->beginWidget('zii.widgets.jui.CJuiDialog', array(
	    'id'=>'activationNotReceivedWindow',
	    // additional javascript options for the dialog plugin
	    'options'=>array(
	        'title'=>Yii::t('site', 'Not Received Our Activation E-Mail?'),
	        'autoOpen'=>false,
	        'modal'=>true, 
			'resizable'=>false,
			'width'=> '50%'      
	    ),
	));
?>
	<div class="form">
	<?php $form=$this->beginWidget('CActiveForm', array(
		'id'=>'activationNotReceived-form',
		'enableClientValidation'=>true,
		'clientOptions'=> array(
							'validateOnSubmit'=> true,
							'validateOnChange'=>false,
						 ),
	
	)); ?>
	
		<div class="row" style="padding-top:1em">
			<?php echo Yii::t('site', 'If you have not received our account activation e-mail although you sent the regisration form, please enter your registration e-mail address into the field below and we will send you a link to activate your account again:'); ?>
		</div>	
		
		</br>	

		<div class="row">
			<?php echo $form->labelEx($model,'email'); ?>
			<?php echo $form->textField($model,'email', array('size'=>40,'maxlength'=>128,'tabindex'=>1));?>
			<?php $errorMessage = $form->error($model,'email'); 
				  if (strip_tags($errorMessage) == '') { echo '<div class="errorMessage">&nbsp;</div>'; }
				  else { echo $errorMessage; }
			?>
		</div>
	
		<div class="row buttons">
			<?php 
			$this->widget('zii.widgets.jui.CJuiButton', array(
					'name'=>'ajaxActivationNotReceived',
					'caption'=>Yii::t('site', 'Submit'),
					'id'=>'activationNotReceivedAjaxButton',
					'htmlOptions'=>array('type'=>'submit','ajax'=>array('type'=>'POST','url'=>array('site/activationNotReceived'),
							'success'=> 'function(result){
								try
								{
									var obj = jQuery.parseJSON(result);
										
									if (obj.result)
									{
										if (obj.result == "1") 
										{
											$("#activationNotReceivedWindow").dialog("close");
											TRACKER.showMessageDialog("'.Yii::t('site', 'We have sent an account activation link to your mailbox. </br> Please make sure you check the spam folder as well. </br> The links in a spam folder may not work sometimes, so if you face such a case </br> please mark our e-mail as \'Not Spam\' and reclick the link.').'");
										}
										else if (obj.result == "0")
										{
											$("#activationNotReceivedWindow").dialog("close");
											TRACKER.showMessageDialog("'.Yii::t('site', 'Activation e-mail cannot be sent! If the problem persits, please inform <a href=\'mailto:contact@traceper.com\'>us</a>.').'");																									
										}
									}	
								}
								catch (error)
								{
									$("#activationNotReceivedWindow").html(result);
								}
							}',
					))
			));			
			
			
			?>
												
			<?php
				$this->widget('zii.widgets.jui.CJuiButton', array(
						'name'=>'activationNotReceivedCancel',
						'caption'=>Yii::t('common', 'Cancel'),
						'id'=>'activationNotReceivedCancelButton',
						'onclick'=> 'js:function(){$("#activationNotReceivedWindow").dialog("close"); return false;}'
				));			
			?>												
		</div>
	
	<?php $this->endWidget(); ?>
</div>
<?php 
	$this->endWidget('zii.widgets.jui.CJuiDialog');
?>
