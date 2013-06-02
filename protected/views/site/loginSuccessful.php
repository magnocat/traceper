<script type="text/javascript">		
	document.getElementById('topBar').style.height='7%';
	document.getElementById('sideBar').style.height='93%';
	document.getElementById('sideBar').style.width='26%';
	document.getElementById('sideBar').style.top='7%';
	document.getElementById('bar').style.top='7%';
	document.getElementById('bar').style.right='74%';
	document.getElementById('map').style.height='93%'; //$("#map").css('height', '94%');
	document.getElementById('map').style.width='74%';		
	$("#username").html('<?php echo $realname ?>');
	$("#userId").html('<?php echo $id ?>');	
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
	$("#logo").hide();
	$("#logoMini").load();
	$("#logoMini").show();
	$("#registerBlock").hide();
	$("#passwordResetBlock").hide();
	$("#lists").load();
	$("#lists").show();
	$("#loginBlock").hide();
	$("#userBlock").load();
	$("#userBlock").show();	
	TRACKER.getFriendList(1);	
	TRACKER.getImageList();
</script>		

