<?php
/********************************************
*
*	Filename:	index.php
*	Author:		Ahmet Oguz Mermerkaya
*	E-mail:		ahmetmermerkaya@hotmail.com
*	Begin:		Tuesday, April 21, 2009  11:27
*
*********************************************/

if (!defined("IN_PHP"))
{
	die();
}

// database host
$dbHost = "localhost";   
// database user name
$dbUsername = "root";
// password to connect to database
$dbPassword = "21236161";
// database name
$dbName = "test";

error_reporting(E_ALL);

define ("STAFF_TRACKER_TABLE_PREFIX", "tracker");

define ("SUCCESS", "1");  // Don't edit
define ("FAILED", "-1");  // Don't edit
define ("MISSING_PARAMETER", "-2"); //Don't edit
define ("UNSUPPORTED_ACTION", "-3"); //Don't edit
define ("UNAUTHORIZED_ACCESS", "-4");
define ("USER_NAME_ALREADY_EXIST", "-5"); //Don't edit

define ("WEB_CLIENT_ACTION_PREFIX", "WebClient");
define ("DEVICE_ACTION_PREFIX", "Device");

define ("ELEMENT_COUNT_IN_LIST_PAGE", 10);
define ("ELEMENT_COUNT_IN_LOCATIONS_PAGE", 100);

?>