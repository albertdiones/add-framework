<?php
ABSTRACT CLASS ldap_member EXTENDS session_user IMPLEMENTS i_auth_entity {

   private static $ldap;

   abstract public static function host();

   abstract public static function login_href();

   abstract public static function user_member_of($user, $group);

   public static function session_key() {
      return "ldap_".get_called_class();
   }

   public static function term_username() {
      return "username";
   }

   public static function term_password() {
      return "password";
   }




   /**
    * name function
    * readable name of the entity
    * @since ADD MVC 0.0
    */
   public function name() {
      return $this->username;
   }

   /**
    * login function
    * login using $username and $password, and optional addinational $options
    * @author albertdiones@gmail.com
    *
    * @since ADD MVC 0.0
    */
   public static function login(/* Polymorphic */) {
      $args = func_get_args();
      $username = $args[0];
      $password = $args[1];
      $group    = $args[2];


      if (!$username) {
         throw new e_user_input("Input your ".$this->term_username());
      }

      if (!$password) {
         throw new e_user_input("Input your ".$this->term_password());
      }

      $ldap_result = static::ldap()->bind($username,  $password);

      if (!$ldap_result) {
         //debug::var_dump(static::TABLE,static::get_value_index_field($username),$username,get_called_class());
         throw new e_user_input("Failed to login: Invalid ".self::term_username()." and ".self::term_password());
      }

      if ($group) {
         # You have to declare ::validate_group_membership() on the late static class
         $late_static_class = get_called_class();
         e_developer::assert(method_exists($late_static_class,'validate_group_membership'),$late_static_class.'::validate_group_membership() function is not declared');
         if (!static::validate_group_membership($username,$group)) {
            throw new e_user_input("Failed to login: invalid group");
         }
      }

      $member = static::singleton();

      $member -> username = $username;

      $member -> group = $group;

      return $member;

   }

   public static function ldap() {
      if (!isset(self::$ldap)) {

         self::$ldap = new self($this->host);

      }
      return self::$ldap;
   }

   static function login_redirect() {
      return add::redirect(static::login_href());
   }
}