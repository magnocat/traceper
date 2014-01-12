
<div class="form" style='height:100%;'>
	<?php $form=$this->beginWidget('CActiveForm', array(
			'id'=>'register-form',
			'enableClientValidation'=>true,
			'clientOptions'=> array(
					'validateOnSubmit'=> true,
					'validateOnChange'=>false,
			),			
			'htmlOptions'=>array('style'=>'height:100%;'),
	));	
	?>

	<div id="ajaxRegisterResponse">
		<!-- Ajax cevabinda tooltiplerin calismasi icin bu script'in Yii::app()->clientScript->registerScript() ie degil, -->
		<!-- direk <div id="ajaxLoginResponse"> tag'inib icinde gonderilmesi gerekiyor, buton ajaxla yeniden yuklenmeyince  -->
		<!-- multiple ajax problemi de olmadigindan uniqid() kullanmaya da gerek kalmiyor.  -->
		<script type="text/javascript">
			$("#RegisterForm_name").tooltipster({
	         	 theme: ".tooltipster-error",
	         	 trigger: "custom",
	         	 maxWidth: 540,
	         	 onlyOne: false,
				 position: "top-left",
				 interactive: true,
				 offsetX: 130,
	         });
			
			$("#RegisterForm_lastName").tooltipster({
	         	 theme: ".tooltipster-error",
	         	 position: "right",
	         	 trigger: "custom",
	         	 maxWidth: 540,
	         	 onlyOne: false,
				 interactive: true,
         	 });
			
			$("#RegisterForm_email").tooltipster({
	         	 theme: ".tooltipster-error",
	         	 position: "right",
	         	 trigger: "custom",
	         	 maxWidth: 400,
	         	 onlyOne: false,
				 interactive: true,
         	 });	

			$("#RegisterForm_emailAgain").tooltipster({
	         	 theme: ".tooltipster-error",
	         	 position: "right",
	         	 trigger: "custom",
	         	 maxWidth: 540,
	         	 onlyOne: false,
				 interactive: true,
         	 });

			$("#RegisterForm_password").tooltipster({
	         	 theme: ".tooltipster-error",
	         	 position: "bottom-left",
	         	 trigger: "custom",
	         	 maxWidth: 540,
	         	 onlyOne: false,
				 interactive: true,
				 offsetX: 130,
         	 });	

			$("#RegisterForm_passwordAgain").tooltipster({
	         	 theme: ".tooltipster-error",
	         	 position: "right",
	         	 trigger: "custom",
	         	 maxWidth: 540,
	         	 onlyOne: false,
				 interactive: true,
         	 });
		</script>	
	
		<div class="sideMenu">
			<div style="position:absolute;display:inline-block;vertical-align:top;width:49%;">
			<?php //echo $form->labelEx($model,'name'); ?>
			<?php echo $form->textField($model,'name', array('size'=>'22%','maxlength'=>128,'tabindex'=>7,'placeholder'=>Yii::t('site', 'First Name'),'class'=>'registerFormField','style'=>'width:145px;')); ?>																				 
			<?php $errorMessage = $form->error($model,'name');  
				if (strip_tags($errorMessage) == '') {
					//echo '<div class="errorMessage">&nbsp;</div>';
									
					?>
					<script type="text/javascript">
						bRegisterFormNameErrorExists = false;
						$("#RegisterForm_name").tooltipster('hide');
					</script>
					<?php				
				}
				else { //echo '<div class="errorMessage" style="font-size: 1.1em;">'.$errorMessage.'</div>';
				
					?>
					<script type="text/javascript">
						bRegisterFormNameErrorExists = true;	
		            	$("#RegisterForm_name").tooltipster('update', "<?php echo strip_tags($errorMessage); ?>");
			            $("#RegisterForm_name").tooltipster('show');					
					</script>
					<?php				
				}
			?>
			</div>
			
			<div style="position:absolute;left:13.6em;display:inline-block;vertical-align:top;width:49%;">
			<?php //echo $form->labelEx($model,'lastName'); ?>
			<?php echo $form->textField($model,'lastName', array('size'=>'22%','maxlength'=>128,'tabindex'=>8,'placeholder'=>Yii::t('site', 'Last Name'),'class'=>'registerFormField','style'=>'width:145px;')); ?>
			<?php $errorMessage = $form->error($model,'lastName');  
				if (strip_tags($errorMessage) == '') {
					//echo '<div class="errorMessage">&nbsp;</div>';
					
					?>
					<script type="text/javascript">
						bRegisterFormLastNameErrorExists = false;
						$("#RegisterForm_lastName").tooltipster('hide');
					</script>
					<?php				
				}
				else { //echo '<div class="errorMessage" style="font-size: 1.1em;">'.$errorMessage.'</div>';
					?>
					<script type="text/javascript">
						bRegisterFormLastNameErrorExists = true;
		            	$("#RegisterForm_lastName").tooltipster('update', "<?php echo strip_tags($errorMessage); ?>");
			            $("#RegisterForm_lastName").tooltipster('show');					
					</script>
					<?php				
				}
			?>
			</div>																
		</div>							
	
		<div class="sideMenu">
			<?php //echo $form->labelEx($model,'email'); ?>
			<?php echo $form->textField($model,'email', array('size'=>'50%','maxlength'=>128,'tabindex'=>9,'placeholder'=>Yii::t('site', 'Your E-mail Address'),'class'=>'registerFormField','style'=>'width:324px;')); ?>		
			<?php $errorMessage = $form->error($model,'email'); 
				if (strip_tags($errorMessage) == '') {
					//echo '<div class="errorMessage">&nbsp;</div>';
					
					?>
					<script type="text/javascript">
						bRegisterFormEmailErrorExists = false;
						$("#RegisterForm_email").tooltipster('hide');
					</script>
					<?php				
				}
				else if(strip_tags($errorMessage) === Yii::t('site', 'Registration incomplete, please request activation e-mail below'))
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
	  					bRegisterFormEmailErrorExists = true;	
	  		            	
	  		            $("#RegisterForm_email").tooltipster('update', '<?php echo Yii::t('site', 'Your registration incomplete, please first complete it by clicking the link at the activation e-mail. If you have not received our activation e-mail, click {activationNotReceivedClickHere} to request a new one.', array('{activationNotReceivedClickHere}' => $link)); ?>');		  		            	
	  			        $("#RegisterForm_email").tooltipster('show');					
	  				</script>
	  				<?php						  	 
			  	}			
				else 
				{ 
					//echo '<div class="errorMessage" style="font-size: 1.1em;">'.$errorMessage.'</div>';
	
					?>
					<script type="text/javascript">
						bRegisterFormEmailErrorExists = true;				
			            $("#RegisterForm_email").tooltipster('update', "<?php echo strip_tags($errorMessage); ?>");
			            $("#RegisterForm_email").tooltipster('show');					
					</script>				
					<?php				
				}
			?>								
		</div>

		<div class="sideMenu">
			<?php //echo $form->labelEx($model,'emailAgain'); ?>
			<?php echo $form->textField($model,'emailAgain', array('size'=>'50%','maxlength'=>128,'tabindex'=>10,'placeholder'=>Yii::t('site', 'Your E-mail Address (Again)'),'class'=>'registerFormField','style'=>'width:324px;')); ?>
			<?php $errorMessage = $form->error($model,'emailAgain'); 
				if (strip_tags($errorMessage) == '') {
					//echo '<div class="errorMessage">&nbsp;</div>';
					
					?>
					<script type="text/javascript">
						bRegisterFormEmailAgainErrorExists = false;
						$("#RegisterForm_emailAgain").tooltipster('hide');
					</script>
					<?php				
				}
				else { //echo '<div class="errorMessage" style="font-size: 1.1em;">'.$errorMessage.'</div>';
				
					?>
					<script type="text/javascript">
						bRegisterFormEmailAgainErrorExists = true;
		                $("#RegisterForm_emailAgain").tooltipster('update', "<?php echo strip_tags($errorMessage); ?>");
			            $("#RegisterForm_emailAgain").tooltipster('show');					
					</script>				
					<?php			
				}
			?>								
		</div>							

		<div class="sideMenu">
			<div style="position:absolute;display:inline-block;vertical-align:top;width:49%;">
			<?php //echo $form->labelEx($model,'password'); ?>
			<?php echo $form->passwordField($model,'password', array('size'=>'22%','maxlength'=>128,'tabindex'=>11,'placeholder'=>Yii::t('site', 'Password'),'class'=>'registerFormField','style'=>'width:145px;')); ?>
			<?php $errorMessage = $form->error($model,'password');
				if (strip_tags($errorMessage) == '') {
					//echo '<div class="errorMessage">&nbsp;</div>';
									
					?>
					<script type="text/javascript">
						bRegisterFormPasswordErrorExists = false;
						$("#RegisterForm_password").tooltipster('hide');
					</script>
					<?php				
				}
				else { //echo '<div class="errorMessage" style="font-size: 1.1em;">'.$errorMessage.'</div>';
				
				?>
				<script type="text/javascript">
					bRegisterFormPasswordErrorExists = true;
		            $("#RegisterForm_password").tooltipster('update', "<?php echo strip_tags($errorMessage); ?>");
		            $("#RegisterForm_password").tooltipster('show');					
				</script>				
				<?php			
				}
			?>
			</div>
			
			<div style="position:absolute;left:13.6em;display:inline-block;vertical-align:top;width:49%;">
			<?php //echo $form->labelEx($model,'passwordAgain'); ?>
			<?php echo $form->passwordField($model,'passwordAgain', array('size'=>'22%','maxlength'=>128,'tabindex'=>12,'placeholder'=>Yii::t('site', 'Password (Again)'),'class'=>'registerFormField','style'=>'width:145px;')); ?>
			<?php $errorMessage = $form->error($model,'passwordAgain'); 
				if (strip_tags($errorMessage) == '') {
					//echo '<div class="errorMessage">&nbsp;</div>';
									
					?>
					<script type="text/javascript">
						bRegisterFormPasswordAgainErrorExists = false;
						$("#RegisterForm_passwordAgain").tooltipster('hide');
					</script>
					<?php				
				}
				else { //echo '<div class="errorMessage" style="font-size: 1.1em;">'.$errorMessage.'</div>';
				
				?>
				<script type="text/javascript">
					bRegisterFormPasswordAgainErrorExists = true;
		            $("#RegisterForm_passwordAgain").tooltipster('update', "<?php echo strip_tags($errorMessage); ?>");
		            $("#RegisterForm_passwordAgain").tooltipster('show');					
				</script>				
				<?php			
				}
			?>
			</div>																
		</div>
	</div>	

	<?php $this->endWidget(); ?>
</div>
