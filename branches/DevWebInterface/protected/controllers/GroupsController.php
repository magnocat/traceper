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
			
			if(Yii::app()->request->isAjaxRequest) 
			{
				$processOutput = false;
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
		//Get all of the groups that the logged in user has created
		$groupsOfUser = Groups::model()->findAll('owner=:owner', array(':owner'=>Yii::app()->user->id));
		
		$selected_groups=array(); //array to hold all the groups of the user's selected friend to be used for the checking the checkboxes in the initial dialog
		$unselected_groups=array(); //array to hold the unselected user groups so that it can be used for user removal from unselected groups
		
		if (isset($_REQUEST['friendId']))
		{
			$friendId = (int)$_REQUEST['friendId'];			
			//Take all the user-group relation rows that the user's friend added to
			$relationRowsSelectedFriendBelongsTo = UserGroupRelation::model()->findAll('userId=:userId', array(':userId'=>$friendId));
			
			//Get only the group ID fields into $selected_groups from the obtained rows
			foreach($relationRowsSelectedFriendBelongsTo as $relationRow)
			{
			    $selected_groups[] = $relationRow->groupId;
			}
		}		
		
		if(isset($_POST['GroupSettingsForm']))
		{
			//echo 'SET';
			
			$model->attributes = $_POST['GroupSettingsForm'];
			//$model->groupStatusArray = $_POST['GroupSettingsForm']['groupStatusArray'];
			
			if($model->validate()) 
			{
//				if(!empty($model->groupStatusArray)) //Check empty() in order to avoid error in foreach
//				{
//					echo 'Selecteds: ';
//					
//					foreach($model->groupStatusArray as $selectedItem)
//					{
//						echo $selectedItem.' ';
//					}
//
//					echo '</br>';
//				}
				
				//We know the selected groups from $model->groupStatusArray, but now know the unselected ones
				//So construct the unselected groups using $groupsOfUser and $model->groupStatusArray 
				foreach($groupsOfUser as $selectedOwnerGroup) //Check for all of the user's groups
				{
					$groupFound = false;
					
					if(!empty($model->groupStatusArray)) //Check empty() in order to avoid error in foreach
					{
						foreach($model->groupStatusArray as $selectedFriendGroup) //Check for all of the checked groups
						{
							if($selectedOwnerGroup->id == $selectedFriendGroup)
							{
								$groupFound = true;
								break;
							}					
						}					
					}
					else //None of the checkboxes selected so all of the user groups will be in $unselected_groups
					{
					
					}

					
					if($groupFound == false)
					{
						$unselected_groups[] = $selectedOwnerGroup->id;
					}
				}

//				if(!empty($unselected_groups)) //Check empty() in order to avoid error in foreach
//				{
//					echo 'UnSelecteds: ';
//					
//					foreach($unselected_groups as $unselectedItem)
//					{
//						echo $unselectedItem.' ';
//					}	
//
//					echo '</br>';
//				}				
				
				//Check whether the friend belongs to the selected group or not before. If he does not, add his relation to the traceper_user_group_relation table				
				if(!empty($model->groupStatusArray)) //Check empty() in order to avoid error in foreach
				{
					foreach($model->groupStatusArray as $selectedFriendGroup) //Check for all of the checked groups
					{
						$relationQueryResult = UserGroupRelation::model()->find(array('condition'=>'userId=:userId AND groupId=:groupId', 
						                                                     'params'=>array(':userId'=>$friendId, 
						                                                                     ':groupId'=>$selectedFriendGroup
						                                                                     )
						                                                     )
						                                               );
	
					    if($relationQueryResult != null) 
					    {
					    	//traceper_user_group_relation table already has the desired relation, so do nothing
					    	
					    	//echo 'Relation already exists for groupId '.$selectedFriendGroup.'</br>';
					    }
					    else
					    {
							$userGroupRelation = new UserGroupRelation;
							$userGroupRelation->userId = $friendId;
							$userGroupRelation->groupId = $selectedFriendGroup;
	
							if($userGroupRelation->save()) // save the change to database
							{
								//Relation added to the traceper_user_group_relation table
								
								//echo 'Relation added for groupId '.$selectedFriendGroup.'</br>';
							}
							else
							{
								echo CJSON::encode(array("result"=> "Unknown error"));
								Yii::app()->end();
							}
					    } 				
					}				
				}

				
				
				//Check whether the friend belongs to the unselected group or not. If he does, delete his relation from the traceper_user_group_relation table
				foreach($unselected_groups as $unselectedFriendGroup) //Check for all of the checked groups
				{
					$relationQueryResult = UserGroupRelation::model()->find(array('condition'=>'userId=:userId AND groupId=:groupId', 
					                                                     'params'=>array(':userId'=>$friendId, 
					                                                                     ':groupId'=>$unselectedFriendGroup
					                                                                     )
					                                                     )
					                                               );

				    if($relationQueryResult != null) 
				    {
						if($relationQueryResult->delete()) // delete the undesired subscription
						{
							//Relation deleted from the traceper_user_group_relation table
							
							//echo 'Relation deleted for groupId '.$unselectedFriendGroup.'</br>';
							
							
						}
						else
						{
							echo CJSON::encode(array("result"=> "Unknown error"));
							Yii::app()->end();
						}				    					    	
				    }
				    else
				    {
						//traceper_user_group_relation table has not the desired relation, so do nothing
						
				    	//echo 'Relation does not already exist for groupId '.$unselectedFriendGroup.'</br>';
				    } 				
				}				
				
				echo CJSON::encode(array("result"=> "1"));
				Yii::app()->end();
			}
			
			if(Yii::app()->request->isAjaxRequest) 
			{
				$processOutput = false;	
				//echo ' Ajax';
			}			
		}
		else
		{
			//echo 'ELSE';

			//If the form is opened, check the checkboxes according to traceper_user_group_relation table
			//Here $model->groupStatusArray is assigned to all of the groups that the user's friend is resgistered no matter the groups' owner is the logged user or not
			$model->groupStatusArray=$selected_groups;			
		}	

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



