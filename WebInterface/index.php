<?php
/********************************************
*
*	Filename:	index.php
*	Author:		Ahmet Oguz Mermerkaya
*	E-mail:		ahmetmermerkaya@hotmail.com
*	Begin:		Tuesday, April 21, 2009  11:27
*
*********************************************/
define("IN_PHP", true);

define("WEB_ADDRESS", 'http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['SCRIPT_NAME']));

require_once("includes/config.php");
require_once("includes/content.php");

$dbc = NULL;  // database connectivity;
$out = NULL;

if (isset($_REQUEST['action']) && !empty($_REQUEST['action'])) 
{
	$action = $_REQUEST['action'];	
	if (strpos($action, WEB_CLIENT_ACTION_PREFIX) === 0)
	{
//		require_once('classes/WebClientManager.php');
		$dbc = getMySQLOperator($dbc, $dbHost,$dbUsername,$dbPassword,$dbName);
		$wcm = new WebClientManager($dbc, WEB_CLIENT_ACTION_PREFIX, STAFF_TRACKER_TABLE_PREFIX, 
									ELEMENT_COUNT_IN_LIST_PAGE, ELEMENT_COUNT_IN_LOCATIONS_PAGE,
									ELEMENT_COUNT_IN_PHOTO_PAGE);
		session_start();
		if (!isset($_SESSION["dataFetchedTime"])){
			$_SESSION["dataFetchedTime"] = time();
		}
		if (!isset($_SESSION["imageFetchedTime"])){
			$_SESSION["imageFetchedTime"] = time();
		}
		$wcm->setImageRelatedVars(UPLOAD_DIRECTORY, MISSING_IMAGE, IMAGE_HANDLER);
		$out = $wcm->process($_REQUEST, &$_SESSION["dataFetchedTime"], &$_SESSION["imageFetchedTime"]);
	}
	else if (strpos($action, DEVICE_ACTION_PREFIX) === 0)
	{
//		require_once ('classes/DeviceManager.php');
		$dbc = getMySQLOperator($dbc, $dbHost,$dbUsername,$dbPassword,$dbName);
		$dm = new DeviceManager($dbc, DEVICE_ACTION_PREFIX, STAFF_TRACKER_TABLE_PREFIX, 
								GPS_MIN_DATA_SENT_INTERVAL, GPS_MIN_DISTANCE_INTERVAL);
		$dm->setUploadPath(UPLOAD_DIRECTORY);
		$out = $dm->process($_REQUEST);
	}
}
else {	
	$out = getContent($_SERVER['PHP_SELF'], FETCH_PHOTOS_IN_INITIALIZATION, UPDATE_USER_LIST_INTERVAL, QUERYING_UPDATED_USER_LIST_INTERVAL, GOOGLE_MAP_API_KEY, LANGUAGE);	
}



//FIXME: Bu yaz�l�m� kullananlar ile takip edilen kullan�c�lar�n verilerin
// aynı tablo da m� farkl� tablo da m� tutulmal� ?
// e�er ayn� tablo kullan�lacaksa WebClientManager daki sorgulamalar kontrol edilmeli

echo $out;
//error_log($out, 3, "log.txt");
///////////////////////////////////////////////////
function getMySQLOperator($dbc, $dbHost,$dbUsername,$dbPassword,$dbName ){	
	if ($dbc == NULL) {
		require_once('classes/MySQLOperator.php');
		$dbc = new MySQLOperator($dbHost,$dbUsername,$dbPassword,$dbName);
	}
	return $dbc;
}
function __autoload($class_name) {
    require_once dirname(__FILE__) .'/classes/' . $class_name . '.php';
}
?>