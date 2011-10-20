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
		
		$sql = 'SELECT u.Id as id, u.realname, u.latitude, u.longitude 
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


}