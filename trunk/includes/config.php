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

error_reporting(E_ALL); // edit if you know what you do
/**
 * This is the number of elements in user and search list page  
 */
define ("ELEMENT_COUNT_IN_LIST_PAGE", 10); 
/**
 * This is the number of elements when updating user data in background
 */
define ("ELEMENT_COUNT_IN_LOCATIONS_PAGE", 50); 
/**
 * This is the time(milliseconds) interval that an ajax request is made to get the users data in every times of interval 
 */
define ("UPDATE_USER_LIST_INTERVAL", 5000); 
/**
 * This is the prefix of table in mysql database, edit it according to your needs.
 */
define ("STAFF_TRACKER_TABLE_PREFIX", "tracker"); 


define ("WEB_CLIENT_ACTION_PREFIX", "WebClient"); //editing is not recommended 
define ("DEVICE_ACTION_PREFIX", "Device"); //editing is not recommended

define ("SUCCESS", "1");  // Don't edit
define ("FAILED", "-1");  // Don't edit
define ("MISSING_PARAMETER", "-2"); //Don't edit
define ("UNSUPPORTED_ACTION", "-3"); //Don't edit
define ("UNAUTHORIZED_ACCESS", "-4");
define ("USER_NAME_ALREADY_EXIST", "-5"); //Don't edit
?>