<?php

/**
 * Temporary Data Store Operator
 * @author mekya
 *
 */
class TempDataStoreOperator {

	public function __construct(){
		session_start();
	}
	
	/**
	 * $days is the number of how many days data is stored
	 */
	public function save($key, $value, $days=0){
		$_SESSION[$key] = $value;
		if ($days > 0) {
			setCookie($key, $value, time()+ 60 * 60 * 24 * $days);
		}
	}	
	
	public function getValue($key){
		$value = NULL;
		if (isset($_SESSION[$key]) === true){
			$value = $_SESSION[$key];
		}
		else if (isset($_COOKIE[$key]) === true){
			$value = $_COOKIE[$key];
		}
		return $value;
	}
	
	public function clearAll(){
		$cookiesSet = array_keys($_COOKIE);
		for ($x=0; $x<count($cookiesSet); $x++){
			setcookie($cookiesSet[$x], null, time()-1);	
		} 
		return session_destroy();
	}

}