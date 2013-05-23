<?php
/**
 * Automatic timer printed on the shutdown handle when in development environment_status
 *
 * @package ADD MVC\Debuggers
 *
 * @since ADD MVC 0.7.2
 */
CLASS add_development_timer EXTENDS add_debug_timer {

/**
 * Always visible when not live
 *
 */
   public static function current_user_allowed() {
      return !add::is_live();
   }

}