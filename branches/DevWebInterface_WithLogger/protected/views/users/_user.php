<div>
<div style='float: left;width:50%'>
<?php 
echo CHtml::link($data['realname'], "#", 
				 array('onclick'=>'TRACKER.trackUser('. $data['id'] .');')); 
echo "&nbsp;&nbsp;&nbsp;&nbsp;";
?>
</div>

<!--//$space_length = 10 - strlen($data['realname']);-->
<!--//-->
<!--//for ( $i = 0; $i <= $space_length; $i++) {-->
<!--//	echo "&nbsp;";-->
<!--//}-->
<div style='float: left;'>
<?php
echo CHtml::link('<img src="images/delete.png"  />', '#',
					array('onclick'=>CHtml::ajax(
											array(
												'url'=>$this->createUrl('users/deleteFriendShip', array('friendShipId'=>$data['friendShipId'])),
												'success'=> 'function(result) { alert(result); }',
											)))
					  );
echo "&nbsp;";
?>
</div>
<?php
if (isset($data['status']) && $data['status'] == 0 
	&& isset($data['requester']) && $data['requester'] == false) 
{
	/*
	 * if status is zero, it means friend ship request is made and not yet confirmed.
	 * requester is about who made first friend request if one is requester he cannot approve friendship
	 */
?>
<div style='float: left;'>
<?php	
	echo CHtml::link('<img src="images/approve.png"  />', '#',
					array('onclick'=>CHtml::ajax(
											array(
												'url'=>$this->createUrl('users/approveFriendShip', array('friendShipId'=>$data['friendShipId'])),
												'success'=> 'function(result) { alert(result); }',
											)))
					  );
?>	
</div>	
<?php			  					 	
}	
else if (isset($data['status']) && $data['status'] == -1) {
	/*
	 * if status is not exist or equal to -1 it means there is no relation between these users.
	 */
?>
<div style='float: left;'>
<?php	
	echo CHtml::link('<img src="images/user_add_friend.png"  />', '#',
					  array('onclick'=>CHtml::ajax(
					  						array('url'=>$this->createUrl('users/addAsFriend', array('friendId'=>$data['id'])),
					  							  'success'=>'function(result) { alert(result); }',
												 )
					  					)
					  		)
					 );
?>					 
</div>	
<?php				 
}				  
?>
</div>

</br>