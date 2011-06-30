<?php
require_once('..\classes\AuthenticateManager.php');

class AuthenticateManagerTest extends PHPUnit_Framework_TestCase {
    
    private $authenticateManager;
	
	public function setUp()
    {
		$this->authenticateManager = new AuthenticateManager(NULL, NULL, NULL);
    }

    public function tearDown()
    {
        unset($this->authenticateManager);
    }	
	
	public function testIsUserAuthenticated() {
		//throw new PHPUnit_Framework_IncompleteTestError('This test has not been implemented yet.');
		
		$this->assertTrue($this->authenticateManager->isUserAuthenticated()); 
		
	}
}
?>