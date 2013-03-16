	<div class="form">
	<?php $form=$this->beginWidget('CActiveForm', array(
		'id'=>'login-form',
		'enableClientValidation'=>true,
	
	)); ?>
		
		<div class="upperMenu" style="margin-top:0.8em;width:11%;">
			<div class="sideMenu" style="top:0%;padding:0px;">								
				<?php										
								//echo CHtml::ajaxSubmitButton(Yii::t('site','Login'), array('site/login'), array('update'=>'#forAjaxRefresh'), array('id'=>'loginAjaxButton','class'=>'ui-button ui-widget ui-state-default ui-corner-all','tabindex'=>4));
								
// 								echo CHtml::ajaxSubmitButton(Yii::t('site','Login'), array('site/login'), 
// 										array('success'=>'function(data){
// 												$("#forAjaxRefresh").html(data);
// 										}'),										
// 										array('id'=>'loginAjaxButton','class'=>'ui-button ui-widget ui-state-default ui-corner-all','tabindex'=>4));
								
								$this->widget('zii.widgets.jui.CJuiButton', array(
										'name'=>'ajaxLogin',
										'caption'=>Yii::t('site', 'Login'),
										'id'=>'loginAjaxButton',
										'htmlOptions'=>array('type'=>'submit','ajax'=>array('type'=>'POST','url'=>array('site/login'),'update'=>'#forAjaxRefresh'))
								));															
								?>
			</div>
			
			<div class="sideMenu" style="top:20%;padding:0px;display:inline;">
				<?php echo $form->checkBox($model,'rememberMe',array('size'=>5,'maxlength'=>128,'tabindex'=>3)); ?>
				<?php echo $form->label($model,'rememberMe'); ?>
			</div>																									
		</div>	
		
		<div class="upperMenu">
			<?php echo $form->labelEx($model,'password'); ?>
			<?php echo $form->passwordField($model,'password', array('size'=>25,'maxlength'=>128,'tabindex'=>2)); ?>
			<?php $errorMessage = $form->error($model,'password'); 
				  if (strip_tags($errorMessage) == '') { echo '<div class="errorMessage">&nbsp;</div>'; }
				  else { echo $errorMessage; }
			?>
		</div>	
		
		<div class="upperMenu">
			<?php echo $form->labelEx($model,'email'); ?>
			<?php echo $form->textField($model,'email', array('size'=>25,'maxlength'=>128,'tabindex'=>1)); ?>
			<?php $errorMessage = $form->error($model,'email'); 
				  if (strip_tags($errorMessage) == '') { echo '<div class="errorMessage">&nbsp;</div>'; }
				  else { echo $errorMessage; }
			?>			
		</div>				
	
	<?php $this->endWidget(); ?>
</div>
