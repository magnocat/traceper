<?php
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
	}	
}