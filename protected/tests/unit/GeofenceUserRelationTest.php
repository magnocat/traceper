<?php

require_once("../bootstrap.php");

class GeofenceUserRelationTest extends CDbTestCase
{
	
 	public $fixtures=array( 			
 			'geofenceUserRelation'=>'GeofenceUserRelation',
 			);
	
	
	public function testTableName()
	{
	    $this->assertEquals(GeofenceUserRelation::model()->tableName(),'traceper_geofence_user_relation');  	    
	}
	
	public function testSaveUserGeofenceRelation() {
		$geofenceId = 5;
		$userId = 23;
	
		GeofenceUserRelation::model()->deleteAll();
		$this->assertTrue(GeofenceUserRelation::model()->saveUserGeofenceRelation($geofenceId, $userId));
	
	
		$rows = GeofenceUserRelation::model()->findAll("geofenceId=:geofenceId", array(":geofenceId"=>$geofenceId));
	
		$this->assertEquals($rows[0]->geofenceId, $geofenceId);
		$this->assertEquals($rows[0]->userId, $userId);
	
		try {
			//try to register same parameters, it should throw exception
			$this->assertTrue(GeofenceUserRelation::model()->saveUserGeofenceRelation($geofenceId, $userId));
			$this->assertTrue(false);
		}
		catch (CDbException $exp){
			$this->assertTrue(true);
		}
	}
	
	public function testDeleteGeofenceMember()
	{
		//save geofenceUserRelation for testing...
		$this->assertTrue($this->geofenceUserRelation("geofence_user_relation1")->save());
		$this->assertEquals(1,GeofenceUserRelation::model()->deleteGeofenceMember($this->geofenceUserRelation("geofence_user_relation1")->userId,$this->geofenceUserRelation("geofence_user_relation1")->geofenceId));
	
		//There is no group member whose id is 5555
		$this->assertEquals(-1,GeofenceUserRelation::model()->deleteGeofenceMember(5555,$this->geofenceUserRelation("geofence_user_relation1")->geofenceId));
	}
	
			
}