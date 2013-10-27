<?php
  /**
   * Multi Email Validator
   * 
   * @author Thom Downing
   * @link http://www.ThomDowning.com
   */
  class MultiEmailValidator extends CEmailValidator
  {
    /** emails separated by */
    public $delimiter = array(',');

    /** minimum number of emails required */
    public $min = 0;

    /** maxmimum number of emails required */
    public $max = 0;

    /**
     * validate comma-separated email string
     * @param object data model
     * @param string data model attributes
     *
     */
    protected function validateAttribute($object, $attribute)
    {
      $values = $object->$attribute;
      if($this->allowEmpty && $this->isEmpty($values))
        return;
      
      $values = str_replace(array(" ",",","\r","\n"),array(",",",",",",","),$values);
      $values = str_replace(",,", ",",$values);
    
      $values = explode(",", $values);
      $count = count($values);

      if ($count > $this->max && $this->max != 0)
      {
        $message=$this->message!==null?$this->message:Yii::t('yii', 'A maximum of {value} email(s) allowed.');
        $this->addError($object,$attribute,$message, array('{value}'=>$this->max));
        return;
      }

      if ($count < $this->min && $this->min != 0)
      {
        $message=$this->message!==null?$this->message:Yii::t('yii', 'At least {value} email(s) required.');
        $this->addError($object,$attribute,$message, array('{value}'=>$this->min));
        return;
      }

      foreach($values as $value)
      {
        $value = trim($value);

        if (!parent::validateValue($value))
        {
          if (!empty($value))
          {
            $message=$this->message!==null?$this->message:Yii::t('site', '"{value}" is not a valid email address');
            $this->addError($object,$attribute,$message, array('{value}'=>$value));
          }
        }
      }
    }
  }
?>