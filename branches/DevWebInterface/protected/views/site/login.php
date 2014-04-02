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
	?>		
		<div id="ajaxLoginResponse">
			<!-- Ajax cevabinda tooltiplerin calismasi icin bu script'in Yii::app()->clientScript->registerScript() ie degil, -->
			<!-- direk <div id="ajaxLoginResponse"> tag'inib icinde gonderilmesi gerekiyor, buton ajaxla yeniden yuklenmeyince  -->
			<!-- multiple ajax problemi de olmadigindan uniqid() kullanmaya da gerek kalmiyor.  -->
			<script type="text/javascript">
			$("#LoginForm_email").tooltipster({
				theme: ".tooltipster-error",
				position: "bottom-right",
				offsetY: 10,
				trigger: "custom",
				maxWidth: 300,
				onlyOne: false,
				interactive: true
			});
					
			$("#LoginForm_password").tooltipster({
				theme: ".tooltipster-error",
				position: "bottom-right",
				offsetY: 10,
				trigger: "custom",
				maxWidth: 208,
				onlyOne: false,
				interactive: true
			});
			</script>				
		
			<div id="loginEmail" class="upperMenu">
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
						  					'complete'=> 'function() { $("#activationNotReceivedWindow").dialog("open"); return false;}',
						  					'update'=> '#activationNotReceivedWindow'
						  			),
						  			array(
						  					'id'=>'activationNotReceivedClickHereLink'.uniqid() //Unique ID oluşturmayınca her ajaxta bir önceki sorgular da tekrarlanıyor
						  					));
						  	?>
		  					<script type="text/javascript">
		  						bLoginFormEmailErrorExists = true;	
		  		            	
		  		            	$("#LoginForm_email").tooltipster('update', '<?php echo Yii::t('site', 'Your registration incomplete, please first complete it by clicking the link at the activation e-mail. If you have not received our activation e-mail, click {activationNotReceivedClickHere} to request a new one.', array('{activationNotReceivedClickHere}' => $link)); ?>');		  		            	
		  			            //$("#LoginForm_email").tooltipster('show');					
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
			  		            //$("#LoginForm_email").tooltipster('show');					
			  				</script>				
			  				<?php						  	
						  }
					?>					
				</div>

				<div id="rememberMeCheckbox" class="ac-custom ac-checkbox ac-checkmark" style="margin-top:0px;padding-top:6px;">
					<?php echo $form->checkBox($model,'rememberMe',array('size'=>5,'maxlength'=>128,'tabindex'=>4)); ?>
					<?php echo $form->label($model,'rememberMe',array('style'=>'font-weight:normal;')); ?>
				</div>
				
				<script type="text/javascript">		
				checkSVGElements("rememberMeCheckbox", false/*par_isForm*/);	
				</script>													
			</div>

			<div id="loginPassword" class="upperMenu" style="width:180px;">
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
  			  		            $("#LoginForm_password").tooltipster('update', "<?php echo strip_tags($errorMessage); ?>");
  			  			        //$("#LoginForm_password").tooltipster('show');					
  			  				</script>
  			  				<?php						  	 
						  }
					?>					
				</div>
					
 				<div style="width:120px;margin-top:0px;padding-top:6px;">
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
			
			<script type="text/javascript">
    		switch(windowWidthSize)
    		{
    		case 0: //En kucuk
		    	$("#loginEmail").css("width", "134px");		    	
		    	document.getElementById('LoginForm_email').size = "20";
		    	
		    	$("#loginPassword").css("width", "134px");		    	
		    	document.getElementById('LoginForm_password').size = "20";	
		    	
		    	$("#loginButton").css("padding-left", "134px");
		    	$("#loginOr").css("padding-left", "4px");
		    	
		    	$("#loginLongFacebookButton").hide(); 
		    	$("#loginShortFacebookButton").show();	    			
    			break;
    			
    		case 1: //Ortancı
		    	$("#loginEmail").css("width", "164px");		    	
		    	document.getElementById('LoginForm_email').size = "25";
		    	
		    	$("#loginPassword").css("width", "164px");		    	
		    	document.getElementById('LoginForm_password').size = "25";	
		    	
		    	$("#loginButton").css("padding-left", "134px");
		    	$("#loginOr").css("padding-left", "4px");
		    	
		    	$("#loginShortFacebookButton").hide(); 
		    	$("#loginLongFacebookButton").show();	    			
    			break;
    			
    		case 2: //En buyuk
		    	$("#loginEmail").css("width", "200px");		    	
		    	document.getElementById('LoginForm_email').size = "30";	

		    	$("#loginPassword").css("width", "200px");		    	
		    	document.getElementById('LoginForm_password').size = "30";	
		    	
		    	$("#loginButton").css("padding-left", "138px");
		    	$("#loginOr").css("padding-left", "10px");
		    	
		    	$("#loginShortFacebookButton").hide(); 
		    	$("#loginLongFacebookButton").show();	    			
    			break;
    			
    		default:
		    	$("#loginEmail").css("width", "200px");		    	
		    	document.getElementById('LoginForm_email').size = "30";	

		    	$("#loginPassword").css("width", "200px");		    	
		    	document.getElementById('LoginForm_password').size = "30";	
		    	
		    	$("#loginButton").css("padding-left", "138px");
		    	$("#loginOr").css("padding-left", "10px");
		    	
		    	$("#loginShortFacebookButton").hide(); 
		    	$("#loginLongFacebookButton").show();	    			
    			break;	    			
    		}
			</script>						
		</div>			
	<?php $this->endWidget(); ?>
</div>
