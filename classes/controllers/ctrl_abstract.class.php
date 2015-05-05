<?php

/**
 * Abstract Controller
 *
 * @package ADD MVC Controllers
 *
 * @see ctrl_tpl_page, ctrl_tpl_ajax and ctrl_tpl_aux
 *
 * @since ADD MVC 0.10
 * @version 2.0
 */
ABSTRACT CLASS ctrl_abstract {

   /**
    * The mode of the process
    * @since ADD MVC 0.3, ctrl_tpl_page 0.2.3
    */
   protected $mode;

   /**
    * The sub mode of the mode
    * @since ADD MVC 0.3, ctrl_tpl_page 0.2.3
    */
   protected $sub_mode;

   /**
    * Mime type of this resource
    *
    * @since ADD MVC 0.8
    */
   protected $content_type = 'text/html';

   /**
    * The views cache
    *
    * Placeholder for the controller's Smarty class instance
    *
    * @see ctrl_tpl_page::view()
    *
    * @since ADD MVC 0.0
    */
   protected static $views = array();


   /**
    * The controller data
    *
    * passed to the view through Smarty's assign()
    *
    * @see ctrl_tpl_page::print_response()
    *
    * @since ADD MVC 0.6
    */
   protected $data = array();

   /**
    * Set mode function
    *
    * @see ctrl_tpl_page::execute()
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
    * Processes any GPC requests
    * Usually you won't need to extend/overload this, use process_mode_* methods instead
    *
    * @see https://code.google.com/p/add-mvc-framework/wiki/modesAndSubModes
    * @see ctrl_tpl_page::process_data()
    *
    * @param array $common_gpc
    * @since ADD MVC 0.1
    *
    * @version 2.0
    *
    * <code>
    * ABSTRACT CLASS ctrl_abstract_member_page EXTENDS ctrl_tpl_page {
    *    function process_data() {
    *
    *       # Require log in for such pages
    *       member::require_logged_in();
    *
    *       $this->pre_mode_process(array());
    *       $this->process_mode(array());
    *       $this->post_mode_process(array());
    *    }
    * }
    * </code>
    *
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


         # Filtering the GPC variables before assigning them
         foreach ($merged_gpc as $gpc_name => $gpc_var) {
            $filter_method = "gpc_filter_$gpc_name";

            if (method_exists($this, $filter_method)) {
               if (!$this->$filter_method($gpc_var)) {
                  $merged_gpc[$gpc_name] = null;
               }
            }
            else if (is_array($gpc_var)) {
               trigger_error("GPC variable $gpc_name is array, but no filter was found", E_USER_WARNING);
               #$merged_gpc[$gpc_name] = array();
            }
         }

         $this->assign($merged_gpc);
         $this->assign('mode',$mode);
         $this->assign('sub_mode',$this->sub_mode);

         return $this->$method_name($merged_gpc);

      }
      else {
         $this->mode = 'default';
         $this->assign($common_gpc);
         $this->assign('mode','default');
      }

      return false;
   }

   /**
    * Accepts 2 dimensional array of keys to be fetched from global variables
    *
    * Returns a multi dimension array of the global variables value of $gpc_array_keys
    *
    * @param array $gpc_array_keys - 2 dimension array of keys
    *
    * @see ctrl_abstract::process_mode()
    *
    * <code>
    * if (!$_GET['foo']) {
    *   add:redirect('?foo=bar');
    * }
    * debug::var_dump($_GET, ctrl_abstract::recursive_compact( array( '_GET' => array('foo') ) ));
    * </code>
    *
    * @since ADD MVC 0.1, ctrl_tpl_page 0.1
    */
   public static function recursive_compact($gpc_array_keys) {
      $compact_array = array();

      # Magic quotes backward support https://code.google.com/p/add-mvc-framework/issues/detail?id=118
      $magic_quotes_on = get_magic_quotes_gpc()
         && $real_gpcs = array('_GET','_POST','_COOKIE','_REQUEST');

      foreach ($gpc_array_keys as $gpc_key => $array_keys) {
         e_developer::assert(isset($GLOBALS[$gpc_key]),"Invalid GPC key $gpc_key");
         $gpc_array = $GLOBALS[$gpc_key];

         if (is_string($array_keys)) {
            trigger_error("GPC:$gpc_key keys for should be array, string given: $array_keys");
            $array_keys = array($array_keys);
         }

         foreach ($array_keys as $array_key) {
            e_developer::assert(is_scalar($array_key),"Invalid GPC array key $array_key");

            # Normal: $compact_array['myKey'] = 'User's Input';
            # Array: $compact_array['myKey[myKey2]'] = 'User's Input';
            # Declaration : _POST => array( 'myKey[][myKey2]', array('myKey'=> 'MyKey2'), 'myKey[][][][SuperDimensional']
            # Output : array( 'myKey' => array('myKey2' => 'User\'s Input'))
            if (preg_match('/^\w+(\[.*?\])+$/',$array_key)) {
               $main_key = preg_replace('/^(\w+)\[.+$/','$1',$array_key);

               $unfiltered_gpc_value = $gpc_array[$main_key];
               $gpc_value            = array();

               preg_match_all('/\[(?P<keys>.*?)\]/',$array_key, $dimensions);

               $dimension_keys = $dimensions['keys'];

               $current_dimension_indexes = "";


               #debug::var_dump($main_key,$gpc_array,$unfiltered_gpc_value);
               $gpc_value = static::get_gpc_value($unfiltered_gpc_value, $dimension_keys);


               if (empty($compact_array[$main_key])) {
                  $compact_array[$main_key] = empty($gpc_value) && $gpc_value !== '0' ? null : $gpc_value;
               }
               else if (is_array($compact_array[$main_key])) {
                  $compact_array[$main_key] = static::gpc_merge($compact_array[$main_key], $gpc_value);
               }

            }
            else {
               $compact_array[$array_key] = empty($gpc_array[$array_key]) && $gpc_array[$array_key] !== '0' ? null : $gpc_array[$array_key];;
            }
         }
         # stripslahes if magic quotes is on https://code.google.com/p/add-mvc-framework/issues/detail?id=118
         if ( $magic_quotes_on && in_array($gpc_key,$real_gpcs) ) {
            foreach ($compact_array as $field => &$value) {
               $value = stripslashes($value);
            }
         }

      }
      return $compact_array;
   }


   /**
    * GPC Merge Recursive
    *
    *
    */
   public function gpc_merge() {
      $gpc_arrays = func_get_args();
      $merged = array_shift($gpc_arrays);
      foreach ($gpc_arrays as $array) {
         foreach ($array as $field => $value) {
            if (is_array($value)) {
               $merged[$field] = static::gpc_merge($merged[$field],$value);
            }
            if (!isset($merged[$field])) {
               $merged[$field] = $value;
            }
         }
      }
      return $merged;
   }


   /**
    * GPC key
    *
    * @param array $unfiltered_variable the (multi)dimensional array that we will get
    * @param array $dimension_keys the string dimension
    *
    */
   public static function get_gpc_value($unfiltered_variable, $dimension_keys) {
      $filtered_variable   = array();
      #debug::var_dump($dimension_keys);
      while (($dimension_key = array_shift($dimension_keys))!==null) {
         #debug::var_dump($dimension_key);
         if ($dimension_key === "") {
            foreach ($unfiltered_variable as $unfiltered_item_field => $unfiltered_item_value) {
               if (filter_var($unfiltered_item_field,FILTER_VALIDATE_INT, array('options' => array('min_range'=>0))) === false) {
                  break;
               }
               if ($dimension_keys) {
                  $filtered_variable["$unfiltered_item_field"] = static::get_gpc_value($unfiltered_item_value,$dimension_keys);
               }
               else {
                  $filtered_variable["$unfiltered_item_field"] = $unfiltered_item_value;
               }
            }
            return $filtered_variable;
         }
         else {
            $current_dimension_indexes .= "[".var_export($dimension_key,true)."]";
            eval('$current_dimension = &$filtered_variable'.$current_dimension_indexes.';');
            eval('$current_unfiltered_dimension = &$unfiltered_variable'.$current_dimension_indexes.';');
            if ($dimension_keys) {
               $current_dimension = static::get_gpc_value($current_unfiltered_dimension, $dimension_keys);
            }
            else {
               $current_dimension = $current_unfiltered_dimension;
            }
         }
      }
      return $filtered_variable;
   }


   /**
    * Assign a variable to the view
    * Arguments are the same as Smarty's assign
    *
    * @see http://www.smarty.net/docs/en/api.assign.tpl
    *
    * @since ADD MVC 0.6
    * <code>
    *    CLASS ctrl_page_index EXTENDS ctrl_tpl_page {
    *       public function process_mode_default($gpc) {
    *
    *          # Assigns current date to $date_today
    *          $this->assign('date_today',date('M d Y'));
    *
    *          # Assigns all from $_SESSION
    *          $this->assign($_SESSION);
    *       }
    *    }
    * </code>
    */
   public function assign() {
      $arg1 = func_get_arg(0);

      if (is_array($arg1) || is_object($arg1)) {
         $this->data = array_merge($this->data,(array) $arg1);
      }
      else {
         $this->data[$arg1] = func_get_arg(1);
      }

   }



   /**
    * Returns the data assigned
    *
    * @see assign()
    *
    * @since ADD MVC 0.10
    */
   public function data() {
      return $this->data;
   }
}