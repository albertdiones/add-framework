<?php

/**
 * ctrl_tpl_aux
 * Auxiliary Script Controller
 *
 * @since ADD MVC 0.4
 * @author albertdiones@gmail.com
 * @version 1.0
 *
 */
ABSTRACT CLASS ctrl_tpl_aux {
   protected $response;

   /**
    * Mime type of this resource
    *
    * @since ADD MVC 0.8
    */
   protected $content_type = 'text/plain';

   /**
    * run the page
    *
    * @since ADD MVC 0.6.2
    */
   public function execute() {
      e_hack::assert($this->can_run(),"Invalid aux script authentication key");

      # Set Content Type
      $this->content_type($this->content_type);

      ob_start();
      try {
         $this->process_data();
      }
      catch(Exception $e) {
         add::handle_exception($e);
      }
      $this->response = ob_get_clean();

      $this->handle_response();
   }

   /**
    * __call() magic function
    *
    * @since ADD MVC 0.6.2
    */
   public function __call($function_name,$arguments) {
      static $renamed_functions = array(
            'page'         => 'execute',
            'process'      => 'process_data',
         );

      if (isset($renamed_functions[$function_name])) {
         call_user_func_array(array($this,$renamed_functions[$function_name]),$arguments);
      }
      else {
         throw new e_developer("Undefined function: ".get_called_class()." $function_name",func_get_args());
      }

   }

   /**
    * handle the response
    *
    * @since ADD MVC 0.4
    */
   public function handle_response() {
      echo $this->response;
   }

   /**
    * Check
    *
    * @since ADD MVC 0.5
    */
   public function can_run() {
      return $_REQUEST['aux_auth'] == $this->aux_key();
   }

   /**
    * The auxiliary key
    *
    * @since ADD MVC 0.4
    */
   public function aux_key() {
      return null;
   }



   /**
    * Logs string to the log file
    *
    * @param string $string the string to log
    *
    * @since ADD MVC 0.5
    */
   public function log($string) {
      e_developer::assert(method_exists($this,'log_filepath'),__CLASS__." log file is not set");
      file_put_contents($this->log_filepath(),$string."\r\n",FILE_APPEND);
   }


   /**
    * The log filepath
    *
    * @since ADD MVC 0.5
    */
   public function log_filepath() {
      return $C->incs_dir."/logs/".get_called_class().".log.txt";
   }

   /**
    * sets the content_type or get the current one
    *
    * @param string $new_content_type
    *
    * @since ADD MVC 0.8
    */
   public function content_type($new_content_type = null) {
      if ($new_content_type) {
         $this->content_type = $new_content_type;
         header("Content-type: ".$this->content_type);
      }
      return $this->content_type;
   }
}