
<?php

if($profilePhotoStatus <= 1)
{
	if($variablesDefined == false)
	{
		//Bu degiskenin diger upload profile photo error tooltip javascript kodlarindan once tanimlanmasi gerekiyor
		Yii::app()->clientScript->registerScript('uploadProfilePhotoVariableDeclarations',
				"var bUploadProfilePhotoErrorExists = false;".
				(($profilePhotoSource == null)?"var eProfilePhotoExists = 'NONE';":"var eProfilePhotoExists = 'ONE';"),
				CClientScript::POS_HEAD);
	}	
	
	Yii::app()->clientScript->registerScript('uploadProfilePhotoTooltipsForNoOrSinglePhoto',
		"
		$(\"#uploadProfilePhoto\").tooltipster({
			theme: \".tooltipster-info\",
			content: \"".$profilePhotoStatusTooltipMessage."\",
			position: \"right\",
			trigger: \"hover\",
			maxWidth: 300,
			offsetX: 15,
			offsetY: -9,
			onlyOne: false,
		});
			
		$(\"#uploadProfilePhoto\").click(function (){
			$(\"#uploadProfilePhoto\").tooltipster('hide');
		});
		",
		CClientScript::POS_READY);
}
else
{
	if($variablesDefined == false)
	{
		Yii::app()->clientScript->registerScript('bothPhotoExistActions',
				"var eProfilePhotoExists = 'BOTH';
				var bUploadProfilePhotoErrorExists = false;
				var tooltipMenu = null;
		
				function closeTooltipMenu()
				{
					var elem = document.getElementById('profilePhotoSettingsMenu');
					var liElem = elem.querySelector( 'li' );
			
					tooltipMenu._closeMenu(liElem);
				}
				",
				CClientScript::POS_HEAD);
	}	
}

Yii::app()->clientScript->registerScript('uploadProfilePhotoFailureAndSuccessTooltips',
	"
	$(\"#uploadProfilePhotoErrorTooltip\").tooltipster({
		theme: \".tooltipster-error\",
		content: \" \",
		position: \"right\",
		trigger: \"custom\",
		maxWidth: 300,
		offsetX: 45,
		offsetY: 1,
		onlyOne: false,
	});

	$(\"html\").click(function() {
		if(bUploadProfilePhotoErrorExists)
		{
			$(\"#uploadProfilePhotoErrorTooltip\").tooltipster('hide');

			if(eProfilePhotoExists == 'NONE')
			{
				$('#profilePhotoUploadButton').removeClass('qq-upload-button-error-with-icon');
				$('#profileUserIcon').removeClass('profileUserIcon-error');
				$('#uploadProfilePhoto').removeClass('uploadProfilePhotoErrorForIcon');
				$('#uploadProfilePhotoErrorTooltip').css('bottom', '4px');
			}
			else if(eProfilePhotoExists == 'ONE')
			{
				$('#profilePhotoUploadButton').removeClass('qq-upload-button-error');
				$('#profilePhoto').removeClass('profilePhoto-error');
			}
			else if(eProfilePhotoExists == 'BOTH')
			{
				$('#profilePhoto').css('opacity', 1);
				$('#profilePhotoSettingsMenu').removeClass('profilePhotoSettingsMenu-error');
				$('#profilePhotoSettingsMenu').css('left', '4px');
				$('#profilePhotoSettingsMenu').css('bottom', '0px');
				$('#profilePhotoSettingsMenu').css('background', '');			
			}			
			else
			{
				alert('Undefined eProfilePhotoExists:' + eProfilePhotoExists);
			}

			bUploadProfilePhotoErrorExists = false;
		}
	});

	$(\"#uploadProfilePhoto\").hover(function (){
		if(bUploadProfilePhotoErrorExists)
		{
			$(\"#uploadProfilePhotoErrorTooltip\").tooltipster('hide');

			if(eProfilePhotoExists == 'NONE')
			{
				$('#profilePhotoUploadButton').removeClass('qq-upload-button-error-with-icon');
				$('#profileUserIcon').removeClass('profileUserIcon-error');
				$('#uploadProfilePhoto').removeClass('uploadProfilePhotoErrorForIcon');
				$('#uploadProfilePhotoErrorTooltip').css('bottom', '4px');
			}
			else if(eProfilePhotoExists == 'ONE')
			{
				$('#profilePhotoUploadButton').removeClass('qq-upload-button-error');
				$('#profilePhoto').removeClass('profilePhoto-error');
			}
			else if(eProfilePhotoExists == 'BOTH')
			{
				$('#profilePhoto').css('opacity', 1);
				$('#profilePhotoSettingsMenu').removeClass('profilePhotoSettingsMenu-error');
				$('#profilePhotoSettingsMenu').css('left', '4px');
				$('#profilePhotoSettingsMenu').css('bottom', '0px');
				$('#profilePhotoSettingsMenu').css('background', '');			
			}			
			else
			{
				alert('Undefined eProfilePhotoExists:' + eProfilePhotoExists);
			}

			bUploadProfilePhotoErrorExists = false;
		}
	});

	$(\"#uploadProfilePhotoSuccessfulTooltip\").tooltipster({
		theme: \".tooltipster-success\",
		content: \" \",
		position: \"right\",
		trigger: \"custom\",
		maxWidth: 300,
		offsetX: 45,
		offsetY: 1,
		onlyOne: false,
		timer: 2000,
		animation: \"grow\",
		speed: 500
	});
	",
	CClientScript::POS_READY);

