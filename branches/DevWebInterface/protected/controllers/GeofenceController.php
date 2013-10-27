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
                'actions'=>array('CreateGeofence', 'UpdateGeofencePrivacy', 'SendGeofenceData','GetGeofences','CheckGeofenceBoundaries'),
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

		$model=new NewGeofenceForm;

		$processOutput = true;

		// collect user input data
		if(isset($_POST['NewGeofenceForm']))
		{
			$model->attributes=$_POST['NewGeofenceForm'];
			// validate user input and if ok return json data and end application.
			if($model->validate()) {

				echo CJSON::encode(array(
                                "result"=> "1",
								"name"=>$model->name,
								"description"=> $model->description,
				));
				Yii::app()->end();
			}
				
			if (Yii::app()->request->isAjaxRequest) {
				$processOutput = false;

			}
		}
		else
		{
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

		$this->renderPartial('createGeofence',array('model'=>$model), false, $processOutput);
	}


	public function actionSendGeofenceData() {

		if (isset($_REQUEST['name']) && isset($_REQUEST['name'])
		&& isset($_REQUEST['point1Latitude']) && isset($_REQUEST['point1Longitude'])
		&& isset($_REQUEST['point2Latitude']) && isset($_REQUEST['point2Longitude'])
		&& isset($_REQUEST['point3Latitude']) && isset($_REQUEST['point3Longitude']))
		{
			try
			{
				if(Geofence::model()->saveGeofence($_REQUEST['name'],
						(float) $_REQUEST['point1Latitude'], (float) $_REQUEST['point1Longitude'],
						(float) $_REQUEST['point2Latitude'], (float) $_REQUEST['point2Longitude'],
						(float) $_REQUEST['point3Latitude'], (float) $_REQUEST['point3Longitude'],
						$_REQUEST['description'],Yii::app()->user->id))
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
			}
		}
		Yii::app()->end();
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
			$model->attributes = $_POST['GeofenceSettingsForm'];

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
							if($selectedOwnerGroup->Id == $selectedFriendGroup)
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
						$unselected_groups[] = $selectedOwnerGroup->Id;
					}
				}
				
							
				//First process the unselected groups for deletion, because if you process the selected groups first, there may be db exceptions at privacy groupings
				
				//Check whether the friend belongs to the unselected group or not. If he does, delete his relation from the traceper_user_group_relation table
				foreach($unselected_groups as $unselectedFriendGroup) //Check for all of the checked groups
				{
					$relationQueryResult = GeofenceUserRelation::model()->deleteGeofenceMember($friendId,$unselectedFriendGroup);
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
				if(!empty($model->geofenceStatusArray)) //Check empty() in order to avoid error in foreach
				{
					foreach($model->geofenceStatusArray as $selectedFriendGroup) //Check for all of the checked groups
					{
						$relationQueryResult = GeofenceUserRelation::model()->find(array('condition'=>'userId=:userId AND geofenceId=:geofenceId',
							                                                     'params'=>array(':userId'=>$friendId, 
							                                                                     ':geofenceId'=>$selectedFriendGroup)));

						if($relationQueryResult != null)
						{
							//traceper_user_group_relation table already has the desired relation, so do nothing
						}
						else
						{
							try
							{
								if(GeofenceUserRelation::model()->saveUserGeofenceRelation($selectedFriendGroup, $friendId)) // save the change to database
								{
									//Relation added to the traceper_user_group_relation table
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
				
			if(Yii::app()->request->isAjaxRequest)
			{
				$processOutput = false;
				//echo ' Ajax';
			}
		}
		else
		{
			//If the form is opened, check the checkboxes according to traceper_user_group_relation table
			//Here $model->groupStatusArray is assigned to all of the groups that the user's friend is resgistered no matter the groups' owner is the logged user or not		
			$model->geofenceStatusArray=$selected_users;
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

		$this->renderPartial('geofenceSettings',array('model'=>$model, 'geofencesOfUser'=>$geofencesOfUser, 'friendId'=>$friendId), false, $processOutput);
	}
	
	public function actionCheckGeofenceBoundaries()
	{
		$processOutput = true;
					
		if (isset($_REQUEST['friendLatitude']) && isset($_REQUEST['friendLongitude'])
			&& isset($_REQUEST['friendId']))
		{			
			/*
			$locationPointOfFriend = array('latitude'=>32.11,
								 'longitude'=>25.11);
								 
			$locationPointOfFriend = array('latitude'=>42.148642,
								 'longitude'=>24.749107);
			*/
			$latitude = (float) $_REQUEST['friendLatitude'];
			$longitude = (float) $_REQUEST['friendLongitude'];
			$locationPointOfFriend = array('latitude'=>$latitude,
								 'longitude'=>$longitude);			
		
			$friendId = (int)$_REQUEST['friendId'];
			//Take all the user-group relation rows that the user's friend added to
			$relationRowsSelectedFriendBelongsTo = GeofenceUserRelation::model()->findAll('userId=:userId', array(':userId'=>$friendId));
			
			//Take all information of friend
			$friend_info = Users::model()->find('Id=:Id', array(':Id'=>$friendId));
			
			//Get only the group ID fields into $selected_users from the obtained rows
			foreach($relationRowsSelectedFriendBelongsTo as $selected_geofences)
			{
				//Get all of the geofences that the logged in user has created
				$related_geofence = Geofence::model()->find('Id=:Id', array(':Id'=>$selected_geofences->geofenceId));
				$geofencePoints = array(
								 array('latitude'=>$related_geofence->point1Latitude,
								 'longitude'=>$related_geofence->point1Longitude),
								 array('latitude'=>$related_geofence->point2Latitude,
								 'longitude'=>$related_geofence->point2Longitude),
								 array('latitude'=>$related_geofence->point3Latitude,
								 'longitude'=>$related_geofence->point3Longitude),
								 );								
								 
				if ($selected_geofences->status == 0)
				{
					if($this->isGeofenceContainsLocation($geofencePoints, $locationPointOfFriend))
					{
						$message = ''.$friend_info->realname.' is in '.$related_geofence->name.' geofence';
						
						$selected_geofences->status = 1;
						
						//Update database
						$selected_geofences->save();
						
						$headers  = 'MIME-Version: 1.0' . "\r\n";
						$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
						$headers  .= 'From: '. Yii::app()->params->contactEmail .'' . "\r\n";
						mail($friend_info->email, "Traceper Geofence", $message, $headers);
						
						echo CJSON::encode(array("result"=> $message));
						Yii::app()->end();
					}
				}
				else
				{
					if(!($this->isGeofenceContainsLocation($geofencePoints, $locationPointOfFriend)))
					{
						$message = ''.$friend_info->realname.' is out of '.$related_geofence->name.' geofence';
						$selected_geofences->status = 0;
						
						//Update database
						$selected_geofences->save();

						$headers  = 'MIME-Version: 1.0' . "\r\n";
						$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
						$headers  .= 'From: '. Yii::app()->params->contactEmail .'' . "\r\n";
						mail($friend_info->email, "Traceper Geofence", $message, $headers);
						
						echo CJSON::encode(array("result"=> $message));
						Yii::app()->end();
					}
				}
			}								
		}
		else
		{
			$processOutput = false;
			echo CJSON::encode(array("result"=> $processOutput));
			Yii::app()->end();
		}		
	}

	//Gets the list of the geofences
	public function actionGetGeofences()
	{
		$dataProvider = null;
		if(Yii::app()->user->id != null)
		{
			$count=Geofence::model()->getGeofencesCount(Yii::app()->user->id);
			$dataProvider = Geofence::model()->getGeofences(Yii::app()->user->id,$count,Yii::app()->params->itemCountInOnePage);
		}
		
		echo CJSON::encode(array("dataProvider"=> $dataProvider->getData(),"count"=>$count));
		Yii::app()->end();
		
	}

	
	private function  isGeofenceContainsLocation($geoFence, $point) {

		// Raycast point in polygon method
		$numPoints = count($geoFence); //MAP_OPERATOR.getPointNumberOfGeoFencePath(geoFence);
		$inPoly = false;
		$j = $numPoints-1;

		for($i = 0; $i < $numPoints; $i++)
		{
			if ($geoFence[$i]['longitude'] < $point['longitude']
			&& $geoFence[$j]['longitude'] >= $point['longitude']
			|| $geoFence[$j]['longitude'] < $point['longitude']
			&& $geoFence[$i]['longitude']  >= $point['longitude'])
			{
				if ($geoFence[$i]['latitude'] + ($point['longitude'] - $geoFence[$i]['longitude']) / ($geoFence[$j]['longitude'] - $geoFence[$i]['longitude']) * ($geoFence[$j]['latitude'] - $geoFence[$i]['latitude']) <$point['latitude']) {
					$inPoly = !$inPoly;
				}
			}
			$j = $i;
		}
		return $inPoly;
	}

	/*
	 $geoFence = array(
	 array('latitude'=>38.445388,
	 'longitude'=>-85.341797),
	 array('latitude'=>35.353216,
	 'longitude'=>-94.438477),
	 array('latitude'=>41.228249,
	 'longitude'=>-96.031494),
	 );
	 $point = array('latitude'=>35.35326,
	 'longitude'=>-94.43846);

	 if (GeoFenceOperator::isGeoFenceContainsLocation($geoFence, $point))
	 {
	 echo "true";
	 }
	 else
	 {
	 echo "false";
	 }
	 */
}