<?php 
$this->beginWidget('zii.widgets.jui.CJuiDialog', array(
	    'id'=>'inviteUsersWindow',
	    // additional javascript options for the dialog plugin
	    'options'=>array(
	        'title'=>Yii::t('site', 'Invite your friends to Traceper'),
	        'autoOpen'=>false,
	        'modal'=>true, 
			'resizable'=>false,
			'width'=> '380px',
	    	'close' => 'js:function(){ $("#InviteUsersForm_emails").tooltipster("hide"); }'
	    ),
	));

Yii::app()->clientScript->registerScript('emailsListTooltip',
		'		
		$("#InviteUsersForm_emails").tooltipster({
			theme: ".tooltipster-info",
			trigger: "custom",
			maxWidth: 300,
			onlyOne: false,
			position: "right",
			interactive: true,
			zIndex:100000
		});

	 	$("#InviteUsersForm_emails").focus(function ()	{
	 		$("#InviteUsersForm_emails").tooltipster("update", TRACKER.langOperator.invitedEmailsNotificationMessage);
	 		$("#InviteUsersForm_emails").tooltipster("show"); 		
		});

	 	$("#InviteUsersForm_emails").blur(function ()	{
	 		$("#InviteUsersForm_emails").tooltipster("hide"); 		
		});
		',
		CClientScript::POS_HEAD);
?>
	<div class="form">
	<?php $form=$this->beginWidget('CActiveForm', array(
		'id'=>'inviteUsers-form',
		'enableClientValidation'=>true,
		'clientOptions'=> array(
							'validateOnSubmit'=> true,
							'validateOnChange'=>false,
						 ),
	
	)); ?>
	
		<div id="ajaxInviteUsersResponse">
			<div class="row">
				<?php echo $form->labelEx($model,'emails'); ?>
				<?php echo $form->textarea($model,'emails', array('rows'=>5, 'cols'=>36,'resizable'=>false)); ?>
				<?php $errorMessage = $form->error($model,'emails'); 
					  if (strip_tags($errorMessage) == '') { echo '<div class="errorMessage">&nbsp;</div>'; }
					  else { echo $errorMessage; }
				?>
			</div>
	
			<div class="row">
				<?php echo $form->labelEx($model,'invitationMessage'); ?>
				<?php echo $form->textArea($model,'invitationMessage', array('rows'=>5, 'cols'=>36,'resizable'=>false)); ?>
				<?php $errorMessage = $form->error($model,'invitationMessage');  
					  if (strip_tags($errorMessage) == '') { echo '<div class="errorMessage">&nbsp;</div>'; }
					  else { echo $errorMessage; }
				?>	  			
			</div>
		</div>			
	
		<div class="row buttons">
			<?php 
// 			echo CHtml::ajaxSubmitButton('Invite', $this->createUrl('site/inviteUsers'), 
// 												array(
// 													'success'=> 'function(result){ 
// 																	try {
// 																		var obj = jQuery.parseJSON(result);
// 																		if (obj.result && obj.result == "1") 
// 																		{
// 																			$("#inviteUsersWindow").dialog("close");
// 																		}
// 																	}
// 																	catch (error){
// 																		$("#inviteUsersWindow").html(result);
// 																	}
// 																 }',
// 													 ),
// 												null); 
						
// 			$this->widget('zii.widgets.jui.CJuiButton', array(
// 					'name'=>'ajaxInviteUsers',
// 					'caption'=>Yii::t('site', 'Invite'),
// 					'id'=>'inviteUsersAjaxButton-'.uniqid(), //Unique ID oluşturmayınca her ajaxta bir önceki sorgular da tekrarlanıyor
// 					'htmlOptions'=>array('type'=>'submit','ajax'=>array('type'=>'POST','url'=>array('site/inviteUsers'),
// 							'success'=> 'function(result){
// 															try 
// 															{
// 																var obj = jQuery.parseJSON(result);
							
// 																if (obj.result && obj.result == "1")
// 																{
// 																	$("#inviteUsersWindow").dialog("close");
// 																	TRACKER.showLongMessageDialog("'.Yii::t('site', 'Invitations sent successfully...').'<br/><br/>'.Yii::t('site', 'E-mails may sometimes not reach to inbox of the recipients but spam/junk box. Therefore we recommend you to contact with the invitees thereafter in order to assure that they have received your invitation.').'");
// 																}
// 																else if(obj.result && obj.result == "Duplicate Entry")
// 																{
// 																	$("#inviteUsersWindow").html(result);
// 																	$("#inviteUsersWindow").dialog("close");

