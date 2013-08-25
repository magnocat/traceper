	<div class="form">
	<?php $form=$this->beginWidget('CActiveForm', array(
		'id'=>'login-form',
		'enableClientValidation'=>true,
	
	)); ?>
		
			<div class="upperMenu">
				<div style="height:3.3em;top:0%;padding:0px;">
					<?php echo $form->labelEx($model,'email'); ?>
					<?php echo $form->textField($model,'email', array('size'=>'30%','maxlength'=>'30%','tabindex'=>1)); ?>
					<?php $errorMessage = $form->error($model,'email'); 
						  if (strip_tags($errorMessage) == '') { echo '<div class="errorMessage">&nbsp;</div>'; }
						  else { echo $errorMessage; }
					?>					
				</div>
				
				<div style="margin-top:18px;padding:0px;">
					<?php echo $form->checkBox($model,'rememberMe',array('size'=>5,'maxlength'=>128,'tabindex'=>4)); ?>
					<?php echo $form->label($model,'rememberMe'); ?>
				</div>									
			</div>

			<div class="upperMenu">
				<div style="height:3.3em;top:0%;padding:0px;">
					<?php echo $form->labelEx($model,'password'); ?>
					<?php echo $form->passwordField($model,'password', array('size'=>'30%','maxlength'=>'30%','tabindex'=>2)); ?>
					<?php $errorMessage = $form->error($model,'password'); 
						  if (strip_tags($errorMessage) == '') { echo '<div class="errorMessage">&nbsp;</div>'; }
						  else { echo $errorMessage; }
					?>					
				</div>
					
 				<div style="margin-top:18px;padding:0px;">
					<?php
					echo CHtml::ajaxLink('<div id="forgotPassword">'.Yii::t('site', 'Forgot Password?').
										'</div>', $this->createUrl('site/forgotPassword'),
							array(
									'complete'=> 'function() { $("#forgotPasswordWindow").dialog("open"); return false;}',
									'update'=> '#forgotPasswordWindow',
							),
							array(
									'id'=>'showForgotPasswordWindow','tabindex'=>5));							
					?>	 					
 				</div>								
			</div>							
											
			<div class="upperMenu" style="margin-top:0.7em;width:10%;min-width:5em;">
				<div style="height:3.3em;top:0%;padding:0px;">								
					<?php																											
					$this->widget('zii.widgets.jui.CJuiButton', array(
							'name'=>'ajaxLogin',
							'caption'=>Yii::t('site', 'Log in'),
							'id'=>'loginAjaxButton',
							'htmlOptions'=>array('type'=>'submit','style'=>'width:8.4em;','tabindex'=>3,'ajax'=>array('type'=>'POST','url'=>array('site/login'),'update'=>'#forAjaxRefresh'))
					));															
					?>
				</div>																					
			</div>				
	
	<?php $this->endWidget(); ?>
</div>
