<?php		
if ($dataProvider != null) {
	
	if (isset($uploadList) && $uploadList == true) {
		echo "<div id='uploadId' style='display:none'></div>";
		$this->beginWidget('zii.widgets.jui.CJuiDialog', array(
		    'id'=>'uploadDeleteConfirmation',
			// additional javascript options for the dialog plugin
		    'options'=>array(
		        'title'=>Yii::t('upload', 'Delete file'),
		        'autoOpen'=>false,
		        'modal'=>true, 
				'resizable'=>false,
				'buttons' =>array (
					"OK"=>"js:function(){
									". CHtml::ajax(
											array(
													'url'=>Yii::app()->createUrl('upload/delete'),
													'data'=> array('id'=>"js:$('#uploadId').html()"),
													'success'=> 'function(result) { 	
																 	try {
																 		$("#uploadDeleteConfirmation").dialog("close");
																		var obj = jQuery.parseJSON(result);
																		if (obj.result && obj.result == "1") 
																		{
																			$.fn.yiiGridView.update("uploadListView");
																		}
																		else 
																		{
																			TRACKER.showMessageDialog("Sorry,an error occured in operation");
																		}
	
																	}
																	catch(ex) {
																		TRACKER.showMessageDialog("Sorry,an error occured in operation");
																	}
																}',
												)) .
								"}",
					"Cancel"=>"js:function() {
						$( this ).dialog( \"close\" );
					}" 
					)),
				));
		echo "Do you want to delete this file?";
		$this->endWidget('zii.widgets.jui.CJuiDialog');
	}
	$this->widget('zii.widgets.grid.CGridView', array(
		    'dataProvider'=>$dataProvider,
	 		'id'=>'uploadListView',
			'summaryText'=>'',
			'pager'=>array( 
				 'header'=>'',
		         'firstPageLabel'=>'',
		         'lastPageLabel'=>'',
			       ),
		    'columns'=>array(
				array(            // display 'create_time' using an expression
		          
					'type' => 'raw',
		            'value'=>'CHtml::link("<img src=\"".Yii::app()->createUrl("upload/get", array(
														 "id"=>$data["id"],
														 "fileType"=>$data["fileType"],
														 "thumb"=>"ok"
														)
										  			)."\"  />", "#",
										array("onclick"=>"TRACKER.showMediaWindow(".$data["id"].");")
					  				  )',
					'htmlOptions'=>array('style'=>'text-align:center'),	
				),
				array(            // display 'create_time' using an expression
		            'name'=>Yii::t('upload', 'Description'),
					'type' => 'raw',
		            'value'=>'CHtml::link($data["description"], "#", array(
    										"onclick"=>"TRACKER.showMediaWindow(".$data["id"].");",
										))',	
				),
				array(            // display 'create_time' using an expression
		            'name'=>Yii::t('upload', 'Sender'),
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
		            						"onclick"=>"$(\"#uploadId\").html(". $data["id"] .");
		            									$(\"#uploadDeleteConfirmation\").dialog(\"open\");", "class"=>"vtip", "title"=>'."Yii::t('upload', 'Delete file')".'
		            						))
		            			: ""; ',
					'htmlOptions'=>array('width'=>'16px'),
				),

			),
	));
	
	

}
?>