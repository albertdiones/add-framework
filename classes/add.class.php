<?php
/**
 * Add Framework Class
 * Every sitewide features and modules should be based from this class
 *
 * This is my prayer for this project: Dear Lord God the Great I am, I pray unto you in the name of your son Jesus, let your holy spirit guide this prayer as I exhort it through the codes done by your wisdom given to me. Thank you for the guidance you've given us, the wisdom, the patience and the visions you. Thank you for the ideas you've given to me through the whispers of your Holy Spirit. Thank you for the opportunity you've given to me to make this project. Thank you for our company, our bosses and our team mates and office mates and friends. Thank you for our job, our position and all the blessings you give to us through your children. Forgive us for our pride, for our ego, for our boasting and for our self-reliance. Forgive us for not thanking you in every goal met, task completed and bugs fixed. Forgive those that wants to do us harm and those that already did. and please give us the strength to forgive them too. I pray unto you to give us more faith in every thing we do, after every tasks, after every version. Guide us with your Holy Spirit and teach us how to do things great. Let your hands be our hands and let your mind be our mind. Give us wisdom, patience and humility in everything we do. Lord please continue to bless me and continue to use me as a proof your existence, let the wisdom you've given me be light to world, not as an excuse to unbelief. Let this project be fruitful and useful for all who use it. Let there projects be protected from all harm and be useful to there clients. Let every developer who will contribute to this project be blessed and be gifted be wisdom. Give us the peace no one can understand, even on the toughest problem we face. Let every one who is involved on this project be blessed and let everything I prayed for be applied to them as well. All these I pray in the name of your Son, Jesus.
 *
 * @author albertdiones@gmail.com
 * @package ADD MVC
 * @version 0.10.3
 */
