<?php

require_once(dirname(__FILE__).'/PHPMailer_5.2.4/class.phpmailer.php');

/**
 * Controller is the customized base controller class.
 * All controller classes for this application should extend from this base class.
 */
class Controller extends CController
{
	/**
	 * @var string the default layout for the controller view. Defaults to '//layouts/column1',
	 * meaning using a single column layout. See 'protected/views/layouts/column1.php'.
	 */
	public $layout='//layouts/column1';
	/**
	 * @var array context menu items. This property will be assigned to {@link CMenu::items}.
	 */
	public $menu=array();
	/**
	 * @var array the breadcrumbs of the current page. The value of this property will
	 * be assigned to {@link CBreadcrumbs::links}. Please refer to {@link CBreadcrumbs::links}
	 * for more details on how to specify this property.
	 */
	public $breadcrumbs=array();
	
	//Used by UsersController and SiteController
	protected function getaddress($lat, $lng, &$par_adress, &$par_country)
	{
		$isTurkey = false;
		
		//Konum Turkiye'de ise
		if(((35.85 <= $lat) && ($lat <= 42.1)) && ((25.6 <= $lng) && ($lng <= 44.8)))
		{
			$url = 'http://maps.googleapis.com/maps/api/geocode/json?latlng='.trim($lat).','.trim($lng).'&sensor=false&language=tr';
			$isTurkey = true;
		}
		else
		{
			$url = 'http://maps.googleapis.com/maps/api/geocode/json?latlng='.trim($lat).','.trim($lng).'&sensor=false&language=en';
		}

		// 		$json = @file_get_contents($url);
		// 		$data=json_decode($json);
		// 		$status = $data->status;
	
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url); //The URL to fetch. This can also be set when initializing a session with curl_init().
		curl_setopt($ch, CURLOPT_HEADER, 0); // Removes the headers from the output
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); //TRUE to return the transfer as a string of the return value of curl_exec() instead of outputting it out directly.
		curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,5); //The number of seconds to wait while trying to connect.
		curl_setopt($ch, CURLOPT_FAILONERROR, TRUE); //To fail silently if the HTTP code returned is greater than or equal to 400.
		curl_setopt($ch, CURLOPT_TIMEOUT, 10); //The maximum number of seconds to allow cURL functions to execute.
		$response = curl_exec($ch);
		curl_close($ch);
	
		$data = json_decode($response);
		$status = $data->status;
	
		$bSuccess = false;

		if($status=="OK")
		{
			//return $data->results[0]->formatted_address;
			
// 			Fb::warn(count($data->results), "count");
// 			Fb::warn(end($data->results)->formatted_address, "end");

			//Fb::warn($data->results[0]->formatted_address, "formatted_address");

			$address = "";
			$country = "";
			
			if($isTurkey)
			{
				$addressPieces = explode(", Türkiye", $data->results[0]->formatted_address);
				$address = $addressPieces[0];
				$country = "Turkey";
			}
			else
			{
				$formattedAddress = $data->results[0]->formatted_address;
				$countryPart = strrchr($formattedAddress, ','); //strrchr() returs the substring from right cut by the specified character
				$addressPieces = explode($countryPart, $formattedAddress);
				$address = $addressPieces[0];
				$countryPieces = explode(", ", $countryPart);
				$country = $countryPieces[1];
			}
			
			//Fb::warn($address, "getaddress()");
			//Fb::warn($country, "Country");
			
				
// 			if(end($data->results)->formatted_address == "Turkey")
// 			{
// 				//Konuma göre ilgili bolgenin ulke indeksi degisebiliyor, fakat bir kere ulke indeksi bulunduktan sonra
// 				//diger adres bilesenleri bu indekse gore bulunabiliyor
// 				$countryIndex = 0;
// 				$city = null;
					
// 				foreach($data->results[0]->address_components as $addressComp)
// 				{
// 					if($addressComp->long_name == "Turkey")
// 					{
// 						break;
// 					}
// 					else if(strpos($addressComp->long_name, "Province"))
// 					{
// 						$cityPieces = explode(" Province", $addressComp->long_name);
// 						$city = $cityPieces[0];	

// 						//Fb::warn($city, "city");
// 					}
	
// 					$countryIndex++;
// 				}
	
// 				$districtIndex = $countryIndex - 4;
// 				$streetIndex = $countryIndex - 5;
// 				$cityIndex = $countryIndex - 3;
// 				$townIndex = $countryIndex - 2;
// 				$zipCodeIndex = $countryIndex + 1;
	
// 				if($districtIndex >= 0)
// 				{
// 					$address .= $data->results[0]->address_components[$districtIndex]->long_name;					
// 				}
				
// 				if($streetIndex >= 0)
// 				{
// 					$address .= ", ".$data->results[0]->address_components[$streetIndex]->short_name;
					
// 					//Cadde bilgisinden once de bilgi oldugu durumda bu bilgiyi de ekle
// 					if($streetIndex > 0)
// 					{
// 						$address .= " ".$data->results[0]->address_components[$streetIndex-1]->long_name;
// 					}					
// 				}

// 				if($zipCodeIndex >= 0)
// 				{
// 					$address .= ", ".$data->results[0]->address_components[$zipCodeIndex]->long_name." ";
// 				}
				
// 				if($townIndex >= 0)
// 				{
// 					$address .= $data->results[0]->address_components[$townIndex]->long_name."/";
// 				}

// 				//$address .= $data->results[0]->address_components[$cityIndex]->long_name;
// 				$address .= $city;
// 			}
// 			else
// 			{
// 				$formattedAddress = $data->results[0]->formatted_address;
// 				$countryPart = strrchr($formattedAddress, ','); //strrchr() returs the substring from right cut by the specified character
// 				$addressPieces = explode($countryPart, $formattedAddress);
// 				$address = $addressPieces[0];
// 			}
	
			//Fb::warn($data->results[0]->formatted_address, "formatted_address");
			//Fb::warn($address, "getaddress()");
			//Fb::warn("Turkey", "Country");
				
			$par_adress = $address;
			$par_country = $country;
			$bSuccess = true;
		}
		else
		{
			$bSuccess = false;
		}
	
		return $bSuccess;
	}	
	
	function init()
	{
		parent::init();
		$app = Yii::app();
		
		if (isset($_POST['_lang']))
		{
			$app->language = $_POST['_lang'];
			//$app->session['_lang'] = $app->language;
		}
		else if (isset($app->session['_lang']))
		{
			$app->language = $app->session['_lang'];
		}
		else 
		{
			$app->language = substr(Yii::app()->getRequest()->getPreferredLanguage(), 0, 2);
			//$app->session['_lang'] = substr(Yii::app()->getRequest()->getPreferredLanguage(), 0, 2);
		}
		
		// register class paths for extension captcha extended
		Yii::$classMap = array_merge( Yii::$classMap, array(
				'CaptchaExtendedAction' => Yii::getPathOfAlias('ext.captchaExtended').DIRECTORY_SEPARATOR.'CaptchaExtendedAction.php',
				'CaptchaExtendedValidator' => Yii::getPathOfAlias('ext.captchaExtended').DIRECTORY_SEPARATOR.'CaptchaExtendedValidator.php'
		));		
	}
	
	protected function afterRender($view, &$output) {
		parent::afterRender($view,$output);
		//Yii::app()->facebook->addJsCallback($js); // use this if you are registering any $js code you want to run asyc
		Yii::app()->facebook->initJs($output); // this initializes the Facebook JS SDK on all pages
		Yii::app()->facebook->renderOGMetaTags(); // this renders the OG tags
		return true;
	}

	public function SMTP_UTF8_mail($fromEmail, $fromName, $toEmail, $toName, $subject, $message, $addFooter = true)
	{
		if($addFooter == true)
		{
			$message .= '<br/><br/>';
			$message .= Yii::t('site', 'The Traceper Team').'<br/><br/><br/>';
			$message .= Yii::t('site', 'Please note: This is an auto generated e-mail and it was sent from an unmonitored e-mail addres. Therefore do not reply to this message and use our <a href="mailto:contact@traceper.com">contact (contact@traceper.com)</a> address if you need to contact us.');			
		}

		header("Content-Type: text/html; charset=utf-8");
	
		error_reporting(E_ALL);
		//error_reporting(E_STRICT);
		date_default_timezone_set('Europe/Istanbul');
	
		$mail = new PHPMailer();
		$body = '
		<html>
		<head>
		<title>'.$subject.'</title>
		</head>
		<body>'.$message.
		'</body>
		</html>
		';
		$mail->Subject = $subject;
		$mail->MsgHTML($body);
		//$mail->Body = $message; //$mail->MsgHTML($body);
	
		$mail->IsSMTP(); // telling the class to use SMTP
		$mail->Host = "mail.traceper.com"; // SMTP server
		$mail->SMTPDebug = 1; // enables SMTP debug information (for testing) - 1 = errors and messages / 2 = messages only
		$mail->SMTPAuth   = true;                  // enable SMTP authentication
		$mail->Port       = 587;                    // set the SMTP port for the GMAIL server
		$mail->Username   = "noreply@traceper.com"; // SMTP account username
		$mail->Password   = "yudu1234yudu";        // SMTP account password
		$mail->IsHTML(true);
		$mail->SetFrom($fromEmail, $fromName);
		$mail->AddReplyTo($fromEmail, $fromName);
		$mail->CharSet    = 'utf-8';
		$mail->AltBody    = Yii::t('site', 'Allow the HTML browser to view this message!'); // optional, comment out and test
	
		$mail->AddAddress($toEmail, $toName);
	
		return $mail->Send();
	
		// 		if(!$mail->Send()) {
		// 			$arr = array ('kmt'=>0);
		// 			echo json_encode($arr);
		// 		} else {
		// 			$arr = array ('kmt'=>1);
		// 			echo json_encode($arr);
		// 		}
	}

	public function sendErrorMail($errorLabel, $par_subject, $par_message)
	{
		//Sunucu localhost degilse mail gonder
		if(Yii::app()->request->getServerName() != "localhost")
		{
			if(isset(Yii::app()->session[$errorLabel]) == false)
			{
				Yii::app()->session[$errorLabel] = 0;
			}
				
			if(Yii::app()->session[$errorLabel] < 1)
			{
				$message = '';
					
				if (Yii::app()->user->isGuest == false)
				{
					$name = null;
					$email = null;
						
					Users::model()->getUserInfo(Yii::app()->user->id, $name, $email);
				
					$message .= 'User Info: '.'<br/><br/>';
					$message .= 'Id: '.Yii::app()->user->id.'<br/>';
					$message .= 'Name: '.$name.'<br/>';
					$message .= 'E-mail: '.$email;
					$message .= '<br/>----------------------------------------------------------------------------------------------<br/>';
				}
					
				$message .= 'Server Info: '.Yii::app()->request->getServerName();
				$message .= '<br/>----------------------------------------------------------------------------------------------<br/>';
					
				if (isset($_REQUEST['client']) && $_REQUEST['client']=='mobile')
				{
					//Fb::warn("client=mobile", "sendErrorMail()");
				
					$message .= 'Call Type: MOBILE';
					$message .= '<br/>----------------------------------------------------------------------------------------------<br/>';
				}
				else
				{
					//Fb::warn("ELSE", "sendErrorMail()");
				}
					
				$message .= $par_message;
					
				$this->SMTP_UTF8_mail(Yii::app()->params->contactEmail, 'Traceper Error Handler', Yii::app()->params->contactEmail, 'Traceper', $par_subject, $message, false /*Do not add footer for error message*/);
				
				//Fb::warn("mail SENT", "sendErrorMail()");
								
				Yii::app()->session[$errorLabel] = Yii::app()->session[$errorLabel] + 1;
			}
		}
		else
		{
			Fb::warn("mail NOT sent for localhost", "sendErrorMail()");
		}
	}	
}