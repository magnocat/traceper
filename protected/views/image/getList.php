<?php		
if ($dataProvider != null) {
	
	if (isset($imageList) && $imageList == true) {
		echo "<div id='imageId' style='display:none'></div>";
		$this->beginWidget('zii.widgets.jui.CJuiDialog', array(
		    'id'=>'imageDeleteConfirmation',
			// additional javascript options for the dialog plugin
		    'options'=>array(
		        'title'=>Yii::t('general', 'Delete Image'),
		        'autoOpen'=>false,
		        'modal'=>true, 
				'resizable'=>false,
				'buttons' =>array (
					"OK"=>"js:function(){
									". CHtml::ajax(
											array(
													'url'=>Yii::app()->createUrl('image/delete'),
													'data'=> array('id'=>"js:$('#imageId').html()"),
													'success'=> 'function(result) { 	
																 	try {
																 		$("#imageDeleteConfirmation").dialog("close");
																		var obj = jQuery.parseJSON(result);
																		if (obj.result && obj.result == "1") 
																		{
																			$.fn.yiiGridView.update("imageListView");
																		}
																		else 
																		{
																			$("#messageDialogText").html("Sorry,an error occured in operation");
																			$("#messageDialog").dialog("open");
																		}
	
																	}
																	catch(ex) {
																		$("#messageDialogText").html("Sorry,an error occured in operation");
																		$("#messageDialog").dialog("open");
																	}
																}',
												)) .
								"}",
					"Cancel"=>"js:function() {
						$( this ).dialog( \"close\" );
					}" 
					)),
				));
		echo "Do you want to delete this image?";
		$this->endWidget('zii.widgets.jui.CJuiDialog');
	}
	$this->widget('zii.widgets.grid.CGridView', array(
		    'dataProvider'=>$dataProvider,
	 		'id'=>'imageListView',
			'summaryText'=>'',
			'pager'=>array( 
				 'header'=>'',
		         'firstPageLabel'=>'',
		         'lastPageLabel'=>'',
			       ),
		    'columns'=>array(
				array(            // display 'create_time' using an expression
		          
					'type' => 'raw',
		            'value'=>'CHtml::link("<img src=\"".Yii::app()->createUrl("image/get", array(
														 "id"=>$data["id"],
														 "thumb"=>"ok"
														)
										  			)."\"  />", "#",
										array("onclick"=>"TRACKER.showImageWindow(".$data["id"].");")
					  				  )',
				),
				array(            // display 'create_time' using an expression
		            'name'=>'Description',
					'type' => 'raw',
		            'value'=>'CHtml::link($data["description"], "#", array(
    										"onclick"=>"TRACKER.showImageWindow(".$data["id"].");",
										))',	
				),
				array(            // display 'create_time' using an expression
		            'name'=>'Sender',
					'type' => 'raw',
		            'value'=>'CHtml::link($data["realname"], "#", array(
    										"onclick"=>"TRACKER.trackUser(".$data["userId"].");",
										))',
					//'htmlOptions'=>array('style'=>'width:160px;'),	
				),
				array(           
		        //    'name'=>'realname',
					'type' =>'raw',
		            'value'=>' ($data["userId"] == Yii::app()->user->id) 
		            			?
		            					CHtml::link("<img src=\"images/delete.png\"  />", "#", array(
		            						"onclick"=>"$(\"#imageId\").html(". $data["id"] .");
		            									$(\"#imageDeleteConfirmation\").dialog(\"open\");",
		            						))
		            			: ""; ',
					'htmlOptions'=>array('width'=>'16px'),
				),

			),
	));
	
	

}
?>