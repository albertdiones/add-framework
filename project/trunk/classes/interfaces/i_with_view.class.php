<?php

/**
 * Interface for classes with views
 *
 * @since ADD MVC 0.7
 */
INTERFACE i_with_view {

   /**
    * The view filepath of $this controller
    * @since ADD MVC 0.7
    */
   public static function view_filepath();
   /**
    * The view base file name of $this controller
    * @since ADD MVC 0.7
    */
   public static function view_basename();

   /**
    * The Smarty view object of $this controller
    * @since ADD MVC 0.7
    */
   public function view();

}