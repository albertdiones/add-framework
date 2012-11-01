<?php
/**
 * Config singleton class
 * @package ADD MVC
 * @author albertdiones@gmail.com
 * @since 0.10
 */
CLASS add_config EXTENDS STDCLass {
   protected $environment_status;

   /**
    * Create config from array $C
    *
    * @param array $C
    *
    * @since since
    */
   public function construct($config_array) {
      foreach ($config_array as $config_field => $config_value) {
         $this->$config_field = $config_value;
      }
   }
}