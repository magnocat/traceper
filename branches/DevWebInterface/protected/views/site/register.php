<?php 
$this->beginWidget('zii.widgets.jui.CJuiDialog', array(
	    'id'=>'registerWindow',
	    // additional javascript options for the dialog plugin
	    'options'=>array(
	        'title'=>Yii::t('general', 'Register'),
	        'autoOpen'=>false,
	        'modal'=>true, 
			'resizable'=>false,
			'width'=> '280px'      
	    ),
	));
?>
	<div class="form">
	<?php $form=$this->beginWidget('CActiveForm', array(
		'id'=>'register-form',
		'enableClientValidation'=>true,
		'clientOptions'=> array(
							'validateOnSubmit'=> true,
							'validateOnChange'=>false,
						 ),
	
	)); ?>
		<div class="row">
			<?php echo $form->labelEx($model,'email'); ?>
			<?php echo $form->textField($model,'email'); ?>
			<?php $errorMessage = $form->error($model,'email'); 
				  if (strip_tags($errorMessage) == '') { echo '<div class="errorMessage">&nbsp;</div>'; }
				  else { echo $errorMessage; }
			?>
		</div>
	
		<div class="row">
			<?php echo $form->labelEx($model,'name'); ?>
			<?php echo $form->textField($model,'name'); ?>
			<?php $errorMessage = $form->error($model,'name');  
				  if (strip_tags($errorMessage) == '') { echo '<div class="errorMessage">&nbsp;</div>'; }
				  else { echo $errorMessage; }
			?>	  			
		</div>
	
		<div class="row">
			<?php echo $form->labelEx($model,'password'); ?>
			<?php echo $form->passwordField($model,'password'); ?>
			<?php $errorMessage = $form->error($model,'password'); 
				  if (strip_tags($errorMessage) == '') { echo '<div class="errorMessage">&nbsp;</div>'; }
				  else { echo $errorMessage; }	
			?>	  		
		</div>
		
		<div class="row">
			<?php echo $form->labelEx($model,'passwordAgain'); ?>
			<?php echo $form->passwordField($model,'passwordAgain'); ?>
			<?php $errorMessage = $form->error($model,'passwordAgain'); 
				  if (strip_tags($errorMessage) == '') { echo '<div class="errorMessage">&nbsp;</div>'; }
				  else { echo $errorMessage; }	
			?>	  		
		</div>		
	
		<div class="row buttons">
			<?php echo CHtml::ajaxSubmitButton('Register', $this->createUrl('site/register'), 
												array(
													'success'=> 'function(result){ 
																	try {
																		var obj = jQuery.parseJSON(result);
																		if (obj.result && obj.result == "1") 
																		{
																			$("#registerWindow").dialog("close");
																			
																			$("#messageWindow").dialog("open");
																		}
																	}
																	catch (error){
																		$("#registerWindow").html(result);
																		var confirmMessage = document.getElementById("messageWindow");
																		if(confirmMessage.style.display != "block") {																		
																			confirmMessage.style.display = "none";
																		}
																	}
																 }',
													 ),
												null); ?>
												
			<?php echo CHtml::htmlButton('Cancel',  
												array(
													'onclick'=> '$("#registerWindow").dialog("close"); return false;',
													 ),
												null); ?>												
		</div>
	
	<?php $this->endWidget(); ?>
</div>
<?php 
	$this->endWidget('zii.widgets.jui.CJuiDialog');
?>


<?php 
$this->beginWidget('zii.widgets.jui.CJuiDialog', array(
	    'id'=>'messageWindow',
	    // additional javascript options for the dialog plugin
	    'options'=>array(
	        'title'=>Yii::t('general', 'Info Message'),
	        'autoOpen'=>false,
	        'modal'=>true, 
			'resizable'=>false,
			'width'=>'520px',
			'rows'=>1,
			'htmlOptions'=>array('style'=>'text-align: center'),
	    ),
	));
?>	
	
	<div align="center" class="row"> <?php echo '<br/> An activation mail is sent to your e-mail address <br/><br/>'; ?> </div>
	<div align="center" class="row buttons"> <?php echo CHtml::htmlButton(Yii::t('general', 'Ok'), array('onclick'=>'$("#messageWindow").dialog("close"); return false;','width'=>'200px'), null); ?> </div>
		 		
<?php	
$this->endWidget('zii.widgets.jui.CJuiDialog');
?>