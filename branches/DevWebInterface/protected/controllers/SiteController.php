<?php

class SiteController extends Controller
{
	/**
	 * Declares class-based actions.
	 */
	public function actions()
	{
		return array(
				// captcha action renders the CAPTCHA image displayed on the contact page
// 				'captcha'=>array(
// 						'class'=>'CCaptchaAction',
// 						'backColor'=>0xFFFFFF,
// 				),
				
				'captcha'=>array(
						'class'=>'CaptchaExtendedAction',
						// if needed, modify settings
						'mode'=>CaptchaExtendedAction::MODE_MATH, //MODE_MATH, MODE_MATHVERBAL, MODE_DEFAULT, MODE_LOGICAL, MODE_WORDS
				),	

				// page action renders "static" pages stored under 'protected/views/site/pages'
				// They can be accessed via: index.php?r=site/page&view=FileName
				'page'=>array(
						'class'=>'CViewAction',
				),
		);
	}

	public function filters()
	{
		return array(
				'accessControl',
		);
	}

	public function accessRules()
	{
		return array(
				array('deny',
						'actions'=>array('changePassword', 'inviteUsers', 'registerGPSTracker', 'runDatabaseQueries'),
						'users'=>array('?'),
				),			
		);
	}

	/**
	 * This is the default 'index' action that is invoked
	 * when an action is not explicitly requested by users.
	 */
	public function actionIndex()
	{
		// renders the view file 'protected/views/site/index.php'
		// using the default layout 'protected/views/layouts/main.php'
		$this->render('index');
	}

	/**
	 * This is the action to handle external exceptions.
	 */
	public function actionError()
	{
		if($error=Yii::app()->errorHandler->error)
		{
			if(Yii::app()->request->isAjaxRequest)
				echo $error['message'];
			else
				$this->render('error', $error);
		}
	}
	
	//UTF8_mail() from parametresini <> ile vermezsen �al��m�yor, buna bak�lacak
	public function UTF8_mail($from, $to, $subject, $message) 
	{
		$from2 = explode("<", $from);
		
		if (isset($from2[0])) {
			$headers = "From: =?UTF-8?B?".base64_encode($from2[0])."?= <".$from2[1]."\r\n";
		} else {
			$headers = "From: ".$from[1]."\r\n";
		}
		
		$subject ="=?UTF-8?B?".base64_encode($subject)."?=\n";
	
// 		$headers .= "Content-Type: text/plain; charset=iso-8859-1; format=flowed \n".
// 				"MIME-Version: 1.0 \n" .
// 				"Content-Transfer-Encoding: 8bit \n".
// 				"X-Mailer: PHP \n";
		
		$headers .= 'MIME-Version: 1.0' . "\r\n";
		$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";	
		
		//ini_set('sendmail_from', 'contact@traceper.com'); //Suggested by "Some Guy"
			
		return mail($to, $subject, $message, $headers);
	}	

	/**
	 * Displays the contact page
	 */
// 	public function actionContact()
// 	{
// 		$model=new ContactForm;
// 		if(isset($_POST['ContactForm']))
// 		{
// 			$model->attributes=$_POST['ContactForm'];
// 			if($model->validate())
// 			{
// 				$headers="From: {$model->email}\r\nReply-To: {$model->email}";
// 				mail(Yii::app()->params['adminEmail'],$model->subject,$model->body,$headers);
// 				Yii::app()->user->setFlash('contact','Thank you for contacting us. We will respond to you as soon as possible.');
// 				$this->refresh();
// 			}
// 		}
// 		$this->render('contact',array('model'=>$model));
// 	}
	
	public function actionContact()
	{
		$model = new ContactForm;
		
		$processOutput = true;
		// collect user input data
		if(isset($_POST['ContactForm']))
		{
			$model->attributes=$_POST['ContactForm'];
			// validate user input and if ok return json data and end application.
			if($model->validate()) {

				if(Yii::app()->user->isGuest == true)
				{
					if($this->SMTP_UTF8_mail($model->email, $model->firstName.' '.$model->lastName, 'contact@traceper.com', 'Traceper', $model->subject, $model->detail))
					{
						echo CJSON::encode(array("result"=> "1"));
					}
					else
					{
						echo CJSON::encode(array("result"=> "0"));
					}								
				}
				else
				{
					$name = null;
					$email = null;
					
					Users::model()->getUserInfo(Yii::app()->user->id, $name, $email);

					if($this->SMTP_UTF8_mail($email, $name, 'contact@traceper.com', 'Traceper', $model->subject, $model->detail))
					{
						echo CJSON::encode(array("result"=> "1"));
					}
					else
					{
						echo CJSON::encode(array("result"=> "0"));
					}					
				}

				Yii::app()->end();
			}
		}

		if (Yii::app()->request->isAjaxRequest)
		{
			if (YII_DEBUG)
			{
				Yii::app()->clientscript->scriptMap['jquery.js'] = false;
				Yii::app()->clientScript->scriptMap['jquery-ui.js'] = false;
			}
			else
			{
				Yii::app()->clientscript->scriptMap['jquery.min.js'] = false;
				Yii::app()->clientScript->scriptMap['jquery-ui.min.js'] = false;
			}
		}
		
		//Complete solution for blinking problem at FireFox
		if (Yii::app()->request->getIsAjaxRequest()) {
			Yii::app()->clientScript->scriptMap['*.js'] = false;
			Yii::app()->clientScript->scriptMap['*.css'] = false;
		}		
		
		$this->renderPartial('contact',array('model'=>$model), false, $processOutput);		
	}	

	public function actionLogin()
	{
		//Fb::warn("actionLogin() called", "SiteController");		
		
		$model = new LoginForm;
			
		$processOutput = true;
		$errorMessage = null;

		// collect user input data
		if(isset($_REQUEST['LoginForm']))
		{
			$model->attributes = $_REQUEST['LoginForm'];
			// validate user input and if ok return json data and end application.

			// 			if (Yii::app()->request->isAjaxRequest) {
			// 				$processOutput = false;
			// 			}
			
			$minDataSentInterval = Yii::app()->params->minDataSentInterval;
			$minDistanceInterval = Yii::app()->params->minDistanceInterval;	 
			$facebookId = 0; 
			$autoSend = 0;
			$latitude = 0;
			$longitude = 0;
			$locationSource = LocationSource::NoSource;
			
			$deviceId = null;
			$androidVer = null;
			$appVer = null;
			$preferredLanguage = null;

			$isRecordUpdateRequired = false;
			
			//Model dogrulamasinda gunce uygulama versiyonu kullanilmasi gerektiginden en basta mobil icin versiyonda farklilik 
			//varsa veritabanini guncelle 
			if (isset($_REQUEST['client']) && $_REQUEST['client']=='mobile')
			{	
				if(Users::model()->getAppVersion($model->email, $appVer) == true)
				{
					if (isset($_REQUEST['appVer']))
					{
						if(strcmp($appVer, $_REQUEST['appVer']) != 0)
						{
							Users::model()->setAppVersion($model->email, $_REQUEST['appVer']);
							$appVer = $_REQUEST['appVer'];
						}
					}					
				}
				else //Uygulama versiyonu okunamadi
				{
					$appVer = null;
				}
			}			

			if($model->validate()) {								
				
				$checkTermsAcception = true; //Uygulama versiyonu bir sebeple alinamazsa, varsayilan davranis sartlarin kontrol edilmesi olsun
				
				//Mobil ise uygulama versiyonuna gore terms acception kontrolu yap veya yapma
				//Web icinse her zaman kontrol et, cunku web herkes icin guncel olacak
				if (isset($_REQUEST['client']) && $_REQUEST['client']=='mobile')
				{
					if($appVer != null) //Uygulama versiyonu okunabilmisse
					{
						if($appVer > "1.0.16")
						{
							$checkTermsAcception = true;
						}
						else
						{
							$checkTermsAcception = false; //Kullanici mobil uygulamasini henuz guncellemediyse terms acception kontrolu yapma
						}									
					}
					else
					{
						$checkTermsAcception = true; //Uygulama versiyonu bir sebeple alinamazsa, varsayilan davranis sartlarin kontrol edilmesi olsun
					}					
				}

				if(($checkTermsAcception == false) || (Users::model()->isTermsAccepted($model->email) === true))
				{					
					if($model->login())
					{
						Users::model()->getLoginRequiredValues(Yii::app()->user->id, $minDataSentInterval, $minDistanceInterval, $facebookId, $autoSend, $deviceId, $androidVer, $appVer, $preferredLanguage, $latitude, $longitude, $locationSource);
						
						if (isset($_REQUEST['client']) && $_REQUEST['client']=='mobile')
						{
							if (isset($_REQUEST['deviceId']))
							{
								if(strcmp($deviceId, $_REQUEST['deviceId']) != 0)
								{
									$deviceId = $_REQUEST['deviceId'];
									$isRecordUpdateRequired = true;
								}
							}
								
							if (isset($_REQUEST['androidVer']))
							{
								if(strcmp($androidVer, $_REQUEST['androidVer']) != 0)
								{
									$androidVer = $_REQUEST['androidVer'];
									$isRecordUpdateRequired = true;
								}
							}
								
							if (isset($_REQUEST['appVer']))
							{
								if(strcmp($appVer, $_REQUEST['appVer']) != 0)
								{
									$appVer = $_REQUEST['appVer'];
									$isRecordUpdateRequired = true;
								}
							}
								
							if (isset($_REQUEST['language']))
							{
								if(strcmp($preferredLanguage, $_REQUEST['language']) != 0)
								{
									$preferredLanguage = $_REQUEST['language'];
									$isRecordUpdateRequired = true;
								}
							}
						
							if($isRecordUpdateRequired == true)
							{
								Users::model()->updateLoginSentItemsNotNull(Yii::app()->user->id, $deviceId, $androidVer, $appVer, $preferredLanguage);
							}
						
							echo CJSON::encode(array(
									"result"=> "1",
									"id"=>Yii::app()->user->id,
									"realname"=> $model->getName(),
									"minDataSentInterval"=> $minDataSentInterval,
									"minDistanceInterval"=> $minDistanceInterval,
									"facebookId"=> $facebookId,
									"autoSend"=> $autoSend
							));
						}
						else {
							$app = Yii::app();
							$language = 'tr';
								
							if (isset($app->session['_lang']))
							{
								$language = $app->session['_lang'];
					
								//echo 'Session VAR';
							}
							else
							{
								$language = substr(Yii::app()->getRequest()->getPreferredLanguage(), 0, 2);
							}
					
							if(strcmp($preferredLanguage, $language) != 0)
							{
								Users::model()->updateLoginSentItemsNotNull(Yii::app()->user->id, null, null, null, $language);
							}							
							
							//Hiç mobil veya HTML5 Geolocation konum bilgisi mevcut degilse, IP location bilgisini rough estimate olarak kullan
							//Ornegin kisi webden kayit olmus ve login oluyorsa (ve tarayicisi HTML5 geolocation desteklemiyor veya konum izni vermediyse)
							if(($locationSource == LocationSource::NoSource) || ($locationSource == LocationSource::WebIP))
							{
								if((Yii::app()->session['latitude'] != null) & (Yii::app()->session['longitude'] != null))
								{
									$address = null;
									$country = null;
										
									$this->getaddress(Yii::app()->session['latitude'], Yii::app()->session['longitude'], $address, $country);
									Users::model()->updateLocationWithAddress(Yii::app()->session['latitude'], Yii::app()->session['longitude'], 0, $address, $country, date('Y-m-d H:i:s'), LocationSource::WebIP, Yii::app()->user->id);
								}								
							}

							$profilePhotoSource = null;
							$profilePhotoStatus = Users::model()->getProfilePhotoStatus(Yii::app()->user->id);
							$profilePhotoStatusTooltipMessage = null;
							$bothPhotoExists = null;
							
							switch($profilePhotoStatus)
							{
								case Users::NO_TRACEPER_PROFILE_PHOTO_EXISTS:
									{
										if(Yii::app()->user->fb_id == 0)
										{
											$profilePhotoSource = null;
											$profilePhotoStatusTooltipMessage = Yii::t('site', 'Click here to upload your profile photo');
										}
										else
										{
											$profilePhotoSource = 'https://graph.facebook.com/'.Yii::app()->user->fb_id.'/picture?type=square';
											$profilePhotoStatusTooltipMessage = Yii::t('site', 'Click here to upload and set your profile photo. You will be able to set your profile photo as your Facebook profile photo again.');
										}
									}
									break;
										
								case Users::TRACEPER_PROFILE_PHOTO_EXISTS:
									{
										$profilePhotoSource = 'profilePhotos/'.Yii::app()->user->id.'.png?random='.time();
										$profilePhotoStatusTooltipMessage = Yii::t('site', 'Click here to change your profile photo');
							
										//Fb::warn($profilePhotoStatusTooltipMessage, "TRACEPER_PROFILE_PHOTO_EXISTS");
									}
									break;
										
								case Users::BOTH_PROFILE_PHOTOS_EXISTS_USE_FACEBOOK:
									{
										$bothPhotoExists = 'useFacebook';
										$profilePhotoSource = 'https://graph.facebook.com/'.Yii::app()->user->fb_id.'/picture?type=square';
										//$profilePhotoStatusTooltipMessage = '4';
									}
									break;
										
								case Users::BOTH_PROFILE_PHOTOS_EXISTS_USE_TRACEPER:
									{
										$bothPhotoExists = 'useTraceper';
										$profilePhotoSource = 'profilePhotos/'.Yii::app()->user->id.'.png?random='.time();
										//$profilePhotoStatusTooltipMessage = '5';
									}
									break;
							}

							$newRequestsCount = null;
							$totalRequestsCount = null;
							
							Friends::model()->getFriendRequestsInfo(Yii::app()->user->id, $newRequestsCount, $totalRequestsCount);

							if($newRequestsCount > 0)
							{
								if($newRequestsCount <= 5)
								{
									$friendReqTooltip = Yii::t('users', 'Friendship Requests').' ('.$newRequestsCount.' '.Yii::t('users', 'new').(($totalRequestsCount > $newRequestsCount)?Yii::t('users', ' totally ').$totalRequestsCount:'').Yii::t('users', ' friendship request(s) you have').')';
								}
								else
								{
									$friendReqTooltip = Yii::t('users', 'Friendship Requests').' ('.$newRequestsCount.' '.Yii::t('users', 'new').(($totalRequestsCount > $newRequestsCount)?Yii::t('users', ' totally ').$totalRequestsCount:'').Yii::t('users', ' friendship request(s) you have').')';
								}
							}
							else
							{
								$friendReqTooltip = Yii::t('users', 'Friendship Requests');
							}

							//echo 'Model NOT valid in SiteController';
							if (Yii::app()->request->isAjaxRequest)
							{
								if (YII_DEBUG)
								{
									Yii::app()->clientscript->scriptMap['jquery.js'] = false;
									Yii::app()->clientScript->scriptMap['jquery-ui.js'] = false;
								}
								else
								{
									Yii::app()->clientscript->scriptMap['jquery.min.js'] = false;
									Yii::app()->clientScript->scriptMap['jquery-ui.min.js'] = false;
								}
							}							
							
							echo CJSON::encode(array(
									"result"=> "1",
									"renderedTabView"=>$this->renderPartial('tabView',array(), true/*return instead of being displayed to end users*/, true),
									"renderedUserAreaView"=>$this->renderPartial('userAreaView',array('profilePhotoSource'=>$profilePhotoSource, 'profilePhotoStatus'=>$profilePhotoStatus, 'profilePhotoStatusTooltipMessage'=>$profilePhotoStatusTooltipMessage, 'bothPhotoExists'=>$bothPhotoExists, 'variablesDefined'=>false), true/*return instead of being displayed to end users*/, true),
									"renderedFriendshipRequestsView"=>$this->renderPartial('friendshipRequestsView',array('newRequestsCount'=>$newRequestsCount, 'friendReqTooltip'=>$friendReqTooltip), true/*return instead of being displayed to end users*/, true),
									"loginSuccessfulActions"=>$this->renderPartial('loginSuccessful',array('id'=>Yii::app()->user->id, 'realname'=>$model->getName(), 'latitude'=>$latitude, 'longitude'=>$longitude), true/*return instead of being displayed to end users*/, $processOutput),
							));							
								
							//$this->renderPartial('loginSuccessful',array('id'=>Yii::app()->user->id, 'realname'=>$model->getName()), false, $processOutput);							
						}
						
						Yii::app()->end();						
					}
					else
					{
						if (isset($_REQUEST['client']) && $_REQUEST['client']=='mobile')
						{
							echo CJSON::encode(array("result"=> "-2")); //Unknown login error
							
							$errorMessage = "Unknown login error for mobile";
							$this->sendErrorMail('mobileUnknownLoginError', 'Error in actionLogin()', $errorMessage);							
						}
						else {
							//echo 'Model NOT valid in SiteController';
						
							if (Yii::app()->request->isAjaxRequest)
							{
								if (YII_DEBUG)
								{
									Yii::app()->clientscript->scriptMap['jquery.js'] = false;
									Yii::app()->clientScript->scriptMap['jquery-ui.js'] = false;
								}
								else
								{
									Yii::app()->clientscript->scriptMap['jquery.min.js'] = false;
									Yii::app()->clientScript->scriptMap['jquery-ui.min.js'] = false;
								}
							}
								
							//Complete solution for blinking problem at FireFox
							if (Yii::app()->request->getIsAjaxRequest()) {
								Yii::app()->clientScript->scriptMap['*.js'] = false;
								Yii::app()->clientScript->scriptMap['*.css'] = false;
							}
							
							//Fb::warn("renderPartial - 1", "SiteController");
								
							$this->renderPartial('login',array('model'=>$model), false, $processOutput);
						}
						
						Yii::app()->end();						
					}	
				}
				else
				{
					if (isset($_REQUEST['client']) && $_REQUEST['client']=='mobile')
					{
						//Mobil icin termsNotAccepted gibi bir kod gonderip, oradan OK demnesi ile continueLogin cagirilmali?
						echo CJSON::encode(array("result"=> "-3")); //Terms not accepted
					}
					else
					{
						if (Yii::app()->request->isAjaxRequest)
						{
							if (YII_DEBUG)
							{
								Yii::app()->clientscript->scriptMap['jquery.js'] = false;
								Yii::app()->clientScript->scriptMap['jquery-ui.js'] = false;
							}
							else
							{
								Yii::app()->clientscript->scriptMap['jquery.min.js'] = false;
								Yii::app()->clientScript->scriptMap['jquery-ui.min.js'] = false;
							}
						}
						
						//Complete solution for blinking problem
						if (Yii::app()->request->getIsAjaxRequest()) {
							Yii::app()->clientScript->scriptMap['*.js'] = false;
							Yii::app()->clientScript->scriptMap['*.css'] = false;
						}
						
						echo CJSON::encode(array(
								"result"=> "-3",
								"renderedView"=>$this->renderPartial('acceptTermsForLogin',array('form'=>$_REQUEST['LoginForm']), true/*return instead of being displayed to end users*/, true),
								"loginView"=>$this->renderPartial('login',array('model'=>$model), true/*return instead of being displayed to end users*/, $processOutput),
						));
						
						//Fb::warn("result:-3", "SiteController");
							
						//$this->renderPartial('acceptTermsForLogin',array('form'=>$_REQUEST['LoginForm']), false, true);						
					}										
				}											
			}
			else
			{
				//echo 'model NOT valid';
				
				//Fb::warn("model NOT valid", "actionLogin()");

				if (isset($_REQUEST['client']) && $_REQUEST['client']=='mobile')
				{
					$result = "1"; //Initialize with "1" to be used whether no error occured
					
					if($model->getError('password') == Yii::t('site', 'Incorrect password or e-mail'))
					{
						$result = "0";
					}
					else if($model->getError('email') == Yii::t('site', 'Activate your account first'))
					{
						$result = "-1";
					}
					else if($model->getError('email') == Yii::t('site', 'You are registered as Facebook user for our service. Please use \"Log in with facebook\" button to log in to your Traceper account.'))
					{
						$result = "-4";
					}
					else
					{
						$result = "-2"; //Unknown login error
						
						$errorMessage = "Unknown login validation error for mobile";
						$this->sendErrorMail('mobileUnknownLoginValidationError', 'Error in actionLogin()', $errorMessage);						
					}					

					echo CJSON::encode(array("result"=> $result));
				}
				else {
					//echo 'Model NOT valid in SiteController';

					if (Yii::app()->request->isAjaxRequest)
					{
						if (YII_DEBUG)
						{
							Yii::app()->clientscript->scriptMap['jquery.js'] = false;
							Yii::app()->clientScript->scriptMap['jquery-ui.js'] = false;
						}
						else
						{
							Yii::app()->clientscript->scriptMap['jquery.min.js'] = false;
							Yii::app()->clientScript->scriptMap['jquery-ui.min.js'] = false;
						}
					}
					
					//Complete solution for blinking problem at FireFox
					if (Yii::app()->request->getIsAjaxRequest()) {
						Yii::app()->clientScript->scriptMap['*.js'] = false;
						Yii::app()->clientScript->scriptMap['*.css'] = false;
					}

					//Fb::warn("renderPartial - 2", "SiteController");
					
					$this->renderPartial('login',array('model'=>$model), false, $processOutput);
				}

				Yii::app()->end();
			}
		}
		else
		{
			//Fb::warn("renderPartial - 3", "SiteController");
			
			//echo 'LoginForm NOT set';
			$this->renderPartial('login',array('model'=>$model), false, $processOutput);
		}
	}
	
