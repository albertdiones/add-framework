<?php
/**
 * ctrl_tpl_with_view controller interface
 *
 * @author albertdiones@gmail.com
 *
 * @package ADD MVC\Extras\Interfaces
 * @since ADD MVC 0.3
 * @version 1.0
 *
 * @deprecated use i_with_view
 *
 */
INTERFACE i_ctrl_with_view EXTENDS i_with_view {
   /**
    * The controller's basename
    * @since ADD MVC 0.1
    */
   public static function basename();

}