<?php
function getContent($callbackURL, $updateUserListInterval, $apiKey, $language) {
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
	<head>
		<title>Traceper Tracking System</title>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
		<meta name="keywords"  content="" />
		<meta name="description" content="open source online tracking system" />
		<link rel="stylesheet" type="text/css" href="style.css" />
		
     <script type="text/javascript" src="http://www.google.com/jsapi?key=<?php echo $apiKey; ?>">
   </script>
    
      <script type="text/javascript" charset="utf-8">
   
        google.load("maps", "2.x");
   
   //     google.load("jquery", "1.3.1");
		
//		google.load("jqueryui", "1.7.1");
   
      </script>
      
	  <link type="text/css" href="js/jquery/plugins/simplemodal/css/basic.css" rel="stylesheet" />
	  <!--[if lt IE 7]>
		<link type='text/css' href='js/jquery/plugins/simplemodal/css/basic_ie.css' rel='stylesheet' media='screen' />
	  <![endif]-->


	<script type="text/javascript" src="js/jquery/jquery.min.js"></script>
	<script type="text/javascript" src="js/jquery/plugins/simplemodal/jquery.simplemodal.js"></script>
	<script type="text/javascript" src="js/TrackerOperator.js"></script>
	<script type="text/javascript" src="js/LanguageOperator.js"></script>		
	<script type="text/javascript" src="js/bindings.js"></script>	
	<script type="text/javascript">		
		var langOp = new LanguageOperator(); 
		langOp.load("<?php echo $language ?>"); 	
				
		$(document).ready( function(){
			var map;
			try 
			{
				if (GBrowserIsCompatible()) 
				{
   					map = new GMap2(document.getElementById("map"));
   					map.setCenter(new GLatLng(39.504041,35.024414), 4);
					map.setUIToDefault();					
					map.setMapType(G_HYBRID_MAP);			   	
   					var trackerOp = new TrackerOperator('<?php echo $callbackURL; ?>', map, <?php echo $updateUserListInterval; ?>, langOp);			
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
				<div id='bar'></div>					
				<div id='sideBar'>	 				
	 						<div id='logo'></div>
	 						<div id='littleMenu'>
	 							<a href='#touLink'>Terms of use</a>&nbsp;&nbsp;&nbsp;
								<a href='#auLink'>About</a>
							</div>
							<div id='lists'>							
								<div class='title'>Users</div>	
								<div id='searchArea'>						
									<input type='text' id='searchBox' /><img src='images/search.png' id='searchButton'  />
								</div>
								<div id="users">																
								</div>
								<div id='search'>
									<a href='#returnToUserList'> &laquo; Return to user list</a>	
									<div id='results'></div>								
								</div>							
							</div>	
						<div id='loading'>Loading</div>																								
				</div>										
				<div id='map'>MAP</div>
		<!--		
				<div style='position:absolute; left:150px; top:20px;'><img src='images/logo_trans.png'  /></div>	
		-->				
										
	</div>	
	<div id='aboutus'>
		
		
	</div>
	<div id='termsofuse'>Terms of use</div>		
	</body>
</html>
<?php
}
?>
