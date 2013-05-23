<?php
/**
 * e_spam class
 * Custom Exception Class for spam errors
 *
 * @author albertdiones@gmail.com
 *
 * @package ADD MVC\Exceptions
 * @since ADD MVC 0.0
 * @version 0.0
 */
CLASS e_spam EXTENDS e_user_malicious {

/**
 * Do nothing, the reason we hate spam is because it bothers us, so we will ignore this error
 *
 */
   public function handle_exception() {
      return add::shutdown(false);
   }

}