<script type="text/javascript">
	var h = $(window).height(), offsetTop = 70; // Calculate the top offset
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

	//$("#users_tab").css("min-height", (485 + 100 - 70 - 82)); $("#users_tab").css("height", (h - offsetTop - 152));
	//$("#photos_tab").css("min-height", (485 + 100 - 70 - 82)); $("#photos_tab").css("height", (h - offsetTop - 152));
	//$("#groups_tab").css("min-height", (485 + 100 - 70 - 68)); $("#groups_tab").css("height", (h - offsetTop - 138));

			$("#users_tab").css("min-height", (485 + 100 - 70 - 82)); $("#users_tab").css("height", (h - offsetTop - 82));
			$("#photos_tab").css("min-height", (485 + 100 - 70 - 82)); $("#photos_tab").css("height", (h - offsetTop - 82));
			$("#groups_tab").css("min-height", (485 + 100 - 70 - 68)); $("#groups_tab").css("height", (h - offsetTop - 72));
			
	 		$("#usersGridView").css("min-height", 370);
			$("#usersGridView").css("height", (h - offsetTop - 82 - 20));
			
	 		$("#uploadsGridView").css("min-height", 370);
			$("#uploadsGridView").css("height", (h - offsetTop - 82 - 20));
			
	 		$("#groupsGridView").css("min-height", 440);
			$("#groupsGridView").css("height", (h - offsetTop - 82 + 10));

		
	
	//$("#usersGridView").css("min-height", (485 + 100 - 70 - 82)); $("#users_tab").css("height", (h - offsetTop - 82));	
	
	//$("#usersGridView").css("height", userListHeight - 50);
	//$("#uploadsGridView").css("height", userListHeight - 50);
	//$("#groupsGridView").css("height", userListHeight - 50);

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

	$('#tab_view').bind('easytabs:ajax:complete', function(e, $clicked, $targetPanel, response, status, xhr){
		var h = $(window).height();
		var offsetTop = 70;

        switch($targetPanel.get(0).id)
        {
		   	 case "users_tab": //Friends
		   	 {
		 		$("#usersGridView").css("min-height", 370);
				$("#usersGridView").css("height", (h - offsetTop - 82 - 20));
		   	 }
		   	 break;
		   	   
		   	 case "photos_tab": //Uploads
		   	 {
		 		$("#uploadsGridView").css("min-height", 370);
				$("#uploadsGridView").css("height", (h - offsetTop - 82 - 20));
		   	 }
		   	 break;
		   	   
		   	 case "groups_tab": //Groups
		   	 {
		 		$("#groupsGridView").css("min-height", 440);
				$("#groupsGridView").css("height", (h - offsetTop - 82 + 10));	
		   	 }
		   	 break;       
        }		

		//alert("ajax complete");
	});		    

	resetAllFormErrors();

	$('#topBar').css('height', '70px');
	$('#sideBar').css('top', '70px');
	$('#sideBar').css('width', '380px');
	$('#sideBar').css('height', (h - offsetTop));
	$('#sideBar').css('min-height', (485 + 100 - 70));
	$('#bar').css('top', offsetTop);
	$('#bar').css('height', (h - offsetTop));
	$('#bar').css('left', '380px');
	$('#bar').css('background-image','url("images/left.png")');
	$('#bar').css('min-height', (485 + 100 - 70));		
	$('#map').css('height', (h - offsetTop)); //$("#map").css('height', '94%');
	$('#map').css('width', (w - offsetLeft));
	$('#map').css('min-width', (735 + 260 - 380));
	$('#map').css('min-height', (485 + 100 - 70));

	$("#username").html('<div class="hi-icon-in-list icon-user"></div><a><?php echo $realname ?></a>');
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
// 	CHtml::ajax(
// 		array(
// 		'url'=>Yii::app()->createUrl('users/getFriendList', array('userType'=>array(UserType::RealUser, UserType::GPSDevice))),
// 		'update'=>'#users_tab',
// 		)
// 	);

// 	CHtml::ajax(
// 		array(
// 		'url'=>Yii::app()->createUrl('users/getFriendList', array('userType'=>array(UserType::RealStaff, UserType::GPSStaff))),
// 		'update'=>'#staff_tab',
// 		)
// 	);
													
// 	CHtml::ajax(
// 		array(
// 		'url'=> Yii::app()->createUrl('upload/getList', array('fileType'=>0)),
// 		'update'=>'#photos_tab',
// 		)
// 	);

// 	CHtml::ajax(
// 		array(
// 		'url'=> Yii::app()->createUrl('groups/getGroupList', array('groupType'=>GroupType::FriendGroup)),
// 		'update'=>'#groups_tab',
// 		)
// 	);
													
// 	CHtml::ajax(
// 		array(
// 		'url'=> Yii::app()->createUrl('groups/getGroupList', array('groupType'=>GroupType::StaffGroup)),
// 		'update'=>'#staff_groups_tab',
// 		)
// 	);	
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

