<?php
/**
 * e_hack class
 * Custom Exception Class for hackish errors
 *
 * @author albertdiones@gmail.com
 *
 * @package ADD MVC\Exceptions
 * @since ADD MVC 0.0
 * @version 0.0
 */
CLASS e_hack EXTENDS e_user_malicious {
   /**
    * Handle exception
    * Mail the error to developers
    *
    * @since ADD MVC 0.0
    */
   public function handle_exception() {
      return $this->mail();
   }
}