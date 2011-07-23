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
			$result = true;
		}
		
		return $result;
	}
}