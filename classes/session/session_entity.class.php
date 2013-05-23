<?php

/**
 * session_entity
 *
 * @package ADD MVC\Session
 *
 * @since ADD MVC 0.6
 */
CLASS session_entity EXTENDS array_entity {

   /**
    * Session variable placeholder
    *
    */
   public $data;

   /**
    * Referrence to $data
    *
    *
    * @deprecated use $this->data instead
    * @since ADD MVC 0.6
    */
   public $session_var;

   /**
    * Do not inherit the __construct of array_entity since it is faulty ( see issue #83 )
    *
    * @param array $session_var
    *
    * @see https://code.google.com/p/add-mvc-framework/issues/detail?id=83
    *
    */
   protected function __construct(&$session_var) {
      #parent::__construct($session_var);
      $this->data = &$session_var;
      $this->session_var = &$this->data;
   }

   /**
    * Sets the session variable
    *
    */
   public function __set($varname,$value) {
      $set = (bool)(parent::__set($varname,$value));
      $updated = (bool) ($this->data[$varname] = $value);
      return $set && $updated;
   }
}