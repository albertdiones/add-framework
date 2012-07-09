<?php
/**
 * ctrl_tpl_with_view controller interface
 *
 * @author albertdiones@gmail.com
 *
 * @package ADD MVC\Controllers
 * @since ADD MVC 0.3
 * @version 0.0
 */
INTERFACE i_ctrl_with_view {

   /**
    * The view filepath of $this controller
    * @since ADD MVC 0.3
    */
   public static function view_filepath();
   /**
    * The view base file name of $this controller
    * @since ADD MVC 0.3
    * @version 0.1
    */
   public static function view_basename();

   /**
    * The controller's basename
    * @since ADD MVC 0.3
    */
   public static function basename();

   /**
    * The Smarty view object of $this controller
    * @since ADD MVC 0.3
    */
   public function view();

}