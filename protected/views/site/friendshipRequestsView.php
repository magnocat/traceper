<?php

if($newRequestsCount > 0)
{
	echo CHtml::ajaxLink($newRequestsCount, $this->createUrl('users/GetFriendRequestList'),
			array(
					'complete'=> 'function() { $("#friendReqCount").hide(); $("#friendRequestsWindow").dialog("open"); return false;}',
					'update'=> '#friendRequestsWindow',
			),
			array(
					'id'=>'friendReqCount','class'=>'vtip', 'title'=>$friendReqTooltip,
					'class'=>'friendRequestCount',
					'onMouseOver' => "$('#friendReqCount').css('background-color', '#F75D59');",
					'onMouseOut' => "$('#friendReqCount').css('background-color', '#F62217');",
			));
}							

echo CHtml::ajaxLink('Friend Requests', $this->createUrl('users/GetFriendRequestList'),
		array(
				'complete'=> 'function() { $("#friendReqCount").hide(); $("#friendRequestsWindow").dialog("open"); return false;}',
				'update'=> '#friendRequestsWindow',
		),
		array(
				'id'=>'showFriendRequestsWindow','class'=>'vtip', 'title'=>$friendReqTooltip,
				'class'=>'hi-icon icon-mail',
				'onMouseOver' => "$('#friendReqCount').css('background-color', '#F75D59');",
				'onMouseOut' => "$('#friendReqCount').css('background-color', '#F62217');",											
		));

?>