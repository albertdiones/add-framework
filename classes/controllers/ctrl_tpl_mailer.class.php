<?php
add::load_lib('phpmailer');
/**
 * ctrl_tpl_mailer
 * Abstract controller class for emails
 *
 * @since ADD MVC 0.2
 */
ABSTRACT CLASS ctrl_tpl_mailer EXTENDS phpMailer IMPLEMENTS i_ctrl, i_ctrl_with_view {

   public $WordWrap          = 70;

   protected static $views;

   protected $data = array();

   /**
    * Execute
    *
    * @since ADD MVC 0.6
    */
   public function execute() {
      $this->process_data();
      parent::Send();
   }

   /**
    * Process the data
    *
    * @since ADD MVC 0.6
    */
   public function process_data() {
      $this->assign('C',add::config());
      $this->view()->assign($this->data);
      $this->Body = $this->view()->fetch(static::view_filepath());
   }

   public function print_response($data) {
      $this->view()->assign($data);
      return $this->view()->display(static::view_filepath());
   }

   /**
    * Before sending
    * @since ADD MVC 0.2
    */
   public function Send() {
      $this->process_data();
      return parent::Send();
   }


   /**
    * The view base file name of $this controller
    * @since ADD MVC 0.3
    * @version 0.1
    */
   public static function view_basename() {
      return self::basename();
   }

   /**
    * The controller's basename
    * @since ADD MVC 0.3
    */
   public static function basename() {
      return preg_replace('/^ctrl_mailer_/','',get_called_class());
   }

   /**
    * The view file path
    * @since ADD MVC 0.2
    */
   public static function view_filepath() {
      return "emails/".static::view_basename().'.tpl';
   }

   /**
    * The Smarty view object of $this controller
    * @since ADD MVC 0.2
    */
   public function view() {
      $class = get_called_class();

      if (!isset(static::$views[$class])) {
         static::$views[$class] = new add_smarty();
      }

      return static::$views[$class];
   }

   public function fetch_view() {

      $this->view()->assign('C',add::config());

      return $this->view()->fetch(static::view_filepath());
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
}