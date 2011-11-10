<?php

class UsersController extends Controller
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

	public function actionGetFriendList()
	{
		$sqlCount = 'SELECT count(*)
					 FROM '. Friends::model()->tableName() . ' f 
					 WHERE (friend1 = '.Yii::app()->user->id.' 
						OR friend2 ='.Yii::app()->user->id.') AND status= 1';
		
		$count=Yii::app()->db->createCommand($sqlCount)->queryScalar();
		
		$sql = 'SELECT u.Id as id, u.realname
				FROM '. Friends::model()->tableName() . ' f 
				LEFT JOIN ' . Users::model()->tableName() . ' u
					ON u.Id = IF(f.friend1 != '.Yii::app()->user->id.', f.friend1, f.friend2)
				WHERE (friend1 = '.Yii::app()->user->id.' 
						OR friend2='.Yii::app()->user->id.') AND status= 1'  ;
		
		$dataProvider = new CSqlDataProvider($sql, array(
		    											'totalItemCount'=>$count,
													    'sort'=>array(
						        							'attributes'=>array(
						             									'id', 'realname',
															),
														),
													    'pagination'=>array(
													        'pageSize'=>5,
														),
													));
											
		Yii::app()->clientScript->scriptMap['jquery.js'] = false;
		$this->renderPartial('userList',array('dataProvider'=>$dataProvider), false, true);

	}
	
	public function actionSearch() {
		$model = new SearchForm();
		
		$dataProvider = null;
		if(isset($_REQUEST['SearchForm'])) 
		{
			$model->attributes = $_REQUEST['SearchForm'];
			if ($model->validate()) {
				
				$sqlCount = 'SELECT count(*)
					 FROM '. Users::model()->tableName() . ' u 
					 WHERE realname like "%'. $model->keyword .'%"';
		
				$count=Yii::app()->db->createCommand($sqlCount)->queryScalar();
		
				$sql = 'SELECT u.Id as id, u.realname 
						FROM '. Users::model()->tableName() . ' u 
						WHERE u.realname like "%'. $model->keyword .'%"' ;
		
				
				$dataProvider = new CSqlDataProvider($sql, array(
		    											'totalItemCount'=>$count,
													    'sort'=>array(
						        							'attributes'=>array(
						             									'id', 'realname',
															),
														),
													    'pagination'=>array(
													        'pageSize'=>5,
															'params'=>array(CHtml::encode('SearchForm[keyword]')=>$model->attributes['keyword']),
														),
													));
											
			}
		}
		Yii::app()->clientScript->scriptMap['jquery.js'] = false;
		$this->renderPartial('searchUser',array('model'=>$model, 'dataProvider'=>$dataProvider), false, true);	
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