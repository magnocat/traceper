
<?php 
$this->beginWidget('zii.widgets.jui.CJuiDialog', array(
	    'id'=>'activationNotReceivedWindow',
	    // additional javascript options for the dialog plugin
	    'options'=>array(
	        'title'=>Yii::t('site', 'Not Received Our Activation E-Mail?'),
	        'autoOpen'=>false,
	        'modal'=>true, 
			'resizable'=>false,
			'width'=>'600px',
	    	//'close'=>'js:function(){ showFormErrorsIfExist(); }' 	
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
	
		<div id="ajaxActivationNotReceivedResponse">
			<div class="row" style="padding-top:1em;">
				<?php echo Yii::t('site', 'If you have not received our account activation e-mail although you sent the regisration form, please enter your registration e-mail address into the field below and we will send you a link to activate your account again:'); ?>
			</div>		

			<div class="row">
				<?php echo $form->labelEx($model,'email'); ?>
				<?php echo $form->textField($model,'email', array('size'=>'50%','maxlength'=>128,'tabindex'=>1));?>
				<?php $errorMessage = $form->error($model,'email'); 
					  if (strip_tags($errorMessage) == '') { echo '<div class="errorMessage">&nbsp;</div>'; }
					  else { echo $errorMessage; }
				?>
			</div>
		</div>	
	
		<div class="row buttons">
			<?php 
// 			$this->widget('zii.widgets.jui.CJuiButton', array(
// 					'name'=>'ajaxActivationNotReceived',
// 					'caption'=>Yii::t('site', 'Submit'),
// 					'id'=>'activationNotReceivedAjaxButton-'.uniqid(), //Unique ID oluşturmayınca her ajaxta bir önceki sorgular da tekrarlanıyor
// 					'htmlOptions'=>array('type'=>'submit','ajax'=>array('type'=>'POST','url'=>array('site/activationNotReceived'),
// 							'success'=> 'function(result){
// 								try
// 								{
// 									var obj = jQuery.parseJSON(result);
										
// 									if (obj.result)
// 									{
// 										if (obj.result == "1") 
// 										{
// 											$("#activationNotReceivedWindow").dialog("close");
// 											TRACKER.showLongMessageDialog("'.Yii::t('site', 'We have sent an account activation link to your mail address \"<b>').'" + obj.email + "'.Yii::t('site', '</b>\". </br></br>Please make sure you check the spam/junk folder as well. The links in a spam/junk folder may not work sometimes; so if you face such a case, mark our e-mail as \"Not Spam\" and reclick the link.').'");
// 										}
// 										else if (obj.result == "0")
// 										{
// 											$("#activationNotReceivedWindow").dialog("close");
// 											TRACKER.showMessageDialog("'.Yii::t('site', 'An error occured while sending the e-mail. Please retry the process and if the error persists please contact us.').'");																									
// 										}
// 									}	
// 								}
// 								catch (error)
// 								{
// 									$("#activationNotReceivedWindow").html(result);
// 								}
// 							}',
// 					))
// 			));			
			
			$app = Yii::app();
			
			echo CHtml::ajaxLink('<button class="btn btn-sliding-green btn-sliding-green-a icon-inviteUsers" style="'.(($app->language == 'en')?'padding-left:28px;padding-right:28px;':'padding-left:28px;padding-right:28px;').'">'.'<span style="font-family:Helvetica">'.Yii::t('site', 'Submit').'</span>'.'</button>', $this->createUrl('site/activationNotReceived'),
					array(
							'type'=>'POST',
							'success'=> 'function(result){
								try
								{
									var obj = jQuery.parseJSON(result);
										
									if (obj.result)
									{
										if (obj.result == "1") 
										{
											$("#activationNotReceivedWindow").dialog("close");
											TRACKER.showLongMessageDialog("'.Yii::t('site', 'We have sent an account activation link to your mail address \"<b>').'" + obj.email + "'.Yii::t('site', '</b>\". </br></br>Please make sure you check the spam/junk folder as well. The links in a spam/junk folder may not work sometimes; so if you face such a case, mark our e-mail as \"Not Spam\" and reclick the link.').'");
										}
										else if (obj.result == "0")
										{
											$("#activationNotReceivedWindow").dialog("close");
											TRACKER.showMessageDialog("'.Yii::t('site', 'An error occured while sending the e-mail. Please retry the process and if the error persists please contact us.').'");																									
										}
									}	
								}
								catch (error)
								{
									//$("#activationNotReceivedWindow").html(result);
							
									$("#hiddenAjaxResponseForActivationNotReceived").html(result);
									$("#ajaxActivationNotReceivedResponse").html($("#hiddenAjaxResponseForActivationNotReceived #ajaxActivationNotReceivedResponse").html());
									$("#hiddenAjaxResponseForActivationNotReceived").html("");								
								}
							}',
					),
					array('id'=>'activationNotReceivedAjaxButton-'.uniqid(), 'style'=>'padding-right:4px;'));			
			?>
												
			<?php
// 				$this->widget('zii.widgets.jui.CJuiButton', array(
// 						'name'=>'activationNotReceivedCancel',
// 						'caption'=>Yii::t('common', 'Cancel'),
// 						'id'=>'activationNotReceivedCancelButton',
// 						'onclick'=> 'js:function(){$("#activationNotReceivedWindow").dialog("close"); return false;}'
// 				));

				echo CHtml::ajaxLink('<button class="btn btn-sliding-red btn-sliding-red-a icon-close" style="'.(($app->language == 'en')?'padding-left:25px;padding-right:25px;':'padding-left:28px;padding-right:28px;').'">'.'<span style="font-family:Helvetica">'.Yii::t('common', 'Cancel').'</span>'.'</button>', '#',
						array(),
						array('id'=>'activationNotReceivedCancelButton', 'onclick'=>'$("#activationNotReceivedWindow").dialog("close"); return false;'));				
			?>												
		</div>
	
	<?php $this->endWidget(); ?>
</div>
<?php 
	$this->endWidget('zii.widgets.jui.CJuiDialog');
?>

<!-- Diyaloglarda main layout'taki hiddenAjaxResponseToParse kullanilamadigindan (diyaloglar dinamik olarak sonradan eklendiginden yukarida -->
<!-- kaliyor) ve ayni isimle olunca da calismadigindan diyaloglarin view dosyalarinin sonuna gizli bir div tanimlaniyor -->
<div id="hiddenAjaxResponseForActivationNotReceived" style="display:none;"></div>