if($bothPhotoExists == 'useFacebook')
{
	$useAjaxLink = CHtml::ajaxLink(Yii::t('layout', 'Use my Traceper profile photo'), $this->createUrl('users/useTraceperProfilePhoto'),
			array(
	 				//'complete'=> 'function() {}',
	 				//'update'=> '#userarea',
	
					'success'=> 'function(msg) {
									$("#userarea").html(msg);
					
									$("#uploadProfilePhotoSuccessfulTooltip").css("bottom", "22px");
									$("#uploadProfilePhotoSuccessfulTooltip").tooltipster("update", "'.Yii::t('site', 'You have changed your profile photo successfully.').'");
									$("#uploadProfilePhotoSuccessfulTooltip").tooltipster("show");
					
									var timeStamp = new Date().getTime();													
									var imageSrc = "profilePhotos/'.Yii::app()->user->id.'.png?random=" + timeStamp;
		
									MAP_OPERATOR.updateMarkerImage(currentUserMarker, imageSrc, true);
									TRACKER.users['.Yii::app()->user->id.'].mapMarker[0].infoWindow.setContent(getContentFor('.Yii::app()->user->id.', imageSrc));						
								}'
			),
			array(
					'id'=>'useTraceperProfilePhotoAjaxLink-'.uniqid(),
			));	
}
else if($bothPhotoExists == 'useTraceper')
{
	$useAjaxLink = CHtml::ajaxLink(Yii::t('layout', 'Use my Facebook profile photo'), $this->createUrl('users/useFacebookProfilePhoto'),
			array(
					//'complete'=> 'function() {}',
					//'update'=> '#userarea',
	
					'success'=> 'function(msg) {
									$("#userarea").html(msg);
					
									$("#uploadProfilePhotoSuccessfulTooltip").css("bottom", "22px");
									$("#uploadProfilePhotoSuccessfulTooltip").tooltipster("update", "'.Yii::t('site', 'You have changed your profile photo successfully.').'");
									$("#uploadProfilePhotoSuccessfulTooltip").tooltipster("show");
					
									var imageSrc = "https://graph.facebook.com/'.Yii::app()->user->fb_id.'/picture?type=square";
					
									MAP_OPERATOR.updateMarkerImage(currentUserMarker, imageSrc, true);
									TRACKER.users['.Yii::app()->user->id.'].mapMarker[0].infoWindow.setContent(getContentFor('.Yii::app()->user->id.', imageSrc));					
								}'
			),
			array(
					'id'=>'useFacebookProfilePhotoAjaxLink-'.uniqid(),
			));	
}
else
{
	$useAjaxLink = null;
}
?>

