<div>
<?php 
echo CHtml::link($data['realname'], "#", 
				 array('onclick'=>'TRACKER.trackUser('. $data['id'] .');')); 
echo "&nbsp;&nbsp;&nbsp;&nbsp;";

echo CHtml::link('Delete', '#',
					array('onclick'=>CHtml::ajax(
											array(
												'url'=>$this->createUrl('users/deleteFriendShip', array('friendShipId'=>$data['friendShipId'])),
												'success'=> 'function(result) { alert(result); }',
											)))
					  );


if (isset($data['status']) && $data['status'] == 0 
	&& isset($data['requester']) && $data['requester'] == false) 
{
	/*
	 * if status is zero, it means friend ship request is made and not yet confirmed.
	 * requester is about who made first friend request if one is requester he cannot approve friendship
	 */
	echo CHtml::link('Approve', '#',
					array('onclick'=>CHtml::ajax(
											array(
												'url'=>$this->createUrl('users/approveFriendShip', array('friendShipId'=>$data['friendShipId'])),
												'success'=> 'function(result) { alert(result); }',
											)))
					  );
	
}	
else if (isset($data['status']) && $data['status'] == -1) {
	/*
	 * if status is not exist or equal to -1 it means there is no relation between these users.
	 */
	echo CHtml::link('Add as Friend', '#',
					  array('onclick'=>CHtml::ajax(
					  						array('url'=>$this->createUrl('users/addAsFriend', array('friendId'=>$data['id'])),
					  							  'success'=>'function(result) { alert(result); }',
												 )
					  					)
					  		)
					 );
}				  
?>
</div>