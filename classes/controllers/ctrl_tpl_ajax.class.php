<?php

/**
 * The controller template class for ajaxed urls
 *
 * @package ADD MVC\Controllers
 * @since ADD MVC 0.0
 * @version 0.1
 * @author albertdiones@gmail.com
 */
ABSTRACT CLASS ctrl_tpl_ajax IMPLEMENTS i_ctrl {

   public $mode;


   /**
    * The view data
    *
    * @since ADD MVC 0.6
    */
   protected $data = array();


   /**
    * __call() magic function
    *
    * @since ADD MVC 0.6
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
    * Must echo the page response
    *
    * @since ADD MVC 0.6
    */
   public function execute() {
      # ADD MVC 0.5 backward support
      if (method_exists($this,'page')) {
         return $this->page();
      }

      $this->mode = isset($_REQUEST['mode']) ? "$_REQUEST[mode]" : '';
      add::$handle_shutdown = false;
      $this->process_data();
      $this->print_response($this->data);
   }

   /**
    * process_mode function
    * Processes any GPC requests
    *
    * @since ADD MVC 0.6
    *
    */
   public function process_mode() {
      $mode = $this->mode;
      if ($mode) {

         $method_name = "process_mode_$mode";

         if (method_exists($this,$method_name)) {

            $gpc_key_var = "mode_gpc_$mode";

            if (isset($this->$gpc_key_var)) {
               $compact_array = ctrl_tpl_page::recursive_compact( $this->$gpc_key_var );
            }

            return $this->$method_name($compact_array);
         }
      }
      return false;
   }

    /**
    * The pre-display process of the controller
    * (former $this->process())
    *
    * @since ADD MVC 0.6
    *
    */
   public function process_data() {

      return $this->process_mode();
   }

   /**
    * assign $variable to pass in ajax
    *
    * @since ADD MVC 0.6
    */
   public function assign() {
      $arg1 = func_get_arg(0);

      if (is_array($arg1) || is_object($arg1)) {
         $this->data = array_merge($this->data,(array) $arg1);
      }
      else {
         $this->data[$arg1] = func_get_arg(1);
      }
   }

   /**
    * convert $data to json
    * @since ADD MVC 0.6
    *
    */
   public function print_response($data) {
      echo json_encode($data);
   }
}