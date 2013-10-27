	<div class="form">
	<?php $form=$this->beginWidget('CActiveForm', array(
		'id'=>'login-form',
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
				<div style="height:3.3em;top:0%;padding:0px;">
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
						  					'id'=>'showActivationNotReceivedWindowClickHere'));
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
				<div style="height:3.3em;top:0%;padding:0px;">
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
									'complete'=> 'function() 
												  { 
													hideFormErrorsIfExist();																														
													$("#forgotPasswordWindow").dialog("open"); 
													return false;
												  }',
									'update'=> '#forgotPasswordWindow',
							),
							array(
									'id'=>'showForgotPasswordWindow','tabindex'=>5));							
					?>	 					
 				</div>								
			</div>							
											
			<div class="upperMenu" style="margin-top:0.7em;width:50px;">
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