	//Kullanim sartlari degisiminden once kaydolmus biri login olmaya calistiginda sartları kabul ederse bu fonksiyon ile isleme devam eder
	public function actionContinueLogin() {
		// collect user input data
		//if(isset($_REQUEST['LoginForm']) && $_REQUEST['LoginForm'] != NULL)
		if(isset($_REQUEST['LoginForm']['email']) && isset($_REQUEST['LoginForm']['password']))
		{
			$language/*Bunu set edecek*/ = null;
			$app = Yii::app();
			
			if (isset($app->session['_lang']))
			{
				$language = $app->session['_lang'];
			}
			else
			{
				$language = substr(Yii::app()->getRequest()->getPreferredLanguage(), 0, 2);
			}
			
			if (isset($_REQUEST['client']) && $_REQUEST['client']=='mobile')
			{
				if (isset($_REQUEST['language']) && isset($_REQUEST['deviceId']) && isset($_REQUEST['androidVer']) && isset($_REQUEST['appVer']))
				{
					$language = $_REQUEST['language'];
					//Diger atamalar altta (commonLogin) yapiliyor
				}
				else
				{
					echo CJSON::encode(array("result"=> "-2")); //Missing parameter
					
					$errorMessage = "Missing parameter";
					$this->sendErrorMail('mobileContinueLoginMissingParameter', 'MOBILE Error in actionContinueLogin()', $errorMessage);					
					Yii::app()->end();
				}
			}		
							
			Users::model()->setTermsAccepted($_REQUEST['LoginForm']['email']);			
			$this->commonLogin($_REQUEST['LoginForm']['email'], $_REQUEST['LoginForm']['password'], $language, false/*$directLogin*/);
		}
		else
		{
			//Fb::warn("email and password NOT SET", "actionContinueLogin()");
			
			echo CJSON::encode(array("result"=> "-2")); //Missing parameter
			
			$errorMessage = "Missing parameter";
			$this->sendErrorMail('continueLoginMissingParameter', 'Error in actionContinueLogin()', $errorMessage);			
			Yii::app()->end();			
		}						
	}

