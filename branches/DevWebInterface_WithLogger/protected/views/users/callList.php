<?php 
	$this->widget('zii.widgets.CListView', array(
									    'dataProvider'=>$dataProvider,
									    'itemView'=>'_call',   // refers to the partial view named '_post'
										'enablePagination'=>true,
										'id'=>'callListView',
										'ajaxUpdate'=>null,
										'summaryText'=>'',
										'emptyText'=>'No match found...',
										'pager'=>array( 'header'=>'',
														'firstPageLabel'=>'',
														'lastPageLabel'=>'',	
														
												 ),
	));
?>