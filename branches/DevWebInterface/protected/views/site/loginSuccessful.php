<script type="text/javascript">		

	$("#username").html('<?php echo $realname ?>');
	$("#userId").html('<?php echo $id ?>');
	$("#lists").show();	
	$("#tab_view").tabs("load",0);
	$("#tab_view").tabs("select",0);

</script>	

<?php
	CHtml::ajax(
		array(
		'url'=>Yii::app()->createUrl('users/getFriendList', array('userType'=>array(UserType::RealUser, UserType::GPSDevice))),
		'update'=>'#users_tab',
		)
	);

	CHtml::ajax(
		array(
		'url'=>Yii::app()->createUrl('users/getFriendList', array('userType'=>array(UserType::RealStaff, UserType::GPSStaff))),
		'update'=>'#staff_tab',
		)
	);
													
	CHtml::ajax(
		array(
		'url'=> Yii::app()->createUrl('upload/getList', array('fileType'=>0)),
		'update'=>'#photos_tab',
		)
	);

	CHtml::ajax(
		array(
		'url'=> Yii::app()->createUrl('groups/getGroupList', array('groupType'=>GroupType::FriendGroup)),
		'update'=>'#groups_tab',
		)
	);
													
	CHtml::ajax(
		array(
		'url'=> Yii::app()->createUrl('groups/getGroupList', array('groupType'=>GroupType::StaffGroup)),
		'update'=>'#staff_groups_tab',
		)
	);																	
?>

<script type="text/javascript">	
	
	$("#loginBlock").hide();
	$("#userBlock").load();
	$("#userBlock").show();
	TRACKER.getFriendList(1);	
	TRACKER.getImageList();

</script>		