	//Hem web hem de mobil tarafindan kullaniliyor
	//Mobilden gonderilecek parametreler: email, fbId, name(Full Name), password, language, deviceId, androidVer, appVer
	public function actionFacebookLogin()
	{
		//Fb::warn("actionFacebookLogin() called", "SiteController");
		
		$fbId = $fbEmail = $name = $password = $autoGeneratedPassword = $preferredLanguage/*Bunu DB'den alacak*/ = $language/*Bunu set edecek*/ = null;
		$app = Yii::app();
		$errorMessage = null;
		
		if (isset($app->session['_lang']))
		{
			$language = $app->session['_lang'];
		}
		else
		{
			$language = substr(Yii::app()->getRequest()->getPreferredLanguage(), 0, 2);
		}
		
		if (isset($_REQUEST['client']) && $_REQUEST['client']=='mobile')
		{
			if (isset($_REQUEST['email']) && isset($_REQUEST['fbId']) && isset($_REQUEST['name']) && isset($_REQUEST['password']) &&
					isset($_REQUEST['language']) && isset($_REQUEST['deviceId']) && isset($_REQUEST['androidVer']) && isset($_REQUEST['appVer']))
			{
				$fbId = $_REQUEST['fbId'];
				$fbEmail = $_REQUEST['email'];
				$name = $_REQUEST['name'];
				$password = $_REQUEST['password'];
				$language = $_REQUEST['language'];
				$autoGeneratedPassword = 'AK'.substr($fbEmail, 0, 3).substr($fbId, -5).'YS';
		
				if($password != $autoGeneratedPassword)
				{
					echo CJSON::encode(array("result"=> "-3")); //Mobile-generated password and web-generated password do not match
					
					$errorMessage = "Mobile-generated password($password) and web-generated password($autoGeneratedPassword) do not match!".'<br/><br/>';
					$errorMessage .= "fbEmail: ".$fbEmail.'<br/>';
					$errorMessage .= "fbId: ".$fbId.'<br/>';
					
					$this->sendErrorMail('mobileFacebookLoginPasswordMismatch', 'MOBILE Error in actionFacebookLogin()', $errorMessage);
					Yii::app()->end();
				}
		
				//Diger atamalar altta yapiliyor
			}
			else
			{
				echo CJSON::encode(array("result"=> "-2")); //Missing parameter
				
				$errorMessage = "Missing parameter!";
				$this->sendErrorMail('mobileFacebookLoginMissingParameter', 'MOBILE Error in actionFacebookLogin()', $errorMessage);				
				Yii::app()->end();
			}
		}
		else //Web
		{
			$userinfo = Yii::app()->facebook->getInfo();
				
			$fbId = $userinfo["id"];
			$fbEmail = $userinfo["email"];
			$name = $userinfo["name"];
			$autoGeneratedPassword = 'AK'.substr($fbEmail, 0, 3).substr($fbId, -5).'YS';
			
			//Fb::warn("fbId: $fbId", "actionFacebookLogin()");
			//Fb::warn("fbEmail: $fbEmail", "actionFacebookLogin()");
			//Fb::warn("name: $name", "actionFacebookLogin()");
			//Fb::warn("autoGeneratedPassword: $autoGeneratedPassword", "actionFacebookLogin()");
		}		

		//Kullanici bu e-posta ile daha once normal kayit yaptiysa, ayri bir diyalog cikarilarak devam ederse artik hep facebook
		//uzeriden girmesi gerekecegi belirtilmeli
		if(Users::model()->isUserRegisteredAsTraceperUser($fbEmail) == true)
		{
			//Fb::warn("User is registered as Traceper user", "actionFacebookLogin()");
			
			//Traceper kullanicisi ayni e-posta ile Facebook logini denerken hic sartlari kabul etmis mi?
			if(Users::model()->isTermsAccepted($fbEmail) === true)
			{
				if (isset($_REQUEST['client']) && $_REQUEST['client']=='mobile')
				{
					echo CJSON::encode(array("result"=> "-4")); //Currently Traceper user and ask switch to Facebook login permanently
				}
				else
				{
					if (Yii::app()->request->isAjaxRequest)
					{
						if (YII_DEBUG)
						{
							Yii::app()->clientscript->scriptMap['jquery.js'] = false;
							Yii::app()->clientScript->scriptMap['jquery-ui.js'] = false;
						}
						else
						{
							Yii::app()->clientscript->scriptMap['jquery.min.js'] = false;
							Yii::app()->clientScript->scriptMap['jquery-ui.min.js'] = false;
						}
					}
						
					//Complete solution for blinking problem
					if (Yii::app()->request->getIsAjaxRequest()) {
						Yii::app()->clientScript->scriptMap['*.js'] = false;
						Yii::app()->clientScript->scriptMap['*.css'] = false;
					}
				
					echo CJSON::encode(array(
							"result"=> "-4", //Currently Traceper user and ask switch to Facebook login permanently
							"renderedView"=>$this->renderPartial('askForSwitchToFacebookLoginPermanently',array(), true/*return instead of being displayed to end users*/, true),
					));
				
					Yii::app()->end();
				}				
			}
			else
			{				
				if (isset($_REQUEST['client']) && $_REQUEST['client']=='mobile')
				{
					echo CJSON::encode(array("result"=> "-1")); //Needs to accept terms
				}
				else
				{
					if (Yii::app()->request->isAjaxRequest)
					{
						if (YII_DEBUG)
						{
							Yii::app()->clientscript->scriptMap['jquery.js'] = false;
							Yii::app()->clientScript->scriptMap['jquery-ui.js'] = false;
						}
						else
						{
							Yii::app()->clientscript->scriptMap['jquery.min.js'] = false;
							Yii::app()->clientScript->scriptMap['jquery-ui.min.js'] = false;
						}
					}
						
					//Complete solution for blinking problem
					if (Yii::app()->request->getIsAjaxRequest()) {
						Yii::app()->clientScript->scriptMap['*.js'] = false;
						Yii::app()->clientScript->scriptMap['*.css'] = false;
					}
						
					//Fb::warn("acceptTermsForFacebookLogin oncesi", "actionFacebookLogin()");
						
					echo CJSON::encode(array(
							"result"=> "-1", //Needs to accept terms
							"renderedView"=>$this->renderPartial('acceptTermsForFacebookLogin',array(), true/*return instead of being displayed to end users*/, true),
					));
				}
				
				Yii::app()->end();				
			}				
		}
		else if((Users::model()->isFacebookUserRegistered($fbEmail, $fbId) == false) || (Users::model()->isTermsAccepted($fbEmail) == false))
		{			
			//Facebook kullanisi kayitli degilse once kayıt edilmeli, facebook kullanicis fakat sartlari henuz kabul etmemisse
			//once sartlari kabul etmeli (her iki durumda da acceptTermsForFacebookLogin render ediliyor)
			
			if (isset($_REQUEST['client']) && $_REQUEST['client']=='mobile')
			{
				echo CJSON::encode(array("result"=> "-1")); //Needs to be registered (so accept terms)				
			}
			else
			{
				if (Yii::app()->request->isAjaxRequest)
				{
					if (YII_DEBUG)
					{
						Yii::app()->clientscript->scriptMap['jquery.js'] = false;
						Yii::app()->clientScript->scriptMap['jquery-ui.js'] = false;
					}
					else
					{
						Yii::app()->clientscript->scriptMap['jquery.min.js'] = false;
						Yii::app()->clientScript->scriptMap['jquery-ui.min.js'] = false;
					}
				}
					
				//Complete solution for blinking problem
				if (Yii::app()->request->getIsAjaxRequest()) {
					Yii::app()->clientScript->scriptMap['*.js'] = false;
					Yii::app()->clientScript->scriptMap['*.css'] = false;
				}
					
				//Fb::warn("acceptTermsForFacebookLogin oncesi", "actionFacebookLogin()");
					
				echo CJSON::encode(array(
						"result"=> "-1", //Needs to be registered (so accept terms)
						"renderedView"=>$this->renderPartial('acceptTermsForFacebookLogin',array(), true/*return instead of being displayed to end users*/, true),
				));				
			}

			Yii::app()->end();
		}
		
		//Eski bir facebook kaydolcu kullanici zaten ilk kogin olusunda oncelikle sartlari kabul etmeye zorlanacagindan
		//asagida kismin mobil kismi aslinda calismis olacak, fakat web kismi kisi uygulamayi guncellemedigi surece
		//defalarca burada calisabilir.
		
		$appVer = $deviceId = null;
		$directLogin = false;
		Users::model()->getFbUserMobileInfo($fbEmail, $appVer, $deviceId);	
		
		if (isset($_REQUEST['client']) && $_REQUEST['client']=='mobile')
		{
			//1.0.16 veya alti bir versiyonda facebook kaydol ile kaydolmus biri yeni uygulamayla ilk login oldugunda
			//sifresini uygulamanin gonderdigi auto-generated sifre ile guncelleyip direk login oldur
			//if((($appVer != null) && ($appVer <= "1.0.16")) && (isset($_REQUEST['appVer']) && ($_REQUEST['appVer'] > "1.0.16")) && (isset($_REQUEST['deviceId']) && ($deviceId == $_REQUEST['deviceId'])))
			if(($appVer <= "1.0.16") && (isset($_REQUEST['appVer']) && ($_REQUEST['appVer'] > "1.0.16")) && (isset($_REQUEST['deviceId']) && ($deviceId == $_REQUEST['deviceId'])))				
			{
				Users::model()->updatePassword($fbEmail, md5($autoGeneratedPassword));
				$directLogin = true;
			}
			else
			{
				$directLogin = false;
			}
		}
		else //Webden login olan facebook kullanicisi ise, web zaten guncel oldugu icin sadece mevcut uygulama versiyonunu kontrol et
		{
			//1.0.16 veya alti bir versiyonda facebook kaydol ile kaydolmus biri yeni weble login olursa
			//uygulama versiyonunu guncelleyinceye kadar ayrica authenticate etmeden login et. Sifresini guncelleme, cunku
			//uygulamayi guncellemezse uygulamadan girememeye baslar
			//if(($appVer != null) && ($appVer <= "1.0.16"))
			if($appVer <= "1.0.16")
			{
				//Kullanici uygulamayi guncelleyinceye kadar webden facebook uzerinden de girmeye calissa guvenlik icin hala sifre girmey zorlansin
				
				//Fb::warn("appVer:$appVer <= 1.0.16", "actionFacebookLogin()");
				
				if (Yii::app()->request->isAjaxRequest)
				{
					if (YII_DEBUG)
					{
						Yii::app()->clientscript->scriptMap['jquery.js'] = false;
						Yii::app()->clientScript->scriptMap['jquery-ui.js'] = false;
					}
					else
					{
						Yii::app()->clientscript->scriptMap['jquery.min.js'] = false;
						Yii::app()->clientScript->scriptMap['jquery-ui.min.js'] = false;
					}
				}
					
				//Complete solution for blinking problem
				if (Yii::app()->request->getIsAjaxRequest()) {
					Yii::app()->clientScript->scriptMap['*.js'] = false;
					Yii::app()->clientScript->scriptMap['*.css'] = false;
				}
					
				$model = new LoginForm(true/*$forOnlyPasswordCheck*/);
				$model->email = $fbEmail;				
					
				echo CJSON::encode(array(
						"result"=> "-2", //Old Facebook user with app<=1.0.16 has to enter his/her Traceper password
						"renderedView"=>$this->renderPartial('enterPasswordForOldFacebookUserToLogin', array('model'=>$model), true/*return instead of being displayed to end users*/, true),
				));

				Yii::app()->end();
			}
			else
			{				
				$directLogin = false;
			}
		}		
		
		$this->commonLogin($fbEmail, $autoGeneratedPassword, $language, $directLogin);
	}
		
	//Kullanici Facebook ile ilk kez login olmaya calistiginda (yani kayit oldugunda) sartları kabul ederse bu fonksiyon ile isleme devam eder
	//Hem web hem de mobil tarafindan kullaniliyor
	//Mobilden gonderilecek parametreler: email, fbId, name(Full Name), password, language, deviceId, androidVer, appVer
	public function actionContinueFacebookLogin() 
	{
		$fbId = $fbEmail = $name = $password = $autoGeneratedPassword = $registrationMedium = $language/*Bunu set edecek*/ = null;		
		$app = Yii::app();

		if (isset($app->session['_lang']))
		{
			$language = $app->session['_lang'];
		}
		else
		{
			$language = substr(Yii::app()->getRequest()->getPreferredLanguage(), 0, 2);
		}		
	
		if (isset($_REQUEST['client']) && $_REQUEST['client']=='mobile')
		{
			if (isset($_REQUEST['email']) && isset($_REQUEST['fbId']) && isset($_REQUEST['name']) && isset($_REQUEST['password']) && 
				isset($_REQUEST['language']) && isset($_REQUEST['deviceId']) && isset($_REQUEST['androidVer']) && isset($_REQUEST['appVer']))
			{
				$fbId = $_REQUEST['fbId'];
				$fbEmail = $_REQUEST['email'];
				$name = $_REQUEST['name'];
				$password = $_REQUEST['password'];
				$language = $_REQUEST['language'];
				$autoGeneratedPassword = 'AK'.substr($fbEmail, 0, 3).substr($fbId, -5).'YS';
				
				if($password != $autoGeneratedPassword)
				{
					echo CJSON::encode(array("result"=> "-3")); //Mobile-generated password and web-generated password do not match
					
					$errorMessage = "Mobile-generated password($password) and web-generated password($autoGeneratedPassword) do not match!".'<br/><br/>';
					$errorMessage .= "fbEmail: ".$fbEmail.'<br/>';
					$errorMessage .= "fbId: ".$fbId.'<br/>';					
					
					$this->sendErrorMail('mobileContinueFacebookLoginPasswordMismatch', 'MOBILE Error in actionContinueFacebookLogin()', $errorMessage);					
					Yii::app()->end();					
				}
				
				//Diger atamalar altta yapiliyor
			}
			else
			{
				echo CJSON::encode(array("result"=> "-2")); //Missing parameter
				
				$errorMessage = "Missing parameter!";
				$this->sendErrorMail('mobileContinueFacebookLoginMissingParameter', 'MOBILE Error in actionContinueFacebookLogin()', $errorMessage);				
				Yii::app()->end();
			}

			$registrationMedium = 'Mobile';
		}
		else
		{
			$userinfo = Yii::app()->facebook->getInfo();
			
			$fbId = $userinfo["id"];
			$fbEmail = $userinfo["email"];
			$name = $userinfo["name"];
			$autoGeneratedPassword = 'AK'.substr($fbEmail, 0, 3).substr($fbId, -5).'YS';
			
			$registrationMedium = 'Web';
		}
		
		$registrationMedium = $registrationMedium.' FB';		
		
// 		Fb::warn("fbId: $fbId", "actionContinueFacebookLogin()");
// 		Fb::warn("fbEmail: $fbEmail", "actionContinueFacebookLogin()");
// 		Fb::warn("name: $name", "actionContinueFacebookLogin()");		

		//Traceper kullanicisi ayni e-posta ile Facebook logini kullanmak istediginde sarlari kabul etmemise, 
		//once kabul edip sonra fonksiyna geri donmusse 
		if(Users::model()->isUserRegisteredAsTraceperUser($fbEmail) == true)
		{
			Users::model()->setTermsAccepted($fbEmail);
			
			if (isset($_REQUEST['client']) && $_REQUEST['client']=='mobile')
			{
				echo CJSON::encode(array("result"=> "-4")); //Currently Traceper user and ask switch to Facebook login permanently
			}
			else
			{
				if (Yii::app()->request->isAjaxRequest)
				{
					if (YII_DEBUG)
					{
						Yii::app()->clientscript->scriptMap['jquery.js'] = false;
						Yii::app()->clientScript->scriptMap['jquery-ui.js'] = false;
					}
					else
					{
						Yii::app()->clientscript->scriptMap['jquery.min.js'] = false;
						Yii::app()->clientScript->scriptMap['jquery-ui.min.js'] = false;
					}
				}
			
				//Complete solution for blinking problem
				if (Yii::app()->request->getIsAjaxRequest()) {
					Yii::app()->clientScript->scriptMap['*.js'] = false;
					Yii::app()->clientScript->scriptMap['*.css'] = false;
				}
			
				echo CJSON::encode(array(
						"result"=> "-4", //Currently Traceper user and ask switch to Facebook login permanently
						"renderedView"=>$this->renderPartial('askForSwitchToFacebookLoginPermanently',array(), true/*return instead of being displayed to end users*/, true),
				));
			
				Yii::app()->end();
			}			
		}
		else if(Users::model()->isFacebookUserRegistered($fbEmail, $fbId) == true)
		{
			//Facebook kullanicisi zaten kayitli ise sartlari kabulden buraya geldigi icin sartlari kabul ettir
			Users::model()->setTermsAccepted($fbEmail);
			
			$appVer = $deviceId = null;
			$directLogin = false;
			Users::model()->getFbUserMobileInfo($fbEmail, $appVer, $deviceId);	
			
			if (isset($_REQUEST['client']) && $_REQUEST['client']=='mobile')
			{
				//1.0.16 veya alti bir versiyonda facebook kaydol ile kaydolmus biri yeni uygulamayla ilk login oldugunda
				//sifresini uygulamanin gonderdigi auto-generated sifre ile guncelleyip direk login oldur
				//if((($appVer != null) && ($appVer <= "1.0.16")) && (isset($_REQUEST['appVer']) && ($_REQUEST['appVer'] > "1.0.16")) && (isset($_REQUEST['deviceId']) && ($deviceId == $_REQUEST['deviceId'])))
				
				//Eski uygulama telefonunda yuklu iken yeni telefon alip tam da yeni uygulamayi kuran biri icin deviceId kontrolu sorun
				//olusturacagi icin kaldirildi
				//if((($appVer != null) && ($appVer <= "1.0.16")) && (isset($_REQUEST['appVer']) && ($_REQUEST['appVer'] > "1.0.16"))) 
				if(($appVer <= "1.0.16") && (isset($_REQUEST['appVer']) && ($_REQUEST['appVer'] > "1.0.16")))
				{
					Users::model()->updatePassword($fbEmail, md5($autoGeneratedPassword));
					$directLogin = true;
				}
				else
				{
					$directLogin = false;
				}
			}
			else //Webden login olan facebook kullanicisi ise, web zaten guncel oldugu icin sadece mevcut uygulama versiyonunu kontrol et
			{
				//1.0.16 veya alti bir versiyonda facebook kaydol ile kaydolmus biri yeni weble login olursa
				//uygulama versiyonunu guncelleyinceye kadar ayrica authenticate etmeden login et. Sifresini guncelleme, cunku
				//uygulamayi guncellemezse uygulamadan girememeye baslar
				//if(($appVer != null) && ($appVer <= "1.0.16"))
				if($appVer <= "1.0.16")
				{
					//Kullanici uygulamayi guncelleyinceye kadar webden facebook uzerinden de girmeye calissa guvenlik icin hala sifre girmey zorlansin
					
					//Fb::warn("appVer:$appVer <= 1.0.16", "actionFacebookLogin()");
					
					if (Yii::app()->request->isAjaxRequest)
					{
						if (YII_DEBUG)
						{
							Yii::app()->clientscript->scriptMap['jquery.js'] = false;
							Yii::app()->clientScript->scriptMap['jquery-ui.js'] = false;
						}
						else
						{
							Yii::app()->clientscript->scriptMap['jquery.min.js'] = false;
							Yii::app()->clientScript->scriptMap['jquery-ui.min.js'] = false;
						}
					}
						
					//Complete solution for blinking problem
					if (Yii::app()->request->getIsAjaxRequest()) {
						Yii::app()->clientScript->scriptMap['*.js'] = false;
						Yii::app()->clientScript->scriptMap['*.css'] = false;
					}
						
					$model = new LoginForm(true/*$forOnlyPasswordCheck*/);
					$model->email = $fbEmail;				
						
					echo CJSON::encode(array(
							"result"=> "-2", //Old Facebook user with app<=1.0.16 has to enter his/her Traceper password
							"renderedView"=>$this->renderPartial('enterPasswordForOldFacebookUserToLogin', array('model'=>$model), true/*return instead of being displayed to end users*/, true),
					));
	
					Yii::app()->end();
				}
				else
				{				
					$directLogin = false;
				}
			}		
			
			$this->commonLogin($fbEmail, $autoGeneratedPassword, $language, $directLogin);
		}
		else if (Users::model()->saveFacebookUser($fbEmail, md5($autoGeneratedPassword), $name, $fbId, 1/*account_type*/, $registrationMedium, $language)) 
		{
			Fb::warn("saveFacebookUser() successful", "actionContinueFacebookLogin()");			
			Users::model()->setTermsAccepted($fbEmail);
			
			//Kullanici zaten simdi kaydedildigi icin direk login olabilir, ek kontrole gerek yok
			$this->commonLogin($fbEmail, $autoGeneratedPassword, $language, true/*$directLogin*/);
		}
		else {
			echo CJSON::encode(array("result"=> "-1")); //User could not be registered
			
			$errorMessage = "saveFacebookUser() failed!".'<br/><br/>';
			$errorMessage .= "fbId: ".$fbId.'<br/>';
			$errorMessage .= "fbEmail: ".$fbEmail.'<br/>';
			$errorMessage .= "autoGeneratedPassword: ".$autoGeneratedPassword.'<br/>';			
			$errorMessage .= "name: ".$name.'<br/>';
			$errorMessage .= "registrationMedium: ".$registrationMedium.'<br/>';
			$this->sendErrorMail('continueFacebookLoginUserCouldNotBeRegistered', 'Error in actionContinueFacebookLogin()', $errorMessage);			
			Yii::app()->end();
		}
	}
		
