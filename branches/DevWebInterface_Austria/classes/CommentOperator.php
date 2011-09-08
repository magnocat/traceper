<?php
/*
 * 03.08.2011 Eren Alp Celik
 * 
 */
require_once('..\classes\tables\BaseTable.php');
require_once('..\classes\MySQLOperator.php');
require_once('..\classes\tables\UploadComment.php');

//�imdilik sadece resimler alt�na yorum koymak i�in kullan�lacak
//sonradan ba�ka itemlara da yorum koyabilecek.
class CommentOperator{
    
    private $baseTable;
    private $dbc;
    private $userId;
    private $photoId;
    private $comment;
    private $result;
	
	public function __construct($dbc)
    {
    	$this->dbc=$dbc;   	
    }

    function __destruct() {
    }
	
	public function getComments($photoId) 
	{
		$valuesArray = array(UploadComment::commentId, UploadComment::photo_id, UploadComment::user_id, UploadComment::comment_time, UploadComment::comment);
		$condArr = array(UploadComment::photo_id => $photo_id);	
		
		return $this->baseTable->select($valuesArray, $condArr);

	}

	public function editComment($userId, $photoId, $comment, $newComment, $commentTime) 
	{
		$fieldsArray = array(UploadComment::comment_id);
		$condArr = array(UploadComment::photo_id => $photo_id, UploadComment::user_id=>$userId, UploadComment::comment=>$comment );	
		$this->comment=$this->baseTable->select($fieldsArray, $condArr);
				
		$updateArray=array(UploadComment::comment => $comment,UploadComment::comment_time => $commentTime );
	    $condArr = array(UploadComment::comment_id => $this->comment);
	    
	    return $this->baseTable->update($updateArray, $condArr);
	}	
    
    public function insertNewComment($userId, $photoId, $commentTime, $comment) 
	{
		$elementsArray=array(UploadComment::photo_id,UploadComment::user_id, UploadComment::comment, UploadComment::comment_time);
		$valuesArray=array($photoId, $userId, $commentTime, $comment);
		
		return $this->baseTable->insert($elementsArray, $valuesArray);		
	}
	
	public function deleteComments($userId, $photoId, $comment, $commentTime) 
	{
		$fieldsArray = array(UploadComment::comment_id);
		$condArr = array(UploadComment::photo_id => $photo_id, UploadComment::user_id=>$userId,UploadComment::comment=>$comment );	
		$this->result=$this->baseTable->select($fieldsArray, $condArr);
				
		$deleteArray=array(UploadComment::comment_id => $this->result);
		$this->result=$this->baseTable->delete($deleteArray);

		return $this->result;
		
	}
}
?>