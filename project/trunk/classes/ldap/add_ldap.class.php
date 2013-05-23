<?php
/**
 * LDAP Wrapper class
 *
 * @package ADD MVC\LDAP
 *
 * A class for wrapping ldap functions
 *
 */
CLASS add_ldap {

/**
 * DS cache
 *
 */
   public $ds;

   /**
    * Search result
    *
    */
   public $search;

   /**
    * connect
    *
    * @param string $host
    *
    */
   public function __construct($host) {
      $this->ds = ldap_connect($host);
   }

   /**
    * login, wrapper for ldap_bind
    *
    * @param string $user sAMAccountName
    * @param string password
    *
    */
   public function bind($user, $password) {

      $r = @ldap_bind($this->ds,$user,$password);

      if ($r) {
         return true;
      }
      else {
         return false;
      }
   }

   /**
    * Close connection
    *
    */
   public function close() {
      return ldap_close($this->ds);
   }

   /**
    * ldap_search() wrapper
    *
    * @param string $dn The base DN for the directory.
    * @param string $filter The search filter can be simple or advanced, using boolean operators in the format described in the LDAP documentation (see the » Netscape Directory SDK for full information on filters).
    * @param array $attr An array of the required attributes, e.g. array("mail", "sn", "cn"). Note that the "dn" is always returned irrespective of which attributes types are requested.
    * @param int $attrs Should be set to 1 if only attribute types are wanted. If set to 0 both attributes types and attribute values are fetched which is the default behaviour.
    * @param int $sizelimit Enables you to limit the count of entries fetched. Setting this to 0 means no limit.
    * @param int $timelimit Sets the number of seconds how long is spend on the search. Setting this to 0 means no limit.
    * @param int $deref Specifies how aliases should be handled during the search
    *
    * @see http://www.php.net/manual/en/function.ldap-search.php
    *
    *
    */
   public function search($dn,$filter,$attr = array(),$attrs = 0,$sizelimit = 0,$timelimit = 0,$deref = 0) {
      return $this->search = ldap_search($this->ds,$dn,$filter,$attr,$attrs,$sizelimit,$timelimit,$deref);
   }

   /**
    * Get entries from the search result
    *
    * wrapper of ldap_get_entries()
    *
    */
   public function get_entries() {
      if (isset($this->search))
         return ldap_get_entries($this->ds, $this->search);
      else
         return 0;
   }

   /**
    * Count the result from the search result
    *
    */
   public function count_entries() {
      if (isset($this->search))
         return ldap_count_entries($this->ds, $this->search);
      else
         return 0;
   }

   /**
    * Escape the string for searching
    *
    * @param string $str
    * @param boolean $for_dn weather to escape the string for dn compatibility
    *
    */
   public static function escape($str, $for_dn = true) {
      if  ($for_dn)
         $metaChars = array(',','=', '+', '<','>',';', '\\', '"', '#');
      else
         $metaChars = array('*', '(', ')', '\\', chr(0));

      $quotedMetaChars = array();

      foreach ($metaChars as $key => $value) {
         $quotedMetaChars[$key] = '\\'.str_pad(dechex(ord($value)), 2, '0');
      }

      $str=str_replace($metaChars,$quotedMetaChars,$str); //replace them

      return ($str);
   }
}