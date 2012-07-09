<?php
add::load_lib('phpmailer');
/**
 * ctrl_tpl_mailer
 * Abstract controller class for emails
 *
 * @since ADD MVC 0.2
 */
CLASS ctrl_tpl_mailer EXTENDS phpMailer IMPLEMENTS i_ctrl_with_view {

   public $WordWrap          = 70;

   protected static $views;

   /**
    * Before sending
    * @since ADD MVC 0.2
    */
   public function Send() {

      if (!$this->Body) {
         $this->Body = $this->fetch_view();
      }

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
}