<?php
/**
 * Timer automatically used and printed when in development config environment status
 *
 * @package ADD MVC Debuggers
 *
 * @since ADD MVC 0.7.2
 */
CLASS add_development_timer EXTENDS \addph\debug\timer {

   public static function config() {
      return add::config();
   }

   public static function set_config($config) {
      throw new e_developer("Wrong use of function");
   }

/**
 * Always visible when not live
 *
 */
   public static function current_user_allowed() {
      return !add::is_live();
   }

}