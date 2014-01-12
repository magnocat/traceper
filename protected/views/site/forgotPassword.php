<?php 
$this->beginWidget('zii.widgets.jui.CJuiDialog', array(
	    'id'=>'forgotPasswordWindow',
	    // additional javascript options for the dialog plugin
	    'options'=>array(
	        'title'=>Yii::t('site', 'Forgot Password?'),
	        'autoOpen'=>false,
	        'modal'=>true, 
			'resizable'=>false,
			'width'=> '600px',
	    	//'close' => 'js:function(){ showFormErrorsIfExist(); }'
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
	
		<div id="ajaxForgotPasswordResponse">
			<div class="row" style="padding-top:1em;">
				<?php echo Yii::t('site', 'Enter your registered e-mail address into the below field and we will send you a link to change your password:'); ?>
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
// 					'name'=>'ajaxForgotPassword',
// 					'caption'=>Yii::t('site', 'Submit'),
// 					'id'=>'forgotPasswordAjaxButton-'.uniqid(), //Unique ID oluşturmayınca her ajaxta bir önceki sorgular da tekrarlanıyor
// 					'htmlOptions'=>array('type'=>'submit','ajax'=>array('type'=>'POST','url'=>array('site/forgotPassword'),
// 							'success'=> 'function(result){
// 								try
// 								{
// 									var obj = jQuery.parseJSON(result);
										
// 									if (obj.result)
// 									{
// 										if (obj.result == "1") 
// 										{
// 											$("#forgotPasswordWindow").dialog("close");
// 											TRACKER.showLongMessageDialog("'.Yii::t('site', 'We have sent the password reset link to your mail address \"<b>').'" + obj.email + "'.Yii::t('site', '</b>\". </br></br>Please make sure you check the spam/junk folder as well. The links in a spam/junk folder may not work sometimes; so if you face such a case, mark our e-mail as \"Not Spam\" and reclick the link.').'");
// 										}
// 										else if (obj.result == "0")
// 										{
// 											$("#forgotPasswordWindow").dialog("close");
// 											TRACKER.showMessageDialog("'.Yii::t('site', 'An error occured while sending the e-mail. Please retry the process and if the error persists please contact us.').'");																									
// 										}
// 									}	
// 								}
// 								catch (error)
// 								{
// 									$("#forgotPasswordWindow").html(result);
// 								}
// 							}',
// 					))
// 			));
			
			$app = Yii::app();
				
			echo CHtml::ajaxLink('<button class="btn btn-sliding-green btn-sliding-green-a icon-inviteUsers" style="'.(($app->language == 'en')?'padding-left:28px;padding-right:28px;':'padding-left:28px;padding-right:28px;').'">'.'<span style="font-family:Helvetica">'.Yii::t('site', 'Submit').'</span>'.'</button>', $this->createUrl('site/forgotPassword'),
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
														$("#forgotPasswordWindow").dialog("close");
														TRACKER.showLongMessageDialog("'.Yii::t('site', 'We have sent the password reset link to your mail address \"<b>').'" + obj.email + "'.Yii::t('site', '</b>\". </br></br>Please make sure you check the spam/junk folder as well. The links in a spam/junk folder may not work sometimes; so if you face such a case, mark our e-mail as \"Not Spam\" and reclick the link.').'");
													}
													else if (obj.result == "0")
													{
														$("#forgotPasswordWindow").dialog("close");
														TRACKER.showMessageDialog("'.Yii::t('site', 'An error occured while sending the e-mail. Please retry the process and if the error persists please contact us.').'");
													}
												}
											}
											catch (error)
											{
												//$("#forgotPasswordWindow").html(result);
							
												$("#hiddenAjaxResponseForForgotPassword").html(result);
												$("#ajaxForgotPasswordResponse").html($("#hiddenAjaxResponseForForgotPassword #ajaxForgotPasswordResponse").html());
												$("#hiddenAjaxResponseForForgotPassword").html("");							
											}
										}',
					),
					array('id'=>'forgotPasswordAjaxButton-'.uniqid(), 'style'=>'padding-right:4px;'));			
			?>
												
			<?php 
// 				echo CHtml::htmlButton('Cancel',  
// 													array(
// 														'onclick'=> '$("#forgotPasswordWindow").dialog("close"); return false;',
// 														 ),
// 													null);
				 
// 				$this->widget('zii.widgets.jui.CJuiButton', array(
// 						'name'=>'forgotPasswordCancel',
// 						'caption'=>Yii::t('common', 'Cancel'),
// 						'id'=>'forgotPasswordCancelButton',
// 						'onclick'=> 'js:function(){$("#forgotPasswordWindow").dialog("close"); return false;}'
// 				));

				echo CHtml::ajaxLink('<button class="btn btn-sliding-red btn-sliding-red-a icon-close" style="'.(($app->language == 'en')?'padding-left:25px;padding-right:25px;':'padding-left:28px;padding-right:28px;').'">'.'<span style="font-family:Helvetica">'.Yii::t('common', 'Cancel').'</span>'.'</button>', '#',
						array(),
						array('id'=>'forgotPasswordCancelButton', 'onclick'=>'$("#forgotPasswordWindow").dialog("close"); return false;'));				
			?>												
		</div>
	
	<?php $this->endWidget(); ?>
</div>
<?php 
	$this->endWidget('zii.widgets.jui.CJuiDialog');
?>

<!-- Diyaloglarda main layout'taki hiddenAjaxResponseToParse kullanilamadigindan (diyaloglar dinamik olarak sonradan eklendiginden yukarida -->
<!-- kaliyor) ve ayni isimle olunca da calismadigindan diyaloglarin view dosyalarinin sonuna gizli bir div tanimlaniyor -->
<div id="hiddenAjaxResponseForForgotPassword" style="display:none;"></div>