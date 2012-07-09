<?php
/**
 * Debug timer class
 * @todo remove the extra micro times that the functions are using
 * @author albertdiones@gmail.com
 *
 * @package ADD MVC\Debuggers
 * @since ADD MVC 0.0
 * @version 0.0
 */
ABSTRACT CLASS add_debug_timer EXTENDS add_debug {

   /**
    * The start timestamp of the timer
    *
    * @since ADD MVC 0.0
    */
   protected $start_timestamp;

   /**
    * The timestamp of the laps
    *
    * @since ADD MVC 0.0
    */
   protected $lap_timestamps = array();

   /**
    * Construct the timer and start it
    *
    * @param $label the label of the point
    *
    * @since ADD MVC 0.0
    */
   protected function __construct($label) {

      $this->start_timestamp = microtime(true);

      $this->lap($label);

   }

   /**
    * Construct and start the timer
    *
    * @param $label the label of the point
    *
    * @since ADD MVC 0.0
    * @version 0.1
    */
   public static function start($label=null) {
      return new static($label);
   }

   /**
    * Add a lap with $label
    * @param string $label the description of where the function is called (eg. "before mysql query" )
    *
    * @since ADD MVC 0.0
    * @version 0.1
    */
   public function lap($label=NULL) {
      $timestamp = microtime(true);

      if (!$label)
         $label = static::caller_file_line();

      if (!isset($this->lap_timestamps["$timestamp"]))
         $this->lap_timestamps["$timestamp"] = $label;
      else
         $this->lap_timestamps["$timestamp_".uniqid()] = $label;

      return $timestamp - $this->start_timestamp;
   }


  /**
   * Add a lap with $label and print the details
   * @param string $label the description of the line where it is placed (eg. "after mysql query" )
   * @author albertdiones@gmail.com
   *
   * @since ADD MVC 0.0
   * @version 0.1
   */
   public function print_lap($label=NULL) {

      $lap_difference = $this->lap($label);

      list($lap_label_x) = array_slice(array_keys($this->lap_timestamps),-1,1);
      list($first_lap_label_x) = array_slice(array_keys($this->lap_timestamps),0,1);

      #var_dump($previous_lap_label,$lap_label);

      static::restricted_echo("<div>".static::us_diff_html(static::us_diff_readable_format($lap_difference),$lap_difference)." from {$this->lap_timestamps[$first_lap_label_x]} to {$this->lap_timestamps[$lap_label_x]}</div>");
   }

   /**
    * Microseconds Diff Readable Format
    * @param int $microseconds the microseconds
    * @since ADD MVC 0.0
    */
   public function us_diff_readable_format($microseconds) {
      $difference_ms = $microseconds*1000;
      return "$difference_ms milliseconds";
   }


   /**
    * Microseconds diff html style
    *
    * @since ADD MVC 0.4
    */
   public function us_diff_html($string,$microseconds) {
      $styles_array = array();
      if ($microseconds > 1000000) {
         $styles_array[] = 'color:red';
         if ($microseconds > 10000000.0) {
            $styles_array[] = 'font-weight:bold';
         }
      }
      else if ($microseconds < 1000) {
         $styles_array[] = 'color:green';
      }
      $styles = implode(';',$styles_array);
      return "<span style='$styles'>$string</span>";
   }
}