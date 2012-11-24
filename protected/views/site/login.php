	<div class="form">
	<?php $form=$this->beginWidget('CActiveForm', array(
		'id'=>'login-form',
		'enableClientValidation'=>true,
	
	)); ?>
		
		<div class="upperMenu">
			<?php 
				//echo CHtml::submitButton('Login');
				echo CHtml::ajaxSubmitButton(Yii::t('site','Login'), Yii::app()->createUrl('site/login'), 
												array(
													'success'=> 'function(result){ 
																	try {
																		var obj = jQuery.parseJSON(result);
																		if (obj.result && obj.result == "1") 
																		{
																			$("#username").html(obj.realname);
																			$("#userId").html(obj.id);
																			//TRACKER.getFriendList(1);
																			$("#lists").show();
																			$("#tab_view").tabs("select",0);
																		'.
																		CHtml::ajax(
																			array(
																			'url'=>Yii::app()->createUrl('users/getFriendList', array('userType'=>array(UserType::RealUser, UserType::GPSDevice))),
																			'update'=>'#users_tab',
																			)
																		)
																		. '  '.
																		CHtml::ajax(
																			array(
																			'url'=>Yii::app()->createUrl('users/getFriendList', array('userType'=>array(UserType::RealStaff, UserType::GPSStaff))),
																			'update'=>'#staff_tab',
																			)
																		)
																		. '  '.														
																		CHtml::ajax(
																			array(
																			'url'=> Yii::app()->createUrl('upload/getList', array('fileType'=>0)),
																			'update'=>'#photos_tab',
																			)
																		)
																		. '  '.
																		CHtml::ajax(
																			array(
																			'url'=> Yii::app()->createUrl('groups/getGroupList', array('groupType'=>GroupType::FriendGroup)),
																			'update'=>'#groups_tab',
																			)
																		)
																		. '  '.														
																		CHtml::ajax(
																			array(
																			'url'=> Yii::app()->createUrl('groups/getGroupList', array('groupType'=>GroupType::StaffGroup)),
																			'update'=>'#staff_groups_tab',
																			)
																		)																	
																		 .'	
																			$("#loginBlock").hide();
																			$("#userBlock").show();
																			$("#userLoginWindow").dialog("close");
																			TRACKER.getFriendList(1);	
		  																	TRACKER.getImageList();
																		}														
																	}
																	catch (error){
																		$("#userLoginWindow").html(result);
																	}
																 }',
													 ),
												array('size'=>20,'maxlength'=>128,'tabindex'=>4)); 
				?>
		</div>
		
		<div class="upperMenu">
			<?php echo $form->checkBox($model,'rememberMe',array('size'=>20,'maxlength'=>128,'tabindex'=>3)); ?>
			<?php echo $form->label($model,'rememberMe'); ?>
			<?php echo $form->error($model,'rememberMe'); ?>
		</div>	
		
		<div class="upperMenu">
			<?php echo $form->labelEx($model,'password'); ?>
			<?php echo $form->passwordField($model,'password', array('size'=>20,'maxlength'=>128,'tabindex'=>2)); ?>
			<?php $errorMessage = $form->error($model,'password'); 
				  if (strip_tags($errorMessage) == '') { echo '<div class="errorMessage">&nbsp;</div>'; }
				  else { echo $errorMessage; }
			?>
		</div>	
		
		<div class="upperMenu">
			<?php echo $form->labelEx($model,'email'); ?>
			<?php echo $form->textField($model,'email', array('size'=>20,'maxlength'=>128,'tabindex'=>1)); ?>
			<?php $errorMessage = $form->error($model,'email'); 
				  if (strip_tags($errorMessage) == '') { echo '<div class="errorMessage">&nbsp;</div>'; }
				  else { echo $errorMessage; }
			?>			
		</div>				
	
	<?php $this->endWidget(); ?>
</div>
