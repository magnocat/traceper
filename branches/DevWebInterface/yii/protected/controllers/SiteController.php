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
	 */
	public function actionLogin()
	{
		$model = new LoginForm;

		$processOutput = true;
		// collect user input data
		if(isset($_POST['LoginForm']))
		{
			$model->attributes=$_POST['LoginForm'];
			// validate user input and if ok return json data and end application.
			if($model->validate() && $model->login()) {
				echo CJSON::encode(array(
								"result"=> "1",
								"realname"=>$model->getName(),
				));
				Yii::app()->end();
			}
				
			if (Yii::app()->request->isAjaxRequest) {
				$processOutput = false;

			}
		}

		Yii::app()->clientScript->scriptMap['jquery.js'] = false;
		Yii::app()->clientScript->scriptMap['jquery-ui.min.js'] = false;

		$this->renderPartial('login',array('model'=>$model), false, $processOutput);
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
				if(Users::model()->updateByPk(Yii::app()->user->id,array("password"=>md5($model->newPassword)),'password=:password', array(':password'=> md5($model->currentPassword)))) // save the change to database
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
}