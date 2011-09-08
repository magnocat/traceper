<?php
/*
 * 03.08.2011 Eren Alp Celik
 * 
 */

//ï¿½imdilik sadece resimler altï¿½na yorum koymak iï¿½in kullanï¿½lacak
//sonradan baï¿½ka itemlara da yorum koyabilecek.
class CommentOperator{
    

    private $uploadCommentTable;
	
	public function __construct($tableprefix, $dbc)
    {	
    	
    	$this->uploadCommentTable=new UploadComment($tableprefix, $dbc);
    }

    function __destruct() {
    }
	
	public function getComments($photoId) 
	{
		$valuesArray = array(UploadComment::commentId, UploadComment::photo_id, UploadComment::user_id, UploadComment::comment_time, UploadComment::comment);
		$condArr = array(UploadComment::photo_id => $photo_id);	
		
		return $this->uploadCommentTable->select($valuesArray, $condArr);
	}

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