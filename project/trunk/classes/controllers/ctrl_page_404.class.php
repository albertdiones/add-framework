<?php
/**
 * ctrl_tpl_404 abstract model class
 * A sample 404 page controller
 *
 * @author albertdiones@gmail.com
 *
 * @package ADD MVC\Controllers\Extras
 * @since ADD MVC 0.0
 * @version 0.0
 */
CLASS ctrl_page_404 EXTENDS ctrl_tpl_page {

   /**
    * The pre-view process
    *
    * @since ADD MVC 0.0
    */
   public function process_data($common_gpc = array()) {
      header("HTTP/1.0 404 Not Found");
   }
}