<?php

/**
 * add_development_timer
 *
 * @since
 */
CLASS add_development_timer EXTENDS add_debug_timer {
   public static function current_user_allowed() {
      return !add::is_live();
   }
}