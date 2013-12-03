<?php		
if ($dataProvider != null) {
	$isSearchResult = isset($searchResult) ? true : false;
	
	$emptyText = Yii::t('upload', 'There is not any upload shared by you, your friends or publicly at the moment. You could take photos by your mobile app and share them with your friends or as public');	
	$uploadSearchEmptyText = Yii::t('upload', 'No uploads found by your search criteria');
	
	if (isset($uploadList) && $uploadList == true) {
		echo "<div id='uploadId' style='display:none;'></div>";

		$deleteUploadJSFunction = "function deleteUpload() { "
		.CHtml::ajax(
			array(
					'url'=>Yii::app()->createUrl('upload/delete'),
					'data'=> array('id'=>"js:$('#uploadId').html()"),
					'success'=> 'function(result) { 	
								 	try {
								 		TRACKER.closeConfirmationDialog();
										var obj = jQuery.parseJSON(result);
										if (obj.result && obj.result == "1") 
										{
											$.fn.yiiGridView.update("uploadListView");
											//$.fn.yiiGridView.update("uploadListView",{ complete: function(){ alert("upladListView updated"); } });
										}
										else 
										{
											TRACKER.showMessageDialog("Sorry,an error occured in operation");
										}

									}
									catch(ex) {
										TRACKER.showMessageDialog("Sorry,an error occured in operation");
									}
								}',
				)).
				"}";	

		Yii::app()->clientScript->registerScript('uploadFunctions',
				$deleteUploadJSFunction,
				CClientScript::POS_READY);

		Yii::app()->clientScript->registerScript('recursiveCheckFunction',
					'function checkImageListCallRequired(isPublicList, selectedPage) { 
						if((TRACKER.allImagesFetched === false) && (selectedPage > (TRACKER.bgImageListPageNo - 1)))
						{
							TRACKER.getImageList(isPublicList, false, function() {checkImageListCallRequired(isPublicList, selectedPage);});
						}
						else
						{
							//Recursive call ended
						}
					}',
					CClientScript::POS_READY);		
	}
	
	if (isset($isPublicList) && $isPublicList == true) {
		//OK
	}
	else
	{
		$isPublicList = false;
	}
	
	?>
	<div id="uploadsGridView" style="overflow:auto;">
	<?php	
	$this->widget('zii.widgets.grid.CGridView', array(
		    'dataProvider'=>$dataProvider,
	 		'id'=>$isPublicList?'publicUploadListView':'uploadListView',
			'ajaxUrl'=>null,
			'ajaxUpdate'=>true,
			//'beforeAjaxUpdate'=>'function(id,options){alert(unescape(options.url)) }',
			'afterAjaxUpdate'=>'function(id, data){ 
									var elems = document.getElementById("'.(($isPublicList === true)?'PublicUploadsPager':'UploadsPager').'").getElementsByTagName("li");
									var selectedPage = 0;
			
								    for (i in elems) {
										if(elems[i].className == "page selected")
										{
											//alert(elems[i].getElementsByTagName("a")[0].innerHTML);
											selectedPage = elems[i].getElementsByTagName("a")[0].innerHTML;
											break;
										}
								    }
			
									//alert("bgImageListPageNo:" + (TRACKER.bgImageListPageNo - 1) + " - selectedPage:" + selectedPage);

									//Kullanicinin sectigi sayfadaki uploadlar icin henüz sorgulama yapilmadiysa gerekli sorgulamalari hemen recursive olarak yap
									checkImageListCallRequired('.(($isPublicList === true)?'true':'false').', selectedPage);			
								}',
			'summaryText'=>'',
			'emptyText'=>$isSearchResult?$uploadSearchEmptyText:$emptyText,
			'htmlOptions'=>array('style'=>'font-size:14px;'),
			'pager'=>array( 
				 //'id'=>'UploadsPager-'.uniqid(), //Unique ID oluşturmayınca her ajaxta bir önceki sorgular da tekrarlanıyor
				 'id'=>($isPublicList === true)?'PublicUploadsPager':'UploadsPager',  //'UploadsPager'.$isPublicList?'-public':'',
				 'header'=>'',
		         'firstPageLabel'=>'',
		         'lastPageLabel'=>'',
			       ),
		    'columns'=>array(
				array(            // display 'create_time' using an expression		          
					'type' => 'raw',
		            'value'=>'CHtml::link("<img src=\"".Yii::app()->createUrl("upload/get", array(
														 "id"=>$data["id"],
														 "fileType"=>$data["fileType"],
														 "thumb"=>"ok"
														)
										  			)."\"  />", "#",
										array("onclick"=>"TRACKER.showMediaWindow(".$data["id"].", '.(($isPublicList === true)?'true':'false').');")
					  				  )',	
					'htmlOptions'=>array('width'=>'40px', 'style'=>'text-align:center;'),
				),
				array(            // display 'create_time' using an expression
		            'name'=>Yii::t('upload', 'Description'),
					'type' => 'raw',
		            'value'=>'CHtml::link($data["description"], "#", array(
    										"onclick"=>"TRACKER.showMediaWindow(".$data["id"].", '.(($isPublicList === true)?'true':'false').');",
										))',	
				),
				array(            // display 'create_time' using an expression
		            'name'=>Yii::t('upload', 'Sender'),
					'type' => 'raw',
		            'value'=>$isPublicList?'$data["realname"]':'CHtml::link($data["realname"], "#", array(
    										"onclick"=>"TRACKER.trackUser(".$data["userId"].");",
										))',
					'htmlOptions'=>array('width'=>'120px', 'style'=>'text-align: center;'),
					//'htmlOptions'=>array('style'=>'width:160px;'),	
				),
				array(           
		        //    'name'=>'realname',
					'type' =>'raw',
		            'value'=>' ($data["userId"] == Yii::app()->user->id) 
		            			?
		            					CHtml::link("<img src=\"images/delete.png\"  />", "#", array(
		            						"onclick"=>"$(\"#uploadId\").html(". $data["id"] .");
		            									TRACKER.showConfirmationDialog(\"'.Yii::t('upload', 'Do you really want to delete this file?').'\", deleteUpload);", 				
											"class"=>"vtip", "title"=>'."Yii::t('upload', 'Delete file')".'
		            						))
		            			: ""; ',
					'htmlOptions'=>array('width'=>'16px'),
					'visible'=>$isPublicList?false:true
				),
			),
	));
	
// 	Yii::app()->clientScript->registerScript('pageClicked',
// 			'
// function getEventTarget(e) {
//     e = e || window.event;
//     return e.target || e.srcElement; 
// }

// var ul = document.getElementById("UploadsPager");
// ul.onclick = function(event) {
//     var target = getEventTarget(event);
//     alert(target.innerHTML);
// 			};
						
			
// 			',
// 			CClientScript::POS_READY);	
	?>
	</div>
	<?php	
}
?>