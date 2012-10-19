<?php

/**
 * Abstract Controller
 *
 * @since ADD MVC 0.10
 */
ABSTRACT CLASS ctrl_abstract {

   /**
    * Set mode function
    *
    * @since ADD MVC 0.10
    */
   public function set_mode() {
      if (isset($_REQUEST['mode']) && preg_match('/^\w+$/',$_REQUEST['mode'])) {
         $this->mode = $_REQUEST['mode'];
         if (isset($_REQUEST['sub_mode']) && preg_match('/^\w+$/',$_REQUEST['sub_mode'])) {
            $this->sub_mode = $_REQUEST['sub_mode'];
         }
      }

      if (!isset($this->mode))
         $this->mode = 'default';
   }

   /**
    * process_mode function
    * Processes any GPC requests
    *
    * @param array $gpc
    * @since ADD MVC 0.1
    *
    * @version 1.0
    */
   public function process_mode( $common_gpc = array() ) {
      $mode = $this->mode;

      $method_name = "process_mode_$mode";

      if (method_exists($this,$method_name)) {

         $gpc_key_var = "mode_gpc_$mode";

         $mode_gpc = array();

         if ( isset( $this->$gpc_key_var ) ) {
            $mode_gpc = $this->recursive_compact( $this->$gpc_key_var );
         }
         else if ($mode != 'default') {
            throw new e_developer(get_called_class()."->$gpc_key_var not declared");
         }

         $reserved_gpc = array('mode' => $this->mode);

         if (!empty($this->sub_mode)) {
            $reserved_gpc['sub_mode'] = $this->sub_mode;
         }

         $merged_gpc = array_merge($reserved_gpc, $common_gpc, $mode_gpc);

         $this->assign($merged_gpc);
         $this->assign('mode',$mode);
         $this->assign('sub_mode',$this->sub_mode);

         return $this->$method_name($merged_gpc);

      }
      else if ($mode == 'default') {
         $this->assign($common_gpc);
         $this->assign('mode','default');
      }

      return false;
   }

   /**
    * recursive compact($array_keys)
    * Returns an array of GPC from the $array_keys
    *
    * @since ADD MVC 0.1, ctrl_tpl_page 0.1
    */
   public static function recursive_compact($gpc_array_keys) {
      $compact_array = array();
      foreach ($gpc_array_keys as $gpc_key => $array_keys) {
         e_developer::assert(isset($GLOBALS[$gpc_key]),"Invalid GPC key $gpc_key");
         $gpc_array = $GLOBALS[$gpc_key];

         foreach ($array_keys as $array_key) {
            e_developer::assert(is_scalar($array_key),"Invalid GPC array key $array_key");
            $compact_array[$array_key] = empty($gpc_array[$array_key]) ? null : $gpc_array[$array_key];
         }

      }
      return $compact_array;
   }
}