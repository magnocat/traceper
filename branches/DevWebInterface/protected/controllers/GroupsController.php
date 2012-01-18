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
	
 	public function filters()
    {
        return array(
            'accessControl',
        );
    }
	
	public function accessRules()
    {
    	//TODO: actionUpload can be added list below after mobile app is able to login the framework
        return array(
        	array('deny',
                'actions'=>array('createGroup', 'updateGroup', 'addUserToGroup'),
        		'users'=>array('?'),
            )
        );
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
	 * Creates new group
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
		}	
			
		Yii::app()->clientScript->scriptMap['jquery.js'] = false;
		Yii::app()->clientScript->scriptMap['jquery-ui.min.js'] = false;			
		
		$this->renderPartial('createGroup',array('model'=>$model), false, $processOutput);
	}
	
	/**
	 * Updates new group
	 */
	public function actionUpdateGroup()
	{
		$model = new GroupSettingsForm;

		$processOutput = true;		
		$groupsOfUser = Groups::model()->findAll('owner=:owner', array(':owner'=>Yii::app()->user->id));
		
		if (isset($_REQUEST['friendId']))
		{

			$friendId = (int) $_REQUEST['friendId'];
			
			$relationRowsSelectedFriendBelongsTo = UserGroupRelation::model()->findAll('userId=:userId', array(':userId'=>$friendId));			
		}		
		
		if(isset($_POST['GroupSettingsForm']))
		{
			echo 'SET';
			
			$model->attributes=$_POST['GroupSettingsForm'];
			
			if($model->validate()) 
			{
				echo CJSON::encode(array("result"=> "1"));

				Yii::app()->end();
			}
			
			if(Yii::app()->request->isAjaxRequest) 
			{
				$processOutput = false;	
				echo ' Ajax';
			}			
		}
		else
		{
			echo 'ELSE';
			
			$selected_groups=array();
	
			$selected_groups[]=8;
			$selected_groups[]=9;
			$selected_groups[]=10;
			$model->groupStatusArray=$selected_groups;			
		}	

		
		

	
		
		
		// collect user input data
//		if(isset($_POST['GroupSettingsForm']))
//		{
//			$model->attributes = $_POST['GroupSettingsForm'];
//			// validate user input and if ok return json data and end application.
//			if($model->validate()) {
//
//			$message = "You selected: ";  
//			$loopCount = 0;
//			foreach($model->groupStatusArray as $selectedItem)
//			{
//			     $message .= strval($selectedItem);
//			     //if(++$loopCount < count($_POST['myCheckBoxList']))   
//			          $message .=  " & ";
//            }				
//
//			}
//		}
			
		Yii::app()->clientScript->scriptMap['jquery.js'] = false;
		Yii::app()->clientScript->scriptMap['jquery-ui.min.js'] = false;			
		
		//$this->renderPartial('groupSettings',array('model'=>$model, 'groupsOfUser'=>$groupsOfUser, 'relationRowsSelectedFriendBelongsTo'=>$relationRowsSelectedFriendBelongsTo, 'friendId'=>$friendId), false, $processOutput);
		$this->renderPartial('groupSettings',array('model'=>$model, 'groupsOfUser'=>$groupsOfUser, 'friendId'=>$friendId), false, $processOutput);
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



