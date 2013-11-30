<script type="text/javascript">
	var h = $(window).height(), offsetTop = 60; // Calculate the top offset
	var w = $(window).width(), offsetLeft = 396; // Calculate the left offset
	var userListHeight = ((h - offsetTop - 72) > 445)?(h - offsetTop - 72):445;
	
    //$.post('saveToSession.php', { width:w, height:userListHeight }, function(json) {
    $.post('index.php?r=site/getWinDimensions', { width:w, height:userListHeight }, function(json) {    
        if(json.outcome == 'success') {
        	//alert('OKKKKK');
            // do something with the knowledge possibly?
        } else {
            alert('Unable to let PHP know what the screen resolution is!');
        }
    },'json');

	if ($('#sideBar > #content').css('display') == "none")
	{					
		var offsetLeft = 396;

		//$('.logo_inFullMap').fadeOut().animate({left:'10px'});
		$('#sideBar > #content').fadeIn('slow');
		$('#sideBar').animate({width:'396px'}, function(){  $('#bar').css('background-image','url("images/left.png")') });
		$('#map').animate({width:(w - offsetLeft)});
		$('#bar').animate({left:'380px'});			
	}    

	$("#users_tab").css("min-height", (485 + 100 - 60 - 72)); $("#users_tab").css("height", (h - offsetTop - 72));
	$("#photos_tab").css("min-height", (485 + 100 - 60 - 72)); $("#photos_tab").css("height", (h - offsetTop - 72));
	$("#groups_tab").css("min-height", (485 + 100 - 60 - 72)); $("#groups_tab").css("height", (h - offsetTop - 72));
	
	$("#usersGridView").css("height", userListHeight - 50);
	$("#uploadsGridView").css("height", userListHeight - 50);
	$("#groupsGridView").css("height", userListHeight - 50);

	$('#tab_view').bind('easytabs:before', function(e, $clicked, $targetPanel, settings){
        switch($targetPanel.get(0).id)
        {
		   	 case "users_tab": //Friends
		   	 {
		   		 TRACKER.showImagesOnTheMap = false; TRACKER.showUsersOnTheMap = true; TRACKER.getImageList(); TRACKER.getFriendList(1, 0/*UserType::RealUser*/);
		   	 }
		   	 break;
		   	   
		   	 case "photos_tab": //Uploads
		   	 {
		   		 TRACKER.showImagesOnTheMap = true; TRACKER.showUsersOnTheMap = false; TRACKER.getImageList(); TRACKER.getFriendList(1, 0/*UserType::RealUser*/);
		   	 }
		   	 break;
		   	   
		   	 case "groups_tab": //Groups
		   	 {
		   		 TRACKER.showImagesOnTheMap = false; TRACKER.showUsersOnTheMap = true; TRACKER.getImageList(); TRACKER.getFriendList(1, 0/*UserType::RealUser*/);
		   	 }
		   	 break;       
        }
	});	    

	resetAllFormErrors();

	$('#topBar').css('height', '60px');
	$('#sideBar').css('top', '60px');
	$('#sideBar').css('width', '380px');
	$('#sideBar').css('height', (h - offsetTop));
	$('#sideBar').css('min-height', (485 + 100 - 60));
	$('#bar').css('top', offsetTop);
	$('#bar').css('height', (h - offsetTop));
	$('#bar').css('left', '380px');
	$('#bar').css('background-image','url("images/left.png")');
	$('#bar').css('min-height', (485 + 100 - 60));		
	$('#map').css('height', (h - offsetTop)); //$("#map").css('height', '94%');
	$('#map').css('width', (w - offsetLeft));
	$('#map').css('min-width', (735 + 260 - 380));
	$('#map').css('min-height', (485 + 100 - 60));

	$("#username").html('<?php echo $realname ?>');
	$("#userId").html('<?php echo $id ?>');	
	//$("#tab_view").tabs("load",0);
	//$("#tab_view").tabs("select",0);

	//$('#tab_view').easytabs('select', '#users_tab');

	function changeSrcTitleBack(elementid, imgSrc, titleText)
	{
	  document.getElementById(elementid).src = imgSrc;
	  document.getElementById(elementid).title = titleText;
	}	

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

	$newRequestsCount = null;
	$totalRequestsCount = null;

	Friends::model()->getFriendRequestsInfo(Yii::app()->user->id, $newRequestsCount, $totalRequestsCount);

	if($newRequestsCount > 0)
	{
		if($newRequestsCount <= 5)
		{
		?>
		<script type="text/javascript">
			document.getElementById('friendRequestsImage').src = "images/friends_" + <?php echo $newRequestsCount ?> + ".png";			
			document.getElementById('friendRequestsImage').title = "<?php echo Yii::t('users', 'Friendship Requests').' ('.$newRequestsCount.' '.Yii::t('users', 'new').(($totalRequestsCount > $newRequestsCount)?Yii::t('users', ' totally ').$totalRequestsCount:'').Yii::t('users', ' friendship request(s) you have').')' ?>";
			document.getElementById('friendRequestsImage').onclick = function() {changeSrcTitleBack('friendRequestsImage', 'images/friends.png', '<?php echo Yii::t('users', 'Friendship Requests') ?>')};
		</script>	
		<?php
		}
		else
		{
		?>
		<script type="text/javascript">
			document.getElementById('friendRequestsImage').src = "images/friends_many.png";			
			document.getElementById('friendRequestsImage').title = "<?php echo Yii::t('users', 'Friendship Requests').' ('.$newRequestsCount.' '.Yii::t('users', 'new').(($totalRequestsCount > $newRequestsCount)?Yii::t('users', ' totally ').$totalRequestsCount:'').Yii::t('users', ' friendship request(s) you have').')' ?>";
			document.getElementById('friendRequestsImage').onclick = function() {changeSrcTitleBack('friendRequestsImage', 'images/friends.png', '<?php echo Yii::t('users', 'Friendship Requests') ?>')};
		</script>	
		<?php
		}
	}	
?>

<script type="text/javascript">
	$("#logo").hide();
	$("#logoMini").load();
	$("#logoMini").show();	
	$("#loginBlock").hide();
	$("#formContent").hide();
	$("#publicUploadsContent").hide();
	$("#showPublicPhotosLink").hide();
	$("#showCachedPublicPhotosLink").hide();	
	$("#showRegisterFormLink").hide();		
	$("#userBlock").load();
	$("#userBlock").show();
	$("#lists").load();
	$("#lists").show();	
	//$("#content").load();
	$("#content").show();
	
	TRACKER.showImagesOnTheMap = false;
	TRACKER.showUsersOnTheMap = true;
	TRACKER.userId = <?php echo Yii::app()->user->id; ?>;		
	TRACKER.getFriendList(1, 0/*UserType::RealUser*/);	
	TRACKER.getImageList(false, true);
</script>		

