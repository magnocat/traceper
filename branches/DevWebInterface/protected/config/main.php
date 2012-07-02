<?php

define('USER_MANAGEMENT_FEATURE', 1);
define('FRIENDS_FEATURE', 1);

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
	const Male  = 0;
	const Female = 1;
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
    	'ext.facebook.*',  // get facebook lib
    	'ext.facebook.lib.*',
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
		),
		
		'errorHandler'=>array(
			// use 'site/error' action to display errors
            'errorAction'=>'site/error',
        ),
		'log'=>array(
			'class'=>'CLogRouter',
			'routes'=>array(
				array(
					'class'=>'CFileLogRoute',
					'levels'=>'error, warning',
				),
				// uncomment the following to show log messages on web pages
				/*
				array(
					'class'=>'CWebLogRoute',
				),
				*/
			),
		),
		
        'fixture'=>array(
            'class'=>'system.test.CDbFixtureManager',
        ),		
	),

	// application-level parameters that can be accessed
	// using Yii::app()->params['paramName']
	'params'=>array(
		// this is used in contact page
		'adminEmail'=>'contact@traceper.com',
		'contactEmail'=>'contact@traceper.com',
		'itemCountInOnePage'=> 10,  // this is the number of users and groups that are shown in a page
		'uploadCountInOnePage'=> 5, // this is the number of images that are shown in a page	
		'itemCountInDataListPage'=> 20,
		'minDistanceInterval'=> 500,
		'minDataSentInterval'=> 300000,
		'duplicateEntryDbExceptionCode' => 23000,
		'uploadPath'=>dirname(__FILE__).DIRECTORY_SEPARATOR.'..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'upload',
	),
);