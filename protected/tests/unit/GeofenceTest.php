<?php

require_once("../bootstrap.php");

class GeofenceTest extends CDbTestCase
{
	
 	public $fixtures=array( 			
 			'geofence'=>'Geofence',
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
		
		Geofence::model()->deleteAll();
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
	
	public function testGetGeofencesCount()
	{
		//save geofences for testing...
		$this->assertTrue($this->geofence("geofence1")->save());
		$this->assertTrue($this->geofence("geofence2")->save());
		$this->assertTrue($this->geofence("geofence3")->save());
	
		$this->assertEquals(3, Geofence::model()->getGeofencesCount($this->geofence("geofence1")->userId));
	}
	
	public function testGetGeofences()
	{
		//save geofences for testing...
		$this->assertTrue($this->geofence("geofence1")->save());
		$this->assertTrue($this->geofence("geofence2")->save());
		$this->assertTrue($this->geofence("geofence3")->save());
	
		$dataProvider = Geofence::model()->getGeofences($this->geofence("geofence1")->userId,3,5);
		$result=$dataProvider->getData();

		//For first geofence
		$this->assertEquals($result[0]["Name"], $this->geofence("geofence1")->name);
		$this->assertEquals($result[0]["Description"], $this->geofence("geofence1")->description);
		$this->assertEquals($result[0]["Point1Latitude"], $this->geofence("geofence1")->point1Latitude);
		$this->assertEquals($result[0]["Point1Longitude"], $this->geofence("geofence1")->point1Longitude);
		$this->assertEquals($result[0]["Point2Latitude"], $this->geofence("geofence1")->point2Latitude);
		$this->assertEquals($result[0]["Point2Longitude"], $this->geofence("geofence1")->point2Longitude);
		$this->assertEquals($result[0]["Point3Latitude"], $this->geofence("geofence1")->point3Latitude);
		$this->assertEquals($result[0]["Point3Longitude"], $this->geofence("geofence1")->point3Longitude);
		
		
		//For second geofence
		$this->assertEquals($result[1]["Name"], $this->geofence("geofence2")->name);
		$this->assertEquals($result[1]["Description"], $this->geofence("geofence2")->description);
		$this->assertEquals($result[1]["Point1Latitude"], $this->geofence("geofence2")->point1Latitude);
		$this->assertEquals($result[1]["Point1Longitude"], $this->geofence("geofence2")->point1Longitude);
		$this->assertEquals($result[1]["Point2Latitude"], $this->geofence("geofence2")->point2Latitude);
		$this->assertEquals($result[1]["Point2Longitude"], $this->geofence("geofence2")->point2Longitude);
		$this->assertEquals($result[1]["Point3Latitude"], $this->geofence("geofence2")->point3Latitude);
		$this->assertEquals($result[1]["Point3Longitude"], $this->geofence("geofence2")->point3Longitude);
		
		//For third geofence
		$this->assertEquals($result[2]["Name"], $this->geofence("geofence3")->name);
		$this->assertEquals($result[2]["Description"], $this->geofence("geofence3")->description);
		$this->assertEquals($result[2]["Point1Latitude"], $this->geofence("geofence3")->point1Latitude);
		$this->assertEquals($result[2]["Point1Longitude"], $this->geofence("geofence3")->point1Longitude);
		$this->assertEquals($result[2]["Point2Latitude"], $this->geofence("geofence3")->point2Latitude);
		$this->assertEquals($result[2]["Point2Longitude"], $this->geofence("geofence3")->point2Longitude);
		$this->assertEquals($result[2]["Point3Latitude"], $this->geofence("geofence3")->point3Latitude);
		$this->assertEquals($result[2]["Point3Longitude"], $this->geofence("geofence3")->point3Longitude);
	}
	
			
}