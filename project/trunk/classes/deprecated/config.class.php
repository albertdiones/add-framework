<?php
/**
 * Config singleton class
 * @package ADD MVC
 * @author albertdiones@gmail.com
 * @deprecated see add::config()
 * @since 0.0
 */
CLASS config IMPLEMENTS i_non_overwritable {
   const VARNAME = 'C';
   const DESTRUCT_ECHO = '<!-- config destruct -->';

   private static $singleton;
   private $data;

   private function __construct(STDClass $C) {
      $this->data = $C;
   }

   public function __clone() {
      throw new e_developer("Cloning for ".get_called_class()." is not allowed");
   }

   function __get($varname) {
      if (isset($this->data->$varname))
         return $this->data->$varname;
      else
         trigger_error("Access to undefined config variable $varname from ".debug::function_caller());
   }

   function __set($varname,$value) {
      $this->set($varname,$value,false);
   }


   function __destruct() {
      #die(self::DESTRUCT_ECHO);
   }

   function set($varname,$value,$force=true) {
      if (!$force && isset($this->data->$varname)) {
         throw new logic_exception("Attempt to redeclared config->$varname");
      }
      $this->data->$varname = $value;
   }

   static function singleton() {
      return add::config();
   }

   static function load_lib($lib_name) {
      return add::load_lib($lib_name);

   }

   static function include_domain_config() {
      if (self::domain_config_exists()) {
         include(self::domain_config_pathname());
      }
   }

   static function domain_config_exists() {
      return file_exists(self::domain_config_pathname());
   }

   static function domain_config_pathname() {
      $C = self::singleton();
      return "$C->configs_dir/domains/$_SERVER[HTTP_HOST].cfg.php";
   }

   static function is_live_project() {
      return self::singleton()->project_status === 'live';
   }

   static function is_debugging_project() {
      return self::singleton()->project_status === 'debugging';
   }

}