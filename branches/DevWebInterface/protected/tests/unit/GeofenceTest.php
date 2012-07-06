<?php

require_once("../bootstrap.php");

class GeofenceTest extends CDbTestCase
{
	
 	public $fixtures=array( 			
 			'users'=>'Users',
 			);
	
	
	public function testTableName()
	{
	    $this->assertEquals(Geofence::model()->tableName(),'traceper_geofence');  	    
	}
	
	public function testSaveGeofence() {
		$name="test";
		$point1Latitude = 11.111111;
		$point1Longitude = 12.111111;
		$point2Latitude = 13.111111;
		$point2Longitude = 14.111111;
		$point3Latitude = 15.111111;
		$point3Longitude = 16.111111;
		$description = "test";
		$userId = 5;
		
		
		$this->assertTrue(Geofence::model()->saveGeofence($name,$point1Latitude, $point1Longitude, $point2Latitude, $point2Longitude, $point3Latitude, $point3Longitude,$description,$userId));
	
		$rows = Geofence::model()->findAll("name=:name", array(":name"=>$name));
	
		$this->assertEquals($rows[0]->name, $name);
		$this->assertEquals($rows[0]->point1Latitude, $point1Latitude);
		$this->assertEquals($rows[0]->point1Longitude, $point1Longitude);
		$this->assertEquals($rows[0]->point2Latitude, $point2Latitude);
		$this->assertEquals($rows[0]->point2Longitude, $point2Longitude);
		$this->assertEquals($rows[0]->point3Latitude, $point3Latitude);
		$this->assertEquals($rows[0]->point3Longitude, $point3Longitude);
		$this->assertEquals($rows[0]->description, $description);
	
		
		try {
			//try to register same name, it should throw exception
			Geofence::model()->saveGeofence($name,"12.111111", $point1Longitude, $point2Latitude, $point2Longitude, $point3Latitude, $point3Longitude,$description,$userId);
			$this->assertTrue(false);
		}
		catch (CDbException $exp){
			$this->assertTrue(true);
		}		
	}
	
			
}