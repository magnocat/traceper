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
}