	//Bu fonksiyon sadece web tarafindan kullaniliyor
	//Henuz uygulamasini guncellememis eski bir Facebook kaydolcu kullanici web Facebook loginden giris yapmaya calisirsa
	//guvenlik acisindan eski sifresini girmesi isteniyor
	public function actionOldFacebookUserLogin()
	{
		//Fb::warn("actionOldFacebookUserLogin() called", "SiteController");
		
		$userinfo = Yii::app()->facebook->getInfo();
		$fbEmail = $userinfo["email"];

		$language = null;
		$app = Yii::app();
		
		if (isset($app->session['_lang']))
		{
			$language = $app->session['_lang'];
		}
		else
		{
			$language = substr(Yii::app()->getRequest()->getPreferredLanguage(), 0, 2);
		}		
		
		$model = new LoginForm(true/*$forOnlyPasswordCheck*/);
		$model->email = $fbEmail;

		// collect user input data
		//if(isset($_REQUEST['LoginForm[password]']))
		if(isset($_POST['LoginForm']))
		{
			//Fb::warn("LoginForm SET", "actionOldFacebookUserLogin()");
			
			//$model->password = $_REQUEST['LoginForm[password]'];
			$model->attributes = $_POST['LoginForm'];
			
			if($model->validate()) {
				$this->commonLogin($fbEmail, $model->password, $language, false/*$directLogin*/);
			}
			else
			{
				if (Yii::app()->request->isAjaxRequest)
				{
					if (YII_DEBUG)
					{
						Yii::app()->clientscript->scriptMap['jquery.js'] = false;
						Yii::app()->clientScript->scriptMap['jquery-ui.js'] = false;
					}
					else
					{
						Yii::app()->clientscript->scriptMap['jquery.min.js'] = false;
						Yii::app()->clientScript->scriptMap['jquery-ui.min.js'] = false;
					}
				}
				
				//Complete solution for blinking problem
				if (Yii::app()->request->getIsAjaxRequest()) {
					Yii::app()->clientScript->scriptMap['*.js'] = false;
					Yii::app()->clientScript->scriptMap['*.css'] = false;
				}				
				
				$this->renderPartial('enterPasswordForOldFacebookUserToLogin',array('model'=>$model), false, true);
			}			
		}
		else
		{
			Fb::warn("LoginForm NOT SET!", "actionOldFacebookUserLogin()");

			if (Yii::app()->request->isAjaxRequest)
			{
				if (YII_DEBUG)
				{
					Yii::app()->clientscript->scriptMap['jquery.js'] = false;
					Yii::app()->clientScript->scriptMap['jquery-ui.js'] = false;
				}
				else
				{
					Yii::app()->clientscript->scriptMap['jquery.min.js'] = false;
					Yii::app()->clientScript->scriptMap['jquery-ui.min.js'] = false;
				}
			}
			
			//Complete solution for blinking problem
			if (Yii::app()->request->getIsAjaxRequest()) {
				Yii::app()->clientScript->scriptMap['*.js'] = false;
				Yii::app()->clientScript->scriptMap['*.css'] = false;
			}			
			
			$this->renderPartial('enterPasswordForOldFacebookUserToLogin',array('model'=>$model), false, true);
		}	
	}

	//Hem web hem de mobil tarafindan kullaniliyor
	//Mobilden gonderilecek parametreler: email, fbId, name(Full Name), password, language, deviceId, androidVer, appVer
	public function actionSwitchToFacebookLoginPermanently()
	{
		$fbId = $fbEmail = $name = $password = $autoGeneratedPassword = $language/*Bunu set edecek*/ = null;
		$app = Yii::app();
		$errorMessage = null;
	
		if (isset($app->session['_lang']))
		{
			$language = $app->session['_lang'];
		}
		else
		{
			$language = substr(Yii::app()->getRequest()->getPreferredLanguage(), 0, 2);
		}
	
		if (isset($_REQUEST['client']) && $_REQUEST['client']=='mobile')
		{
			if (isset($_REQUEST['email']) && isset($_REQUEST['fbId']) && isset($_REQUEST['name']) && isset($_REQUEST['password']) &&
					isset($_REQUEST['language']) && isset($_REQUEST['deviceId']) && isset($_REQUEST['androidVer']) && isset($_REQUEST['appVer']))
			{
				$fbId = $_REQUEST['fbId'];
				$fbEmail = $_REQUEST['email'];
				$name = $_REQUEST['name'];
				$password = $_REQUEST['password'];
				$language = $_REQUEST['language'];
				$autoGeneratedPassword = 'AK'.substr($fbEmail, 0, 3).substr($fbId, -5).'YS';
	
				if($password != $autoGeneratedPassword)
				{
					echo CJSON::encode(array("result"=> "-3")); //Mobile-generated password and web-generated password do not match

					$errorMessage = "Mobile-generated password($password) and web-generated password($autoGeneratedPassword) do not match!".'<br/><br/>';
					$errorMessage .= "fbEmail: ".$fbEmail.'<br/>';
					$errorMessage .= "fbId: ".$fbId.'<br/>';					
					
					$this->sendErrorMail('mobileSwitchToFacebookPasswordMismatch', 'MOBILE Error in actionSwitchToFacebookLoginPermanently()', $errorMessage);					
					Yii::app()->end();
				}
	
				//Diger atamalar altta (commonLogin) yapiliyor
			}
			else
			{
				echo CJSON::encode(array("result"=> "-2")); //Missing parameter
				
				$errorMessage = "Missing parameter!";
				$this->sendErrorMail('mobileSwitchToFacebookMissingParameter', 'MOBILE Error in actionSwitchToFacebookLoginPermanently()', $errorMessage);				
				Yii::app()->end();
			}
		}
		else
		{
			$userinfo = Yii::app()->facebook->getInfo();
				
			$fbId = $userinfo["id"];
			$fbEmail = $userinfo["email"];
			$name = $userinfo["name"];
			$autoGeneratedPassword = 'AK'.substr($fbEmail, 0, 3).substr($fbId, -5).'YS';
		}

// 		Fb::warn("fbId: $fbId", "actionSwitchToFacebookLoginPermanently()");
// 		Fb::warn("fbEmail: $fbEmail", "actionSwitchToFacebookLoginPermanently()");
// 		Fb::warn("name: $name", "actionSwitchToFacebookLoginPermanently()");
		
		if (Users::model()->updateTraceperUserAsFacebookUser($fbEmail, md5($autoGeneratedPassword), $name, $fbId, $language))
		{
			//Fb::warn("updateTraceperUserAsFacebookUser() successful", "actionSwitchToFacebookLoginPermanently()");
				
			//Kullanici bilgisi zaten simdi guncellendiginden ayri bir login kontrolune gerek yok, direk login olabilir
			$this->commonLogin($fbEmail, $autoGeneratedPassword, $language, true/*$directLogin*/);
		}
		else {
			echo CJSON::encode(array("result"=> "-1")); //User info cannot be updated as Facebook user
			
			$errorMessage = "User info cannot be updated as Facebook user!";
			$this->sendErrorMail('mobileSwitchToFacebookInfoCannotBeUpdated', 'Error in actionSwitchToFacebookLoginPermanently()', $errorMessage);			
			Yii::app()->end();
		}
	}	
	
	private function commonLogin($email, $password, $language, $directLogin = false)
	{
		$model = new LoginForm;			
		$model->email = $email;
		$model->password = $password;
			
		$minDataSentInterval = Yii::app()->params->minDataSentInterval;
		$minDistanceInterval = Yii::app()->params->minDistanceInterval;
		$facebookId = $autoSend = $latitude = $longitude = 0;
		$locationSource = LocationSource::NoSource;
		$deviceId = $androidVer = $appVer = $preferredLanguage/*Bunu DB'den alacak*/ = null;
		
		$isRecordUpdateRequired = $loginSuccessful = false;
		
		if($directLogin)
		{
			$loginSuccessful = $model->directLogin();
		}
		else
		{
			$loginSuccessful = $model->login();
		}	

		if($loginSuccessful)
		{
			//Fb::warn("directLogin() successful", "actionContinueFacebookLogin()");
		
			Users::model()->getLoginRequiredValues(Yii::app()->user->id, $minDataSentInterval, $minDistanceInterval, $facebookId, $autoSend, $deviceId, $androidVer, $appVer, $preferredLanguage, $latitude, $longitude, $locationSource);
				
			if (isset($_REQUEST['client']) && $_REQUEST['client']=='mobile')
			{
				if (isset($_REQUEST['deviceId']))
				{
					if(strcmp($deviceId, $_REQUEST['deviceId']) != 0)
					{
						$deviceId = $_REQUEST['deviceId'];
						$isRecordUpdateRequired = true;
					}
				}
					
				if (isset($_REQUEST['androidVer']))
				{
					if(strcmp($androidVer, $_REQUEST['androidVer']) != 0)
					{
						$androidVer = $_REQUEST['androidVer'];
						$isRecordUpdateRequired = true;
					}
				}
					
				if (isset($_REQUEST['appVer']))
				{
					if(strcmp($appVer, $_REQUEST['appVer']) != 0)
					{
						$appVer = $_REQUEST['appVer'];
						$isRecordUpdateRequired = true;
					}
				}

				if(strcmp($preferredLanguage, $language) != 0)
				{
					$preferredLanguage = $language;
					$isRecordUpdateRequired = true;
				}
					
				if($isRecordUpdateRequired == true)
				{
					Users::model()->updateLoginSentItemsNotNull(Yii::app()->user->id, $deviceId, $androidVer, $appVer, $preferredLanguage);
				}
					
				echo CJSON::encode(array(
						"result"=> "1", //(Facebook register and )login successful
						"id"=>Yii::app()->user->id,
						"realname"=> $model->getName(),
						"minDataSentInterval"=> $minDataSentInterval,
						"minDistanceInterval"=> $minDistanceInterval,
						"facebookId"=> $facebookId,
						"autoSend"=> $autoSend
				));
				Yii::app()->end();
			}
			else {		
				if(strcmp($preferredLanguage, $language) != 0)
				{
					Users::model()->updateLoginSentItemsNotNull(Yii::app()->user->id, null, null, null, $language);
				}
		
				//Hiç mobil veya HTML5 Geolocation konum bilgisi mevcut degilse, IP location bilgisini rough estimate olarak kullan
				//Ornegin kisi webden kayit olmus ve login oluyorsa (ve tarayicisi HTML5 geolocation desteklemiyor veya konum izni vermediyse)
				if(($locationSource == LocationSource::NoSource) || ($locationSource == LocationSource::WebIP))
				{
					if((Yii::app()->session['latitude'] != null) & (Yii::app()->session['longitude'] != null))
					{
						$address = null;
						$country = null;
						
						$this->getaddress(Yii::app()->session['latitude'], Yii::app()->session['longitude'], $address, $country);
						Users::model()->updateLocationWithAddress(Yii::app()->session['latitude'], Yii::app()->session['longitude'], 0, $address, $country, date('Y-m-d H:i:s'), LocationSource::WebIP, Yii::app()->user->id);
					}
				}
					
				$profilePhotoSource = null;
				$profilePhotoStatus = Users::model()->getProfilePhotoStatus(Yii::app()->user->id);
				$profilePhotoStatusTooltipMessage = null;
				$bothPhotoExists = null;
		
				switch($profilePhotoStatus)
				{
					case Users::NO_TRACEPER_PROFILE_PHOTO_EXISTS:
						{
							if(Yii::app()->user->fb_id == 0)
							{
								$profilePhotoSource = null;
								$profilePhotoStatusTooltipMessage = Yii::t('site', 'Click here to upload your profile photo');
							}
							else
							{
								$profilePhotoSource = 'https://graph.facebook.com/'.Yii::app()->user->fb_id.'/picture?type=square';
								$profilePhotoStatusTooltipMessage = Yii::t('site', 'Click here to upload and set your profile photo. You will be able to set your profile photo as your Facebook profile photo again.');
							}
						}
						break;
							
					case Users::TRACEPER_PROFILE_PHOTO_EXISTS:
						{
							$profilePhotoSource = 'profilePhotos/'.Yii::app()->user->id.'.png?random='.time();
							$profilePhotoStatusTooltipMessage = Yii::t('site', 'Click here to change your profile photo');
								
							//Fb::warn($profilePhotoStatusTooltipMessage, "TRACEPER_PROFILE_PHOTO_EXISTS");
						}
						break;
							
					case Users::BOTH_PROFILE_PHOTOS_EXISTS_USE_FACEBOOK:
						{
							$bothPhotoExists = 'useFacebook';
							$profilePhotoSource = 'https://graph.facebook.com/'.Yii::app()->user->fb_id.'/picture?type=square';
							//$profilePhotoStatusTooltipMessage = '4';
						}
						break;
							
					case Users::BOTH_PROFILE_PHOTOS_EXISTS_USE_TRACEPER:
						{
							$bothPhotoExists = 'useTraceper';
							$profilePhotoSource = 'profilePhotos/'.Yii::app()->user->id.'.png?random='.time();
							//$profilePhotoStatusTooltipMessage = '5';
						}
						break;
				}
					
				$newRequestsCount = null;
				$totalRequestsCount = null;
		
				Friends::model()->getFriendRequestsInfo(Yii::app()->user->id, $newRequestsCount, $totalRequestsCount);
					
				if($newRequestsCount > 0)
				{
					if($newRequestsCount <= 5)
					{
						$friendReqTooltip = Yii::t('users', 'Friendship Requests').' ('.$newRequestsCount.' '.Yii::t('users', 'new').(($totalRequestsCount > $newRequestsCount)?Yii::t('users', ' totally ').$totalRequestsCount:'').Yii::t('users', ' friendship request(s) you have').')';
					}
					else
					{
						$friendReqTooltip = Yii::t('users', 'Friendship Requests').' ('.$newRequestsCount.' '.Yii::t('users', 'new').(($totalRequestsCount > $newRequestsCount)?Yii::t('users', ' totally ').$totalRequestsCount:'').Yii::t('users', ' friendship request(s) you have').')';
					}
				}
				else
				{
					$friendReqTooltip = Yii::t('users', 'Friendship Requests');
				}
					
				//echo 'Model NOT valid in SiteController';
				if (Yii::app()->request->isAjaxRequest)
				{
					if (YII_DEBUG)
					{
						Yii::app()->clientscript->scriptMap['jquery.js'] = false;
						Yii::app()->clientScript->scriptMap['jquery-ui.js'] = false;
					}
					else
					{
						Yii::app()->clientscript->scriptMap['jquery.min.js'] = false;
						Yii::app()->clientScript->scriptMap['jquery-ui.min.js'] = false;
					}
				}
		
				echo CJSON::encode(array(
						"result"=> "1", //(Facebook register and )login successful
						"renderedTabView"=>$this->renderPartial('tabView',array(), true/*return instead of being displayed to end users*/, true),
						"renderedUserAreaView"=>$this->renderPartial('userAreaView',array('profilePhotoSource'=>$profilePhotoSource, 'profilePhotoStatus'=>$profilePhotoStatus, 'profilePhotoStatusTooltipMessage'=>$profilePhotoStatusTooltipMessage, 'bothPhotoExists'=>$bothPhotoExists, 'variablesDefined'=>false), true/*return instead of being displayed to end users*/, true),
						"renderedFriendshipRequestsView"=>$this->renderPartial('friendshipRequestsView',array('newRequestsCount'=>$newRequestsCount, 'friendReqTooltip'=>$friendReqTooltip), true/*return instead of being displayed to end users*/, true),
						"loginSuccessfulActions"=>$this->renderPartial('loginSuccessful',array('id'=>Yii::app()->user->id, 'realname'=>$model->getName(), 'latitude'=>$latitude, 'longitude'=>$longitude), true/*return instead of being displayed to end users*/, true),
				));
				Yii::app()->end();
					
				//$this->renderPartial('loginSuccessful',array('id'=>Yii::app()->user->id, 'realname'=>$model->getName()), false, $processOutput);
			}
				
			Yii::app()->end();
		}
		else
		{
			Fb::warn("directLogin() FAILED!", "actionContinueFacebookLogin()");

			echo CJSON::encode(array("result"=> "0")); //Unknown login error
			
			$errorMessage = null;
			
			if($directLogin)
			{
				$errorMessage = "Unknown login error occured during DIRECT login!";
			}
			else
			{
				$errorMessage = "Unknown login error occured during login (NOT direct)!";
			}

			$this->sendErrorMail('commonLoginError', 'Error in commonLogin()', $errorMessage);			
			Yii::app()->end();
		}
	}	
	
