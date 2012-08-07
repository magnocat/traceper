<?php

require_once("../bootstrap.php");

class UploadTest extends CDbTestCase
{
	
 	public $fixtures=array( 			
 			'upload'=>'Upload',
 			);
	
	
	public function testTableName()
	{
	    $this->assertEquals(Upload::model()->tableName(),'traceper_upload');  	    
	}
}