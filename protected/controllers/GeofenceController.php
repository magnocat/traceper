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
		$fence=new Geofence;
		$result = "Missing parameter";
		if (isset($_REQUEST['point1Latitude']) && isset($_REQUEST['point1Longitude'])
		&& isset($_REQUEST['point2Latitude']) && isset($_REQUEST['point2Longitude'])
		&& isset($_REQUEST['point3Latitude']) && isset($_REQUEST['point3Longitude']))
		{
			$point1Lat = (float) $_REQUEST['point1Latitude'];
			$point1Long = (float) $_REQUEST['point1Longitude'];
			$point2Lat = (float) $_REQUEST['point2Latitude'];
			$point2Long = (float) $_REQUEST['point2Longitude'];
			$point3Lat = (float) $_REQUEST['point3Latitude'];
			$point3Long = (float) $_REQUEST['point3Longitude'];

			$fence->point1Latitude = $point1Lat;
			$fence->point1Longitude = $point1Long;
			$fence->point2Latitude = $point2Lat;
			$fence->point2Longitude = $point2Long;
			$fence->point3Latitude = $point3Lat;
			$fence->point3Longitude = $point3Long;

			$fence->userId = Yii::app()->user->id;

			$result = "Error in operation";
			if ($fence->save()) {
				$result = 1;
			}

			echo CJSON::encode(array(
                                         	"result"=>$result,
			));
		}
	}
}