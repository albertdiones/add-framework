<?php
/**
 * Smarty view extension
 *
 * @author albertdiones@gmail.com
 *
 * @package ADD MVC\Extras
 * @since ADD MVC 0.0
 * @version 0.0
 *
 * @todo make the config variables changeable outside framework
 */
CLASS add_smarty EXTENDS Smarty {
   const VARNAME = 'T';

   /**
    * Smarty construct
    *
    * @since ADD MVC 0.0
    */
   public function __construct() {
      global $C;

      parent::__construct();

      $this -> setTemplateDir(array($C->views_dir,$C->add_dir.'/views/'));

      $this -> compile_dir  = $C->caches_dir.'/smarty_compile/';
      $this -> config_dir   = $C->configs_dir.'/smarty/';
      $this -> cache_dir    = $C->caches_dir.'/smarty_cache/';

   }

    /**
     * displays a Smarty template
     *
     * @param string $template   the resource handle of the template file or template object
     * @param mixed  $cache_id   cache id to be used with this template
     * @param mixed  $compile_id compile id to be used with this template
     * @param object $parent     next higher level of Smarty variables
     */
   public function display($template = null, $cache_id = null, $compile_id = null, $parent = null) {
      if (!$this->templateExists($template))
         throw new Exception("Template ($template) not found. (Template Dir: ".(is_array($this->template_dir) ? implode(",",$this->template_dir) : "$this->template_dir").")");
      $result = parent::display($template, $cache_id, $compile_id, $parent);
      return $result;
   }

   /**
    * Assign variables by global variable name
    * @deprecated unused
    * @author albertdiones@gmail.com
    */
   function assign_globals(/* Polymorphic */) {
      $keys = func_get_args();
      foreach ($keys as $key) {
         if (isset($GLOBALS[$key])) {
            if (is_array($key)) {
               $this->assign_globals($key);
            }
            else {
               $this->assign($key,$GLOBALS[$key]);
            }
         }
         else {
            $this->assign($key,NULL);
         }
      }
   }
}