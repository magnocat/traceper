<?php

class GeofenceController extends Controller
{
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
	
	public function accessRules()
    {
    	//TODO: actionUpload can be added list below after mobile app is able to login the framework
        return array(
        	array('deny',
                'actions'=>array('CreateGeofence', 'UpdateGeofencePrivacy'),
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
	

	
	public function actionCreateGeofence() {
		$geofence=new Geofence;
		$result = "Missing parameter";
		if (isset($_REQUEST['name']) && isset($_REQUEST['name'])
		&& isset($_REQUEST['point1Latitude']) && isset($_REQUEST['point1Longitude'])
		&& isset($_REQUEST['point2Latitude']) && isset($_REQUEST['point2Longitude'])
		&& isset($_REQUEST['point3Latitude']) && isset($_REQUEST['point3Longitude']))
		{
			$name = $_REQUEST['name'];			
			$point1Lat = (float) $_REQUEST['point1Latitude'];
			$point1Long = (float) $_REQUEST['point1Longitude'];
			$point2Lat = (float) $_REQUEST['point2Latitude'];
			$point2Long = (float) $_REQUEST['point2Longitude'];
			$point3Lat = (float) $_REQUEST['point3Latitude'];
			$point3Long = (float) $_REQUEST['point3Longitude'];
			
			$geofence->name = $name;			
			$geofence->point1Latitude = $point1Lat;
			$geofence->point1Longitude = $point1Long;
			$geofence->point2Latitude = $point2Lat;
			$geofence->point2Longitude = $point2Long;
			$geofence->point3Latitude = $point3Lat;
			$geofence->point3Longitude = $point3Long;
			
			if (isset($_REQUEST['description']) && isset($_REQUEST['description']))
			{
				$description = $_REQUEST['description'];
				$geofence->description = $description;
			}
			
			$geofence->userId = Yii::app()->user->id;

			$result = "Error in operation";
			if ($geofence->save()) {
				$result = 1;
			}

			echo CJSON::encode(array(
                                         	"result"=>$result,
			));
		}
	}
	
	
	public function actionUpdateGeofencePrivacy()
	{
		$model = new GeofenceSettingsForm;

		$processOutput = true;		
		//Get all of the geofences that the logged in user has created
		$geofencesOfUser = Geofence::model()->findAll('userId=:userId', array(':userId'=>Yii::app()->user->id));
		
		$selected_users=array(); //array to hold all the groups of the user's selected friend to be used for the checking the checkboxes in the initial dialog
		$unselected_users=array(); //array to hold the unselected user groups so that it can be used for user removal from unselected groups
		
		if (isset($_REQUEST['friendId']))
		{
			$friendId = (int)$_REQUEST['friendId'];			
			//Take all the user-group relation rows that the user's friend added to
			$relationRowsSelectedFriendBelongsTo = GeofenceUserRelation::model()->findAll('userId=:userId', array(':userId'=>$friendId));
			
			//Get only the group ID fields into $selected_users from the obtained rows
			foreach($relationRowsSelectedFriendBelongsTo as $relationRow)
			{
			    $selected_users[] = $relationRow->geofenceId;
			}
		}		
		
		if(isset($_POST['GeofenceSettingsForm']))
		{
			//echo 'SET';
			
			$model->attributes = $_POST['GeofenceSettingsForm'];
			//$model->groupStatusArray = $_POST['GroupSettingsForm']['groupStatusArray'];
			
			if($model->validate()) 
			{				
				//We know the selected groups from $model->groupStatusArray, but not know the unselected ones
				//So construct the unselected groups using $geofencesOfUser and $model->groupStatusArray 
				foreach($geofencesOfUser as $selectedOwnerGroup) //Check for all of the user's groups
				{
					$groupFound = false;
					
					if(!empty($model->geofenceStatusArray)) //Check empty() in order to avoid error in foreach
					{
						foreach($model->geofenceStatusArray as $selectedFriendGroup) //Check for all of the checked groups
						{
							if($selectedOwnerGroup->id == $selectedFriendGroup)
							{
								$groupFound = true;
								break;
							}					
						}					
					}
					else //None of the checkboxes selected so all of the user groups will be in $unselected_users
					{
					
					}

					
					if($groupFound == false)
					{
						$unselected_users[] = $selectedOwnerGroup->id;
					}
				}
													
				//Check for privacy groups
				if(count($model->geofenceStatusArray) > 1)
				{
					echo CJSON::encode(array("result"=> "Duplicate Entry"));
					Yii::app()->end();				
				}
				else
				{
					//First process the unselected groups for deletion, because if you process the selected groups first, there may be db exceptions at privacy groupings
	
					//Check whether the friend belongs to the unselected group or not. If he does, delete his relation from the traceper_user_group_relation table
					foreach($unselected_users as $unselectedFriendGroup) //Check for all of the checked groups
					{
						$relationQueryResult = GeofenceUserRelation::model()->find(array('condition'=>'userId=:userId AND geofenceId=:geofenceId', 
						                                                     'params'=>array(':userId'=>$friendId, 
						                                                                     ':geofenceId'=>$unselectedFriendGroup
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
					
					//Check whether the friend belongs to the selected group or not before. If he does not, add his relation to the traceper_user_group_relation table				
					if(!empty($model->geofenceStatusArray)) //Check empty() in order to avoid error in foreach
					{
						foreach($model->geofenceStatusArray as $selectedFriendGroup) //Check for all of the checked groups
						{
							$relationQueryResult = GeofenceUserRelation::model()->find(array('condition'=>'userId=:userId AND geofenceId=:geofenceId', 
							                                                     'params'=>array(':userId'=>$friendId, 
							                                                                     ':geofenceId'=>$selectedFriendGroup
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
								$geofenceUserRelation = new GeofenceUserRelation;
								$geofenceUserRelation->userId = $friendId;
								$geofenceUserRelation->geofenceId = $selectedFriendGroup;
								$geofenceUserRelation->geofenceOwner = Yii::app()->user->id;
	
								try
								{
									if($geofenceUserRelation->save()) // save the change to database
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
			$model->geofenceStatusArray=$selected_users;			
		}	

		Yii::app()->clientScript->scriptMap['jquery.js'] = false;
		Yii::app()->clientScript->scriptMap['jquery-ui.min.js'] = false;			
		
		//$this->renderPartial('groupSettings',array('model'=>$model, 'groupsOfUser'=>$geofencesOfUser, 'relationRowsSelectedFriendBelongsTo'=>$relationRowsSelectedFriendBelongsTo, 'friendId'=>$friendId), false, $processOutput);
		$this->renderPartial('geofenceSettings',array('model'=>$model, 'geofencesOfUser'=>$geofencesOfUser, 'friendId'=>$friendId), false, $processOutput);
	}		
}