<?php
CLASS add_ldap {
   public $ds;

   public $search;

   public function __construct($host) {
      $this->ds = ldap_connect($host);
   }

   public function bind($user, $password) {

      $r = @ldap_bind($this->ds,$user,$password);

      if ($r) {
         return true;
      }
      else {
         return false;
      }
   }

   public function close() {
      return ldap_close($this->ds);
   }

   public function search($dn,$filter,$attr = array(),$attrs = 0,$sizelimit = 0,$timelimit = 0,$deref = 0) {
      return $this->search = ldap_search($this->ds,$dn,$filter,$attr,$aattrs,$sizelimit,$timelimit,$deref);
   }

   public function get_entries() {
      if (isset($this->search))
         return ldap_get_entries($this->ds, $this->search);
      else
         return 0;
   }

   public function count_entries() {
      if (isset($this->search))
         return ldap_count_entries($this->ds, $this->search);
      else
         return 0;
   }

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