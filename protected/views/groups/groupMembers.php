<?php

if ($dataProvider != null) 
{
	//TODO: Refactor make common confirmation dialog 	
	/** This is the group member id holder, when user clicks delete, its content is filled***/
	echo "<div id='groupMemberId' style='display:none'></div>";
	echo "<div id='groupId' style='display:none'></div>";
	$this->beginWidget('zii.widgets.jui.CJuiDialog', array(
	    'id'=>'groupMemberDeleteConfirmation',
		// additional javascript options for the dialog plugin
	    'options'=>array(
	        'title'=>Yii::t('groups', 'Remove Group Member'),
	        'autoOpen'=>false,
	        'modal'=>true, 
			'resizable'=>false,
	    	'width'=>'400px',
			'buttons' =>array (
			Yii::t('common', 'OK')=>"js:function(){
							". CHtml::ajax(
									array(
											'url'=>Yii::app()->createUrl('groups/deleteGroupMember'),
											'data'=> array('userId'=>"js:$('#groupMemberId').html()", 'groupId'=>"js:$('#groupId').html()"),
											'success'=> 'function(result) { 	
														 	try {
														 		$("#groupMemberDeleteConfirmation").dialog("close");
																var obj = jQuery.parseJSON(result);
																if (obj.result && obj.result == "1") 
																{
																	$.fn.yiiGridView.update("groupMembersListView");
																}
																else 
																{
																	TRACKER.showMessageDialog("'.Yii::t('groups', 'Sorry,an error occured in operation - 1').'");
																}

															}
															catch(ex) {
																TRACKER.showMessageDialog("'.Yii::t('groups', 'Sorry,an error occured in operation - 2').'");
															}
														}',
										)) .
						"}",
			Yii::t('common', 'Cancel')=>"js:function() {
				$( this ).dialog( \"close\" );
			}" 
			)),
		));	
	echo Yii::t('groups', 'Do you really want to remove this group member?').'<br/> <br/>';
	$this->endWidget('zii.widgets.jui.CJuiDialog');	
	

	$this->widget('zii.widgets.grid.CGridView', array(
		    'dataProvider'=>$dataProvider,
	 		'id'=>'groupMembersListView',
			'summaryText'=>'',
			'pager'=>array( 
				 'header'=>'',
		         'firstPageLabel'=>'',
		         'lastPageLabel'=>'',
			       ),
		    'columns'=>array(
		array(            // display 'create_time' using an expression
				    'name'=>Yii::t('common', 'Name'),
					'type' => 'raw',		
					'sortable'=>true,	
					'value'=>'$data["Name"]',	
		),
		array(            // display 'create_time' using an expression
	//    'name'=>'realname',
					'type' => 'raw',
            'value'=>'CHtml::link("<img src=\"images/delete.png\"  />", "#",
								array("onclick"=>"$(\"#groupMemberId\").text(".$data[\'userId\'].");
												  $(\"#groupId\").text(".$data[\'groupId\'].");
												  $(\"#groupMemberDeleteConfirmation\").dialog(\"open\");", 
										"class"=>"vtip", 
										"title"=>'.("Yii::t('groups', 'Remove Group Member')").'))',

					'htmlOptions'=>array('width'=>'16px')
		),
	),
	));
						
}
/*
 */
?>