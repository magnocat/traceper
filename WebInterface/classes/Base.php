<?php

abstract class Base 
{
	/**
	 * this is temp data store operator instance
	 */
	protected $tdo = NULL;
	/**
	 * database operator object
	 */
	protected $dbc = NULL; 
	
	public static function checkVariable($string)
	{
		return str_replace ( array ( '&', '"', "'", '<', '>' ),
		array ( '&amp;' , '&quot;', '&apos;' , '&lt;' , '&gt;' ), $string );
	}
	
	public function setTempDataStoreOperator($Op){
		$this->tdo = $Op;
	}
	
	/**
	 * Set database connectivity object
	 */	 
	public function setDatabaseConnectivity($dbc){
		$this->dbc;		
	}
	
	
}
?>