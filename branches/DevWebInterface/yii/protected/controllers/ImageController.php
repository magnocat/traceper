<?php

class ImageController extends Controller
{
	const thumbSuffix = '_thumb';


	public function actionDelete()
	{
		$result = "id field missing";
		if (isset($_REQUEST['id'])) {
			$imageId = (int)$_REQUEST['id'];
			//TODO: refactor not to fetch every item in user table, below line fetches everything
			$image = Upload::model()->with("user")-> findBypk($imageId); 											
			$result = "No image with specific id";
			if ($image != null) 
			{
				$result = "not authorized to delete";
				if ($image->user->Id == Yii::app()->user->id ) 
				{
					$result = "An error occured";
					if ($image->delete()) {
						if (file_exists($this->getFileName($imageId))) {
							$result = "An error occured";
							if (unlink($this->getFileName($imageId))) {
								$result = 1;											
								if (file_exists($this->getFileName($imageId))) {
									$result = "An error occured";
									if (unlink($this->getFileName($imageId))) {
										$result = 1;								
									}
								}		
							}
						}
																	
					} 
				}
			}
		}
		echo CJSON::encode(array("result"=> $result));	
		Yii::app()->end();
	}

	public function actionGetList()
	{
		$friendList = AuxiliaryFriendsOperator::getFriendIdList();
		$sqlCount = 'SELECT count(*)
					 FROM '. Upload::model()->tableName() . ' u 
					 WHERE userId in ('. $friendList .') OR 
					 	   userId = '. Yii::app()->user->id .'';

		$count=Yii::app()->db->createCommand($sqlCount)->queryScalar();

		$sql = 'SELECT u.Id as id, s.realname, s.Id as userId
					 FROM '. Upload::model()->tableName() . ' u 
					 LEFT JOIN  '. Users::model()->tableName() . ' s ON s.Id = u.userId
					 WHERE userId in ('. $friendList .') OR 
					 	   userId = '. Yii::app()->user->id .'';

		$dataProvider = new CSqlDataProvider($sql, array(
		    											'totalItemCount'=>$count,
													    'sort'=>array(
						        							'attributes'=>array(
						             									'id',
		),
		),
													    'pagination'=>array(
													        'pageSize'=>5,
		),
		));
			
		Yii::app()->clientScript->scriptMap['jquery.js'] = false;
		$this->renderPartial('getList',array('dataProvider'=>$dataProvider), false, true);
	}

	public function actionSearch() {
		$model = new SearchForm();

		$dataProvider = null;
		if(isset($_REQUEST['SearchForm']))
		{
			$model->attributes = $_REQUEST['SearchForm'];
			if ($model->validate()) {

				$sqlCount = 'SELECT count(*)
					 FROM '. Upload::model()->tableName() . ' u
					 LEFT JOIN  '. Users::model()->tableName() . ' s ON s.Id = u.userId 
					 WHERE s.realname like "%'. $model->keyword .'%"';

				$count=Yii::app()->db->createCommand($sqlCount)->queryScalar();

				$sql ='SELECT u.Id as id, s.realname, s.Id as userId
					 FROM '. Upload::model()->tableName() . ' u
					 LEFT JOIN  '. Users::model()->tableName() . ' s ON s.Id = u.userId 
					 WHERE s.realname like "%'. $model->keyword .'%"';


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
		$this->renderPartial('search', array('model'=>$model, 'dataProvider'=>$dataProvider), false, true);
	}

	public function actionGet()
	{
		if (!isset($_REQUEST['id'])) {
			echo "No id";
			Yii::app()->end();
		}
		$imageId = (int) $_REQUEST['id'];

		$fileName = $this->getFileName($imageId);
		if (file_exists($fileName) === true){
			$thumb = false;
			if (isset($_REQUEST['thumb'])) {
				$thumb = true;
			}
			if ($thumb == true) {
				$fileName = $this->getFileName($imageId, true);;
				if (file_exists($fileName) === false) {
					$this->createThumb($imageId);
				}
			}

		}
		else {
			$fileName = Yii::app()->basePath . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'images/image_missing.png';
		}
		header('Content-Type: image/jpeg;');
		echo file_get_contents($fileName);

		Yii::app()->end();
	}

	private function getFileName($imageId, $thumb = false)
	{
		$fileName = Yii::app()->params->uploadPath. '/' . $imageId . '.jpg';
		if ($thumb == true) {
			$fileName = Yii::app()->params->uploadPath . '/' . $imageId . self::thumbSuffix .'.jpg';
		}
		return $fileName;
	}

	private function createThumb($imageId) {

		// Set maximum height and width
		$width  = 36;
		$height = 36;
		$filename =  $this->getFileName($imageId);
		if (file_exists($filename) === true) {
			// Get new dimensions
			list($width_orig, $height_orig) = getimagesize($filename);

			$width = ($height / $height_orig) * $width_orig;


			// Resample
			$image_p = imagecreatetruecolor($width, $height);
			$image   = imagecreatefromjpeg($filename);
			imagecopyresampled($image_p, $image, 0, 0, 0, 0, $width, $height, $width_orig, $height_orig);
			$filenameThumb =  $this->getFileName($imageId, true);
			touch($filenameThumb);
			// Output
			imagejpeg($image_p, $filenameThumb);
			imagedestroy($image);
		}

	}
	// Uncomment the following methods and override them if needed
	/*
	public function filters()
	{
	// return the filter configuration for this controller, e.g.:
	return array(
	'inlineFilterName',
	array(
	'class'=>'path.to.FilterClass',
	'propertyName'=>'propertyValue',
	),
	);
	}

	public function actions()
	{
	// return external action classes, e.g.:
	return array(
	'action1'=>'path.to.ActionClass',
	'action2'=>array(
	'class'=>'path.to.AnotherActionClass',
	'propertyName'=>'propertyValue',
	),
	);
	}
	*/
}