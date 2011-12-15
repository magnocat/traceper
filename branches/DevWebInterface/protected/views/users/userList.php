<?php
if ($dataProvider != null) {
	$isFriendRequestList = isset($friendRequestList) ? true : false;
	$isSearchResult = isset($searchResult) ? true : false;
	$isFriendList = isset($friendList) ? true : false;
	
	$this->widget('zii.widgets.grid.CGridView', array(
		    'dataProvider'=>$dataProvider,
	 		'id'=>isset($viewId) ? $viewId : 'userListView',
			'summaryText'=>'',
		    'columns'=>array(

				array(            // display 'create_time' using an expression
		        //    'name'=>'realname',
					'type' => 'raw',
		            'value'=>'CHtml::link($data["realname"], "#", array(
    										"onclick"=>"TRACKER.trackUser(".$data["id"].");",
										))',	
				),
				array(            // display 'create_time' using an expression
		        //    'name'=>'realname',
					'type' => 'raw',
		            'value'=>'CHtml::link(\'<img src="images/delete.png"  />\', \'#\',
										array(\'onclick\'=>CHtml::ajax(
												array(
													\'url\'=>Yii::app()->createUrl(\'users/deleteFriendShip\', array(\'friendShipId\'=>$data[\'friendShipId\'])),
													\'success\'=> \'function(result) { alert(result); }\',
												)))
					  				  )',
					'htmlOptions'=>array('width'=>'16px'),
					'visible'=>$isFriendList || $isFriendRequestList,
				),
				array(            // display 'create_time' using an expression
		        //    'name'=>'realname',
					'type' => 'raw',
		            'value'=>'(isset($data[\'status\']) && $data[\'status\'] == 0 
								&& isset($data[\'requester\']) && $data[\'requester\'] == false) ?
									CHtml::link(\'<img src="images/approve.png"  />\', \'#\',
										array(\'onclick\'=>CHtml::ajax(
											array(
												\'url\'=>Yii::app()->createUrl(\'users/approveFriendShip\', array(\'friendShipId\'=>$data[\'friendShipId\'])),
												\'success\'=> \'function(result) { alert(result); }\',
											)))
					  				 )
					  			: ""',
					'htmlOptions'=>array('width'=>'16px'),
					'visible'=>$isFriendRequestList,
				),
				array(            // display 'create_time' using an expression
		        /*  This field can only be seen in search results 
		         * if status == -1 it means there is no relation between these users*/
					'type' => 'raw',
		            'value'=>' (isset($data[\'status\']) && $data[\'status\'] == -1) ?  
		            				 CHtml::link(\'<img src="images/user_add_friend.png"  />\', \'#\',
					  				array(\'onclick\'=>CHtml::ajax(
					  						array(\'url\'=>Yii::app()->createUrl(\'users/addAsFriend\', array(\'friendId\'=>$data[\'id\'])),
					  							  \'success\'=>\'function(result) { alert(result); }\',
												 )
					  						)
					  					)
					 				)
					 			: "";',
					'htmlOptions'=>array('width'=>'16px'),
					'visible'=>$isSearchResult,
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
	 'itemView'=>'_user',   // refers to the partial view named '_post'
	 'enablePagination'=>true,
	 'id'=>isset($viewId) ? $viewId : 'userListView',
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