<?php
/**
 * i_singleton interface class
 *
 * An interface for singleton type objects
 *
 * @author albertdiones@gmail.com
 * @package ADD MVC\Extras\Interfaces
 * @version 0.0
 *
 * @since ADD MVC 0.0
 */
INTERFACE i_singleton {

   /**
    * singleton() function
    *
    * Must return the one and only instance of the class
    * @since ADD MVC 0.0
    */
   public static function singleton();


   /**
    * __clone() magic function
    * Calling this function must be treated as an error
    * So the body of this function must throw new exception() or die() or trigger_error()
    * @since ADD MVC 0.0
    */
   function __clone();
}