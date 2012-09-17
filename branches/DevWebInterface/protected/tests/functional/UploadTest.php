<?php

require_once("../bootstrap.php");

class UploadTest extends WebTestCase
{
	public $fixtures=array(
			'upload'=>'Upload',
	);

	protected function setUp()
	{
		$this->setBrowser("*firefox");
		$this->setBrowserUrl("http://localhost/Traceper_WebInterface/");
	}
	
	
	public function testDelete()
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
		sleep(5);

    	$this->click("link=Fotolar");
    	sleep(5);
    	$this->assertTrue($this->isElementPresent("//div[@id='uploadListView']/table/tbody/tr/td[4]/a/img"));
    	$this->click("//div[@id='uploadListView']/table/tbody/tr/td[4]/a/img");
    	sleep(5);
    	$this->verifyTextPresent("Do you want to delete this file");
	}
	
	
	public function testGetList()
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
		sleep(5);
	
		$this->click("link=Fotolar");
		sleep(5);		
		$this->verifyTextPresent("test9");
	
	}
	
	
	public function testSearch()
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
		sleep(5);
	
		$this->click("link=Fotolar");
		sleep(5);
		$this->type("xpath=(//input[@id='SearchForm_keyword'])[2]", "test");
		$this->click("id=searchUploadButton");
		sleep(5);
		$this->verifyTextPresent("Test User 5");
	}
	
}
