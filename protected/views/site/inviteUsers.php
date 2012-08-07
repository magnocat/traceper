<?php 
$this->beginWidget('zii.widgets.jui.CJuiDialog', array(
	    'id'=>'inviteUsersWindow',
	    // additional javascript options for the dialog plugin
	    'options'=>array(
	        'title'=>Yii::t('general', 'Send invitations to your friends'),
	        'autoOpen'=>false,
	        'modal'=>true, 
			'resizable'=>false,
			'width'=> '380px'      
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
			<?php echo $form->labelEx($model,'emails'); ?>
			<?php echo $form->textarea($model,'emails', array('rows'=>5, 'cols'=>36,'resizable'=>false)); ?>
			<?php $errorMessage = $form->error($model,'emails'); 
				  if (strip_tags($errorMessage) == '') { echo '<div class="errorMessage">&nbsp;</div>'; }
				  else { echo $errorMessage; }
			?>
		</div>
	
		<div class="row">
			<?php echo $form->labelEx($model,'message'); ?>
			<?php echo $form->textArea($model,'message', array('rows'=>5, 'cols'=>36,'resizable'=>false)); ?>
			<?php $errorMessage = $form->error($model,'message');  
				  if (strip_tags($errorMessage) == '') { echo '<div class="errorMessage">&nbsp;</div>'; }
				  else { echo $errorMessage; }
			?>	  			
		</div>		
	
		<div class="row buttons">
			<?php echo CHtml::ajaxSubmitButton('Invite', $this->createUrl('site/inviteUsers'), 
												array(
													'success'=> 'function(result){ 
																	try {
																		var obj = jQuery.parseJSON(result);
																		if (obj.result && obj.result == "1") 
																		{
																			$("#inviteUsersWindow").dialog("close");
																		}
																	}
																	catch (error){
																		$("#inviteUsersWindow").html(result);
																	}
																 }',
													 ),
												null); ?>
												
			<?php echo CHtml::htmlButton('Cancel',  
												array(
													'onclick'=> '$("#inviteUsersWindow").dialog("close"); return false;',
													 ),
												null); ?>												
		</div>
	
	<?php $this->endWidget(); ?>
</div>
<?php 
	$this->endWidget('zii.widgets.jui.CJuiDialog');
?>