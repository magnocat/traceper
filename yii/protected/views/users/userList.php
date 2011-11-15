<?php 		
if ($dataProvider != null) {
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
}
?>