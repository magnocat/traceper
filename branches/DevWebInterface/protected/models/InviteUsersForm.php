<?php

/**
 * InviteUsersForm class.
 * InviteUsersForm is the data structure for inviting user's friends
 * to the web site. It is used by the 'inviteUsers' action of 'SiteController'.
 */
class InviteUsersForm extends CFormModel
{
	public $emails;
	public $invitationMessage;

	/**
	 * Declares the validation rules.
	 * The rules state that username and password are required,
	 * and password needs to be authenticated.
	 */
	public function rules()
	{
		return array(
			// username and password are required
			array('emails', 'required', 'message'=>Yii::t('site', 'Please, enter the field')),
			array('emails', 'ext.MultiEmailValidator', 'delimiter'=>',', 'min'=>1, 'max'=>10),
			array('emails', 'isRegisteredOrInvited'),			
			array('invitationMessage', 'length', 'max'=>500), //Bu alanin duzgun calismasi icin bir rule tanimlamak gerekiyor
		);
	}

	/**
	 * Declares attribute labels.
	 */
	public function attributeLabels()
	{
		return array(
			'emails'=>Yii::t('site', 'E-mails'),
			'invitationMessage'=>Yii::t('site', 'Message for your friends'),
		);
	}

	public function isRegisteredOrInvited($attribute,$params)
	{
		if(!$this->hasErrors())
		{
			$values = trim($this->emails);
			$values = str_replace(array(" ",",","\r","\n"),array(",",",",",",","),$values);
			$values = str_replace(",,", ",",$values);
			$values = explode(",", $values);
			
			foreach($values as $value)
			{
				$value = trim($value);
			
				$criteria=new CDbCriteria;
				$criteria->select='email';
				$criteria->condition='email=:email';		
				$criteria->params=array(':email'=>$value);
				//$data = Users::model()->find($criteria);
					
				if(Users::model()->find($criteria) != null)
				{
					//$this->addError('emails',$value);
					$this->addError('emails',Yii::t('site', '"{value}" is already registered. You could search by name and add as friend at "Users" tab.', array('{value}'=>$value)));
				}
				else if(InvitedUsers::model()->find($criteria) != null)
				{
					//$this->addError('emails',$value);
					$this->addError('emails',Yii::t('site', '"{value}" has been invited before', array('{value}'=>$value)));
				}				
			}						
		}
	}	
}
