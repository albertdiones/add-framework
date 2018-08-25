<?php

/**
 * Default Debug class
 * Override this on application for customized debugging
 *
 * @author albertdiones@gmail.com
 *
 * @package ADD MVC Debuggers
 * @since ADD MVC 0.0
 * @version 0.1
 */
ABSTRACT CLASS debug EXTENDS \addph\debug\debug {

   public static function config() {
      return add::config();
   }

   public static function set_config($config) {
      throw new e_developer("Wrong use of function");
   }

}