	/**
	 * Logs out the current user and redirect to homepage.
	 */
	public function actionLogout()
	{
		Yii::app()->user->logout(false);

		if (isset($_REQUEST['client']) && $_REQUEST['client'] == 'mobile') {
			// if mobile client end the app, no need to redirect...
			echo CJSON::encode(array(
					"result"=> "1"));
			Yii::app()->end();
		}
		else {
			$this->redirect(Yii::app()->homeUrl);
		}
	}

	/**
	 * Changes the user's current password with the new one
	 */
	public function actionChangePassword()
	{
		//Fb::warn("actionChangePassword() called", "SiteController");

		$model = new ChangePasswordForm;

		$processOutput = true;
		// collect user input data
		if(isset($_POST['ChangePasswordForm']))
		{
			$model->attributes=$_POST['ChangePasswordForm'];
			// validate user input and if ok return json data and end application.
			if($model->validate()) {
				//$users=Users::model()->findByPk(Yii::app()->user->id);
				//$users->password=md5($model->newPassword);

				//if($users->save()) // save the change to database
				if(Users::model()->changePassword(Yii::app()->user->id, $model->newPassword)) // save the change to database
				{
					echo CJSON::encode(array("result"=> "1"));
				}
				else
				{
					echo CJSON::encode(array("result"=> "0"));
					
					$errorMessage = "Password cannot be changed!";
					$this->sendErrorMail('changePasswordCannotBeChanged', 'Error in actionChangePassword()', $errorMessage);					
				}
				
				Yii::app()->end();
			}
		}

		if (Yii::app()->request->isAjaxRequest)
		{
			if (YII_DEBUG)
			{
				Yii::app()->clientscript->scriptMap['jquery.js'] = false;
				Yii::app()->clientScript->scriptMap['jquery-ui.js'] = false;
			}
			else
			{
				Yii::app()->clientscript->scriptMap['jquery.min.js'] = false;
				Yii::app()->clientScript->scriptMap['jquery-ui.min.js'] = false;
			}
		}
		
		//Complete solution for blinking problem at FireFox
		if (Yii::app()->request->getIsAjaxRequest()) {
			Yii::app()->clientScript->scriptMap['*.js'] = false;
			Yii::app()->clientScript->scriptMap['*.css'] = false;
		}		

		$this->renderPartial('changePassword',array('model'=>$model), false, $processOutput);
	}
	
	/**
	 * Sends a password reset link to the user's e-mail address 
	 */
	public function actionForgotPassword()
	{
		//Fb::warn("actionForgotPassword() called", "SiteController");
		
		$model = new ForgotPasswordForm;
	
		$processOutput = true;
				
		$mobileLang = null;
		
		if(isset($_REQUEST['language']))
		{
			$mobileLang = $_REQUEST['language'];
		}
				
		$result = "-100";
		
		// collect user input data
		if(isset($_POST['ForgotPasswordForm']))
		{
			$model->attributes = $_POST['ForgotPasswordForm'];
			// validate user input and if ok return json data and end application.
			if($model->validate()) {				
				$token = sha1(uniqid(mt_rand(), true));

				ResetPassword::model()->saveToken($model->email, $token);
				$name = Users::model()->getNameByEmail($model->email);
				
				$isTranslationRequired = false;
				
				if($mobileLang != null)
				{
					if($mobileLang == 'tr')
					{
						if(Yii::app()->language == 'tr')
						{
							$isTranslationRequired = false;
						}
						else
						{
							$isTranslationRequired = true;
						}
					}
					else
					{
						if(Yii::app()->language == 'tr')
						{
							$isTranslationRequired = true;
						}
						else
						{
							$isTranslationRequired = false;
						}
					}
				}
				
				if($isTranslationRequired == true)
				{
					if($mobileLang == 'tr')
					{
						Yii::app()->language = 'tr';
					}
					else
					{
						Yii::app()->language = 'en';
					}
				}				
				
				$message = Yii::t('site', 'Hi').' '.$name.',<br/><br/>';
								
				$message .= Yii::t('site', 'If you forgot your password, you can create a new password by clicking');
				$message .= ' '.'<a href="'.'http://'.Yii::app()->request->getServerName().Yii::app()->request->getBaseUrl().'/index.php?tok='.$token.'">'.Yii::t('site', 'here').'</a>';
				$message .= ' '.Yii::t('site', 'or the link below:').'<br/>';
				$message .= '<a href="'.'http://'.Yii::app()->request->getServerName().Yii::app()->request->getBaseUrl().'/index.php?tok='.$token.'">';
				$message .= 'http://'.Yii::app()->request->getServerName().Yii::app()->request->getBaseUrl().'/index.php?tok='.$token;
				$message .= '</a>';
				$message .= '<br/><br/><br/>';										
				$message .= Yii::t('site', 'If you did not attempt to create a new password, take no action and please inform <a href="mailto:contact@traceper.com">us</a>.');				

				//echo $message;
				
				if($this->SMTP_UTF8_mail(Yii::app()->params->noreplyEmail, 'Traceper', $model->email, $name, Yii::t('site', 'Did you forget your Traceper password?'), $message))
				{
					echo CJSON::encode(array("result"=>"1", "email"=>$model->email));
				}
				else
				{
					echo CJSON::encode(array("result"=>"0")); //Error occured while sending e-mail
				}

				//Language recovery should be done after sending the mail, because some generic message is added also in SMTP_UTF8_mail()
				if($isTranslationRequired == true) //Recover the language if needed for mobile
				{
					if($mobileLang == 'tr')
					{
						Yii::app()->language = 'en';
					}
					else
					{
						Yii::app()->language = 'tr';
					}
				}				
				
				//Even if web for these results no renderPartial required so end the running code
				Yii::app()->end();
			}
			else //Form invalid
			{	
				//Update results to be used in mobile
				
				if($model->getError('email') == Yii::t('site', 'This e-mail is not registered!'))
				{
					$result = "-1";
				}
				else if($model->getError('email') == Yii::t('site', 'Registration incomplete, please request activation e-mail below the sign up form'))
				{
					$result = "-2";
				}
				else if($model->getError('email') == Yii::t('site', 'You are registered as Facebook user for our service, therefore you do not have to enter a Traceper password. You could use "Log in with facebook" button to log in to your Traceper account.'))
				{
					$result = "-5";
				}
				else
				{
					$result = "-3"; //Unknown form error
					
					//Webse hatalar kullaniciya gosteriliyor zaten, mobilden ise form hatali olarak gonderilmiyor olmali
					if (isset($_REQUEST['client']) && $_REQUEST['client']=='mobile')
					{
						$errorMessage = "Unknown mobile form validation error occured!";
						$this->sendErrorMail('mobileForgotPasswordUnknownValidationError', 'Error in actionForgotPassword()', $errorMessage);						
					}
				}													
			}
		}
		else
		{
			//Update results to be used in mobile
			
			$result = "-4"; //Form missing

			//Webse form yoksa form diyalogu cikariliyor, mobilden ise form mutlaka gelmeli
			if (isset($_REQUEST['client']) && $_REQUEST['client']=='mobile')
			{
				$errorMessage = "Mobile form missing error occured!";
				$this->sendErrorMail('mobileForgotPasswordFormMissing', 'Error in actionForgotPassword()', $errorMessage);
			}			
		}
		
		if (isset($_REQUEST['client']) && $_REQUEST['client']=='mobile')
		{
			echo CJSON::encode(array("result"=> $result));
		}
		else
		{
			if (Yii::app()->request->isAjaxRequest)
			{
				if (YII_DEBUG)
				{
					Yii::app()->clientscript->scriptMap['jquery.js'] = false;
					Yii::app()->clientScript->scriptMap['jquery-ui.js'] = false;
				}
				else
				{
					Yii::app()->clientscript->scriptMap['jquery.min.js'] = false;
					Yii::app()->clientScript->scriptMap['jquery-ui.min.js'] = false;
				}
			}
			
			//Complete solution for blinking problem
			if (Yii::app()->request->getIsAjaxRequest()) {
				Yii::app()->clientScript->scriptMap['*.js'] = false;
				Yii::app()->clientScript->scriptMap['*.css'] = false;
			}
			
			$this->renderPartial('forgotPassword',array('model'=>$model), false, $processOutput);			
		}
	}	
	
