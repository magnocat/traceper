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
$dbName = "php";

error_reporting(E_ALL); // edit if you know what you do

define("GOOGLE_MAP_API_KEY", "ABQIAAAAEUQFPfeMmwQlu4rVizjq5RTUBQ_8WQnJ0r_AJ0Rg3Y6UmQXNMhTgFS-OpJG3vTTQzF67ve7Li8eh_g");
/* 
 * language macro
 * en for english -> default language
 * tr for turkish
 */
define("LANGUAGE", "en");

/* This is the number of elements in user and search list page  */
define ("ELEMENT_COUNT_IN_LIST_PAGE", 18); 

define ('ELEMENT_COUNT_IN_PHOTO_PAGE',10);
/**
 * This is the number of elements when updating user data in background
 */
define ("ELEMENT_COUNT_IN_LOCATIONS_PAGE", 50); 
/**
 * This is the time(milliseconds) interval that an ajax request is made to get the users data in every times of interval 
 */
define ("UPDATE_USER_LIST_INTERVAL", 5000); 
/**
 * After all users info taken, only users whose location changed are taken from the
 * server in every QUERY_UPDATED_USER_LIST_INTERVAL seconds
 */
define("QUERYING_UPDATED_USER_LIST_INTERVAL", 30000);
/**
 * This is the prefix of table in mysql database, edit it according to your needs.
 */
define ("STAFF_TRACKER_TABLE_PREFIX", "traceper"); 
/**
 * Device minimum data sent time interval in ms 
 */
define("GPS_MIN_DATA_SENT_INTERVAL", 60000);
/**
 * Device minimum data sent distance interval in meters 
 */
define("GPS_MIN_DISTANCE_INTERVAL", 100);
/**
 * Flag to determine fetching photos in initialization
 * its value may be 1 or 0, it is used if cookie is not defined to fetch images
 */
define("FETCH_PHOTOS_IN_INITIALIZATION", 1);
/**
 * UPLOAD_DIRECTORY is the directory that traceper clients
 * upload images maybe some other things in future.
 * 
 *  Keep in mind that these upload directory doesn't have to under web server document root
 *  and web server user has to have right to write to this directory
 */
define("UPLOAD_DIRECTORY", dirname(dirname(__FILE__)).'/upload');
/**
 * WEB_ADDRESS is defined in index.php to get the web site full adress.
 * MISSING_IMAGE is the image when the correct image is not found.
 */
define("MISSING_IMAGE", WEB_ADDRESS . '/images/image_missing.png');

define("IMAGE_HANDLER", WEB_ADDRESS);

define ("WEB_CLIENT_ACTION_PREFIX", "WebClient"); //editing is not recommended 
define ("DEVICE_ACTION_PREFIX", "Device"); //editing is not recommended

define ("SUCCESS", "1");  // Don't edit
define ("FAILED", "-1");  // Don't edit
define ("MISSING_PARAMETER", "-2"); //Don't edit
define ("UNSUPPORTED_ACTION", "-3"); //Don't edit
define ("UNAUTHORIZED_ACCESS", "-4");
define ("USER_NAME_ALREADY_EXIST", "-5"); //Don't edit
define ("EMAIL_NOT_FOUND", "-6");
?>