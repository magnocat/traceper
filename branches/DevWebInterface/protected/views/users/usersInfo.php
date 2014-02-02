
<?php if (Yii::app()->user->isGuest == false) { ?>
	<div class="form">
	<?php $form=$this->beginWidget('CActiveForm', array(
		'id'=>'searchUser-form',
		'enableClientValidation'=>true,
		'clientOptions'=> array(
							'validateOnSubmit'=> true,
							'validateOnChange'=>false,
						 ),
	
		));
	
		Yii::app()->clientScript->registerScript('userSearchTooltip',
		'$("#userSearchField").tooltipster({
			theme: ".tooltipster-info",
			trigger: "custom",
			maxWidth: 450,
			onlyOne: false,
			position: "right",
			interactive: true,
		 	offsetX: 120,
		});

	 	$("#userSearchField").focus(function ()	{
	 		$("#userSearchField").tooltipster("update", TRACKER.langOperator.userSearchNotificationMessage);
	 		$("#userSearchField").tooltipster("show"); 		
		});

	 	$("#userSearchField").blur(function ()	{
			$("#userSearchField").tooltipster("hide"); 		
		});			
			
			', CClientScript::POS_HEAD);

		Yii::app()->clientScript->registerScript('searchUserPlaceholderForNonSupportingBrowsers',
				"$('input, textarea').placeholder(); //Placeholder desteklemeyen browser'lar icin",
				CClientScript::POS_READY);		
		 ?>
		<div class="row">
			<?php // echo $form->labelEx($model,'keyword'); ?>
			<?php echo $form->textField($model,'keyword', array('id'=>'userSearchField','class'=>'searchBox','placeholder'=>Yii::t('users', 'Type a friend\'s name'))); ?>
			<?php				
// 				echo CHtml::ajaxSubmitButton(Yii::t('common', 'Search'), $this->createUrl('users/search'),
// 												array(
// 													'complete'=> 'function() { $("#userSearchResults").dialog("open"); return false;}',
// 													'update'=> '#userSearchResults',
// 													 ),
// 												array(
// 													'id'=>'searchUserButton',
// 												));
				
// 				$this->widget('zii.widgets.jui.CJuiButton', array(
// 						'name'=>'ajaxUserSearch',
// 						'caption'=>Yii::t('common', 'Search'),
// 						'id'=>'userSearchAjaxButton-'.uniqid(), //Unique ID oluşturmayınca her ajaxta bir önceki sorgular da tekrarlanıyor
// 						'htmlOptions'=>array('type'=>'submit','style'=>'width:6em;margin-left:0.2em;','ajax'=>array('type'=>'POST','url'=>array('users/search'),
// 																			'complete'=> 'function() { $("#userSearchResults").dialog("open"); return false;}',
// 																			'update'=> '#userSearchResults',
// 						))
// 				));

				$app = Yii::app();
				
				//Yii butonları <buton> tag'i ile uretmedigi icin boyle yapildi, bu css'ler Yii'nin urettigi <input> ile calismiyor
				echo CHtml::ajaxLink('<button class="btn btn-3 btn-3a icon-search" style="'.(($app->language == 'en')?'padding-left:50px;':'padding-left:63px;padding-right:23px;').'">'.Yii::t('common', 'Search').'</button>', $this->createUrl('users/search'),
						array(
								'type'=>'POST',
								'complete'=> 'function() { $("#userSearchResults").dialog("open"); return false;}',
								'update'=> '#userSearchResults',
						),
						array('id'=>'userSearchAjaxButton-'.uniqid(),'type'=>'submit','style'=>'padding-left:2px;'));				
			?>
			<?php echo $form->error($model,'keyword'); 	?>
		</div>
	<?php $this->endWidget(); ?>
	</div>
<?php }?>
<?php
	if(in_array(UserType::RealUser, $userType) Or in_array(UserType::GPSDevice, $userType))
	{
		$viewId = 'userListView';
		$groupType = GroupType::FriendGroup;
	}	
	else if(in_array(UserType::RealStaff, $userType) Or in_array(UserType::GPSStaff, $userType))
	{
		$viewId = 'staffListView';
		$groupType = GroupType::StaffGroup;
	}

	if (isset($dataProvider)) {
		$this->renderPartial('userList', array('dataProvider'=>$dataProvider, 'friendList'=>true, 'groupType'=>$groupType, 'viewId'=>$viewId), false, true);
	}
	else
	{
		echo Yii::t('users', 'No users to show...').'<br/> <br/>';				
	}	
?>