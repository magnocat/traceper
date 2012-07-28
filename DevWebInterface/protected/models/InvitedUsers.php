<?php

/**
 * This is the model class for table "traceper_invitedusers".
 *
 * The followings are the available columns in table 'traceper_invitedusers':
 * @property string $Id
 * @property string $email
 * @property string $dt
 *
 * The followings are the available model relations:
 * @property TraceperFriends[] $traceperFriends
 * @property TraceperFriends[] $traceperFriends1
 */
class InvitedUsers extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return Users the static model class
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
		return 'traceper_invitedusers';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
				array('email', 'required'),
				array('email', 'length', 'max'=>100),
				// The following rule is used by search().
				// Please remove those attributes that should not be searched.
				array('Id, email, dt', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array();

		//		return array(
		//			'traceperFriends' => array(self::HAS_MANY, 'Friends', 'friend2'),
		//			'traceperFriends1' => array(self::HAS_MANY, 'Friends', 'friend1'),
		//		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
				'Id' => 'ID',
				'email' => 'E-mail',
				'dt' => 'Data Time',
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
		$criteria->compare('email',$this->email,true);
		$criteria->compare('dt',$this->dt,true);

		return new CActiveDataProvider($this, array(
				'criteria'=>$criteria,
		));
	}


	public function saveInvitedUsers($email, $dt){
		$invitedUsers = new InvitedUsers;
		$invitedUsers->email = $email;
		$invitedUsers->dt = $dt;

		return $invitedUsers->save();
	}
}