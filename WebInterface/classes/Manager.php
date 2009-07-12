<?php

abstract class Manager 
{
	function checkVariable($string)
	{
		return str_replace ( array ( '&', '"', "'", '<', '>' ),
		array ( '&amp;' , '&quot;', '&apos;' , '&lt;' , '&gt;' ), $string );
	}
}
?>