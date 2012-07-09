<?php

/**
 * The controller template class for ajaxed urls
 *
 * @package ADD MVC\Controllers
 * @since ADD MVC 0.0
 * @version 0.1
 * @author albertdiones@gmail.com
 */
ABSTRACT CLASS ctrl_tpl_ajax {

   public $mode;

   /**
    * Must echo the page response
    *
    * @since ADD MVC 0.0
    */
   public function page() {
      $this->mode = "$_REQUEST[mode]";
      $this->process_mode();
   }

   /**
    * process_mode function
    * Processes any GPC requests
    *
    * @since ADD MVC 0.1
    * @version 0.1.2
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
}