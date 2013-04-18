<?php
/**
 * e_database class
 * Custom Exception Class for database errors
 *
 * @author albertdiones@gmail.com
 *
 * @package ADD MVC\Exceptions
 * @since ADD MVC 0.0
 * @version 0.0
 */
CLASS e_database EXTENDS e_unknown {

   /**
    * Class constructor
    *
    * @param string $message exception message
    * @param array $data extra error data
    * @param int $code user defined exception code
    * @param Exception $previous previous exception if nested exception
    *
    * @since ADD MVC 0.0
    */
   public function __construct($message = null, $data=array(), $code = 0, Exception $previous = null) {
      $code = (int) preg_replace('/\D/','',$code);

      if (!$code)
         $code = NULL;

      parent::__construct($message,$data,$code,$previous);
      switch ($code) {
         case 1062:
            if (preg_match('/Duplicate entry \\\'(?P<duplicate_value>.+?)\\\' for key (?<key>.+)/',$message,$message_parts)) {
               $data['duplicate_field'] = $data['field'] ? implode(" or ",array_keys($data['fields'],$message_parts[duplicate_value])) : '';
               $this->message = "#$code Duplicate $data[table] data $data[duplicate_field] field value '$message_parts[duplicate_value]'";
               break;
            }
         case 1048:
            if (preg_match('/column \\\'(?P<field>.+?)\\\' cannot be null/',$message,$message_parts)) {
               $this->message = "#$code $data[table] $data[field] field is blank";
               break;
            }
         case 1452:
            if (preg_match('/Cannot .+ \(.+\/(?P<table1>\w+).\, CONSTRAINT .+ FOREIGN KEY \(.(?P<field1>\w+).\) REFERENCES .(?P<table2>\w+). \(.(?P<field2>\w+).\)/',$message,$message_parts)) {
               $this->message = "$message_parts[table1] $message_parts[field1] should be a valid $message_parts[table2] $message_parts[field2]";
               break;
            }
         default:
            $this->data = $data;
            $this->message =  $this->getMessage();
         break;
      }
   }


   /**
    * The handling of the exception
    * Extend this to child classes
    *
    * @since ADD MVC 0.0
    */
   public function handle_exception() {
      $this->handle_sensitive_exception("A database error occured");
   }

   /**
    * The email subject
    *
    * @since ADD MVC 0.10.7
    */
   public function mail_subject() {
      return $this->truncated_subject("Database Error".($this->getCode() ? '#'.$this->getCode() : '').": ");
   }
}