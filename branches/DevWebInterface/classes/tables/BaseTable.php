<?php
class BaseTable
{
	private $tableName;
	private $dbc;
	
	function __construct($tableName, $dbc)
	{
		$this->tableName = $tableName;
		$this->dbc = $dbc;
	}
	
	public function update($values, $conditions, $limit=1) {

		$keyValuePair = array();
		foreach ($values as $key => $value)
		{
			array_push($keyValuePair, $key .'='. $value);
		}
		
		$sqlUpdatePart = implode(',', $keyValuePair);
		
		
		$sql = 'UPDATE ' . $this->tableName
				.' SET ' . $sqlUpdatePart;
		
		if(!empty($conditions) && is_array($conditions))
		{
			$conditionValuePair = array();
			foreach ($conditions as $key => $value)
			{
				array_push($conditionValuePair, $key .'='. $value);
			}
	
			$sqlConditionPart = implode(',', $conditionValuePair);
			
			$sql .= ' WHERE ' . $sqlConditionPart;
		}
		
		$sql .= ' LIMIT '. $limit;

		$result = false;
		if($this->dbc->query($sql) != false)
		{
			$result = $this->dbc->getAffectedRows();
		}
		
		return $result;
	}
	
	public function select($values, $conditions, $limit=1) {
		
		$sqlSelectPart = implode(',', $values);

		$sql = 'SELECT ' . $sqlSelectPart . ' FROM ' . $this->tableName;				
		
		if(!empty($conditions) && is_array($conditions))
		{
			$conditionValuePair = array();
			foreach ($conditions as $key => $value)
			{
				array_push($conditionValuePair, $key .'='. $value);
			}
	
			$sqlConditionPart = implode(',', $conditionValuePair);
			
			$sql .= ' WHERE ' . $sqlConditionPart;
		}
		
		$sql .= ' LIMIT '. $limit;
		
		//echo $sql;

		$result = $this->dbc->query($sql);
		
		//echo "Number Of Rows:".$this->dbc->numRows($result);
		
		return $result;
	}	
	
	public function insert($elements) {
		
		$keysArray = array();
		$valuesArray = array();
		foreach ($elements as $key => $value)
		{
			array_push($keysArray, $key);
			array_push($valuesArray, $value);
		}

		$sqlElementsPart = implode(',', $keysArray);
		$sqlValuesPart = implode(',', $valuesArray);

		$sql = 'INSERT INTO ' . $this->tableName . '(' . $sqlElementsPart . ')' . 'VALUES(' . $sqlValuesPart .')';
					
		$result = false;
		if($this->dbc->query($sql) != false)
		{
			$result = true;
		}
		
		//echo $sql;
		
		return $result;
	}	
	
	public function delete($conditions, $limit=1) {
		
		$sql = 'DELETE FROM ' . $this->tableName;				
		
		if(!empty($conditions) && is_array($conditions))
		{
			$conditionValuePair = array();
			foreach ($conditions as $key => $value)
			{
				array_push($conditionValuePair, $key .'='. $value);
			}
	
			$sqlConditionPart = implode(',', $conditionValuePair);
			
			$sql .= ' WHERE ' . $sqlConditionPart;
		}
		
		$sql .= ' LIMIT '. $limit;
		
		//echo $sql;

		$result = false;
		if($this->dbc->query($sql) != false)
		{
			$result = true;
		}		
		
		//echo "Number Of Rows:".$this->dbc->numRows($result);
		
		return $result;
	}
		
	
	
	
	/*
	//Eren tarafýndan eklendi, veritabanýndan veri silme yöntemi saðlamak için
	public function delete($values)
	{
		$keyValuePair = array();
		foreach ($values as $key => $value)
		{
			array_push($keyValuePair, $key .'='. $value);
		}
		
		$sqlDeletePart = implode(',', $keyValuePair);
		
		
		$sql = 'DELETE ' . $this->tableName
				.' WHERE ' . $sqlDeletePart;

		echo $sql;

		$result = false;
		if($this->dbc->query($sql) != false)
		{
			$result = true;
		}

		return $result;
	}
	*/
	
}

?>