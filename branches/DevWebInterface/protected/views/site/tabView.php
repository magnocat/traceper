<?php

$this->widget('ext.EasyTabs.EasyTabs', array(
		'id'=>"tab_view",
		'tabs'=>array(
				array(
						'id'=>'users_tab',
						'title' => Yii::t('layout', 'Users'),
						'contentTitle' => '',
						'url' => $this->createUrl('users/getFriendList', array('userType'=>array(UserType::RealUser/*, UserType::GPSDevice*/))),
				),
				array(
						'id'=>'photos_tab',
						'title' => Yii::t('layout', 'Photos'),
						'contentTitle' => '',
						'url' => $this->createUrl('upload/getList', array('fileType'=>0)),
				),
				array(
						'id'=>'groups_tab',
						'title' => Yii::t('layout', 'Groups'),
						'contentTitle' => '',
						'url' => $this->createUrl('groups/getGroupList', array('groupType'=>GroupType::FriendGroup)),
				),
		),
		'options' => array(
				'updateHash' => false,
				'cache' => true,
		),
));

?>