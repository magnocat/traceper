<?php


require_once('common.php');

class TestOfDeviceManager  extends UnitTestCase {
	
	private $dbc = null;
	private $dm = null;
	private $data = array();

	
	function setUp() 
	{
		$this->dbc = new MySQLOperator(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME);
		$this->dm = new DeviceManager($this->dbc, DEVICE_ACTION_PREFIX_FOR_TEST, STAFF_TRACKER_TABLE_PREFIX_FOR_TEST, ELEMENT_COUNT_IN_A_PAGE_FOR_TEST);
				
		$this->data['action'] = DEVICE_ACTION_PREFIX_FOR_TEST . 'RegisterMe';
				
		$this->data['username'] = 'mekya';
				
		$this->data['password'] = '123456';
		
		$this->data['email'] = 'mekya@mekya.com';
		
		$this->data['im'] = 'mekyaim@mekya.com';
		
		$this->data['realname'] = "real mekya";
		
		$this->data['latitude'] = 56.4542489;
		
		$this->data['longitude'] = 235.5654438;
		
		$this->data['altitude'] = 245.2322604;
		
		$this->data['deviceId'] = "12.23.34.55";
        
    }
    
    function tearDown() {
       
    }
	
	
	public function testRegisterMe()
	{
		
		$this->dbc->query("truncate " . STAFF_TRACKER_TABLE_PREFIX_FOR_TEST . "_users" );
		
		$reqArray = array();
		$out = $this->dm->process($reqArray);
		$this->assertEqual($out, -2, 'User registration missing parameter test 0 failed'); 	
		
		$reqArray['action'] = DEVICE_ACTION_PREFIX_FOR_TEST . 'RegisterMe';
		$out = $this->dm->process($reqArray);
		$this->assertEqual($out, -2, 'User registration missing parameter test 1 failed'); 	
				
		$reqArray['username'] = $this->data['username'];
		$out = $this->dm->process($reqArray);
		$this->assertEqual($out, -2, 'User registration missing parameter test 2 failed'); 	
				
		$reqArray['password'] = $this->data['password'];
		$out = $this->dm->process($reqArray);
		$this->assertEqual($out, -2, 'User registration missing parameter test 3 failed'); 	
		
		$reqArray['email'] = $this->data['email'];
		$out = $this->dm->process($reqArray);
		$this->assertEqual($out, -2, 'User registration missing parameter test 4 failed'); 	
		
		$reqArray['im'] = $this->data['im'];
		$out = $this->dm->process($reqArray);
		$this->assertEqual($out, -2, 'User registration missing parameter test 5 failed'); 	
		
		$reqArray['realname'] = $this->data['realname'];
				
		
		$out = $this->dm->process($reqArray);
		$this->assertEqual($out, 1, 'User registration test failed'); 	
		
		//it must not be registered
		$out = $this->dm->process($reqArray);
		$this->assertEqual($out, -5, 'User name already exists test failed'); 	
		
		
		$id = $this->dbc->lastInsertId();
		
		$result = $this->dbc->query("select * from "
									 . STAFF_TRACKER_TABLE_PREFIX_FOR_TEST ."_users
									where Id = " . $id);
									
	    $row = $this->dbc->fetchObject($result);
		
		$this->assertEqual($reqArray['username'], $row->username);		
		$this->assertEqual($reqArray['password'], $row->password);
		$this->assertEqual($reqArray['email'], $row->email);
		$this->assertEqual($reqArray['im'], $row->im);
		$this->assertEqual($reqArray['realname'], $row->realname);		
	
	}
	
	

	
	function testOfTakeMyLocation() {
		
		$this->testRegisterMe();
		
		$reqArray = array();
		$out = $this->dm->process($reqArray);
		$this->assertEqual($out, -2, 'User registration missing parameter test 0 failed'); 	
		
		$reqArray['action'] = DEVICE_ACTION_PREFIX_FOR_TEST . 'TakeMyLocation';
		$out = $this->dm->process($reqArray);
		$this->assertEqual($out, -2, 'User take my location missing parameter test 1 failed'); 	
				
		$reqArray['username'] = $this->data['username'];
		$out = $this->dm->process($reqArray);
		$this->assertEqual($out, -2, 'User take my location missing parameter test 2 failed'); 	
				
		$reqArray['password'] = $this->data['password'];
		$out = $this->dm->process($reqArray);
		$this->assertEqual($out, -2, 'User take my location missing parameter test 3 failed'); 	
		
		$reqArray['latitude'] = $this->data['latitude'];
		$out = $this->dm->process($reqArray);
		$this->assertEqual($out, -2, 'User take my location missing parameter test 4 failed'); 	
		
		$reqArray['longitude'] = $this->data['longitude'];
		$out = $this->dm->process($reqArray);
		$this->assertEqual($out, -2, 'User take my location missing parameter test 5 failed'); 	
		
		$reqArray['altitude'] = $this->data['altitude'];
		$out = $this->dm->process($reqArray);
		$this->assertEqual($out, -2, 'User take my location missing parameter test 6 failed'); 
		
		$reqArray['deviceId'] = $this->data['deviceId'];		
				
		$out = $this->dm->process($reqArray);
		
		$this->assertEqual($out, 1, 'User take my location test failed'); 	
				
		
		$result = $this->dbc->query("select * from "
									 . STAFF_TRACKER_TABLE_PREFIX_FOR_TEST ."_users
									where username = '" . $reqArray['username'] ."'
									limit 1" );

	    $row = $this->dbc->fetchObject($result);

		$this->assertEqual($reqArray['username'], $row->username);		
		$this->assertEqual($reqArray['password'], $row->password);
		$this->assertEqual(round($reqArray['latitude'],6), (float)$row->latitude);
		$this->assertEqual(round($reqArray['longitude'],6),(float)$row->longitude);
		$this->assertEqual(round($reqArray['altitude'],6), (float)$row->altitude);	
		$this->assertEqual($reqArray['deviceId'], $row->deviceId);		
				
	}
	
	
}



?>