	/**
	 * Resets the user's current password
	 */
	public function actionResetPassword()
	{
		$model = new ResetPasswordForm;
		
		$processOutput = true;
		
		$token = null;
		
		if (isset($_GET['token']) && $_GET['token'] != null)
		{
			$token = $_GET['token'];
		}			
		
		// collect user input data
		if(isset($_POST['ResetPasswordForm']))
		{
			$model->attributes = $_POST['ResetPasswordForm'];
			// validate user input and if ok return json data and end application.
			
			//Fb::warn("token:".$token, "SiteController - actionResetPassword()");
			
			if($model->validate()) {
				if(Users::model()->changePassword(Users::model()->getUserId(ResetPassword::model()->getEmailByToken($token)), $model->newPassword)) // save the change to database
				{
					ResetPassword::model()->deleteToken($token);

// 					Yii::app()->clientScript->scriptMap['jquery.js'] = false;
// 					Yii::app()->clientScript->scriptMap['jquery-ui.min.js'] = false;
// 					$this->renderPartial('resetPasswordSuccessful',array(), false, $processOutput);
					
// 					Yii::app()->end();

					//echo CJSON::encode(array("result"=>"1"));
					
					if (Yii::app()->request->isAjaxRequest)
					{
						if (YII_DEBUG)
						{
							Yii::app()->clientscript->scriptMap['jquery.js'] = false;
							Yii::app()->clientScript->scriptMap['jquery-ui.js'] = false;
						}
						else
						{
							Yii::app()->clientscript->scriptMap['jquery.min.js'] = false;
							Yii::app()->clientScript->scriptMap['jquery-ui.min.js'] = false;
						}
					}
					
					//Complete solution for blinking problem at FireFox
					if (Yii::app()->request->getIsAjaxRequest()) {
						Yii::app()->clientScript->scriptMap['*.js'] = false;
						Yii::app()->clientScript->scriptMap['*.css'] = false;
					}					
					
					echo CJSON::encode(array("result"=>"1",
											 "resetPaswordView"=>$this->renderPartial('resetPassword',array('model'=>$model, 'token'=>$token), true/*return instead of being displayed to end users*/, $processOutput)
					));				
				}
				else
				{				
					//Fb::warn("An error occured while changing your password!", "SiteController - actionResetPassword()");

					//echo CJSON::encode(array("result"=>"0"));
					
					echo CJSON::encode(array("result"=>"0",
											 "resetPaswordView"=>$this->renderPartial('resetPassword',array('model'=>$model, 'token'=>$token), true/*return instead of being displayed to end users*/, $processOutput)
					));

					$errorMessage = "Password cannot be reset!";
					$this->sendErrorMail('resetPasswordCannotBeReset', 'Error in actionResetPassword()', $errorMessage);					
				}
				
				Yii::app()->end();
			}
			else
			{
				//Fb::warn("model NOT valid", "SiteController - actionResetPassword()");												
			}
			
			if (Yii::app()->request->isAjaxRequest)
			{
				if (YII_DEBUG)
				{
					Yii::app()->clientscript->scriptMap['jquery.js'] = false;
					Yii::app()->clientScript->scriptMap['jquery-ui.js'] = false;
				}
				else
				{
					Yii::app()->clientscript->scriptMap['jquery.min.js'] = false;
					Yii::app()->clientScript->scriptMap['jquery-ui.min.js'] = false;
				}
			}

			//Complete solution for blinking problem at FireFox
			if (Yii::app()->request->getIsAjaxRequest()) {
				Yii::app()->clientScript->scriptMap['*.js'] = false;
				Yii::app()->clientScript->scriptMap['*.css'] = false;
			}			
			
			$this->renderPartial('resetPassword',array('model'=>$model, 'token'=>$token), false, $processOutput);
			
			Yii::app()->end();
		}
		else
		{
			//Fb::warn("ResetPasswordForm is NOT set", "SiteController - actionResetPassword()");
		}															
	}

	/**
	 * Resends an account activation link to the user's e-mail address, if he has already started registration process
	 */
	public function actionActivationNotReceived()
	{
		//Fb::warn("actionActivationNotReceived() called", "SiteController");
	
		$model = new ActivationNotReceivedForm;
	
		$processOutput = true;
		
		$mobileLang = null;
		
		if(isset($_REQUEST['language']))
		{
			$mobileLang = $_REQUEST['language'];
		}
		
		$result = "-100";
		
		// collect user input data
		if(isset($_POST['ActivationNotReceivedForm']))
		{
			$model->attributes = $_POST['ActivationNotReceivedForm'];
			// validate user input and if ok return json data and end application.
			if($model->validate()) 
			{
				$candidatePassword = null;
				$candidateName = null;
				$candidateRegistrationTime = null;
				
				UserCandidates::model()->getCandidateInfoByEmail($model->email, $candidatePassword, $candidateName, $candidateRegistrationTime);
				
				$key = md5($model->email.$candidateRegistrationTime);
				
				$isTranslationRequired = false;
				
				if($mobileLang != null)
				{
					if($mobileLang == 'tr')
					{
						if(Yii::app()->language == 'tr')
						{
							$isTranslationRequired = false;
						}
						else
						{
							$isTranslationRequired = true;
						}
					}
					else
					{
						if(Yii::app()->language == 'tr')
						{
							$isTranslationRequired = true;
						}
						else
						{
							$isTranslationRequired = false;
						}
					}
				}
				
				if($isTranslationRequired == true)
				{
					if($mobileLang == 'tr')
					{
						Yii::app()->language = 'tr';
					}
					else
					{
						Yii::app()->language = 'en';
					}
				}				
				
				$message = Yii::t('site', 'Hi').' '.$candidateName.',<br/><br/>';
				$message .= Yii::t('site', 'You could activate your account by clicking');
				$message .= ' '.'<a href="'.'http://'.Yii::app()->request->getServerName().$this->createUrl('site/activate',array('email'=>$model->email,'key'=>$key)).'">'.Yii::t('site', 'here').'</a>';
				$message .= ' '.Yii::t('site', 'or the link below:').'<br/>';
				$message .= '<a href="'.'http://'.Yii::app()->request->getServerName().$this->createUrl('site/activate',array('email'=>$model->email,'key'=>$key)).'">';
				$message .= 'http://'.Yii::app()->request->getServerName().$this->createUrl('site/activate',array('email'=>$model->email,'key'=>$key));
				$message .= '</a>';
				$message .= '<br/><br/>';				
				$message .= Yii::t('site', 'If you do not remember your password, you could request to generate new one.');							

				//echo $message;
												
				if($this->SMTP_UTF8_mail(Yii::app()->params->noreplyEmail, 'Traceper', $model->email, $candidateName, Yii::t('site', 'Traceper Activation'), $message))
				{
					//Bu cevabi hem web (activationNotReceived.php) hem mobil kullandigindan mobil kontrolu yapma
					echo CJSON::encode(array("result"=>"1", "email"=>$model->email));
				}
				else
				{
					//Bu cevabi hem web (activationNotReceived.php) hem mobil kullandigindan mobil kontrolu yapma
					echo CJSON::encode(array("result"=>"0")); //Error occured while sending e-mail
				}
				
				//Language recovery should be done after sending the mail, because some generic message is added also in SMTP_UTF8_mail()
				if($isTranslationRequired == true) //Recover the language if needed for mobile
				{
					if($mobileLang == 'tr')
					{
						Yii::app()->language = 'en';
					}
					else
					{
						Yii::app()->language = 'tr';
					}
				}				
	
				//Even if web for these results no renderPartial required so end the running code
				Yii::app()->end();
			}
			else //Form invalid
			{
				//Update results to be used in mobile
			
				if($model->getError('email') == Yii::t('site', 'You are already registered for our service. If you do not remember your password, you could request to determine a new one by the link "Forgot Password?".'))
				{
					$result = "-1";
				}
				else if($model->getError('email') == Yii::t('site', 'There has been a problem with your registration process. Please try to sign up for Traceper again.'))
				{
					$result = "-2";
				}
				else if($model->getError('email') == Yii::t('site', 'You are already registered as Facebook user for our service. You could use "Log in with facebook" button to log in to your Traceper account.'))
				{
					$result = "-5";
				}
				else
				{
					$result = "-3"; //Unknown form error

					//Webse hatalar kullaniciya gosteriliyor zaten, mobilden ise form hatali olarak gonderilmiyor olmali
					if (isset($_REQUEST['client']) && $_REQUEST['client']=='mobile')
					{
						$errorMessage = "Unknown mobile form validation error occured!";
						$this->sendErrorMail('mobileActivationNotReceivedUnknownValidationError', 'Error in actionActivationNotReceived()', $errorMessage);
					}					
				}
			}			
		}
		else
		{
			//Update results to be used in mobile
				
			$result = "-4"; //Form missing
			
			//Webse form yoksa form diyalogu cikariliyor, mobilden ise form mutlaka gelmeli
			if (isset($_REQUEST['client']) && $_REQUEST['client']=='mobile')
			{
				$errorMessage = "Mobile form missing error occured!";
				$this->sendErrorMail('mobileActivationNotReceivedFormMissing', 'Error in actionActivationNotReceived()', $errorMessage);
			}			
		}
				
		if (isset($_REQUEST['client']) && $_REQUEST['client']=='mobile')
		{
			echo CJSON::encode(array("result"=> $result));
		}
		else
		{
			if (Yii::app()->request->isAjaxRequest)
			{
				if (YII_DEBUG)
				{
					Yii::app()->clientscript->scriptMap['jquery.js'] = false;
					Yii::app()->clientScript->scriptMap['jquery-ui.js'] = false;
				}
				else
				{
					Yii::app()->clientscript->scriptMap['jquery.min.js'] = false;
					Yii::app()->clientScript->scriptMap['jquery-ui.min.js'] = false;
				}
			}
			
			//Complete solution for blinking problem at FireFox
			if (Yii::app()->request->getIsAjaxRequest()) {
				Yii::app()->clientScript->scriptMap['*.js'] = false;
				Yii::app()->clientScript->scriptMap['*.css'] = false;
			}
			
			$this->renderPartial('activationNotReceived',array('model'=>$model), false, $processOutput);			
		}	
	}	

	public function actionRegister()
	{
		$model = new RegisterForm;
		$processOutput = true;		
		$mobileLang = null;
		$isMobile = false;

		$app = Yii::app();
		$preferredLanguage = null;
	
		if (isset($app->session['_lang']))
		{
			$preferredLanguage = $app->session['_lang'];
		}
		else
		{
			$preferredLanguage = substr(Yii::app()->getRequest()->getPreferredLanguage(), 0, 2);
		}

		if(isset($_REQUEST['language']))
		{
			$mobileLang = $_REQUEST['language'];
			$preferredLanguage = $_REQUEST['language'];
		}
		
		if (Yii::app()->request->isAjaxRequest)
		{
			if (YII_DEBUG)
			{
				Yii::app()->clientscript->scriptMap['jquery.js'] = false;
				Yii::app()->clientScript->scriptMap['jquery-ui.js'] = false;
			}
			else
			{
				Yii::app()->clientscript->scriptMap['jquery.min.js'] = false;
				Yii::app()->clientScript->scriptMap['jquery-ui.min.js'] = false;
			}
		}
			
		//Complete solution for blinks at FireFox
		if (Yii::app()->request->getIsAjaxRequest()) {
			Yii::app()->clientScript->scriptMap['*.js'] = false;
			Yii::app()->clientScript->scriptMap['*.css'] = false;
		}		
		
		// collect user input data
		if(isset($_REQUEST['RegisterForm']))
		{
			$model->attributes = $_REQUEST['RegisterForm'];
		
			// validate user input and if ok return json data and end application.
		
			// 			if (Yii::app()->request->isAjaxRequest) {
			// 				$processOutput = false;
			// 			}			
		
			if($model->validate()) {
				//echo $model->ac_id;

				if (isset($_REQUEST['client']) && $_REQUEST['client']=='mobile')
				{
					$isMobile = true;
				}
				else
				{
					$isMobile = false;
				}

				$correctedEmail = null;
					
				if(Users::model()->doesUserEmailNeedToBeCorrected($model->email, $correctedEmail))
				{
					//Fb::warn($model->email." needs to be corrected to ".$correctedEmail, "actionRegister()");
					
					echo CJSON::encode(array("result"=>"-1",
							"renderedView"=>$this->renderPartial('yourEmailSeemsToBeInvalid',array('form'=>$_REQUEST['RegisterForm'], 'currentEmail'=>$model->email, 'correctedEmail'=>$correctedEmail, 'preferredLanguage'=>$preferredLanguage, 'mobileLang'=>$mobileLang, 'isMobile'=>$isMobile), true/*return instead of being displayed to end users*/, true),
							//"registerView"=>$this->renderPartial('register',array('model'=>$model), true/*return instead of being displayed to end users*/, $processOutput)
					));					
				}
				else
				{
					//Fb::warn("No need to correct email", "actionRegister");
					
					$this->commonRegister($model, $preferredLanguage, $mobileLang, $isMobile);
				}

				Yii::app()->end();
			}
			else
			{
				if($isMobile)
				{
					$result = "1"; //Initialize with "1" to be used whether no error occured

					if($model->getError('email') == Yii::t('site', 'E-mail is already registered!'))
					{
						$result = "-1";
					}
					else if($model->getError('email') == Yii::t('site', 'Registration incomplete, please request activation e-mail below'))
					{
						$result = "-2";
					}
					else if($model->getError('email') == Yii::t('site', 'You are already registered as Facebook user for our service. Please use \"Log in with facebook\" button to log in to your Traceper account.'))
					{
						$result = "-4";
					}
					else if($model->getError('email') != null)
					{
						$result = $model->getError('email');
					}
					else
					{
						$result = "-3"; //Unkown registration errror
						
						$errorMessage = "Unkown form validation error occured!";
						$this->sendErrorMail('mobileRegisterUnknownValidationError', 'Error in actionRegister()', $errorMessage);						
					}
		
					echo CJSON::encode(array(
							"result"=> $result,
					));
				}
				else
				{	
					$this->renderPartial('register',array('model'=>$model), false, $processOutput);
				}
		
				Yii::app()->end();
			}
		}
		else
		{
			//echo 'RegisterForm is NOT set';
			//Even if model is not set this renderPartial is useful for language transition
			$this->renderPartial('register',array('model'=>$model), false, $processOutput);
		}
	}
	
	public function actionContinueRegister()
	{
		$model = new RegisterForm;	
		
		if(isset($_REQUEST['RegisterForm']) && ($_REQUEST['RegisterForm'] != NULL) &&
		   isset($_REQUEST['preferredLanguage']) && ($_REQUEST['preferredLanguage'] != NULL))
		{
			//Fb::warn("['RegisterForm'] is SET", "actionContinueRegister()");
			
			if(isset($_REQUEST['registerEmail']) && ($_REQUEST['registerEmail'] != NULL))
			{
				$model->attributes = $_REQUEST['RegisterForm'];
				$model->email = $_REQUEST['registerEmail'];
				$model->emailAgain = $_REQUEST['registerEmail'];
				$this->commonRegister($model, $_REQUEST['preferredLanguage'], $_REQUEST['mobileLang'], $_REQUEST['isMobile']);				
			}
			else
			{
				echo CJSON::encode(array("result"=>"-1")); //Please select an option
			}
		}
		else
		{
			//Fb::warn("['RegisterForm'] is NOT set!", "actionContinueRegister()");
		}		
	}	

