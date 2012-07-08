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
                'actions'=>array('createGroup', 'updateGroup', 'getGroupList', 'getGroupMembers', 'deleteGroup', 'deleteGroupMember', 'setPrivacyRights'),
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
				try
				{
					if(PrivacyGroups::model()->saveGroup($model->name, $model->groupType, Yii::app()->user->id, $model->description)) // save the change to database
					{
						echo CJSON::encode(array("result"=> "1"));
					}
					else
					{
						echo CJSON::encode(array("result"=> "Unknown error"));
					}
					Yii::app()->end();				
				}
				catch (Exception $e) 
				{
					if($e->getCode() == Yii::app()->params->duplicateEntryDbExceptionCode) //Duplicate Entry
					{
						echo CJSON::encode(array("result"=> "Duplicate Entry"));
					}
					Yii::app()->end();
					
//					echo 'Caught exception: ',  $e->getMessage(), "\n";    				
//    				echo 'Code: ', $e->getCode(), "\n";
				}
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
	 * Updates user-group relations. Inserts the selected friend into the selected groups or remove the selected friend
	 * from unselected groups
	 */
	public function actionUpdateGroup()
	{
		$model = new GroupSettingsForm;
		
		$groupType = GroupType::FriendGroup;
		
		if (isset($_REQUEST['groupType']))
		{
			$groupType = (int)$_REQUEST['groupType'];
		}

		$processOutput = true;		
		//Get all of the groups that the logged in user has created
		$groupsOfUser = PrivacyGroups::model()->findAll('owner=:owner AND type=:type', array(':owner'=>Yii::app()->user->id, ':type'=>$groupType));
		
		$selected_groups=array(); //array to hold all the groups of the user's selected friend to be used for the checking the checkboxes in the initial dialog
		$unselected_groups=array(); //array to hold the unselected user groups so that it can be used for user removal from unselected groups
		
		if (isset($_REQUEST['friendId']))
		{
			$friendId = (int)$_REQUEST['friendId'];			
			//Take all the user-group relation rows that the user's friend added to
			$relationRowsSelectedFriendBelongsTo = UserPrivacyGroupRelation::model()->findAll('userId=:userId AND groupOwner=:groupOwner', array(':userId'=>$friendId, ':groupOwner'=>Yii::app()->user->id));
			
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
				
				//We know the selected groups from $model->groupStatusArray, but not know the unselected ones
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
								
				
				//Check for privacy groups
				if(count($model->groupStatusArray) > 1)
				{
					echo CJSON::encode(array("result"=> "Duplicate Entry"));
					Yii::app()->end();				
				}
				else
				{
					//First process the unselected groups for deletion, because if you process the selected groups first, there may be db exceptions at privacy groupings
	
					//Check whether the friend belongs to the unselected group or not. If he does, delete his relation from the traceper_user_group_relation table
					foreach($unselected_groups as $unselectedFriendGroup) //Check for all of the checked groups
					{
						$relationQueryResult = UserPrivacyGroupRelation::model()->deleteGroupMember($friendId,$unselectedFriendGroup);
						if($relationQueryResult == 1)
						{
							//Relation deleted from the traceper_user_group_relation table
						}
						elseif ($relationQueryResult == 0)
						{
							echo CJSON::encode(array("result"=> "Unknown error"));
							Yii::app()->end();
						}
						else
						{
							//traceper_user_group_relation table has not the desired relation, so do nothing
						}
					}
					
					//Check whether the friend belongs to the selected group or not before. If he does not, add his relation to the traceper_user_group_relation table				
					if(!empty($model->groupStatusArray)) //Check empty() in order to avoid error in foreach
					{
						foreach($model->groupStatusArray as $selectedFriendGroup) //Check for all of the checked groups
						{
							$relationQueryResult = UserPrivacyGroupRelation::model()->find(array('condition'=>'userId=:userId AND groupId=:groupId', 
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
								try
								{
									if(UserPrivacyGroupRelation::model()->saveGroupRelation($friendId, $selectedFriendGroup, Yii::app()->user->id)) // save the change to database
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
						    	catch (Exception $e) 
								{
									if($e->getCode() == Yii::app()->params->duplicateEntryDbExceptionCode) //Duplicate Entry
									{
										echo CJSON::encode(array("result"=> "Duplicate Entry"));
									}
									Yii::app()->end();
									
				//					echo 'Caught exception: ',  $e->getMessage(), "\n";    				
				//    				echo 'Code: ', $e->getCode(), "\n";
								}
						    } 				
						}				
					}				
					
					echo CJSON::encode(array("result"=> "1"));
					Yii::app()->end();				
				}	


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
	
	
	//Gets the all of the groups that the logged in user owns
	public function actionGetGroupList()
	{
		$groupType = GroupType::FriendGroup;
		
		if (isset($_GET['groupType']) && $_GET['groupType'] != NULL)
		{
			$groupType = $_GET['groupType'];
		}		
		
		$dataProvider = null;
		if(Yii::app()->user->id != null)
		{			
			$dataProvider=PrivacyGroups::model()->getGroupsList(Yii::app()->user->id, $groupType, Yii::app()->params->itemCountInOnePage);			
		}
		
		//echo 'groupsInfo called'.date("Y-m-d H:i:s");

		Yii::app()->clientScript->scriptMap['jquery.js'] = false;
		//TODO: added below line because gridview.js is loaded before.
		Yii::app()->clientScript->scriptMap['jquery.yiigridview.js'] = false;
		$this->renderPartial('groupsInfo',array('dataProvider'=>$dataProvider,'model'=>new SearchForm(), 'groupType'=>$groupType), false, true);
	}
	
	//Gets the list of the selected froup members
	public function actionGetGroupMembers()
	{
		if(isset($_REQUEST['groupId']))
		{
			$groupId = (int)$_REQUEST['groupId'];
		}		
		
		$dataProvider = null;
		
		if(Yii::app()->user->id != null)
		{
			$count=UserPrivacyGroupRelation::model()->getGroupMembersCount($groupId);
			
			$sql = 'SELECT u.Id as id, u.realname as Name, ugr.groupId, ugr.userId 	   
					FROM '. UserPrivacyGroupRelation::model()->tableName() . ' ugr 
					LEFT JOIN ' . Users::model()->tableName() . ' u
						ON u.Id = ugr.userId
					WHERE groupId='.$groupId;
	
			$dataProvider = new CSqlDataProvider($sql, array(
			    											'totalItemCount'=>$count,		
														    'pagination'=>array(
														        'pageSize'=>Yii::app()->params->itemCountInOnePage,
			),
			));
			
		}

		Yii::app()->clientScript->scriptMap['jquery.js'] = false;
		//TODO: added below line because gridview.js is loaded before.
		Yii::app()->clientScript->scriptMap['jquery.yiigridview.js'] = false;
		$this->renderPartial('groupMembersDialog',array('dataProvider'=>$dataProvider), false, true);
	}	
	
	//Deletes the selected group
	public function actionDeleteGroup()
	{
		if (isset($_REQUEST['groupId']))
		{
			$groupId = (int)$_REQUEST['groupId'];
		}	

		//Since there is a foreign constraint between PrivacyGroups and UserPrivacyGroupRelation, when the group is deleted the corresponding relation rows are also be deleted automatically
		//Since a group can be deleted only by its owner, check also the group owner
		$result = PrivacyGroups::model()->deleteGroup($groupId, Yii::app()->user->id);
		if($result == 1)
		{
			//Group deleted from the traceper_groups table
		
			echo CJSON::encode(array("result"=> "1"));
			Yii::app()->end();
		}
		elseif ($result == 0)
		{
			echo CJSON::encode(array("result"=> "Unknown error"));
			Yii::app()->end();
		}
		else
		{
			//traceper_groups table has not the selected group of the owner
		}
	}
	
	//Deletes the member of the selected group
	public function actionDeleteGroupMember()
	{
		if (isset($_REQUEST['groupId']))
		{
			$groupId = (int)$_REQUEST['groupId'];
		}	
		
		if (isset($_REQUEST['userId']))
		{
			$userId = (int)$_REQUEST['userId'];
		}		
		
		$relationQueryResult = UserPrivacyGroupRelation::model()->deleteGroupMember($userId,$groupId);
		if($relationQueryResult == 1)
		{
			//Relation deleted from the traceper_user_group_relation table
		
			echo CJSON::encode(array("result"=> "1"));
			Yii::app()->end();
		}
		elseif ($relationQueryResult == 0)
		{
			echo CJSON::encode(array("result"=> "Unknown error"));
			Yii::app()->end();
		}
		else
		{
			//traceper_user_group_relation table has not the desired relation, so do nothing
		}
	}	
	
	//Sets the privacy rights for the selected group
	public function actionSetPrivacyRights()
	{
		if (isset($_REQUEST['groupId']))
		{
			$groupId = (int)$_REQUEST['groupId'];
		}	
		
		$model = new GroupPrivacySettingsForm;

		$processOutput = true;
		// collect user input data				
		if(isset($_POST['GroupPrivacySettingsForm']))
		{
			$model->attributes = $_POST['GroupPrivacySettingsForm'];

			if($model->validate()) 
			{				
				//echo $_POST['GroupPrivacySettingsForm']['allowToSeeMyPosition'];
				//echo $model->allowToSeeMyPosition;
				
				if(isset($model->allowToSeeMyPosition))
				{
//					if($model->allowToSeeMyPosition) //Checked
//					{
//						echo 'CHECKED';
//					}
//					else //Unchecked
//					{
//						echo 'UNCHECKED';
//					}
					
					if(PrivacyGroups::model()->updatePrivacySettings($groupId, $model->allowToSeeMyPosition) >= 0) // save the change to database
					{
		
						//Take all the user-group relation rows that the selected group added to
						$relationRowsSelectedGroupBelongsTo = UserPrivacyGroupRelation::model()->findAll('groupId=:groupId', array(':groupId'=>$groupId));
						
						//Get only the user ID fields into $group_members from the obtained rows
						foreach($relationRowsSelectedGroupBelongsTo as $relationRow)
						{
							$errorOccured = Friends::model()->makeFriendsVisibilities(Yii::app()->user->id,$relationRow->userId,$model->allowToSeeMyPosition);
						}

						if($errorOccured == false)
						{
							echo CJSON::encode(array("result"=> "1"));
						}
						else
						{
							echo CJSON::encode(array("result"=> "0"));
						}
						Yii::app()->end();

					}
					else
					{
						echo CJSON::encode(array("result"=> "0"));
						Yii::app()->end();
					}					
					
				}
				else
				{
					//$model->allowToSeeMyPosition is UNSET
				}
			}
			
			if(Yii::app()->request->isAjaxRequest) 
			{
				$processOutput = false;
			}			
		}
		else
		{
			$group=PrivacyGroups::model()->findByPk($groupId);
			$model->allowToSeeMyPosition = $group->allowedToSeeOwnersPosition;
		}	
			
		Yii::app()->clientScript->scriptMap['jquery.js'] = false;
		Yii::app()->clientScript->scriptMap['jquery-ui.min.js'] = false;			
		
		$this->renderPartial('groupPrivacySettings',array('model'=>$model, 'groupId'=>$groupId), false, $processOutput);
	}

	
//	public function actionSearch() {
//		$model = new SearchForm();
//
//		$dataProvider = null;
//		if(isset($_REQUEST['SearchForm']))
//		{
//			$model->attributes = $_REQUEST['SearchForm'];
//			if ($model->validate()) {
//
//				$sqlCount = 'SELECT count(*)
//					 FROM '. Users::model()->tableName() . ' u 
//					 WHERE realname like "%'. $model->keyword .'%"';
//
//				$count=Yii::app()->db->createCommand($sqlCount)->queryScalar();
//
//				/*
//				 * if status is 0 it means friend request made but not yet confirmed
//				 * if status is 1 it means friend request is confirmed.
//				 * if status is -1 it means there is no relation of any kind between users.
//				 */
//				$sql = 'SELECT u.Id as id, u.realname, f.Id as friendShipId,
//								 IF(f.status = 0 OR f.status = 1, f.status, -1) as status,
//								 IF(f.friend1 = '. Yii::app()->user->id .', true, false ) as requester
//						FROM '. Users::model()->tableName() . ' u 
//						LEFT JOIN '. Friends::model()->tableName().' f 
//							ON  (f.friend1 = '. Yii::app()->user->id .' 
//								 AND f.friend2 =  u.Id)
//								 OR 
//								 (f.friend1 = u.Id 
//								 AND f.friend2 = '. Yii::app()->user->id .' ) 
//						WHERE u.realname like "%'. $model->keyword .'%"' ;
//
//				$dataProvider = new CSqlDataProvider($sql, array(
//		    											'totalItemCount'=>$count,
//													    'sort'=>array(
//						        							'attributes'=>array(
//						             									'id', 'realname',
//				),
//				),
//													    'pagination'=>array(
//													        'pageSize'=>Yii::app()->params->itemCountInOnePage,
//															'params'=>array(CHtml::encode('SearchForm[keyword]')=>$model->attributes['keyword']),
//				),
//				));
//					
//			}
//		}
//		Yii::app()->clientScript->scriptMap['jquery.js'] = false;
//		//TODO: added below line because gridview.js is loaded before.
//		Yii::app()->clientScript->scriptMap['jquery.yiigridview.js'] = false;
//		$this->renderPartial('searchResults',array('model'=>$model, 'dataProvider'=>$dataProvider), false, true);
//	}	
}



