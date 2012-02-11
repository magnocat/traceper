<?php

class ImageController extends Controller
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
                'actions'=>array('upload'),
        		'users'=>array('?'),
            )
        );
    }
    
	/**
	 * this action is used by mobile clients
	 */
	public function actionUpload()
	{		
		$result = "Missing parameter";		
		if (isset($_FILES["video"])
			&& isset($_REQUEST['latitude']) && $_REQUEST['latitude'] != NULL
			&& isset($_REQUEST['longitude']) && $_REQUEST['longitude'] != NULL
			&& isset($_REQUEST['altitude']) && $_REQUEST['altitude'] != NULL
			&& isset($_REQUEST['description']) && $_REQUEST['description'] != NULL)
		{
			$result = "Upload Error";
			if ($_FILES["video"]["error"] == UPLOAD_ERR_OK )
			{
				$latitude = (float) $_REQUEST['latitude'];
				$longitude = (float) $_REQUEST['longitude'];
				$altitude = (float) $_REQUEST['altitude'];
				$description = htmlspecialchars($_REQUEST['description']);

				$publicData = 0;
				if (isset($_REQUEST['publicData']) && $_REQUEST['publicData'] != NULL) {
					$tmp = (int) $_REQUEST['publicData'];
					if ($tmp == 1) {
						$publicData = 1;
					}
				}

				if (Yii::app()->user->id != null) 
				{
					
					$sql = sprintf('INSERT INTO '
									. Upload::model()->tableName() .'
									(fileType, userId, latitude, longitude, altitude, uploadtime, publicData, description)
									VALUES(%d, %d, %s, %s, %s, NOW(), %d, "%s")', 
									1/*Video*/, Yii::app()->user->id, $latitude, $longitude, $altitude, $publicData, $description);
					$result = "Unknown Error";
					$effectedRows = Yii::app()->db->createCommand($sql)->execute();
					if ($effectedRows == 1)
					{
						$result = "Error in moving uploading file";
						if (move_uploaded_file($_FILES["video"]["tmp_name"], Yii::app()->params->uploadPath .'/'. Yii::app()->db->lastInsertID . '.flv'))
						{
							$result = "1";
						}
					}
				}
		
			}

		}
		echo CJSON::encode(array("result"=> $result));
		Yii::app()->end();

	}    
}    