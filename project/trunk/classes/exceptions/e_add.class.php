<?php
/**
 * e_add class
 * Custom Exception Class
 *
 * @author albertdiones@gmail.com
 *
 * @package ADD MVC\Exceptions
 * @since ADD MVC 0.0
 * @version 0.1
 */
CLASS e_add EXTENDS Exception {

   /**
    * Email adress(es) to send debug email to
    * @see e_add::mail()
    * @since ADD MVC 0.3
    */
   static $email_addresses;

   /**
    * exception data
    * @since ADD MVC 0.0
    */
   public $data;

   /**
    * @param string $message
    * @param mixed $data
    * @param int $code
    * @param Exception $exception
    *
    * @since ADD MVC 0.4
    */
   public function __construct($message,$data=NULL,$code = 0,Exception $exception = null) {
      $this->data = $data;
      return parent::__construct($message,$code,$exception);
   }

   /**
    * Checks $condition, if false, throw the exception class
    *
    * @param mixed $condition
    * @param string $message the error message
    * @param int $error_number the error code
    * @param mixed $data extra data of the error
    *
    * @since ADD MVC 0.0
    * @version 0.1
    */
   static function assert($condition, $message, $data=NULL, $error_number=NULL) {
      if (!$condition) {
         $e = new static($message,$error_number);
         $e->data = $data;
         throw $e;
      }
   }

   /**
    * Send email about the exception
    *
    * @since ADD MVC 0.0
    */
   public function mail() {
      mail($this->mail_to(),$this->mail_subject(),$this->mail_body());
   }

   /**
    * The email recipient
    * Extend this to child classes
    *
    * @since ADD MVC 0.0
    */
   public function mail_to() {
      return static::$email_addresses;
   }

   /**
    * The email subject
    *
    * @since ADD MVC 0.0
    */
   public function mail_subject() {
      return "Error: ".$this->message;
   }

   /**
    * The mail body / message
    * Extend this to child classes
    *
    * @since ADD MVC 0.0
    */
   public function mail_body() {
      return
         "Path: $_SERVER[REQUEST_URI]\r\n".
         "Referrer: $_SERVER[HTTP_REFERRER]\r\n".
         "IP: $_SERVER[REMOTE_ADDR]\r\n".
         "Data:\r\n".
         print_r($this->data,true)."\r\n".
         "Trace:\r\n".
         print_r($this->getTrace(),true)."\r\n".
         "Request:\r\n".
         print_r($_REQUEST,true).
         "Get:\r\n".
         print_r($_GET,true).
         "Post:\r\n".
         print_r($_POST,true).
         "Cookie:\r\n".
         print_r($_COOKIE,true).
         "Server:\r\n".
         print_r($_SERVER,true);
   }

   /**
    * The handling of the exception
    * Extend this to child classes
    *
    * @since ADD MVC 0.0
    */
   public function handle_exception() {
      die("{$this->getFile()}({$this->getLine()}): ({$this->getCode()}){$this->getMessage()}");
   }
}