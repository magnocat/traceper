<?php

/**
 * This is the model class for table "traceper_reset_password".
 *
 * The followings are the available columns in table 'traceper_reset_password':
 * @property string $email
 * @property string $token
 * @property string $requestTime
 */
class ResetPassword extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return ResetPassword the static model class
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
		return 'traceper_reset_password';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('email, token, requestTime', 'required'),
			array('email', 'length', 'max'=>100),
			array('token', 'length', 'max'=>50),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('email, token, requestTime', 'safe', 'on'=>'search'),
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
			'email' => 'Email',
			'token' => 'Token',
			'requestTime' => 'Request Time',
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

		$criteria->compare('email',$this->email,true);
		$criteria->compare('token',$this->token,true);
		$criteria->compare('requestTime',$this->requestTime,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
	
	public function saveToken($email, $token)
	{
		$record = ResetPassword::model()->find('email=:email', array(':email'=>$email));
	
		if ($record != null)
		{
			$record->email = $email;
			$record->token = $token;
			$record->requestTime = date("Y-m-d H:i:s");
				
			$result = $record->save();
		}
		else
		{
			$newRecord=new ResetPassword;
				
			$newRecord->email = $email;
			$newRecord->token = $token;
			$newRecord->requestTime = date("Y-m-d H:i:s");
				
			$result = $newRecord->save();
		}
	
		return $result;
	}
	
	public function tokenExists($token)
	{
		$record = ResetPassword::model()->find('token=:token', array(':token'=>$token));
	
		return ($record != null);
	}
	
	public function isRequestTimeValid($token)
	{
		$record = ResetPassword::model()->find('token=:token', array(':token'=>$token));
	
		$result = false;
		
		if($record != null)
		{			
			if((time()-(60*60*24)) < strtotime($record->requestTime))
			{
				$result = true;				
			}
			else
			{
				$result = false;
			}
		}
		else
		{
			$result = false;
		}
		
		return $result;
	}	
	
	public function getEmailByToken($token)
	{
		$record = ResetPassword::model()->find('token=:token', array(':token'=>$token));
	
		if($record != null)
		{
			$result = $record->email;
		}
		else
		{
			$result = null;
		}
	
		return $result;
	}
	
	public function deleteToken($token)
	{
		$record = ResetPassword::model()->find('token=:token', array(':token'=>$token));
	
		$result = false;
	
		if($record != null)
		{
			$result = $record->delete();
		}
	
		return $result;
	}	
}