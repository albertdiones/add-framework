<?php
/**
 * adodb wrapper abstract
 * A class to be used as wrapper on adodb
 * Being considered to be deprecated
 * @package ADD MVC\ADODB wrappers
 * @version 0.0
 */
ABSTRACT CLASS add_adodb {

   /**
    * The default adodb fetch mode
    * @since 0.0
    */
   const FETCH_MODE = ADODB_FETCH_ASSOC;

   /**
    * The adodb object variable
    * @since 0.0
    */
   public $adodb;

   public static $debug = false;
   public $add_last_operation;

   static $ignore_error_numbers = array();

   static function singleton() {
      $adodb = ADONewConnection(static::DB_TYPE);
      $global_varname = static::varname();

      if (
         !isset($GLOBALS[$global_varname])
         ||
            (
            isset($GLOBALS[$global_varname])
            &&
            !$GLOBALS[$global_varname] instanceof self
            )
         ) {
         $adodb_wrapper = $GLOBALS[$global_varname] = new static($adodb);
         $adodb_wrapper -> Connect();
         $adodb_wrapper -> adodb->SetFetchMode(static::FETCH_MODE);
      }

      return $GLOBALS[$global_varname];
   }

   function __construct($adodb) {
      $this->adodb = $adodb;
   }


   public function __clone() {
      throw new Exception('Clone is not allowed.');
   }

   function __call($method, $args) {//call adodb methods
      $this->add_last_operation = array(array($this->adodb, $method),$args);
      $this->add_last_operation['result'] = call_user_func_array(array($this->adodb, $method),$args);
      $this->check_error();
      return $this->add_last_operation['result'];
   }

   function __get($property) {
      return $this -> adodb -> $property;
   }

   function __set($property, $value) {
      $this ->adodb -> $property = $value;
   }

   function check_error() {
      $last_operation = &$this->add_last_operation;
      if (!$last_operation['result'] && ($last_operation['error_message'] = $this->adodb->ErrorMsg()) && !in_array($this->adodb->ErrorNo(),static::$ignore_error_numbers)) {
         $this->handle_error();
      }
   }

   function handle_error() {
      trigger_error("ADODB Error: (".$this->adodb->ErrorNo().")".$this->add_last_operation['error_message']);
   }

   static function debug() {
      return debug::x(func_get_args());
   }

   abstract public function Connect();

   /**
    * The variable name of the singleton of this class
    *
    * @since ADD MVC 0.7
    */
   public static function varname() {
      return get_called_class();
   }

}