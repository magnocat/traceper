<?php

/**
 * This is the model class for table "traceper_deleted_uploads".
 *
 * The followings are the available columns in table 'traceper_deleted_uploads':
 * @property integer $uploadId
 * @property integer $publicData
 * @property integer $userId
 * @property string $deletionTime
 */
class DeletedUploads extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return DeletedUploads the static model class
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
		return 'traceper_deleted_uploads';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('uploadId, userId, deletionTime', 'required'),
			array('uploadId, publicData, userId', 'numerical', 'integerOnly'=>true),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('uploadId, publicData, userId, deletionTime', 'safe', 'on'=>'search'),
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
			'uploadId' => 'Upload',
			'publicData' => 'Public Data',
			'userId' => 'User',
			'deletionTime' => 'Deletion Time',
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

		$criteria->compare('uploadId',$this->uploadId);
		$criteria->compare('publicData',$this->publicData);
		$criteria->compare('userId',$this->userId);
		$criteria->compare('deletionTime',$this->deletionTime,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
	
	public function saveDeletion($uploadId, $publicData, $userId, $time) {
		$deletedUpload = new DeletedUploads;
	
		$deletedUpload->uploadId = $uploadId;
		$deletedUpload->publicData = $publicData;
		$deletedUpload->userId = $userId;
		$deletedUpload->deletionTime = $time;
	
		return $deletedUpload->save();
	}
	
	public function getDeletedList($friendList, $time) {
	
		if($friendList == null)
		{
			$sql = 'SELECT uploadId
			FROM '. DeletedUploads::model()->tableName() . '
			WHERE (publicData = 1)
			AND unix_timestamp(deletionTime) >= '. $time;			
		}
		else
		{
			$sql = 'SELECT uploadId
			FROM '. DeletedUploads::model()->tableName() . '
			WHERE (userId in ('. $friendList .')
			OR publicData = 1)
			AND unix_timestamp(deletionTime) >= '. $time;			
		}
	
		$dataReader = Yii::app()->db->createCommand($sql)->query();
	
		return $dataReader;
	}	
}