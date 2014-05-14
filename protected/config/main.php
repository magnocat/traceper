<?php

/*User Type Enumeration in PHP*/
class UserType
{
	const RealUser  = 0;
	const GPSDevice = 1;
	const RealStaff = 2;
	const GPSStaff  = 3;
}

/*Gender Enumeration in PHP*/
class Gender
{
	const Male   = 0;
	const Female = 1;
}

/*Group Type Enumeration in PHP*/
class GroupType
{
	const FriendGroup = 0;
	const StaffGroup  = 1;
}

/*User Authority Level Enumeration in PHP*/
class AuthorityLevel
{
	const UnauthorizedUser = 0;
	const StandardUser     = 1;
	const AuthorizedUser   = 2;
	const SuperUser        = 3;
}

/*Password Reset Status Enumeration in PHP*/
class PasswordResetStatus
{
	const RequestInvalid = -1;
	const NoRequest      = 0;
	const RequestValid   = 1;
}

/*Location Sources Enumeration in PHP*/
class LocationSource
{
	const NoSource 		 = -1;
	const WebIP    	     = 0;
	const Mobile   		 = 1;
	const WebGeolocation = 2;
}

// uncomment the following to define a path alias
// Yii::setPathOfAlias('local','path/to/local-folder');

// This is the main Web application configuration. Any writable
// CWebApplication properties can be configured here.
return array(
	'basePath'=>dirname(__FILE__).DIRECTORY_SEPARATOR.'..',
	'name'=>'Traceper',
	'sourceLanguage'=>'xyz', //It is a trick to be able to use abbreviation for long strings in both real source and 
							 //target languages, since the translation is not performed when source and target languases are same
	'language'=>'tr',
	//'language'=>'en',

	// preloading 'log' component
	'preload'=>array('log'),

	// autoloading model and component classes
	'import'=>array(
		'application.models.*',
		'application.components.*',
		'application.components.FirePHPCore.*',
    	//'ext.facebook.*',  // get facebook lib
    	//'ext.facebook.lib.*',
		'ext.EasyTabs.*',
		//'ext.imageSelect.*',
		'application.helpers.*',
	),

	'modules'=>array(
		// uncomment the following to enable the Gii tool
		
		'gii'=>array(
			'class'=>'system.gii.GiiModule',
			'password'=>'123456',
		 	// If removed, Gii defaults to localhost only. Edit carefully to taste.
			'ipFilters'=>array('127.0.0.1','::1'),
		),
		
	),
	
	// application components
	'components'=>array(
		'user'=>array(
			// enable cookie-based authentication
			'class' => 'WebUser',
			'allowAutoLogin'=>true,
		),
		'widgetFactory' => array(
            'widgets' => array(
                'CJuiAutoComplete' => array(
                    'themeUrl' => 'css/jqueryui',
                    'theme' => 'cupertino',
                ),
                'CJuiDialog' => array(
                    'themeUrl' => 'css/jqueryui',
                    'theme' => 'cupertino',
                ),
                'CJuiDatePicker' => array(
                    'themeUrl' => 'css/jqueryui',
                    'theme' => 'cupertino',
                ),
                'CJuiTabs' =>array(
                	'themeUrl' => 'css/jqueryui',
                    'theme' => 'cupertino',
                ),
                'CJuiButton' =>array(
                	'themeUrl' => 'css/jqueryui',
                    'theme' => 'cupertino',
                ),
            ),
       ),
		// uncomment the following to enable URLs in path-format
		/*
		'urlManager'=>array(
			'urlFormat'=>'path',
			'rules'=>array(
				'<controller:\w+>/<id:\d+>'=>'<controller>/view',
				'<controller:\w+>/<action:\w+>/<id:\d+>'=>'<controller>/<action>',
				'<controller:\w+>/<action:\w+>'=>'<controller>/<action>',
			),
		),
		*/
		/*
		'db'=>array(
			'connectionString' => 'sqlite:'.dirname(__FILE__).'/../data/testdrive.db',
		),
		// uncomment the following to use a MySQL database
		*/
		'db'=>array(
			'connectionString' => 'mysql:host=localhost;dbname=php',
			'emulatePrepare' => true,
			'username' => 'root',
			'password' => '',
			'charset' => 'utf8',
			'enableProfiling'=>(isset($_REQUEST['client']) && ($_REQUEST['client'] == 'mobile'))?false:true,
			'enableParamLogging'=>(isset($_REQUEST['client']) && ($_REQUEST['client'] == 'mobile'))?false:true,				
			'schemaCachingDuration'=>3600 // turn on schema caching to improve performance											
		),
			
		'session' => array(
			'class' => 'CCacheHttpSession',
		),

		'cache' => array(
			'class' => 'CApcCache',
		),			
		
		'errorHandler'=>array(
			// use 'site/error' action to display errors
            'errorAction'=>'site/error',
        ),
			
// 		'log'=>array(
// 			'class'=>'CLogRouter',
// 			'routes'=>array(
// 				array(
// 					'class'=>'CFileLogRoute',
// 					'levels'=>'error, warning', //SQL sorgulari icin listeye profile'i da ekle
// 				),
// 				// uncomment the following to show log messages on web pages
// 				/*
// 				array(
// 					'class'=>'CWebLogRoute',
// 				),
// 				*/
// 			),
// 		),

		'log'=>array(
			'class'=>'CLogRouter',
			'routes'=>array(
				array(
					'class'=>'CProfileLogRoute',
					//'report'=>'summary',
					'enabled'=>YII_DEBUG,
					'showInFireBug'=>true,
					//'ignoreAjaxInFireBug'=>false //Sadece webLogRoute'ta kullaniliyormus	
				)										
			),
		),			
		
        'fixture'=>array(
            'class'=>'system.test.CDbFixtureManager',
        ),

		'clientScript'=>array(				
				'packages'=>array(
						'jquery'=>array(
								//'baseUrl'=>'//ajax.googleapis.com/ajax/libs/jquery/1.8.3/',
								'baseUrl'=>'//ajax.googleapis.com/ajax/libs/jquery/2.0.2/',
								'js'=>YII_DEBUG ? array('jquery.js') : array('jquery.min.js'),
								'coreScriptPosition'=>CClientScript::POS_HEAD
						),												
						'jquery.ui'=>array(
								//'baseUrl'=>'//ajax.googleapis.com/ajax/libs/jqueryui/1.8.24/',
								'baseUrl'=>'//ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/',
								'js'=>YII_DEBUG ? array('jquery-ui.js') : array('jquery-ui.min.js'),
						        'depends'=>array('jquery'),
						        'coreScriptPosition'=>CClientScript::POS_BEGIN
						),						
				),
		),
		'image'=>array(
				'class'=>'application.extensions.image.CImageComponent',
				// GD or ImageMagick
				'driver'=>'GD',
				// ImageMagick setup path
				//'params'=>array('directory'=>'/opt/local/bin'),
		),
		'facebook'=>array(
				'class' => 'ext.yii-facebook-opengraph.SFacebook',
				
				//'appId'=>'118597668307459', //PRODUCTION: 'YOUR_FACEBOOK_APP_ID', // needed for JS SDK, Social Plugins and PHP SDK
				//'secret'=>'7e4c3f814585c845fd85ab21337504e5', //PRODUCTION: 'YOUR_FACEBOOK_APP_SECRET', // needed for the PHP SDK
				
				'appId'=>'396574887041318', //TEST: 'YOUR_FACEBOOK_APP_ID', // needed for JS SDK, Social Plugins and PHP SDK
				'secret'=>'e3dc4912a5429dac709cd11f4a812468', //TEST: 'YOUR_FACEBOOK_APP_SECRET', // needed for the PHP SDK				
				
				//'fileUpload'=>false, // needed to support API POST requests which send files
				//'trustForwarded'=>false, // trust HTTP_X_FORWARDED_* headers ?
				'locale'=>'en_US', // override locale setting (defaults to en_US)
				'jsSdk'=>true, // don't include JS SDK
				'async'=>true, // load JS SDK asynchronously
				'jsCallback'=>false, // declare if you are going to be inserting any JS callbacks to the async JS SDK loader
				//'status'=>true, // JS SDK - check login status
				//'cookie'=>true, // JS SDK - enable cookies to allow the server to access the session
				'oauth'=>true,  // JS SDK - enable OAuth 2.0
				//'xfbml'=>true,  // JS SDK - parse XFBML / html5 Social Plugins
				//'frictionlessRequests'=>true, // JS SDK - enable frictionless requests for request dialogs
				//'html5'=>true,  // use html5 Social Plugins instead of XFBML
				//'ogTags'=>array(  // set default OG tags
				//'og:title'=>'MY_WEBSITE_NAME',
				//'og:description'=>'MY_WEBSITE_DESCRIPTION',
				//'og:image'=>'URL_TO_WEBSITE_LOGO',
				//),
		)			
	),

	// application-level parameters that can be accessed
	// using Yii::app()->params['paramName']
	'params'=>array(
		// this is used in contact page
		'adminEmail'=>'contact@traceper.com',
		'contactEmail'=>'contact@traceper.com',
		'noreplyEmail'=>'noreply@traceper.com',
		'itemCountInOnePage'=> 20,  // this is the number of users and groups that are shown in a page
		'uploadCountInOnePage'=> 10, // this is the number of images that are shown in a page	
		'itemCountInDataListPage'=> 10,
		'minDistanceInterval'=> 500, //meters
		'minDataSentInterval'=> 300000, //milliseconds
		'duplicateEntryDbExceptionCode' => 23000,
		'uploadPath'=>dirname(__FILE__).DIRECTORY_SEPARATOR.'..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'upload',
		'featureStaffManagementEnabled' => false,
		'featureFriendManagementEnabled' => true,
		'featureGPSDeviceEnabled' => false,
	),
);