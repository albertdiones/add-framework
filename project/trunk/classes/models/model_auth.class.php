<?php
/**
 * model_auth object class
 * An abstract class for authenticated models
 * example: members, admins
 * @author albertdiones@gmail.com
 *
 * @package ADD MVC\Models\Extras
 * @since ADD MVC 0.0
 * @version 0.1.1
 */
ABSTRACT CLASS model_auth EXTENDS model_rwd IMPLEMENTS i_auth_entity {

   /**
    * the username database table field
    * @since ADD MVC 0.0
    */
   const USERNAME_FIELD = 'email';

   /**
    * the password database table field
    * @since ADD MVC 0.0
    */
   const PASSWORD_FIELD = 'sha1_password';

   /**
    * the login page to redirect to
    * @since ADD MVC 0.0
    */
   const LOGIN_PAGE = 'login';

   /**
    * The terminology for the username
    * @since ADD MVC 0.0
    */
   const TERM_USERNAME = 'email';

   /**
    * The terminology for the password
    * @since ADD MVC 0.0
    */
   const TERM_PASSWORD = 'password';

/**
 * Required variables

   const SESSION_KEY;
   const NAME_FIELD;

   static $type_index_fields = array(
         'int'    => self::TABLE_PK,
         'string' => static::USERNAME_FIELD
      );

*/

   /**
    * name function
    * readable name of the entity
    * @since ADD MVC 0.0
    */
   public function name() {
      return $this->{static::NAME_FIELD};
   }

   /**
    * login function
    * login using $username and $password, and optional addinational $options
    * @author albertdiones@gmail.com
    *
    * @since ADD MVC 0.0
    */
   static function login(/* Polymorphic */) {
      $args = func_get_args();
      $username = $args[0];
      $password = $args[1];


      if (!$username) {
         throw new e_user_input("Input your ".static::TERM_USERNAME);
      }

      if (!$password) {
         throw new e_user_input("Input your ".static::TERM_PASSWORD);
      }

      $instance = static::get_one_where(array( static::USERNAME_FIELD => $username ));

      if (!$instance) {
         //debug::var_dump(static::TABLE,static::get_value_index_field($username),$username,get_called_class());
         throw new e_user_input("Invalid ".static::TERM_USERNAME." $username");
      }

      if (static::password_check($instance,$password)) {
         $_SESSION[static::SESSION_KEY] = $instance->id();
         return $instance;
      }
      else {
         throw new e_user_input("Invalid ".static::TERM_PASSWORD);
      }
   }

   /**
    * Checks if the password is correct
    * @param mixed $arg1 either the username or the member
    * @param string $password the password to check
    * @return boolean true for correct password or false for wrong password
    *
    * @since ADD MVC 0.0
    * @version 0.1
    */
   static function password_check($arg1,$password) {

      if (is_string($arg1)) {
         $username = $arg1;
         $instance = static::get_instance($username);
      }
      else if ($arg1 instanceof self) {
         $instance = $arg1;
      }

      if (!$instance)
         return false;

      if ($instance->{static::PASSWORD_FIELD} == static::hash_password($password)) {
         return true;
      }
      else {
         return false;
      }
   }

   /**
    * logout function
    * Logs out the currently logged in entity
    * @author albertdiones@gmail.com
    *
    * @since ADD MVC 0.0
    */
   static function logout() {
      unset($_SESSION[static::SESSION_KEY]);
   }

   /**
    * Currently logged in user
    * Fetches the object instance of the currently logged in user
    * @since ADD MVC 0.0
    */
   static function current_logged_in() {
      return self::get_instance($_SESSION[static::SESSION_KEY]);
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
      if (!self::current_logged_in()) {
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

   /**
    * Hash the password
    *
    * @since ADD MVC 0.1
    */
   static function hash_password($password) {
      return sha1($password);
   }


   /**
    * set_password
    * @param string $password
    * @param string $confirm_password
    * @since ADD MVC 0.3
    */
   public function set_password($password,$confirm_password = false) {

      e_developer(is_string($password),"Password argument is not string",func_get_args());

      if ($confirm_password !== false && $password === $confirm_password) {
         throw new e_user("Password confirmation incorrect");
      }

      $this->{PASSWORD_FIELD} = $this->hash_password($password);

   }

}