<?php
//require_once('..\includes\config.php');
require_once('..\classes\tables\CommentOperator.php');
require_once('..\classes\MySQLOperator.php');
require_once('..\classes\tables\UploadComment.php');

class CommentOperatorTest extends PHPUnit_Framework_TestCase {
    
    private $commentOperator;
    private $dbc;
    private $userId;
	
	public function setUp()
    {
    	$this->userId=1;
    	
		$this->dbc = new MySQLOperator("localhost","root","","php");
    	$this->commentOperator = new CommentOperator($this->userId, $this->dbc);
    }

    public function tearDown()
    {
        unset($this->commentOperator);
    }	
   
    public function testInsert() 
	{
		$photoId=1;
		$commentTime=10;  // ??????
		$comment="Example comment for trials";
		
		$this->assertTrue($this->commentOperator->insertNewComment($photoId, $commentTime, $comment));
	}
	
	public function testUpdate() 
	{
		$photoId=1;
		$comment="Example comment for trials";
		
		$newComment="Example comment for Update trials";
		$commentTime=15;

		$this->assertTrue($this->commentOperator->editComment($photoId, $comment, $newComment, $commentTime));
	}	
 
	
	public function testSelect() 
	{
		$photoId=1;
		$this->assertTrue($this->commentOperator->fetchComments($photoId)); 
	}	
	
	public function testDelete()
	{
		$photoId=1;
		
		$this->assertTrue($this->commentOperator->deleteComments($photoId));
	}
}
?>