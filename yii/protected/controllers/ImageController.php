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
													        'pageSize'=>Yii::app()->params->imageCountInOnePage,
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
													        'pageSize'=>Yii::app()->params->imageCountInOnePage,
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

	public function actionGetImageListXML()
	{
		if (Yii::app()->user->isGuest) {
			return;
		}
		$pageNo = 1;
		if (isset($_REQUEST['pageNo']) && $_REQUEST['pageNo'] > 0) {
			$pageNo = (int) $_REQUEST['pageNo'];
		}
		$offset = ($pageNo - 1) * Yii::app()->params->itemCountInDataListPage;
		$out = '';
		$dataFetchedTimeKey = "ImageController.dataFetchedTime";
		if (isset($_REQUEST['list'])) {
			if ($_REQUEST['list'] == "onlyUpdated")
			{
				$time = Yii::app()->session[$dataFetchedTimeKey];
				if ($time !== false)
				{
						$friendList = AuxiliaryFriendsOperator::getFriendIdList();
						$sqlCount = 'SELECT ceil(count(*)/'. Yii::app()->params->itemCountInDataListPage .')
								 FROM '. Upload::model()->tableName() . ' u 
								 WHERE (userId in ('. $friendList .') 
								        OR userId = '. Yii::app()->user->id .')
								        AND unix_timestamp(u.uploadTime) >= '. $time;
			
						$pageCount=Yii::app()->db->createCommand($sqlCount)->queryScalar();
			
						$sql = 'SELECT u.Id as id, s.realname, s.Id as userId, u.altitude, u.latitude, u.longitude
								 FROM '. Upload::model()->tableName() . ' u 
								 LEFT JOIN  '. Users::model()->tableName() . ' s ON s.Id = u.userId
								 WHERE (userId in ('. $friendList .') 
								 		OR userId = '. Yii::app()->user->id .')
								 		AND unix_timestamp(u.uploadTime) >= '. $time;
					
					$out = $this->prepareXML($sql, $pageNo, $pageCount, "userList");
				}

			}
		}
		else {

			$friendList = AuxiliaryFriendsOperator::getFriendIdList();
			$sqlCount = 'SELECT ceil(count(*)/'. Yii::app()->params->itemCountInDataListPage .')
					 FROM '. Upload::model()->tableName() . ' u 
					 WHERE userId in ('. $friendList .') OR 
					 	   userId = '. Yii::app()->user->id .'';

			$pageCount=Yii::app()->db->createCommand($sqlCount)->queryScalar();

			$sql = 'SELECT u.Id as id, s.realname, s.Id as userId, u.altitude, u.latitude, u.longitude
					 FROM '. Upload::model()->tableName() . ' u 
					 LEFT JOIN  '. Users::model()->tableName() . ' s ON s.Id = u.userId
					 WHERE userId in ('. $friendList .') OR 
					 	   userId = '. Yii::app()->user->id .'';
				

			$out = $this->prepareXML($sql, $pageNo, $pageCount, "imageList");
		}
		echo $out;
		Yii::app()->session[$dataFetchedTimeKey] = time();
		Yii::app()->end();
	}

	private function prepareXML($sql, $pageNo, $pageCount, $type="userList")
	{
		$dataReader = NULL;
		// if page count equal to 0 then there is no need to run query
		//		echo $sql;
		if ($pageCount >= $pageNo && $pageCount != 0) {
			$dataReader = Yii::app()->db->createCommand($sql)->query();
		}

		$str = NULL;
		$userId = NULL;
		if ($dataReader != NULL )
		{
			while ( $row = $dataReader->read() )
			{
				$str .= $this->getImageXMLItem($row);
			}
		}
		$extra = "thumbSuffix=\"thumb=ok\"";
		$pageNo = $pageCount == 0 ? 0 : $pageNo;
		return $this->addXMLEnvelope($pageNo, $pageCount, $str, $extra);
	}

	private function addXMLEnvelope($pageNo, $pageCount, $str, $extra = ""){
			
		$pageStr = 'pageNo="'.$pageNo.'" pageCount="' . $pageCount .'"' ;

		header("Content-type: application/xml; charset=utf-8");
		$out = '<?xml version="1.0" encoding="UTF-8"?>'
		.'<page '. $pageStr . '  '. $extra .' >'
		. $str
		.'</page>';

		return $out;
	}


	private function getImageXMLItem($row)
	{
		$row['latitude'] = isset($row['latitude']) ? $row['latitude'] : null;
		$row['longitude'] = isset($row['longitude']) ? $row['longitude'] : null;
		$row['altitude'] = isset($row['altitude']) ? $row['altitude'] : null;
		$row['uploadTime'] = isset($row['uploadTime']) ? $row['uploadTime'] : null;
		$row['id'] = isset($row['id']) ? $row['id'] : null;
		$row['userId'] = isset($row['userId']) ? $row['userId'] : null;
		$row['realname'] = isset($row['realname']) ? $row['realname'] : null;
		$row['rating'] = isset($row['rating']) ? $row['rating'] : null;


		$str = '<image url="'. Yii::app()->homeUrl .urlencode('?r=image/get&id='. $row['id']) .'"   id="'. $row['id']  .'" byUserId="'. $row['userId'] .'" byRealName="'. $row['realname'] .'" altitude="'.$row['altitude'].'" latitude="'. $row['latitude'].'"	longitude="'. $row['longitude'] .'" rating="'. $row['rating'] .'" time="'.$row['uploadTime'].'" />';

		return $str;
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