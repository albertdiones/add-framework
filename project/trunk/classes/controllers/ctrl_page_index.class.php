<?php
/**
 * default index page
 * A sample index page controller
 * replace this with your own on your application's classes directory
 *
 * @package ADD MVC\Controllers\Extras
 * @since ADD MVC 0.0
 * @version 0.0
 */
CLASS ctrl_page_index EXTENDS ctrl_tpl_page {

   /**
    * The pre-view process
    *
    * @since ADD MVC 0.0
    */
   public function process() {

      # Required Directories
      self::require_dir_exists(add::config()->incs_dir);
      self::require_dir_exists(add::config()->caches_dir,true);
      $this->require_dir($this->view()->compile_dir,true);
      $this->require_dir($this->view()->cache_dir,true);

      # Other essential directories
      $this->check_dir_exists(add::config()->classes_dir);
      $this->check_dir_exists(add::config()->assets_dir);
      $this->check_dir_exists(add::config()->css_dir);
      $this->check_dir_exists(add::config()->js_dir);
   }

   /**
    * Require and attempt to create the directory
    *
    * @param string $dir
    * @param boolean $writable weather to require it being writeable or not
    *
    * @since ADD MVC 0.5
    */
   public function require_dir($dir,$writable) {

      $this->view()->assign('log',array('warnings'=>array(),'dirs'=>array()));

      if (!file_exists($dir)) {
         $this->view()->append('log.dirs',$dir);
         if ($writable)
            mkdir($dir,0777);
         else
            mkdir($dir);
      }
      return self::require_dir_exists($dir,$writable);
   }

   /**
    * Require existence of a directory
    *
    * @param string $dir
    * @param boolean $writable weather to require it being writeable or not
    *
    * @since ADD MVC 0.5
    */
   public static function require_dir_exists($dir,$writable=false) {
      if (!file_exists($dir)) {
         throw new e_system("Directory $dir is not existing",add::config());
      }
      if ($writable && !is_writable($dir)) {
         throw new e_system("Directory $dir is not writable",add::config());
      }
   }

   /**
    * Checks if a directory exists
    *
    * @param string $dir
    *
    */
   public function check_dir_exists($dir) {
      if (!file_exists($dir)) {
         $this->view()->append('log.warnings',"Directory $dir is not existing");
      }
   }


}