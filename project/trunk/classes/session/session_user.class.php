<?php

/**
 * loginless user
 *
 * @since ADD MVC 0.6
 */
ABSTRACT CLASS session_user EXTENDS session_entity {
   static $singleton = array();
   const SESSION_KEY = 'session_user';

   /**
    * session_user singleton
    *
    * @since
    */
   public static function singleton() {
      if (!isset(static::$singleton[get_called_class()] )) {
         if (!isset($_SESSION[static::session_key()]))
            $_SESSION[static::session_key()] = array();
         $session_var = &$_SESSION[static::session_key()];

         static::$singleton[get_called_class()] = new static($session_var);
      }
      return static::$singleton[get_called_class()];
   }
/**
 * t_user trait class
 *
 */
   /**
    * The readable name of the user
    *
    * @since ADD MVC 0.6
    * @uses static::USERNAME_FIELD
    */
   public function name() {
      return $this->{static::NAME_FIELD};
   }

   public static function current_logged_in() {
      if (empty($_SESSION[static::session_key()]))
         return false;
      else
         return static::singleton();
   }

   public static function logout() {
      unset($_SESSION[static::session_key()]);
   }


   /**
    * require_logged_in()
    * requires current_logged_in() user, if there is none, redirect and die()
    *
    * @param i_auth_entity $user require the current logged in to be $user
    *
    * @since ADD MVC 0.0
    */
   static function require_logged_in(i_auth_entity $user=NULL) {
      if (!static::current_logged_in()) {
         static::login_redirect();
      }
      return true;
   }

   /**
    * login_redirect()
    * extend to change
    * @since ADD MVC 0.0
    */
   static function login_redirect() {
      redirect(static::LOGIN_PAGE."?redirect=".urlencode($_SERVER['REQUEST_URI']));
   }


   static function session_key() {
      return static::SESSION_KEY;
   }



}