<?php 

class AuxiliaryFriendsOperator {
	
	public static function getFriendIdList() 
	{
		$sql = 'SELECT IF (friend1!=' .Yii::app()->user->id. ', friend1, friend2) as friend
				FROM '.Friends::model()->tableName().'
				WHERE (friend1 = ' . Yii::app()->user->id . '
					   OR friend2 = ' . Yii::app()->user->id .') 
					  AND status = 1';
		
		$friendsResult = Yii::app()->db->createCommand($sql)->queryAll();
		$length = count($friendsResult);
		
		$friends = array();
		for ($i = 0; $i < $length; $i++) {
			array_push($friends, $friendsResult[$i]['friend']);			
		}
				
		return implode(',', $friends);
	}
	
	
}


?>