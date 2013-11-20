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
						'actions'=>array('changePassword', 'inviteUser', 'registerGPSTracker'),
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
			
			$deviceId = null;
			$androidVer = null;
			$appVer = null;
			$preferredLanguage = null;

			$isRecordUpdateRequired = false;
			
			//*** Valid degilken bunları gondermeden de olabiliyorsa mobile bunları gondermeyelim?
			Users::model()->getLoginRequiredValues(Yii::app()->user->id, $minDataSentInterval, $minDistanceInterval, $facebookId, $autoSend, $deviceId, $androidVer, $appVer, $preferredLanguage);

			if($model->validate()) {								
				
				if(Users::model()->isTermsAccepted($model->email) === true)
				{
					if($model->login())
					{
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
								
							$this->renderPartial('loginSuccessful',array('id'=>Yii::app()->user->id, 'realname'=>$model->getName()), false, $processOutput);							
						}
						
						Yii::app()->end();						
					}
					else
					{
						if (isset($_REQUEST['client']) && $_REQUEST['client']=='mobile')
						{
							$result = "-2"; //Unknown login error

							echo CJSON::encode(array(
									"result"=> $result,
									"id"=>Yii::app()->user->id,
									"realname"=> $model->getName(),
									"minDataSentInterval"=> $minDataSentInterval,
									"minDistanceInterval"=> $minDistanceInterval,
									"facebookId"=> $facebookId,
									"autoSend "=> $autoSend
							));
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
						
						$result = "-3"; //Terms not accepted
						
						echo CJSON::encode(array(
								"result"=> $result,
								"id"=>Yii::app()->user->id,
								"realname"=> $model->getName(),
								"minDataSentInterval"=> $minDataSentInterval,
								"minDistanceInterval"=> $minDistanceInterval,
								"facebookId"=> $facebookId,
								"autoSend "=> $autoSend
						));						
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
							
						$this->renderPartial('acceptTermsForLogin',array('form'=>$_REQUEST['LoginForm']), false, true);						
					}										
				}											
			}
			else
			{
				//echo 'model NOT valid';

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
					else
					{
						$result = "-2"; //Unknown login error
					}					

					echo CJSON::encode(array(
							"result"=> $result,
							"id"=>Yii::app()->user->id,
							"realname"=> $model->getName(),
							"minDataSentInterval"=> $minDataSentInterval,
							"minDistanceInterval"=> $minDistanceInterval,
							"facebookId"=> $facebookId,
							"autoSend "=> $autoSend
					));
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
					
					$this->renderPartial('login',array('model'=>$model), false, $processOutput);
				}

				Yii::app()->end();
			}
		}
		else
		{
			//echo 'LoginForm NOT set';
			$this->renderPartial('login',array('model'=>$model), false, $processOutput);
		}
	}
	
	//Kullanim sartlari degisiminden once kaydolmus biri login olmaya calistiginda sartları kabul ederse bu fonksiyon ile isleme devam eder
	public function actionContinueLogin() {
		$model = new LoginForm;

		// collect user input data
		if(isset($_REQUEST['LoginForm']) && $_REQUEST['LoginForm'] != NULL)
		{
			$model->attributes = $_REQUEST['LoginForm'];
			
			$minDataSentInterval = Yii::app()->params->minDataSentInterval;
			$minDistanceInterval = Yii::app()->params->minDistanceInterval;
			$facebookId = 0;
			$autoSend = 0;
				
			$deviceId = null;
			$androidVer = null;
			$appVer = null;
			$preferredLanguage = null;
			
			$isRecordUpdateRequired = false;
				
			//*** Valid degilken bunları gondermeden de olabiliyorsa mobile bunları gondermeyelim, zaten daha login olunmadigi icin dogru degerler alinamayacak?
			Users::model()->getLoginRequiredValues(Yii::app()->user->id, $minDataSentInterval, $minDistanceInterval, $facebookId, $autoSend, $deviceId, $androidVer, $appVer, $preferredLanguage);			
			
			Users::model()->setTermsAccepted($model->email);
			
			//model daha once validate edildigi icin bir daha validate etmeye gerek yok
			if($model->login())
			{
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
			
					$this->renderPartial('loginSuccessful',array('id'=>Yii::app()->user->id, 'realname'=>$model->getName()), false, true);
				}
			
				Yii::app()->end();
			}
			else
			{
				if (isset($_REQUEST['client']) && $_REQUEST['client']=='mobile')
				{
					$result = "-2"; //Unknown login error
			
					echo CJSON::encode(array(
							"result"=> $result,
							"id"=>Yii::app()->user->id,
							"realname"=> $model->getName(),
							"minDataSentInterval"=> $minDataSentInterval,
							"minDistanceInterval"=> $minDistanceInterval,
							"facebookId"=> $facebookId,
							"autoSend "=> $autoSend
					));
				}
				else {
					Yii::app()->end();
				}			
			}
		}						
	}	

	/**
	 *
	 * facebook login action
	 */
	public function actionFacebooklogin() {
		Yii::import('ext.facebook.*');
		$ui = new FacebookUserIdentity('370934372924974', 'c1e85ad2e617b480b69a8e14cfdd16c7');

		if ($ui->authenticate()) {
			$user=Yii::app()->user;
			$user->login($ui);

			$this->FB_Web_Register($nd);
			if($nd == 0)
			{					
				$str=array("email" => Yii::app()->session['facebook_user']['email'] ,"password" => Yii::app()->session['facebook_user']['id']) ;
					
				$this->fbLogin($str);
					
			}else {

			}

			//exit;
			$this->redirect($user->returnUrl);
		} else {
			throw new CHttpException(401, $ui->error);
		}
	}

	/**
	 * Logs out the current user and redirect to homepage.
	 */
	public function actionLogout()
	{
		Yii::app()->user->logout(false);
		session_destroy();
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
				else
				{
					$result = "-3"; //Unknown form error
				}													
			}
		}
		else
		{
			//Update results to be used in mobile
			
			$result = "-4"; //Form missing					
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
			
			Fb::warn("token:".$token, "SiteController - actionResetPassword()");
			
			if($model->validate()) {
				if(Users::model()->changePassword(Users::model()->getUserId(ResetPassword::model()->getEmailByToken($token)), $model->newPassword)) // save the change to database
				{
					ResetPassword::model()->deleteToken($token);

// 					Yii::app()->clientScript->scriptMap['jquery.js'] = false;
// 					Yii::app()->clientScript->scriptMap['jquery-ui.min.js'] = false;
// 					$this->renderPartial('resetPasswordSuccessful',array(), false, $processOutput);
					
// 					Yii::app()->end();

					echo CJSON::encode(array("result"=>"1"));
				}
				else
				{				
					//Fb::warn("An error occured while changing your password!", "SiteController - actionResetPassword()");

					echo CJSON::encode(array("result"=>"0"));
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

// 	public function actionResetPassword2()
// 	{
// 		$result = "Sorry, you entered this page with wrong parameters";
// 		$tokenNotGiven = false;
// 		$tokenNotFound = false;
		
// 		$model = new ResetPasswordForm;
// 		$processOutput = true;
		
// 		if (isset($_GET['tok']) && $_GET['tok'] != null)
// 		{
// 			$token = $_GET['tok'];

// 			// collect user input data
// 			if(isset($_POST['ResetPasswordForm']))
// 			{
// 				$model->attributes = $_POST['ResetPasswordForm'];
// 				// validate user input and if ok return json data and end application.
				
// 				if($model->validate()) {
// // 					if(Users::model()->changePassword(Users::model()->getUserId(ResetPassword::model()->getEmailByToken($token)), $model->newPassword)) // save the change to database
// // 					{
// // 						echo CJSON::encode(array("result"=> "1")); //Password Changed
// // 						ResetPassword::model()->deleteToken($token);
// // 					}
// // 					else
// // 					{
// // 						echo CJSON::encode(array("result"=> "0")); //Password Not Chaged
// // 					}
// 					//var_dump($model->getErrors());
					
// 					echo CJSON::encode(array("result"=> "1")); //Password Changed
					
//  					Yii::app()->end();
// 				}
				
// 				//print_r($model->getErrors());
					
// 				if (Yii::app()->request->isAjaxRequest) {
// 					$processOutput = false;							
// 				}
// 			}
// 			else
// 			{
// 				if(ResetPassword::model()->tokenExists($token) == false)	
// 				{
// 					$tokenNotFound = true;
// 				}				
// 			}
// 		}
// 		else 
// 		{
// 			$tokenNotGiven = true;
// 		}
	
// 		if($tokenNotGiven)
// 		{
// 			$result = Yii::t('site', 'Sorry, you entered this page with wrong parameters...'); 
// 			$this->renderPartial('errorInPage',array('result'=>$result), false, true);
// 		}
// 		else if($tokenNotFound)
// 		{
// 			$result = Yii::t('site', 'This link is not valid anymore...');
// 			$this->renderPartial('errorInPage',array('result'=>$result), false, true);
// 		}
// 		else
// 		{
// // 			Yii::app()->clientScript->scriptMap['jquery.js'] = false;
// // 			Yii::app()->clientScript->scriptMap['jquery-ui.min.js'] = false;			
			
// 			$this->renderPartial('resetPassword2',array('model'=>$model), false, $processOutput);
			
// 			//$this->render('resetPassword2',array('model'=>$model), false);
// 		}
// 	}

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
			
				if($model->getError('email') == Yii::t('site', 'You are already registered!'))
				{
					$result = "-1";
				}
				else if($model->getError('email') == Yii::t('site', 'There has been a problem with your registration process. Please try to sign up for Traceper again.'))
				{
					$result = "-2";
				}
				else
				{
					$result = "-3"; //Unknown form error
				}
			}			
		}
		else
		{
			//Update results to be used in mobile
				
			$result = "-4"; //Form missing
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
		
		// collect user input data
		if(isset($_REQUEST['RegisterForm']))
		{
			$model->attributes = $_REQUEST['RegisterForm'];
		
			// validate user input and if ok return json data and end application.
		
			// 			if (Yii::app()->request->isAjaxRequest) {
			// 				$processOutput = false;
			// 			}
		
			if($model->validate()) {
		
				$time = date('Y-m-d h:i:s');
		
				//echo $model->ac_id;
				
				$registrationMedium = null;
				
				if (isset($_REQUEST['client']) && $_REQUEST['client']=='mobile')
				{
					$registrationMedium = 'Mobile';
				}
				else
				{
					$registrationMedium = 'Web';
				}				
		
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
		
					echo $message;

					if($this->SMTP_UTF8_mail(Yii::app()->params->noreplyEmail, 'Traceper', $model->email, trim($model->name).' '.trim($model->lastName), Yii::t('site', 'Traceper Activation'), $message))
					{
						echo CJSON::encode(array("result"=>"1", "email"=>$model->email));
						//Yii::app()->end();					
					}
					else
					{
						echo CJSON::encode(array("result"=>"2"));
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

					Yii::app()->end();
				}
				else
				{
					echo JSON::encode(array("result"=>"0")); //Error in saving
					Yii::app()->end();
				}
		
				Yii::app()->end();
			}
			else
			{
				if (isset($_REQUEST['client']) && $_REQUEST['client']=='mobile')
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
					else if($model->getError('email') != null)
					{
						$result = $model->getError('email') ;
					}
					else
					{
						$result = "-3"; //Unkown registration errror
					}
		
					echo CJSON::encode(array(
							"result"=> $result,
					));
				}
				else
				{
					//echo 'RegisterForm not valid';
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
		echo CJSON::encode(array(
				"result"=> $result,
		));
	}

	//facebook web register
	public function FB_Web_Register()
	{
		$result = 0;
			
		// validate user input and if ok return json data and end application.
		if(Yii::app()->session['facebook_user']) {

			if (Users::model()->saveFacebookUser(Yii::app()->session['facebook_user']['email'], md5(Yii::app()->session['facebook_user']['id']), Yii::app()->session['facebook_user']['name'], Yii::app()->session['facebook_user']['id'], 1))
			{
				$result = 1;
			}
			else
			{
				$result = 0;
			}

		}
		return $result;
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
							$result .= Yii::t('site', 'You have signed up via our web site, so it is possible that you have not installed our mobile application. If so, you could not provide location information without using the mobile app. Therefore we strongly recommend you to download and install our mobile app at Google Play. You could find the app link just below the "Sign Up" form. After logging into mobile app, you and your friends could see your location on the map.');							
						}
						else
						{
							$result = Yii::t('site', 'Your account has been activated successfully, you can login now...').'</br></br>';
							$result .= Yii::t('site', 'You should login at mobile app in order to provide your location info. On the other hand, you could also use our web site for various common operations in addition to viewing the shared photos and creating friend groups which are available for only web site at the moment.');							
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
		
		//Bununla tum site render ediliyor sonra da $content degiskeninde tutulan messageDialoh view'ı render ediliyor
		$this->render('messageDialog', array('result'=>$result, 'title'=>Yii::t('site', 'Account Activation')), false);
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
}