CLASS add {

   /**
    * The config variable
    * @since ADD MVC 0.0
    */
   protected static $C;

   /**
    * The config variable name
    *
    * @todo remove support for $C on ADD MVC 1.0
    *
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
    * Errors Array
    *
    * @since ADD MVC 0.7
    */
   static $errors = array(
         E_ERROR           => array(),
         E_WARNING         => array(),
         E_NOTICE          => array(),
         E_STRICT          => array(),
         E_DEPRECATED      => array(),
         E_USER_ERROR      => array(),
         E_USER_WARNING    => array(),
         E_USER_NOTICE     => array(),
      );

   /**
    * Weather to handle shutdown or not
    *
    * @since ADD MVC 0.7.2
    */
   static $handle_shutdown = false;

   /**
    * Environment status
    *
    * @since ADD MVC 0.10
    */
   protected static $environment_status = 'live';


   /**
    * Mime type of this resource
    *
    * @since ADD MVC 0.10.5
    */
   protected static $content_type = 'text/html';

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
         $config_array = (array) $C;
         $full_default_config_array = (array) self::default_config();

         $default_config_array = array_diff_key($full_default_config_array, $config_array);
         $merge_config_array = array_intersect_key($config_array,$full_default_config_array);

         $config_array = array_merge( $default_config_array , $config_array );

         foreach ($merge_config_array as $key => $value) {
            if (is_object($value))
               $value = (array) $value;
            if (is_array($value))
               $config_array[$key] = array_merge((array) $value, (array) $full_default_config_array[$key]);
            else
               $config_array[$key] = $value;
         }

         self::$C = $GLOBALS[self::CONFIG_VARNAME] = (object) $config_array;
         self::$environment_status = self::$C->environment_status;

         # Convert to object
         foreach (self::$C as &$var) {
            if (is_array($var)) {
               $var = (object) $var;
            }
         }

      }
      return self::$C;

   }

   /**
    * The default config
    *
    * @since ADD MVC 0.7
    */
   public static function default_config() {
      preg_match('/^((?P<sub_domain>\w+)\.)?(?P<super_domain>((\w+\.)+(?P<tld>\w+))|\w+)$/',$_SERVER['HTTP_HOST'],$domain_parts);
      return (object) array(
            'super_domain'       => isset($domain_parts['super_domain']) ? $domain_parts['super_domain'] : null,
            'sub_domain'         => isset($domain_parts['sub_domain']) ? $domain_parts['sub_domain'] : null,
            'path'               => preg_replace('/\/[^\/]*?$/','/',$_SERVER['REQUEST_URI']),
            'root_dir'           => realpath('./'),
            'environment_status' => 'live',
            'developer_ips'      => array(),

            /**
             * Library init files
             */
            'libs'            => (object) array(
                  'adodb'     => 'adodb/adodb.inc.php',
                  'smarty'    => 'smarty/Smarty.class.php',
                  'phpmailer' => 'phpmailer/class.phpmailer.php',
               ),
         );
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

      # Iterate it through classes directories
      foreach (add::config()->classes_dirs as $classes_dir) {
         # Load it from the application's class dir

         $class_filepath_wildcard = "$classes_dir/{,*/}$classname.class.php";
         $class_filepath_search = glob($class_filepath_wildcard,GLOB_BRACE);

         # Backward support to 0.2 (without .class)
         if (!$class_filepath_search) {
            $class_filepath_wildcard = "$classes_dir/{,*/}$classname.php";
            $class_filepath_search = glob($class_filepath_wildcard,GLOB_BRACE);
         }

         if ($class_filepath_search) {
            $class_filepath = $class_filepath_search[0];
            break;
         }

      }

      if (!empty($class_filepath)) {
         $class_loaded = include_once($class_filepath);
         $class_loaded &=
            (
               class_exists($classname,false)
               ||
               interface_exists($classname,false)
               ||
               ( function_exists('trait_exists') && trait_exists($classname,false) )
            );
      }
      else {
         $class_loaded = false;
      }

      # Check if the class is actually loaded
      if (!empty($class_filepath) && !$class_loaded) {

         $e_class = 'e_developer';

         if (class_exists($e_class)) {
            throw new $e_class("$classname not found from $class_filepath");
         }
         else {
            throw new Exception("$classname not found from $class_filepath");
         }

      }

      # ADD MVC Class Loaded Event
      if ($class_loaded && method_exists($classname,'__add_loaded')) {
         $classname::__add_loaded();
      }

      return $class_loaded;
   }

   /**
    * handle_exception()
    * The error handler for exceptions
    * @param Exception $e the exception to handle
    * @since ADD MVC 0.0
    */
   static function handle_exception(Exception $e) {
      try {

         static::ob_flush();

         try {
            if (method_exists($e,'handle_exception')) {
               return $e->handle_exception();
            }
            else {
               throw new Exception("Non e_add exception sub class or no handle_exception() function found for exception");
            }
         }
         catch (Exception $e2) {

            if ($e instanceof SmartyException) {
               static::handle_exception(
                     new e_smarty(
                           $e->getMessage(),
                           # Fixed Issue #58, e_smarty data is too long
                           array(
                              'message' => $e->getMessage(),
                              'code'    => $e->getCode(),
                              'trace'   => str_replace(
                                    array(
                                          add::config()->root_dir
                                          ,add::config()->add_dir
                                       )
                                       ,
                                    array(
                                          '*root_dir*',
                                          '*add_dir*',
                                       ),
                                    $e->getTraceAsString()
                                 ),
                           )
                        )
                  );
            }

            if (add::content_type() == 'text/plain') {
               die(
                     get_class($e)."(#".$e->getCode().")\r\n\r\n".
                     $e->getMessage()."\r\n\r\n".$e->getFile().":".$e->getLine()."\r\n\r\n\r\n\r\n"
                  );
            }
            else {

               if (add::is_developer()) {
                  DEFINE('add\terminal_error\error_message',$e->getMessage());
                  DEFINE('add\terminal_error\error_trace',$e->getTraceAsString());
                  DEFINE('add\terminal_error\error_header',get_class($e));
                  DEFINE('add\terminal_error\error_footer',$e->getFile().":".$e->getLine());
               }
               else {
                  $debug_mail_to = add::config()->developer_emails;

                  if (is_array($debug_mail_to) || is_object($debug_mail_to)) {
                     $debug_mail_to = implode(",",(array)$debug_mail_to);
                  }

                  mail(
                        $debug_mail_to,
                        "Terminal Error: ".$e->getMessage(),
                        $e->getTraceAsString()
                     );

                  DEFINE('add\terminal_error\error_message','An error occured<br />please contact the system administrators or go back to <a href="'.add::config()->path.'">Home</a>');
                  DEFINE('add\terminal_error\error_header','Warning!');
                  DEFINE('add\terminal_error\error_footer','Please contact the system administrators');
                  DEFINE('add\terminal_error\error_trace','');
               }


               if (!headers_sent() && !add::include_include_file('terminal_error.php')) {
                  throw new Exception("Failed to display terminal error");
               }
               add::shutdown(false);
            }
         }
      }
      catch (Exception $e3) {
         $e1_string = "<div style='color:red'>".get_class($e)."(#".$e->getCode().")".$e->getMessage()."\r\n".$e->getFile().":".$e->getLine()."</div>";

         echo $e1_string;

         $e3_string = "<div style='color:red'>".get_class($e3)."(#".$e3->getCode().")".$e3->getMessage()."\r\n".$e3->getFile().":".$e3->getLine()."</div>";

         if ($e3_string != $e1_string) {
            echo $e3_string;
         }
         debug::var_dump($e3->getTraceAsString());

         die();
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

      static $error_code_readable_strings = array(
            E_ERROR           => 'Fatal Error',
            E_WARNING         => 'Warning',
            E_PARSE           => 'Parse Error',
            E_NOTICE          => 'Notice',
            E_STRICT          => 'PHP Strict Standards',
            E_DEPRECATED      => 'Deprecated',
            E_CORE_ERROR      => 'Core Error',
            E_CORE_WARNING    => 'Core warning',
            E_COMPILE_ERROR   => 'Compile Error',
            E_COMPILE_WARNING => 'Compile Warning',
            E_USER_ERROR      => 'Developer Issued Error',
            E_USER_WARNING    => 'Developer Issued Warning',
            E_USER_NOTICE     => 'Developer Issued Notice'
         );

      if (!(error_reporting() & $errno)) {
         return;
      }

      $error_index = $errno;

      if (!isset(static::$errors[$error_index]))
         static::$errors[$error_index] = array();

      $backtrace = array_slice(debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS),1,20);

      array_shift($backtrace);

      # Throw away same backtrace as $errfile and $errline
      while (
            isset($backtrace[0]['file'])
            && $backtrace[0]['file'] == $errfile
            && $backtrace[0]['line'] == $errline
         ) {
         array_shift($backtrace);
      }

      $error = array(
            'type' => isset($error_code_readable_strings [$errno]) ? $error_code_readable_strings [$errno] : $errno,
            'errno'      => $errno,
            'message' => $errstr,
            'file'    => $errfile,
            'line'       => $errline,
            'backtrace'  => $backtrace,
         );

      $recorded_already = false;
      foreach (static::$errors[$error_index] as $error_number => $recorded_error) {
         unset($recorded_error['num_occured']);
         if ($recorded_error === $error) {
            static::$errors[$error_index][$error_number]++;
            $recorded_already = true;
            break;
         }
      }

      if ( !$recorded_already ) {
         $error['num_occured']            = 1;
         static::$errors[$error_index][]  = $error;
      }

   }

   /**
    * Print errors
    */
   static function print_errors() {
      static $error_code_strings = array(
            E_ERROR           => 'E_ERROR',
            E_WARNING         => 'E_WARNING',
            E_PARSE           => 'E_PARSE',
            E_NOTICE          => 'E_NOTICE',
            E_STRICT          => 'E_STRICT',
            E_DEPRECATED      => 'E_DEPRECATED',
            E_CORE_ERROR      => 'E_CORE_ERROR',
            E_CORE_WARNING    => 'E_CORE_WARNING',
            E_COMPILE_ERROR   => 'E_COMPILE_ERROR',
            E_COMPILE_WARNING => 'E_COMPILE_WARNING',
            E_USER_ERROR      => 'E_USER_ERROR',
            E_USER_WARNING    => 'E_USER_WARNING',
            E_USER_NOTICE     => 'E_USER_NOTICE'
         );

      $default_error_tpl = "errors/e_default.tpl";
      $smarty = new add_smarty();
      foreach (static::$errors as $error_index => $errors) {

         $error_string_index = isset($error_code_strings[$error_index]) ? $error_code_strings[$error_index] : $error_index;

         $error_tpl = "errors/".strtolower($error_string_index).".tpl";
         if (!$smarty->templateExists($error_tpl)) {
            $error_tpl = $default_error_tpl;
         }

         foreach ($errors as $error) {

            # The chunk of code on the location of the error
            if (!add::is_live()) {
               $code_on_error = "";
               $file_codes = preg_split('/<br\s*\/>/',highlight_file($error['file'],true));

               $code_on_error_padding = 6;

               $code_on_error_start = max($error['line'] - $code_on_error_padding,1);

               $smarty->assign('code_on_error_start', $code_on_error_start);
               $code_on_error_end = min($error['line'] + $code_on_error_padding,count($file_codes));

               for ($code_on_error_x = $code_on_error_start; $code_on_error_x <= $code_on_error_end; $code_on_error_x++) {
                  $code_on_error .= $file_codes[$code_on_error_x-1]."<br />";
               }

               preg_match('/^(\&nbsp\;)+?/m',$code_on_error,$code_white_space);
               $code_on_error = preg_replace('/^'.preg_quote($code_white_space[0],'/').'/','',$code_on_error);

               $smarty->assign('code_on_error',$code_on_error);
               $smarty->assign('code_on_error_end',$code_on_error_end);

            }

            $error['filepathname'] = $error['file'];
            $error['file'] = basename($error['file']);

            $compiled_tpl_preg = '/[\da-f]{40}\.file\.(.*\.tpl)\.php/';

            if (preg_match($compiled_tpl_preg,$error['file'])) {
               $error['file'] = "*".preg_replace($compiled_tpl_preg,'$1',$error['file']);
            }

            $error['file_lines'] = array();
            foreach ($error['backtrace'] as $backtrace_data) {
               $error['file_lines'][] = array( 'file' => basename($backtrace_data['file']), 'line' => $backtrace_data['line'], 'filepathname' => $backtrace_data['file'] );
            }


            if ($smarty->templateExists($error_tpl)) {
               $smarty->assign("error",$error);
               $smarty->display($error_tpl);
            }
            else {
               echo "<div>$error[type] : $error[file]:$error[line] : <b>$error[message]</b></div>";
            }
         }

      }
   }

   /**
    * Handle shutdown
    * When development, record the time spent on script execution (since 0.7.2)
    *
    * @since ADD MVC 0.5.1
    */
   static function handle_shutdown() {
      try {
         static::ob_flush();
         if (static::$handle_shutdown && !add::is_live() && add::is_developer()) {
            global $add_mvc_root_timer;

            $smarty = new add_smarty();
            $smarty->display('debug/handle_shutdown.tpl');

            if (isset($add_mvc_root_timer) && $add_mvc_root_timer instanceof add_debug_timer) {

               add_debug::print_config('environment_status');
               add_debug::print_config('add_dir');
               add_debug::print_config('path');
               add_debug::print_config('developer_ips',true);
               add_debug::print_data('current_user_ip',current_user_ip());
               add_debug::print_data('POST variable', $_POST);
               add_debug::print_data('GET variable', $_GET);
               add_debug::print_data('COOKIE variable', $_COOKIE);
               add_debug::print_data('REQUEST variable', $_COOKIE);
               if (isset($_SESSION)) {
                  add_debug::print_data('SESSION variable', $_SESSION);
               }

               $add_mvc_root_timer->lap("Shutdown");
               $add_mvc_root_timer->print_all_laps();
            }

            return static::print_errors();
         }
      }
      catch (Exception $e) {
         add::$handle_shutdown = false;
         add::handle_exception($e);
      }
   }

   /**
    * Flush unflushed buffers, if the debug class isn't done var dumping, then void
    *
    */
   public static function ob_flush() {
      if (!add_debug::dumping()) {
         while (ob_get_level()) {
            echo ob_get_clean();
         }
      }
      else {
         $void_dump = ob_get_clean();
      }
   }

   /**
    * Custom die() function
    *
    * @param boolean $handle_shutdown weather to print the shutdown handle or not
    *
    * @since ADD MVC 0.7.2
    */
   static function shutdown($handle_shutdown = true) {
      self::$handle_shutdown = $handle_shutdown;
      die();
   }

   /**
    * include() include file path
    * example add::include_include_file('functions/date.functions.php')
    *
    * @param string $include_path the include path
    * @param boolean $include_once weather to include it only once
    *
    * @since ADD MVC 1.0
    */
   protected static function include_include_file($include_path, $include_once = true) {
      $filepath = static::include_filepath($include_path);

      /*
      if (!add::is_live()) {
         static::php_check_syntax($filepath);
      }
      */

      if ($include_once) {
         return include_once($filepath);
      }
      else {
         return include($filepath);
      }
   }

   /**
    * Check php syntax(this still needs to be tested and fixed)
    *
    * @param string $filepath to the file
    *
    * @since ADD MVC 0.7
    */
   public static function php_check_syntax($filepath) {
      /*
      $cmd_line = 'php -n -l '.escapeshellarg($filepath);
      $output = shell_exec($cmd_line);

      if ($output) {
         if (preg_match('/^PHP Parse error/',$output)) {
            throw new e_syntax($output);
         }
      }
      */
   }


   /**
    * load_functions($functions_group_name)
    *
    * @param string $functions_group_name the base name of the function file to include
    *
    * example: add::load_functions('date');
    * @since ADD MVC 0.1
    */
   static function load_functions($functions_group_name) {
      return self::include_include_file("functions/$functions_group_name.functions.php");
   }

   /**
    * Load library from $C config
    * @param string $lib_name the $C->libs index
    * @since ADD MVC 0.1
    */
   static function load_lib($lib_name) {
      static $loaded_libs = array();

      if (in_array($lib_name,$loaded_libs)) {
         return false;
      }

      require static::lib_init_path($lib_name);

      $loaded_libs[] = $lib_name;

   }

   /**
    * library init path from the config
    * @param string $lib_name the $C->libs index
    * @since ADD MVC 0.8.0
    */
   static function lib_init_path($lib_name) {

      $lib = add::config()->libs->$lib_name;

      if (!$lib)
         throw new e_developer("Library $lib_name not found");


      # Bug fix - if the lib is not casted to an object
      if (is_array($lib)) {
         $lib = (object) $lib;
      }

      if (is_string($lib)) {
         $lib_path = $lib;
      }
      else if (is_object($lib) && isset($lib->init_path)) {
         $lib_path = $lib->init_path;
      }
      else {
         throw new e_developer("Invalid config format for $lib_name", $lib);
      }
      if ($lib_path[0] === '/')
         return $lib_path;
      else
         return static::include_filepath('libs/'.$lib_path);
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
            if (file_exists(self::current_view_filepath())) {
               $current_controller = new ctrl_default_page();
            }
            else if (
                  ( $class_name = rtrim($class_name,'_') )
                  && class_exists($class_name)
               ) {
               $class_name::redirect();
            }
            else {
               $current_controller = new ctrl_page_404();
            }
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
         $current_controller_basename = self::current_controller_basename();
         $current_controller_class    =  $current_controller_basename ? 'ctrl_page_'.$current_controller_basename : false;;
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

         $relative_path =
               isset($_GET['add_mvc_path'])
               ? preg_replace('/^'.preg_quote(add::config()->path,'/').'/','',$_GET['add_mvc_path'])
               : preg_replace('/^.*\/(.+?)(\?.*)?$/','$1',$_SERVER['REQUEST_URI']);

         $current_controller_basename = $relative_path;
         $current_controller_basename = preg_replace('/\-+/','_',$current_controller_basename);
         $current_controller_basename = preg_replace('/\.php$/','',$current_controller_basename);
         $current_controller_basename = preg_replace('/\//','__',$current_controller_basename);

         if (preg_match('/\W+/',$current_controller_basename)) {
            $current_controller_basename = false;
            return $current_controller_basename;
         }

         if (!$current_controller_basename)
            $current_controller_basename = self::$HOME_CONTROLLER;
      }

      return $current_controller_basename;
   }

   /**
    * The view filepath of the  current request
    *
    */
   public static function current_view_filepath() {
      static $cache;
      if (!isset($cache)) {
         $basename = self::current_controller_basename();
         if (is_array(self::config()->views_dir)) {
            foreach (self::config()->views_dir as $views_dir) {
               $view_filepath = "$views_dir/pages/$basename.tpl";
               if (file_exists($view_filepath)) {
                  $cache = $view_filepath;
                  break;
               }
            }

            if (!isset($cache)) {
               $cache = false;
            }

         }
         else {
            $cache = self::config()->views_dir.'/pages/'.self::current_controller_basename().'.tpl';
         }
      }
      return $cache;
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
    * @todo do not die when failed to redirect
    */
   static function redirect($url) {
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
      if ($merge_current) {
         $get = $_GET;
         unset($get['add_mvc_path']);
         $query = array_merge($get,$new_query);
      }
      else
         $query = $new_query;
      return add::redirect("?".http_build_query($query));
   }


   /**
    * Get the $_GET without the additional add-mvc $_GET parameters like add_mvc_path
    *
    * @since add 0.6.1
    */
   static function gpc_get() {
      $get = $_GET;
      unset($get['add_mvc_path']);
      return $get;
   }

   /**
    * Sets and return or return an environment status
    *
    * @param string $new_status either live or development
    *
    * @since ADD MVC 0.8
    */
   public static function environment_status($new_status = null) {

      /**
       *
       * @see http://code.google.com/p/add-mvc-framework/issues/detail?id=33
       */
      if (!add::is_developer()) {
         if (
               add::$environment_status != 'live'
               ||
               (isset($new_status) && $new_status != 'live')
            ) {
            $new_status = 'live';
         }
      }

      if ($new_status) {

         if (is_string($new_status)) {

            if ($new_status === 'development') {
               if (add::is_developer()) {
                  add::$environment_status = $new_status;
               }
               else {
                  add::$environment_status = "live";
               }
            }
            else if ($new_status === 'live') {
               add::$environment_status = 'live';
            }
            else {
               throw new e_developer("Invalid environment_status: $new_status");
            }
         }
         else if ($new_status !== true) {
            throw new e_developer("Invalid new environment status",$new_status);
         }

         /**
          * No errors if live
          *
          * @since ADD MVC 0.7.2
          */
         if (add::is_live()) {
            error_reporting(0);
            ini_set('display_errors',0);
            add::$handle_shutdown = false;
         }
         # TODO: Figure out why we have else instead of else if
         else {
            error_reporting(E_ALL);
            ini_set('display_errors',1);

            /**
             * When development, record the time spent on script execution
             *
             * @since ADD MVC 0.7.2
             */
            if (add::is_development()) {
               add::$handle_shutdown          = !isset(add::config()->handle_shutdown) || add::config()->handle_shutdown == true;

               if (!isset($GLOBALS['add_mvc_root_timer'])) {
                  $GLOBALS['add_mvc_root_timer'] = add_development_timer::start("Framework Configuration");
               }

               add::config()->root_timer      = $GLOBALS['add_mvc_root_timer'];

            }
         }
      }

      return add::$environment_status;
   }

   /**
    * environment check: is live?
    *
    * @since ADD MVC 0.7
    */
   public static function is_live() {
      return add::$environment_status === 'live';
   }


   /**
    * environment check: is_development()
    *
    * @since ADD MVC 0.7
    */
   public static function is_development() {
      return add::$environment_status === 'development';
   }


   /**
    * is_developer()
    *
    * Checks if the user is developer according to his/her IP
    *
    * @since ADD MVC 0.7.2
    */
   public static function is_developer() {

      /**
       * @see http://code.google.com/p/add-mvc-framework/issues/detail?id=39
       */
      if (php_sapi_name() == "cli") {
         return true;
      }

      # Fix for issue #6
      if (current_ip_in_network())
         return true;

      if (isset(add::config()->developer_ips))
         return in_array(current_user_ip(), (array) add::config()->developer_ips);
      else
         return false;
   }


   /**
    * content_type()
    *
    * @param string $new_content_type
    *
    * @since ADD MVC 0.10.5
    */
   public static function content_type($new_content_type = null) {
      if ($new_content_type) {
         ini_set('html_errors',$new_content_type != 'text/plain');
         static::$content_type = $new_content_type;
         header("Content-type: ".static::$content_type);
      }
      return static::$content_type;
   }
}