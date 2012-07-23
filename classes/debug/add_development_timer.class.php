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
   * Add a lap with $label and print the details
   * @param string $label the description of the line where it is placed (eg. "after mysql query" )
   * @author albertdiones@gmail.com
   *
   * @since ADD MVC 0.0
   * @version 0.1
   */
   public function print_lap_difference($label=NULL) {
      echo "<div style='text-align:center; padding:10px 20px; margin: 10px 20px;'>";
      parent::print_lap_difference($label);
      echo "</div>";
   }
}