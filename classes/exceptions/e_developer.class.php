<?php
/**
 * Custom Exception Class for system and developer errors
 *
 * @author albertdiones@gmail.com
 *
 * @package ADD MVC\Exceptions
 * @since ADD MVC 0.0
 * @version 0.0
 */
CLASS e_developer EXTENDS e_system {

   /**
    * The email subject
    *
    * @since ADD MVC 0.0
    */
   public function mail_subject() {
      return $this->truncated_subject("Developer Error: ");
   }
}