<?php

/**
 * add_development_timer
 *
 * @since ADD MVC 0.7.2
 */
CLASS add_development_timer EXTENDS add_debug_timer {
   public static function current_user_allowed() {
      return !add::is_live();
   }

  /**
   * print_lap_difference between two laps, additional style for development timer
   *
   * @since ADD MVC 0.7.2
   */
   public function print_lap_difference($lap1, $lap2) {
      echo "<div style='text-align:center; padding:10px 20px; margin: 10px 20px;'>";
      parent::print_lap_difference($lap1,$lap2);
      echo "</div>";
   }
}