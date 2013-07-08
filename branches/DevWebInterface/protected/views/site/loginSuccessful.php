<script type="text/javascript">
	var h = $(window).height(), offsetTop = 60; // Calculate the top offset
	var w = $(window).width(), offsetLeft = 396; // Calculate the left offset

	$('#topBar').css('height', '60px');
	$('#sideBar').css('top', '60px');
	$('#sideBar').css('width', '380px');
	$('#sideBar').css('height', (h - offsetTop));
	$('#sideBar').css('min-height', (485 + 100 - 60));
	$('#bar').css('top', offsetTop);
	$('#bar').css('height', (h - offsetTop));
	$('#bar').css('left', '380px');
	$('#bar').css('min-height', (485 + 100 - 60));		
	$('#map').css('height', (h - offsetTop)); //$("#map").css('height', '94%');
	$('#map').css('width', (w - offsetLeft));
	$('#map').css('min-width', (735 + 260 - 380));
	$('#map').css('min-height', (485 + 100 - 60));
		
	$("#username").html('<?php echo $realname ?>');
	$("#userId").html('<?php echo $id ?>');	
	$("#tab_view").tabs("load",0);
	$("#tab_view").tabs("select",0);

	//alert('loginSuccessful.php: offsetTop:'+(offsetTop)+' offsetLeft:'+(offsetLeft));
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
	$("#languageBlock").hide();	
	$("#lists").load();
	$("#lists").show();
	$("#loginBlock").hide();
	$("#userBlock").load();
	$("#userBlock").show();	
	TRACKER.getFriendList(1);	
	TRACKER.getImageList();
</script>		

