<?php
/**
 * ctrl_default_page default page for views with no controllers
 *
 * Prints the view without any process
 *
 * @author albertdiones@gmail.com
 *
 * @package ADD MVC\Controllers
 * @since ADD MVC 0.10
 * @version 0.0
 */
CLASS ctrl_default_page EXTENDS ctrl_tpl_page {

   /**
    * Gets the original controller basename
    *
    * @since ADD MVC 0.10
    */
   public static function view_basename() {
      return add::current_controller_basename();
   }

}