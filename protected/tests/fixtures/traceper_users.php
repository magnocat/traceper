<?php
return array(
		'user1'=>array(
				'Id'=>1,
				'password'=>md5(12345),
				'realname'=>'Test User',
				'email'=>'test@traceper.com',
				'userType' => UserType::RealUser
		),
		'user2'=>array(
				'Id'=>2,
				'password'=>md5(54321),
				'realname'=>'Test User 2',
				'email'=>'test2@traceper.com',
				'userType' => UserType::GPSDevice
		),
		'user_privacy_group_test'=>array(
				'Id'=>3,
				'password'=>md5(54321),
				'realname'=>'User privacy group test',
				'email'=>'user_privacy_group_test@traceper.com',
				'userType' => UserType::RealStaff
		),
		'user4'=>array(
				'Id'=>4,
				'password'=>md5(12321),
				'realname'=>'Test User 4',
				'email'=>'test4@traceper.com',
				'userType' => UserType::GPSStaff
		),	
		'user5'=>array(
				'Id'=>5,
				'password'=>md5(1234321),
				'realname'=>'Test User 5',
				'email'=>'test5@traceper.com',
				'userType' => UserType::RealUser
		),			
);