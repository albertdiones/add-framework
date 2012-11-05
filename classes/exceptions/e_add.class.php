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
    * Exception constructor
    *
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
    * @param mixed $data extra data of the error
    * @param int $error_number the error code
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

            $tokens = token_get_all("<?php $file_line_content ?>");

            $assert_arguments = array();
            $assert_argument_string = "";
            $on_argument      = false;
            for ( $token_x = 0; isset($tokens[$token_x]); ++$token_x ) {
               $token = $tokens[$token_x];
               if ($token === array(307,get_called_class(),1) && isset($tokens[$token_x+1])) {
                  $pn_token = $tokens[++$token_x]; # Paamayim Nekudotayim
                  if ($pn_token === array(376,"::",1) && isset($tokens[$token_x+2])) {
                     $assert_token = $tokens[++$token_x];
                     if ($assert_token === array(307,__FUNCTION__,1)) {
                        $parenthesis_count = 1;
                        $on_argument = true;
                        ++$token_x;
                        continue;
                     }
                  }
               }

               if ($on_argument) {

                  if ($token === '(') {
                     ++$parenthesis_count;
                  }


                  if ($token === ')') {
                     --$parenthesis_count;
                  }

                  if ($parenthesis_count<=0) {
                     break;
                  }

                  $assert_argument_string .= is_array($token) ? $token[1] : $token;

               }

            }

            if (!$assert_argument_string) {
               $assert_argument_string = preg_replace('/^\s*\$?\w+\:\:assert\((.+)(\,.+)?\)\;/','$1',$file_line_content);
            }

            if (preg_match('/(?P<function_name>\w+)\((?P<arguments>.*?)\)/', $assert_argument_string, $assert_condition_parts)) {
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
               $message = "Failed to validate condition ".$assert_argument_string;
            }
         }
         $e = new static($message,$data, $error_number);
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
         "*Path*: $_SERVER[REQUEST_URI] *IP*: $_SERVER[REMOTE_ADDR]\r\n".
         "== Data ==\r\n".
         ($this->data ? print_r($this->data,true) : "_null_ \r\n").
         "== Trace ==\r\n".
         print_r($this->getTrace(),true)."\r\n".
         "== Request ==\r\n".
         print_r($_REQUEST,true).
         "== Get ==\r\n".
         print_r($_GET,true).
         "== Post ==\r\n".
         print_r($_POST,true).
         "== Cookie ==\r\n".
         print_r($_COOKIE,true).
         "== Server == \r\n".
         print_r($_SERVER,true);
   }

   /**
    * The handling of the exception
    * Extend this to child classes
    *
    * @since ADD MVC 0.0
    */
   public function handle_exception() {
      echo("{$this->getFile()}({$this->getLine()}): ({$this->getCode()}){$this->getMessage()}");
      add::shutdown();
   }


   /**
    * Handling Exceptions
    *
    * @param string $user_message
    *
    * @since ADD MVC 0.7
    */
   public function handle_sensitive_exception($user_message = "An error has occured") {
      if (!add::is_development()) {
         $this->mail();
      }

      if (!headers_sent()) {
         while (ob_get_level()) {
            ob_end_clean();
         }
         if (add::is_development()) {
            # Prevent misuse on live exceptions
            $this->view()->assign('exception',$this);
         }
         $this->view()->assign('user_message',$user_message);
         # note, to access config on the view, use add::config()
         #$this->view()->assign('C',add::config());
         $this->print_response();
      }
      else {
         $this->print_exception();
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

   /**
    * Exception page view
    *
    */
   public static function view_filepath() {
      if (add::is_development()) {

         $tpl_filepath = "exceptions/development/".self::view_basename().".tpl";

         if (!static::view()->templateExists($tpl_filepath)) {
            $tpl_filepath = "exceptions/development/e_add.tpl";
         }

         if (static::view()->templateExists($tpl_filepath)) {
            return $tpl_filepath;
         }
         else {
            # Throw a default exception cause we are already on a a custom exception
            throw new Exception("No development view file found for exception ".print_r($this,true));
         }
      }
      else {
         return "exceptions/".self::view_basename().".tpl";
      }
   }

   /**
    * The basename of the view of the exception
    *
    */
   public static function view_basename() {
      return get_called_class();
   }


   /**
    * Backward support
    *
    * @since ADD MVC 0.8
    */
   public function display_view() {
      return $this->print_response();
   }

   /**
    * Print the exception's response
    *
    * @since ADD MVC 0.8
    */
   public function print_response() {
      return $this->view()->display(self::view_filepath());
   }
}