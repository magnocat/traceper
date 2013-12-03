<?php

/**
 * This is the model class for table "traceper_privacy_groups".
 *
 * The followings are the available columns in table 'traceper_privacy_groups':
 * @property string $id
 * @property string $name
 * @property integer $type
 * @property string $owner
 * @property string $description
 * @property integer $allowedToSeeOwnersPosition
 */
class PrivacyGroups extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return PrivacyGroups the static model class
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
		return 'traceper_privacy_groups';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('name, type, owner', 'required'),
			array('type, allowedToSeeOwnersPosition', 'numerical', 'integerOnly'=>true),
			array('name', 'length', 'max'=>45),
			array('owner', 'length', 'max'=>10),
			array('description', 'length', 'max'=>500),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, name, type, owner, description, allowedToSeeOwnersPosition', 'safe', 'on'=>'search'),
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
			'id' => 'ID',
			'name' => 'Name',
			'type' => 'Type',
			'owner' => 'Owner',
			'description' => 'Description',
			'allowedToSeeOwnersPosition' => 'Allowed To See Owners Position',
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

		$criteria->compare('id',$this->id,true);
		$criteria->compare('name',$this->name,true);
		$criteria->compare('type',$this->type);
		$criteria->compare('owner',$this->owner,true);
		$criteria->compare('description',$this->description,true);
		$criteria->compare('allowedToSeeOwnersPosition',$this->allowedToSeeOwnersPosition);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
	
	public function saveGroup($name, $type, $ownerId, $description){
		$privacyGroups = new PrivacyGroups;
		$privacyGroups->name = $name;
		$privacyGroups->type = $type;
		$privacyGroups->owner = $ownerId;
		$privacyGroups->description = $description;
	
		return $privacyGroups->save();
	}	

	public function getGroupsList($ownerId, $type, $itemCountInOnePage) {
		
		if(isset(Yii::app()->session['groupsPageSize']) == false)
		{
			Yii::app()->session['groupsPageSize'] = Yii::app()->params->uploadCountInOnePage;
		}		
	
		$dataProvider=new CActiveDataProvider('PrivacyGroups', array(
				'criteria'=>array(
						'condition'=>'owner=:owner AND type=:type',
						'params'=>array(':owner'=>$ownerId, ':type'=>$type),
						//'order'=>'create_time DESC',
						//'with'=>array('author'),
				),
				'pagination'=>array(
						'pageSize'=>Yii::app()->session['groupsPageSize'],
				),
		));
	
		return $dataProvider;
	}

	public function deleteGroup($groupId,$ownerId) {
	
		$result = PrivacyGroups::model()->find(array('condition'=>'id=:groupId AND owner=:ownerId',
				'params'=>array(':groupId'=>$groupId,
						':ownerId'=>$ownerId)
		)
		);
			
		if($result != null)
		{
			if($result->delete()) // Delete the selected group
			{
				//Group deleted from the traceper_groups table
				$returnResult=1;
			}
			else
			{
				$returnResult=0;
			}
		}
		else
		{
			//traceper_groups table has not the selected group of the owner
			$returnResult=-1;
		}
		return $returnResult;
	}	
	
	public function updatePrivacySettings($groupId, $allowToSeeMyPosition) {
		$count= PrivacyGroups::model()->updateByPk($groupId, array("allowedToSeeOwnersPosition"=>$allowToSeeMyPosition));
		return $count;
	}	
}