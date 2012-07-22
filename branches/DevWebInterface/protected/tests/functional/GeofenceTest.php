<?php

require_once("../bootstrap.php");

class GeofenceTest extends WebTestCase
{
	public $fixtures=array(
			'geofence'=>'Geofence',
	);

	protected function setUp()
	{
		$this->setBrowser("*firefox");
		$this->setBrowserUrl("http://localhost/Traceper_WebInterface/");
	}

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

	public function testSendGeofenceData()
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

		$this->type("id=LoginForm_email", "test@traceper.com");
		$this->type("id=LoginForm_password", "12345");
		$this->click("id=yt0");

		$this->click("css=#geoFence > img");
		
		sleep(1);
		$this->click("//div[@id='map']/div/div/div/div[4]/div/div/div[5]");
		sleep(1);
		$this->click("//div[@id='map']/div/div/div/div[4]/div/div/div[9]");
		sleep(1);
		$this->click("//div[@id='map']/div/div/div/div[4]/div/div/div[4]");
		sleep(1);

		for ($second = 0; ; $second++) {
			if ($second >= 60) $this->fail("timeout");
			try {
				if ($this->isElementPresent("id=createGeofenceWindow")) break;
			} catch (Exception $e) {
			}
			sleep(1);
		}
		
		sleep(1);
		$this->type("id=NewGeofenceForm_name", "deneme");
		$this->type("id=NewGeofenceForm_description", "deneme");
		$this->click("id=yt0");
	}
	
	public function testUpdateGeofencePrivacy()
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

		$this->type("id=LoginForm_email", "test@traceper.com");
		$this->type("id=LoginForm_password", "12345");
		$this->click("id=yt0");

		for ($second = 0; ; $second++) {
			if ($second >= 60) $this->fail("timeout");
			try {
				if ($this->isElementPresent("id=geofenceSettingsWindow")) break;
			} catch (Exception $e) {
			}
			sleep(1);
		}


		//$this->click("//div[@id='userListView']/table/tbody/tr/td[2]/a/img");
		//$this->click("id=GeofenceSettingsForm_geofenceStatusArray_0");
		//$this->click("xpath=(//input[@id='yt0'])[2]");

	}

	public function testGetGeofences()
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

		$this->type("id=LoginForm_email", "test@traceper.com");
		$this->type("id=LoginForm_password", "12345");
		$this->click("id=yt0");

		$this->click("css=#showGeofence > img");

		for ($second = 0; ; $second++) {
			if ($second >= 60) $this->fail("timeout");
			try {
				if ($this->isElementPresent("id=ui-dialog-title-messageDialog")) break;
			} catch (Exception $e) {
			}
			sleep(1);
		}

		sleep(2);

		$this->verifyTextPresent("There is no geofence");
	}
}
