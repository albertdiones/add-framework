<?php
/**
 * Common functions file
 * @author albertdiones@gmail.com
 * @package ADD MVC\Functions
 * @deprecated
 * @todo move sitewide functions to add class
 *
 * @since ADD MVC 0.0
 */


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
 * @version 1.0
 */
function handle_error($errno , $errstr , $errfile = NULL, $errline = NULL , $errcontext = NULL) {
   trigger_error( "Deprecated handle_error function", E_USER_DEPRECATED );
   return add::handle_error( $errno , $errstr , $errfile, $errline, $errcontext );
}
/**
 * Print errors
 *
 */
function print_errors() {
   add::print_errors();
}
/**
 * Handle shutdown
 */
function handle_shutdown() {
   add::handle_shutdown();
}

/**
 * superglobal2globals($array,$keys)
 * Copies $array items with keys $keys, and quoting it if possible
 * @param array $array the superglobal array
 * @deprecated due to incompatibility with ADD MVC
 * @param array $keys the array of string KEYS of the super global array that will be copied to globals
 */
function superglobal2globals($array,$keys) {
   global $MYSQL_D;
   foreach ($keys as $key) {
      if (is_array($key)) {
         superglobal2globals($array,$key);
      }
      else {
         $GLOBALS[$key] = isset($array[$key]) ? $array[$key] : NULL;
         if ($MYSQL_D)
            $GLOBALS["Q_$key"] = recursive_quote($GLOBALS[$key],array($MYSQL_D,'quote'));
      }

   }
}

/**
 * Recursively quote $arg with $callback function
 * @param mixed $arg the variable to quote
 * @param callback $callback the callback
 * @deprecated due to incompatibility with ADD MVC
 * @todo research if this is just equal to array_walk()
 */
function recursive_quote($arg,$callback) {
   if (is_null($arg))
      $arg = '';
   if (is_scalar($arg)) {
      if (is_callable($callback))
         return call_user_func($callback,$arg);
      else
         trigger_error("Invalid callback");
   }
   foreach ($arg as &$value) {
      $value = recursive_quote($value,$callback);
   }
   return $arg;
}

/**
 * Copies the request variable to global
 * param string $arg1 [$arg2,$argn...] the string key to the request to copy to globals
 * @uses superglobal2globals()
 * @deprecated due to incompatibility with ADD MVC
 */
function r2globals(/* args */) {
   return superglobal2globals($_REQUEST,func_get_args());
}

/**
 * Copies the get variable to global
 * param string $arg1 [$arg2,$argn...] the string key to the get to copy to globals
 * @uses superglobal2globals()
 * @deprecated due to incompatibility with ADD MVC
 */
function g2globals(/* args */) {
   return superglobal2globals($_GET,func_get_args());
}

/**
 * Copies the post variable to global
 * param string $arg1 [$arg2,$argn...] the string key to the post to copy to globals
 * @uses superglobal2globals()
 * @deprecated due to incompatibility with ADD MVC
 */
function p2globals(/* args */) {
   return superglobal2globals($_POST,func_get_args());
}

/**
 * Copies the cookie variable to global
 * param string $arg1 [$arg2,$argn...] the string key to the cookie to copy to globals
 * @uses superglobal2globals()
 * @deprecated due to incompatibility with ADD MVC
 */
function c2globals(/* args */) {
   return superglobal2globals($_COOKIE,func_get_args());
}

/**
 * Copies the session variable to global
 * param string $arg1 [$arg2,$argn...] the string key to the session to copy to globals
 * @uses superglobal2globals()
 * @deprecated due to incompatibility with ADD MVC
 */
function s2globals(/* args */) {
   return superglobal2globals($_SESSION,func_get_args());
}

/**
 * Redirect function
 * die() included
 * @param string $url the href to redirect into
 * todo do not die when failed to rediredct
 *
 * @deprecated see add::redirect()
 */
function redirect($url) {
   add::redirect($url);
}

/**
 * void redirect_query(array $new_query,bool $merge_current=true)
 * redirects to the new query string, die()s in the process
 * @param array $new_query
 * @param boolean $merge_current true to include current $_GET with $new_query
 * @todo support string on $new_query
 * @uses redirect()
 *
 * @deprecated see add::redirect_query()
 */
function redirect_query($new_query,$merge_current=true) {
   add::redirect_query($new_query, $merge_current);
}


/**
 * salt_string($string,$salt = DEFAULT_SALT)
 * Salt a string $salt
 * @param string $string the string to salt
 * @param string $salt the secret salt
 */
function salt_string( $string, $salt = DEFAULT_SALT) {
   if (!defined('DEFAULT_SALT'))
      throw new Exception("DEFAULT_SALT is not defined");
   return sha1($string.$salt);
}

add::load_functions('network');
