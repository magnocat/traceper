<?php 		
		$this->widget('zii.widgets.CListView', array(
								    'dataProvider'=>$dataProvider,
								    'itemView'=>'_user',   // refers to the partial view named '_post'
									'enablePagination'=>true,
									'id'=>'userListView',
									'pager'=>array('class'=>'AjaxLinkPager'),
		));
?>