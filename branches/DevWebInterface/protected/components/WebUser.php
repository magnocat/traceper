<?php

class WebUser extends CWebUser {

    // some code here

    public function loginRequired() {
		
    	$app=Yii::app();
    	$request=$app->getRequest();
    	
    	//Webden ajax cagrilarina "Login Required" donuluyor ki bu cevaba gore sayfa yenileniyor
    	//Mobilde yapilan cagrilara "Login Required" donuluyor ki eger server tarafinda session kapanmisa yeninde oto login yapilsin
    	if($request->getIsAjaxRequest() || (isset($_REQUEST['client']) && ($_REQUEST['client'] == 'mobile')))
    	{
    		echo "Login Required";
    	}
    	else //Direk tarayici adresinden yetkisiz bir erisim istenirse login sayfasi yukleniyor
    	{
    		//$app->getController()->render('//site/index');
    		    		
    		$app->getController()->redirect(array('site/index'));
    	}
    }
}

?>