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
		
		$this->type("id=LoginForm_email", "test@traceper.com");
		$this->type("id=LoginForm_password", "12345");
		$this->click("id=yt0");	
		
		$this->click("link=".Yii::t('layout', 'Users'));
		$this->waitForElementPresent("id=userListView");		
		
	    $this->click("//div[@id='userListView']/table/tbody/tr[2]/td[4]/a/img");
	    $this->click("xpath=(//button[@type='button'])[2]");
	    
	    //$this->assertTrue($this->isTextPresent("Kullanýcý bulunamadý"));
	}	
}
