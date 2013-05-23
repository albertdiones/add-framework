<?php
/**
 * Smarty view for exceptions
 *
 * @author albertdiones@gmail.com
 *
 * @package ADD MVC\Extras
 * @since ADD MVC 0.7
 * @version 0.0
 *
 * @deprecated
 *
 * @todo make the config variables changeable outside framework
 */
CLASS e_add_smarty EXTENDS add_smarty {

   /**
    * Smarty construct
    *
    * @since ADD MVC 0.7
    */
   public function __construct() {

      parent::__construct();

      $this -> setTemplateDir(
            array(
                  add::config()->views_dir.'/exceptions',
                  add::config()->add_dir.'/views/exceptions'
               )
         );

   }

}