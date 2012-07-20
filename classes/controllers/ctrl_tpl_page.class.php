<?php
/**
 * ctrl_tpl_page abstract model class
 *
 * @author albertdiones@gmail.com
 *
 * @package ADD MVC\Controllers
 * @since ADD MVC 0.0
 * @version 1.0
 */
ABSTRACT CLASS ctrl_tpl_page IMPLEMENTS i_ctrl, i_ctrl_with_view {

   /**
    * The mode of the process
    * @since ADD MVC 0.3, ctrl_tpl_page 0.2.3
    */
   protected $mode;

   /**
    * The views cache
    * @since ADD MVC 0.0
    */
   protected static $views = array();


   /**
    * The view data
    *
    * @since ADD MVC 0.6
    */
   protected $data = array();

   /**
    * __call() magic function
    *
    * @since ADD MVC 0.6
    */
   public function __call($function_name,$arguments) {
      static $renamed_functions = array(
            'page'         => 'execute',
            'process'      => 'process_data',
            'display_view' => 'print_response'
         );

      if (isset($renamed_functions[$function_name])) {
         call_user_func_array(array($this,$renamed_functions[$function_name]),$arguments);
      }
      else {
         throw new e_developer("Undefined function: ".get_called_class()." $function_name",func_get_args());
      }

   }

   /**
    * Process the page response (former $this->page())
    * @todo use a better term that is applicable to all response
    *
    * @since ADD MVC 0.0
    * @version 0.2
    */
   public function execute() {

      # ADD MVC 0.5 backward support
      if (method_exists($this,'page')) {
         return $this->page();
      }

      try {
         $this->mode = isset($_REQUEST['mode']) ? "$_REQUEST[mode]" : '';
         $this->process_data();
      }
      catch(e_user $e) {

         if ($e instanceof e_user_malicious) {
            $e->handle_exception();
            die();
         }

         if ($this->mode)
            $exception_label = "user_".$this->mode;
         else
            $exception_label = "user";

         $this->add_exception($e,$exception_label);
      }

      $this->assign('ctrl_basename',$this->basename());
      $this->assign('C',add::config());

      $error_messages = $this->view()->getTemplateVars('error_messages');

      if (is_array($error_messages))
         $this->assign('error_message',$error_messages[0]);

      $this->print_response($this->data);
   }

   /**
    * The pre-display process of the controller
    * (former $this->process())
    *
    * @since ADD MVC 0.0
    * @version 0.1
    *
    * @todo remove version 0.5 support on ADD MVC 1.0
    */
   public function process_data() {

      # ADD MVC 0.5 backward support
      if (method_exists($this,'process')) {
         return $this->process();
      }

      return $this->process_mode();
   }


   /**
    * process_mode function
    * Processes any GPC requests
    *
    * @since ADD MVC 0.1
    * @version 0.2
    */
   public function process_mode() {
      $mode = $this->mode;

      if (!$mode) {
         $mode = "default";
      }

      $method_name = "process_mode_$mode";

      if (method_exists($this,$method_name)) {

         $gpc_key_var = "mode_gpc_$mode";

         if (isset($this->$gpc_key_var)) {
            $compact_array = $this->recursive_compact( $this->$gpc_key_var );
         }
         else {
            throw new e_developer(get_called_class()."->$gpc_key_var not declared");
         }

         $this->view()->assign($compact_array);
         $this->view()->assign('mode',$mode);

         return $this->$method_name($compact_array);

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
            $compact_array[$array_key] = $gpc_array[$array_key]!=="" ? $gpc_array[$array_key] : null;
         }

      }
      return $compact_array;
   }


   /**
    * The view filepath of $this controller
    * @since ADD MVC 0.0
    */
   public static function view_filepath() {
      return static::view_basename().'.tpl';
   }
   /**
    * The view base file name of $this controller
    * @since ADD MVC 0.0
    * @version 0.1
    */
   public static function view_basename() {
      return static::basename();
   }

   /**
    * The controller's basename
    * @since ADD MVC 0.1, ctrl_tpl_page 0.1.1
    */
   static function basename() {
      return preg_replace('/^ctrl_page_/','',get_called_class());
   }

   /**
    * The Smarty view object of $this controller
    * @since ADD MVC 0.0
    */
   public function view() {
      $class = get_called_class();

      if (!isset(static::$views[$class])) {
         static::$views[$class] = new add_smarty();
      }

      return static::$views[$class];
   }

   /**
    * The canonical url path of this controller
    *
    * @since ADD MVC 0.5, ctrl_tpl_page 0.3
    */
   public function path() {
      return str_replace('_','-',self::basename());
   }

   /**
    * Redirect
    *
    * @since ADD MVC 0.5
    */
   public function redirect() {
      add::redirect(self::path());
   }

   /**
    * display() the Smarty template of $this controller
    *
    * @since ADD MVC 0.0, ctrl_tpl_page 0.1
    * @version 0.2
    *
    * @todo remove ADD MVC 0.5 version support
    *
    */
   public function print_response($data) {

      # ADD MVC 0.5 backward support
      if (method_exists($this,'display_view')) {
         return $this->display_view();
      }

      $tpl = static::view_filepath();

      if ($this->view()->templateExists($tpl)) {
      $this->view()->assign($data);
         $this->view()->display($tpl);
      }
      else {
         $template_vars = $data;
         unset($template_vars['C']);
         $this->view()->assign('template_vars',$template_vars);
         $this->view()->display('debug/list_array_page.tpl');
      }
   }


   /**
    * Register an exception to the view
    *
    * @param Exception $e the exception object
    * @param string $label the label for the exception
    * In smarty:
    * <code>{$error_messages.label}</code>
    * Latest Error Message:
    * <code>{$error_message}</code>
    * @since ADD MVC 0.3, ctrl_tpl_page 0.2.2
    */
   public function add_exception(Exception $e,$label=null) {
      $error_messages = $this->view()->getTemplateVars('error_messages');

      if (!$error_messages)
         $error_messages = array();

      array_unshift($error_messages,$e->getMessage());

      if ($label) {
         $error_messages[$label] = $e->getMessage();
      }

      $this->view()->assign('error_messages',$error_messages);
   }

   /**
    * Assign a variable to the view
    *
    * @since ADD MVC 0.6, ctrl_tpl_page 1.0
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



# DEPRECATED FUNCTIONS

   /**
    * The default page title
    *
    * @since ADD MVC 0.1, ctrl_tpl_page 0.1
    * @deprecated use the view instead
    * @version 0.1
    */
   public function meta_title() {
      return add::config()->default_meta_title;
   }

   /**
    * meta description
    *
    * @since ADD MVC 0.1, ctrl_tpl_page 0.1
    * @deprecated use the view instead
    */
   public function meta_description() {
      return isset(add::config()->default_meta_description) ? add::config()->default_meta_description : null;
   }

   /**
    * meta keywords
    *
    * @since ADD MVC 0.1, ctrl_tpl_page 0.1
    * @deprecated use the view instead
    */
   public function meta_keywords() {
      return isset(add::config()->default_meta_keywords) ? add::config()->default_meta_keywords : null;
   }
}