<?php

/**
 * This is the model class for table "traceper_geofence_user_relation".
 *
 * The followings are the available columns in table 'traceper_geofence_user_relation':
 * @property string $Id
 * @property string $geofenceId
 * @property string $userId
 * @property integer $status
 */
class GeofenceUserRelation extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return GeofenceUserRelation the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'traceper_geofence_user_relation';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('geofenceId, userId', 'required'),
			array('status', 'numerical', 'integerOnly'=>true),
			array('geofenceId, userId', 'length', 'max'=>11),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('Id, geofenceId, userId, status', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'Id' => 'ID',
			'geofenceId' => 'Geofence',
			'userId' => 'User',
			'status' => 'Status',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search()
	{
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('Id',$this->Id,true);
		$criteria->compare('geofenceId',$this->geofenceId,true);
		$criteria->compare('userId',$this->userId,true);
		$criteria->compare('status',$this->status);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
	
	public function saveUserGeofenceRelation($geofenceId, $userId){
		$geofenceUserRelation = new GeofenceUserRelation;
		$geofenceUserRelation->geofenceId = $geofenceId;
		$geofenceUserRelation->userId = $userId;								
		
		//TODO: status must be updated, firstly save not in geofence
		$geofenceUserRelation->status = 0;
	
		return $geofenceUserRelation->save();
	}
	
	public function deleteGeofenceMember($friendId,$unselectedFriendGroup) {
		
		$relationQueryResult = GeofenceUserRelation::model()->find(array('condition'=>'userId=:userId AND geofenceId=:geofenceId',
				'params'=>array(':userId'=>$friendId,
						':geofenceId'=>$unselectedFriendGroup)));
		
		if($relationQueryResult != null)
		{
			if($relationQueryResult->delete()) // delete the friend from the group
			{
				//Relation deleted from the traceper_user_group_relation table
				$returnResult=1;
			}
			else
			{
				$returnResult=0;
			}
		}
		else
		{
			//traceper_user_group_relation table has not the desired relation, so do nothing
			$returnResult=-1;
		}
		return $returnResult;
	}
}