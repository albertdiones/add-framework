<?php

/**
 * ctrl_tpl_aux
 * Auxiliary Script Controller
 *
 * @since ADD MVC 0.4
 * @author albertdiones@gmail.com
 * @version 0.1
 *
 */
ABSTRACT CLASS ctrl_tpl_aux {
   protected $response;

   /**
    * run the page
    *
    * @since ADD MVC 0.4
    */
   public function page() {
      header("Content-type: text/plain");
      e_hack::assert($this->can_run(),"Invalid aux script authentication key");

      ob_start();
      try {
         $this->process();
      }
      catch(Exception $e) {
         add::handle_exception($e);
      }
      $this->response = ob_get_clean();
      $this->handle_response();
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
}