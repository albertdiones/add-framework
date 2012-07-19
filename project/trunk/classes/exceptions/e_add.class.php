<?php
/**
 * e_add class
 * Custom Exception Class
 *
 * @author albertdiones@gmail.com
 *
 * @package ADD MVC\Exceptions
 * @since ADD MVC 0.0
 * @version 0.2
 */
CLASS e_add EXTENDS Exception IMPLEMENTS i_with_view {

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
   static function assert($condition, $message = null, $data=NULL, $error_number=NULL) {
      if (!$condition) {
         if (!$message) {
            $caller_backtrace = debug::caller_backtrace();
            $file_lines = file($caller_backtrace['file']);
            $file_line_content = $file_lines[$caller_backtrace['line']-1];
            $assert_condition = preg_replace('/^\s*\w+\:\:assert\((.+)(\,.+)?\)\;/','$1',$file_line_content);
            if (preg_match('/(?P<function_name>\w+)\((?P<arguments>.*?)\)/', $assert_condition, $assert_condition_parts)) {
               $function = $assert_condition_parts['function_name'];
               $arguments = $assert_condition_parts['arguments'];
               switch ($function) {
                  case 'is_int':
                     $message = "$arguments is not integer";
                  break;

                  case 'is_array':
                     $message = "$arguments is not array";
                  break;

                  case 'is_bool':
                     $message = "$arguments is not boolean";
                  break;

                  case 'is_callable':
                     $message = "$arguments is not callable";
                  break;

                  case 'is_double':
                     $message = "$arguments is not double";
                  break;

                  case 'is_float':
                     $message = "$arguments is not float";
                  break;

                  case 'is_null':
                     $message = "$arguments is not null";
                  break;

                  case 'is_numeric':
                     $message = "$arguments is not numeric";
                  break;

                  case 'is_object':
                     $message = "$arguments is not object";
                  break;

                  case 'is_resource':
                     $message = "$arguments is not resource";
                  break;

                  case 'is_scalar':
                     $message = "$arguments is not scalar";
                  break;

                  case 'is_string':
                     $message = "$arguments is not string";
                  break;


                  case 'isset':
                     $message = "$arguments is not set";
                  break;

                  case 'empty':
                     $message = "$arguments is not empty";
                  break;


                  case 'ctype_digit':
                     $message = "$arguments is not numeric";
                  break;


                  case 'is_float':
                     $message = "$arguments is not float";
                  break;

                  case 'filter_var':
                     $message = "$arguments is not accepted";
                  break;

                  case 'filter_has_var':
                     $message = "$arguments does not exist";
                  break;

                  case 'filter_input_array':
                     $message = "$arguments is not accepted";
                  break;

                  case 'filter_has_var':
                     $message = "$arguments does not exist";
                  break;


                  default:
                     $message ="Failed to validate $function ($arguments) ";
               }
            }
            else {
               $message = "Failed to validate condition ".$assert_condition;
            }
         }
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


   /**
    * Handling Exceptions
    *
    * @since ADD MVC 0.7
    */
   public function handle_sensitive_exception($user_message = "An error has occured") {
      if (add::is_development()) {
         $this->print_exception();
         die();
      }
      else {
         $this->mail();
         $this->view()->assign('exception',$this);
         $this->view()->assign('$C',add::config());
         $this->display_view();
      }
   }
   /**
    * print_exception()
    *
    * @since ADD MVC 0.7
    */
   public function print_exception() {
      add::handle_error(E_USER_ERROR, $this->getMessage(), $this->getFile(), $this->getLine(),null);
   }

   /**
    * The smarty object
    *
    * @since ADD MVC 0.7
    */
   public function view() {
      static $view;

      if (!isset($view)) {
         $view = new add_smarty();
      }

      return $view;

   }

   public static function view_filepath() {
      return "exceptions/".self::view_basename().".tpl";
   }

   public static function view_basename() {
      return get_called_class();
   }


   public function display_view() {
      return $this->view()->display(self::view_filepath());
   }
}