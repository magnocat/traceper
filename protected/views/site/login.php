	<div class="form">
	<?php $form=$this->beginWidget('CActiveForm', array(
		'id'=>'login-form',
		'enableAjaxValidation'=>false, //Yii 1.1.11'den itibaren "yii Session object destruction failed" hatasi aliniyor, forumda biri bu false olursa alinmadigini belirtmis
		'enableClientValidation'=>true,
		'clientOptions'=> array(
				'validateOnSubmit'=> true,
				'validateOnChange'=>false,
		),				
	));
	
	Yii::app()->clientScript->registerScript('loginTooltips',
			'$("#LoginForm_email").tooltipster({
			theme: ".tooltipster-error",
			position: "bottom-right",
			offsetY: 10,
			trigger: "custom",
			maxWidth: 300,
			onlyOne: false,
			interactive: true, 
			});

			$("#LoginForm_password").tooltipster({
			theme: ".tooltipster-error",
			position: "bottom-right",
			offsetY: 10,
			trigger: "custom",
			maxWidth: 540,
			onlyOne: false,
			interactive: true,
			});
			',				
			CClientScript::POS_HEAD);	
	?>
		
			<div class="upperMenu">
				<div style="height:3em;top:0%;padding:0px;">
					<?php echo $form->labelEx($model,'email'); ?>
					<?php echo $form->textField($model,'email', array('size'=>'30%','maxlength'=>'30%','tabindex'=>1)); ?>
					<?php $errorMessage = $form->error($model,'email'); 
						  if (strip_tags($errorMessage) == '') 
						  { 
						  	//echo '<div class="errorMessage">&nbsp;</div>';
						  	
						  	?>
		  					<script type="text/javascript">
		  						bLoginFormEmailErrorExists = false;
		  						$("#LoginForm_email").tooltipster('hide');
		  					</script>
		  					<?php						  	
						  }
						  else if(strip_tags($errorMessage) === Yii::t('site', 'Activate your account first'))
						  { 
						  	$link = CHtml::ajaxLink(Yii::t('site', 'here'), $this->createUrl('site/activationNotReceived'),
						  			array(
						  					'complete'=> 'function() { hideFormErrorsIfExist(); $("#activationNotReceivedWindow").dialog("open"); return false;}',
						  					'update'=> '#activationNotReceivedWindow'
						  			),
						  			array(
						  					'id'=>'activationNotReceivedClickHereLink'.uniqid() //Unique ID oluşturmayınca her ajaxta bir önceki sorgular da tekrarlanıyor
						  					));
						  	?>
		  					<script type="text/javascript">
		  						bLoginFormEmailErrorExists = true;	
		  		            	
		  		            	$("#LoginForm_email").tooltipster('update', '<?php echo Yii::t('site', 'Your registration incomplete, please first complete it by clicking the link at the activation e-mail. If you have not received our activation e-mail, click {activationNotReceivedClickHere} to request a new one.', array('{activationNotReceivedClickHere}' => $link)); ?>');		  		            	
		  			            $("#LoginForm_email").tooltipster('show');					
		  					</script>
		  					<?php						  	 
						  }
						  else
						  {						  	
						  	//echo $errorMessage;
						  	
						  	?>
			  				<script type="text/javascript">
			  				bLoginFormEmailErrorExists = true;
			  		            $("#LoginForm_email").tooltipster('update', "<?php echo strip_tags($errorMessage); ?>");
			  		            $("#LoginForm_email").tooltipster('show');					
			  				</script>				
			  				<?php						  	
						  }
					?>					
				</div>
				
				<div style="margin-top:0px;padding:0px;">
					<?php echo $form->checkBox($model,'rememberMe',array('size'=>5,'maxlength'=>128,'tabindex'=>4)); ?>
					<?php echo $form->label($model,'rememberMe',array('style'=>'font-weight:normal;')); ?>
				</div>									
			</div>

			<div class="upperMenu">
				<div style="height:3em;top:0%;padding:0px;">
					<?php echo $form->labelEx($model,'password'); ?>
					<?php echo $form->passwordField($model,'password', array('size'=>'30%','maxlength'=>'30%','tabindex'=>2)); ?>
					<?php $errorMessage = $form->error($model,'password'); 
						  if (strip_tags($errorMessage) == '') 
						  { 
						  	//echo '<div class="errorMessage">&nbsp;</div>';
						  	
						  	?>
  			  				<script type="text/javascript">
  			  					bLoginFormPasswordErrorExists = false;
  			  					$("#LoginForm_password").tooltipster('hide');
  			  				</script>
  			  				<?php						  	 
						  }
						  else 
						  { 
						  	//echo $errorMessage;
						  	?>
  			  				<script type="text/javascript">
  			  					bLoginFormPasswordErrorExists = true;	
  			  		            //$("#LoginForm_password").tooltipster('update', "<?php echo strip_tags($errorMessage); ?>");
  			  		        	$("#LoginForm_password").tooltipster('update', '<?php echo $errorMessage; ?>');
  			  			        $("#LoginForm_password").tooltipster('show');					
  			  				</script>
  			  				<?php						  	 
						  }
					?>					
				</div>
					
 				<div style="margin-top:2px;padding:0px;">
					<?php
					echo CHtml::ajaxLink('<div id="forgotPassword">'.Yii::t('site', 'Forgot Password?').
										'</div>', $this->createUrl('site/forgotPassword'),
							array(
// 									'complete'=> 'function() 
// 												  { 
// 													hideFormErrorsIfExist();																														
// 													$("#forgotPasswordWindow").dialog("open"); 
// 													return false;
// 												  }',
// 									'update'=> '#forgotPasswordWindow',
					
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
														hideFormErrorsIfExist();
														
														var opt = {
															autoOpen: false,
															modal: true,
															resizable: false,
															width: 600,
															title: "'.Yii::t('site', 'Forgot Password?').'"
														};
														
														$("#forgotPasswordWindow").dialog(opt).dialog("open");
														$("#forgotPasswordWindow").html(result);
													}
												}',									
							),
							array(
									'id'=>'forgotPasswordAjaxLink-'.uniqid(), //Unique ID oluşturmayınca her ajaxta bir önceki sorgular da tekrarlanıyor
									'tabindex'=>5));							
					?>	 					
 				</div>								
			</div>							
											
			<div class="upperMenu" style="margin-top:0.7em;width:50px;">
				<div style="height:3.3em;top:0%;padding:0px;">								
					<?php																											
