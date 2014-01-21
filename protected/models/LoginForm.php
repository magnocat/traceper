<?php

/**
 * LoginForm class.
 * LoginForm is the data structure for keeping
 * user login form data. It is used by the 'login' action of 'SiteController'.
 */
class LoginForm extends CFormModel
{
	public $email;
	public $password;
	public $rememberMe;
	

	private $_identity;

	/**
	 * Declares the validation rules.
	 * The rules state that username and password are required,
	 * and password needs to be authenticated.
	 */
	public function rules()
	{
		return array(
			// username and password are required
			array('email, password', 'required','message'=>Yii::t('site', 'Please, enter the field')),
			
			array('email', 'email', 'message'=>Yii::t('site', 'E-mail not valid!')),
			
			// rememberMe needs to be a boolean
			array('rememberMe', 'boolean'),
			
			array('email', 'isExists'),
				
			// password needs to be authenticated
			array('password', 'authenticate'),
		);
	}

	/**
	 * Declares attribute labels.
	 */
	public function attributeLabels()
	{
		return array(
			'rememberMe'=>Yii::t('site', 'Remember me'),
			'email'=>Yii::t('site', 'E-mail'),
			'password'=>Yii::t('site', 'Password'),
		);
	}
	
	public function isExists($attribute,$params)
	{
		if(!$this->hasErrors())
		{
			$criteria=new CDbCriteria;
			$criteria->select='email';
			$criteria->condition='email=:email';
			$criteria->params=array(':email'=>$this->email);
			//$data = Users::model()->find($criteria);
				
			if(Users::model()->find($criteria) == null) //e-mail Users tablosunda yoksa
			{
				if(UserCandidates::model()->find($criteria) != null)
				{
					$this->addError('email',Yii::t('site', 'Activate your account first')); //Candidates tablosunda ise
				}
				else //Her iki tabloda da yoksa
				{
					//Aslinda kullanicinin hatali oldugu belli, fakat guvenlik acisinda hata boyle veriliyor
					$this->addError('password',Yii::t('site', 'Incorrect password or e-mail'));
				}				
			}
		}
	}	

	/**
	 * Authenticates the password.
	 * This is the 'authenticate' validator as declared in rules().
	 */
	public function authenticate($attribute,$params)
	{
		if(!$this->hasErrors())
		{
			$this->_identity=new UserIdentity($this->email, $this->password);
			switch($this->_identity->authenticate())
			{
				//case CUserIdentity::ERROR_USERNAME_INVALID:					
				case CUserIdentity::ERROR_PASSWORD_INVALID:
					//Aslinda sifrenin hatali oldugu belli, fakat guvenlik acisinda hata boyle veriliyor
					$this->addError('password',Yii::t('site', 'Incorrect password or e-mail'));
					break;
					
// 				case UserIdentity::ERROR_REGISTRATION_UNCOMPLETED:
// 					$this->addError('email',Yii::t('site', 'Activate your account first'));
// 					break;
					
				case CUserIdentity::ERROR_NONE:
					break;					
			}	
				
		}
	}

	/**
	 * Logs in the user using the given username and password in the model.
	 * @return boolean whether login is successful
	 */
	public function login()
	{
		if($this->_identity === null)
		{
			$this->_identity = new UserIdentity($this->email, $this->password);
			$this->_identity->authenticate();
		}
		
		if($this->_identity->errorCode === UserIdentity::ERROR_NONE)
		{
			$duration = $this->rememberMe ? 3600*24*30 : 0; // 30 days
			Yii::app()->user->login($this->_identity, $duration);
			
			return true;
		}
		else
		{
			return false;
		}
	}
	
	public function getName() {
		
		return $this->_identity->getName();
	}
}
