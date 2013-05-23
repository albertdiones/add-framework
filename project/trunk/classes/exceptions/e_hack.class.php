<?php
/**
 * Custom Exception Class for hackish errors (Emailed)
 *
 * This will typically send email to developer_emails declared on config
 *
 * <code>
 * if (!$current_user->owns($page)) {
 *    throw new e_hack("Current user does not own page",array($current_user,$page));
 * }
 * </code>
 *
 * @author albertdiones@gmail.com
 *
 * @package ADD MVC Exceptions
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
      return $this->handle_sensitive_exception("An authentication error occured, please try going back to the homepage");
   }
}