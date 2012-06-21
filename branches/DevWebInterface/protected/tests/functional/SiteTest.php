<?php


require_once("bootstrap.php");

class SiteTest extends WebTestCase
{
	
	public $fixtures=array(
			'users'=>'Users',
			'candidates'=>'UserCandidates',
	);	
	
	public function testLogin()
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
		$this->assertEquals("Test User", $this->getText("id=username"));
	}
	
	//TODO: aynı e-mail adresi ile iki kez giriş yapılması test edilmeli.
	
	public function testRegister()
	{
		$this->open('index-test.php');
		$this->click("id=showRegisterWindow");
		// after click the window it loads windows so we need to wait a little
		for ($second = 0; ; $second++) {
			if ($second >= 60) $this->fail("timeout");
			try {
				if ($this->isElementPresent("id=RegisterForm_email")) break;
			} catch (Exception $e) {
			}
			sleep(1);
		}
		
		$this->type("id=RegisterForm_email", "ahmetmermerkaya@gmail.com");
		$this->type("id=RegisterForm_name", "Ahmet Oğuz Mermerkaya");
		$this->type("id=RegisterForm_password", "123456");
		$this->type("id=RegisterForm_passwordAgain", "123456");
		$this->click("id=yt0");
		$this->verifyTextPresent("Aktivasyon maili e-mail adresinize gönderilmiştir...");
		$this->click("//button[@type='button']");
	}
	
	public function testActivate()
	{
		$this->open("index-test.php?r=site/activate");
		
		$this->verifyTextPresent("Sorry, you entered this page with wrong parameters");
		
		$key = md5($this->candidates('candidate1')->email . $this->candidates('candidate1')->time);
		$url = 'index-test.php?r=site/activate&email='. $this->candidates('candidate1')->email. '&key='.$key;

		$this->open($url);
		
		$this->verifyTextPresent("Your account has been activated successfully, you can login now");
		
	}
	
	
	
	

}
