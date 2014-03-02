<?php

/**
 * RegisterForm class.
 * RegisterForm is the data structure for registering
 * the user. It is used by the 'register' action of 'SiteController'.
 */
class RegisterForm extends CFormModel
{
	public $register;
	public $email;
	public $emailAgain;
	public $name;
	public $lastName;
	public $password;
	public $passwordAgain;
	public $image;
	public $account_type;
	public $ac_id;

	/**
	 * Declares the validation rules.
	 * The rules state that username and password are required,
	 * and password needs to be authenticated.
	 */
	public function rules()
	{
		return array(
			// username and password are required
		/*
			array('email, name, password, account_type, ac_id, passwordAgain', 'required',
			'message'=>'Field cannot be blank!'),
		*/
			array('email, emailAgain, name, lastName, password, passwordAgain', 'required', 'message'=>Yii::t('site', 'Please, enter the field')),

			//array('password, passwordAgain', 'required', 'message'=>Yii::t('site', 'Enter the field')),
								
			array('email, emailAgain', 'email', 'message'=>Yii::t('site', 'E-mail not valid!')),
				
			array('emailAgain', 'compare', 'compareAttribute'=>'email', 'message'=>Yii::t('site', 'E-mails not same!')),

			array('password', 'length', 'min'=>5, 'message'=>Yii::t('site', 'Minimum 5 characters')),
				
			//array('password', 'checkLength'),
				
			// password needs to be same
			array('passwordAgain', 'compare', 'compareAttribute'=>'password', 'message'=>Yii::t('site', 'Passwords not same!')),
			
			//These attributes should be defined as safe in order to be usable
			array('ac_id, account_type', 'safe'),
			
			array('email', 'checkFacebook'),
				
			array('email', 'isExists'),

			//array('email', 'checkEmailDomain'),				
			
			//array('image', 'isExists')
		);
	}

	/**
	 * Declares attribute labels.
	 */
	public function attributeLabels()
	{
		return array(
			'register'=>Yii::t('common', 'Sign Up'),
			'email'=>Yii::t('site', 'E-mail'),
			'emailAgain'=>Yii::t('site', 'E-mail (Again)'),
			'name'=>Yii::t('site', 'First Name'),
			'lastName'=>Yii::t('site', 'Last Name'),
			'password'=>Yii::t('site', 'Password'),
			'passwordAgain'=>Yii::t('site', 'Password (Again)'),			
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
			
			if(Users::model()->find($criteria) != null) 
			{
				$this->addError('email',Yii::t('site', 'E-mail is already registered!'));
			}
			else if(UserCandidates::model()->find($criteria) != null)
			{
				$this->addError('email',Yii::t('site', 'Registration incomplete, please request activation e-mail below'));
			}
			
// 			if ($data == null) {
// 				$data = UserCandidates::model()->find($criteria);
// 			}
// 			if ($data != null) {
// 				$this->addError('email',Yii::t('site', 'E-mail is already registered!'));				
// 			}							
		}
	}
	
	public function checkFacebook($attribute,$params)
	{
		if(!$this->hasErrors())
		{
			$appVersion = null;
				
			if(Users::model()->isFacebookUser($this->email, $appVersion))
			{
				//1.0.16'dan sonraki uygulamalarda bu kontrolü yap, eski uygulamalari bozma
				if($appVersion > "1.0.16")
				{
					$this->addError('email',Yii::t('site', 'You are already registered as Facebook user for our service. Please use \"Log in with facebook\" button to log in to your Traceper account.'));
				}
			}
		}
	}	

// 	public function checkLength($attribute,$params)
// 	{
// 		//if(!$this->hasErrors())
// 		{
// 			if(strlen($this->password) < 5)
// 				//if(true)
// 			{
// 				$this->addError('password',Yii::t('site', 'Minimum 5 characters'));
// 			}			
// 		}
// 	}

// 	public function checkEmailDomain($attribute,$params)
// 	{
// 		//if(!$this->hasErrors())
// 		{
// 			//list($user, $domain) = explode('@', $this->email); -> Buna e-mail alanı boşken hata veriyor
// 			$user = strtok($this->email, "@");
// 			$domain = strtok("@");
						
// 			//list($domainName, $extension) = explode('.', $domain);  -> Buna e-mail alanı boşken hata veriyor
// 			$domainName = strtok($domain, ".");
// 			$extension = strtok(".");
									
// 			if(($domain == 'gmial.com') || ($domain == 'gmil.com') || ($domain == 'gmal.com') || ($domain == 'glail.com'))
// 			{
// 				$this->addError('email',Yii::t('site', 'Did you mean ').$user.'@gmail.com?');
// 			}
// 			else if(($domain == 'yaho.com') || ($domain == 'yhao.com') || ($domain == 'yhaoo.com') || ($domain == 'yhoo.com'))
// 			{
// 				$this->addError('email',Yii::t('site', 'Did you mean ').$user.'@yahoo.com?');
// 			} 
// 			else if(($domain == 'hotmial.com') || ($domain == 'hotmal.com') || ($domain == 'hotmil.com') || ($domain == 'htmail.com') || ($domain == 'hotma.com'))
// 			{
// 				$this->addError('email',Yii::t('site', 'Did you mean ').$user.'@hotmail.com?');
// 			}	
// 			else if(($domain == 'myet.com') || ($domain == 'mynt.com') || ($domain == 'mymet.com'))
// 			{
// 				$this->addError('email',Yii::t('site', 'Did you mean ').$user.'@mynet.com?');
// 			}
// 			else if(($extension == 'con') || ($extension == 'co'))
// 			{
// 				$this->addError('email',Yii::t('site', 'Did you mean ').$user.'@'.$domainName.'.com?');
// 			}					
// 		}
// 	}	
}
