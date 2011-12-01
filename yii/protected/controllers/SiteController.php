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
			'captcha'=>array(
				'class'=>'CCaptchaAction',
				'backColor'=>0xFFFFFF,
		),
		// page action renders "static" pages stored under 'protected/views/site/pages'
		// They can be accessed via: index.php?r=site/page&view=FileName
			'page'=>array(
				'class'=>'CViewAction',
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

	/**
	 * Displays the contact page
	 */
	public function actionContact()
	{
		$model=new ContactForm;
		if(isset($_POST['ContactForm']))
		{
			$model->attributes=$_POST['ContactForm'];
			if($model->validate())
			{
				$headers="From: {$model->email}\r\nReply-To: {$model->email}";
				mail(Yii::app()->params['adminEmail'],$model->subject,$model->body,$headers);
				Yii::app()->user->setFlash('contact','Thank you for contacting us. We will respond to you as soon as possible.');
				$this->refresh();
			}
		}
		$this->render('contact',array('model'=>$model));
	}

	/**
	 * Displays the login page,
	 * If there is an error in validation or parameters it returns the form code with errors
	 * if everything is ok, it returns JSON with result=>1 and realname=>"..." parameters
	 * ATTENTION: This function is also used by mobile clients
	 */
	public function actionLogin()
	{
		$model = new LoginForm;

		$processOutput = true;
		// collect user input data
		if(isset($_POST['LoginForm']))
		{
			$model->attributes = $_POST['LoginForm'];
			// validate user input and if ok return json data and end application.
			if($model->validate() && $model->login()) {
				echo CJSON::encode(array(
								"result"=> "1",
								"realname"=> $model->getName(),
								"minDataSentInterval"=> Yii::app()->params->minDataSentInterval,
								"minDistanceInterval"=> Yii::app()->params->minDistanceInterval,
				));
				Yii::app()->end();
			}
			if (Yii::app()->request->isAjaxRequest) {
				$processOutput = false;

			}
		}

		if (isset($_REQUEST['client']) && $_REQUEST['client']=='mobile')
		{
				
			if ($model->getError('password') != null) {
				$result = $model->getError('password');
			}
			else if ($model->getError('email') != null) {
				$result = $model->getError('email');
			}
			else if ($model->getError('rememberMe') != null) {
				$result = $model->getError('rememberMe');
			}
				
			echo CJSON::encode(array(
								"result"=> $result,
			));
			Yii::app()->end();
		}
		else {
			Yii::app()->clientScript->scriptMap['jquery.js'] = false;
			Yii::app()->clientScript->scriptMap['jquery-ui.min.js'] = false;
			$this->renderPartial('login',array('model'=>$model), false, $processOutput);
		}
	}

	/**
	 * Logs out the current user and redirect to homepage.
	 */
	public function actionLogout()
	{
		Yii::app()->user->logout();
		$this->redirect(Yii::app()->homeUrl);
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
				if(Users::model()->updateByPk(Yii::app()->user->id, array("password"=>md5($model->newPassword)))) // save the change to database
				{
					echo CJSON::encode(array("result"=> "1"));
				}
				else
				{
					echo CJSON::encode(array("result"=> "0"));
				}
				Yii::app()->end();
			}

			if (Yii::app()->request->isAjaxRequest) {
				$processOutput = false;

			}
		}

		Yii::app()->clientScript->scriptMap['jquery.js'] = false;
		Yii::app()->clientScript->scriptMap['jquery-ui.min.js'] = false;

		$this->renderPartial('changePassword',array('model'=>$model), false, $processOutput);
	}

	public function actionRegister()
	{
		$model = new RegisterForm;

		$processOutput = true;
		// collect user input data
		if(isset($_POST['RegisterForm']))
		{
			$isMobileClient = false;
			if (isset($_REQUEST['client']) && $_REQUEST['client']=='mobile') {
				$isMobileClient = true;
			}
			$model->attributes = $_POST['RegisterForm'];
			// validate user input and if ok return json data and end application.
			if($model->validate()) {

				$time = date('Y-m-d h:i:s');

				$userCandidates = new UserCandidates;
				$userCandidates->email = $model->email;
				$userCandidates->realname = $model->name;
				$userCandidates->password = md5($model->password);
				$userCandidates->time = $time;
				$userCandidates->save();

				if($userCandidates->save()) // save the change to database
				{
					$key = md5($model->email.$time);
					$message = 'Hi '.$model->name.',<br/> <a href="'.$this->createUrl('site/activate',array('email'=>$model->email,'key'=>$key)).'">'.
					'Click here to register to traceper</a> <br/>';										
					$message .= '<br/> Your Password is :'.$model->password;
					$message .= '<br/> The Traceper Team';
					$headers  = 'MIME-Version: 1.0' . "\r\n";
					$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
					$headers  .= 'From: '. Yii::app()->params->contactEmail .'' . "\r\n";
					//echo $message;
					mail($model->email, "Traceper Activation", $message, $headers);
					echo CJSON::encode(array("result"=> "1"));
				}
				else
				{
					echo CJSON::encode(array("result"=> "Unknown error"));
				}
				Yii::app()->end();
			}

			if (Yii::app()->request->isAjaxRequest) {
				$processOutput = false;

			}
		}

		if ($isMobileClient == true)
		{
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
			Yii::app()->clientScript->scriptMap['jquery.js'] = false;
			Yii::app()->clientScript->scriptMap['jquery-ui.min.js'] = false;

			$this->renderPartial('register',array('model'=>$model), false, $processOutput);
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

				$emailArray= $this->splitEmails($model->emails);
				$arrayLength = count($emailArray);
				$invitationSentCount = 0;
				for ($i = 0; $i < $arrayLength; $i++)
				{
					$dt = date("Y-m-d H:m:s");
						
					$invitedUsers = new InvitedUsers;
					$invitedUsers->email = $emailArray[$i];
					$invitedUsers->dt = $dt;
						
					if ($invitedUsers->save())
					{
						$key = md5($emailArray[$i].$dt);
						//send invitation mail
						$invitationSentCount++;

						//Invitation kontrol� yap�ld���nda bu k�s�m a��lacak

						//$message = 'Hi ,<br/> You have been invited to traceper by one of your friends <a href="'.$this->createUrl('site/register',array('invitation'=>true, 'email'=>$emailArray[$i],'key'=>$key)).'">'.
						//'Click here to register to traceper</a> <br/>';

						$message = 'Hi ,<br/> You have been invited to traceper by one of your friends <a href="'.$this->createUrl('site/register').'">'.
						'Click here to register to traceper</a> <br/>';
						$message .= '<br/> ' . $model->message;
						$message .= '<br/> The Traceper Team';
						$headers  = 'MIME-Version: 1.0' . "\r\n";
						$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
						$headers  .= 'From: contact@traceper.com' . "\r\n";
						//echo $message;
						mail($emailArray[$i], "Traceper Invitation", $message, $headers);
					}
				}

				if ($arrayLength == $invitationSentCount) // save the change to database
				{
					echo CJSON::encode(array("result"=> "1"));
				}
				else
				{
					echo CJSON::encode(array("result"=> "0"));
				}
				Yii::app()->end();
			}

			if (Yii::app()->request->isAjaxRequest) {
				$processOutput = false;

			}
		}

		Yii::app()->clientScript->scriptMap['jquery.js'] = false;
		Yii::app()->clientScript->scriptMap['jquery-ui.min.js'] = false;

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
		$email = $_GET['email'];
		$key = $_GET['key'];

		$processOutput = true;
		// collect user input data

		$criteria=new CDbCriteria;
		$criteria->select='Id,email,realname,password,time';
		$criteria->condition='email=:email';
		$criteria->params=array(':email'=>$email);
		$userCandidate = UserCandidates::model()->find($criteria); // $params is not needed

		$generatedKey =  md5($email.$userCandidate->time);

		if ($generatedKey == $key)
		{
			$users = new Users;
			$users->email = $userCandidate->email;
			$users->realname = $userCandidate->realname;
			$users->password = $userCandidate->password;

			if($users->save())
			{
				$userCandidate->delete();
				echo CJSON::encode(array("result"=> "1"));
			}
		}
		else
		{
			echo CJSON::encode(array("result"=> "0"));
		}

		Yii::app()->end();

		//G�rsel d�zenlemeler s�ras�nda i�lem ba�ar�l� oldu�unda i�lemin tamamland���na dair mesaj ��kacak ve kullan�c� login olmu� olacak

		//		if (Yii::app()->request->isAjaxRequest) {
		//			$processOutput = false;
		//		}
		//
		//		Yii::app()->clientScript->scriptMap['jquery.js'] = false;
		//		Yii::app()->clientScript->scriptMap['jquery-ui.min.js'] = false;
		//
		//		$this->renderPartial('activate',array('model'=>$model), false, $processOutput);
	}
}



