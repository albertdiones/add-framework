<?php
/**
 * Add Framework Class
 * Every sitewide features and modules should be based from this class
 * @author albertdiones@gmail.com
 * @package ADD MVC
 * @version 0.6
 */
CLASS add {

   /**
    * The config variable
    * @since ADD MVC 0.0
    */
   protected static $C;

   /**
    * The config variable name
    * @since ADD MVC 0.0
    */
   const CONFIG_VARNAME = 'C';

   /**
    * The home or index controller's basename
    * example: ctrl_page_index
    * @since ADD MVC 0.0
    */
   static $HOME_CONTROLLER = 'index';

   /**
    * Gets the site config
    *
    * @param STDClass $C the config variable
    *
    * @returns the config object
    * @since ADD MVC 0.0
    */
   static function config(STDClass $C = null) {

      if ($C) {
         self::$C = $C;
         $GLOBALS[self::CONFIG_VARNAME] = self::$C;
      }

      return self::$C;

   }

   /**
    * The include file path
    * or the add file path(if existing) of the path
    * example: include_filepath('functions/common.functions.php')
    * @param string $path the include path
    * @since ADD MVC 0.0
    */
   static function include_filepath($path) {
      global $C;
      $real_path = $path;
      $incs_path = $path = "$C->incs_dir/$path";

      if (file_exists($path)) {
         return $path;
      }
      else {
         $lib_path = "$C->add_dir/$real_path";
         if (file_exists($lib_path))
            return $lib_path;
      }
      return $incs_path;
   }


   /**
    * Autoload class function
    *
    * @param string $classname
    *
    * @todo on 1.0, move the ifs() and deprecate filename without .class.
    * @author albertdiones@gmail.com
    * @since ADD MVC 0.0
    * @version 1.0
    */
   static function load_class($classname) {


      global $C;

      if (class_exists('e_developer',false))
         e_developer::assert($classname,"Blank classname");

      $incs_class_filepath = add::config()->classes_dir."/$classname.class.php";

      if (file_exists($incs_class_filepath)) {
         $class_filepath = $incs_class_filepath;
      }

      # Backward support to 0.0
      if (empty($class_filepath)) {
         $incs_class_filepath = add::config()->classes_dir."/$classname.php";
         if (file_exists($incs_class_filepath)) {
            $class_filepath = $incs_class_filepath;
            trigger_error("{$class_filepath} format is deprecated",E_USER_NOTICE);
         }
      }

      if (empty($class_filepath)) {
         $class_filepath_wildcard = $C->incs_dir.'/classes/*/'.$classname.'.class.php';
         $class_filepath_search = glob($class_filepath_wildcard);

         # Backward support to 0.0
         if (!$class_filepath_search) {
            $class_filepath_wildcard = $C->incs_dir.'/classes/*/'.$classname.'.php';
            if ($class_filepath_search = glob($class_filepath_wildcard)) {
               trigger_error("{$class_filepath_search[0]} format is deprecated",E_USER_NOTICE);
            }
         }

         if (!$class_filepath_search) {
            $class_filepath_wildcard = $C->add_dir.'/classes/*/'.$classname.'.class.php';
            $class_filepath_search = glob($class_filepath_wildcard);
         }

         # Backward support to 0.0
         if (!$class_filepath_search) {
            $class_filepath_wildcard = $C->add_dir.'/classes/*/'.$classname.'.php';
            if ($class_filepath_search = glob($class_filepath_wildcard)) {
               trigger_error("{$class_filepath_search[0]} format is deprecated",E_USER_NOTICE);
            }
         }

         if ($class_filepath_search) {
            $class_filepath = $class_filepath_search[0];
         }
      }

      # Backward support to 0.2
      if (empty($class_filepath)) {
         $class_filepath = $C->add_dir.'/classes/'.$classname.'.php';
         if (file_exists($class_filepath)) {
            trigger_error("{$class_filepath} format is deprecated",E_USER_NOTICE);
         }
         if (!file_exists($class_filepath))
            $class_filepath = null;
      }

      /*if (empty($class_filepath)) {
         $e_class = 'e_developer';
         if (class_exists($e_class)) {
            throw new $e_class("$classname not found",array($C->add_dir,add::config()->classes_dir));
         }
         else {
            throw new Exception("$classname not found" . $C->add_dir . " " . add::config()->classes_dir );
         }
      }*/
      if ($class_filepath)
         include_once($class_filepath);

      if ($class_filepath && !class_exists($classname) && !interface_exists($classname)) {
         $e_class = 'e_developer';
         if (class_exists($e_class)) {
            throw new $e_class("$classname not found from $class_filepath");
         }
         else {
            throw new Exception("$classname not found from $class_filepath");
         }
      }

      return class_exists($classname);
   }

   /**
    * handle_exception()
    * The error handler for exceptions
    * @param Exception $e the exception to handle
    * @since ADD MVC 0.0
    */
   static function handle_exception(Exception $e) {
      if (method_exists($e,'handle_exception'))
         return $e->handle_exception();
      else {
         while (ob_get_level()) {
            ob_end_clean();
         }
         die("<div style='color:red'>".$e->getMessage()."</div>");
      }
   }

   /**
    * handle_error()
    * callback function for handling trigger_error() errors
    *
    * @param int  $errno contains the level of the error raised, as an integer.
    * @param string $errstr contains the error message, as a string.
    * @param string $errfile contains the filename that the error was raised in, as a string.
    * @param int $errline contains the line number the error was raised at, as an integer.
    * @param array $errcontext an array that points to the active symbol table at the point the error occurred. In other words, errcontext will contain an array of every variable that existed in the scope the error was triggered in. User error handler must not modify error context.
    *
    * @see http://www.php.net/manual/en/function.set-error-handler.php
    */
   public static function handle_error($errno , $errstr , $errfile = NULL, $errline = NULL , $errcontext = NULL) {
      global $G_errors;

      static $error_indexes = array(
            E_ERROR        => 'error',
            E_WARNING      => 'warnings',
            E_NOTICE       => 'notices',
            E_STRICT       => 'strict',
            E_USER_ERROR   => 'user_errors',
            E_USER_WARNING => 'user_warnings',
            E_USER_NOTICE  => 'user_notices'
         );

      if (!isset($G_errors)) {
         $G_errors = array();
      }

      if (!(error_reporting() & $errno)) {
         return;
      }

      $error_index = isset($error_indexes[$errno]) ? $error_indexes[$errno] : $errno;

      if (!isset($G_errors[$error_index]))
         $G_errors[$error_index] = array();
      $G_errors[$error_index][] = array(
            'message' => $errstr,
            'file'    => $errfile,
            'line'    => $errline
         );
   }

   /**
    * Print errors
    */
   static function print_errors() {
      global $G_errors;
      foreach ($G_errors as $error_index => $errors) {
         foreach ($errors as $error) {
            echo("<div style='margin:5px 10px;border:1px solid #333; background: #faa; padding:5px 10px'><small>$error_index</small><p>$error[message]</p><small>".basename($error['file']).":$error[line]</small></div>");
         }
      }
   }

   /**
    * Handle shutdown
    *
    * @since ADD MVC 0.5.1
    */
   static function handle_shutdown() {
      return static::print_errors();
   }

   /**
    * include() include file path
    * example add::include_include_file('functions/date.functions.php')
    * @param string $include_path the include path
    * @since ADD MVC 0.0
    */
   protected static function include_include_file($include_path) {
      return include(static::include_filepath($include_path));
   }
   /**
    * load_functions($functions_group_name)
    *
    * @param string $functions_group_name the base name of the function file to include
    *
    * example: add::load_functions('date');
    * @since ADD MVC 0.0
    */
   static function load_functions($functions_group_name) {
      return include_once(static::include_filepath("functions/$functions_group_name.functions.php"));
   }

   /**
    * Load library from $C config
    * @param string $lib_name the $C->libs index
    * @since ADD MVC 0.0
    */
   static function load_lib($lib_name) {
      static $loaded_libs=array();

      if (in_array($lib_name,$loaded_libs)) {
         return;
      }

      $C = self::config();

      $lib = $C->libs->$lib_name;

      if (!$lib)
         throw new Exception("Library $lib_name not found");

      if (is_string($lib)) {
         $lib_path = $lib;
      }
      else if (is_object($lib) && isset($lib->init_path)) {
         $lib_path = $lib->init_path;
      }
      else {
         throw new logic_exception("Invalid format for $lib_name");
      }

      $file = self::include_filepath('libs/'.$lib_path);

      if (file_exists($file))
         require_once($file);
      else
         throw new e_developer("Failed to load library $lib from ".$file);

   }


   /**
    * current_controller()
    * Returns the current controller object instance
    *
    * @since ADD MVC 0.0
    */
   static function current_controller() {
      static $current_controller;

      if (!isset($current_controller)) {
         $class_name = self::current_controller_class();
         if ($class_name && class_exists($class_name)) {
            $current_controller = new $class_name();
         }
         else {
            $current_controller = new ctrl_page_404();
         }
      }

      return $current_controller;

   }

   /**
    * add::current_controller_class()
    * The controller class according to the current url
    * take note that all controllers start with the prefix ctrl_page_
    * @return string the controller class name
    * @since ADD MVC 0.0
    */
   static function current_controller_class() {
      static $current_controller_class;
      if (!isset($current_controller_class)) {
         $current_controller_class = 'ctrl_page_'.self::current_controller_basename();
      }
      return $current_controller_class;
   }

   /**
    * add::current_controller_basename()
    * Returns the basename (minus the prefix) of the current controller
    * @return string the controller's basename
    * @since ADD MVC 0.0
    */
   static function current_controller_basename() {
      global $C;
      static $current_controller_basename;
      if (!isset($current_controller_basename)) {
         $relative_path = isset($_GET['add_mvc_path']) ? "$_GET[add_mvc_path]" : preg_replace('/^.*\/(.+?)(\?.*)?$/','$1',$_SERVER['REQUEST_URI']);
         $current_controller_basename = $relative_path;
         $current_controller_basename = preg_replace('/\-+/','_',$current_controller_basename);
         $current_controller_basename = preg_replace('/\.php$/','',$current_controller_basename);

         if (preg_match('/\W+/',$current_controller_basename)) {
            return null;
         }

         if (!$current_controller_basename)
            $current_controller_basename = self::$HOME_CONTROLLER;
      }
      return $current_controller_basename;
   }


   /**
    * canonicalize URL to the current controller
    *
    * @since ADD MVC 0.5
    */
   static function canonicalize_path() {
      if (add::current_controller()->path() != "$_GET[add_mvc_path]") {
         add::current_controller()->redirect();
      }
   }


   /**
    * Redirect function
    * die() included
    * @param string $url the href to redirect into
    * todo do not die when failed to rediredct
    */
   function redirect($url) {
      header("Location: $url");
      die();
   }

   /**
    * void redirect_query(array $new_query,bool $merge_current=true)
    * redirects to the new query string, die()s in the process
    * @param array $new_query
    * @param boolean $merge_current true to include current $_GET with $new_query
    * @todo support string on $new_query
    * @uses redirect()
    */
   function redirect_query($new_query,$merge_current=true) {
      if ($merge_current)
         $query = array_merge($_GET,$new_query);
      else
         $query = $new_query;
      return redirect("?".http_build_query($query));
   }

}