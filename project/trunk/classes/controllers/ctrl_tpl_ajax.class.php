<?php

/**
 * The controller template class for ajaxed urls
 *
 * @package ADD MVC\Controllers
 * @since ADD MVC 0.0
 * @version 0.1
 * @author albertdiones@gmail.com
 *
 * @todo ADD MVC version 1.0: implement i_ctrl (version 0.9) for this
 *
 */
ABSTRACT CLASS ctrl_tpl_ajax EXTENDS ctrl_abstract IMPLEMENTS i_ctrl_0_9 {

   public $mode;

   /**
    * Mime type of this resource
    *
    * @since ADD MVC 0.8
    */
   protected $content_type = 'text/plain';

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

      # Set Content Type
      $this->content_type($this->content_type);

      $this->set_mode();

      add::$handle_shutdown = false;

      $this->process_data(
            isset($this->common_gpc)
            ? ctrl_tpl_page::recursive_compact( $this->common_gpc )
            : array()
         );

      $this->print_response($this->data);
   }

   /**
    * The pre-display process of the controller
    * (former $this->process())
    * @param array $common_gpc
    * @since ADD MVC 0.6
    *
    */
   public function process_data( $common_gpc = array() ) {

      $this->pre_mode_process($common_gpc);

      $process_mode_result = $this->process_mode( $common_gpc );

      $this->post_mode_process( $common_gpc );
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

   /**
    * sets the content_type or get the current one
    *
    * @param string $new_content_type
    * @deprecated use add::content_type()
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

   /**
    * Process before the main mode process
    *
    * @param array $common_gpc
    * @since ADD MVC 0.9
    */
   public function pre_mode_process($common_gpc) {
   }

   /**
    * Process after the main mode process
    *
    * @param array $common_gpc
    * @since ADD MVC 0.9
    */
   public function post_mode_process($common_gpc) {
   }
}