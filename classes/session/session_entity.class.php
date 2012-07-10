<?php

/**
 * session_entity
 *
 * @since ADD MVC 0.6
 */
CLASS session_entity EXTENDS array_entity {

   public $data;

   /**
    * Referrence to $data
    *
    *
    * @deprecated use $this->data instead
    * @since ADD MVC 0.6
    */
   public $session_var;


   protected function __construct(&$session_var) {
      parent::construct($session_var);
      $this->data = &$session_var;
      $this->session_var = &$this->data;
   }


   public function __set($varname,$value) {
      $set = (bool)(parent::__set($varname,$value));
      $updated = (bool) ($this->data[$varname] = $value);
      return $set && $updated;
   }
}