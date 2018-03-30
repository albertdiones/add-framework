<?php
/**
 * Custom exception for ADD MVC
 * can mail error message to developers, change the view completely, smart assert etc.
 *
 * <code>
 * CLASS e_insufficient_credits EXTENDS e_system {
 *    public function mail_to() {
 *       return "management@add.ph";
 *    }
 * }
 * </code>
 *
 * @author albertdiones@gmail.com
 *
 * @package ADD MVC Exceptions
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
    *
    * <code>
    *    if (!$username)
    *       throw new e_user_input("Username is blank"):
    * </code>
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
    * Checks $condition, if false, throw the static exception class
    *
    * @param mixed $condition
    * @param string $message the error message
    * @param mixed $data extra data of the error
    * @param int $error_number the error code
    *
    * @todo Fix define of $parenthesis_count
    *
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
    * Exception email notification
    *
    *
    *
    * @since ADD MVC 0.0
    */
   public function mail() {
      mail(
            $this->mail_to(),
            $this->mail_subject(),
            "<pre style='font-size:12px'>".htmlentities($this->mail_body())."</pre>",
            "MIME-Version: 1.0\r\nContent-type: text/html; charset=iso-8859-1"
         );
   }

   /**
    * The exception email recipient
    * Defaults to add::config()->developer_emails
    *
    * @since ADD MVC 0.0
    */
   public function mail_to() {
      return static::$email_addresses;
   }

   /**
    * The full email subject
    *
    * @since ADD MVC 0.0
    */
   public function mail_subject() {
      return $this->truncated_subject("Error: ");
   }


   /**
    * Truncated subject
    *
    * @param string $prefix
    * @param string $suffix
    *
    */
   public function truncated_subject($prefix="", $suffix = "...") {
      $max_length = ( 80 - strlen($suffix) - strlen($prefix) );

      if (strlen($this->message) > $max_length) {
         return $prefix.substr($this->message, 0, $max_length).$suffix;
      }
      return $prefix.$this->message;
   }

   /**
    * The mail body / message
    * Extend this to child classes
    *
    * @todo current_user_ip on IP https://code.google.com/p/add-mvc-framework/issues/detail?id=157
    *
    * @since ADD MVC 0.0
    */
   public function mail_body() {

      $important_info = array(
            "Path"   => $_SERVER['REQUEST_URI'],
            "IP"     => $_SERVER['REMOTE_ADDR'],
            "User Agent"     => $_SERVER['HTTP_USER_AGENT'],
         );

      $original_message = $this->getMessage();
      $message = $this->truncated_subject();

      $header = "= $message =\r\n";

      if ($original_message != $message) {
         $header .= "$original_message\r\n\r\n";
      }


      return
         $header.
         debug::return_pretty_var_dump($important_info)."\r\n".
         "== Data ==\r\n".debug::return_pretty_var_dump($this->data)."\r\n".
         "== Trace ==\r\n".$this->getTraceAsString()."\r\n".
         "== Request Headers ==\r\n".debug::return_pretty_var_dump(function_exists('apache_request_headers') ? apache_request_headers() : false)."\r\n".
         "== Request ==\r\n".debug::return_pretty_var_dump($_REQUEST)."\r\n".
         "== Get ==\r\n".debug::return_pretty_var_dump($_GET)."\r\n".
         "== Post ==\r\n".debug::return_pretty_var_dump($_POST)."\r\n".
         "== Cookie ==\r\n".debug::return_pretty_var_dump($_COOKIE)."\r\n".
         "== Session ==\r\n".debug::return_pretty_var_dump($_SESSION)."\r\n".
         "== Server == \r\n".debug::return_pretty_var_dump($_SERVER);
   }

   /**
    * Handle the exception
    *
    * @since ADD MVC 0.0
    */
   public function handle_exception() {
      echo("{$this->getFile()}({$this->getLine()}): ({$this->getCode()}){$this->getMessage()}");
      add::shutdown();
   }


   /**
    * Handling exception as sensitive
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
         if (!$this->view()) {
            if (add::is_development()) {
               echo $this->getMessage();
            }
            else {
               echo $user_message;
            }
         }
         # Prevent misuse on live exceptions
         if (add::is_development()) {
            $this->view()->assign('exception',$this);
         }
         $this->view()->assign('user_message',$user_message);
         # note, to access config on the view, use add::config()
         #$this->view()->assign('C',add::config());
         $this->send_headers();
         $this->print_response();
      }
      else {
         $this->print_exception();
      }

   }
   /**
    * Print the exception
    *
    * @since ADD MVC 0.7
    */
   public function print_exception() {
      add::handle_error(E_USER_ERROR, $this->getMessage(), $this->getFile(), $this->getLine(),$this->data);
   }

   /**
    * The smarty object
    *
    * @since ADD MVC 0.7
    */
   public function view() {
      static $view;

      if (!class_exists('smarty')) {
         return false;
      }

      if (!isset($view)) {
         $view = new add_smarty();
      }

      return $view;

   }

   /**
    * Exception page view filepath name
    *
    */
   public static function view_filepath() {
      if (add::is_development()) {

         $tpl_filepath = "exceptions/development/".static::view_basename().".tpl";

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
         $tpl_filepath = "exceptions/".static::view_basename().".tpl";
         if (!static::view()->templateExists($tpl_filepath)) {
            $parent_class = get_parent_class(get_called_class());
            $tpl_filepath = $parent_class::view_filepath();
         }
         return $tpl_filepath;
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
    * @deprecated
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
      return $this->view()->display(static::view_filepath());
   }

   protected function send_headers() {
      header($_SERVER['SERVER_PROTOCOL'] . ' 500 Internal Server Error', true, 500);
   }
}
