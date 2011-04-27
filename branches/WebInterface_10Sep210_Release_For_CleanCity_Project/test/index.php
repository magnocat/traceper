<?php
require_once('simpletest/autorun.php');

class AllTests extends TestSuite {
    function AllTests() 
    {
    	set_include_path(get_include_path(). PATH_SEPARATOR . dirname(__FILE__));
    			
        $this->TestSuite('All tests');
        $this->addFile('TestOfDeviceManager.php');
		$this->addFile('TestOfWebClientManager.php');
    }
}

?>

