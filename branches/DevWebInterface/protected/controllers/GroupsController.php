<?php

class GroupsController extends Controller
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
	 * Displays the create group page,
	 * If there is an error in validation or parameters it returns the form code with errors
	 * if everything is ok, it returns JSON with result=>1 and realname=>"..." parameters
	 */
	public function actionCreateGroup()
	{
		$model = new NewGroupForm;

		$processOutput = true;
		// collect user input data
		if(isset($_POST['NewGroupForm']))
		{
			$model->attributes = $_POST['NewGroupForm'];
			// validate user input and if ok return json data and end application.
			if($model->validate()) {

				$groups = new Groups;
				$groups->name = $model->name;
				$groups->owner = Yii::app()->user->id;
				$groups->description = $model->description;
				$groups->save();

				if($groups->save()) // save the change to database
				{
					echo CJSON::encode(array("result"=> "1"));
				}
				else
				{
					echo CJSON::encode(array("result"=> "Unknown error"));
				}
				Yii::app()->end();
			}
			
			//$this->renderPartial('newGroup',array('model'=>$model), false, $processOutput);
	}
	
		/**
	 * Displays the create group page,
	 * If there is an error in validation or parameters it returns the form code with errors
	 * if everything is ok, it returns JSON with result=>1 and realname=>"..." parameters
	 */
	public function actionAddUserToGroup()
	{
		$model = new UpdateGroupForm;

		$processOutput = true;
		// collect user input data
		if(isset($_POST['UpdateGroupForm']))
		{
			$model->attributes = $_POST['UpdateGroupForm'];
			// validate user input and if ok return json data and end application.
			if($model->validate()) {
				$group = Groups::model()->findByPk($model->groupId);
				
				//Add user to the group, if the adder is the user owner
				if($group->owner == Yii::app()->user->id)
				{
					$userGroupRelation = new UserGroupRelation;
					//$user=Users::model()->find('email=:email', array(':email'=>$model->email));				
					$userGroupRelation->userId = $model->userId;
					//$group=Groups::model()->find('groupName=:groupName', array(':groupName'=>$model->groupName));				
					$userGroupRelation->groupId = $model->groupId;
					$userGroupRelation->save();
	
					if($groups->save()) // save the change to database
					{
						echo CJSON::encode(array("result"=> "1"));
					}
					else
					{
						echo CJSON::encode(array("result"=> "Unknown error"));
					}					
				}
				else
				{
					echo CJSON::encode(array("result"=> "Unknown error"));
				}
			}
				
			Yii::app()->end();
		}
		
		//$this->renderPartial('groupInfo',array('model'=>$model), false, $processOutput);
	}	
}



