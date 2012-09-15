<?php

require_once("../bootstrap.php");

class UploadTest extends WebTestCase
{
	public $fixtures=array(
			'geofence'=>'Geofence',
	);

	protected function setUp()
	{
		$this->setBrowser("*firefox");
		$this->setBrowserUrl("http://localhost/Traceper_WebInterface/");
	}
	
	/*
	public function testCreateGeofence()
	{
		$this->open("index-test.php");
		$this->click("id=showLoginWindow");

		// after click the window it loads windows so we need to wait a little
		for ($second = 0; ; $second++) {
			if ($second >= 60) $this->fail("timeout");
			try {
				if ($this->isElementPresent("id=LoginForm_email")) break;
			} catch (Exception $e) {
			}
			sleep(1);
		}

		//$this->waitForElementPresent("id=showLoginWindow");

		$this->type("id=LoginForm_email", "test@traceper.com");
		$this->type("id=LoginForm_password", "12345");
		$this->click("id=yt0");

		$this->click("css=#geoFence > img");
		$this->verifyTextPresent("Select 3 points to generate a Geofence");

		$this->click("css=#geoFence > img");
		$this->verifyTextPresent("Geofence points selection disabled");

	}
	*/
}
