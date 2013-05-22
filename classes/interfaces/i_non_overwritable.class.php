<?php
/**
 * i_non_overwritable interface
 * an interface for non overwritable class
 *
 * @author albertdiones@gmail.com
 * @package ADD MVC\Extras\Interfaces
 * @version 0.0
 *
 * @deprecated unused
 * @since ADD MVC 0.0
 */
INTERFACE i_non_overwritable {

   /**
    * Destruct, error out when the instance is destroyed
    *
    */
   function __destruct();

   /**
    * Prevent cloning
    *
    */
   public function __clone();

   /**
    * Returns a cached single instance of this class
    *
    */
   static function singleton(/* Polymorphic */);

}