// 																	var msgString = "'.Yii::t('site', 'Since invitations already sent for the e-mails below, <br/> only the other invitations sent!').'<br/><br/>";
							
// 																	for (var i=0;i<obj.emails.length;i++)
// 																	{
// 																		msgString = msgString + obj.emails[i] + "<br/>";
// 																	}
							
// 																	TRACKER.showMessageDialog(msgString);
// 																}							
// 															}																
// 															catch (error)
// 															{
// 																$("#inviteUsersWindow").html(result);
// 															}
// 														}',
// 					))
// 			));

			$app = Yii::app();
				
			echo CHtml::ajaxLink('<button class="btn btn-sliding-green btn-sliding-green-a icon-inviteUsers" style="'.(($app->language == 'en')?'padding-left:28px;padding-right:28px;':'padding-left:28px;padding-right:28px;').'">'.'<span style="font-family:Helvetica">'.Yii::t('site', 'Send Invitation').'</span>'.'</button>', $this->createUrl('site/inviteUsers'),
					array(
							'type'=>'POST',
							'success'=> 'function(result){
											try
											{
												var obj = jQuery.parseJSON(result);
												
												if (obj.result && obj.result == "1")
												{
													$("#inviteUsersWindow").dialog("close");
													TRACKER.showLongMessageDialog("'.Yii::t('site', 'Invitations sent successfully...').'<br/><br/>'.Yii::t('site', 'E-mails may sometimes not reach to inbox of the recipients but spam/junk box. Therefore we recommend you to contact with the invitees thereafter in order to assure that they have received your invitation.').'");
												}
												else if(obj.result && obj.result == "Duplicate Entry")
												{
													$("#inviteUsersWindow").html(result);
													$("#inviteUsersWindow").dialog("close");
											
													var msgString = "'.Yii::t('site', 'Since invitations already sent for the e-mails below, <br/> only the other invitations sent!').'<br/><br/>";
												
													for (var i=0;i<obj.emails.length;i++)
													{
														msgString = msgString + obj.emails[i] + "<br/>";
													}
												
													TRACKER.showMessageDialog(msgString);
												}
											}
											catch (error)
											{
												//$("#inviteUsersWindow").html(result);
							
												$("#hiddenAjaxResponseForInviteUsers").html(result);
												$("#ajaxInviteUsersResponse").html($("#hiddenAjaxResponseForInviteUsers #ajaxInviteUsersResponse").html());
												$("#hiddenAjaxResponseForInviteUsers").html("");							
											}
										}',
					),
					array('id'=>'inviteUsersAjaxButton-'.uniqid(), 'style'=>'padding-right:4px;'));			
			?>
												
			<?php 
// 			echo CHtml::htmlButton('Cancel',  
// 												array(
// 													'onclick'=> '$("#inviteUsersWindow").dialog("close"); return false;',
// 													 ),
// 												null); 
			?>
			
			<?php 
// 				$this->widget('zii.widgets.jui.CJuiButton', array(
// 						'name'=>'inviteUsersCancel',
// 						'caption'=>Yii::t('common', 'Cancel'),
// 						'id'=>'inviteUsersCancelButton',
// 						'onclick'=> 'js:function(){$("#inviteUsersWindow").dialog("close"); return false;}'
// 				));

				echo CHtml::ajaxLink('<button class="btn btn-sliding-red btn-sliding-red-a icon-close" style="'.(($app->language == 'en')?'padding-left:25px;padding-right:25px;':'padding-left:28px;padding-right:28px;').'">'.'<span style="font-family:Helvetica">'.Yii::t('common', 'Cancel').'</span>'.'</button>', '#',
						array(),
						array('id'=>'inviteUsersCancelButton', 'onclick'=>'$("#inviteUsersWindow").dialog("close"); return false;'));				
 			?>															
		</div>
	
	<?php $this->endWidget(); ?>
</div>
<?php 
	$this->endWidget('zii.widgets.jui.CJuiDialog');
?>

<!-- Diyaloglarda main layout'taki hiddenAjaxResponseToParse kullanilamadigindan (diyaloglar dinamik olarak sonradan eklendiginden yukarida -->
<!-- kaliyor) ve ayni isimle olunca da calismadigindan diyaloglarin view dosyalarinin sonuna gizli bir div tanimlaniyor -->
<div id="hiddenAjaxResponseForInviteUsers" style="display:none;"></div>