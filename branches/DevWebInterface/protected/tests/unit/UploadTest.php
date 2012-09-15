<?php

require_once("../bootstrap.php");

class UploadTest extends CDbTestCase
{
	
 	public $fixtures=array( 			
 			'upload'=>'Upload',
 			'users'=>'Users',
 			);
	
	
	public function testTableName()
	{
	    $this->assertEquals(Upload::model()->tableName(),'traceper_upload');  	    
	}
	
	public function testGetId() {
		
		/*
		$fileType = 0;
		$userId = 5;
		$latitude = 11.111111;
		$longitude = 11.111111;
		$altitude = 0;
		$uploadTime = date('Y-m-d');
		$publicData = 1;
		$description = 'test1';
		$isLive = 0;
		*/
		$liveKey = 'test1';
		
		/*
		echo $this->upload("upload1")->userId;
		$this->assertTrue($this->upload("upload1")->save());
		*/
		
		$uploads = Upload::model()->getId($liveKey);
		$this->assertEquals($uploads, 1);
		
		
		$uploads2 = Upload::model()->getId('test9999');
		$this->assertEquals($uploads2, null);
		
		/*
		$this->assertEquals($rows[0]->fileType, $fileType);
		$this->assertEquals($rows[0]->userId, $userId);
		$this->assertEquals($rows[0]->latitude, $latitude);
		$this->assertEquals($rows[0]->longitude, $longitude);
		$this->assertEquals($rows[0]->altitude, $altitude);
		//$this->assertEquals($rows[0]->uploadTime, $uploadTime);
		$this->assertEquals($rows[0]->publicData, $publicData);
		$this->assertEquals($rows[0]->description, $description);
		$this->assertEquals($rows[0]->isLive, $isLive);
		$this->assertEquals($rows[0]->liveKey, $liveKey);
		*/
	}
	
	
	public function testAddNewRecord() {
		$fileType = 0;
		$userID = $this->users("user_upload_test1")->Id;
		$latitude = 22.111111;
		$longitude = 22.111111;
		$altitude = 0;
		$uploadTime = date('Y-m-d');
		$publicData = 1;
		$description = 'test2';
		$isLive = 0;
		$liveKey = 'test2';
		
		$effectedRows = Upload::model()->addNewRecord($fileType,$userID,$latitude, $longitude, $altitude, $publicData, $description, $isLive, $liveKey);
		$this->assertEquals($effectedRows, 1);
	}
	
	public function testGetRecordList() {
		$fileType = $this->upload("upload2")->fileType;
		$userID = $this->upload("upload2")->userId;
		$friendList = $this->upload("upload3")->userId;
		$dataProvider = Upload::model()->getRecordList($fileType,$userID,$friendList);
		
		$rows = $dataProvider->getData();
		$this->assertEquals($rows[2]['description'], $this->upload("upload2")->description);
		$this->assertEquals($rows[1]['description'], $this->upload("upload3")->description);
		$this->assertEquals($rows[0]['description'], $this->upload("upload4")->description);
	}
	
	public function testGetSearchResult() {
		$fileType = $this->upload("upload5")->fileType;
		$userID = $this->upload("upload5")->userId;
		$friendList = $this->upload("upload5")->userId;
		$keyword = $this->upload("upload5")->description;
		
		$dataProvider = Upload::model()->getSearchResult($fileType,$userID,$friendList,$keyword,$keyword);
	
		$rows = $dataProvider->getData();
		$this->assertEquals($rows[0]['description'], $this->upload("upload5")->description);
		
		$fileType2 = $this->upload("upload6")->fileType;
		$userID2 = $this->upload("upload6")->userId;
		$friendList2 = $this->upload("upload6")->userId;
		$keyword2 = $this->users("user_upload_test2")->realname;
		
		$dataProvider2 = Upload::model()->getSearchResult($fileType2,$userID2,$friendList2,$keyword2,$keyword2);
		$rows2 = $dataProvider2->getData();
		$this->assertEquals($rows2[0]['description'], $this->upload("upload6")->description);
	}
	
	public function testGetUploadCount() {
		$fileType = $this->upload("upload2")->fileType;
		$userID = $this->upload("upload2")->userId;
		$friendList = $this->upload("upload3")->userId;

		$dataFetchedTimeKey = "Upload.dataFetchedTime";
		$time = Yii::app()->session[$dataFetchedTimeKey];
	
		/*TODO: Check these tests, because 1 may be wrong
		 * 
		 */
		$uploadCount = Upload::model()->getUploadCount($fileType,$userID,$friendList,$time);
		$this->assertEquals(1, $uploadCount);
		
		$time = null;
		$uploadCount = Upload::model()->getUploadCount($fileType,$userID,$friendList,$time);
		$this->assertEquals(1, $uploadCount);
		
	}
	
	
	public function testGetUploadList() {
		$fileType = $this->upload("upload2")->fileType;
		$userID = $this->upload("upload2")->userId;
		$friendList = $this->upload("upload3")->userId;
		$offset = 1;
	
		$dataFetchedTimeKey = "Upload.dataFetchedTime";
		$time = Yii::app()->session[$dataFetchedTimeKey];
		$dataReader = Upload::model()->getUploadList($fileType,$userID,$friendList,$time,$offset);
		$row = $dataReader->read();
		$this->assertEquals($this->upload("upload3")->description, $row['description']);

		
		$time = null;
		$offset = 2;
		$dataReader = Upload::model()->getUploadList($fileType,$userID,$friendList,$time,$offset);
		$row = $dataReader->read();
		$this->assertEquals($this->upload("upload2")->description, $row['description']);
	}
}