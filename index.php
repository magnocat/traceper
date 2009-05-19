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

require_once("includes/config.php");

$dbc = NULL;  // database connectivity;

if (isset($_POST['action']) && !empty($_POST['action'])) {
	$action = $_POST['action'];	
}
else {	
	die(FAILED);
}

$out = NULL;

//FIXME: Bu yazýlýmý kullananlar ile takip edilen kullanýcýlarýn verilerin
// ayný tablo da mý farklý tablo da mý tutulmalý ?
// eðer ayný tablo kullanýlacaksa WebClientManager daki sorgulamalar kontrol edilmeli
 
if (strpos($action, WEB_CLIENT_ACTION_PREFIX) == 0)
{
	require_once('classes/WebClientManager.php');
	$dbc = getMySQLOperator($dbc, $dbHost,$dbUsername,$dbPassword,$dbName);
	$wcm = new WebClientManager($dbc, WEB_CLIENT_ACTION_PREFIX, STAFF_TRACKER_TABLE_PREFIX, ELEMENT_COUNT_IN_LIST_PAGE, ELEMENT_COUNT_IN_LOCATIONS_PAGE);
	$out = $wcm->process($_POST);
}
else if (strpos($action, DEVICE_ACTION_PREFIX) == 0)
{
	require_once ('classes/DeviceManager.php');
	$dbc = getMySQLOperator($dbc, $dbHost,$dbUsername,$dbPassword,$dbName);
	$dm = new DeviceManager($dbc, DEVICE_ACTION_PREFIX, STAFF_TRACKER_TABLE_PREFIX);
	$out = $dm->process($_POST);
}
else 
{
	$out = FAILED;
}

echo $out;

///////////////////////////////////////////////////
function getMySQLOperator($dbc, $dbHost,$dbUsername,$dbPassword,$dbName ){	
	if ($dbc == NULL) {
		require_once('classes/MySQLOperator.php');
		$dbc = new MySQLOperator($dbHost,$dbUsername,$dbPassword,$dbName);
	}
	return $dbc;
}
?>