// 					$this->widget('zii.widgets.jui.CJuiButton', array(
// 							'name'=>'ajaxLogin',
// 							'caption'=>Yii::t('site', 'Log in'),
// 							'id'=>'loginAjaxButton-'.uniqid(), //Unique ID oluşturmayınca her ajaxta bir önceki sorgular da tekrarlanıyor
// 							'htmlOptions'=>array('type'=>'submit','style'=>'width:8.4em;','tabindex'=>3,'ajax'=>array('type'=>'POST','url'=>array('site/login'),'update'=>'#forAjaxRefresh'))
// 					));

					$this->widget('zii.widgets.jui.CJuiButton', array(
							'name'=>'ajaxLogin',
							'caption'=>Yii::t('site', 'Log in'),
							'id'=>'loginAjaxButton-'.uniqid(),
							'htmlOptions'=>array('type'=>'submit','style'=>'width:8.4em;','tabindex'=>3,'ajax'=>array('type'=>'POST','url'=>array('site/login'),
									'success'=> 'function(msg){
													try
													{
														var obj = jQuery.parseJSON(msg);
														
														if (obj.result)
														{
															if (obj.result == "1")
															{
																$("#tabViewList").html(obj.renderedTabView);
																$("#forAjaxRefresh").html(obj.loginSuccessfulActions);
															}									
															else if (obj.result == "-3")
															{
																$("#forAjaxRefresh").html(obj.loginView);
									
																var opt = {
															        autoOpen: false,
															        modal: true,
																	resizable: false,
															        width: 600,
															        title: "'.Yii::t('site', 'Accept Terms to continue').'"
																};													
					
																$("#acceptTermsForLoginWindow").dialog(opt).dialog("open");
																$("#acceptTermsForLoginWindow").html(obj.renderedView);
															}
														}
													}
													catch (error)
													{
														$("#forAjaxRefresh").html(msg);
													}
												}',
							))
					));				
					?>
				</div>																					
			</div>				
	
	<?php $this->endWidget(); ?>
</div>
