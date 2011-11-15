<div>
    <a href="javascript:TRACKER.showImageWindow(<?php echo $data["id"]; ?>)"><img src="<?php echo $this->createUrl("image/get", array(
														 'id'=>$data["id"],
														 'thumb'=>'ok'
														)
										  );?>" />
		Image sender:<?php echo $data['realname']; ?>
   </a>
		<?php 
			if ($data['userId'] == Yii::app()->user->id) {
				echo CHtml::ajaxLink('Delete', $this->createUrl('image/delete', array('id'=>$data["id"])), 
 										array(
				 							'success'=>'function(result){ 
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
																		}',
											), 
										array()
									); 
			}
		?>
</div>