	private function commonRegister($model, $preferredLanguage, $mobileLang, $isMobile)
	{
		$processOutput = true;
		$registrationMedium = null;
		
		if($isMobile)
		{
			$registrationMedium = 'Mobile';
		}
		else
		{
			$registrationMedium = 'Web';
		}
		
		//Facebook kaydol ise
		if (isset($model->account_type) && $model->account_type != "0") {
			//Fb::warn("Before saveFacebookUser()", "SiteController");
				
			$registrationMedium = $registrationMedium.' FB';
				
			if (Users::model()->saveFacebookUser($model->email, md5($model->password), trim($model->name).' '.trim($model->lastName), $model->ac_id, $model->account_type, $registrationMedium, $preferredLanguage)) {
				echo CJSON::encode(array("result"=>"1"));
				Yii::app()->end();
			}
			else {
				echo CJSON::encode(array("result"=>"Error in saving"));
				Yii::app()->end();
			}
		}
		else if (UserCandidates::model()->saveUserCandidates($model->email, md5($model->password), trim($model->name).' '.trim($model->lastName), date('Y-m-d h:i:s'), $registrationMedium, $preferredLanguage))
		{
			//Fb::warn("saveUserCandidates() called", "SiteController");
				
			$isTranslationRequired = false;
				
			if($mobileLang != null)
			{
				if($mobileLang == 'tr')
				{
					if(Yii::app()->language == 'tr')
					{
						$isTranslationRequired = false;
					}
					else
					{
						$isTranslationRequired = true;
					}
				}
				else
				{
					if(Yii::app()->language == 'tr')
					{
						$isTranslationRequired = true;
					}
					else
					{
						$isTranslationRequired = false;
					}
				}
			}
				
			if($isTranslationRequired == true)
			{
				if($mobileLang == 'tr')
				{
					Yii::app()->language = 'tr';
				}
				else
				{
					Yii::app()->language = 'en';
				}
			}
				
			$time = date('Y-m-d h:i:s');
			$key = md5($model->email.$time);
				
			$message = Yii::t('site', 'Hi').' '.trim($model->name).',<br/><br/>';
				
			//$message .= 'mobileLang: '.$mobileLang;
				
			$message .= Yii::t('site', 'You could activate your account by clicking');
			$message .= ' '.'<a href="'.'http://'.Yii::app()->request->getServerName().$this->createUrl('site/activate',array('email'=>$model->email,'key'=>$key)).'">'.Yii::t('site', 'here').'</a>';
			$message .= ' '.Yii::t('site', 'or the link below:').'<br/>';
			$message .= '<a href="'.'http://'.Yii::app()->request->getServerName().$this->createUrl('site/activate',array('email'=>$model->email,'key'=>$key)).'">';
			$message .= 'http://'.Yii::app()->request->getServerName().$this->createUrl('site/activate',array('email'=>$model->email,'key'=>$key));
			$message .= '</a>';
			$message .= '<br/><br/>';
			$message .= Yii::t('site', 'Your Password is').':'.$model->password;
				
			//echo $message;
				
			if($this->SMTP_UTF8_mail(Yii::app()->params->noreplyEmail, 'Traceper', $model->email, trim($model->name).' '.trim($model->lastName), Yii::t('site', 'Traceper Activation'), $message))	
			{
				if (isset($_REQUEST['client']) && $_REQUEST['client']=='mobile')
				{
					echo CJSON::encode(array("result"=>"1", "email"=>$model->email));
				}
				else
				{
					echo CJSON::encode(array("result"=>"1",
							"email"=>$model->email,
							"registerView"=>$this->renderPartial('register',array('model'=>$model), true/*return instead of being displayed to end users*/, $processOutput)
					));
				}
				//Yii::app()->end();
			}
			else
			{
				if($isMobile)
				{
					echo CJSON::encode(array("result"=>"2"));
				}
				else
				{
					echo CJSON::encode(array("result"=>"2",
							"registerView"=>$this->renderPartial('register',array('model'=>$model), true/*return instead of being displayed to end users*/, $processOutput)
					));
				}
				//Yii::app()->end();
			}
				
			//Language recovery should be done after sending the mail, because some generic message is added also in SMTP_UTF8_mail()
			if($isTranslationRequired == true) //Recover the language if needed for mobile
			{
				if($mobileLang == 'tr')
				{
					Yii::app()->language = 'en';
				}
				else
				{
					Yii::app()->language = 'tr';
				}
			}
		}
		else
		{
			if ($isMobile)
			{
				echo JSON::encode(array("result"=>"0")); //Error in saving
					
				$errorMessage = "Error in saving user candidate!";
				$this->sendErrorMail('mobileRegisterErrorInSavingUserCandidate', 'MOBILE Error in actionRegister()', $errorMessage);
			}
			else
			{
				echo JSON::encode(array("result"=>"0",
						"registerView"=>$this->renderPartial('register',array('model'=>$model), true/*return instead of being displayed to end users*/, $processOutput)
				)); //Error in saving
					
				$errorMessage = "Error in saving user candidate!";
				$this->sendErrorMail('registerErrorInSavingUserCandidate', 'Error in actionRegister()', $errorMessage);
			}
		}		
	}

	//Bu fonksiyonu mobil uygulama kullanıyor
	public function actionIsFacebookUserRegistered(){

		$result = "Missing parameter";
		if (isset($_REQUEST['email']) && $_REQUEST['email'] != NULL
			 && isset($_REQUEST['facebookId']) && $_REQUEST['facebookId'] != NULL)
		{
			$email = $_REQUEST['email'];
			$facebookId = $_REQUEST['facebookId'];
			$result = "0";
			if (Users::model()->isFacebookUserRegistered($email, $facebookId)){
				$result = "1";
			}
		}
		else
		{
			$message = "Missing Parameter: 'email' or 'facebookId' is missing!";
			$this->sendErrorMail('isFacebookUserRegisteredMissingParameter', 'Error in actionIsFacebookUserRegistered()', $message);
		}
		
		echo CJSON::encode(array(
				"result"=> $result,
		));
	}

	public function actionRegisterGPSTracker()
	{
		$model = new RegisterGPSTrackerForm;

		$processOutput = true;
		$isMobileClient = false;
		// collect user input data
		if(isset($_POST['RegisterGPSTrackerForm']))
		{
			$isMobileClient = false;
			if (isset($_REQUEST['client']) && $_REQUEST['client']=='mobile') {
				$isMobileClient = true;
			}
			$model->attributes = $_POST['RegisterGPSTrackerForm'];
			// validate user input and if ok return json data and end application.
			if($model->validate()) {

				//Check whether a device exists with the same name in the Users table (Since the table 'Users' is used as common for both
				//real users and devices we cannot add unique index for realname, so we have to check same name existance manually)
				if(Users::model()->find('userType=:userType AND realname=:name', array(':userType'=>UserType::GPSDevice, ':name'=>$model->name)) == null)
				{
					try
					{
						if (Users::model()->saveGPSUser($model->deviceId, md5($model->name), $model->name, UserType::GPSDevice, 0))
						{

							if(Friends::model()->makeFriends(Yii::app()->user->id, Users::model()->getUserId($model->deviceId)))
							{
								echo CJSON::encode(array("result"=> "1"));
							}
							else
							{
								echo CJSON::encode(array("result"=> "Unknown error 1"));
							}
						}
						else
						{
							echo CJSON::encode(array("result"=> "Unknown error 2"));
						}
					}
					catch (Exception $e)
					{
						if($e->getCode() == Yii::app()->params->duplicateEntryDbExceptionCode) //Duplicate Entry
						{
							echo CJSON::encode(array("result"=> "Duplicate Entry"));
						}
						else
						{
							echo 'Caught exception: ',  $e->getMessage(), "\n";
							echo 'Code: ', $e->getCode(), "\n";
						}
						Yii::app()->end();							
					}
				}
				else
				{
					echo CJSON::encode(array("result"=> "Duplicate Name"));
				}

				Yii::app()->end();
			}

// 			if (Yii::app()->request->isAjaxRequest) {
// 				$processOutput = false;
// 			}
		}

		if ($isMobileClient == true)
		{
			$result = "1"; //Initialize with "1" to be used whether no error occured

			if ($model->getError('password') != null) {
				$result = $model->getError('password');
			}
			else if ($model->getError('email') != null) {
				$result = $model->getError('email');
			}
			else if ($model->getError('passwordAgain') != null) {
				$result = $model->getError('passwordAgain');
			}
			else if ($model->getError('passwordAgain') != null) {
				$result = $model->getError('passwordAgain');
			}

			echo CJSON::encode(array(
					"result"=> $result,
			));
			Yii::app()->end();
		}
		else {
			if (Yii::app()->request->isAjaxRequest)
			{
				if (YII_DEBUG)
				{
					Yii::app()->clientscript->scriptMap['jquery.js'] = false;
					Yii::app()->clientScript->scriptMap['jquery-ui.js'] = false;
				}
				else
				{
					Yii::app()->clientscript->scriptMap['jquery.min.js'] = false;
					Yii::app()->clientScript->scriptMap['jquery-ui.min.js'] = false;
				}
			}
			
			//Complete solution for blinking problem at FireFox
			if (Yii::app()->request->getIsAjaxRequest()) {
				Yii::app()->clientScript->scriptMap['*.js'] = false;
				Yii::app()->clientScript->scriptMap['*.css'] = false;
			}
						
			$this->renderPartial('registerGPSTracker',array('model'=>$model), false, $processOutput);
		}
	}

	public function actionRegisterNewStaff()
	{
		$model = new RegisterNewStaffForm;

		$processOutput = true;
		$isMobileClient = false;
		// collect user input data
		if(isset($_POST['RegisterNewStaffForm']))
		{
			$isMobileClient = false;
			$registrationMedium = null;
			
			if (isset($_REQUEST['client']) && $_REQUEST['client']=='mobile') {
				$isMobileClient = true;
				$registrationMedium = 'Mobile';
			}
			else
			{
				$registrationMedium = 'Web';
			}
			
			$model->attributes = $_POST['RegisterNewStaffForm'];
			// validate user input and if ok return json data and end application.
			if($model->validate()) {

				try
				{
					if(Users::model()->saveUser($model->email, md5($model->password), $model->name, UserType::RealStaff/*userType*/, 0/*accountType*/, $registrationMedium))
					{
						if(Friends::model()->makeFriends(Yii::app()->user->id, Users::model()->getUserId($model->email)))
						{
							echo CJSON::encode(array("result"=> "1"));
						}
						else
						{
							echo CJSON::encode(array("result"=> "Unknown error 1"));
						}
					}
					else
					{
						echo CJSON::encode(array("result"=> "Unknown error 2"));
					}
				}
				catch (Exception $e)
				{
					if($e->getCode() == Yii::app()->params->duplicateEntryDbExceptionCode) //Duplicate Entry
					{
						echo CJSON::encode(array("result"=> "Duplicate Entry"));
					}
					Yii::app()->end();
				}

				Yii::app()->end();
			}
		}

		if ($isMobileClient == true)
		{
			$result = "1"; //Initialize with "1" to be used whether no error occured

			if ($model->getError('password') != null) {
				$result = $model->getError('password');
			}
			else if ($model->getError('email') != null) {
				$result = $model->getError('email');
			}
			else if ($model->getError('passwordAgain') != null) {
				$result = $model->getError('passwordAgain');
			}
			else if ($model->getError('passwordAgain') != null) {
				$result = $model->getError('passwordAgain');
			}

			echo CJSON::encode(array(
					"result"=> $result,
			));
			Yii::app()->end();
		}
		else {
			if (Yii::app()->request->isAjaxRequest)
			{
				if (YII_DEBUG)
				{
					Yii::app()->clientscript->scriptMap['jquery.js'] = false;
					Yii::app()->clientScript->scriptMap['jquery-ui.js'] = false;
				}
				else
				{
					Yii::app()->clientscript->scriptMap['jquery.min.js'] = false;
					Yii::app()->clientScript->scriptMap['jquery-ui.min.js'] = false;
				}
			}
			
			//Complete solution for blinking problem at FireFox
			if (Yii::app()->request->getIsAjaxRequest()) {
				Yii::app()->clientScript->scriptMap['*.js'] = false;
				Yii::app()->clientScript->scriptMap['*.css'] = false;
			}
						
			$this->renderPartial('registerNewStaff',array('model'=>$model), false, $processOutput);
		}

	}

	public function actionInviteUsers()
	{
		$model = new InviteUsersForm;

		$processOutput = true;
		// collect user input data
		if(isset($_POST['InviteUsersForm']))
		{
			$model->attributes = $_POST['InviteUsersForm'];
			// validate user input and if ok return json data and end application.
			if($model->validate()) {

				$emailsList = trim($model->emails);
				$emailArray= $this->splitEmails($emailsList);
				$duplicateEmails = array();
				$arrayLength = count($emailArray);
				$invitationSentCount = 0;
				
				$inviterName = null;
				$inviterEmail = null;
				Users::model()->getUserInfo(Yii::app()->user->id, $inviterName, $inviterEmail);
								
				for ($i = 0; $i < $arrayLength; $i++)
				{					
					$dt = date("Y-m-d H:m:s");
					$inviteeEmail = trim($emailArray[$i]);

					try
					{
						if(InvitedUsers::model()->saveInvitedUsers($inviteeEmail, $dt))
						{
							$key = md5($inviteeEmail.$dt);
							//send invitation mail
							$invitationSentCount++;
						
							$message = Yii::t('site', 'Hi').',<br/><br/>'.Yii::t('site', 'You have been invited to Traceper by {name} ({email}).', array('{name}'=>$inviterName, '{email}'=>$inviterEmail));
							
							if($model->invitationMessage != null)
							{
								$message .= ' '.Yii::t('site', 'Your friend\'s message:').'<br/><br/>';
								$message .= '"'.$model->invitationMessage.'"';
							}
													
							$message .= '<br/><br/>';

							$message .= '<a href="'.'http://'.Yii::app()->request->getServerName().Yii::app()->request->getBaseUrl().'">';					
							$message .= Yii::t('site', 'Click here to sign up for Traceper');
							$message .= '</a>';
							
							$message .= '<br/><br/>';

							$message .= '<a href="https://play.google.com/store/apps/details?id=com.yudu&feature=search_result#?t=W251bGwsMSwxLDEsImNvbS55dWR1Il0.">';
							$message .= Yii::t('site', 'Click here to download and install the mobile application at Google Play');
							$message .= '</a>'.' '.Yii::t('site', '(Registration could also be done by mobile application)');
							
							//echo $message;
							$this->SMTP_UTF8_mail(Yii::app()->params->noreplyEmail, 'Traceper', $inviteeEmail, '', Yii::t('site', 'Traceper Invitation'), $message);
						}						
					} 
					catch (Exception $e)
					{
						if($e->getCode() == Yii::app()->params->duplicateEntryDbExceptionCode) //Duplicate Entry
						{
							//echo CJSON::encode(array("result"=> "Duplicate Entry"));
							$duplicateEmails[] = $inviteeEmail;
						}
						else
						{
							echo 'Caught exception: ',  $e->getMessage(), "\n";
							echo 'Code: ', $e->getCode(), "\n";
						}
						//Yii::app()->end();
					}
				}

				if ($arrayLength == $invitationSentCount) // save the change to database
				{
					echo CJSON::encode(array("result"=> "1"));
				}
				else
				{
					//echo CJSON::encode(array("result"=> "0"));
					echo CJSON::encode(array("result"=>"Duplicate Entry", "emails"=>$duplicateEmails));
				}
				Yii::app()->end();
			}
		}

		if (Yii::app()->request->isAjaxRequest)
		{
			if (YII_DEBUG)
			{
				Yii::app()->clientscript->scriptMap['jquery.js'] = false;
				Yii::app()->clientScript->scriptMap['jquery-ui.js'] = false;
			}
			else
			{
				Yii::app()->clientscript->scriptMap['jquery.min.js'] = false;
				Yii::app()->clientScript->scriptMap['jquery-ui.min.js'] = false;
			}
		}
		
		//Complete solution for blinking problem at FireFox
		if (Yii::app()->request->getIsAjaxRequest()) {
			Yii::app()->clientScript->scriptMap['*.js'] = false;
			Yii::app()->clientScript->scriptMap['*.css'] = false;
		}		

		$this->renderPartial('inviteUsers',array('model'=>$model), false, $processOutput);
	}

