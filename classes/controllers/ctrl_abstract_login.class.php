<?php

/**
 * login page abstract controller
 *
 * @since homeland voice 0.0
 */
CLASS ctrl_abstract_login EXTENDS ctrl_tpl_page {


   /**
    * member model class
    *
    * @var string
    *
    * @since homeland voice 0.0
    */
   const MEMBER_MODEL = 'member';


   /**
    * Honeypot trap input name
    *
    * @since homeland voice 0.0
    */
   const HONEYPOT_FIELD = 'website';


   /**
    * Common GPC
    *
    * @since homeland voice 0.0
    */
   public $common_gpc = array(
         '_REQUEST' => array(
               'redirect'
            )
      );

   /**
    * mode=login request variables
    *
    * @since homeland voice 0.0
    */
   public $mode_gpc_login = array(
         '_POST' => array()
      );

   /**
    * mode=logout request variables
    *
    * @since homeland voice 0.0
    */
   public $mode_gpc_logout = array(
         '_POST' => array()
      );

   /**
    * Pre Execute process
    *
    * @param array $common_gpc
    *
    * @since homeland voice 0.0
    */
   public function execute() {
      $class = static::member_model();
      $this->assign('class',$class);
      $this->assign('username_term', $class::TERM_USERNAME);
      $this->assign('username_field', $class::USERNAME_FIELD);
      $this->assign('password_term', $class::TERM_PASSWORD);
      $this->assign('password_field', $class::PASSWORD_FIELD);
      $this->assign('honeypot_field', static::HONEYPOT_FIELD);
      $this->common_gpc['_REQUEST'][] = $this->data['username_field'];
      $this->mode_gpc_login['_POST'][] = $this->data['password_field'];
      $this->mode_gpc_login['_POST'][] = static::HONEYPOT_FIELD;
      parent::execute();
   }

   /**
    *
    * Default: prefill
    *
    * @param $gpc
    */
   public function process_mode_default($gpc) {
      $username = $gpc[$this->data['username_field']];

      if ($gpc['redirect']) {
         if ($_SERVER['HTTP_REFERER']) {
            $go_back_link = "<a href='".htmlspecialchars($_SERVER['HTTP_REFERER'])."'>Go Back</a>";
         }
         else {
            $go_back_link = "<a href='javascript:window.history.back();'>Go Back</a>";
         }
         $this->assign('message',"Please login first to continue");
         $this->assign('go_back_link',$go_back_link);
      }

      $this->assign('username_value',$username);
   }
   /**
    * mode=login
    *
    * @param array $gpc
    *
    * @since homeland voice 0.0
    */
   public function process_mode_login($gpc) {

      $honeypot = $gpc[static::HONEYPOT_FIELD];

      if (!empty($honeypot)) {
         throw new e_spam("Spammer Detected");
      }

      $username = $gpc[$this->data['username_field']];
      $password = $gpc[$this->data['password_field']];
      $redirect = $gpc['redirect'];

      $class = $this->data['class'];

      $this->assign('username_value',$username);

      if ($class::login($username,$password)) {
         if (empty($redirect)) {
            $redirect = static::default_redirect();
         }
         if (empty($redirect)) {
            $redirect = add::config()->path;
         }
         add::redirect($redirect);
      }

   }


   /**
    * mode=logout
    *
    * @param array $gpc
    *
    * @since homeland voice 0.0
    */
   public function process_mode_logout($gpc) {
      $class = $this->data['class'];

      $class::logout();

      add::redirect('?mode=logged_out');
   }


   /**
    * member_model()
    *
    * @since homeland voice 0.0
    */
   public static function member_model() {
      return static::MEMBER_MODEL;
   }


   /**
    * view_file_path() default
    *
    * @since homeland voice 0.0
    */
   public function view_filepath() {
      return 'layouts/login.tpl';
   }

   /**
    * Overload and return a string
    * @return string
    */
   public static function default_redirect() {
      return null;
   }
}