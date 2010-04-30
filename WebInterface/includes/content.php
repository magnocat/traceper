<?php
function getContent($callbackURL, $fetchPhotosInInitialization, $updateUserListInterval, $queryIntervalForChangedUsers, $apiKey, $language) {
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
	<head>
		<title></title>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
		<meta name="keywords"  content="" />
		<meta name="description" content="open source GPS tracking system" />
		<link rel="stylesheet" type="text/css" href="style.css" />
		<link rel="shortcut icon" href="images/icon.png" type="image/x-icon"/>
		
     <script type="text/javascript" src="http://www.google.com/jsapi?key=<?php echo $apiKey; ?>">
   </script>
    
      <script type="text/javascript" charset="utf-8">
   
        google.load("maps", "2.x",{"other_params":"sensor=true"});
   
   //     google.load("jquery", "1.3.1");
		
//		google.load("jqueryui", "1.7.1");
   
      </script>
      	  
	<link type="text/css" href="js/jquery/plugins/colorbox/colorbox.css" rel="stylesheet" media="screen"/>
    <link type="text/css" href="js/jquery/plugins/superfish/css/superfish.css" rel="stylesheet" media="screen"/>
	
	<script type="text/javascript" src="js/jquery/jquery.min.js"></script>
	<script type="text/javascript" src="js/jquery/plugins/jquery.cookie.js"></script>
	<script type="text/javascript" src="js/jquery/plugins/colorbox/jquery.colorbox-min.js"></script>
	<script type="text/javascript" src="js/jquery/plugins/superfish/js/superfish.js"></script>
	
	<script type="text/javascript" src="js/TrackerOperator.js"></script>
	<script type="text/javascript" src="js/LanguageOperator.js"></script>		
	<script type="text/javascript" src="js/bindings.js"></script>	
	<script type="text/javascript">		
		var langOp = new LanguageOperator();
		var fetchPhotosDefaultValue = <?php echo $fetchPhotosInInitialization; ?>;
		langOp.load("<?php echo $language ?>"); 	
				
		$(document).ready( function(){			
			setLanguage(langOp);	
			var checked = false;
			// showPhotosOnMapCookieId defined in bindings.js
			if ($.cookie && $.cookie(showPhotosOnMapCookieId) != null){
				if ($.cookie(showPhotosOnMapCookieId) == "true"){
					checked = true;
				}				
			}
			else if (fetchPhotosDefaultValue == 1){
				checked = true;
			}
			$('#showPhotosOnMap').attr('checked', checked);
			
			var map;
			try 
			{
				if (GBrowserIsCompatible()) 
				{
   					map = new GMap2(document.getElementById("map"));
   					map.setCenter(new GLatLng(39.504041,35.024414), 3);
					map.setUIToDefault();					
					map.setMapType(G_HYBRID_MAP);	
					map.enableRotation();
	   	
   					var trackerOp = new TrackerOperator('<?php echo $callbackURL; ?>', map, <?php echo $fetchPhotosInInitialization; ?>, <?php echo $updateUserListInterval; ?>, <?php echo $queryIntervalForChangedUsers; ?>, langOp);			
					trackerOp.getUserList(1); 	

   				}
			}
   			catch (e) {
				
			}    			
			bindElements(langOp, trackerOp);
		});	
	</script>
	
	</head>
	<body  onunload="GUnload();" >	
	<div id='wrap'>	
				<div class='logo_inFullMap'></div>										
				<div id='bar'></div>
				<div id='sideBar'>						
					<div id='content'>						
	 						<div id='logo'></div>
							<div id='lists'>	
								<div class='titles'>						
									<div class='title active_title' id='user_title'></div>	
									<div class='title' id='photo_title'></div>
								</div>
								<div id='usersList'>	
									<div class='search'>						
										<input type='text' id='searchBox' value='' /><img src='images/search.png' id='searchButton'  />
									</div>
									<div id="users"></div>
									<div class='searchResults'>
										<a href='#returnToUserList'></a>	
										<div id='results'></div>								
									</div>		
								</div>
								<div id="photosList">
									<div class='search'>
										<input type='text' id='searchBox' value='' /><img src='images/search.png' id='searchButton'  />
									</div>
									<input type='checkbox' id='showPhotosOnMap'> Show photos on map
									<div id="photos"></div>
									<div class='searchResults'>
										<a href='#returnToPhotoList' id="returnToPhotoList"></a>	
										<div id='results'></div>								
									</div>
								</div>	
								
							<!-- <div id='footer'>							
									<a href='#auLink'></a>								
								</div>
							-->					
							</div>							
					</div>
																																									
				</div>
				
				<div id='map'>MAP</div>	
				<div id='infoBottomBar'></div>
				<div id='loading'></div>	
						
										
	</div>
	<div style="display:none;">	
	<div id='aboutus'></div>	
	</div>
	</body>
</html>
<?php
}
?>