	private function splitEmails($emails)
	{
		$emails = str_replace(array(" ",",","\r","\n"),array(";",";",";",";"),$emails);
		$emails = str_replace(";;", ";",$emails);
		$emails = explode(";", $emails);
		return $emails;
	}

	public function actionActivate()
	{
		$result = "Sorry, you entered this page with wrong parameters";
		if (isset($_GET['email']) && $_GET['email'] != null
				&& isset($_GET['key']) && $_GET['key'] != null
		)
		{
			$email = $_GET['email'];
			$key = $_GET['key'];

			$processOutput = true;
			// collect user input data

			$criteria=new CDbCriteria;
			$criteria->select='Id,email,realname,password,time,registrationMedium,preferredLanguage';
			$criteria->condition='email=:email';
			$criteria->params=array(':email'=>$email);
			$userCandidate = UserCandidates::model()->find($criteria); // $params is not needed
			
			if($userCandidate != null)
			{
				$generatedKey =  md5($email.$userCandidate->time);
					
				if ($generatedKey == $key)
				{
					$result = "Sorry, there is a problem in activating the user";
					if(Users::model()->saveUser($userCandidate->email, $userCandidate->password, $userCandidate->realname, UserType::RealUser/*userType*/, 0/*accountType*/, $userCandidate->registrationMedium, $userCandidate->preferredLanguage))
					{
						if(strcmp($userCandidate->registrationMedium, 'Web') == 0)
						{
							//$result = Yii::t('site', 'Your account has been activated successfully, you can login now. You have signed up via our web site, so it is possible that you have not installed our mobile application. If so, you could not provide location information without using the mobile app. Therefore we strongly recommend you to download and install our mobile app at Google Play. You could find the app link just below the "Sign Up" form. After logging into mobile app, you and your friends could see your location on the map.');
							
							$result = Yii::t('site', 'Your account has been activated successfully, you can login now...').'</br></br>';
							$result .= Yii::t('site', 'You have signed up via our web site, so it is possible that you have not installed our mobile application. If so, we strongly recommend you to download and install our mobile app at Google Play so that you could share your location  or whenever you want. You could download the app by clicking the links {downloadIcon} and {QRCodeIcon} at bottom-left part of the page or by just clicking {downloadAppHere}. After logging into mobile app, you and your friends could see your location on the map.', 
													   array('{downloadIcon}' => '<div class="lo-icon-in-tooltip icon-download1"></div>', '{QRCodeIcon}' => '<div class="lo-icon-in-tooltip icon-qrcode"></div>', '{downloadAppHere}' => CHtml::link(Yii::t('common', 'here'), "https://play.google.com/store/apps/details?id=com.yudu&feature=search_result#?t=W251bGwsMSwxLDEsImNvbS55dWR1Il0.", array())));							
						}
						else
						{
							$result = Yii::t('site', 'Your account has been activated successfully, you can login now...').'</br></br>';
							$result .= Yii::t('site', 'You should login at mobile app in order to begin providing your location info. On the other hand, you could also use our web site for various common operations in addition to viewing the shared photos and creating friend groups which are available for only web site at the moment.');							
						}
						
						$userCandidate->delete();
						
						//echo CJSON::encode(array("result"=> "1"));
					}
				}
				else 
				{
					$result = Yii::t('site', 'There has been a problem with your registration process. Please try to sign up for Traceper again.');
				}				
			}
			else
			{
				if(Users::model()->isUserRegistered($email))
				{
					$result = Yii::t('site', 'You have already registered to Traceper, so you can login now. If you forgot your password, you can request to generate a new one.');
				}
				else 
				{
					$result = Yii::t('site', 'There has been a problem with your registration process. Please try to sign up for Traceper again.');
				}
			}
		}

		//$this->redirect(Yii::app()->homeUrl);
		//$this->renderPartial('messageDialog', array('result'=>$result, 'title'=>Yii::t('site', 'Account Activation')), false, true);
		
		//Bununla tum site render ediliyor sonra da $content degiskeninde tutulan messageDialog view'ı render ediliyor
		$this->render('messageDialog', array('result'=>$result, 'title'=>Yii::t('site', 'Account Activation')), false);
		
// 		Yii::app()->user->setflash(1, array('title' => Yii::t('site', 'Account Activation'), 'content' => $result) );
// 		$this->render('index');
	}
	
	public function actionChangeLanguage()
	{
		$app = Yii::app();
		
		if (isset($_GET['lang'])  && ($_GET['lang'] != null))
		{
			$app->language = $_GET['lang'];
			$app->session['_lang'] = $_GET['lang'];
			
			//Fb::warn("actionChangeLanguage() called", "SiteController");
		}
	}

	/**
	 * Displays About Us Info
	 */
	public function actionAboutUs()
	{
		$processOutput = true;

		if (Yii::app()->request->isAjaxRequest)
		{
			if (YII_DEBUG)
			{
				Yii::app()->clientscript->scriptMap['jquery.js'] = false;
				Yii::app()->clientScript->scriptMap['jquery-ui.js'] = false;
			}
			else
			{
				Yii::app()->clientscript->scriptMap['jquery.min.js'] = false;
				Yii::app()->clientScript->scriptMap['jquery-ui.min.js'] = false;
			}
		}

		$this->renderPartial('aboutUs',array(), false, $processOutput);
	}

	/**
	 * Displays Terms Info
	 */
	public function actionTerms()
	{
		$processOutput = true;
	
		if (Yii::app()->request->isAjaxRequest)
		{
			if (YII_DEBUG)
			{
				Yii::app()->clientscript->scriptMap['jquery.js'] = false;
				Yii::app()->clientScript->scriptMap['jquery-ui.js'] = false;
			}
			else
			{
				Yii::app()->clientscript->scriptMap['jquery.min.js'] = false;
				Yii::app()->clientScript->scriptMap['jquery-ui.min.js'] = false;
			}
		}
	
		$this->renderPartial('terms',array(), false, $processOutput);
	}
	
	/**
	 * Returns the HTML formatted Terms of Use for mobile
	 */
	public function actionGetTermsOfUse()
	{
		//Fb::warn("actionGetTermsOfUse() called", "SiteController");
		
		$mobileLang = null;
		$isTranslationRequired = false;
		
		if(isset($_REQUEST['language']))
		{
			$mobileLang = $_REQUEST['language'];
		}

		if($mobileLang == 'tr')
		{
			if(Yii::app()->language == 'tr')
			{
				$isTranslationRequired = false;
			}
			else
			{
				$isTranslationRequired = true;
			}
		}
		else
		{
			if(Yii::app()->language == 'tr')
			{
				$isTranslationRequired = true;
			}
			else
			{
				$isTranslationRequired = false;
			}
		}
		
		if($isTranslationRequired == true)
		{
			if($mobileLang == 'tr')
			{
				Yii::app()->language = 'tr';
			}
			else
			{
				Yii::app()->language = 'en';
			}
		}

		$htmlStr = "<html>\n\t<head>\n\t\t<meta http-equiv=\"Content-Type\" content=\"text/html; charset=UTF-8\" />\n\t</head>\n\t<body>\n\t".Yii::t('layout', 'Traceper Terms')."\n\t</body>\n</html>";
		
		if($isTranslationRequired == true) //Recover the language if needed for mobile
		{
			if($mobileLang == 'tr')
			{
				Yii::app()->language = 'en';
			}
			else
			{
				Yii::app()->language = 'tr';
			}
		}

		echo $htmlStr;
	}	

	public function actionGetWinDimensions()
	{
// 		Fb::warn("actionGetWinDimensions() called", "SiteController");
// 		Fb::warn($_POST['width'], "width");
// 		Fb::warn($_POST['height'], "height");

		unset(Yii::app()->session['usersPageSize']);
		
		Yii::app()->session['publicUploadsPageSize'] = (int)(($_POST['height'] - 150)/50);
		Yii::app()->session['uploadsPageSize'] = (int)(($_POST['height'] - 200)/50);
		Yii::app()->session['usersPageSize'] = (int)(($_POST['height'] - 180)/50);
		Yii::app()->session['groupsPageSize'] = (int)(($_POST['height'] - 75)/38);
		
// 		Fb::warn(Yii::app()->session['publicUploadsPageSize'], "publicUploadsPageSize");
// 		Fb::warn(Yii::app()->session['uploadsPageSize'], "uploadsPageSize");
//		Fb::warn(Yii::app()->session['usersPageSize'], "usersPageSize");
		
		echo json_encode(array('outcome'=>'success'));		
	}

	public function actionAjaxEmailNotification()
	{	
		//Fb::warn("actionAjaxEmailNotification() called", "SiteController");		

		$title = null;
		$message = null;
		$params = null;
		
		if(isset($_REQUEST['title']))
		{
			$title = $_REQUEST['title'];
		}		
	
		if(isset($_REQUEST['message']))
		{
			$message = $_REQUEST['message'];
		}

		if(isset($_REQUEST['params']))
		{
			$params = $_REQUEST['params'];
		}

		$this->sendErrorMail($params, $title, $message);

		//Yii::app()->end();
	}

	public function actionNullifyCountryNameSession()
	{
		//Fb::warn("actionNullifyCountryNameSession() called", "SiteController");
		
		Yii::app()->session['countryName'] = "null";
	}

	public function actionUpdateCountryNameSessionVar()
	{
		//Fb::warn("actionUpdateCountryNameSessionVar() called with ".$_POST['country'], "SiteController");
	
		Yii::app()->session['countryName'] = $_POST['country'];
	}

	public function actionRunDatabaseQueries()
	{
		Fb::warn("actionRunDatabaseQueries() called", "SiteController");
	
		$model = new DatabaseOperationsForm;
	
		$processOutput = true;
		// collect user input data
		if(isset($_POST['DatabaseOperationsForm']))
		{
			$model->attributes=$_POST['DatabaseOperationsForm'];
			// validate user input and if ok return json data and end application.
			if($model->validate()) {
				try
				{
					if($model->selectSql != null)
					{
						Fb::warn("selectSql is NOT null", "actionRunDatabaseQueries()");
							
						$queryResult = Yii::app()->db->createCommand($model->selectSql)->queryRow();
							
						Fb::warn($queryResult, "Select Query Result");
							
						echo CJSON::encode(array(
								"result"=> "Select",
								"queryResult"=>$queryResult
						));
					}
					else if($model->selectAllSql != null)
					{
						Fb::warn("selectAllSql is NOT null", "actionRunDatabaseQueries()");
							
						$queryResult = Yii::app()->db->createCommand($model->selectAllSql)->queryAll();
					
						Fb::warn($queryResult, "Select Query All Result");
					
						echo CJSON::encode(array(
								"result"=> "Select All",
								"queryResult"=>$queryResult
						));
					}
					else if($model->updateSql != null)
					{
						Fb::warn("updateSql is NOT null", "actionRunDatabaseQueries()");
					
						$numberOfEffectedRows = Yii::app()->db->createCommand($model->updateSql)->execute();
					
						Fb::warn($numberOfEffectedRows, "Updated Row Count");
					
						echo CJSON::encode(array(
								"result"=> "Update",
								"numberOfEffectedRows"=>$numberOfEffectedRows
						));
					}
					else if($model->deleteSql != null)
					{
						Fb::warn("deleteSql is NOT null", "actionRunDatabaseQueries()");
					
						$numberOfEffectedRows = Yii::app()->db->createCommand($model->deleteSql)->execute();
					
						Fb::warn($numberOfEffectedRows, "Deleted Row Count");
					
						echo CJSON::encode(array(
								"result"=> "Delete",
								"numberOfEffectedRows"=>$numberOfEffectedRows
						));
					}
					else
					{
						echo CJSON::encode(array(
								"result"=> "No query"
						));
					}					
				}
				catch(Exception $e)
				{
					$message = $e->getMessage();
					
					Fb::warn($message, "actionRunDatabaseQueries()");
					
					echo CJSON::encode(array(
							"result"=> "Error",
							"errorMessage"=>$message
					));					
				}

				Yii::app()->end();
			}
		}
	
		if (Yii::app()->request->isAjaxRequest)
		{
			if (YII_DEBUG)
			{
				Yii::app()->clientscript->scriptMap['jquery.js'] = false;
				Yii::app()->clientScript->scriptMap['jquery-ui.js'] = false;
			}
			else
			{
				Yii::app()->clientscript->scriptMap['jquery.min.js'] = false;
				Yii::app()->clientScript->scriptMap['jquery-ui.min.js'] = false;
			}
		}
	
		//Complete solution for blinking problem at FireFox
		if (Yii::app()->request->getIsAjaxRequest()) {
			Yii::app()->clientScript->scriptMap['*.js'] = false;
			Yii::app()->clientScript->scriptMap['*.css'] = false;
		}
	
		$this->renderPartial('databaseOperations',array('model'=>$model), false, $processOutput);
	}	
}



