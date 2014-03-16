<?php 
$this->beginWidget('zii.widgets.jui.CJuiDialog', array(
	    'id'=>'databaseOperationsWindow',
	    // additional javascript options for the dialog plugin
	    'options'=>array(
	        'title'=>Yii::t('layout', 'Database Operations'),
	        'autoOpen'=>false,
	        'modal'=>true, 
			'resizable'=>false,
			'width'=> '800px'      
	    ),
	));
?>
	<div class="form">
	<?php $form=$this->beginWidget('CActiveForm', array(
		'id'=>'databaseOperations-form',
		'enableClientValidation'=>true,
		'clientOptions'=> array(
							'validateOnSubmit'=> true,
							'validateOnChange'=>false,
						 ),
	
	)); ?>
	
		<div id="ajaxDatabaseOpertionsResponse">
			<div class="row">
				<?php echo $form->labelEx($model,'selectSql'); ?>
				<?php echo $form->textField($model,'selectSql', array('size'=>'100%')); ?>
				<?php $errorMessage = $form->error($model,'selectSql'); 
					  if (strip_tags($errorMessage) == '') { echo '<div class="errorMessage">&nbsp;</div>'; }
					  else { echo $errorMessage; }
				?>
			</div>
			
			<div class="row">
				<?php echo $form->labelEx($model,'selectAllSql'); ?>
				<?php echo $form->textField($model,'selectAllSql', array('size'=>'100%')); ?>
				<?php $errorMessage = $form->error($model,'selectAllSql'); 
					  if (strip_tags($errorMessage) == '') { echo '<div class="errorMessage">&nbsp;</div>'; }
					  else { echo $errorMessage; }
				?>
			</div>			
	
			<div class="row">
				<?php echo $form->labelEx($model,'updateSql'); ?>
				<?php echo $form->textField($model,'updateSql', array('size'=>'100%')); ?>
				<?php $errorMessage = $form->error($model,'updateSql');  
					  if (strip_tags($errorMessage) == '') { echo '<div class="errorMessage">&nbsp;</div>'; }
					  else { echo $errorMessage; }
				?>	  			
			</div>
	
			<div class="row">
				<?php echo $form->labelEx($model,'deleteSql'); ?>
				<?php echo $form->textField($model,'deleteSql', array('size'=>'100%')); ?>
				<?php $errorMessage = $form->error($model,'deleteSql'); 
					  if (strip_tags($errorMessage) == '') { echo '<div class="errorMessage">&nbsp;</div>'; }
					  else { echo $errorMessage; }	
				?>	  		
			</div>
		</div>
		
		<div id="selectQueryResults"></div>	
	
		<div class="row buttons">
			<?php
			$app = Yii::app();
				
			echo CHtml::ajaxLink('<button class="btn btn-sliding-green btn-sliding-green-a icon-checkmark" style="'.(($app->language == 'en')?'padding-left:28px;padding-right:28px;':'padding-left:28px;padding-right:28px;').'">'.'<span style="font-family:Helvetica">'.Yii::t('site', 'Execute').'</span>'.'</button>', $this->createUrl('site/runDatabaseQueries'),
					array(
							'type'=>'POST',
							'success'=> 'function(result)
										 {
											try
											{
												var obj = jQuery.parseJSON(result);
											
												if (obj.result)
												{
													if(obj.result == "Select")
													{							
														if(obj.queryResult == false)
														{
															TRACKER.showMessageDialog("The record does not exist!");
														}
														else
														{
															for (key in obj.queryResult) {
																//alert(key + ":" + obj.queryResult[key]);
								
																console.log(key + ":" + obj.queryResult[key]);
															}
							
															TRACKER.showMessageDialog("The record found...");
														}
													}
													else if(obj.result == "Select All")
													{							
														if(obj.queryResult == false)
														{
															TRACKER.showMessageDialog("No record exists!");
														}
														else
														{
															for (key in obj.queryResult) {
																console.log(key + ":");
																
																for (key2 in obj.queryResult[key]) {
																	console.log(key2 + ":" + obj.queryResult[key][key2]);
																}	
															}
							
															TRACKER.showMessageDialog(obj.queryResult.length + " record(s) found...");
														}
													}
													else if(obj.result == "Update")
													{							
														if(obj.numberOfEffectedRows == 0)
														{
															TRACKER.showMessageDialog("No record updated!");
														}
														else
														{
															TRACKER.showMessageDialog(obj.numberOfEffectedRows + " record(s) updated...");
														}
													}
													else if(obj.result == "Delete")
													{							
														if(obj.numberOfEffectedRows == 0)
														{
															TRACKER.showMessageDialog("No record deleted!");
														}
														else
														{
															TRACKER.showMessageDialog(obj.numberOfEffectedRows + " record(s) deleted...");
														}
													}
													else if(obj.result == "Error")
													{							
														TRACKER.showLongMessageDialog(obj.errorMessage);
													}							
													else
													{
														TRACKER.showMessageDialog("Please enter a query!");
													}
												}
											}
											catch(error)
											{
												//$("#changePasswordWindow").html(result);
							
												$("#hiddenAjaxResponseForDatabaseOperations").html(result);
												$("#ajaxDatabaseOpertionsResponse").html($("#hiddenAjaxResponseForDatabaseOperations #ajaxDatabaseOpertionsResponse").html());
												$("#hiddenAjaxResponseForDatabaseOperations").html("");							
											}
										 }',
					),
					array('id'=>'databaseOperationsAjaxButton-'.uniqid(), 'style'=>'padding-right:4px;'));			
			?>
												
			<?php
				echo CHtml::ajaxLink('<button class="btn btn-sliding-red btn-sliding-red-a icon-close" style="'.(($app->language == 'en')?'padding-left:25px;padding-right:25px;':'padding-left:28px;padding-right:28px;').'">'.'<span style="font-family:Helvetica">'.Yii::t('common', 'Cancel').'</span>'.'</button>', '#',
						array(),
						array('id'=>'databaseOperationsCancelButton', 'onclick'=>'$("#databaseOperationsWindow").dialog("close"); return false;'));				
			?>												
		</div>
	
	<?php $this->endWidget(); ?>
</div>
<?php 
	$this->endWidget('zii.widgets.jui.CJuiDialog');
?>

<!-- Diyaloglarda main layout'taki hiddenAjaxResponseToParse kullanilamadigindan (diyaloglar dinamik olarak sonradan eklendiginden yukarida -->
<!-- kaliyor) ve ayni isimle olunca da calismadigindan diyaloglarin view dosyalarinin sonuna gizli bir div tanimlaniyor -->
<div id="hiddenAjaxResponseForDatabaseOperations" style="display:none;"></div>