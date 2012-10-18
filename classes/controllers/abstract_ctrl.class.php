<?php

/**
 * Abstract Controller
 *
 * @since ADD MVC 0.10
 */
ABSTRACT CLASS abstract_ctrl {

   /**
    * Set mode function
    *
    * @since ADD MVC 0.10
    */
   public function set_mode() {
      if (isset($_REQUEST['mode'])) {
         if (preg_match('/^\w+$/',$_REQUEST['mode'])) {
            $this->mode = $_REQUEST['mode'];
            if (preg_match('/^\w+$/',$_REQUEST['sub_mode'])) {
               $this->sub_mode = $_REQUEST['sub_mode'];
            }
         }
         else {
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

         $compact_array = array();

         if ( isset( $this->$gpc_key_var ) ) {
            $compact_array = $this->recursive_compact( $this->$gpc_key_var );
         }
         else if ($mode != 'default') {
            throw new e_developer(get_called_class()."->$gpc_key_var not declared");
         }

         $merge_compact_array = array_merge($common_gpc, $compact_array);

         $this->assign($merge_compact_array);
         $this->assign('mode',$mode);
         $this->assign('sub_mode',$this->sub_mode);

         return $this->$method_name($merge_compact_array);

      }
      else if ($mode == 'default') {
         $this->assign($common_gpc);
         $this->assign('mode','default');
      }

      return false;
   }
}