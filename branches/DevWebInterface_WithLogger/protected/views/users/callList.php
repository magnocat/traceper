<?php 
$this->widget('zii.widgets.grid.CGridView', array(
		    'dataProvider'=>$dataProvider,
	 		'id'=>'callLogview',
			'summaryText'=>'',
		    'columns'=>array(

				array(            // display 'create_time' using an expression
		        //    'name'=>'realname',
					'type' => 'raw',
		            'value'=>'$data["number"]',	
				),
				array(            // display 'create_time' using an expression
		        //    'name'=>'realname',
					'type' => 'raw',
		            'value'=>'$data["begin"]',
					
				),
				array(            // display 'create_time' using an expression
		        //    'name'=>'realname',
					'type' => 'raw',
		            'value'=>'$data["end"]',
					
				),
				array(            // display 'create_time' using an expression
		        //    'name'=>'realname',
					'type' => 'raw',
		            'value'=>'$data["type"]',
					
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



?>