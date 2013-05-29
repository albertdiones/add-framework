<?php
/**
 * adodb wrapper abstract
 * A class to be used as wrapper on adodb
 * One child class = one database connection, therefore for each database connection you must create a concrete class or use the 2 built in concrete class mysql and mssql
 *
 * @see add_adodb_mysql , add_adodb_mssql, model_rwd::db()
 *
 *
 * Being considered to be deprecated
 * @package ADD MVC ADODB wrappers
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

   /**
    * Contains data about the last operation
    *
    * @see __call and check_error()
    *
    */
   public $add_last_operation;

   /**
    * Error codes to ignore, those that are normally throwing such as insert duplicate error
    *
    */
   static $ignore_error_numbers = array();

   /**
    * Creates a single instance for the adodb class
    *
    */
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

   /**
    * Construct from adodb, see singleton() and Connect
    *
    * @param object $adodb
    *
    *
    */
   function __construct($adodb) {
      $this->adodb = $adodb;
   }

   /**
    * Prevent to be cloned
    *
    */
   public function __clone() {
      throw new Exception('Clone is not allowed.');
   }

   /**
    * Record the function and the arguments then call the adodb method
    * Also checks for errors
    *
    * @param string $method
    * @param array $args
    *
    */
   function __call($method, $args) {//call adodb methods
      $this->add_last_operation = array(array($this->adodb, $method),$args);
      $this->add_last_operation['result'] = call_user_func_array(array($this->adodb, $method),$args);
      $this->check_error();
      return $this->add_last_operation['result'];
   }

   /**
    * Get the property of the inner adodb object
    *
    * @param string $property
    *
    */
   function __get($property) {
      return $this -> adodb -> $property;
   }

   /**
    * Set the property of the inner adodb object
    *
    * @param string $property
    * @param mixed $value
    *
    */
   function __set($property, $value) {
      $this -> adodb -> $property = $value;
   }

   /**
    * Check if there is error on the last operation
    *
    */
   function check_error() {
      $last_operation = &$this->add_last_operation;
      if (!$last_operation['result'] && ($last_operation['error_message'] = $this->adodb->ErrorMsg()) && !in_array($this->adodb->ErrorNo(),static::$ignore_error_numbers)) {
         $this->handle_error();
      }
   }


   /**
    * Handle the error, extend this to handle specific erors
    *
    */
   function handle_error() {
      trigger_error("ADODB Error: (".$this->adodb->ErrorNo().")".$this->add_last_operation['error_message']);
   }

   /**
    * Extend this method to specify the credentials
    *
    */
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