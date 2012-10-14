<?php

require_once("../bootstrap.php");

class SiteTest extends WebTestCase
{	
	public $fixtures=array(
			'users'=>'Users',
			'friends'=>'Friends',
	);	
	
	protected function setUp()
	{
		$this->setBrowser("*firefox");
		$this->setBrowserUrl("http://localhost/traceper/branches/DevWebInterface/");
	}	
	
	public function testGetFriendsList()
	{
		$this->open("index-test.php");
		$this->click("id=showLoginWindow");
		$this->waitForElementPresent("id=showLoginWindow");
		
		$this->type("id=LoginForm_email", "test@traceper.com");
		$this->type("id=LoginForm_password", "12345");
		$this->click("id=yt0");	
		
		$this->click("link=".Yii::t('layout', 'Users'));
		$this->waitForElementPresent("id=userListView");

		$this->assertEquals("Test User 2", $this->getText("link=Test User 2"));
		$this->assertEquals("Test User 4", $this->getText("link=Test User 4"));
		
		$this->click("link=".Yii::t('layout', 'Staff'));
		$this->waitForElementPresent("id=staffListView");
		
 		$this->assertEquals("Test User 5", $this->getText("link=Test User 5"));
 		$this->assertEquals("Test User 6", $this->getText("link=Test User 6"));		
	}
	
	public function testDeleteFriendShip()
	{
		$this->open("index-test.php");		
		
		$this->click("id=showLoginWindow");
		$this->waitForElementPresent("id=showLoginWindow");
		sleep(1);
		
		$this->type("id=LoginForm_email", "test@traceper.com");
		$this->type("id=LoginForm_password", "12345");
		$this->click("id=yt0");
		sleep(1);
		
		$this->click("link=".Yii::t('layout', 'Users'));
		$this->waitForElementPresent("id=userListView");
		sleep(1);

		$this->click("//div[@id='userListView']/table/tbody/tr[2]/td[4]/a/img");
		sleep(1);
		$this->click("xpath=(//button[@type='button'])[2]");
		sleep(1);
		$this->click("//div[@id='userListView']/table/tbody/tr/td[4]/a/img");
		sleep(1);
		$this->click("xpath=(//button[@type='button'])[2]");
		sleep(1);
		$this->click("css=td");
		
		//$this->setSpeed('120');
	
	    for ($second = 0; ; $second++) {
	        if ($second >= 60) $this->fail("timeout");
	        try {
	            if ($this->isTextPresent("Kullanıcı bulunamadı")) break;
	        } catch (Exception $e) {}
	        sleep(1);
	    }
	
	    $this->verifyTextPresent("Kullanıcı bulunamadı");
	}	
}
