<?php		
if ($dataProvider != null) {
	
	$this->widget('zii.widgets.grid.CGridView', array(
		    'dataProvider'=>$dataProvider,
	 		'id'=>'imageListView',
			'summaryText'=>'',
		    'columns'=>array(
				array(            // display 'create_time' using an expression
		        //    'name'=>'realname',
					'type' => 'html',
		            'value'=>'CHtml::link("<img src=\"".Yii::app()->createUrl("image/get", array(
														 \'id\'=>$data["id"],
														 \'thumb\'=>\'ok\'
														)
										  			)."  />", "#",
										array("onclick"=>"TRACKER.showImageWindow(".$data["id"].");")
					  				  )',
					'htmlOptions'=>array('width'=>'16px'),
				),
				array(            // display 'create_time' using an expression
		        //    'name'=>'realname',
					'type' => 'raw',
		            'value'=>'CHtml::link($data["realname"], "#", array(
    										"onclick"=>"TRACKER.showImageWindow(".$data["id"].");",
										))',	
				),
				array(           
		        //    'name'=>'realname',
					'type' => 'html',
		            'value'=>'($data[\'userId\'] == Yii::app()->user->id) ? 
		            			CHtml::ajaxLink("<img src=\"images/delete.png\"  />",
		            				    Yii::app()->createUrl("image/delete", array("id"=>$data["id"])), 
 										array(
				 							"success"=>\'function(result){ 
				 														  try {
				 																var obj = jQuery.parseJSON(result);
																				if (obj.result && obj.result == "1") {
																					alert("operation successfull")
																				}
																				else {
																					alert(obj.result);
																				}
																		  }
																		  catch(error) {
																		  	alert(error);
																		  }
														}\',
											), 
										array()
									) 
		            			: "" 
		            				',
					'htmlOptions'=>array('width'=>'16px'),
				),
				
				
/*			
			array(            // display 'author.username' using an expression
		            'name'=>'authorName',
		            'value'=>'$data->author->username',
			),
			array(            // display a column with "view", "update" and "delete" buttons
		            'class'=>'CButtonColumn',
			),
*/
			),
	));
	
	
	
/*	
	$this->widget('zii.widgets.CListView', array(
									    'dataProvider'=>$dataProvider,
									    'itemView'=>'_image',   // refers to the partial view named '_post'
										'enablePagination'=>true,
										'id'=>'imageListView',
										'ajaxUpdate'=>null,
										'summaryText'=>'',
										'emptyText'=>'No match found...',
										'pager'=>array( 'header'=>'',
														'firstPageLabel'=>'',
														'lastPageLabel'=>'',	
														
												 ),
	));
*/
}
?>