<?php

require_once 'BaseTable.php';

class RatingPlugin extends BaseTable {
	 
	const table_name = 'upload_rating';
	const field_upload_id = 'upload_id';
	const field_voting_count = 'voting_count';
	const field_points = 'points';

	function __construct($tablePrefix, $dbc)
	{
		parent::__construct($tablePrefix.'_'.self::table_name, $dbc);
	}

	function process($reqArray)
	{
		 
		switch ($reqArray['action'])
		{
			case "SetUploadRating":
				$out = MISSING_PARAMETER;
	
				if (isset($reqArray['uploadId']) && $reqArray['uploadId'] != null &&
				isset($reqArray['points']) && $reqArray['points'] != null
				)
				{
					$points = (int)$reqArray['points'];
					$uploadId = (int)$reqArray['uploadId'];
						
					$updateArray = array(RatingPlugin::field_voting_count => RatingPlugin::field_voting_count. "+1", RatingPlugin::field_points => RatingPlugin::field_points.'+'.$points);
					$condArr = array(RatingPlugin::field_upload_id => $uploadId);
						
					$result = $this->update($updateArray, $condArr);
					$out = FAILED;
						
					if($result > 0)
					{
						$out = SUCCESS;
					}
					else if($result === 0)
					{
						$insertArray = array(RatingPlugin::field_upload_id => $uploadId, RatingPlugin::field_points => $points);
						$out = FAILED;
						if ($this->insert($insertArray)) {
							$out = SUCCESS;
						}
					}
				}
			break;
			case "GetUploadRating":
				
				break;

			
		}
		return $out;
	}
	
	function getRating($uploadId)
	{
		$fieldsArray = array(RatingPlugin::field_points .'/' . RatingPlugin::vote_count . ' as rating');
		$condArr = array(RatingPlugin::field_upload_id => $uploadId);			
		
		$this->select($fieldsArray, $condArr);	
	}

}

?>
