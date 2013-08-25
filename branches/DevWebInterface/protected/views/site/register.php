
<div class="form" style='height:100%;'>
	<?php $form=$this->beginWidget('CActiveForm', array(
			'id'=>'register-form',
			'enableClientValidation'=>true,
			'htmlOptions'=>array('style'=>'height:100%;'),
	)); ?>

	<div style="padding-left:15px;font-size:3em;">
		<?php echo $form->labelEx($model,'register'); ?>
	</div>

	<div class="sideMenu">
		<div style="position:absolute;display:inline-block;vertical-align:top;width:49%;">
		<?php echo $form->labelEx($model,'name'); ?>
		<?php echo $form->textField($model,'name', array('size'=>'22%','maxlength'=>128,'tabindex'=>7)); ?>
		<?php $errorMessage = $form->error($model,'name');  
			if (strip_tags($errorMessage) == '') {
				echo '<div class="errorMessage">&nbsp;</div>';
			}
			else { echo '<div class="errorMessage" style="font-size: 1.1em;">'.$errorMessage.'</div>';
			}
		?>
		</div>
		
		<div style="position:absolute;left:13.6em;display:inline-block;vertical-align:top;width:49%;">
		<?php echo $form->labelEx($model,'lastName'); ?>
		<?php echo $form->textField($model,'lastName', array('size'=>'22%','maxlength'=>128,'tabindex'=>8)); ?>
		<?php $errorMessage = $form->error($model,'lastName');  
			if (strip_tags($errorMessage) == '') {
				echo '<div class="errorMessage">&nbsp;</div>';
			}
			else { echo '<div class="errorMessage" style="font-size: 1.1em;">'.$errorMessage.'</div>';
			}
		?>
		</div>																
	</div>							
	
	<div class="sideMenu">
		<?php echo $form->labelEx($model,'email'); ?>
		<?php echo $form->textField($model,'email', array('size'=>'50%','maxlength'=>128,'tabindex'=>9)); ?>
		<?php $errorMessage = $form->error($model,'email'); 
			if (strip_tags($errorMessage) == '') {
				echo '<div class="errorMessage">&nbsp;</div>';
			}
			else { echo '<div class="errorMessage" style="font-size: 1.1em;">'.$errorMessage.'</div>';
			}
		?>								
	</div>

	<div class="sideMenu">
		<?php echo $form->labelEx($model,'emailAgain'); ?>
		<?php echo $form->textField($model,'emailAgain', array('size'=>'50%','maxlength'=>128,'tabindex'=>10)); ?>
		<?php $errorMessage = $form->error($model,'emailAgain'); 
			if (strip_tags($errorMessage) == '') {
				echo '<div class="errorMessage">&nbsp;</div>';
			}
			else { echo '<div class="errorMessage" style="font-size: 1.1em;">'.$errorMessage.'</div>';
			}
		?>								
	</div>							

	<div class="sideMenu">
		<div style="position:absolute;display:inline-block;vertical-align:top;width:49%;">
		<?php echo $form->labelEx($model,'password'); ?>
		<?php echo $form->passwordField($model,'password', array('size'=>'22%','maxlength'=>128,'tabindex'=>11)); ?>
		<?php $errorMessage = $form->error($model,'password');
			if (strip_tags($errorMessage) == '') {
				echo '<div class="errorMessage">&nbsp;</div>';
			}
			else { echo '<div class="errorMessage" style="font-size: 1.1em;">'.$errorMessage.'</div>';
			}
		?>
		</div>
		
		<div style="position:absolute;left:13.6em;display:inline-block;vertical-align:top;width:49%;">
		<?php echo $form->labelEx($model,'passwordAgain'); ?>
		<?php echo $form->passwordField($model,'passwordAgain', array('size'=>'22%','maxlength'=>128,'tabindex'=>12)); ?>
		<?php $errorMessage = $form->error($model,'passwordAgain'); 
			if (strip_tags($errorMessage) == '') {
				echo '<div class="errorMessage">&nbsp;</div>';
			}
			else { echo '<div class="errorMessage" style="font-size: 1.1em;">'.$errorMessage.'</div>';
			}
		?>
		</div>																
	</div>
	
	<div class="sideMenu">
		<div style="position:absolute;display:inline-block;vertical-align:top;width:40%;">
			<?php
			$this->widget('zii.widgets.jui.CJuiButton', array(
					'name'=>'ajaxRegister',
					'caption'=>Yii::t('site', 'Sign Up'),
					'id'=>'registerAjaxButton',
					'htmlOptions'=>array('type'=>'submit','ajax'=>array('type'=>'POST','url'=>array('site/register'),
											'success'=> 'function(msg){																																								
															try
															{																								
																var obj = jQuery.parseJSON(msg);
																	
																if (obj.result)
																{
																	if (obj.result == "1") 
																	{
																		TRACKER.showLongMessageDialog("'.Yii::t('site', 'Your account created successfully. ').Yii::t('site', 'We have sent an account activation link to your mail address \"<b>').'" + obj.email + "'.Yii::t('site', '</b>\". </br></br>Please make sure you check the spam/junk folder as well. The links in a spam/junk folder may not work sometimes; so if you face such a case, mark our e-mail as \"Not Spam\" and reclick the link.').'");
																	}
																	else if (obj.result == "2")
																	{
																		TRACKER.showLongMessageDialog("'.Yii::t('site', 'Your account created successfully, but an error occured while sending your account activation e-mail. You could request your activation e-mail by clicking the link \"Not Received Our Activation E-Mail?\" just below the register form. If the error persists, please contact us about the problem.').'");
																	}
																	else if (obj.result == "0")
																	{
																		TRACKER.showMessageDialog("'.Yii::t('common', 'Sorry, an error occured in operation').'");																									
																	}																													
																}
															}
															catch (error)
															{
																$("#forRegisterRefresh").html(msg);
															}			
											}',
					))
			));								
			?>
		</div>
		
		<div style="position:absolute;left:9em;top:1.2em;display:inline-block;vertical-align:top;width:60%;">
										<?php
		echo CHtml::ajaxLink('<div id="activationNotReceived">'.Yii::t('site', 'Not Received Our Activation E-Mail?').
							'</div>', $this->createUrl('site/activationNotReceived'),
				array(
						'complete'=> 'function() { $("#activationNotReceivedWindow").dialog("open"); return false;}',
						'update'=> '#activationNotReceivedWindow',
				),
				array(
						'id'=>'showActivationNotReceivedWindow','tabindex'=>14));							
		?>
		</div>
	</div>	

	<?php $this->endWidget(); ?>
</div>
