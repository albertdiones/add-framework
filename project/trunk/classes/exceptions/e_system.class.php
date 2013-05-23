<?php
/**
 * Custom Exception Class for system errors
 * When it's an error of the system but it's not developer's fault, use this
 *
 * <code>
 * if ($api_limit_reached) {
 *    throw new e_system("The API limit has been reached");
 * }
 * <code>
 *
 * @author albertdiones@gmail.com
 *
 * @package ADD MVC Exceptions
 * @since ADD MVC 0.0
 * @version 0.0
 */
CLASS e_system EXTENDS e_add {

   /**
    * Handle exception
    * Mail the error to system managers
    *
    * @since ADD MVC 0.0
    */
   public function handle_exception() {
      return $this->handle_sensitive_exception("An error occurred on our system, our developers has been notified about this and we will fix it as soon as we can.");
   }

   /**
    * The email subject
    *
    * @since ADD MVC 0.0
    */
   public function mail_subject() {
      return $this->truncated_subject("System Error: ");
   }
}