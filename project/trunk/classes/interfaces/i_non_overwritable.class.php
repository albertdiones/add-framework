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

   function __destruct();

   public function __clone();

   static function singleton(/* Polymorphic */);

}