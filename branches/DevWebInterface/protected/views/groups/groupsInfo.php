
<?php 
	if($groupType == GroupType::FriendGroup)
	{
		$viewId = 'friendGroupsListView';
	}
	else if($groupType == GroupType::StaffGroup)
	{
		$viewId = 'staffGroupsListView';
	}

	if (isset($dataProvider)) {
		$this->renderPartial('groupList', array('dataProvider'=>$dataProvider, 'groupType'=>$groupType, 'viewId'=>$viewId), false, true);
	}
	else
	{
		echo Yii::t('groups', 'No groups to show...').'<br/> <br/>';
	}
	
	echo '<div id="groupSearchResults"></div>';
?>