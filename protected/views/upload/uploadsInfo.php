<?php if (Yii::app()->user->isGuest == false) { 

// 	Yii::app()->clientScript->registerCoreScript('jquery');
// 	Yii::app()->clientScript->registerCoreScript('tooltipster');
// 	Yii::app()->clientScript->registerCoreScript('DataOperations');
// 	Yii::app()->clientScript->registerCoreScript('TrackerOperator');	
?>

	<div class="form">
	<?php $form=$this->beginWidget('CActiveForm', array(
		'id'=>'searchUpload-form',	
		'enableClientValidation'=>true,
		'clientOptions'=> array(
							'validateOnSubmit'=> true,
							'validateOnChange'=>false,
						 ),	
		));
	
		Yii::app()->clientScript->registerScript('uploadSearchTooltip',
			'$("#uploadSearchField").tooltipster({
				theme: ".tooltipster-infoWithIcon",
				trigger: "custom",
				maxWidth: 450,
				onlyOne: false,
				position: "right",
				interactive: true,
				offsetX: 120,
			});
	
			$("#uploadSearchField").focus(function (){
				$("#uploadSearchField").tooltipster("update", TRACKER.langOperator.uploadSearchNotificationMessage);
				$("#uploadSearchField").tooltipster("show");
			});
	
			$("#uploadSearchField").blur(function (){
				$("#uploadSearchField").tooltipster("hide");
			});				
			', CClientScript::POS_HEAD);	
		 ?>
		 
		<div class="row">
			<?php	echo $form->textField($model,'keyword', array('id'=>'uploadSearchField','class'=>'searchBox','placeholder'=>Yii::t('upload', 'Type a keyword'))); ?>
	
			<?php   
// 					echo CHtml::ajaxSubmitButton(Yii::t('common', 'Search'), $this->createUrl('upload/search', array('fileType'=>$fileType)), 
// 												array(
// 													'complete'=> 'function() { $("#uploadSearchResults").dialog("open"); return false;}',
// 													'update'=> '#uploadSearchResults',
// 													 ),
// 												array(
// 													'id'=>'searchUploadButton'
// 							 					));
					
// 					$this->widget('zii.widgets.jui.CJuiButton', array(
// 							'name'=>'ajaxUploadSearch',
// 							'caption'=>Yii::t('common', 'Search'),
// 							'id'=>'uploadSearchAjaxButton-'.uniqid(), //Unique ID oluşturmayınca her ajaxta bir önceki sorgular da tekrarlanıyor
// 							'htmlOptions'=>array('type'=>'submit','style'=>'width:6em;margin-left:0.2em;','ajax'=>array('type'=>'POST','url'=>$this->createUrl('upload/search', array('fileType'=>$fileType)),
// 									'complete'=> 'function() { $("#uploadSearchResults").dialog("open"); return false;}',
// 									'update'=> '#uploadSearchResults',
// 							))
// 					));

					$app = Yii::app();

					//Yii butonları <buton> tag'i ile uretmedigi icin boyle yapildi, bu css'ler Yii'nin urettigi <input> ile calismiyor
					echo CHtml::ajaxLink('<button class="btn btn-3 btn-3a icon-search" style="'.(($app->language == 'en')?'padding-left:50px;':'padding-left:63px;padding-right:23px;').'">'.Yii::t('common', 'Search').'</button>', $this->createUrl('upload/search', array('fileType'=>$fileType)),
							array(
									'type'=>'POST',
									'complete'=> 'function() { $("#uploadSearchResults").dialog("open"); return false;}',
									'update'=> '#uploadSearchResults',
							),
							array('id'=>'uploadSearchAjaxButton-'.uniqid(),'type'=>'submit','style'=>'padding-left:2px;'));					
				?>
			<?php echo $form->error($model,'keyword'); 	?>
		</div>
	<?php $this->endWidget(); ?>
	</div>
<?php } 

	if (isset($dataProvider)) {
		$params = array('dataProvider'=>$dataProvider);
		
		if (isset($uploadList) && $uploadList == true) {
			$params = array_merge($params, array('uploadList'=>true));
		}
		
		if (isset($isPublicList) && $isPublicList == true) {
			$params = array_merge($params, array('isPublicList'=>true, 'viewId'=>'publicUploadListView'));
		}
		else
		{
			$params = array_merge($params, array('isPublicList'=>false, 'viewId'=>'uploadListView'));
		}
		
		$this->renderPartial('getList', $params, false, true);
	}
	else
	{
		echo 'No files to show... <br/> <br/>';			
	}
?>