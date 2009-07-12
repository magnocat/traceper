<?php

require_once('simpletest/autorun.php');

define('IN_PHP', true);

require_once(dirname(__FILE__) . '/../includes/config.php');

require_once(dirname(__FILE__) . '/../classes/MySQLOperator.php');
require_once(dirname(__FILE__) . '/../classes/DeviceManager.php');


define('DEVICE_ACTION_PREFIX_FOR_TEST', 'Device');
define('STAFF_TRACKER_TABLE_PREFIX_FOR_TEST', 'tracker');
define('ELEMENT_COUNT_IN_A_PAGE_FOR_TEST',15);

define('DB_HOST', $dbHost);
define('DB_USERNAME', $dbUsername);
define('DB_PASSWORD', $dbPassword);
define('DB_NAME',$dbName);


?>