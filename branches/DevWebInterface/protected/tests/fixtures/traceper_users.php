<?php
return array(
		'user1'=>array(
				'Id'=>1,
				'password'=>md5(12345),
				'realname'=>'Test User',
				'email'=>'test@traceper.com',
				'userType' => UserType::RealUser,
				'publicPosition'=>1,
				'authorityLevel'=>AuthorityLevel::UnauthorizedUser,
				'fb_id'=>123456789,
		),
		'user2'=>array(
				'Id'=>2,
				'password'=>md5(54321),
				'realname'=>'Test User 2',
				'email'=>'test2@traceper.com',
				'userType' => UserType::GPSDevice,
				'publicPosition'=>0,
				'authorityLevel'=>AuthorityLevel::StandardUser,
				'fb_id'=>123456789,
		),
		'user_privacy_group_test'=>array(
				'Id'=>3,
				'password'=>md5(54321),
				'realname'=>'User privacy group test 3',
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
		'user6'=>array(
				'Id'=>6,
				'password'=>md5(12345),
				'realname'=>'Test User 1',
				'email'=>'test6@traceper.com',
				'userType' => UserType::RealUser
		),
		'user7'=>array(
				'Id'=>7,
				'password'=>md5(54321),
				'realname'=>'Test User 7',
				'email'=>'test27666@traceper.com',
				'userType' => UserType::GPSDevice
		),
		'user8'=>array(
				'Id'=>8,
				'password'=>md5(54321),
				'realname'=>'User privacy group test 8',
				'email'=>'user_privacy_group_test8@traceper.com',
				'userType' => UserType::RealStaff
		),
		'user9'=>array(
				'Id'=>9,
				'password'=>md5(12321),
				'realname'=>'Test User 9',
				'email'=>'test49@traceper.com',
				'userType' => UserType::GPSStaff
		),
		'user10'=>array(
				'Id'=>10,
				'password'=>md5(1234321),
				'realname'=>'Test User 10',
				'email'=>'test50@traceper.com',
				'userType' => UserType::RealUser
		),
		'user11'=>array(
				'Id'=>11,
				'password'=>md5(12345),
				'realname'=>'Test User 11',
				'email'=>'test1@traceper.com',
				'userType' => UserType::RealUser
		),
		'user12'=>array(
				'Id'=>12,
				'password'=>md5(54321),
				'realname'=>'Test User 12',
				'email'=>'test12@traceper.com',
				'userType' => UserType::GPSDevice
		),
		'user13'=>array(
				'Id'=>13,
				'password'=>md5(54321),
				'realname'=>'User privacy group test 13',
				'email'=>'user_privacy_group_test13@traceper.com',
				'userType' => UserType::RealStaff
		),
		'user14'=>array(
				'Id'=>14,
				'password'=>md5(12321),
				'realname'=>'Test User 14',
				'email'=>'test14@traceper.com',
				'userType' => UserType::GPSStaff
		),
		'user15'=>array(
				'Id'=>15,
				'password'=>md5(1234321),
				'realname'=>'Test User 15',
				'email'=>'test15@traceper.com',
				'userType' => UserType::RealUser
		),
		'user16'=>array(
				'Id'=>16,
				'password'=>md5(12345),
				'realname'=>'Test User 16',
				'email'=>'test16@traceper.com',
				'userType' => UserType::RealUser
		),
		'user17'=>array(
				'Id'=>17,
				'password'=>md5(54321),
				'realname'=>'Test User 17',
				'email'=>'test17@traceper.com',
				'userType' => UserType::GPSDevice
		),
		'user18'=>array(
				'Id'=>18,
				'password'=>md5(54321),
				'realname'=>'User privacy group test 18',
				'email'=>'user_privacy_group_test18@traceper.com',
				'userType' => UserType::RealStaff
		),
		'user19'=>array(
				'Id'=>19,
				'password'=>md5(12321),
				'realname'=>'Test User 19',
				'email'=>'test194@traceper.com',
				'userType' => UserType::GPSStaff
		),
);