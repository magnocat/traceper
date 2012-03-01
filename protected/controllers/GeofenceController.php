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
                'actions'=>array('CreateGeofence', 'UpdateGeofencePrivacy', 'SendGeofenceData','GetGeofences'),
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
		Yii::app()->clientScript->scriptMap['jquery.js'] = false;
		Yii::app()->clientScript->scriptMap['jquery-ui.min.js'] = false;

		$this->renderPartial('createGeofence',array('model'=>$model), false, $processOutput);
	}


	public function actionSendGeofenceData() {

		$geofence=new Geofence;
		$result = "Missing parameter";

		if (isset($_REQUEST['name']) && isset($_REQUEST['name'])
		&& isset($_REQUEST['point1Latitude']) && isset($_REQUEST['point1Longitude'])
		&& isset($_REQUEST['point2Latitude']) && isset($_REQUEST['point2Longitude'])
		&& isset($_REQUEST['point3Latitude']) && isset($_REQUEST['point3Longitude']))
		{
			$geofence->name = $_REQUEST['name'];
			$geofence->point1Latitude = (float) $_REQUEST['point1Latitude'];
			$geofence->point1Longitude = (float) $_REQUEST['point1Longitude'];
			$geofence->point2Latitude = (float) $_REQUEST['point2Latitude'];
			$geofence->point2Longitude = (float) $_REQUEST['point2Longitude'];
			$geofence->point3Latitude = (float) $_REQUEST['point3Latitude'];
			$geofence->point3Longitude = (float) $_REQUEST['point3Longitude'];
				
			if (isset($_REQUEST['description']) && isset($_REQUEST['description']))
			{
				$description = $_REQUEST['description'];
				$geofence->description = $description;
			}
				
			$geofence->userId = Yii::app()->user->id;
				
			try
			{
				if($geofence->save()) // save the change to database
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
			
			//Get all points of the friends that the logged in user has created
			//TODO: get last location record, but first record is taken			
			$lastLocationOfFriend = UserWasHere::model()->find(array('condition'=>'userId =:userId','params'=>array(':userId'=>$friendId)));
			$locationPointOfFriend = array('latitude'=>$lastLocationOfFriend->latitude,
								 'longitude'=>$lastLocationOfFriend->longitude);
			//$sql=;
			//$locationsOfFriend = UserWasHere::model()->findAllBySql($sql,array(':userId'=>$friendId));
									
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
					
					$geofencePoints = array(
								 array('latitude'=>$selectedOwnerGroup->point1Latitude,
								 'longitude'=>$selectedOwnerGroup->point1Longitude),
								 array('latitude'=>$selectedOwnerGroup->point2Latitude,
								 'longitude'=>$selectedOwnerGroup->point2Longitude),
								 array('latitude'=>$selectedOwnerGroup->point3Latitude,
								 'longitude'=>$selectedOwnerGroup->point3Longitude),
								 );		

					//$deneme=$this->isGeofenceContainsLocation($geofencePoints, $locationPointOfFriend);
											
					if($this->isGeofenceContainsLocation($geofencePoints, $locationPointOfFriend)) //Check empty() in order to avoid error in foreach
					{
						$groupFound = true;								
					}
											
					if($groupFound == false)
					{
						$unselected_users[] = $selectedOwnerGroup->Id;
					}
				}
				
				echo CJSON::encode(array("result"=> $groupFound));
				Yii::app()->end();
					
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
			//If the form is opened, check the checkboxes according to traceper_user_group_relation table
			//Here $model->groupStatusArray is assigned to all of the groups that the user's friend is resgistered no matter the groups' owner is the logged user or not		
			$model->geofenceStatusArray=$selected_users;
		}
		Yii::app()->clientScript->scriptMap['jquery.js'] = false;
		Yii::app()->clientScript->scriptMap['jquery-ui.min.js'] = false;

		$this->renderPartial('geofenceSettings',array('model'=>$model, 'geofencesOfUser'=>$geofencesOfUser, 'friendId'=>$friendId), false, $processOutput);
	}

	//Gets the list of the geofences
	public function actionGetGeofences()
	{
		if(Yii::app()->user->id != null)
		{
			$sqlCount = 'SELECT count(*)
						 FROM '. Geofence::model()->tableName() . ' f 
						 WHERE (userId = '.Yii::app()->user->id.')';

			$count=Yii::app()->db->createCommand($sqlCount)->queryScalar();

			$sql = 'SELECT f.Id as id, f.name as Name, f.description as Description, 
					f.point1Latitude as Point1Latitude, f.point1Longitude as Point1Longitude,
					f.point2Latitude as Point2Latitude, f.point2Longitude as Point2Longitude,
					f.point3Latitude as Point3Latitude, f.point3Longitude as Point3Longitude
					FROM '. Geofence::model()->tableName() . ' f 					
					WHERE (userId = '.Yii::app()->user->id.')';
				
			$dataProvider = new CSqlDataProvider($sql, array(
			    											'totalItemCount'=>$count,
														    'sort'=>array(
							        							'attributes'=>array(
							             									'id', 'Name',
			),
			),
														    'pagination'=>array(
														        'pageSize'=>Yii::app()->params->itemCountInOnePage,
			),
			));
		}
		else
		{
			$dataProvider = null;
		}
		
		/*
		Yii::app()->clientScript->scriptMap['jquery.js'] = false;
		$this->renderPartial('userGeofences',array('dataProvider'=>$dataProvider), false, true);
		*/
		
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