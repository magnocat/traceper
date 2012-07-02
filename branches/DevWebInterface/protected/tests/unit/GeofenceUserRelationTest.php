<?php

require_once("bootstrap.php");

class GeofenceUserRelationTest extends CDbTestCase
{
	
 	public $fixtures=array( 			
 			'users'=>'Users',
 			);
	
	
	public function testTableName()
	{
	    $this->assertEquals(GeofenceUserRelation::model()->tableName(),'traceper_geofence_user_relation');  	    
	}
	
			
}