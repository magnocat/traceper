<?php
/**
 * @name EasyTabs
 * @author Artur Oliveira - artur.oliveira@gmail.com - 2013-01-09
 * @version 1.0
 * @see http://yiidemo.arturoliveira.com/yiidemo/easyTabs/index
 * Uses plugin from http://os.alfajango.com/easytabs/
 *	Please refer to http://os.alfajango.com/easytabs/#available-options for options
 * Uses plugin from http://benalman.com/projects/jquery-hashchange-plugin/
 *	Please refer to http://benalman.com/code/projects/jquery-hashchange/docs/files/jquery-ba-hashchange-js.html for options
 */
class EasyTabs extends CWidget
{
	/**
	 * @var string the tag name for the view container. Defaults to 'div'.
	 */
	public $tagName='div';
	/**
	 * @var array menu tabs configuration. Each array element represents the configuration
	 * for one particular tab which should be an array.
	 */
	public $tabs=array();
	/**
	 * @var string the base script URL for all tile slide menu resources (eg javascript, CSS file, images).
	 * Defaults to null, meaning using the integrated tile slide menu resources (which are published as assets).
	 */
	public $baseScriptUrl;
	/**
	 * @var string the URL of the CSS file used by this tile slide menu. Defaults to null, meaning using the integrated
	 * CSS file. If this is set false, you are responsible to explicitly include the necessary CSS file in your page.
	 */
	public $cssFile;	
	/**
	 * @var array the initial JavaScript options that should be passed to the EasyTabs plugin.
	 */
	public $options=array();
	/**
	 * @var array the HTML options for the view container tag.
	 */
	public $htmlOptions=array();
	/**
	 * Initializes the widget
	 * This method will initialize required property value.
	 */
 	public function init()
	{
		parent::init();

		$this->htmlOptions['class']='easy-tabs';

		if($this->baseScriptUrl===null)
			$this->baseScriptUrl=Yii::app()->getAssetManager()->publish(Yii::getPathOfAlias('ext.'.__CLASS__.'.assets'));
		if($this->cssFile!==false)
		{
			if($this->cssFile===null)
				$this->cssFile=$this->baseScriptUrl.'/'.__CLASS__.'.css';
			Yii::app()->getClientScript()->registerCssFile($this->cssFile);
		}
		
		$this->htmlOptions['id']=$this->getId();
	}

	/**
	 * Renders the view.
	 * This is the main entry of the whole view rendering.
	 * Child classes should mainly override {@link renderContent} method.
	 */
	public function run()
	{
		$this->registerClientScript();
		
		$id=$this->getId();
		echo CHtml::openTag('div',array('class'=>'easy-tabs-tab-container','id'=>$id));
		$this->renderTabs();
		$this->renderTabContent();
		echo CHtml::closeTag('div');
	}
	
	/**
	 * Registers necessary client scripts.
	 */
	public function registerClientScript()
	{
		$id=$this->getId();
		$cs=Yii::app()->getClientScript();
		$cs->registerCoreScript('jquery');
		$cs->registerScriptFile($this->baseScriptUrl.'/'. (YII_DEBUG ? 'jquery.easytabs.js' : 'jquery.easytabs.min.js'));				
#		$cs->registerScriptFile($this->baseScriptUrl.'/'.(YII_DEBUG ? 'jquery.ba-hashchange.js' : 'jquery.ba-hashchange.min.js'));				
		$options=empty($this->options) ? '' : CJavaScript::encode($this->options);
		$cs->registerScript(__CLASS__.'#'.$id,"$($id).easytabs($options);");
	}
	
    protected function renderTabs()
    {
		$id=$this->getId();
    	echo CHtml::openTag('ul',array('class'=>'easy-tabs-etabs'));
    	$tabCount = 0;
       	foreach($this->tabs as $key=>$value)
 		{
 			$tabId = isset($value['id'])?$value['id']:$id.'_tab_'.$tabCount++;
 			
 		   	echo CHtml::openTag('li',array('class'=>'easy-tabs-tab'));
 		   	if(!array_key_exists('url',$value))
 		   		echo CHtml::openTag('a',array('href'=>'#'.$tabId));
 		   	else
 		   		echo CHtml::openTag('a',array('href'=>$value['url'],'data-target'=>'#'.$tabId));
  		   	if (!array_key_exists('title',$value))
 		   		throw new CException(Yii::t('ext.EasyTabs','Tab title is not defined'));
 		   	else
 		   		echo $value['title'];
 		   	echo CHtml::closeTag('a');
 			echo CHtml::closeTag('li');
 		}
 		echo CHtml::closeTag('ul');
    }
    
    protected function renderTabContent()
    {
    	$id=$this->getId();
		echo CHtml::openTag('div',array('class'=>'easy-tabs-panel-container'));
    	$tabCount = 0;
    	foreach($this->tabs as $key=>$value)
    	{
    		$tabId = isset($value['id'])?$value['id']:$id.'_tab_'.$tabCount++;
    		echo CHtml::openTag('div',array('id'=>$tabId));
    		if(array_key_exists('contentTitle',$value)) {
    			if ($value['contentTitle']=='') {
    				
    			} else {
    				echo CHtml::openTag('h2');
    				echo $value['contentTitle'];
    				echo CHtml::closeTag('h2');
    			}
    		} else {
    			echo CHtml::openTag('h2');
    			echo $value['title'];
    			echo CHtml::closeTag('h2');
    		}
    		if(array_key_exists('content',$value))
    			echo $value['content'];
    		echo CHtml::closeTag('div');
    	}
		echo CHtml::closeTag('div');
    }
}