<?php

class UploadController extends Controller
{
	const thumbSuffix = '_thumb';
	
 	public function filters()
    {
        return array(
            'accessControl',
        );
    }
    
	public function accessRules()
    {
        return array(
        	array('deny',
                'actions'=>array('delete', 'search', 'upload', 
                				 'get','getUploadListXML'),
        		'users'=>array('?'),
            )
        );
    }
   
	public function actionDelete()
	{
		$result = "id field missing";
		if (isset($_REQUEST['id'])) {
			$uploadId = (int)$_REQUEST['id'];
			//TODO: refactor not to fetch every item in user table, below line fetches everything
			$upload = Upload::model()->with("user")-> findBypk($uploadId);
			$result = "No upload with specific id";
			if ($upload != null)
			{
				$result = "not authorized to delete";
				if ($upload->user->Id == Yii::app()->user->id )
				{
					$result = "An error occured 1";
					if ($upload->delete()) {
						$result = 1;
						if (file_exists($this->getFileName($uploadId, $upload->fileType))) {
							$result = "An error occured 2";
							if (unlink($this->getFileName($uploadId, $upload->fileType))) {
								$result = 1;
								if (file_exists($this->getFileName($uploadId, $upload->fileType))) {
									$result = "An error occured 3";
									if (unlink($this->getFileName($uploadId, $upload->fileType))) {
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
		if (isset($_GET['fileType']) && $_GET['fileType'] != NULL)
		{
			$fileType = $_GET['fileType'];
							
			if(Yii::app()->user->id != null)
			{
				$friendList = AuxiliaryFriendsOperator::getFriendIdList();
				
				$dataProvider = Upload::model()->getRecordList($fileType,Yii::app()->user->id,$friendList);
			}
			else
			{
				$dataProvider = null;
			}			
		}
		else
		{
			$dataProvider = null;
		}

		Yii::app()->clientScript->scriptMap['jquery.js'] = false;
		//TODO: added below line because gridview.js is loaded before.
		Yii::app()->clientScript->scriptMap['jquery.yiigridview.js'] = false;
		$this->renderPartial('uploadsInfo',array('dataProvider'=>$dataProvider,'model'=>new SearchForm(),'uploadList'=>true, 'fileType'=>$fileType), false, true);
	}

	public function actionSearch() {
		$model = new SearchForm();
		$dataProvider = null;
		if(isset($_REQUEST['SearchForm']))
		{
			$model->attributes = $_REQUEST['SearchForm'];
			
			if (isset($_GET['fileType']) && $_GET['fileType'] != NULL)
			{
				$fileType = $_GET['fileType'];
				
				if ($model->validate()) 
				{
					$friendList = AuxiliaryFriendsOperator::getFriendIdList();
					
					$dataProvider=Upload::model()->getSearchResult($fileType,Yii::app()->user->id,$friendList,$model->keyword,$model->attributes['keyword']);
				}				
			}
			else
			{
				$dataProvider = null;
			}			
		}
		Yii::app()->clientScript->scriptMap['jquery.js'] = false;
		//TODO: added below line because gridview.js is loaded before.
		Yii::app()->clientScript->scriptMap['jquery.yiigridview.js'] = false;
		$this->renderPartial('searchUploadResults', array('model'=>$model, 'dataProvider'=>$dataProvider, 'fileType'=>$fileType), false, true);
	}

	public function actionGet()
	{
		//Fb::warn("actionGet() called");
				
		if (!isset($_REQUEST['id'])) {
			echo "No id";
			Yii::app()->end();
		}
		if (!isset($_REQUEST['fileType'])) {
			echo "No file Type";
			Yii::app()->end();
		}		
		$uploadId = (int) $_REQUEST['id'];
		$fileType = (int) $_REQUEST['fileType'];

		$fileName = $this->getFileName($uploadId, $fileType);
		
		if (file_exists($fileName) === true){
			$thumb = false;
			
			Fb::warn($uploadId, "File exists");
			
			if (isset($_REQUEST['thumb'])) {
				$thumb = true;
			}
			
			if ($thumb == true) {
				$fileName = $this->getFileName($uploadId, $fileType, true);
				
				if (file_exists($fileName) === false) {
					Fb::warn("createThumb() called");
					
					$this->createThumb($uploadId, $fileType);
				}
			}
		}
		else {
			Fb::warn($uploadId, "File does not exist");
			
			$fileName = Yii::app()->basePath . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . ($fileType == 0)?'images/image_missing.png':'images/video.png';
		}
		
		if($fileType == 0) //Image
		{
			header('Content-Type: image/jpeg;');
			echo file_get_contents($fileName);		
		}
		else 
		{
		
		}

		Yii::app()->end();
	}

	/**
	 * this action is used by mobile clients
	 */
	public function actionUpload()
	{		
		$result = "Missing parameter";
		$uploadId = 0;
				
		if (isset($_FILES["upload"])
			&& isset($_REQUEST['latitude']) && $_REQUEST['latitude'] != NULL
			&& isset($_REQUEST['longitude']) && $_REQUEST['longitude'] != NULL
			&& isset($_REQUEST['altitude']) && $_REQUEST['altitude'] != NULL
			&& isset($_REQUEST['description']) && $_REQUEST['description'] != NULL
			&& isset($_REQUEST['fileType']) && $_REQUEST['fileType'] != NULL
/*				
			&& ($_REQUEST['fileType'] == "0" ||
						($_REQUEST['fileType'] == "1"
								&& isset($_REQUEST['isLive']) && $_REQUEST['isLive'] != NULL
								&& isset($_REQUEST['partIndex']) && $_REQUEST['partIndex'] != NULL
								&& isset($_REQUEST['uniqueId']) && $_REQUEST['uniqueId'] != NULL
								&& isset($_REQUEST['partIndex']) && $_REQUEST['partIndex'] != NULL))
*/								
								)
		{
			$result = "Upload Error";
			if ($_FILES["upload"]["error"] == UPLOAD_ERR_OK )
			{
				$latitude = (float) $_REQUEST['latitude'];
				$longitude = (float) $_REQUEST['longitude'];
				$altitude = (float) $_REQUEST['altitude'];
				$description = htmlspecialchars($_REQUEST['description']);
				$fileType = (int) $_REQUEST['fileType'];

				$publicData = 0;
				if (isset($_REQUEST['publicData']) && $_REQUEST['publicData'] != NULL) {
					$tmp = (int) $_REQUEST['publicData'];
					if ($tmp == 1) {
						$publicData = 1;
					}
				}
				
				$isLive = 0;
				$insertToDB = true;
				$liveKey = 0;
				
				if (isset($_REQUEST['isLive']) && $_REQUEST['isLive'] == "1"){
				
					$isLive = 1;
					$insertToDB = false;
					if (isset($_REQUEST['partIndex']) && $_REQUEST['partIndex'] == "0") {
						$insertToDB = true;
					}
					$liveKey = trim($_REQUEST['uniqueId']);
				}
				
				$extension = '.jpg';
				if ($fileType == 1) {
					$extension = '.mp4';
				}

				if ($insertToDB == true) {
									
					$result = "Unknown Error";
					$uploadId = Upload::model()->addNewRecord($fileType, Yii::app()->user->id, $latitude, $longitude, $altitude, $publicData, $description, $isLive, $liveKey);
					
					if($uploadId > 0)
					{
						$result = "Error in moving uploading file";
						$fileName = Yii::app()->params->uploadPath .'/'. Yii::app()->db->lastInsertID . $extension;
						
						if (move_uploaded_file($_FILES["upload"]["tmp_name"], $fileName))
						{
							if ($fileType == 1) {
								$newFileName = Yii::app()->params->uploadPath .'/'. Yii::app()->db->lastInsertID . '.flv';
								$command = 'ffmpeg -i '. $fileName . ' -sameq -ar 22050 ' . $newFileName;
								$out = shell_exec($command);
								//echo $out;
								
							}
							
							$result = "1";
						}
					}
				}
				else if ($isLive == 1)
				{
					$partIndex = $_REQUEST['partIndex'];
					if ($_REQUEST['isLastPacket'] == "1"){
				
					}
				
					$fileName = Yii::app()->params->uploadPath .'/'. $liveKey . $partIndex . $extension;
					if (move_uploaded_file($_FILES["upload"]["tmp_name"], $fileName))
					{
						$newFileName = Yii::app()->params->uploadPath .'/'. $liveKey . $partIndex . '.flv';
						$command = 'ffmpeg -i '. $fileName . ' -sameq -ar 22050 ' . $newFileName;
						$out = shell_exec($command);
						$videoId = Upload::model()->getId($liveKey);
						$previousVideoFileName = Yii::app()->params->uploadPath .'/' . $videoId .'.flv';
						$this->getCombineFlvVideosCommand($previousVideoFileName, $newFileName);
				
						$result = "1";
					}
				}		
			}
		}
		echo CJSON::encode(array("result"=>$result, "uploadId"=>$uploadId));
		Yii::app()->end();
	}
	
	private function getCombineFlvVideosCommand($file1, $file2)
	{
		$command = 'yes | ffmpeg -i '.$file1.' -sameq intermediate1.mpg &';
		$result = shell_exec($command);
		if ($result == null) {
			echo $command;
			echo "result null";
		}
		$command = 'yes | ffmpeg -i '. $file2 . ' -sameq intermediate2.mpg &';
		$result = shell_exec($command);
		if ($result == null) {
			echo "result null";
		}
		$command = 'yes | cat intermediate1.mpg intermediate2.mpg > intermediate_all.mpg &';
		$result = shell_exec($command);
		if ($result == null) {
			echo "result null";
		}
		$command = 'yes | ffmpeg -i intermediate_all.mpg -sameq '. $file1.' &';
		$result = shell_exec($command);
		if ($result == null) {
			echo "result null";
		}
	}

	private function getFileName($uploadId, $fileType, $thumb = false)
	{
		$fileExtension = ($fileType == 0)?'.jpg':'.flv';
		
		$fileName = Yii::app()->params->uploadPath. '/' . $uploadId . $fileExtension;
		if ($thumb == true) {
			$fileName = Yii::app()->params->uploadPath . '/' . $uploadId . self::thumbSuffix .$fileExtension;
		}
		return $fileName;
	}

	private function createThumb($uploadId, $fileType) {
		// Set maximum height and width
		$width  = 36;
		$height = 36;
		$filename =  $this->getFileName($uploadId, $fileType);

		if (file_exists($filename) === true) {
			Fb::warn("file_exists(): true");
			
			Fb::warn($fileType, "File Type");
			
			if($fileType == 0) //Image
			{								
				// Get new dimensions
				list($width_orig, $height_orig) = getimagesize($filename);
				$width = ($height / $height_orig) * $width_orig;
		
				// Resample
				$image_p = imagecreatetruecolor($width, $height);
				$image   = imagecreatefromjpeg($filename);
				imagecopyresampled($image_p, $image, 0, 0, 0, 0, $width, $height, $width_orig, $height_orig);
				$filenameThumb =  $this->getFileName($uploadId, $fileType, true);
				touch($filenameThumb);
				// Output
				imagejpeg($image_p, $filenameThumb);
				imagedestroy($image);
			}
			else //Video
			{
			
			}			
		}
		else
		{
			Fb::warn("file_exists(): false");
		}
	}

	public function actionGetUploadListXML()
	{
		if (Yii::app()->user->isGuest) {
			return;
		}
		$pageNo = 1;
		if (isset($_REQUEST['pageNo']) && $_REQUEST['pageNo'] > 0) {
			$pageNo = (int) $_REQUEST['pageNo'];
		}
		
		if(isset($_REQUEST['fileType']) && $_REQUEST['fileType'] != NULL)
		{
			$fileType = (int)$_REQUEST['fileType'];
		}	
		
		$offset = ($pageNo - 1) * Yii::app()->params->itemCountInDataListPage;
		$out = '';
		$dataFetchedTimeKey = "uploadController.dataFetchedTime";
		
		$dataReader = NULL;
		
		if (isset($_REQUEST['list'])) {
			if ($_REQUEST['list'] == "onlyUpdated")
			{
				$time = Yii::app()->session[$dataFetchedTimeKey];
				if ($time !== false && $time != "")
				{
					$friendList = AuxiliaryFriendsOperator::getFriendIdList();
					
					$pageCount=Upload::model()->getUploadCount($fileType,Yii::app()->user->id,$friendList,$time);
					
					if ($pageCount >= $pageNo && $pageCount != 0) {
						$dataReader=Upload::model()->getUploadList($fileType,Yii::app()->user->id,$friendList,$time,$offset);
					}
					
					$out = $this->prepareXML($dataReader, $pageNo, $pageCount, "userList");
				}
			}
		}
		else {

			$friendList = AuxiliaryFriendsOperator::getFriendIdList();
			$pageCount=Upload::model()->getUploadCount($fileType,Yii::app()->user->id,$friendList,NULL);
			if ($pageCount >= $pageNo && $pageCount != 0) {
				$dataReader=Upload::model()->getUploadList($fileType,Yii::app()->user->id,$friendList,NULL,$offset);
			}
			$out = $this->prepareXML($dataReader, $pageNo, $pageCount, "uploadList");
		}
		echo $out;
		Yii::app()->session[$dataFetchedTimeKey] = time();
		Yii::app()->end();
	}

	//private function prepareXML($sql, $pageNo, $pageCount, $type="userList")
	private function prepareXML($dataReader, $pageNo, $pageCount, $type="userList")
	{

		$str = NULL;
		$userId = NULL;
		if ($dataReader != NULL )
		{
			while ( $row = $dataReader->read() )
			{
				$str .= $this->getUploadXMLItem($row);
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

	private function getUploadXMLItem($row)
	{
		$row['latitude'] = isset($row['latitude']) ? $row['latitude'] : null;
		$row['longitude'] = isset($row['longitude']) ? $row['longitude'] : null;
		$row['altitude'] = isset($row['altitude']) ? $row['altitude'] : null;
		$row['uploadTime'] = isset($row['uploadTime']) ? $row['uploadTime'] : null;
		$row['id'] = isset($row['id']) ? $row['id'] : null;
		$row['userId'] = isset($row['userId']) ? $row['userId'] : null;
		$row['realname'] = isset($row['realname']) ? $row['realname'] : null;
		$row['rating'] = isset($row['rating']) ? $row['rating'] : null;
		$row['description'] = isset($row['description']) ? $row['description'] : null;

		$str = '<upload description="'.$row['description'].'" url="'. Yii::app()->homeUrl .urlencode('?r=upload/get&id='. $row['id']) .'"   id="'. $row['id']  .'" byUserId="'. $row['userId'] .'" byRealName="'. $row['realname'] .'" altitude="'.$row['altitude'].'" latitude="'. $row['latitude'].'"	longitude="'. $row['longitude'] .'" rating="'. $row['rating'] .'" time="'.$row['uploadTime'].'" />';

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