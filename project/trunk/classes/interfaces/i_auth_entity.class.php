<?php
/**
 * i_auth_entity interface
 * an interface for authenticated entities
 *
 * @package ADD MVC\Extras\Interfaces
 * @version 0.0
 * @since ADD MVC 0.0
 * @author albertdiones@gmail.com
 */
INTERFACE i_auth_entity {

/**
 * name function
 * readable name of the entity
 * @author albertdiones@gmail.com
 */
   public function name();

/**
 * login function
 * login using $username and $password, and optional addinational $options
 * @author albertdiones@gmail.com
 */
   static function login(/* Polymorphic */);

/**
 * logout function
 * Logs out the currently logged in entity
 * @author albertdiones@gmail.com
 */
   static function logout();

/**
 * Currently logged in user
 * Fetches the object instance of the currently logged in user
 * @author albertdiones@gmail.com
 */
   static function current_logged_in();

/**
 * requireLoggedIn()
 * requires currentLoggedIn() user, if there is none, redirect and die()
 *
 * @param i_auth_entity $user require the current logged in to be $user
 *
 * @author albertdiones@gmail.com
 */
   static function require_logged_in(i_auth_entity $user);
}