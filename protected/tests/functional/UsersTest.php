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
	
	public function testDeleteUser()
	{
		$this->open("index-test.php");
	
		$this->click("id=showLoginWindow");
		$this->waitForElementPresent("id=showLoginWindow");
		sleep(1);
	
		$this->type("id=LoginForm_email", "test@traceper.com");
		$this->type("id=LoginForm_password", "12345");
		$this->click("id=yt0");
		sleep(1);
	
		$this->click("link=Personel");
		sleep(1);

		$this->click("//div[@id='staffListView']/table/tbody/tr/td[4]/a/img");
		sleep(1);
		
		$this->click("xpath=(//button[@type='button'])[2]");
		sleep(1);
		
		$this->click("//div[@id='staffListView']/table/tbody/tr/td[4]/a/img");
		sleep(1);
		
		$this->click("xpath=(//button[@type='button'])[2]");
		sleep(1);
	
		for ($second = 0; ; $second++) {
			if ($second >= 60) $this->fail("timeout");
			try {
				if ($this->isTextPresent("Kullanıcı bulunamadı")) break;
			} catch (Exception $e) {
			}
			sleep(1);
		}
	
		$this->verifyTextPresent("Kullanıcı bulunamadı");
	}	
	
	public function testApproveFriendShip()
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
	
		$this->click("css=#friendRequests > img");
		sleep(1);
		
		$this->click("//div[@id='userListDialog']/table/tbody/tr/td[3]/a/img");
		sleep(1);
		
		for ($second = 0; ; $second++) {
			if ($second >= 60) $this->fail("timeout");
			try {
				if ($this->isTextPresent("Arkadaşlık isteği bulunamadı")) break;
			} catch (Exception $e) {
			}
			sleep(1);
		}
		
		$this->verifyTextPresent("Arkadaşlık isteği bulunamadı");	
	}
	
	public function testAddAsFriend()
	{
		$this->open("index-test.php");
	
		$this->click("id=showLoginWindow");
		$this->waitForElementPresent("id=showLoginWindow");
		sleep(1);
	
		$this->type("id=LoginForm_email", "test@traceper.com");
		$this->type("id=LoginForm_password", "12345");
		$this->click("id=yt0");
		sleep(1);
	
		$this->click("link=Personel");
		sleep(1);

		$this->type("id=SearchForm_keyword", "Test");
		sleep(1);
		
		$this->click("id=searchUserButton");
		sleep(1);
		
		$this->click("//div[@id='searchResultList']/table/tbody/tr[3]/td[2]/a/img");
		sleep(1);
		
		$this->click("xpath=(//button[@type='button'])[2]");
		
		for ($second = 0; ; $second++) {
			if ($second >= 60) $this->fail("timeout");
			try {
				if ($this->isTextPresent("Arkadaşlık isteği gönderildi")) break;
			} catch (Exception $e) {
			}
			sleep(1);
		}		
		
		$this->verifyTextPresent("Arkadaşlık isteği gönderildi");
	}	
	
	public function testSearch()
	{
		$this->open("index-test.php");
	
		$this->click("id=showLoginWindow");
		$this->waitForElementPresent("id=showLoginWindow");
		sleep(1);
	
		$this->type("id=LoginForm_email", "test@traceper.com");
		$this->type("id=LoginForm_password", "12345");
		$this->click("id=yt0");
		sleep(1);
	
		$this->click("link=Personel");
		sleep(1);
	
	    $this->type("id=SearchForm_keyword", "User privacy group");
	    sleep(1);
	    
	    $this->click("id=searchUserButton");
	    sleep(1);

		for ($second = 0; ; $second++) {
			if ($second >= 60) $this->fail("timeout");
			try {
				if ($this->isTextPresent("User privacy group test 3")) break;
			} catch (Exception $e) {
			}
			sleep(1);
		}
	
		$this->verifyTextPresent("User privacy group test 3");
		$this->verifyTextPresent("User privacy group test 8");
	}

	public function testGetUserInfo()
	{
		$this->open("index-test.php");
		
		$this->click("id=showLoginWindow");
		$this->waitForElementPresent("id=showLoginWindow");
		sleep(1);

		$this->type("id=LoginForm_email", "test@traceper.com");
		$this->type("id=LoginForm_password", "12345");
		$this->click("id=yt0");
		sleep(1);		
		
		$this->waitForElementPresent("id=username");
		sleep(1);	
			
		$this->click("id=username");
		sleep(1);
		
		$this->verifyTextPresent("Test User");
		$this->verifyTextPresent("0.000000, 0.000000");	
		$this->verifyTextPresent("<< Previous point");
		$this->verifyTextPresent("Operations");
		$this->verifyTextPresent("Zoom in");
		$this->verifyTextPresent("Zoom max");
		
		$this->click("//img[contains(@src,'http://maps.gstatic.com/mapfiles/mv/imgs8.png')]");
		
		$this->click("link=Test User 2");
		for ($second = 0; ; $second++) {
			if ($second >= 60) $this->fail("timeout");
			try {
				if ($this->isTextPresent("Test User 2")) break;
			} catch (Exception $e) {
			}
			sleep(1);
		}
		
		$this->verifyTextPresent("Test User 2");
		$this->verifyTextPresent("10.000000, 20.000000");
		$this->verifyTextPresent("<< Previous point");
		$this->verifyTextPresent("Operations");
		$this->verifyTextPresent("Zoom in");
		$this->verifyTextPresent("Zoom max");				
	}	
}
