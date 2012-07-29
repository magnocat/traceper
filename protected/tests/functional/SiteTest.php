<?php


require_once("../bootstrap.php");

class SiteTest extends WebTestCase
{

	public $fixtures=array(
			'users'=>'Users',
			'candidates'=>'UserCandidates',
	);

	protected function setUp()
	{
		$this->setBrowser("*firefox");
		$this->setBrowserUrl("http://localhost/Traceper_WebInterface/");
	}

	public function testContact()
	{
		$this->open("index-test.php");
		$this->click("id=logo");
		$this->verifyTextPresent("traceper");
	}

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

	public function testLogout()
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
		$this->click("css=#signout > img");
		$this->waitForPageToLoad("30000");
		$this->verifyTextPresent("Kullanıcı bilgileri");
	}

	public function testChangePassword()
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

		$this->click("css=img");
		sleep(1);
		$this->type("id=ChangePasswordForm_currentPassword", "12345");
		$this->type("id=ChangePasswordForm_newPassword", "123456");
		$this->type("id=ChangePasswordForm_newPasswordAgain", "123456");
		$this->click("xpath=(//input[@id='yt0'])[2]");

		$this->click("css=img");
		sleep(1);
		$this->type("id=ChangePasswordForm_currentPassword", "123456");
		$this->type("id=ChangePasswordForm_newPassword", "12345");
		$this->type("id=ChangePasswordForm_newPasswordAgain", "1234");
		$this->click("xpath=(//input[@id='yt0'])[2]");
		sleep(1);
		$this->verifyTextPresent("Passwords not same!");

		$this->click("css=img");
		sleep(1);
		$this->type("id=ChangePasswordForm_currentPassword", "12345");
		$this->type("id=ChangePasswordForm_newPassword", "123457");
		$this->type("id=ChangePasswordForm_newPasswordAgain", "123457");
		$this->click("xpath=(//input[@id='yt0'])[2]");
		sleep(1);
		$this->verifyTextPresent("Password incorrect!");

		$this->click("css=img");
		sleep(1);
		$this->type("id=ChangePasswordForm_currentPassword", "123456");
		$this->type("id=ChangePasswordForm_newPassword", "12345");
		$this->type("id=ChangePasswordForm_newPasswordAgain", "12345");
		$this->click("xpath=(//input[@id='yt0'])[2]");


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

	public function testRegisterGPSTracker()
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

		$this->click("css=#registerGPSTracker > img");
		sleep(2);
		$this->type("id=RegisterGPSTrackerForm_name", "deneme");
		$this->type("id=RegisterGPSTrackerForm_deviceId", "12345678");
		$this->click("xpath=(//input[@id='yt0'])[2]");
		for ($second = 0; ; $second++) {
			if ($second >= 60) $this->fail("timeout");
			try {
				if ($this->isElementPresent("id=ui-dialog-title-messageDialog")) break;
			} catch (Exception $e) {
			}
			sleep(1);
		}
		$this->verifyTextPresent("Cihaz başarıyla kaydedildi");
		sleep(3);
		$this->click("//button[@type='button']");


		$this->click("css=#registerGPSTracker > img");
		sleep(2);
		$this->type("id=RegisterGPSTrackerForm_name", "deneme");
		$this->type("id=RegisterGPSTrackerForm_deviceId", "12345678");
		$this->click("xpath=(//input[@id='yt0'])[2]");
		for ($second = 0; ; $second++) {
			if ($second >= 60) $this->fail("timeout");
			try {
				if ($this->isElementPresent("id=ui-dialog-title-messageDialog")) break;
			} catch (Exception $e) {
			}
			sleep(1);
		}
		$this->verifyTextPresent("Aynı isim ile yalnızca bir GPS takip cihazı ekleyebilirsiniz!");
		$this->click("//button[@type='button']");
	}

	public function testRegisterNewStaff()
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


		$this->click("css=#registerNewStaff > img");
		sleep(2);
		$this->type("id=RegisterNewStaffForm_name", "test");
		$this->type("id=RegisterNewStaffForm_email", "test5@traceper.com");
		$this->type("id=RegisterNewStaffForm_password", "12345");
		$this->type("id=RegisterNewStaffForm_passwordAgain", "12345");
		$this->click("xpath=(//input[@id='yt0'])[2]");
		$this->verifyTextPresent("Bu e-posta kayıtlı!");

		$this->type("id=RegisterNewStaffForm_email", "test25@traceper.com");
		$this->click("xpath=(//input[@id='yt0'])[2]");
		for ($second = 0; ; $second++) {
			if ($second >= 60) $this->fail("timeout");
			try {
				if ($this->isElementPresent("id=ui-dialog-title-messageDialog")) break;
			} catch (Exception $e) {
			}
			sleep(1);
		}
		sleep(2);
		$this->verifyTextPresent("Personel başarıyla kaydedildi");
		sleep(10);
		$this->click("//button[@type='button']");
	}

	public function testInviteUsers()
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


		$this->click("css=#inviteUser > img");
		sleep(2);
		$this->type("id=InviteUsersForm_emails", "test30@traceper.com,test45@traceper.com");
		$this->type("id=InviteUsersForm_message", "Come traceper.com");
		$this->click("xpath=(//input[@id='yt0'])[2]");

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
