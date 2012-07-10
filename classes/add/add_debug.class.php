<?php
/**
 * For debugging purposes only
 * Not intended to appear permanently on any code
 *
 * @author albertdiones@gmail.com
 *
 * @package ADD MVC\Debuggers
 * @since ADD MVC 0.0
 * @version 0.1
 */
ABSTRACT CLASS add_debug {
  /**
   * echo only if IP matched
   *
   * @param string $arg the string to echo
   *
   * @author albertdiones@gmail.com
   * @since ADD MVC 0.0
   */
   static function restricted_echo($arg) {

      if (static::current_user_allowed()) {
         echo $arg;
      }

   }

   /**
    * Declare this on extensions to set the allowed IPs or allowed users to see the debug prints
    * @return boolean true if allowed false if not
    * @since ADD MVC 0.0
    */
   abstract static function current_user_allowed();




  /**
   * Prints request variables and sessions variables
   * @since ADD MVC 0.0
   */
   static function print_request() {

      $request = array(
            "url" => self::current_url(),
            "get" => $_GET,
            "post" => $_POST,
            "cookie" => $_COOKIE,
            "request" => $_REQUEST,
            "session" => $_SESSION
         );

      self::html_print($request,"Request");
   }

   /**
    * Returns the current url
    *
    * @since ADD MVC 0.0
    */
   static function current_url() {
      return "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
   }

   /**
    * Prints/Returns location backtrace
    *
    * @param boolean $return true to return instead of echo
    *
    * @since ADD MVC 0.0
    */
   static function lines_backtrace($return=false) {
      $traces = debug_backtrace();
      $file_lines = array();

      foreach ($traces as $trace) {
         $file_line = array(
               'file'=>$trace[file],
               'line'=>$trace[line]
            );
         $file_lines[]  = $file_line;
         if (count(array_keys($file_lines,$file_line))>20) {
            die("Possible infinite loop detected");
         }
      }
      if ($return)
         return $file_lines;
      else {
         foreach ($file_lines as &$file_line) {
            $file_line = implode(":",$file_line);
         }
         self::html_print($file_lines,"Location");
      }
   }

  /**
   * Returns the file and line number of the caller of the function
   * @since ADD MVC 0.0
   */
   static function caller_file_line() {
      $backtraces = debug_backtrace();

      foreach ($backtraces as $backtrace) {
         $is_trace_class_debug = ($backtrace['class'] == __CLASS__ || is_subclass_of($backtrace['class'],__CLASS__));
         if (empty($backtrace['class']) || !$is_trace_class_debug) {
            break;
         }
         $file_line = $backtrace['file'].':'.$backtrace['line'];
      }

      if (!$file_line) {
         return "Unknown Location";
      }

      $file_line            = preg_replace('/^'.preg_quote(add::config()->root_dir,'/').'\//','',$file_line);

      return $file_line;

   }



   /**
    * XMP Var Dump
    * var_dump with <xmp>
    * @author albertdiones@gmail.com
    * @since ADD MVC 0.0
    */
   static function var_dump() {
      $args = func_get_args();
      if (!$args) {
         $args[0] = debug::get_declared_globals();
      }
      $var = self::return_var_dump($args);
      $output="<div style='clear:both'><b>".self::caller_file_line()."</b><xmp>".$var."</xmp></div>";
      self::restricted_echo($output);
      return $args[0];
   }

  /**
   * make a list out of $arg
   *
   * @param mixed $arg the data to print
   * @param string $name the title of this list
   *
   * @since ADD MVC 0.0
   */
   static function html_print($arg,$name) {
      ob_start();
      if (is_array($arg) || is_object($arg)) {
         echo("<ul class='html_print_ul html_print'><li>
             <b onclick='$(this).parents(\"li:eq(0)\").find(\"ul\").slideToggle()' style='cursor:pointer;text-decoration:underline;'>$name</b> <small>(".gettype($arg)."{".count($arg)."})</small>");

         foreach ($arg as $i=>$value) {
            self::html_print($value,$i);
            unset($value);
         }

      }
      else {
         echo("
           <ul class='html_print_one_item html_print'><li>
           <b>$name</b> <small>(".gettype($arg).")</small>");
         echo(": ");

         if (filter_var($arg,FILTER_VALIDATE_URL)) {
            echo("<a href='".htmlspecialchars($arg)."' target='_blank' >");
            if (preg_match('/(?i)\.(jpg|gif|png)/',$arg))
               echo("<img src='".htmlspecialchars($arg)."' border=0 onmouseover='$(this).css(\"height\",\"\")' onmouseout='$(this).css(\"height\",20)' style='height:20px;color:#888;'/>");
            else
               echo("$arg");
            echo("</a>");
         }
         else
            echo("$arg");
      }
      echo("</li></ul>");
      $output = ob_get_clean();
      self::restricted_echo($output);
   }

  /**
   * evaluates $var and prints $var and the value it returns
   *
   * @param string $var the command to eval
   *
   * @since ADD MVC 0.0
   */
  static function print_eval($var) {
      $var=preg_replace('/\$(\w+)/','$GLOBALS[$1]',$var);
      echo "<b>".htmlspecialchars($var)."</b> ";

      if (strpos($var,"return ")===false)
         $var = eval("return $var;");
      else
         $var = eval($var);

      if (is_string($var)) {
         echo $var."<br />";
      }
      else {
         echo "<xmp>".self::return_var_dump($var)."</xmp><br />";
      }

   }

   /**
    * Prints array into a HTML table
    *
    * @param array $array the array to convert
    *
    * @since ADD MVC 0.0
    */
   static function print_array_table($array) {
      $fields = array_keys($array[0]);
      echo "<table style='min-width:100%;border:1px solid #ccc;background:#e8e8e8' cellspacing=0 cellpadding=5 >";
      echo "<tr>";
      foreach ($fields as $field) {
         echo "<td style='font-weight:bold;background:e0e0e0;'>$field</td>";
      }
      echo "</tr>";
      $count = 0;
      foreach ($array as $item) {
         if ($count % 2 == 0) {
            $background = "#e0e0e0";
         }
         else {
            $background = "#d8d8d8";
         }
         echo "<tr>";
         foreach ($fields as $field) {
            echo "<td style='border:1px solid #e0e0e0;background:$background'>".$item[$field]."</td>";
         }
         echo "</tr>";
         $count++;
      }
      echo "</table>";
   }

   /**
    * deprecated_file() function
    * Call this on deprecated files
    * @since ADD MVC 0.0
    */
   public function deprecated_file() {
      $locations = self::location();
      $var_dump = self::return_var_dump($locations);
      mail(self::EMAIL_ADDRESS,"DEPRECATED FILE STILL IN USE ".self::caller_file_line(),$var_dump);
   }

   /**
    * return_var_dump()
    * get var dump function
    * @param mixed $args
    * @since ADD MVC 0.0
    */
   public function return_var_dump($args) {
      ob_start();
      call_user_func_array('var_dump',$args);
      $var = ob_get_clean();
      return $var;
   }

   /**
    * Returns an array of declared globals
    * @since ADD MVC 0.0
    */
   public function get_declared_globals() {
      return array_diff_key(
            $GLOBALS,
            array_flip(
                  array(
                        '_GET',
                        '_POST',
                        '_COOKIE',
                        '_REQUEST',
                        '_SERVER',
                        '_FILES',
                        '_ENV',
                        'php_errormsg',
                        'HTTP_RAW_POST_DATA',
                        'http_response_header',
                        'argc',
                        'argv',
                        'GLOBALS',
                     )
               )
         );
   }
}