<div style="position:absolute;display:inline-block;">
<?php

$this->widget('ext.EAjaxUpload.EAjaxUpload',
		array(
				'id'=>'uploadProfilePhoto',
				'config'=>array(
						'action'=>Yii::app()->createUrl('users/upload'),
						'allowedExtensions'=>array("jpg", "jpeg", "png"),//array("jpg","jpeg","gif","exe","mov" and etc...
						'sizeLimit'=>10*1024*1024,// maximum file size in bytes
						'photoSrc'=>$profilePhotoSource,
						'bothPhotoExists'=>$bothPhotoExists,
						'useAjaxLink'=>$useAjaxLink,
						'uploadMenuLabel'=>Yii::t('layout', 'Upload a new Traceper profile photo and use it'),
						//'minSizeLimit'=>10*1024*1024,// minimum file size in bytes
						'onSubmit'=>"js:function(file, extension) {
						//$('div.preview').addClass('loading');
						bUploadProfilePhotoErrorExists = false;
}",
						'onComplete'=>"js:function(id, fileName, responseJSON){
						$('#profilePhotoUploadButton').removeClass('qq-upload-button-hover');
							
						if(eProfilePhotoExists == 'NONE') //Ne Traceper ne de FB fotosu yok
						{
						if(typeof responseJSON['result'] != 'undefined')
						{
						if(responseJSON['result'] == '-1')
						{
						$('#uploadProfilePhotoErrorTooltip').tooltipster('update', '".Yii::t('site', 'The file you try to select is unreadable. Please, select a proper file.')."');
}
						else
						{
						$('#uploadProfilePhotoErrorTooltip').tooltipster('update', '".Yii::t('site', 'There occured an unknown error during upload process. Make sure that you select a proper image file. If the error persists, please contact us.')."');
}

						$('#profilePhotoUploadButton').addClass('qq-upload-button-error-with-icon');
						$('#profileUserIcon').addClass('profileUserIcon-error');
						$('#uploadProfilePhoto').addClass('uploadProfilePhotoErrorForIcon');
						$('#uploadProfilePhotoErrorTooltip').css('bottom', '28px');
						$('#uploadProfilePhotoErrorTooltip').tooltipster('show');
						bUploadProfilePhotoErrorExists = true;
}
						else if(bUploadProfilePhotoErrorExists == false) //showMessage'a bir hata gelmemisse
						{
						var timeStamp = new Date().getTime();
						var imageSrc = 'profilePhotos/'+responseJSON['filename']+'.png'+'?random=' + timeStamp;
							
						$('#profileUserIcon').hide();
						$('#profilePhoto').attr('src', imageSrc);

						$('#profilePhoto').show();
						$('#uploadProfilePhoto').tooltipster('update', '".Yii::t('site', 'Click here to change your profile photo')."');
							
						$('#uploadProfilePhotoSuccessfulTooltip').tooltipster('update', '".Yii::t('site', 'You have uploaded your profile photo successfully.')."');
						$('#uploadProfilePhotoSuccessfulTooltip').tooltipster('show');

						MAP_OPERATOR.updateMarkerImage(currentUserMarker, imageSrc, true);
						TRACKER.users[".Yii::app()->user->id."].mapMarker[0].infoWindow.setContent(getContentFor(".Yii::app()->user->id.", imageSrc));

						eProfilePhotoExists = 'ONE';
}
						else
						{
						//Show message'a hata gelmis
}
}
						else if(eProfilePhotoExists == 'ONE') //Ya sadece Traceper ya da sadece FB fotosu var
						{
						if(typeof responseJSON['result'] != 'undefined')
						{
						if(responseJSON['result'] == '-1')
						{
						$('#uploadProfilePhotoErrorTooltip').tooltipster('update', '".Yii::t('site', 'The file you try to select is unreadable. Please, select a proper file.')."');
}
						else
						{
						$('#uploadProfilePhotoErrorTooltip').tooltipster('update', '".Yii::t('site', 'There occured an unknown error during upload process. Make sure that you select a proper image file. If the error persists, please contact us.')."');
}

						$('#profilePhotoUploadButton').addClass('qq-upload-button-error');
						$('#profilePhoto').addClass('profilePhoto-error');
						$('#uploadProfilePhotoErrorTooltip').tooltipster('show');
						bUploadProfilePhotoErrorExists = true;
}
						else if(bUploadProfilePhotoErrorExists == false) //showMessage'a bir hata gelmemisse
						{
						//Bu case'de sadece FB fotosu varken Traceper fotosu yuklenebildigi icin ajax yeniden alinmali ki tooltip menu cikmaya baslasin sayfayi refresh etmeden
						jQuery.ajax({
						type: 'POST',
						url: '".Yii::app()->createUrl('users/viewProfilePhoto', array('variablesNotDefined'=>true))."',
						success: function(html){
						jQuery('#userarea').html(html);".
							
						((Yii::app()->user->fb_id == 0)?"$('#uploadProfilePhoto').tooltipster('hide'); eProfilePhotoExists = 'ONE';":"eProfilePhotoExists = 'BOTH';").
						"$('#uploadProfilePhotoSuccessfulTooltip').css('bottom', '22px');
						$('#uploadProfilePhotoSuccessfulTooltip').tooltipster('update', '".Yii::t('site', 'You have changed your profile photo successfully.')."');
						$('#uploadProfilePhotoSuccessfulTooltip').tooltipster('show');

						var timeStamp = new Date().getTime();
						var imageSrc = 'profilePhotos/'+responseJSON['filename']+'.png'+'?random=' + timeStamp;
							
						MAP_OPERATOR.updateMarkerImage(currentUserMarker, imageSrc, true);
						TRACKER.users[".Yii::app()->user->id."].mapMarker[0].infoWindow.setContent(getContentFor(".Yii::app()->user->id.", imageSrc));
}
});
}
						else
						{
						//Show message'a hata gelmis
}
}
						else if(eProfilePhotoExists == 'BOTH') //Hem Traceper hem de FB fotosu var
						{
						if(typeof responseJSON['result'] != 'undefined')
						{
						closeTooltipMenu();

						$('#profilePhotoSettingsMenu').addClass('profilePhotoSettingsMenu-error');
						$('#profilePhotoSettingsMenu').css('left', '1px');
						$('#profilePhotoSettingsMenu').css('bottom', '3px');
						$('#profilePhotoSettingsMenu').css('background', '#C00');
						$('#uploadProfilePhotoErrorTooltip').css('bottom', '28px');

						if(responseJSON['result'] == '-1')
						{
						$('#uploadProfilePhotoErrorTooltip').tooltipster('update', '".Yii::t('site', 'The file you try to select is unreadable. Please, select a proper file.')."');
}
						else
						{
						$('#uploadProfilePhotoErrorTooltip').tooltipster('update', '".Yii::t('site', 'There occured an unknown error during upload process. Make sure that you select a proper image file. If the error persists, please contact us.')."');
}
							
						$('#uploadProfilePhotoErrorTooltip').tooltipster('show');
						bUploadProfilePhotoErrorExists = true;
}
						else if(bUploadProfilePhotoErrorExists == false) //showMessage'a bir hata gelmemisse
						{
						//Sorun olustugunda toolip menu acik kaldigindan ve kapatilamadigindan ayni photo yeniden ajaxla yuklenerek bu sorun giderilmeye calisiliyor
						jQuery.ajax({
						type: 'POST',
						url: '".Yii::app()->createUrl('users/viewProfilePhoto')."',
						success: function(html){
						jQuery('#userarea').html(html);".
							
						((Yii::app()->user->fb_id == 0)?"$('#uploadProfilePhoto').tooltipster('hide');":"").
						"$('#uploadProfilePhotoSuccessfulTooltip').css('bottom', '22px');
						$('#uploadProfilePhotoSuccessfulTooltip').tooltipster('update', '".Yii::t('site', 'You have changed your profile photo successfully.')."');
						$('#uploadProfilePhotoSuccessfulTooltip').tooltipster('show');

						var timeStamp = new Date().getTime();
						var imageSrc = 'profilePhotos/'+responseJSON['filename']+'.png'+'?random=' + timeStamp;
							
						MAP_OPERATOR.updateMarkerImage(currentUserMarker, imageSrc, true);
						TRACKER.users[".Yii::app()->user->id."].mapMarker[0].infoWindow.setContent(getContentFor(".Yii::app()->user->id.", imageSrc));
}
});
}
						else
						{
						//Show message'a hata gelmis
}
}
						else
						{
						alert('Undefined eProfilePhotoExists:' + eProfilePhotoExists);
}
}",
						'messages'=>array(
								'typeError'=>Yii::t('site', 'The file you try to select is invalid. Please, select a file of types {extensions}.'),
								'sizeError'=>Yii::t('site', 'The file you try to select is too large. Please, select a file smaller than 10 MB.'),
								//'minSizeError'=>"{file} is too small, minimum file size is {minSizeLimit}.",
								'emptyError'=>Yii::t('site', 'The file you try to select is empty. Please, select a proper file.'),
								//'onLeave'=>"The files are being uploaded, if you leave now the upload will be cancelled."
						),
						'showMessage'=>"js:function(message){
						//alert(message);

						if(eProfilePhotoExists == 'NONE')
						{
						$('#profilePhotoUploadButton').removeClass('qq-upload-button-hover');
						$('#profilePhotoUploadButton').addClass('qq-upload-button-error-with-icon');
						$('#profileUserIcon').addClass('profileUserIcon-error');
						$('#uploadProfilePhoto').addClass('uploadProfilePhotoErrorForIcon');
						$('#uploadProfilePhotoErrorTooltip').css('bottom', '28px');

						$('#uploadProfilePhotoErrorTooltip').tooltipster('update', message);
						$('#uploadProfilePhotoErrorTooltip').tooltipster('show');
						bUploadProfilePhotoErrorExists = true;
}
						else if(eProfilePhotoExists == 'ONE')
						{
						$('#profilePhotoUploadButton').removeClass('qq-upload-button-hover');
						$('#profilePhotoUploadButton').addClass('qq-upload-button-error');
						$('#profilePhoto').addClass('profilePhoto-error');

						$('#uploadProfilePhotoErrorTooltip').tooltipster('update', message);
						$('#uploadProfilePhotoErrorTooltip').tooltipster('show');
						bUploadProfilePhotoErrorExists = true;
}
						else if(eProfilePhotoExists == 'BOTH')
						{
						closeTooltipMenu();
							
						$('#profilePhotoSettingsMenu').addClass('profilePhotoSettingsMenu-error');
						$('#profilePhotoSettingsMenu').css('left', '1px');
						$('#profilePhotoSettingsMenu').css('bottom', '3px');
						$('#profilePhotoSettingsMenu').css('background', '#C00');
						$('#uploadProfilePhotoErrorTooltip').css('bottom', '28px');

						$('#uploadProfilePhotoErrorTooltip').tooltipster('update', message);
						$('#uploadProfilePhotoErrorTooltip').tooltipster('show');
						bUploadProfilePhotoErrorExists = true;
}
						else
						{
						alert('Undefined eProfilePhotoExists: ' + eProfilePhotoExists);
}
}"
				),
		));

echo CHtml::label('', '#',
		array(
				'id'=>'uploadProfilePhotoErrorTooltip',
				'style'=>'pointer-events:none; position:absolute; left:3px; bottom:4px;'
		));

echo CHtml::label('', '#',
		array(
				'id'=>'uploadProfilePhotoSuccessfulTooltip',
				'style'=>'pointer-events:none; position: absolute; bottom:4px;'
		));

?>
</div>

<div style="position:absolute;display:inline-block;top:30px;">
<?php

echo CHtml::link(Yii::app()->user->name, "#", array('class'=>'vtip', 'onclick'=>'TRACKER.trackUser('.Yii::app()->user->id.')', 'title'=>Yii::t('layout', 'See your position on the map'),
		'style'=>'position:absolute; left:54px; bottom:-20px;'));

?>
</div>