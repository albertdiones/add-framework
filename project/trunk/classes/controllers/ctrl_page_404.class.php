<?php
/**
 * A sample 404 page controller
 *
 * replace this with your own on your application's /classes directory
 *
 *
 * @package ADD MVC Controllers\Extras
 * @since ADD MVC 0.0
 * @version 0.0
 */
CLASS ctrl_page_404 EXTENDS ctrl_tpl_page {

   /**
    * The pre-view process
    *
    * @param array $common_gpc
    *
    * @since ADD MVC 0.0
    */
   public function process_data($common_gpc = array()) {
      header("HTTP/1.0 404 Not Found");
      $this->assign('is_development', add::is_development());
   }
}