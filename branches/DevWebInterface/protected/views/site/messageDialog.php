<?php 
$this->beginWidget('zii.widgets.jui.CJuiDialog', array(
	    'id'=>'messageDialogWindow',
	    'options'=>array(
	        'title'=>$title,
	        'autoOpen'=>true,
	        'modal'=>true, 
			'resizable'=>false,
	    	'width'=>'600px',
	    	'height'=>'auto',
	    	'position'=>array('my'=>'top-100%'),	    		 
	    	'open' => 'js:function(){ $("#messageDialogContent").show(); }',    		
	    ),
	));
?>

	<!-- Sayfa açılırken mesaj sayfanın tepesinde görülmesin diye önce "display:none" yapiyorsun, sonra dialog açılınca show() cagiriyorsun -->
	<div id="messageDialogContent" style="display:none;padding-top:25px;">
		<div style="font-family:Helvetica;"><?php echo $result; ?></div>
					
		<div style="padding-top:2em;text-align:center">
		<?php
			$app = Yii::app();
			
			echo CHtml::ajaxLink('<button class="btn btn-sliding-green btn-sliding-green-a icon-checkmark" style="'.(($app->language == 'en')?'padding-left:25px;padding-right:25px;':'padding-left:28px;padding-right:28px;').'">'.'<span style="font-family:Helvetica">'.Yii::t('common', 'OK').'</span>'.'</button>', '#',
					array(),
					array('onclick'=>'$("#messageDialogWindow").dialog("close");'));
		?>
		</div>
	</div>							
	
	<?php
			
	$this->endWidget('zii.widgets.jui.CJuiDialog');	
?>
