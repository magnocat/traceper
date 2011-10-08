<?php
/*
 * 03.08.2011 Eren Alp Celik
 * 
 */
class CommentOperator{

	private $uploadCommentTable;
	private $dbc;
	
	private $dbc_mySqlOperator;
	
	public function __construct($tableprefix, $dbc)
    {	
    	
    	$this->uploadCommentTable=new UploadComment($tableprefix, $dbc);
    	$this->dbc=$dbc;
    }

    function __destruct() {
    }
	
	public function getComments($photoId) 
	{
		$this->dbc_mySqlOperator=new MySQLOperator("localhost","root","","traceper");
		
		$valuesArray = array(UploadComment::field_upload_id, UploadComment::field_photo_id, UploadComment::field_user_id, UploadComment::field_comment_time, UploadComment::field_comment);
		$condArr = array(UploadComment::field_photo_id => $photoId);	
		
		$result=$this->uploadCommentTable->select($valuesArray, $condArr);
		
		$str='<page pageNo="1" pageCount="1">';
		
		$uploadId=UploadComment::field_upload_id;
		$time=UploadComment::field_comment_time;
		$userId=UploadComment::field_user_id;
		$comment=UploadComment::field_comment;
		
		while ($row = $this->dbc->fetchObject($result) ){
			
			
			$sql_query='SELECT realname FROM traceper_users WHERE Id='.$row->$userId;		
			$userName=$this->dbc_mySqlOperator->getUniqueField($sql_query);
		
			$str.='<comment Id="'.$row->$uploadId.'" time="'.$row->$time.'" userId="'.$row->$userId.'" userName="'.$userName.'" >'.$row->$comment.'</comment>';
		
		}
	
		$str .= "</page>";
		
		header("Content-type: application/xml; charset=utf-8");
		
		return $str;
	}

	//Not In-use
	public function editComment($userId, $photoId, $comment, $newComment, $commentTime) 
	{
		//TODO: commentId direk kullanýlabilir durumda
		$fieldsArray = array(UploadComment::comment_id);
		$condArr = array(UploadComment::photo_id => $photo_id, UploadComment::user_id=>$userId, UploadComment::comment=>$comment );	
		$this->comment=$this->uploadCommentTable->select($fieldsArray, $condArr);
				
		$updateArray=array(UploadComment::comment => $comment,UploadComment::comment_time => $commentTime );
	    $condArr = array(UploadComment::comment_id => $this->comment);
	    
	    return $this->uploadCommentTable->update($updateArray, $condArr);
	}	
    
    public function insertNewComment($userId, $photoId, $comment) 
	{
		$elementsArray=array(UploadComment::field_photo_id=>$photoId, UploadComment::field_user_id=>$userId, UploadComment::field_comment=>"\"" . $comment . "\"", UploadComment::field_comment_time=>"NOW()");
		
		return $this->uploadCommentTable->insert($elementsArray);		
	}
	
	public function deleteComment($commentId) 
	{
		$deleteArray=array(UploadComment::field_upload_id => $commentId);
		$this->result=$this->uploadCommentTable->delete($deleteArray);
		
		return $this->result;
	}
}
?>