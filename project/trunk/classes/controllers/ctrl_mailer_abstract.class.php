<?php
/**
 * Mailer Abstract File
 *
 *
 */
if (!class_exists('phpmailer')) {
   add::load_lib('phpmailer');
}

/**
 * Mailer Abstract
 *
 * A phpmailer object directly integrated with a controller
 *
 * <code>
 * #includes/classes/ctrl_page_register.class.php
 * CLASS ctrl_page_register EXTENDS ctrl_tpl_page {
 *    public $mode_gpc_register = array( '_POST' => array('username');
 *    public function process_mode_register($gpc) {
 *       # ... registered successfully
 *       $this->assign('member', member::add_new($gpc));
 *       $mailer = new ctrl_mailer_register($this);
 *       $mailer->execute();
 *    }
 * }
 * </code>
 * <code>
 * #includes/classes/ctrl_mailer_register.class.php
 * CLASS ctrl_mailer_register EXTENDS ctrl_mailer_abstract {
 * }
 * </code>
 * <code>
 * #views/mailers/register.tpl
 * {*SMARTY*}
 * Hello {$member->username} you are successfully registered!
 * </code>
 *
 * @package ADD MVC Controllers
 */
ABSTRACT CLASS ctrl_mailer_abstract EXTENDS phpmailer {

   /**
    * Default Wordwrap 70 characters
    *
    * @var int
    *
    */
   public $WordWrap          = 70;

   /**
    * View variable cache
    *
    */
   protected static $views;

   /**
    * assign()ed data
    *
    */
   protected $data = array();


   /**
    * controller variable cache
    *
    */
   protected $add_controller;

   /**
    * Constructor
    *
    * @param ctrl_abstract $ctrl
    *
    */
   public function __construct(ctrl_abstract $ctrl = null/* other php mailer arguments */) {
      $args = func_get_args();

      /* $ctrl = */array_shift($args);
      parent::__construct($args);

      $this->add_controller = $ctrl;
   }

   /**
    * Execute (send)
    *
    * @since ADD MVC 0.10.4
    */
   public function execute() {
      if ($this->add_controller instanceof ctrl_abstract) {
         $data = $this->add_controller->data();
      }
      else {
         $data = array();
      }
      $this->process_data($data);
      return $this->Send();
   }


   /**
    * Fetches the view before sending
    *
    */
   public function Send() {
      $this->Body = $this->fetch_view();
      return call_user_func_array('parent::'.__FUNCTION__, func_get_args());
   }

   /**
    * Process the data
    *
    * @param array $data
    *
    * @since ADD MVC 0.10.4
    */
   public function process_data($data) {
      if ($data)
         $this->assign($data);
   }

   /**
    * Print the response (instead of sending it)
    *
    * @param array $data
    *
    */
   public function print_response($data = array()) {
      if ($data) {
         $this->assign($data);
      }
      echo $this->fetch_view();
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
      return "mailers/".static::view_basename().'.tpl';
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

   /**
    * Gets the processed template
    *
    */
   public function fetch_view() {
      $this->view()->assign($this->data);
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

   /**
    * sets the content_type or get the current one
    *
    * @param string $new_content_type
    *
    * @since ADD MVC 0.8
    */
   public function content_type($new_content_type = null) {
      if ($new_content_type) {
         $this->ContentType = $new_content_type;
      }
      return $this->ContentType;
   }

}