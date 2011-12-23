<?php

// change the following paths if necessary
//$yiic=dirname(__FILE__).'/../../../../softwares/yii-1.1.8.r3324/framework/yiic.php';
$yiic=dirname(__FILE__).'/../yii-1.1.8.r3324/framework/yii.php';
$config=dirname(__FILE__).'/config/console.php';

require_once($yiic);

if(isset($config))
{
	//$command_directory=dirname(__FILE__).'/../yii-1.1.8.r3324/framework/yii.php';
	$app=Yii::createConsoleApplication($config);
	//$app->commandRunner->addCommands(YII_PATH.'/commands');
	$app->commandRunner->addCommands(dirname(__FILE__).'/commands');
	$env=@getenv('YII_CONSOLE_COMMANDS');
	
	if(!empty($env))
	{
		//echo "tamam";
		$app->commandRunner->addCommands($env);
	}
	/*
	else
		echo "eksik";
	*/
}
else
	//$app=Yii::createConsoleApplication(array('basePath'=>dirname(__FILE__).'/cli'));
	echo "hata";

$app->run();
