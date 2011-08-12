<?php
/*
 * 03.08.2011 Eren Alp Celik
 * 
 * Ahmet'in MySQLOperator içerisinde sunduðu komutlarý kullanarak
 * kullanýcýlarýn resimler altýna girdiði mesajlarý veritabanýna
 * gönderir veya mevcut mesajlarda güncellemeler silmeler yapar. 
 * 
 * Çalýþmalar sonucunda Adnan'ýn BaseTable sýnýfý bu durum için kullanýþlý olmadý
 * 
 */
require_once('..\classes\tables\BaseTable.php');
require_once('..\classes\MySQLOperator.php');
require_once('..\classes\tables\UploadComment.php');

//þimdilik sadece resimler altýna yorum koymak için kullanýlacak
//sonradan baþka itemlara da yorum koyabilecek.
class CommentOperator{
    
    private $baseTable;
    private $dbc;
    private $userId;
    private $photoId;
    private $comment;
    private $result;
	
	public function __construct($userId, $dbc)
    {
    	//Kullanýcý yorum yazmaya yeltendiðinde ilk veritabaný baðlantýsý kurulsun
    	$this->userId=$userId;
    	$this->dbc=$dbc;
    	
		$this->dbc = new MySQLOperator("localhost","root","","php");
    	$this->baseTable = new BaseTable("traceper_upload_comment", $this->dbc);
    }

	//Gerekli mi bilmiyorum ama ben yine de koydum
    function __destruct() {
    }
	
	//bir resme baktýðýnda onun hakkýnda yapýlmýþ tüm yorumlarý datetime sýrasýnda getir
	public function fetchComments($photoId) 
	{
		$valuesArray = array(UploadComment::photo_id, UploadComment::user_id, UploadComment::comment_time, UploadComment::comment);
		$condArr = array(UploadComment::photo_id => $photo_id);	
		
		$this->assertTrue($this->baseTable->select($valuesArray, $condArr));
		
		//Select ile ilgili resim için yazýlmýþ tüm mesajlar çekilecek v ekrana parse edilecek
		//ilgili yorumun KÝÞÝ, ZAMAN ve ÝÇERÝK bilgileri çekilir
		//$sqlQuery="Select ".UploadComment::photo_id.",".UploadComment::user_id.",".
		//          UploadComment::comment_time.",".UploadComment::comment.
		//          "FROM traceper_upload_comment Where photo_id=".$photoId;
		//this->result=$this->dbc->query($sqlQuery);
	}

    //bir yorumu deðiþtirir
	public function editComment($photoId, $comment, $newComment, $commentTime) 
	{
		//Önce deðiþtirilecek yorumun id si çekilir.
		$fieldsArray = array(UploadComment::comment_id);
		$condArr = array(UploadComment::photo_id => $photo_id, UploadComment::user_id=>$this->usedId,UploadComment::comment=>$comment );	
		$this->comment=$this->baseTable->select($fieldsArray, $condArr);
				
		$updateArray=array(UploadComment::comment => $comment,UploadComment::comment_time => $commentTime )
	    $condArr = array(UploadComment::comment_id => $this->comment);
	    $this->assertTrue($this->baseTable->update($updateArray, $condArr));
	    
		//$sqlQuery="Select ".UploadComment::comment_id.",".
		//          "FROM traceper_upload_comment Where photo_id=".$photoId.
		//          " AND user_id=".$userId." AND comment=".$comment;
		//result ilgili yorumun id sini çeker, birazdan deðiþtirilecek
		//this->result=$this->dbc->query($sqlQuery);
		
		//$sqlQuery="UPDATE traceper_upload_comment ".
        //          "SET comment=".$newComment.", comment_time=".$commentTime.
        //         "WHERE ".UploadComment::comment_id."=".$result;
		//$this->dbc->query($sqlQuery);	

	}	
    
	//yeni yorum gir
    public function insertNewComment($photoId, $commentTime, $comment) 
	{
		$elementsArray=array(UploadComment::photo_id,UploadComment::user_id, UploadComment::comment, UploadComment::comment_time);
		$valuesArray=array($photoId, $this->userId, $commentTime, $comment);
		$this->assertTrue($this->baseTable->insert($elementsArray, $valuesArray));		
		
		//$sqlQuery="INSERT INTO table_name (photo_id, user_id, comment, comment_time) ".
		//"VALUES (".$photoId.",".$this->userId.",".$commentTime.",".$comment.")";
		//this->dbc->query($sqlQuery);
	}
	
	//gerekli durumlarda ilgili yorumlarýn silinmesi için
	public function deleteComments($photoId)
	{
		//Önce silinecek yorumun id si çekilir.
		$fieldsArray = array(UploadComment::comment_id);
		$condArr = array(UploadComment::photo_id => $photo_id, UploadComment::user_id=>$this->usedId,UploadComment::comment=>$comment );	
		$this->result=$this->baseTable->select($fieldsArray, $condArr);
		
		$deleteArray=array(UploadComment::comment_id => $this->result);
		$this->result=$this->assertTrue($this->baseTable->delete($deleteArray));
		
		//$sqlQuery="Select ".UploadComment::comment_id.",".
		//          "FROM traceper_upload_comment Where photo_id=".$photoId.
		//          " AND user_id=".$this->userId." AND comment=".$comment;
	
 		//result silinecek yorumun id sini çeker, birazdan silinecek
		//this->result=$this->dbc->query($sqlQuery);
		//$sqlQuery="DELETE FROM traceper_upload_comment WHERE ".
		//			UploadComment::comment_id."=".$result;
		
	}
}
?>